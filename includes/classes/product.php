<?php
require_once("table.php");
class Product extends Table{
    protected $data = [
        "id" => 0,
        "title" => "",
        "description" => "",
        "link" => "",
        "creation_time" => 0,
        "category_id" => 0,
        "category_name" => "",
        "slideShow_path" => "",
        "isSlideShow" => 0,
        "picture_path" => "",
        "keywords" => "",
        "published" => 0,
        "modify_time" =>0,
        "pictures" => []
    ];
    const PUBLISHED = "published";
    const UNPUBLISHED = "unpublished";
    const ALLPRODUCTS = "all";
    public static function get_slideShow(){
        $conn = self::connectMysqli();
        $query = "CALL sp_get_slideShow()";
        $result = $conn->query($query);
        $ret = [];
        if ($result->num_rows)
            foreach ($result->fetch_all(MYSQLI_ASSOC) as $path)
                $ret[] = new Product($path);
        self::disconnect($conn);
        return $ret;
    }
    public static function get_product_by_url($uri){
        $uri = htmlentities($uri);
        $conn = self::connectPDO();
        $query = "SELECT * FROM products WHERE link = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$uri]);
        $ret = false;
        if($stmt->rowCount()) {
            $row = $stmt->fetch();
            if ($pictures = PicturesProduct::get_all_pictures_byProductID($row['id'])) {
                foreach ($pictures as $picture)
                    $row['pictures'][] = $picture;
            }
            $ret = new Product($row);
        }
        self::disconnect($conn);
        return $ret;
    }
    public static function get_all_products($publish = self::PUBLISHED,$start = 0, $limit = 0,$returnArray = false){
        $ret = [];
        if((is_numeric($start) and is_numeric($limit)) and ($publish === self::PUBLISHED or $publish === self::UNPUBLISHED or $publish === self::ALLPRODUCTS)) {
            $conn = self::connectPDO();
            $stmt = $conn->prepare("CALL sp_get_all_products(:start_index,:limit_index,:publish)");
            $stmt->bindParam(':start_index', $start);
            $stmt->bindParam(':limit_index', $limit);
            $stmt->bindParam(':publish', $publish);
            $stmt->execute();
            if ($stmt->rowCount())
                while ($product = $stmt->fetch(PDO::FETCH_ASSOC))
                    if (!$returnArray)
                        $ret[] = new Product($product);
                    else
                        $ret[] = $product;
            self::disconnect($conn);
        }
        return $ret;

    }
    public static function get_count_products(){
        $products = self::get_all_products(self::ALLPRODUCTS);
        reset($products);
        $product = current($products);
        $array = [
            'all' => 0,
            'published' => 0,
            'unpublished' => 0,
        ];
        if ($product)
            do {
                $array['all']++;
                if ($product->published === 0)
                    $array['unpublished']++;
                else
                    $array['published']++;
            } while ($product = next($products));
        return $array;
    }
    public static function get_products_by_category($categoryId,$returnArray = false){
        $ret = [];
        if(is_numeric($categoryId)){
            $conn = self::connectPDO();
            $stmt = $conn->prepare("CALL sp_get_products_by_category(:category_id)");
            $stmt->bindParam('category_id',$categoryId);
            $stmt->execute();
            if($stmt->rowCount()) {
                while ($product = $stmt->fetch(PDO::FETCH_ASSOC))
                    if (!$returnArray)
                        $ret[] = new Product($product);
                    else
                        $ret[] = $product;
            }
            self::disconnect($conn);
        }
        return $ret;
    }
    public static function get_product_by_id($productID){
        $ret = false;
        if(is_numeric($productID)){
            $conn = self::connectPDO();
            $stmt = $conn->prepare("CALL sp_get_product_byID(:product_id)");
            $stmt->bindParam(':product_id',$productID);
            $stmt->execute();
            if($stmt->rowCount()){
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
                $stmt->closeCursor();
                if($pics = PicturesProduct::get_all_pictures_byProductID($productID))
                    foreach ($pics as $pic)
                        $product['pictures'][] = $pic;
                $ret = new Product($product);
            }

            self::disconnect($conn);
        }
        return $ret;
    }
    public static function insert($array)
    {
        $lastInsertID = 0;
        if (is_numeric($array['category_id'])) {
            date_default_timezone_set('Asia/Tehran');
            $title = strip_tags($array['title']);
            $link = self::setLinkStandard(strip_tags($array['link']));
            $keywords = strip_tags($array['keywords']);
            $description = self::removeScripTags($array['description']) ;
            $creation_time = time();
            $conn = self::connectPDO();
            $stmt = $conn->prepare("CALL sp_insert_product(:product_title ,:product_desc ,:product_link ,:product_creation_time,:product_cat_id ,
                                      :product_slideShow_path,:product_isSlideShow ,:product_picture_path,:product_keywords ,:product_published ,:product_modify_time,@result)");
            $stmt->bindParam(':product_title', $title);
            $stmt->bindParam(':product_desc',$description );
            $stmt->bindParam(':product_link', $link);
            $stmt->bindParam(':product_creation_time', $creation_time);
            $stmt->bindParam(':product_cat_id', $array['category_id']);
            $stmt->bindParam(':product_slideShow_path', $array['slideShow_path']);
            $stmt->bindParam(':product_isSlideShow', $array['isSlideShow']);
            $stmt->bindParam(':product_picture_path', $array['picture_path']);
            $stmt->bindParam(':product_keywords', $keywords);
            $stmt->bindParam(':product_published', $array['published']);
            $stmt->bindParam(':product_modify_time', $creation_time);
            if ($stmt->execute()) {
                $stmt->closeCursor();
                $result = $conn->query("SELECT @result AS result");
                if ($result->rowCount())
                    $lastInsertID = $result->fetch(PDO::FETCH_ASSOC)['result'];
                self::disconnect($conn);
            }
            return $lastInsertID;
        }
    }
    public static function delete($productID){
        $ret = false;
        if(is_numeric($productID)) {
            $conn = self::connectPDO();
            $stmt = $conn->prepare("CALL sp_delete_product(:productID)");
            $stmt->bindParam(":productID", $productID);
            if($stmt->execute())
                $ret = true;
            self::disconnect($conn);
        }
        return $ret;

    }
    public static function update($array){
        $ret = false;
        if(is_numeric($array['id']) and is_numeric($array['category_id'])) {
            date_default_timezone_set('Asia/Tehran');
            $title = strip_tags($array['title']);
            $link = self::setLinkStandard(strip_tags($array['link']));
            $keywords = strip_tags($array['keywords']);
            $description = self::removeScripTags($array['description']) ;
            $modify_time = time();
            $conn = self::connectPDO();
            $stmt = $conn->prepare("CALL sp_update_product(:product_id ,:product_title ,:product_desc ,:product_link ,:product_cat_id ,
                                      :product_isSlideShow ,:product_keywords ,:product_published ,:product_modify_time)");
            $stmt->bindParam(':product_id', $array['id']);
            $stmt->bindParam(':product_title',$title );
            $stmt->bindParam(':product_desc', $description);
            $stmt->bindParam(':product_link', $link);
            $stmt->bindParam(':product_cat_id', $array['category_id']);
            $stmt->bindParam(':product_isSlideShow', $array['isSlideShow']);
            $stmt->bindParam(':product_keywords', $keywords);
            $stmt->bindParam(':product_published', $array['published']);
            $stmt->bindParam(':product_modify_time', $modify_time);
            if ($stmt->execute())
                $ret = true;
            self::disconnect($conn);
        }
        return $ret;
    }
    public function __set($property,$value){
        if(array_key_exists($property,$this->data)){
            if($property !== "id"){
                if($property === 'link')
                    $value = self::setLinkStandard($value);
                $conn = self::connectPDO();
                $stmt = $conn->prepare("UPDATE `products` SET $property =:property_value WHERE `id` =:product_id");
                $value = ($value);
                $stmt->bindParam(':property_value',$value);
                $productID = $this->id;
                $stmt->bindParam(':product_id',$productID);
                if($stmt->execute())
                {
                    $this->data[$property] = $value;
                }
                self::disconnect($conn);
            }
        }
    }
    private static function setLinkStandard($link){
        preg_match('/^http(?:s)?:\/\/.+/',$link,$match);
        if(!$match){
            $link = preg_split('/,\s*|\s+|\-+\s*/',$link);
            $link = join('-',$link);
        }
        return $link;
    }
    private static function removeScripTags($string){
        $scripPattern = '/<script.+?\/script>/is';
        $scripPatternEncoded = '/&lt;script.*?\/script&gt/is';
        $string = preg_replace($scripPattern,"",$string);
        return preg_replace($scripPatternEncoded,"",$string);

    }


}
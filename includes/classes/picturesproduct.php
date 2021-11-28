<?php
require_once("table.php");

class PicturesProduct extends Table {
    protected $data = [
        "id" => 0,
        "path" => "",
        "product_id" => 0,
        "title" => ""
    ];
    public static function get_all_pictures_byProductID($productId){
        $ret = [];
        if(is_numeric($productId)){
            $conn = self::connectPDO();
            $query = "CALL get_all_pictures_byProductID(?)";
            $stmt = $conn->prepare($query);
            $stmt->execute([$productId]);
            while($row = $stmt->fetch())
                $ret[] = new PicturesProduct($row);
            self::disconnect($conn);
        }
        return $ret;
    }
    public static function get_picture_byPictureId($productId){
        $ret = [];
        if(is_numeric($productId)){
            $conn = self::connectPDO();
            $stmt = $conn->prepare("CALL sp_get_picture_byPictureId(:picture_id)");
            $stmt->bindParam(':picture_id',$productId);
            $stmt->execute();
            if($stmt->rowCount())
                     $ret = new PicturesProduct($stmt->fetch(PDO::FETCH_ASSOC));
            self::disconnect($conn);
        }
        return $ret;
    }
    public static function delete($productId,$picturePath){
        $ret = false;
        if(is_numeric($productId)){
            $conn = self::connectPDO();
            $stmt = $conn->prepare("CALL sp_delete_product_picture(:productId ,:picture_path)");
            $stmt->bindParam(':productId',$productId);
            $stmt->bindParam(':picture_path',$picturePath);
            if($stmt->execute())
                $ret=true;
            self::disconnect($conn);
        }
        return $ret;
    }
    public static function insert($array){
        $ret = false;
        if(is_numeric($array['product_id'])){
            $path = strip_tags($array['path']);
            $title = strip_tags($array['title']);
            $conn = self::connectPDO();
            $stmt = $conn->prepare("CALL sp_insert_product_pictures(:product_id,:picture_path,:product_title)");
            $stmt->bindParam(':product_id',$array['product_id']);
            $stmt->bindParam(':picture_path',$path);
            $stmt->bindParam(':product_title',$title);
            if($stmt->execute())
                $ret=true;
            self::disconnect($conn);
        }
        return $ret;
    }
    public function __set($property,$value){
        if(array_key_exists($property,$this->data)){
            if($property != 'id'){
                $value = strip_tags($value);
                $conn = self::connectPDO();
                $stmt = $conn->prepare("UPDATE `products_pictures` SET $property =:property_value WHERE `id` =:picture_id");
                $value = strip_tags($value);
                $stmt->bindParam(':property_value',$value);
                $pictureID = $this->id;
                $stmt->bindParam(':picture_id',$pictureID);
                if($stmt->execute())
                    $this->data[$property] = $value;
                self::disconnect($conn);
            }
        }
    }

}
<?php
require_once ("table.php");

class Category extends Table
{
    private static $arrayCats = [];
    protected $data = [
        "id" => 0,
        "name" => "",
        "parent_id" => 0,
        "priority" => 0
    ];
    const INCREASE_LEVEL = 'increase';
    const DECREASE_LEVEL = 'decrease';
    public static function getCategoriesByParentId($parent_id, $returnArray = false){
        if (!is_numeric($parent_id))
            return false;
        $ret = [];
        $conn = self::connectPDO();
        $stmt = $conn->prepare("CALL get_categories_byParentId(:parentID)");
        $stmt->bindParam(':parentID', $parent_id);
        $stmt->execute();
        if ($stmt->rowCount()) {
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $cat)
                if ($returnArray)
                    self::$arrayCats[] = $cat;
                else
                    self::$arrayCats[] = new Category($cat);
            if($returnArray)
                $ret = self::sortByPriority(true);
            else
                $ret = self::sortByPriority();

        }
        self::disconnect($conn);
        return $ret;
    }

    public static function insert($array){
        if (!is_numeric($array['parentID']))
            return false;
        $catName = strip_tags($array['catName']);
        $conn = self::connectPDO();
        $stmt = $conn->prepare("CALL sp_insert_category(:cat_name,:cat_parent_id)");
        $stmt->bindParam(':cat_name',$catName );
        $stmt->bindParam(':cat_parent_id', $array['parentID']);
        $ret = false;
        if ($stmt->execute())
            $ret = true;
        self::disconnect($conn);
        return $ret;
    }

    public static function delete($id)
    {
        if (!is_numeric($id))
            return false;
        $conn = self::connectPDO();
        $stmt = $conn->prepare("CALL sp_delete_category(:cat_id,@result)");
        $stmt->bindParam(':cat_id', $id);
        $ret = false;
        if ($stmt->execute()) {
            $stmt->closeCursor();
            $result = $conn->query("SELECT @result AS result");
            if ($result->rowCount()) {
                $row = $result->fetch(PDO::FETCH_ASSOC);
                $ret = $row['result'];
            }
        }
        self::disconnect($conn);
        return $ret;

    }

    public function __set($property, $value)
    {
        $ret = false;
        if (array_key_exists($property, $this->data)) {
            if ($property !== 'id') {
                $value = strip_tags($value);
                $conn = self::connectPDO();
                $stmt = $conn->prepare("UPDATE `categories` set $property =:property_value WHERE `id` =:cat_id");
                $value = strip_tags($value);
                $stmt->bindParam(':property_value', $value);
                $catID = $this->id;
                $stmt->bindParam(':cat_id', $catID);
                if ($stmt->execute()) {
                    $this->data[$property] = $value;
                    $ret = true;
                }
                self::disconnect($conn);
            }
        }
        return $ret;
    }
    private static function sortByPriority($returnArray = false){
        reset(self::$arrayCats);
        if(!$returnArray) {
            $ret[] = self::getFirsElementInGroup();
            $size = count(self::$arrayCats);
            $id = $ret[0]->id;
            for($i = 0; $i <$size  ; $i++){
                foreach (self::$arrayCats as $key=>$cat){
                    if($id === $cat->priority){
                        $ret[] = ($cat);
                        $id = $cat->id;
                        array_splice(self::$arrayCats,$key,1);
                        break;
                    }
                }
            }
        }else{
            $ret[0] = self::getFirsElementInGroup(true);
            $size = count(self::$arrayCats);
            $id = $ret[0]['id'];
            for ($i = 0; $i < $size; $i++)
                foreach (self::$arrayCats as $key=>$cat){
                    if($id === $cat['priority']){
                        $ret[] = $cat;
                        $id = $cat['id'];
                        array_splice(self::$arrayCats,$key,1);
                        break;
                    }
                }
        }
        return $ret;

   }
    private static function getFirsElementInGroup($returnArray = false){/*return cat with priority 0 */
        if($returnArray)
            foreach (self::$arrayCats as $key=>$value){
                if($value['priority'] == 0){
                    array_splice(self::$arrayCats,$key,1);
                    return( $value);
                }
            }
        else
            foreach (self::$arrayCats as $key=>$value){
            if($value->priority == 0){
                array_splice(self::$arrayCats,$key,1);
                return( $value);
            }
        }
    }
    public static function sortCategories($array,$parentId=0){
        if(is_numeric($parentId)) {
            $sortCategories = self::getCategoriesByParentId($parentId);
            foreach ($sortCategories as $id => $cat) {
                $flag = false;
                foreach ($array as $idCat => $catName)
                    if ($cat->id === (int)$idCat) {
                        $flag = true;
                        break;
                    }
                if (!$flag)
                    unset($sortCategories[(int)$id]);
            }
            return array_values($sortCategories);
        }
    }
    public static function changeLevel($catId,$actionLevel){
        if (!is_numeric($catId) || ($actionLevel !== self::INCREASE_LEVEL and $actionLevel !== self::DECREASE_LEVEL))
            return false;
        $conn = self::connectPDO();
        $stmt = $conn->prepare("CALL sp_change_priority(:new_Cat_id,:action_level)");
        $stmt->bindParam(':new_Cat_id', $catId);
        $stmt->bindParam(':action_level', $actionLevel);
        $ret = false;
        if ($stmt->execute())
                $ret = true;
        self::disconnect($conn);
        return $ret;
    }
}

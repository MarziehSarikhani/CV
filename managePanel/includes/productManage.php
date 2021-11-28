<?php
require_once ("../../includes/includes.php");
ini_set('session.cookie_httponly',1);
ini_set('session.cookie_lifetime',3600);
session_start();
if(!isset($_SESSION["user"]) or $_SESSION['user'] !== USER_NAME or
    !isset($_SESSION['userIP']) or $_SESSION['userIP'] !== $_SERVER['REMOTE_ADDR'])
       header("location:http://sarikhani.id.ir/");
$response = [];
if(isset($_SERVER['REQUEST_METHOD']) and $_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_POST['id']) and is_numeric($_POST['id'])){
        if(isset($_POST['do'])){
            if($_POST['do'] === "delete"){
                if($product = Product::get_product_by_id($_POST['id'])){
                        deleteImageForDeletedProduct('../../'.$product->slideShow_path);
                        deleteImageForDeletedProduct('../../'.$product->picture_path);
                        deleteDirImage('../../images/thumbnails/'.$product->id);
                        deleteDirImage('../../images/gallery/'.$product->id);
                    }
                if(Product::delete($_POST['id'])){
                    if($products = Product::get_all_products(Product::ALLPRODUCTS,$_POST['start'],MAX_PAGING,true))
                        echo json_encode($products);
                    else echo json_encode([]);
                }else echo "error";
            }else if($_POST['do'] === "published"){
                if($product = Product::get_product_by_id($_POST['id']))
                    $product->published = 1;
                if($products = Product::get_all_products(Product::ALLPRODUCTS,$_POST['start'],MAX_PAGING,true))
                    echo json_encode($products);
                else echo json_encode([]);
            }else if($_POST['do'] === "unpublished"){
                if($product = Product::get_product_by_id($_POST['id']))
                    $product->published = 0;
                if($products = Product::get_all_products(Product::ALLPRODUCTS,$_POST['start'],MAX_PAGING,true))
                    echo json_encode($products);
                else echo json_encode([]);
            }
        }else if(isset($_POST['title']) and isset($_POST['categories']) and is_numeric($_POST['categories'])){
            editProduct();
            deleteDir('../../images/temp');
            echo json_encode($response);
        }
    }else if(isset($_POST['title']) and isset($_POST['categories']) and is_numeric($_POST['categories'])){
             saveNewProduct();
             echo json_encode($response);

    }else if(isset($_POST['imagePath'])and isset($_POST['action']) and $_POST['action'] == 'deleteImage'){
        $imagePath=strip_tags($_POST['imagePath']);
        deleteImageForEditProduct($imagePath);
        echo json_encode($response);
    }else if(isset($_FILES['slideShowPicture'])){
        imageValidate($_FILES['slideShowPicture'],"../../images/temp/upload/slideShow");
        echo json_encode($response);
    }else if (isset($_FILES['mainPicture'])){
        imageValidate($_FILES['mainPicture'],"../../images/temp/upload/pictures");
        echo json_encode($response);
    }else if (isset($_FILES['otherPicture'])){
        $file_count = count($_FILES['otherPicture']['name']);
        if($file_count > 10){
            errorResponse("حداکثر مجاز به انتخاب 10 تصویر هستید!");
        }else {
            $Files_post = reArrayFiles($_FILES['otherPicture']);
            for ($i = 0; $i < $file_count; $i++)
                imageValidate($Files_post[$i], "../../images/temp/upload/thumbnails", true);
        }
        echo json_encode($response);
    }
}else if(isset($_SERVER['REQUEST_METHOD']) and $_SERVER['REQUEST_METHOD'] === 'GET'){
    if(isset($_GET['flag']) and $_GET['flag'] === "getCountProducts"){
        if($array = Product::get_count_products())
            echo json_encode($array);
        else echo json_encode([]);
    }else if(isset($_GET['get'])){
        $action = strip_tags($_GET['get']);
        if($products = Product::get_all_products($action,0,MAX_PAGING,true))
            echo json_encode($products);
        else echo json_encode([]);
    }else if(isset($_GET['categoryid']) and is_numeric($_GET['categoryid'])){
        if($products = Product::get_products_by_category($_GET['categoryid'],true))
            echo json_encode($products);
        else echo json_encode([]);
    }else  header("location:http://localhost:8080/Sarikhani/");
}


function imageValidate($files,$imagePath,$saveToGallery=false){
    if ($files['error'] === 0) {
        if ($files['size'] < 204801) {
            if (!empty($files['name'])) {
                if(!file_exists($imagePath)){
                    if(!mkdir($imagePath,0777,true)) {
                        errorResponse('بروز خطا هنگام آپلود !');
                        return;
                    }
                }
                $fileName = rand(100,100000) . time();
                $ext = pathinfo($files['name'], PATHINFO_EXTENSION);
                $path = $imagePath ."/" . $fileName . "." . $ext;
                if (checkImageType( $path)) {
                    if (!is_file( $path)) {
                        if (!imageSave($files['tmp_name'],$path,$saveToGallery)) {
                            errorResponse('بروز خطا هنگام آپلود فایل !');
                        }else successResponse($path);
                    }
                } else  errorResponse( "فقط مجاز به آپلود فایل از نوع jpg , png می باشید.");
            }else  errorResponse('بروز خطا هنگام آپلود فایل !');
        } else errorResponse("حجم فایل آپلود شده بیشتر از 200 کیلو بایت است.");
    }else if($files['error'] === 1 || $files['error'] === 2){
        errorResponse('حجم فایل آپلود شده بیشتر از 200 کیلو بایت است. ');
    }else if($files['error'] === 4){
        errorResponse('لطفا تصویر مورد نظر را انتخاب نمایید! ');
    }
}
function checkImageType($filePath){
    $fileType = pathinfo($filePath, PATHINFO_EXTENSION);
    if($fileType){
        $allowTypes = array('jpg', 'png', 'jpeg');
        if(in_array($fileType,$allowTypes))
            return true;
    }else{
        $fileType = getimagesize($filePath)['mime'];
        $allowTypes = array('image/png', 'image/jpg', 'image/jpeg');
        if(in_array($fileType,$allowTypes))
            return true;
    }
    return false;
}
function imageSave($imagePath,$path,$saveToGallery=false) {
    $sourceProperties = getimagesize($imagePath);
    $imageType = $sourceProperties[2];
    switch ($imageType) {
        case IMAGETYPE_PNG:
            $imageResourceId = imagecreatefrompng($imagePath);
            $targetLayer = imageResize($imageResourceId,$sourceProperties[0],$sourceProperties[1]);
            imagepng($targetLayer, $path );
            if($saveToGallery){
                $targetLayer = imageResize($imageResourceId,$sourceProperties[0],$sourceProperties[1],true);
                $path = str_replace("thumbnails","gallery",$path);
                $dir = dirname($path);
//                $lastSlash = strrpos($path,"/");
//                $dir = substr($path,0,$lastSlash);
                if(!file_exists($dir)) {
                    if (mkdir($dir, 0777, true))
                        imagepng($targetLayer, $path);
                }else  imagepng($targetLayer, $path);
            }
            return true;
            break;
        case IMAGETYPE_JPEG:
            $imageResourceId = imagecreatefromjpeg($imagePath);
            $targetLayer = imageResize($imageResourceId,$sourceProperties[0],$sourceProperties[1]);
            imagejpeg($targetLayer, $path );
            if($saveToGallery) {
                $targetLayer = imageResize($imageResourceId, $sourceProperties[0], $sourceProperties[1], true);
                $path = str_replace("thumbnails","gallery",$path);
                $dir = dirname($path);
//                $lastSlash = strrpos($path,"/");
//                $dir = substr($path,0,$lastSlash);
                if(!file_exists($dir)) {
                    if (mkdir($dir, 0777, true))
                        imagepng($targetLayer, $path);
                }else  imagepng($targetLayer, $path);
            }
            return true;
            break;
        default:
            return false;
            break;
    }
}
function imageResize($imageResourceId,$width,$height,$saveToGallery=false) {
    if($saveToGallery){
        $targetWidth =700;
        $targetHeight =700;
    }else{
        $targetWidth =400;
        $targetHeight =267;
    }
    $targetLayer=imagecreatetruecolor($targetWidth,$targetHeight);
    $transparentColor = imagecolorallocatealpha($targetLayer,0,0,0,127);
    imagefill($targetLayer,0,0,$transparentColor);
    imagecolortransparent($targetLayer,$transparentColor);
    imagecopyresampled($targetLayer,$imageResourceId,0,0,0,0,$targetWidth,$targetHeight, $width,$height);
    return $targetLayer;
}



function reArrayFiles($file_post) {
    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);
    for ($i=0; $i<$file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_post[$key][$i];
        }
    }
    return $file_ary;
}

function imageHandlerForSave($product,$arrayTitlePictures = []){
    if(file_exists('../../images/temp/delete')){
        if(file_exists('../../images/temp/delete/images/slideShow')){
            if(removeFile('../../images/temp/delete/images/slideShow/','../../images/slideShow/'))
                $product->slideShow_path = "";
        }
        if(file_exists('../../images/temp/delete/images/pictures')){
            if(removeFile('../../images/temp/delete/images/pictures/','../../images/pictures/'))
                $product->picture_path = "";
        }
        if(file_exists('../../images/temp/delete/images/thumbnails/'.$product->id)){
            if($picturesPath = removeFile('../../images/temp/delete/images/thumbnails/'.$product->id.'/','../../images/thumbnails/'.$product->id.'/')){
                for($i = 0 ; $i < count($picturesPath) ; $i++)
                    PicturesProduct::delete($product->id,substr($picturesPath[$i],6));
            }
            removeFile('../../images/temp/delete/images/thumbnails/'.$product->id.'/','../../images/gallery/'.$product->id.'/');
        }
    }
    if(file_exists('../../images/temp/upload')){
        if(file_exists('../../images/temp/upload/slideShow')){
            if($slideShowPath = copyFileInDirAndGetNameFile('../../images/temp/upload/slideShow/','../../images/slideShow/'))
                $product->slideShow_path = str_replace('../../','',$slideShowPath[0]);
        }
        if(file_exists('../../images/temp/upload/pictures')){
            if($mainPicturePath = copyFileInDirAndGetNameFile('../../images/temp/upload/pictures/','../../images/pictures/'))
                $product->picture_path = str_replace('../../','',$mainPicturePath[0]);
        }
        if(file_exists('../../images/temp/upload/thumbnails')) {
            if ($arrayPathPictures = copyFileInDirAndGetNameFile('../../images/temp/upload/thumbnails/', '../../images/thumbnails/' . $product->id . '/')) {
                if ($arrayTitlePictures) {
                    $patternImageNameFromTitle = '/^name=(\d+\.(?:jpg|png|jpeg))/';
                    $patternImageTitle = '/&title=(.*)&id=\d*$/';
                    for ($i = 0; $i < count($arrayTitlePictures); $i++) {
                        preg_match($patternImageNameFromTitle, $arrayTitlePictures[$i], $imageName);
                        if($imageName)
                            if ($imageName[1]) {
                                $patternImageNameFromPath = '/' . $imageName[1] . '$/';
                                for ($j = 0; $j < count($arrayPathPictures); $j++) {
                                    preg_match($patternImageNameFromPath, $arrayPathPictures[$j], $match);
                                    if($match)
                                        if ($match[0]) {
                                            preg_match($patternImageTitle, $arrayTitlePictures[$i], $titlePicture);
                                            $title = "";
                                            if($titlePicture)
                                                if($titlePicture[1])
                                                    $title = $titlePicture[1];
                                            PicturesProduct::insert([
                                                "path" => substr($arrayPathPictures[$j],6),
                                                "product_id" => $product->id,
                                                "title" => $title
                                            ]);
                                            array_splice($arrayPathPictures, $j, 1);
                                            break;
                                        }
                                }
                            }
                    }
                }
            }
        }
        if(file_exists('../../images/temp/upload/gallery')){
            copyFileInDirAndGetNameFile('../../images/temp/upload/gallery/','../../images/gallery/'.$product->id.'/');
        }
    }
}
function removeFile($firstPath,$deletePath){
    $array = [];
    if($dir = opendir($firstPath)) {
        while (false !== ($entity = readdir($dir)))
            if ($entity != '.' && $entity != '..')
                if (!is_dir($firstPath . $entity))
                    if (is_file($deletePath . $entity)) {
                        $array[] = $deletePath . $entity;
                        unlink($deletePath . $entity);
                    }
        closedir($dir);
    }
    return $array;
}


function copyFileInDirAndGetNameFile($path,$insertPath){
    $array = [];
    if($dir = opendir($path)) {
        while (false !== ($entity = readdir($dir)))
            if ($entity != '.' && $entity != '..')
                if (!is_dir($path . $entity)) {
                    if (is_file($path . $entity)) {
                        if (!file_exists($insertPath))
                            @mkdir($insertPath, 0777, true);
                        if (@copy($path . $entity, $insertPath . $entity))
                            $array[] = $insertPath . $entity;
                    }
                }
        closedir($dir);
    }
    return $array;
}

function saveNewProduct()
{
    $title = strip_tags($_POST['title']);
    $description = ($_POST['description']);
    $link = strip_tags(htmlentities($_POST['link']));
    $keywords = strip_tags($_POST['keywords']);
    $catId = $_POST['categories'];
    if (isset($_POST['publish']) and $_POST['publish'] === "yes")
        $published = 1;
    else
        $published = 0;
    if (isset($_POST['slideShow']) and $_POST['slideShow'] === "yes")
        $slideShow = 1;
    else
        $slideShow = 0;
    if (strlen($title) < 3)
        errorResponse("عنوان نمونه کار معتبر نیست!");
    else if (strlen($link) < 3)
        errorResponse("لینک نمونه کار معتبر نیست!");
    else {
        $array = [
            "title" => $title,
            "description" => $description,
            "link" => $link,
            "category_id" => $catId,
            "slideShow_path" => "",
            "isSlideShow" => $slideShow,
            "picture_path" => "",
            "keywords" => $keywords,
            "published" => $published,
        ];
        if($productId = Product::insert($array)){
            $product = new Product(['id' => $productId]);
            if (file_exists("../../images/temp")) {
                if (isset($_POST['titlePictures']))
                    imageHandlerForSave($product, $_POST['titlePictures']);
                else  imageHandlerForSave($product);
            }
            successResponse("تغییرات با موفیت ذخیره شدند.",$productId);

        }else errorResponse("در ثبت اطلاعات مشکلی بوجود آمده!");
    }
}
function editProduct(){
    $productId = $_POST['id'];
    $title = strip_tags($_POST['title']);
    $description = ($_POST['description']);
    $link = strip_tags(htmlentities($_POST['link']));
    $keywords = strip_tags($_POST['keywords']);
    $catId = $_POST['categories'];
    if(isset($_POST['publish']) and $_POST['publish'] === "yes")
        $published = 1;
    else
        $published = 0;
    if(isset($_POST['slideShow']) and $_POST['slideShow'] === "yes")
        $slideShow = 1;
    else
        $slideShow = 0;
    if(strlen($title) < 3)
        errorResponse("عنوان نمونه کار معتبر نیست!");
    else if(strlen($link) < 3)
        errorResponse("لینک نمونه کار معتبر نیست!");
    else {
        $array = [
            "id" => $productId,
            "title" => $title,
            "description" => $description,
            "link" => $link,
            "category_id" => $catId,
            "isSlideShow" => $slideShow,
            "keywords" => $keywords,
            "published" => $published,
        ];
        if (Product::update($array)) {
            $product = new Product(['id' => $productId]);
            if (file_exists("../../images/temp")) {
                if (isset($_POST['titlePictures']))
                    imageHandlerForSave($product, $_POST['titlePictures']);
                else  imageHandlerForSave($product);
            }
            if (isset($_POST['titlePictures'])) {
                $patternImageID = '/&id=(\d*)$/i';
                $patternImageTitle = '/&title=(.*)&id=\d*$/i';
                for ($i = 0; $i < count($_POST['titlePictures']); $i++) {
                    preg_match($patternImageID, $_POST['titlePictures'][$i], $IDPicture);
                    if ($IDPicture)
                        if (isset($IDPicture[1])) {
                            $pictureProduct = new PicturesProduct(['id' => $IDPicture[1]]);
                            preg_match($patternImageTitle, $_POST['titlePictures'][$i], $titlePicture);
                            $title = "";
                            if ($titlePicture)
                                if ($titlePicture[1])
                                    $title = $titlePicture[1];
                            $pictureProduct->title = $title;
                        }
                }
            }
            successResponse("تغییرات با موفیت ذخیره شدند.",$productId);
        } else errorResponse("بروز خطا در ثبت تغییرات!");
    }
}
function deleteImageForEditProduct($imagePath){
    if(checkImageType($imagePath)) {
         $patternAllow= '/^images\/temp\/upload\/\w+\/\d+\.(?:jpg|png|jpeg)$/i';
        $patternGeneral = '/^images\/\w+\//i';
        preg_match($patternGeneral, $imagePath,$match);
        if ($match) {
            if (is_file("../../" . $imagePath)) {
                $dir = dirname($imagePath);
                preg_match($patternAllow,$imagePath,$match);
                if($match){
                    if (unlink("../../" . $imagePath)) {
                       successResponse('فایل با موفقیت حذف شد.');
                    } else errorResponse('بروز خطا هنگام حذف!');
                }else
                    {
                    if(!file_exists("../../images/temp/delete/" . $dir)){
                        if (!mkdir("../../images/temp/delete/" . $dir, 0777, true)) {
                            errorResponse('بروز خطا هنگام حذف!');
                        }else{
                            if (copy("../../" . $imagePath,"../../images/temp/delete/" . $imagePath)) {
                                successResponse('فایل با موفقیت حذف شد.');
                            } else errorResponse('بروز خطا هنگام حذف!');
                        }
                    }else{
                        if (copy("../../" . $imagePath,"../../images/temp/delete/" . $imagePath)) {
                            successResponse('فایل با موفقیت حذف شد.');
                        } else errorResponse('بروز خطا هنگام حذف!');
                    }
                }

            } else errorResponse('فایل وجود ندارد!');
        }else errorResponse('دسترسی غیر مجاز برای حذف فایل!');
    }else errorResponse('دسترسی غیر مجاز برای حذف فایل!');
}
function deleteImageForDeletedProduct($imagePath){
    if (file_exists($imagePath)) {
        $dir = dirname($imagePath);
        $imageName = basename($imagePath);
        if ($handle = opendir($dir)) {
            while (false !== ($entity = readdir($handle))) {
                if ($entity != '.' && $entity != '..')
                    if (!is_dir($dir . $entity))
                        if ($imageName === $entity)
                            unlink($dir.'/'. $entity);
            }
            closedir($handle);
        }
    }
}
function deleteDirImage($dir){
    if(file_exists($dir)) {
        if (substr($dir, strlen($dir) - 1, 1) != '/')
            $dir .= '/';
        if ($handle = opendir($dir)) {
            while (($obj = readdir($handle)) !== false) {
                if ($obj != '.' && $obj != '..') {
                    if (is_dir($dir . $obj)) {
                        if (!deleteDir($dir . $obj))
                            return false;
                    } elseif (is_file($dir . $obj)) {
                        if (!unlink($dir . $obj))
                            return false;
                    }
                }
            }
            closedir($handle);
            if (!@rmdir($dir))
                return false;
            return true;
        }
    }
    return false;
}
function errorResponse($message){
    global $response;
    $response[] = [
        'message' => $message,
        'status' => 0
    ];

}
function successResponse($message,$id=0){
    global $response;
    $response[] = [
        'message' => $message,
        'status' => 1,
        'id' => $id
    ];

}

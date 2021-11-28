<?php
include_once("../../includes/includes.php");
ini_set('session.cookie_httponly',1);
ini_set('session.cookie_lifetime',3600);
session_start();
if(!isset($_SESSION["user"]) or $_SESSION['user'] !== USER_NAME or
    !isset($_SESSION['userIP']) or $_SESSION['userIP'] !== $_SERVER['REMOTE_ADDR'])
    header("location:http://sarikhani.id.ir/");
$response = '';
if(isset($_POST['catName']) and isset($_POST['parentID']) and is_numeric($_POST['parentID'])){
    $catName = trim(strip_tags($_POST['catName']));
    $parentID = trim(strip_tags($_POST['parentID']));
    if(strlen($catName) < 3 || strlen($catName) >101 || !is_numeric($parentID))
        errorResponse("تعداد کاراکترهای نام گروه نامعتبر است!");
    else {
        if (Category::insert([
            'catName' => $catName,
            'parentID' => $parentID
        ])) {
           successResponse('گروه جدید با موفقیت ایجاد شد.');
        }else errorResponse("بروز خطا در هنگام ثبت گروه جدید!");
    }
    echo json_encode($response);
}else if(isset($_POST['catID']) and is_numeric($_POST['catID'])){
    $catID = trim(strip_tags($_POST['catID']));
    if(Category::delete($catID))
        successResponse("گروه با موفقیت حذف شد.");
    else
        errorResponse("بروز خطا! ممکن است یکی از نمونه کارها جزو گروه انتخابی باشد.");
    echo json_encode($response);
}else if(isset($_POST['id']) and is_numeric($_POST['id']) and isset($_POST['categoryName']) and isset($_POST['do']) and $_POST['do'] === "edit"){
    $category = new Category([
        'id' => $_POST['id']
    ]);
    if($category->name = strip_tags($_POST['categoryName']))
        successResponse("تغییر نام گروه با موفقیت انجام شد.");
    else errorResponse('بروز خطا!');
    echo json_encode($response);
}else if(isset($_POST['id']) and is_numeric($_POST['id']) and isset($_POST['actionForChangeLevel'])){
    $catId = $_POST['id'];
    $action = strip_tags($_POST['actionForChangeLevel']);
    if(Category::changeLevel($catId,$action))
        successResponse("تغییر سطح گروه با موفقیت انجام شد.");
    else
        errorResponse('بروز خطا!');
    echo json_encode($response);
}
else{
    $flag = file_get_contents("php://input");
    if($flag === "getCategories"){
        class CAT{
            public $cats;
            public $childs;
            function __construct($cat,$childArray = []){
                $this->cats = $cat;
                $this->childs = $childArray;
            }
        }
        $output = [];
        if($cats = Category::getCategoriesByParentId(0,true)){
            foreach ($cats as $cat){
                $childArray= [];
                if($childs = Category::getCategoriesByParentId($cat['id'],true)){
                    $childArray = $childs;
                }
                $output[] = new CAT($cat,$childArray);
            }
        }
        echo json_encode($output);
    }else
        header('location: http://sarikhani.id.ir/');
}

function errorResponse($message){
    global $response;
    $response = [
        'message' => $message,
        'status' => 0
    ];

}
function successResponse($message){
    global $response;
    $response = [
        'message' => $message,
        'status' => 1
    ];
}
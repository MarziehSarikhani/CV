<?php
require_once ("../includes/includes.php");
ini_set("session.cookie_httponly",1);
ini_set("session.cookie_lifetime",3600);
session_start();
recordLog("index");
if(!isset($_SESSION["user"]) or $_SESSION['user'] !== USER_NAME or
    !isset($_SESSION['userIP']) or $_SESSION['userIP'] !== $_SERVER['REMOTE_ADDR'])
    header("location:./login.php");
if(isset($_GET['action']) and $_GET['action'] == 'logout'){
    $_SESSION = array();
    session_destroy();
    header("location:./login.php");
}
recordLog("loginSuccess");
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
     <title>پنل مدیریت</title>
     <meta charset="UTF-8" />
     <meta name="viewport" content="width=device-width,initial-scale=1.0" />
     <link href="http://sarikhani.id.ir/contents/reset.css" rel="stylesheet"/>
     <link href="http://sarikhani.id.ir/contents/myGrideSystem.css" rel="stylesheet"/>
     <link href="http://sarikhani.id.ir/contents/managePanel/homePage.css" rel="stylesheet"/>
</head>
<body>
  <main class="wrapper row">
    <aside class="row col-xs-12 col-sm-12 col-md-2" id="sidebar">
        <section id="adminMenu">
            <nav>
                <header>اعمال مدیریتی</header>
                <ul>
                    <li><a href="http://sarikhani.id.ir/" target="_blank">صفحه اصلی</a></li>
                    <li><a href="./">پیشخوان</a></li>
                    <li><a href="./?action=categories">مدیریت گروه ها</a></li>
                    <li><a href="./?action=messages">مدیریت پیام ها</a></li>
                    <li><a href="./?action=products">مدیریت نمونه کارها</a></li>
                    <li><a href="./?action=newProducts">ایجاد نمونه کار جدید</a></li>
                    <li><a href="./?action=logout">خروج</a></li>
                </ul>
            </nav>
        </section>
    </aside>
    <section class="row col-xs-12 col-sm-12 col-md-10" id="content">
                <?php
                if(isset($_GET['action']) and $_GET['action'] == 'categories') {
                    include('includes/categories.php');
                }
                else if(isset($_GET['action']) and $_GET['action'] == 'messages') {
                    include('includes/messages.php');
                }
                 else if(isset($_GET['action']) and $_GET['action'] == 'products'){
                     deleteDir("../images/temp");
                     include ('includes/products.php');
                 }
                else if(isset($_GET['action']) and $_GET['action'] == 'newProducts'){
                      deleteDir("../images/temp");
                      include ('includes/newProducts.php');
                }
                else{
                    include "includes/dashboard.php";
                }
                ?>
    </section>
  </main>
  <footer id="mainFooter">
      <p >© کلیه حقوق مادی و معنوی این وب سایت متعلق به مرضیه ساریخانی است.
      </p>
  </footer>
</body>
</html>

<?php
require_once ("../includes/includes.php");
ini_set("session.cookie_httponly",1);
ini_set("session.cookie_lifetime",3600);
session_start();
$message = "";
recordLog("login");
 if(isset($_POST['userName']) and isset($_POST['pswUser']) and isset($_POST['chaptcha'])){
     if(isset($_SESSION['chaptcha']))
      if ($_POST['chaptcha'] != strtolower($_SESSION['chaptcha'])) {
             $message = "کد امنیتی اشتباه است!";
         }else if($_POST['userName'] == USER_NAME and HashString::checkHash($_POST['pswUser'],PASSWORD)){
                  $_SESSION['user'] = strip_tags($_POST['userName']);
                  $_SESSION['userIP'] = $_SERVER['REMOTE_ADDR'];
                  header("location:./");
              }else{
                    $message = "نام کاربری یا کلمه عبور اشتباه است.";
              }
 }else if(isset($_SESSION['user']) and $_SESSION['user'] == USER_NAME and $_SESSION['userIP'] == $_SERVER['REMOTE_ADDR'])
     header('location:./');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="http://sarikhani.id.ir/contents/login.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود</title>
</head>
<body>
<div class="login-wrapper">
    <div class="login-container">
        <h1>خوش آمدید</h1>
        <form action="login.php" class="form" id="form-login"  method="post" >
            <input type="text" name="userName" maxlength="30" placeholder="نام کاربری" required="required">
            <input type="password" name="pswUser"  maxlength="30" placeholder="کلمه عبور" required="required">
            <img src="http://sarikhani.id.ir/account/chaptcha1.php" alt="CHAPTCHA" id="image-chaptcha"/>
            <i id="refresh-chaptcha"  >&#8635;</i>
            <input type="text" name="chaptcha" placeholder="سوال امنیتی" max="5" required="required"/>
            <button type="submit" id="login-button">ورود</button>
        </form>
        <p>
            <?php
            echo $message;
            ?>
        </p>
    </div>
</div>

<script src="http://sarikhani.id.ir/contents/login.js"></script>
</body>
</html>

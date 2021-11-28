<?php
include_once("includes.php");
session_start();
$dataPost = file_get_contents("php://input");
$json = json_decode($dataPost,true);
if($json and isset($_SESSION['chaptcha'])) {
        $emailUser =trim(strip_tags($json['emailUser']));
        $messageUser =nl2br(htmlentities(trim(($json['messageUser']))));
        if ($_SESSION['chaptcha'] != ($json['chaptcha']))
            echo "errorChaptcha";
        else {
            $emailPattern = '/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,6})+$/';
            if (!preg_match($emailPattern, $emailUser) or strlen($emailUser) > 100)
                echo "errorEmail";
            else if (strlen($messageUser) < 10 or strlen($messageUser) > 43500)
                echo "errorMessage";
            else {
                $result = Comment::insertComment([
                    'comment' => $messageUser,
                    'email' => $emailUser,
                    'user_ip' => $_SERVER['REMOTE_ADDR']
                ]);
                if($result === "ALLOWED"){
                $messageUser = $messageUser . "  sended from: " . $emailUser ;
                $mail = new EmailSender();
     $mail->send("host", "miss.sarikhani@gmail.com", "ایمیل جدید از سایت","$messageUser");
                    echo "success";
                }else if($result === 'NOT ALLOWED')
                    echo "errorNOTALLOWED";
                else
                    echo "error";
            }
        }
}else
    header("location: http://sarikhani.id.ir/");



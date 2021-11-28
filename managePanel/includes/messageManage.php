<?php
include_once("../../includes/includes.php");
ini_set('session.cookie_httponly',1);
ini_set('session.cookie_lifetime',3600);
session_start();
if(!isset($_SESSION["user"]) or $_SESSION['user'] !== USER_NAME or
    !isset($_SESSION['userIP']) or $_SESSION['userIP'] !== $_SERVER['REMOTE_ADDR'])
    header("location:http://sarikhani.id.ir/");
if(isset($_GET['id']) and is_numeric($_GET['id'])){
    $commentID = strip_tags($_GET['id']);
    if(isset($_GET['do']) and $_GET['do'] === 'delete'){
        if(Comment::delete($commentID)){
            echo "success";
        }else
            echo "error";
    }
}else if(isset($_POST['flag']) and $_POST['flag'] === "getMessage" and isset($_POST['start']) and is_numeric($_POST['start'])){
        if($comments = Comment::get_all_comments(true,strip_tags($_POST['start']),MAX_PAGING))
            echo json_encode($comments);
        else
            echo json_encode([]);
}else if(isset($_POST['flag']) and $_POST['flag'] === "getCountMessage"){
        if($array = Comment::get_count_comments())
            echo json_encode($array);
        else
            echo json_encode([]);
}else if(isset($_POST['comment'])){
    $action = strip_tags($_POST['comment']);
    if($comments =  Comment::get_all_comments(true,0,MAX_PAGING,$action))
        echo json_encode($comments);
    else
        echo json_encode([]);
}else if(isset($_POST['email']) and isset($_POST['message']) and isset($_POST['chaptcha']) and isset($_POST['subject'])
          and isset($_POST['parent']) and is_numeric($_POST['parent']) and isset($_POST['SMTP'])){
    if(isset($_SESSION['chaptcha'])) {
        $chaptcha = strip_tags($_POST['chaptcha']);
        if ($_SESSION['chaptcha'] != $chaptcha)
            echo "errorChaptcha";
        else{
            $emailAddress = trim(strip_tags($_POST['email']));
            $message = nl2br(trim(htmlentities($_POST['message'])));
            $subject = trim(htmlentities($_POST['subject']));
            $parentID = strip_tags($_POST['parent']);
            $emailSender = strip_tags($_POST['emailSender']);
            $SMTP = strip_tags($_POST['SMTP']);
            $emailPattern = '/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,6})+$/';
            if ($emailSender)
                if (!preg_match($emailPattern, $emailSender) or strlen($emailAddress) > 100) {
                    echo "errorEmail";
                    return;
                }
            if (!preg_match($emailPattern, $emailAddress) or strlen($emailAddress) > 100) {
                echo "errorEmail";
                return;
            }
             if (strlen($message) < 5 or strlen($message) > 43500) {
                 echo "errorMessage";
                 return;
             }
             if (strlen($subject) < 1 or strlen($subject) > 500) {
                 echo "errorSubject";
                 return;
             }
                    $mail = new EmailSender();
                    if ($emailSender = $mail->send($SMTP, $emailAddress, $subject, $message, $emailSender,true)) {
                        $lastAnswer = Comment::insertAnswer([
                            "commentID" => $parentID,
                            "comment" => $message,
                            "email" => $emailSender,
                            "user_ip" => $_SERVER['REMOTE_ADDR']
                        ], true);
                        if ($lastAnswer)
                            echo json_encode($lastAnswer);
                        else json_encode([]);
                    } else  echo "error";

        }

    }
}else
    header('location: http://sarikhani.id.ir/');


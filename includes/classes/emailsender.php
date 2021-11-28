<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require_once('PHPMailer/PHPMailer.php');
class EmailSender{
    private $mail;
    private $emailPattern = '/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,6})+$/';
    const HOST = "host";
    const GMAIL = "gmail";
    function __construct(){
        $this->mail = new PHPMailer(true);
    }
    public function send($SMTP,$address,$subject,$body,$from = false,$returnEmailSender = false){
        $emailSender = '';
        if($SMTP === self::HOST)
            $this->configWithHostSMTP();
        else
            $this->configWithGmailSMTP();
        $this->mail->Subject = strip_tags($subject);
        $this->mail->Body = htmlentities(nl2br($body));
        if(preg_match($this->emailPattern,$address))
           $this->mail->addAddress(strip_tags($address));
        else return false;
        if($from) {
            if (preg_match($this->emailPattern, $from)) {
                $this->mail->setFrom(strip_tags($from));
                $emailSender = strip_tags($from);
            }
        }else if($SMTP === self::HOST){
                $this->mail->setFrom(EMAIL_USERNAME_HOST);
                $emailSender = EMAIL_USERNAME_HOST;
        }else {
            $this->mail->setFrom(EMAIL_USERNAME);
            $emailSender = EMAIL_USERNAME;
        }
        try {
            if ($this->mail->send())
                if( !$returnEmailSender)
                    return true;
                else
                    return $emailSender;
            else
                return false;
        }catch (Exception $e){
            return false;
        }
    }
    private function configWithHostSMTP(){
        $this->mail->isHTML();
        $this->mail->isSMTP();
        $this->mail->SMTPAuth = true;
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $this->mail->Host = EMAIL_HOST_HOST;
        $this->mail->Port = 465;
        $this->mail->Username = EMAIL_USERNAME_HOST;
        $this->mail->Password = EMAIL_PASSWORD_HOST;
        $this->mail->CharSet = 'utf-8';
    }
    private function configWithGmailSMTP(){
        $this->mail->isHTML();
        $this->mail->isSMTP();
        $this->mail->SMTPAuth = true;
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $this->mail->Host = EMAIL_HOST;
        $this->mail->Port = 465;
        $this->mail->Username = EMAIL_USERNAME;
        $this->mail->Password = EMAIL_PASSWORD;
        $this->mail->CharSet = 'utf-8';
    }
}
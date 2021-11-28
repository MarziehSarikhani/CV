<?php
class HashString{
     const START_SALT = '40&GPam)';
     const END_SALT = '!+48DKnv$%';
     const MID_SALT = '^/*SXnm450v';
     private static function createSaltString($str){
         $midLength = floor(strlen($str)/2);
         return self::START_SALT.substr($str,0,$midLength).self::MID_SALT.substr($str,$midLength).self::END_SALT;
     }
     public static function createHash($str){
         $saltStr = self::createSaltString($str);
         return password_hash($saltStr,PASSWORD_DEFAULT);
     }
     public static function checkHash($str,$correctHash){
         $saltStr = self::createSaltString($str);
         return password_verify($saltStr,$correctHash);
     }
}
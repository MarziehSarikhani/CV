<?php
require_once ("table.php");
class Comment extends Table
{
    protected $data = [
        "id" => 0,
        "comment" => "",
        "email" => "",
        "creation_time" => 0,
        "user_ip" => "",
        "parent_id" => 0,
        "readed" => 0,
        "answer_time" => 0
    ];
    const ALL_COMMENTS = "all";
    const NEW_COMMENTS = "new";
    const ANSWERED_COMMENTS = "answer";
    const NOANSWER_COMMENTS = "noAnswer";

    public static function insertComment($dataArray, $parent_id = 0)
    {
        if(is_numeric($parent_id)) {
            $creation_time = time();
            $body = strip_tags($dataArray['comment']);
            $email = strip_tags($dataArray['email']);
            $userIp = strip_tags($dataArray['user_ip']);
            $ret = false;
            $conn = self::connectPDO();
            $stmt = $conn->prepare("CALL sp_insert_comment(:comment,:email,:creation_time,:user_ip,:parent_id,@result)");
            $stmt->bindParam(':comment', $body);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':creation_time', $creation_time);
            $stmt->bindParam(':user_ip', $userIp);
            $stmt->bindParam(':parent_id', $parent_id);
            if($stmt->execute()){
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
    }

    public static function insertAnswer($dataArray,$returnArray = false){
        $ret = false;
        if(is_numeric($dataArray['commentID'])){
            $body = strip_tags($dataArray['comment']);
            $email = strip_tags($dataArray['email']);
            $userIp = strip_tags($dataArray['user_ip']);
            $creation_time = time();
            $conn = self::connectPDO();
            $stmt = $conn->prepare("CALL sp_insert_comment_answer_select(:comment_id,:comment,:email,:creation_time,:user_ip)");
            $stmt->bindParam(':comment_id', $dataArray['commentID']);
            $stmt->bindParam(':comment', $body);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':creation_time', $creation_time);
            $stmt->bindParam(':user_ip', $userIp);
            $stmt->execute();
            if ($stmt->rowCount()) {
                    $comment = $stmt->fetch(PDO::FETCH_ASSOC);
                    if (!$returnArray)
                        $ret = new Comment($comment);
                    else
                        $ret = $comment;
            }
            self::disconnect($conn);
        }
        return $ret;
    }
    public static function get_all_comments($returnArray = false, $start = 0, $limit = 0, $action = self::ALL_COMMENTS){
        $ret = false;
        if((is_numeric($start) and is_numeric($limit)) and ($action === self::ALL_COMMENTS or $action === self::NEW_COMMENTS or $action === self::ANSWERED_COMMENTS or $action === self::NOANSWER_COMMENTS)) {
            $conn = self::connectPDO();
            $stmt = $conn->prepare("CALL sp_get_all_comments(:start_index,:limit_index,:action)");
            $stmt->bindParam(':start_index', $start);
            $stmt->bindParam(':limit_index', $limit);
            $stmt->bindParam(':action', $action);
            $stmt->execute();
            $ret = [];
            if ($stmt->rowCount()) {
                while ($comment = $stmt->fetch(PDO::FETCH_ASSOC))
                    if ($returnArray)
                        $ret[] = $comment;
                    else
                        $ret[] = new Comment($comment);
            }
            self::disconnect($conn);
        }
        return $ret;
    }

    public static function get_count_comments()
    {
        $comments = self::get_all_comments();
        reset($comments);
        $comment = current($comments);
        $array = [
            'new' => 0,
            'answered' => 0,
            'noAnswered' => 0,
            'all' => 0
        ];
        if ($comment)
            do {
                $array['all']++;
                if ($comment->readed === 0)
                    $array['new']++;
                if ($comment->answer_time != 0)
                    $array['answered']++;
                else if ($comment->answer_time === 0)
                    $array['noAnswered']++;
            } while ($comment = next($comments));
        return $array;
    }

    public static function delete($commentID){
        $ret = false;
            if (is_numeric($commentID)) {
                $conn = self::connectPDO();
                $stmt = $conn->prepare("CALL sp_delete_comment(:comment_id)");
                $stmt->bindParam(":comment_id", $commentID);
                if ($stmt->execute())
                    $ret = true;
                self::disconnect($conn);
            }
        return $ret;
    }

    public static function getComment($commentID){/*return main comment and answers*/
        $ret = [];
        if(is_numeric($commentID)) {
            $conn = self::connectPDO();
            $stmt = $conn->prepare('CALL sp_get_comment_by_id(:comment_id)');
            $stmt->bindParam(':comment_id', $commentID);
            $stmt->execute();
            if ($stmt->rowCount()) {
                while ($comment = $stmt->fetch(PDO::FETCH_ASSOC))
                    $ret[] = new Comment($comment);
                self::disconnect($conn);
            }
        }
        return $ret;
    }
}
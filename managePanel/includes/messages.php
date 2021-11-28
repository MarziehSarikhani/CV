<?php
if(!isset($_SESSION["user"]) or $_SESSION['user'] !== USER_NAME or
    !isset($_SESSION['userIP']) or $_SESSION['userIP'] !== $_SERVER['REMOTE_ADDR'])
       header("location:http://sarikhani.id.ir/");
if(!isset($_GET['action']) or $_GET['action'] !== 'messages')
    header('location: ../');
if(isset($_GET['do']) and $_GET['do'] === "view" and isset($_GET['commentID']) and is_numeric($_GET['commentID']) ){
    viewComment();
}else{
    if (isset($_GET['section']) and is_numeric($_GET['section']))
        $section = $_GET['section'];
    else
        $section = 1;
    $start = ($section - 1) * MAX_PAGING;
    viewListComments($section,$start);
}

function viewListComments($section,$start){
    ?>
    <article id="messages">
    <header id="commentsHead" class="group-header">
        <?php
        if($arrayCountComments = Comment::get_count_comments()){
            ?>
            <a href="./?action=messages&comment=all"> کل پیام ها: <?=$arrayCountComments['all'] ?></a>
            <a href="./?action=messages&comment=new"> پیام های خوانده نشده: <?=$arrayCountComments['new'] ?></a>
            <a href="./?action=messages&comment=answer"> پیام های پاسخ داده: <?=$arrayCountComments['answered'] ?></a>
            <a href="./?action=messages&comment=noAnswer"> پیام های بدون پاسخ: <?=$arrayCountComments['noAnswered'] ?></a>
        <?php
        }
        ?>
    </header>
    <div class="loadingMessage"></div>
<!--    <span id="operationsMessage"></span>-->
    <table id="massagesShow">
       <tr>
           <th>ردیف</th>
           <th>تاریخ ایجاد</th>
           <th>تاریخ پاسخ</th>
           <th>چکیده پیام</th>
           <th>فرستنده</th>
           <th>عملیات</th>
       </tr>
       <?php
       $counter = 1;
       if(isset($_GET['comment']) and $_GET['comment'] === "noAnswer")
           $comments = Comment::get_all_comments(false,$start,MAX_PAGING,Comment::NOANSWER_COMMENTS);
       else if(isset($_GET['comment']) and $_GET['comment'] === 'new')
           $comments = Comment::get_all_comments(false,$start,MAX_PAGING,Comment::NEW_COMMENTS);
       else $comments = Comment::get_all_comments(false,$start,MAX_PAGING);
       if($comments){
           reset($comments);
           $comment = current($comments);
           do{
              // $creationString = convertDateDefaultFormat($comment->creation_time);
               $creation_time = convertDate($comment->creation_time);
               $creationString = $creation_time['year']."/".$creation_time['month_num']."/".$creation_time['day'].
                   " ".$creation_time['hour'].":".$creation_time['minute'];
               if($comment->answer_time > 0){
//                   $answerString = convertDateDefaultFormat($comment->answer_time);
                   $answer_time = convertDate($comment->answer_time);
                   $answerString = $answer_time['year'] . "/" . $answer_time['month_num'] . "/" . $answer_time['day'] .
                       " " . $answer_time['hour'] . ":" . $answer_time['minute'];
               }else $answerString = "----";
               if(!$comment->readed) {
                   $class = 'class="noReadedComment" ';
                   $title = 'title="پیام جدید" ';
               }
               else {
                   $class = "";
                   $title = "";
               }
               ?>
               <tr >
                   <td><?=$counter++; ?></td>
                   <td><?=number2farsi($creationString) ?></td>
                   <td><?=number2farsi($answerString) ?></td>
                   <td <?= $class ?> <?=$title ?> ><?=substr($comment->comment,0,20) ?></td>
                   <td><?=$comment->email ?></td>
                   <td>
                       <a href="./?action=messages&do=view&commentID=<?=$comment->id ?>"><img src="http://sarikhani.id.ir/images/eye.png" alt="مشاهده" title="مشاهده" class="groupMessage"/></a>

                       <img data-name="delete" data-start="<?=$start ?>" data-id="<?=$comment->id ?>" src="http://sarikhani.id.ir/images/delete.png" alt="حذف" title="حذف" class="groupMessage"/>
                       <a href="./?action=messages&do=view&commentID=<?=$comment->id ?>"><img src="http://sarikhani.id.ir/images/Email.png" alt="پاسخ" title="پاسخ" class="groupMessage"/></a>
                   </td>
               </tr>
               <?php
           }while($comment = next($comments));
          }
       ?>
    </table>
    <?php
    $totalComments = $arrayCountComments['all'];
    $totalSections = ceil($totalComments / MAX_PAGING);
    ?>
<div id="paging">
    <p>صفحه‌ی <?=$section ?>  از  <?=$totalSections ?></p>
    <ul>
        <?php
        for($i = 1 ; $i <= $totalSections ; $i++){
            if($i == $section)
                $class = 'class="activeSection"';
            else
                $class = "";
            echo "<li $class><a href=\"./?action=messages&section=$i\" >$i</a></li>";
        }
        ?>
    </ul>
</div>
</article>
    <script src="http://sarikhani.id.ir/contents/managePanel/messagesPage.js"></script>
<?php
}
function viewComment(){
    ?>
    <header class="group-header">
        <p>مشاهده پیام</p>
    </header>
    <?php
    $flagAnswer = false;
    $email = "";
        if($comments = Comment::getComment($_GET['commentID'])){
            reset($comments);
            $comment = current($comments);
            do{
               // $creationString = convertDateDefaultFormat($comment->creation_time);
                $creation_time = convertDate($comment->creation_time);
                $creationString = $creation_time['year']."/".$creation_time['month_num']."/".$creation_time['day'].
                    " ".$creation_time['hour'].":".$creation_time['minute'];
                if($comment->answer_time > 0) {
//                    $answerString = convertDateDefaultFormat($comment->answer_time);
                    $answer_time = convertDate($comment->answer_time);
                    $answerString = $answer_time['year'] . "/" . $answer_time['month_num'] . "/" . $answer_time['day'] .
                        " " . $answer_time['hour'] . ":" . $answer_time['minute'];
                }else $answerString = "----";
                if($comment->parent_id !== 0 && $flagAnswer == false){
                    $flagAnswer = true;
                    echo " <header class='group-header'><p> لیست پاسخ ها:</p></header>";
                                }
                ?>
                <article class="groupComment">
                    <header <?php if($comment->parent_id == 0) echo 'class="recivedComment"'; ?>>
                        <?php
                        if($comment->parent_id == 0){
                            echo "تاریخ دریافت: <time>" . number2farsi($creationString)."</time>";
                            echo "<time> تاریخ آخرین پاسخ ارسالی: ". number2farsi($answerString)."</time>";
                            echo "<p>آی پی کاربر:".number2farsi($comment->user_ip)."</p>";
                            echo "<p>ایمیل کاربر:".$comment->email."</p>";
                        }else{
                            echo "تاریخ ارسال: <time>" . number2farsi($creationString)."</time>";
                            echo "<p>آی پی ارسالی:".number2farsi($comment->user_ip)."</p>";
                            echo "<p>ایمیل ارسالی:".$comment->email."</p>";
                        }
                        ?>
                    </header>
                    <div>
                        <p><?=$comment->comment ?></p>
                    </div>
                    <footer>
                        <?php
                if($comment->parent_id == 0){
                    $email = $comment->email;
                    echo "<a href=\"#\" class='buttonLink' id='sendAnswer'>ارسال پاسخ</a>";
                } ?>
                    </footer>
                </article>
                <?php
            }while($comment = next($comments));
            ?>

<?php

        }
        ?>
    <form id="sendEmailForm" method="post">
        <p>ارسال پاسخ </p>
        <div>
             <label>ایمیل دریافت کننده:</label>
             <span id="messageEmail"></span>
        </div>
        <input type="email" name="email" required="required" maxlength="100" value="<?=$email ?>"/>
        <div>
            <label>ایمیل ارسال کننده:</label>
        </div>
        <input type="email" name="emailSender"  maxlength="100" placeholder="پیش فرض از ایمیل هاست ارسال می شود."/>
        <div>
            <label>موضوع:</label>
            <span id="messageSubject"></span>
        </div>
        <input type="text" name="subject"  maxlength="500" required="required"/>
        <div>
            <label>پاسخ شما:</label>
            <span id="messageText"></span>
        </div>
        <textarea name="message" required="required" maxlength="43500"></textarea><br/>
        <input type="radio" name="SMTP" value="gmail"/>ارسال از طریق gmail
        <input type="radio" name="SMTP" value="host"/>ارسال از طریق host
        <span id="messageSMTP"></span>
        <div>
            <img src="http://sarikhani.id.ir/account/chaptcha1.php" alt="CHAPTCHA" id="image-chaptcha"/>
            <i id="refresh-chaptcha" class="button" >&#8635;</i>
            <span></span>
        </div>
        <input type="text" name="chaptcha" placeholder="سوال امنیتی" max="5" required="required" />
        <input type="hidden" name="parent" value="<?=$_GET['commentID']; ?>"/>
        <input type="submit" value="ارسال" class="button"/>
    </form>
    <span class="message"></span>
    <div class="loading"></div>
    <script src="http://sarikhani.id.ir/contents/managePanel/viewMessagePage.js" rel="script"></script>
<?php
}

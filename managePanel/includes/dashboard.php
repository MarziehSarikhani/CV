<?php
if(!isset($_SESSION["user"]) or $_SESSION['user'] !== USER_NAME or
    !isset($_SESSION['userIP']) or $_SESSION['userIP'] !== $_SERVER['REMOTE_ADDR'])
    header("location: http://sarikhani.id.ir/");
$countProducts = Product::get_count_products();
$comments = Comment::get_count_comments();
if(file_exists('../logs/counterVisitorSite.txt')) {
    $data = file_get_contents('../logs/counterVisitorSite.txt');
    preg_match('/\s+(\d+)/',$data,$match);
    if($match)
        if($match[1])
          $visitCounts = $match[1];
}
else $visitCounts = 0;
?>
<article id="dashboard">
    <header>پنل کاربری مدیر</header>
    <div>
        <table>
            <tr>
                <td><a href="./?action=products&get=published">تعداد نمونه کارهای منتشر شده:</a></td>
                <td><?=$countProducts['published'] ?></td>
            </tr>
            <tr>
                <td><a href="./?action=products&get=unpublished">تعداد نمونه کارهای منتشر نشده:</a></td>
                <td><?=$countProducts['unpublished'] ?></td>
            </tr>
            <tr>
                <td><a href="./?action=messages&comment=all">تعداد کل پیامها:</a></td>
                <td><?=$comments['all'] ?></td>
            </tr>
            <tr>
                <td><a href="./?action=messages&comment=new">تعداد پیام های خوانده نشده:</a></td>
                <td><?=$comments['new'] ?></td>
            </tr>
            <tr>
                <td><a href="./?action=messages&comment=noAnswer">تعداد پیام های بدون پاسخ:</a></td>
                <td><?=$comments['noAnswered'] ?></td>
            </tr>
            <tr>
                <td>تعداد کل بازدید از سایت:</td>
                <td><?=$visitCounts ?></td>
            </tr>
        </table>
    </div>
</article>

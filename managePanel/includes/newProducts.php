<?php
if(!isset($_SESSION["user"]) or $_SESSION['user'] !== USER_NAME or
    !isset($_SESSION['userIP']) or $_SESSION['userIP'] !== $_SERVER['REMOTE_ADDR'])
    header("location:http://sarikhani.id.ir/");
if(!isset($_GET['action']) or $_GET['action'] !== "newProducts")
    header('location: ../');
?>
    <div class="loading"></div>
    <article id="product">
        <header class="group-header">
            ایجاد نمونه کار
        </header>
        <form method="post"  id="editProduct">
            <div>
                <label>عنوان:</label><span></span>
                <input class="group-input" type="text" name="title" value="" required="required"/>
            </div>
            <div>
                <span style="color: darkgoldenrod;">استفاده از "&lt;--more--!&gt;" جهت جدا کردن بخش چکیده از متن اصلی. </span>
                <textarea id="area" name="description"></textarea>
            </div>
            <div>
                <label>لینک:</label><span></span>
                <input class="group-input" type="text" name="link" value="" />
            </div>
            <div>
                <label>کلمات کلیدی:</label>
                <input class="group-input" type="text" name="keywords" value=""/>
            </div>
            <div>
                <label>گروه ها:</label><span></span>
                <ul id="cats">
                    <?php
                    if($cats = Category::getCategoriesByParentId(0)){
                        foreach ($cats as $cat){
                            ?>
                            <li>
                                <input type="radio" name="categories" value="<?=$cat->id?>"/>
                                <label><?=$cat->name?></label>
                            </li>
                            <?php
                        }
                    }
                    ?>
                </ul>
            </div>
            <div>
                <label>نمونه کار انتشار یابد؟</label>
                <input type="checkbox" name="publish" value="yes" />
            </div>
            <div>
                <label>نمونه کار در اسلاید شو نشان داده شود؟</label>
                <input type="checkbox" name="slideShow" value="yes"/>
            </div>
            <div >
                <fieldset>
                    <legend>تصویر اسلاید شو:</legend>
                    <input type="file" name="slideShowPicture" form="slideShowPicForm"/>
                    <input type="submit" value="آپلود تصویر" class="button uploadImage" form="slideShowPicForm"/>
                    <div class="groupPictures row">
                        <span class="messageProduct"></span>
                    </div>
                </fieldset>
            </div>
            <div>
                <fieldset>
                    <legend>تصویر اصلی:</legend>
                    <input type="file" name="mainPicture" form="mainPicForm"/>
                    <input type="submit" value="آپلود تصویر" class="button uploadImage" form="mainPicForm"/>

                    <div class="groupPictures row">
                        <span class="messageProduct"></span>
                    </div>
                </fieldset>
            </div>
            <div>
                <fieldset>
                    <legend>سایر تصاویر:</legend>
                    <input type="file" name="otherPicture[]" form="otherPicForm" multiple="multiple" />
                    <input type="submit" value="آپلود تصویر" class="button uploadImage" form="otherPicForm"/>
                    <div class="groupPictures row">
                        <span class="messageProduct"></span>
                    </div>
                </fieldset>
            </div>
            <div>
                <input type="button" value="ذخیره" class="button" id="sendFormCreate"/>
                <a href="./?action=products" class="button">بازگشت </a>
            </div>
        </form>
        <form method="post" enctype="multipart/form-data" id="slideShowPicForm"></form>
        <form method="post" enctype="multipart/form-data" id="mainPicForm"></form>
        <form method="post" enctype="multipart/form-data" id="otherPicForm"></form>
        <div class="loadingMessage"></div>
        <script src="http://sarikhani.id.ir/contents/CKeditor/ckeditor.js"></script>
        <script src="http://sarikhani.id.ir/contents/managePanel/editProduct.js"></script>
    </article>

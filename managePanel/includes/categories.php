<?php
if(!isset($_SESSION["user"]) or $_SESSION['user'] !== USER_NAME or
    !isset($_SESSION['userIP']) or $_SESSION['userIP'] !== $_SERVER['REMOTE_ADDR'])
    header("location:http://localhost:8080/Sarikhani/");
if(!isset($_GET['action']) or $_GET['action'] !== 'categories')
    header('location: ../');
?>
<div class="loading"></div>
<article id="categories">
    <div>
        <header class="group-header">گروه های فعلی</header>
        <ul id="cats">
            <?php
            if($cats = Category::getCategoriesByParentId(0)){
                foreach ($cats as $cat){
                    ?>
                    <li>
                        <?php
                        echo "<span class='upLevel'>&#x25B2;</span>
                              <span class='downLevel'>&#x25BC;</span>
                              <label class=\"groupMessage\" title=\"ویرایش\" data-catid='$cat->id' >$cat->name</label>".'<img src="http://sarikhani.id.ir/images/edit.png" alt="ویرایش" title="ویرایش" class="groupMessage"/>';
                        if($childs = Category::getCategoriesByParentId($cat->id)){
                            ?>
                            <ul>
                                <?php
                                foreach ($childs as $child){
                                    ?>
                                    <li>
                                        <span class='upLevel'>&#x25B2;</span>
                                        <span class='downLevel'>&#x25BC;</span>
                                        <label class="groupMessage" title="ویرایش" data-catid="<?=$child->id ?>" ><?=$child->name ?></label><img src="http://sarikhani.id.ir/images/edit.png" alt="ویرایش" title="ویرایش" class="groupMessage"/>
                                    </li>
                                    <?php
                                }
                                ?>
                            </ul>
                            <?php
                        }
                        ?>
                    </li>
            <?php
                }
            }
            ?>
        </ul>
    </div>
    <div>
        <header class="group-header">ایجاد گروه</header>
        <form method="post" id="createCat">
            <lable>عنوان:</lable>
            <input type="text" name="catName" maxlength="100" required="required"/>
            <lable>گروه والد:</lable>
            <select name="parentID" required="required">
                <option value="0">بدون والد</option>
                <?php
                if($cats = Category::getCategoriesByParentId(0)){
                    foreach ($cats as $cat){
                        ?>
                        <option value="<?=$cat->id ?>"><?=$cat->name ?></option>
                <?php
                    }
                }
                ?>
            </select>
            <input type="submit" value="ایجاد" class="button"/>
        </form>

    </div>
    <div>
        <header class="group-header">حذف گروه</header>
        <form method="post" id="deleteCat">
            <select name="catID" required="required">
                <option value="-1" selected="selected">--انتخاب کنید--</option>
                <?php
                if($cats = Category::getCategoriesByParentId(0)){
                    foreach ($cats as $cat){
                        ?>
                       <option value="<?=$cat->id ?>" class="headerOption" ><?=$cat->name ?></option>
                        <?php
                        if($childs = Category::getCategoriesByParentId($cat->id)){
                            foreach ($childs as $child){
                                ?>
                                <option value="<?=$child->id ?>" class="childOption">------------ <?=$child->name ?></option>
                                <?php
                            }
                        }
                        ?>
                <?php
                    }
                }
                ?>
            </select>
            <input type="submit" value="حذف" class="button"/>
        </form>
    </div>
    <span id="message"></span>
</article>
<script src="http://sarikhani.id.ir/contents/managePanel/categoriesPage.js"></script>

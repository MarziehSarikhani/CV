<?php
if(!isset($_SESSION["user"]) or $_SESSION['user'] !== USER_NAME or
    !isset($_SESSION['userIP']) or $_SESSION['userIP'] !== $_SERVER['REMOTE_ADDR'])
       header("location:http://sarikhani.id.ir/");
if(!isset($_GET['action']) or $_GET['action'] !== "products")
    header('location: ../');
else if(isset($_GET['do']) and $_GET['do'] === "edit" and isset($_GET['id']) and is_numeric($_GET['id']))
    editProduct();
else {
    if (isset($_GET['section']) and is_numeric($_GET['section']))
        $section = $_GET['section'];
    else
        $section = 1;
    $start = ($section - 1) * MAX_PAGING;
    viewListProducts($section, $start);
}

function viewListProducts($section,$start){
   ?>
    <article id="productsList">
        <header  class="group-header">
            <?php
            if($arrayCountProducts = Product::get_count_products()){
                ?>
                <a href="./?action=products&get=all"> کل نمونه کارها: (<?=$arrayCountProducts['all'] ?>)</a>
                <a href="./?action=products&get=published"> نمونه کارهای منتشر شده: (<?=$arrayCountProducts['published'] ?>)</a>
                <a href="./?action=products&get=unpublished"> نمونه کارهای منتشر نشده: (<?=$arrayCountProducts['unpublished'] ?>)</a>
                <?php
            }
            ?>
        </header>
        <p class="parag">مشاهده نمونه کارها براساس گروه:</p>
        <select id="showProductByCategory">
            <option value="0">انتخاب کنید...</option>
            <?php
            if($cats = Category::getCategoriesByParentId(0)){
              foreach($cats as $cat){
                  ?>
                  <option value="<?=$cat->id?>"><?=$cat->name?></option>
                  <?php
              }
            }
            ?>
        </select>
        <div class="loadingMessage"></div>
        <table id="productShow">
            <tr>
                <th>ردیف</th>
                <th>عنوان</th>
                <th>گروه</th>
                <th>زمان انتشار</th>
                <th>زمان آخرین تغییر</th>
                <th>عملیات</th>
            </tr>
            <?php
            $counter = 1;
            if(isset($_GET['get']) and $_GET['get'] === "published")
                $products = Product::get_all_products(Product::PUBLISHED,$start,MAX_PAGING);
            else if(isset($_GET['get']) and $_GET['get'] === "unpublished")
                $products = Product::get_all_products(Product::UNPUBLISHED,$start,MAX_PAGING);
            else $products = Product::get_all_products(Product::ALLPRODUCTS,$start,MAX_PAGING);
            if($products ){
                reset($products);
                $product = current($products);
                $pattern = '/^http(?:s)?:\/\/.+/';
                do{
                    //$creationString = convertDateDefaultFormat($product->creation_time);
                    //$modifystring = convertDateDefaultFormat($product->modify_time);
                        $creation_time = convertDate($product->creation_time);
                        $creationString = $creation_time['year']."/".$creation_time['month_num']."/".$creation_time['day'].
                        " ".$creation_time['hour'].":".$creation_time['minute'];
                        $modify_time = convertDate($product->modify_time);
                        $modifystring = $modify_time['year'] . "/" . $modify_time['month_num'] . "/" . $modify_time['day'] .
                            " " . $modify_time['hour'] . ":" . $modify_time['minute'];
                        if($product->published == 1){
                            $publishText = "مخفی";
                            $class = "";
                            $name = "unpublished";
                        }else{
                            $publishText = "انتشار";
                            $class = 'class="unpublished"';
                            $name = "published";
                        }
                        $editLink = "./?action=products&amp;do=edit&amp;id={$product->id}";
                        preg_match($pattern,$product->link,$match);
                        ?>
                    <tr>
                        <td><?=$counter++; ?></td>
                        <td><a <?php  if($match) echo "target=\"_blank\" href=\"{$product->link}\""; else  echo "href=\"http://sarikhani.id.ir/نمونه-کار/{$product->link}\""; ?> class="link" title="<?=$product->link?>"><?=$product->title?></a></td>
                        <td><?=$product->category_name?></td>
                        <td><?=number2farsi($creationString); ?></td>
                        <td><?=number2farsi($modifystring); ?></td>
                        <td>
                            <a data-name="<?=$name?>" data-id="<?=$product->id?>" data-start="<?=$start?>" href="#" <?=$class ?>><?=$publishText?></a>
                            <a href="<?=$editLink?>">ویرایش</a>
                            <a data-name="delete" data-id="<?=$product->id?>" data-start="<?=$start?>" href="#">حذف</a>
                        </td>
                    </tr>
            <?php
                }while($product = next($products));
            }
            ?>
        </table>
    </article>
    <?php
    $totalComments = $arrayCountProducts['all'];
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
                echo "<li $class><a href=\"./?action=products&section=$i\" >$i</a></li>";
            }
            ?>
        </ul>
    </div>
    <script src="http://sarikhani.id.ir/contents/managePanel/productsPage.js"></script>
<?php
}
function editProduct(){
    $productId = $_GET['id'];
    $product = Product::get_product_by_id($productId);
    ?>
    <div class="loading"></div>
    <article id="product">
        <header class="group-header">
            ویرایش نمونه کار
        </header>
        <form method="post"  id="editProduct">
            <div>
                   <label>عنوان:</label><span></span>
                   <input class="group-input" type="text" name="title" value="<?=$product->title?>" required="required"/>
            </div>
            <div>
                  <span style="color: darkgoldenrod;">استفاده از "&lt;--more--!&gt;" جهت جدا کردن بخش چکیده از متن اصلی. </span>
                  <textarea id="area" name="description"><?=$product->description?></textarea>
            </div>
            <div>
                <label>لینک:</label><span></span>
                <input class="group-input" type="text" name="link" value="<?=$product->link?>" />
            </div>
            <div>
                <label>کلمات کلیدی:</label>
                <input class="group-input" type="text" name="keywords" value="<?=$product->keywords?>"/>
            </div>
            <div>
                <label>گروه ها:</label><span></span>
                <ul id="cats">
                    <?php
                    if($cats = Category::getCategoriesByParentId(0)){
                        foreach ($cats as $cat){
                            if($product->category_id == $cat->id)
                                $state = "checked";
                            else
                                $state = "";
                            ?>
                            <li>
                                <input type="radio" name="categories" <?=$state?> value="<?=$cat->id?>"/>
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
                <?php
                if($product->published == 1)
                    $state = "checked";
                else
                    $state = "";
                ?>
                <input type="checkbox" name="publish" value="yes" <?=$state ?> />
            </div>
            <div>
                <label>نمونه کار در اسلاید شو نشان داده شود؟</label>
                <?php
                if($product->isSlideShow === 1)
                    $state = "checked";
                else
                    $state = "";
                ?>
                <input type="checkbox" name="slideShow" value="yes" <?=$state?>/>
            </div>
            <div id="slideShowPicture" >
                    <fieldset>
                        <legend>تصویر اسلاید شو:</legend>
                        <input type="file" name="slideShowPicture" form="slideShowPicForm"/>
                        <input type="submit" value="آپلود تصویر" class="button uploadImage" form="slideShowPicForm"/>
                        <div class="groupPictures row">
                        <?php
                        if(strlen($product->slideShow_path)>0){
                            if(is_file("../".$product->slideShow_path)) {
                                ?>
                                    <figure class="groupPicAndBtn ">
                                        <img src="http://sarikhani.id.ir/<?= $product->slideShow_path ?>" class="groupPic" alt="تصویر نمونه کار"/>
                                        <input type="image"  src="http://sarikhani.id.ir/images/delete.png" class="groupBtn deleteImage" alt="حذف" title="حذف"/>
                                    </figure>

                                <?php
                            }
                        }
                        ?>
                            <span class="messageProduct"></span>
                        </div>
                   </fieldset>
            </div>
            <div id="mainPicture">
                    <fieldset>
                        <legend>تصویر اصلی:</legend>
                        <input type="file" name="mainPicture" form="mainPicForm"/>
                        <input type="submit" value="آپلود تصویر" class="button uploadImage" form="mainPicForm"/>

                        <div class="groupPictures row">
                        <?php
                        if(strlen($product->picture_path) > 0){
                            if(is_file("../".$product->picture_path)){
                                ?>
                            <figure class="groupPicAndBtn ">
                                <img src="http://sarikhani.id.ir/<?= $product->picture_path ?>"  class="groupPic" alt="تصویر نمونه کار"/>
                                <input type="image"  src="http://sarikhani.id.ir/images/delete.png" class="groupBtn deleteImage"  alt="حذف" title="حذف"/>
                            </figure>
                                <?php
                            }
                        }
                        ?>
                            <span class="messageProduct"></span>
                        </div>
                    </fieldset>
            </div>
            <div id="otherPictures">
                    <fieldset>
                        <legend>سایر تصاویر:</legend>
                        <input type="file" name="otherPicture[]" form="otherPicForm" multiple="multiple" />
                        <input type="submit" value="آپلود تصویر" class="button uploadImage" form="otherPicForm"/>
                        <div class="groupPictures row">
                        <?php
                        if($product->pictures){
                            foreach ($product->pictures as $pic){
                                if(is_file("../".$pic->path)){
                                    ?>
                                        <figure class="groupPicAndBtn ">
                                            <img src="http://sarikhani.id.ir/<?= $pic->path ?>"  class="groupPic" alt="تصویر نمونه کار"/>
                                            <input type="image" src="http://sarikhani.id.ir/images/delete.png" class="groupBtn deleteImage"  alt="حذف" title="حذف"/>
                                            <input type="text" class="titlePictures group-input"  placeholder="عنوان تصویر را وارد کنید" data-imageid="<?= $pic->id?>"  value="<?=$pic->title?>"  />
                                        </figure>
                                    <?php
                                }
                            }
                        }
                        ?>
                            <span class="messageProduct"></span>
                        </div>
                    </fieldset>
            </div>
            <div>
                <input type="hidden" name="id" value="<?=$productId?>"/>
                <input type="button" value="بروزرسانی" class="button" id="sendFormEdit"/>
                <a href="./?action=products" class="button">بازگشت </a>
            </div>
        </form>
        <form method="post" enctype="multipart/form-data" id="slideShowPicForm"></form>
        <form method="post" enctype="multipart/form-data" id="mainPicForm"></form>
        <form method="post" enctype="multipart/form-data" id="otherPicForm"></form>
        <div class="loadingMessage"></div>
    </article>
    <script src="http://sarikhani.id.ir/contents/CKeditor/ckeditor.js"></script>
    <script src="http://sarikhani.id.ir/contents/managePanel/editProduct.js"></script>
<?php
}
?>

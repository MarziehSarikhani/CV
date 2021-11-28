<?php
include_once ("includes.php");
if(!isset($uri))
    exit(1);
$categories = [];
$pattern = '/^http(?:s)?:\/\/.+/';
if($products = Product::get_all_products()){
    reset($products);
    $product = current($products);
    do{
        $categories[$product->category_id] = $product->category_name;
    }while($product = next($products));
    $categories = Category::sortCategories($categories,0);
}
?>
<main >
    <section id="mainSection" class="wrapper row">
        <h1>نمونه کار</h1>
        <a class="bottun" href="http://sarikhani.id.ir/" >صفحه اصلی</a>
        <?php
        if($categories) {
            foreach ($categories as $category) {
                ?>
                <section class="row products">
                    <h2>نمونه کارهای <?= $category->name ?></h2>
                    <?php
                    reset($products);
                    $product = current($products);
                    do{
                        if($product->category_id == $category->id ){
                            preg_match($pattern,$product->link,$match);
                            ?>
                            <article class="col-xs-12 col-sm-6 col-md-4 ">
                                <a <?php  if($match) echo "target=\"_blank\" href=\"{$product->link}\""; else  echo "href=\"http://sarikhani.id.ir/نمونه-کار/{$product->link}\""; ?>>
                                    <figure>
                                        <img src="http://sarikhani.id.ir/<?=$product->picture_path ?>" alt="<?=$product->title ?>"/>
                                        <figcaption class="popup">
                                            <h3><?=$product->title ?></h3>
                                        </figcaption>
                                    </figure>
                                </a>
                                <a class="showDescription" data-anchor="site-<?=$product->id ?>" href="<?=$product->title ?>">مشاهده توضیحات</a>
                            </article>
                    <?php
                        }
                    }while($product = next($products));
                    ?>

                </section>
                <?php
            }
            ?>
        <section id="descriptionPanel" class="fadeOut hide">
                <?php
                reset($products);
                $product = current($products);
                do{
                    preg_match($pattern,$product->link,$match);
                    ?>
                    <div class="descriptionItem hide" id="site-<?=$product->id ?>">
                        <h3><?=$product->title ?></h3>
                        <p>
                            <?php
                            $description = substr($product->description,0,strpos($product->description,'<!--more-->'));
                            ?>
                            <?=$description ?>
                        </p>
                        <a <?php  if($match) echo "target=\"_blank\" href=\"{$product->link}\""; else  echo "href=\"http://sarikhani.id.ir/نمونه-کار/{$product->link}\""; ?> class="bottun">مشاهده وب سایت</a>
                    </div>
                    <?php

                }while($product = next($products));
                ?>
            <span id="close">
                &times;
            </span>
        </section>
            <?php
        }
        ?>
    </section>
</main>

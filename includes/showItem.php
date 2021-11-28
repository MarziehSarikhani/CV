<?php
if(!isset($uri))
    exit(1);
?>
<main class="wrapper">
    <div class="topPanel">
        <h1><?=$product->title ?></h1>
        <nav class="row">
            <a class="bottun" href="http://sarikhani.id.ir/" >صفحه اصلی</a><span>&rsaquo;</span>
            <a class="bottun" href="http://sarikhani.id.ir/نمونه-کار" >نمونه کار</a><span>&rsaquo;</span>
            <a class="bottun" href="http://sarikhani.id.ir/نمونه-کار/<?=$product->link;?>"><?=$product->title;?></a>
        </nav>
    </div>

    <section class="galery">
        <div class="row productDesc">
            <?php
            if($product->picture_path != ""){
                ?>
                <figure>
                    <img src="http://sarikhani.id.ir/<?= $product->picture_path ?>" alt="<?=$product->title ?>"/>
                </figure>
            <?php
            }
            ?>
            <div>
                <?= substr($product->description,0,strpos($product->description,('<!--more-->')) + 11) ?>
            </div>
            <?= substr($product->description,strpos($product->description,('<!--more-->')) + 11) ?>
        </div>
        <?php
        if($product->pictures){
            ?>
            <div id="picturs">
                <?php
                foreach ($product->pictures as $picture){
                    if(is_file('./'.$picture->path)) {
                        $alt = str_replace('طراحی', "", $product->title);
                        ?>
                        <div class="popup" title="<?= $picture->title ?>">
                            <img class="show" src="http://sarikhani.id.ir/<?= $picture->path ?>"
                                 alt="<?= $picture->title . $alt ?>"/>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
     <?php
        }
        ?>
        <section id="imagePanel" class="hide" tabindex="0">
            <button class="noSelect" id="close">&times;</button>
            <div id="control" class="noSelect">
                <span id="nextProduct" title="بعدی">&lt;</span>
                <span id="prevProduct" title="قبلی">&gt;</span>
            </div>
        </section>
    </section>

</main>

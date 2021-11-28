<?php

if(!isset($uri))
        exit(1);
       
?>
<main>
    <section id="mainSection">
        <nav>
            <a class="bottun" href="http://sarikhani.id.ir/" >صفحه اصلی</a>
            <a class="bottun" href="http://sarikhani.id.ir/نمونه-کار" >نمونه کار</a>
        </nav>
        <h1>صفحه مورد نظر در سایت یافت نشد</h1>
        <h2>Page Not Found 404</h2>
        <p>تا لحظاتی دیگر به به صفحه اصلی سایت منتقل خواهید شد.</p>
    </section>
</main>
<?php
  header("Refresh: 5;url=http://sarikhani.id.ir/");
?>

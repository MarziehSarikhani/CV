<?php

class Footer{
    private $html;
    public function __construct(){
        $this->html = <<<EOS
<footer id="mainFooter" class="section" data-anchor="page2">
        <section class="wrapper flex">
            <div >
                             <div class="borderBottom">
                                <p>طراحی سایت توسط مرضیه ساریخانی</p>                                 
                                <span class="icon-font" title="Gmail">E</span>
                                 <p>contact@sarikhani.id.ir</p> 
                            </div>
                            <div class="borderBottom">
                                <a target="_blank" href="http://instagram.com/sarikhani_._._" title="صفحه من در اینستاگرام">
                                    <p>صفحه من در اینستاگرام </p>
                                    <span class="icon-font" title="instagram">H</span>                            
                                </a>
                            </div>
                            <div>
                                <a target="_blank" href="https://www.linkedin.com/in/marzieh-sarikhani" title="صفحه من در لینکدین">
                                    <p>صفحه من در لینکدین</p>
                                    <span class="icon-font f-20" title="linkdin">I</span>                            
                                </a>
                            </div>
                    </div>
            <form id="formFooter"  method="post">
                <h4>ارتباط با من</h4>
                <label>ایمیل شما:</label>
                <span id="messageEmail"></span>
                <input type="email" name="emailUser" class="formGroup"  placeholder="example@domain.com" required="required"  maxlength="100"/><br />
                <label>پیام شما:</label>
                <span id="messageText"></span>
                <textarea name="messageUser" class="formGroup"  required="required" maxlength="43500"></textarea>
                <div id="message">
                    <img src="http://sarikhani.id.ir/account/chaptcha1.php" alt="CHAPTCHA" id="image-chaptcha"/>
                    <i id="refresh-chaptcha"  >&#8635;</i>
                    <span></span>
                </div>
                <input type="text" name="chaptcha" placeholder="سوال امنیتی" max="5" required="required" class="formGroup"/>
                <button type="submit" id="sendButton">ارسال</button>
            </form>
        </section>       
    </footer>
EOS;

    }
    public function render(){
        echo $this->html;
    }

}
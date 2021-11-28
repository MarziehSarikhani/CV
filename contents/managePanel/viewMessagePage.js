const sendAnswer = document.getElementById('sendAnswer');
const refreshChaptcha = document.getElementById('refresh-chaptcha');
const emailPattern = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,6})+$/;
const sendEmailForm = document.getElementById('sendEmailForm');
const emailInputs = sendEmailForm.querySelectorAll('input[type="email"]');
const messagetxtarea = sendEmailForm.querySelector('textarea[name="message"]');
const sendButton =  sendEmailForm.querySelector('input[type="submit"]');
const chaptcha = sendEmailForm.querySelector('input[name=chaptcha]');
const subject = sendEmailForm.querySelector('input[name="subject"]');
const SMTP = sendEmailForm.querySelectorAll('input[name="SMTP"]');
let validate = true;
subject.addEventListener('blur',validateSubject);
sendButton.addEventListener('click',sendAjaxHandler);
emailInputs.forEach(emailInput=>{emailInput.addEventListener('keyup',validateEmail);
    emailInput.addEventListener('blur',validateEmail); });

messagetxtarea.addEventListener('keypress', validateMessage);
messagetxtarea.addEventListener('blur', validateMessage);
function validateEmail(){
    const messageEmail = document.getElementById("messageEmail");
    if ( this.value.trim().length > 0 &&(!emailPattern.test(this.value) || this.value.length > 100)) {
        this.classList.add('invalid');
        messageEmail.classList.add("message-error");
        messageEmail.textContent = "ایمیل وارد شده معتبر نیست.";
    }
    else {
        this.classList.remove('invalid');
        messageEmail.classList.remove("message-error");
        messageEmail.textContent = "";
    }
}
function validateMessage(){
    const messageText = document.getElementById("messageText");
    if (this.value.trim().length > 0 && (this.value.trim().length < 5 || this.value.length > 43500)) {
        this.classList.add('invalid');
        messageText.classList.add("message-error");
        messageText.textContent = "طول پیام وارد شده نامعتبر است.";
    } else {
        this.classList.remove('invalid');
        messageText.classList.remove("message-error");
        messageText.textContent = "";
    }
}
function validateSubject(){
    const messageSubject = document.getElementById("messageSubject");
    if (this.value.trim().length < 1 || this.value.length > 500){
        this.classList.add('invalid');
        messageSubject.classList.add("message-error");
        messageSubject.textContent = "طول موضوع وارد شده نامعتبر است.";
    } else {
        this.classList.remove('invalid');
        messageSubject.classList.remove("message-error");
        messageSubject.textContent = "";
    }
}
sendAnswer.addEventListener('click',function () {
    const sendEmailForm = document.getElementById('sendEmailForm');
    if(sendEmailForm.style.display === "block")
        sendEmailForm.style.display = "none";
        else
        sendEmailForm.style.display = "block";
    location.href = "#sendEmailForm";
    refreshHandler();
});
refreshChaptcha.addEventListener('click',refreshHandler);
function sendAjaxHandler(event){
    event.preventDefault();
    validateForm();
    if(validate){
        const loading = '<img src="http://sarikhani.id.ir/images/loading.gif" alt="loading" height="200" width="200" >';
        const divLoading = document.querySelector('.loading');
        const message = document.querySelector(".message");
        message.classList.remove("message-error");
        message.classList.remove("message-success");
        message.textContent = "";
        let xhr = new XMLHttpRequest();
        xhr.addEventListener('readystatechange',function () {
            if(xhr.readyState >0 && xhr.readyState <4) {
                divLoading.innerHTML = loading;
                divLoading.style.display = "block";
            }
        });
        xhr.addEventListener('load', function () {
            if((xhr.status >= 200 && xhr.status <= 300) || xhr.status === 304){
                message.textContent = "";
                divLoading.style.display = "none";
                const response = (xhr.responseText);
                if(response === "errorChaptcha"){
                    message.classList.add("message-error");
                    message.textContent = "جواب سوال امنیتی اشتباه است.";
                    chaptcha.value = "";
                }else if(response === "errorEmail"){
                    message.classList.add("message-error");
                    message.textContent = "ایمیل وارد شده معتبر نمی باشد.";
                }else if(response === "errorMessage"){
                    message.classList.add("message-error");
                    message.textContent = "طول پیام وارد شده معتبر نمی باشد.";
                }else if(response === "errorSubject"){
                    message.classList.add("message-error");
                    message.textContent = "طول فیلد موضوع وارد شده معتبر نمی باشد.";
                }else if(response === "error"){
                    message.classList.add("message-error");
                    message.textContent = "در ثبت پیام مشکلی پیش آمده.";

                }else{
                    loadNewAnswer(JSON.parse(response));
                    message.classList.add("message-success");
                    message.textContent = "پیام شما با موفقیت ارسال شد.";
                }
                refreshHandler();
            }
        });
        xhr.open("POST","http://sarikhani.id.ir/managePanel/includes/messageManage.php",true);
        const form = document.forms[0];
        const data = new FormData(form);
        xhr.send(data);
    }
}
function loadNewAnswer(response) {
    let html = '<header>\n';
    let creation_time = (response.creation_time) * 1000;
    let options = {
        year : 'numeric',
        month : 'numeric',
        day : 'numeric',
        hour : 'numeric',
        minute : 'numeric'
    };
    creation_time = new Date(creation_time).toLocaleDateString('fa-IR',options);
    html += '<time>تاریخ ارسال:'+creation_time+'</time>\n';
    html += '<p>آی پی ارسالی:'+ number2farsi(response.user_ip)+'</p>\n';
    html += '<p>ایمیل ارسالی:'+response.email+'</p>\n</header>\n';
    html += '<div>\n<p>'+response.comment+'</p>\n</div>\n';
    // html += '</article>';
    const article = document.createElement('article');
    const content = document.getElementById('content');
    article.classList.add('groupComment');
    article.innerHTML = html;
    content.insertBefore(article,sendEmailForm);
    refreshHandler();
}
function refreshHandler() {
    const imageChaptcha = document.getElementById("image-chaptcha");
    imageChaptcha.src = "http://sarikhani.id.ir/account/chaptcha1.php?rnd=" + Math.random();
}
function validateForm() {
    emailInputs.forEach(emailInput=>{
        if(!emailPattern.test(emailInput.value)){
            if(emailInput.name === "email") {
                validate = false;
                emailInput.classList.add('invalid');
            }else if(emailInput.value.length > 0){
                validate = false;
                emailInput.classList.add('invalid');
            }else
                emailInput.classList.remove('invalid');
        }else
            emailInput.classList.remove('invalid');
    });
    if (subject.value.trim().length < 1 || subject.value.length > 500){
        validate = false;
        subject.classList.add('invalid');
    } else {
        subject.classList.remove('invalid');
    }
    if (messagetxtarea.value.trim().length < 5) {
        validate = false;
        messagetxtarea.classList.add('invalid');
    }else if(chaptcha.value.length < 1){
        validate = false;
        chaptcha.classList.add('invalid');
    }else{
        let smtpFlag ;
        for(let input of SMTP){
            validate = false;
            smtpFlag = false;
            if(input.checked) {
                validate = true;
                smtpFlag =true;
                break;
            }
        }
        const messageSMTP = sendEmailForm.querySelector('#messageSMTP');
        if(!smtpFlag){
            messageSMTP.classList.add("message-error");
            messageSMTP.textContent = "لطفا یک روش ارسال را انتخاب نمایید.";
            event.preventDefault();
        }else{
            messageSMTP.classList.remove('message-error');
            messageSMTP.textContent = "";
        }
    }

}
function number2farsi(str) {
    const ar_num  = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
    const fa_num= ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    if(typeof str === 'string')
        for(let i=0; i<10; i++){
            str = str.replace(/[0-9]/g, function (w) {
                return fa_num[+w];
            });
        }
    return str
}
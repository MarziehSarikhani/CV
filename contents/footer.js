"use strict"
const formFooter = document.getElementById('formFooter');
const emailUser = formFooter.querySelector('input[name=emailUser]');
const messageUser = formFooter.querySelector('textarea[name=messageUser]');
const sendButton =  formFooter.querySelector('#sendButton');
const emailPattern = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,6})+$/;
const refreshChaptcha = formFooter.querySelector("#refresh-chaptcha");
const chaptcha = formFooter.querySelector('input[name=chaptcha]');
// let validate = true;

emailUser.addEventListener('keyup',validateEmail);
emailUser.addEventListener('blur',validateEmail);
messageUser.addEventListener('keypress', validateMessage);
messageUser.addEventListener('blur', validateMessage);
// formFooter.addEventListener('submit', validationForm);
sendButton.addEventListener("click",sendAjaxHandler);
refreshChaptcha.addEventListener("click",refreshHandler);
chaptcha.addEventListener('blur', validateCHAPTCHA);

function validateEmail() {
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
};

function validateMessage() {
    const messageText = document.getElementById("messageText");
    if (this.value.trim().length > 0 && (this.value.trim().length < 10 || this.value.length > 43500)) {
        this.classList.add('invalid');
        messageText.classList.add("message-error");
        messageText.textContent = "طول پیام وارد شده نامعتبر است.";
    } else {
        this.classList.remove('invalid');
        messageText.classList.remove("message-error");
        messageText.textContent = "";
    }
}

function validateCHAPTCHA() {
    if(this.value.trim().length > 0){
        this.classList.remove('invalid');
    }
}

function sendAjaxHandler(event){
    let validate = true;
    if (!emailPattern.test(emailUser.value)) {
        validate = false;
        event.preventDefault();
        emailUser.classList.add('invalid');
    }
    if (messageUser.value.trim().length < 10) {
        validate = false;
        event.preventDefault();
        messageUser.classList.add('invalid');
    }
    if(chaptcha.value.trim() == ""){
        validate = false;
        event.preventDefault();
        chaptcha.classList.add('invalid');
    }
    if(validate){
        event.preventDefault();
        const message = formFooter.querySelector("#message span");
        message.classList.remove("message-error");
        message.classList.remove("message-success");
        message.textContent = "";
        let xhr = new XMLHttpRequest();
        xhr.addEventListener('load', function () {
            if((xhr.status >= 200 && xhr.status <= 300) || xhr.status === 304){
                const response = xhr.responseText;
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
                }else if(response === "success"){
                    message.classList.add("message-success");
                    message.textContent = "پیام شما با موفقیت ارسال شد.";
                    emailUser.value = "";
                    messageUser.value = "";
                    chaptcha.value = "";
                }else if(response === 'errorNOTALLOWED'){
                    message.classList.add("message-error");
                    message.textContent = "تعداد پیام ارسالی شما بیش از حد مجاز است.";
                }else{
                    message.classList.add("message-error");
                    message.textContent = "در ثبت پیام مشکلی پیش آمده.";
                }
                refreshHandler();
            }
        });
        xhr.open("POST","http://sarikhani.id.ir/includes/saveMessage.php",true);
        let dataForm = {
            chaptcha : chaptcha.value,
            emailUser : emailUser.value,
            messageUser : messageUser.value,
        };
        const json = JSON.stringify(dataForm);
        xhr.send(json);
    }
}

function refreshHandler(){
    const imageChaptcha = document.getElementById("image-chaptcha");
    imageChaptcha.src = "http://sarikhani.id.ir/account/chaptcha1.php?rnd=" + Math.random();
}


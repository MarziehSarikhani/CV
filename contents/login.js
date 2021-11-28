
const formLogin = document.getElementById('form-login');
const refreshChaptcha = document.getElementById("refresh-chaptcha");
formLogin.addEventListener('submit', (event)=> {
 const inputTxt = document.getElementsByTagName('input');
    for (let i =0 ; i < inputTxt.length ; i++){
        if(inputTxt[i].value.trim().length < 1 ) {
            event.preventDefault();
            inputTxt[i].classList.add('invalid');
        }else
            inputTxt[i].classList.remove('invalid')
    }
});
refreshChaptcha.addEventListener("click",function () {
    const imageChaptcha = document.getElementById("image-chaptcha");
    imageChaptcha.src = "http://sarikhani.id.ir/account/chaptcha1.php?rnd=" + Math.random();
});





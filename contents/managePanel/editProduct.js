CKEDITOR.replace(document.getElementById( 'area' ) ,{
    contentsLangDirection : 'rtl',
    fontSize_defaultLabel : '16',
});
CKEDITOR.stylesSet.add ('default', [
    // Block-level styles
    { name: 'Heading 1', element: 'h1'},
    { name: 'Heading 2', element: 'h2'},
    { name: 'Heading 3', element: 'h3'},
    { name: 'Heading 4', element: 'h4'},
    { name: 'Heading 5', element: 'h5'},
    { name: 'CSS Style ul-panel' , element: 'div', attributes: { 'class': 'ul-panel'} },
    // Inline Styles
    { name: 'Marker: Yellow',   element: 'span',    styles: { 'background-color': 'Yellow' } },
]);

const editProductForm = document.forms[0];
const sendFormEdit = document.getElementById('sendFormEdit');
const sendFormCreate = document.getElementById('sendFormCreate');
const loading = document.querySelector('.loading');
const deleteImageButtons = document.querySelectorAll('.deleteImage');
document.querySelectorAll('.uploadImage').forEach(uploadImage=>{
    uploadImage.addEventListener('click',uploadImageAjaxHandler);
});
if(deleteImageButtons){
    deleteImageButtons.forEach(deleteImage=>{
        deleteImage.addEventListener('click',deleteImageAjaxHandler);
    })
}
if(sendFormEdit)
 sendFormEdit.addEventListener('click',ajaxHandler);
if(sendFormCreate)
    sendFormCreate.addEventListener('click',ajaxHandler);
function ajaxHandler(event) {
    if(event.target.id === 'sendFormEdit') {
        const hiddenInput = editProductForm.querySelector('input[type="hidden"]');
        const pattern = /^\d+$/;
        if (pattern.test(hiddenInput.value)) {
            if(validateForm())
                saveAjaxHandler(true);
        }
    }else if(event.target.id === 'sendFormCreate'){
        if(validateForm())
               saveAjaxHandler(false);
    }
}
function saveAjaxHandler(edit){
    CKUpdate();
    const loadingMessage = document.querySelector('.loadingMessage');
    removeMessage(loadingMessage);
        const xhr = new XMLHttpRequest();
        xhr.addEventListener('load', function () {
            if ((xhr.status >= 200 && xhr.status <= 300) || xhr.status === 304) {
                loading.style.display = "none";
                const response = JSON.parse(xhr.responseText);
                if (response[0]['status'] === 1) {
                    if(edit){
                        showSuccessMessage(loadingMessage, response[0]['message']);
                        const slideShowPic = document.getElementById('slideShowPicture');
                        const mainPicture = document.getElementById('mainPicture');
                        let figure =  slideShowPic.firstElementChild.lastElementChild.firstElementChild;
                        if(figure.tagName === 'FIGURE')
                            figure.firstElementChild.src = figure.firstElementChild.src.replace('temp/upload/',"");
                        figure =  mainPicture.firstElementChild.lastElementChild.firstElementChild;
                        if(figure.tagName === 'FIGURE')
                            figure.firstElementChild.src = figure.firstElementChild.src.replace('temp/upload/',"");
                        document.querySelectorAll('.titlePictures').forEach(item=>{
                            let src = item.parentElement.firstElementChild.src;
                            const pattern = /temp\/upload\/\w+?\/\d+.(?:png|jpg|jpeg)/;
                            if(pattern.test(src)){
                                src = item.parentElement.firstElementChild.src.replace('temp/upload/','');
                                src = src.substr(0,src.lastIndexOf('/')+1) + response[0]['id']  + src.substr (src.lastIndexOf('/'));
                                item.parentElement.firstElementChild.src = src;
                            }
                        });

                    }
                    else
                       location.href = `./?action=products&id=${response[0]['id']}&do=edit`;
                }
                else
                    showErrorMessage(loadingMessage,response[0]['message']);
            }
        });
        xhr.addEventListener('readystatechange', showLoading);
        xhr.open('POST', 'http://sarikhani.id.ir/managePanel/includes/productManage.php', true);
        const data = new FormData(editProductForm);
        document.querySelectorAll('.titlePictures').forEach(item=>{
            let imageSRC = item.parentElement.firstElementChild.src;
            let imageName = imageSRC.substr(imageSRC.lastIndexOf('/')+1);
            let titlePicture = `name=${imageName}&title=${item.value}&id=${item.dataset.imageid}`;
            data.append('titlePictures[]',titlePicture);
        });
        xhr.send(data);

}
function deleteImageAjaxHandler(event) {
    event.preventDefault();
    const src = event.target.previousElementSibling.src;
    const pathImagePos = src.indexOf('images');
    const imagePath = src.substr(pathImagePos);
        const xhr = new XMLHttpRequest();
        const messageProduct = event.target.parentElement.parentElement.lastElementChild;
        removeMessage(messageProduct);
        xhr.addEventListener('load', function () {
            if ((xhr.status >= 200 && xhr.status <= 300) || xhr.status === 304) {
                loading.style.display = "none";
                const response = JSON.parse(xhr.responseText);
                if (response[0]['status'] === 1) {
                    showSuccessMessage(messageProduct,response[0]['message']);
                    deleteImageFromPanel(event.target.parentElement);

                } else
                    showErrorMessage(messageProduct,response[0]['message']);
                window.setTimeout(function () {
                    removeMessage(messageProduct);
                },5000);
            }
        });
        xhr.addEventListener('readystatechange',showLoading);
        xhr.open('POST', 'http://sarikhani.id.ir/managePanel/includes/productManage.php', true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        let queryString = `imagePath=${encodeURIComponent(imagePath)}&action=deleteImage`;
        xhr.send(queryString);
}
function deleteImageAjaxHandlerTrigger(divGroupPictures) {
    const src = divGroupPictures.firstElementChild.firstElementChild.src;
    const pathImagePos = src.indexOf('images');
    const imagePath = src.substr(pathImagePos);
    const xhr2 = new XMLHttpRequest();
    const messageProduct = divGroupPictures.lastElementChild;
    removeMessage(messageProduct);
    xhr2.addEventListener('load', function () {
        if ((xhr2.status >= 200 && xhr2.status < 300) || xhr2.status === 304) {
            loading.style.display = "none";
            const response = JSON.parse(xhr2.responseText);
            if (response[0]['status'] == 1) {
                divGroupPictures.removeChild(divGroupPictures.firstElementChild);
            } else
                showErrorMessage(messageProduct,response[0]['message']);
        }
    });
    xhr2.addEventListener('readystatechange',showLoading);
    xhr2.open('POST', 'http://sarikhani.id.ir/managePanel/includes/productManage.php', true);
    xhr2.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    let queryString = `imagePath=${encodeURIComponent(imagePath)}&action=deleteImage`;
    xhr2.send(queryString);
}
function uploadImageAjaxHandler(event) {
    event.preventDefault();
    const message = event.target.nextElementSibling.lastElementChild;
    removeMessage(message);
    if(validateUploadImage(event,message)) {
        const xhr = new XMLHttpRequest();
        xhr.addEventListener('load', function () {
            if ((xhr.status >= 200 && xhr.status <= 300) || xhr.status === 304) {
                loading.style.display = "none";
                const response = JSON.parse(xhr.responseText);
                const sizeResponse =  response.length;
                let flag = false;
                for(let i = 0 ; i < sizeResponse ; i++){
                    if(response[i]['status'] === 1){
                        flag = true;
                        let productPath = response[i]['message'].substr(6);
                        let html = `<img src="http://sarikhani.id.ir/${productPath}" class="groupPic" alt="تصویر نمونه کار"/>`;
                        const figureElement = document.createElement('FIGURE');
                        figureElement.classList.add('groupPicAndBtn');
                        figureElement.innerHTML = html;
                        const inputElement = document.createElement('INPUT');
                        inputElement.setAttribute('type','image');
                        inputElement.setAttribute('src',"http://sarikhani.id.ir/images/delete.png");
                        inputElement.setAttribute('alt','حذف');
                        inputElement.setAttribute('title','حذف');
                        inputElement.classList.add('groupBtn');
                        inputElement.classList.add('deleteImage');
                        inputElement.addEventListener('click',deleteImageAjaxHandler);
                        figureElement.appendChild(inputElement);
                        const span = event.target.nextElementSibling.lastElementChild;
                        event.target.nextElementSibling.insertBefore(figureElement,span);
                        if(event.target.form.id === "otherPicForm") {
                            const input = document.createElement('INPUT');
                            input.setAttribute('type', 'text');
                            input.setAttribute('placeholder', 'عنوان تصویر را وارد کنید');
                            input.setAttribute('data-imageid',"");
                            input.classList.add('group-input');
                            input.classList.add('titlePictures');
                            figureElement.appendChild(input);
                        }
                    }
                }
                if(!flag) {
                    for(let i = 0 ; i < sizeResponse ; i++)
                        if(response[i]['status'] === 0) {
                            showErrorMessage(message,response[i]['message']);
                            break;
                        }
                }
            }
        });
        xhr.addEventListener('readystatechange',showLoading);
        xhr.open('POST', "http://sarikhani.id.ir/managePanel/includes/productManage.php", true);
        const data = new FormData(event.target.form);
        xhr.send(data);
    }
}
function deleteImageFromPanel(element) {
    const parentElement = element.parentElement;
    parentElement.removeChild(element);
}

function validateUploadImage(event,message){
    const uploadInput = event.target.previousElementSibling;
    if('files' in uploadInput ) {
        if (uploadInput.files.length === 0) {
            showErrorMessage(message,"لطفا تصویر مورد نظر را انتخاب نمایید!");
            return false;
        } else if (uploadInput.files.length > 10) {
            showErrorMessage(message, "حداکثر مجاز به انتخاب 10 تصویر هستید!");
            return false;
        } else {
            for (let i = 0; i < uploadInput.files.length; i++) {
                if ('size' in uploadInput.files[i])
                    if (uploadInput.files[i].size > 204801) {
                        showErrorMessage(message,  "حجم فایل آپلود شده بیشتر از 200 کیلو بایت است.");
                        return false;
                    }
                const MIMEType = ['image/jpeg','image/jpg','image/png'];
                if('type' in uploadInput.files[i]){
                    if(!MIMEType.includes(uploadInput.files[i].type)) {
                        showErrorMessage(message,  "فقط مجاز به آپلود فایل از نوع jpg , png می باشید.");
                        return false;
                    }
                }

            }

        }
        const targetUpload = event.target.form.id;
        if (targetUpload === "slideShowPicForm" || targetUpload === "mainPicForm") {
            const divGroupPictures = event.target.nextElementSibling;
            if (divGroupPictures.childElementCount > 1) {
                let answer = confirm("آیا تصویر انتخابی جایگزین تصویر قبلی شود؟");
                if(!answer)
                   return false;
                else
                   deleteImageAjaxHandlerTrigger(divGroupPictures);
            }
        }
    }else return false;
    return true;
}
function validateForm(){
    const title = editProductForm.querySelector('input[name="title"]');
    const link = editProductForm.querySelector('input[name="link"]');
    const cats  = editProductForm.querySelectorAll('input[name="categories"]');
    if(title.value.trim().length < 3){
        showErrorMessage(title.previousElementSibling,"عنوان نمونه کار معتبر نیست!");
        return false;
    }  else
        removeMessage(title.previousElementSibling);
    if(link.value.trim().length < 3){
        showErrorMessage(link.previousElementSibling,"لینک نمونه کار معتبر نیست!");
        return false;
    }  else
        removeMessage(link.previousElementSibling);
    let flag = false;
    const span = document.getElementById('cats').previousElementSibling;
    for(let cat of cats)
        if(cat.checked){
            flag = true;
            break;
        }
    if(!flag){
        showErrorMessage(span,"گروه نمونه کار را تعیین کنید!");
        return false;
    }  else
        removeMessage(span);
    return true;
}
function showLoading(event) {
    const loadingImg = '<img src="http://sarikhani.id.ir/images/loading.gif" alt="loading"  >';
    loading.style.display = "block";
    if(event.target.readyState >0 && event.target.readyState <4) {
        loading.innerHTML = loadingImg;
    }
}
function CKUpdate(){
    for (let instance in CKEDITOR.instances )
        CKEDITOR.instances[instance].updateElement();
}
function removeMessage(messageElem){
    messageElem.innerHTML = "";
    messageElem.classList.remove("message-error");
    messageElem.classList.remove("message-success");
}
function showErrorMessage(messageElem,message){
    messageElem.innerHTML = message;
    messageElem.classList.add('message-error');
}
function showSuccessMessage(messageElem,message){
    messageElem.innerHTML = message;
    messageElem.classList.add('message-success');
}
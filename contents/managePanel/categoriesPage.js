const createCatForm = document.forms[0];
const deleteCatForm = document.forms[1];
const catName = createCatForm.querySelector("input[name=catName]");
const createbtn = createCatForm.querySelector("input[type=submit]");
const parentID = createCatForm.querySelector("select[name=parentID]");
const deletebtn = deleteCatForm.querySelector("input[type=submit]");
const catID = deleteCatForm.querySelector("select[name=catID]");
const catsUL = document.getElementById('cats');
const message = document.getElementById("message");
const loading = document.querySelector('.loading');
let oldCatName = "";
catsUL.addEventListener('click',editCatHandler);
deletebtn.addEventListener('click',deleteAjaxHandler);
catName.addEventListener('keyup',validateLength);
createbtn.addEventListener('click',createAjaxHandler);
function validateLength() {
    if(this.value.trim().length < 3 || this.value.length > 101) {
        this.classList.add('invalid');
    }else{
        this.classList.remove('invalid');
        }
}
function createAjaxHandler(event) {
    message.classList.remove("message-error");
    message.classList.remove("message-success");
    message.textContent = "";
        let validate = true;
        if(catName.value.trim().length < 3){
            validate = false;
            event.preventDefault();
            catName.classList.add("invalid");
        }
        if(validate){
            event.preventDefault();
            let xhr = new XMLHttpRequest();
            xhr.addEventListener('load',function(){
                if((xhr.status >= 200 && xhr.status <= 300) || xhr.status === 304){
                    const response = JSON.parse(xhr.responseText);
                    if(response['status'] == 1){
                        getCategories();
                        message.classList.add("message-success");
                        message.textContent = response['message'];
                        catName.value = "";
                        parentID.selectedIndex = 0;
                    }else{
                        loading.style.display = "none";
                        message.classList.add("message-error");
                        message.textContent = response['message'];
                    }
                }
            });
            xhr.addEventListener('readystatechange', showLoading);
           xhr.open("POST","http://sarikhani.id.ir/managePanel/includes/categoryManage.php",true);
           let data = new FormData(createCatForm);
           xhr.send(data);
        }
}
function deleteAjaxHandler(event) {
    event.preventDefault();
    const message = document.getElementById("message");
    message.classList.remove("message-error");
    message.classList.remove("message-success");
    message.textContent = "";
    if(catID.value !== "" && catID.selectedIndex !== 0 ){
        let xhr = new XMLHttpRequest();
        xhr.addEventListener('load',function(){
            if((xhr.status >= 200 && xhr.status <= 300) || xhr.status === 304){
                const response = JSON.parse(xhr.responseText);
               if(response['status'] == 1){
                   getCategories();
                    message.classList.add("message-success");
                    message.textContent = response['message'];
                    catID.selectedIndex = 0;
                }else{
                   loading.style.display = "none";
                   message.classList.add("message-error");
                   message.textContent = response['message'];
            }
        }});
        xhr.addEventListener('readystatechange', showLoading);
        xhr.open("POST","http://sarikhani.id.ir/managePanel/includes/categoryManage.php",true);
        let data = new FormData(deleteCatForm);

        xhr.send(data);
    }

}
function getCategories() {
    let xhr = new XMLHttpRequest();
    xhr.addEventListener('load',function () {
        if((xhr.status >= 200 && xhr.status <= 300) || xhr.status === 304) {
            loading.style.display = "none";
            const response = JSON.parse(xhr.responseText);
            let htmlUL = "";
            let htmlSelectCreate = `<option value="0">بدون والد</option>`;
            let htmlSelectDelete = `<option value="-1" selected="selected">--انتخاب کنید--</option>`;
            for(let i = 0 ; i < response.length ; i++){
                htmlSelectCreate += `<option value="${response[i].cats.id}">${response[i].cats.name}</option>`;
                htmlSelectDelete += `<option value="${response[i].cats.id}"  class="headerOption">${response[i].cats.name}</option>`;
                htmlUL += `<li><span class='upLevel'>&#x25B2;</span>
                              <span class='downLevel'>&#x25BC;</span>
                              <label class='groupMessage' title='ویرایش' data-catid='${response[i].cats.id}'>${response[i].cats.name}</label><img src='http://sarikhani.id.ir/images/edit.png' alt='ویرایش' title='ویرایش' class='groupMessage' />`;
                if(response[i].childs.length > 0){
                    htmlUL += "<ul>";
                    for(let j = 0; j < response[i].childs.length ; j++) {
                        htmlUL += `<li><span class='upLevel'>&#x25B2;</span>
                                        <span class='downLevel'>&#x25BC;</span>
                                        <label class='groupMessage' title='ویرایش' data-catid='${response[i].childs[j].id}'>${response[i].childs[j].name}</label><img src='http://sarikhani.id.ir/images/edit.png' alt='ویرایش' title='ویرایش' class='groupMessage'/></li>`;
                        htmlSelectDelete += `<option value=" ${response[i].childs[j].id }" class="childOption">------------${response[i].childs[j].name }</option>`;
                    }
                    htmlUL += "</ul>";
                }
                htmlUL += "</ul>";
            }
            catsUL.innerHTML = htmlUL;
            parentID.innerHTML = htmlSelectCreate;
            catID.innerHTML  = htmlSelectDelete;
        }
    });
    xhr.addEventListener('readystatechange', showLoading);
    xhr.open('POST',"http://sarikhani.id.ir/managePanel/includes/categoryManage.php",true);
    xhr.send("getCategories");
}

function editCatHandler(event) {
    if(event.target.tagName === "IMG" || event.target.tagName === "LABEL" || event.target.tagName === "SPAN") {
            removeEditElement();
            let label;
            let img;
            switch (event.target.tagName) {
                case "LABEL":
                    label = event.target;
                    img = event.target.nextElementSibling;
                    break;
                case "IMG":
                    label = event.target.previousElementSibling;
                    img = event.target;
                    break;
                case "SPAN":
                    const lastLi = catsUL.querySelectorAll('#cats li:last-child > .downLevel');
                    const firstLi = catsUL.querySelectorAll('#cats li:first-child > .upLevel');
                    let levelName = event.target.className;
                    if(levelName === "upLevel") {
                        let callFunctionFlag = true;
                        firstLi.forEach(item => {
                            if (item.isSameNode(event.target)) {
                                callFunctionFlag = false;
                            }
                        });
                        if(callFunctionFlag){
                            const catId = event.target.nextElementSibling.nextElementSibling.dataset.catid;
                            changeLevelCat(catId,"increase");
                        }

                    }
                    else if(levelName === "downLevel"){
                        let callFunctionFlag = true;
                        lastLi.forEach(item=>{
                            if(item.isSameNode(event.target)){
                                callFunctionFlag = false;
                            }
                        });
                        if(callFunctionFlag){
                            const catId = event.target.nextElementSibling.dataset.catid;
                            changeLevelCat(catId,'decrease');
                        }
                    }
                    return;
            }
            createEditElements(label, img);
    }
}
function createEditElements(label,img) {
    oldCatName = label.textContent;
    const li = label.parentElement;
    const input = document.createElement('INPUT');
    input.setAttribute("type", "text");
    input.setAttribute("name", "catName");
    input.setAttribute("value", label.textContent);
    input.setAttribute("data-catid", label.dataset.catid);
    input.classList.add('group-input');
    li.replaceChild(input,label);
    const buttonIMG = document.createElement('INPUT');
    buttonIMG.setAttribute("type", "button");
    buttonIMG.setAttribute('title','بروزرسانی');
    buttonIMG.setAttribute("value", 'بروزرسانی');
    buttonIMG.classList.add('button');
    li.replaceChild(buttonIMG,img);
    const cancel = document.createElement('BUTTON');
    cancel.setAttribute('title','انصراف');
    cancel.textContent = "انصراف";
    cancel.classList.add('button');
    li.insertBefore(cancel,buttonIMG);
    buttonIMG.addEventListener('click',sendAjaxHandler);
    cancel.addEventListener('click', removeEditElement);
    li.querySelectorAll('span').forEach((span,index)=>{
        if(index === 0 || index === 1)
           li.removeChild(span);
    });

}
function removeEditElement() {
    const textInput = catsUL.querySelector('input[type="text"]');
    const buttonInput = catsUL.querySelector('input[type="button"]');
    const cancel = catsUL.querySelector('button');
    if(typeof(textInput) != 'undefined' && textInput != null && typeof(buttonInput) != 'undefined' && buttonInput != null
        && typeof(cancel) != 'undefined' && cancel != null) {
        const li = textInput.parentElement;
        const label = document.createElement('LABEL');
        const img = document.createElement('IMG');
        const spanUpLevel = document.createElement('SPAN');
        const spanDownLevel = document.createElement('SPAN');
        label.textContent = textInput.value;
        label.dataset.catid = textInput.dataset.catid;
        img.setAttribute("src", "http://sarikhani.id.ir/images/edit.png");
        img.setAttribute("alt", "ویرایش");
        img.setAttribute("title", "ویرایش");
        li.replaceChild(label, textInput);
        li.replaceChild(img, buttonInput);
        img.classList.add('groupMessage');
        label.classList.add('groupMessage');
        li.removeChild(cancel);
        spanDownLevel.textContent = `\u25bc`;
        spanUpLevel.textContent = `\u25b2`;
        li.insertBefore(spanUpLevel,label);
        li.insertBefore(spanDownLevel,label);
        spanUpLevel.classList.add('upLevel');
        spanDownLevel.classList.add('downLevel')
    }
}
function sendAjaxHandler() {
    const inputText = catsUL.querySelector('input[type="text"]');
    if(oldCatName.trim().localeCompare(inputText.value.trim()) !== 0) {
        const xhr = new XMLHttpRequest();
        xhr.addEventListener('load', function () {
            if ((xhr.status >= 200 && xhr.status <= 300) || xhr.status === 304) {
               const response = JSON.parse(xhr.responseText);
                if(response['status'] == 1){
                    getCategories();
                    message.classList.add("message-success");
                    message.textContent = response['message'];

                }else{
                    loading.style.display = "none";
                    message.classList.add("message-error");
                    message.textContent = response['message'];
                }

            }
        });
        xhr.addEventListener('readystatechange', showLoading);
        xhr.open('POST', "http://sarikhani.id.ir/managePanel/includes/categoryManage.php", true);
        const data = new FormData();
        data.append('id', inputText.dataset.catid);
        data.append('categoryName', inputText.value);
        data.append('do', 'edit');
        xhr.send(data);
    }else
        removeEditElement();

}
function showLoading(event) {
    const loadingImg = '<img src="http://sarikhani.id.ir/images/loading.gif" alt="loading"  >';
    loading.style.display = "block";
    if(event.target.readyState >0 && event.target.readyState <4) {
        loading.innerHTML = loadingImg;
    }
}

function changeLevelCat(catId,action) {
    message.classList.remove("message-success");
    message.classList.remove("message-error");
    message.textContent = "";
    let pattern = /^\d+$/;
    if(pattern.test(catId)){
        const xhr = new XMLHttpRequest();
        xhr.addEventListener('load', function () {
            if ((xhr.status >= 200 && xhr.status <= 300) || xhr.status === 304) {
                const response = JSON.parse(xhr.responseText);
                if(response['status'] == 1){
                    getCategories();
                    message.classList.add("message-success");
                    message.textContent = response['message'];
                }else{
                    loading.style.display = "none";
                    message.classList.add("message-error");
                    message.textContent = response['message'];
                }
            }
        });
        xhr.addEventListener('readystatechange', showLoading);
        xhr.open('POST', "http://sarikhani.id.ir/managePanel/includes/categoryManage.php", true);
        const data = new FormData();
        data.append('id', catId);
        data.append('actionForChangeLevel', action);
        xhr.send(data);
    }
}

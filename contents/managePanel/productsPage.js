const productShowTable = document.getElementById('productShow');
const loadingMessage = document.querySelector('.loadingMessage');
const loading = '<img src="http://sarikhani.id.ir/images/loading.gif" alt="loading" >';
const header = document.querySelector('#productsList > header');
const showProductByCategory = document.getElementById('showProductByCategory');

productShowTable.addEventListener('click',ajaxHandler);
header.addEventListener('click',ajaxHandlerHeader);
showProductByCategory.addEventListener('change',ajaxHandlerGetProductByCat);

function ajaxHandler(event) {
    removeLoading();
    if(event.target.tagName === "A"){
        const dataName = event.target.dataset.name;
        const array = ["published","unpublished","delete"];
        if(array.includes(dataName))
        {
            event.preventDefault();
            const productId = event.target.dataset.id;
            const startPaging = event.target.dataset.start;
            (dataName === "delete") ? deleteProductAjax(productId,startPaging) : changePublishAjax(productId,dataName,startPaging);
        }
    }

}
function ajaxHandlerHeader(event) {
    removeLoading();
    event.preventDefault();
    if(event.target.nodeName === 'A'){
        const actionPos = event.target.href.lastIndexOf('get=');
        const action = event.target.href.substr(actionPos+4);
        const xhr = new XMLHttpRequest();
        xhr.addEventListener('load',function () {
            if((xhr.status >= 200 && xhr.status <= 300) || xhr.status === 304)
                showProducts(JSON.parse(xhr.responseText),0);
        });
        xhr.addEventListener('readystatechange',showLoading);
        xhr.open("GET","http://sarikhani.id.ir/managePanel/includes/productManage.php?get="+encodeURIComponent(action),true);
        xhr.send();
    }
}
function ajaxHandlerGetProductByCat(){
    if(showProductByCategory.value != 0){
        pattern = /^\d+$/;
        if(pattern.test(showProductByCategory.value)) {
            const xhr = new XMLHttpRequest();
            xhr.addEventListener('load', function () {
                showProducts(JSON.parse(xhr.responseText), 0);
            });
            xhr.addEventListener('readystatechange', showLoading);
            xhr.open('GET', `http://sarikhani.id.ir/managePanel/includes/productManage.php?categoryid=${showProductByCategory.value}`, true);
            xhr.send();
        }
    }

}
function changePublishAjax(productId,dataName,startPaging) {
    const xhr = new XMLHttpRequest();
    xhr.addEventListener('load',function () {
        if((xhr.status >= 200 && xhr.status <= 300) || xhr.status === 304){
            getCountProducts();
            showProducts(JSON.parse(xhr.responseText),startPaging);
        }
    });
    xhr.addEventListener('readystatechange',showLoading);
    xhr.open("POST","http://sarikhani.id.ir/managePanel/includes/productManage.php",true);
    const data = new FormData();
    data.append('id',productId);
    data.append('do',dataName);
    data.append('start',startPaging);
    xhr.send(data);
}
function deleteProductAjax(productId,startPaging) {
    let answer = confirm("برای حذف نمونه کار مطمئن هستید؟");
    if(answer) {
        const xhr = new XMLHttpRequest();
        xhr.addEventListener('load', function () {
            if ((xhr.status >= 200 && xhr.status <= 300) || xhr.status === 304) {
                const response = xhr.responseText;
                if (response === "error") {
                    showErrorMessage('بروز خطا!');
                } else {
                    getCountProducts();
                    showProducts(JSON.parse(response), startPaging);
                }
            }
        });
        xhr.addEventListener('readystatechange', showLoading);
        xhr.open("POST", "http://sarikhani.id.ir/managePanel/includes/productManage.php", true);
        const data = new FormData();
        data.append('id', productId);
        data.append('start', startPaging);
        data.append('do', 'delete');
        xhr.send(data);
    }
}
function showProducts(response,startPaging) {
    let html = `<tr>
                   <th>ردیف</th>
                   <th>عنوان</th> 
                   <th>گروه</th>
                   <th>زمان انتشار</th>
                   <th>زمان آخرین تغییر</th>
                   <th>عملیات</th>
                </tr>`;
    pattern = /^http(?:s)?:\/\/.+/;
    let options = {
        year : 'numeric',
        month : 'numeric',
        day : 'numeric',
        hour : 'numeric',
        minute : 'numeric'
    };
    for(let i = 0 ; i< response.length ; i++){
        let creation_time = (response[i].creation_time) * 1000;
        creation_time = new Date(creation_time).toLocaleDateString('fa-IR',options);
        let modify_time = (response[i].modify_time) * 1000;
        modify_time = new Date(modify_time).toLocaleDateString('fa-IR',options);
        let publishText = "انتشار";
        let classstr = 'class="unpublished"';
        let name = "published";
        if(parseInt(response[i].published) === 1){
             publishText = "مخفی";
             classstr = "";
             name = "unpublished";
        }
        let editLink = "./?action=products&amp;do=edit&amp;id="+response[i].id;
        let str = '';
        if(pattern.test(response[i].link))
             str = `target="_blank" href="${response[i].link}"`;
        else str = `href="http://sarikhani.id.ir/نمونه-کار/${response[i].link}"`;
        html += `<tr>
                    <td>${i+1}</td>
                    <td><a ${str} class="link">${response[i].title}</a></td>
                    <td>${response[i].category_name}</td>
                    <td>${creation_time}</td> 
                    <td>${modify_time}</td>
                    <td>
                       <a data-name="${name}" data-id="${response[i].id}" data-start="${startPaging}" href="#" ${classstr}>${publishText}</a>
                       <a href="${editLink}">ویرایش</a>
                       <a data-name="delete" data-id="${response[i].id}" data-start="${startPaging}" href="#">حذف </a>
                    </td> 
                  </tr>`;

    }
    productShowTable.innerHTML = html;
    document.querySelectorAll('.unpublished').forEach(item=>{item.classList.add('unpublished')});
    removeLoading();
}
function getCountProducts() {
    const xhr = new XMLHttpRequest();
    xhr.addEventListener('load',function () {
        if((xhr.status >= 200 && xhr.status <= 300) || xhr.status === 304){
            const response = JSON.parse(xhr.responseText);
            const header = document.querySelector('#productsList header');
            header.classList.add('group-header');
            let html = '<a href="./?action=products&get=all"> کل نمونه کارها: ('+response.all+')</a>';
            html += '<a href="./?action=products&get=published"> نمونه کارهای منتشر شده: ('+response.published+')</a>';
            html += ' <a href="./?action=products&get=unpublished"> نمونه کارهای منتشر نشده: ('+response.unpublished+')</a>';
            header.innerHTML = html;
        }
    });
    xhr.open("GET","http://sarikhani.id.ir/managePanel/includes/productManage.php?flag=getCountProducts",true);
    xhr.send();
}

function showLoading(event) {
    if(event.target.readyState >0 && event.target.readyState <4) {
        loadingMessage.innerHTML = loading;
    }
}
function removeLoading(){
    loadingMessage.innerHTML = "";
    loadingMessage.classList.remove("message-error");
    loadingMessage.classList.remove("message-success");
}
function showErrorMessage(message){
    loadingMessage.innerHTML = message;
    loadingMessage.classList.add('message-error');
}




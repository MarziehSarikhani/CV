const massagesShow = document.getElementById('massagesShow');
const commentsHead = document.getElementById('commentsHead');
const loadingMessage = document.querySelector('.loadingMessage');
const loading = '<img src="http://sarikhani.id.ir/images/loading.gif" alt="loading" height="100" width="100" >';

massagesShow.addEventListener('click',deleteAjaxHandler);
commentsHead.addEventListener('click',sortCommentsAjaxHandler);

function deleteAjaxHandler(event) {
    loadingMessage.innerHTML = "";
    loadingMessage.classList.remove("message-error");
    loadingMessage.classList.remove("message-success");
    if(event.target.nodeName === 'IMG'){
        const name = event.target.dataset.name;
        if(name === 'delete'){
            const id = event.target.dataset.id;
            let xhr = new XMLHttpRequest();
            xhr.addEventListener('load',function () {
                if((xhr.status >= 200 && xhr.status <= 300) || xhr.status === 304){
                    const response = xhr.responseText;
                    if(response === "success"){
                        getCountMessages();
                        getMessages(event.target.dataset.start);

                    }else {
                        loadingMessage.classList.add("message-error");
                        loadingMessage.textContent = "بروز خطا.";
                    }
                }
            });
            xhr.addEventListener('readystatechange',showLoading);
            let rnd = Math.floor(Math.random()*100000);
            let url = "http://sarikhani.id.ir/managePanel/includes/messageManage.php?id="+ encodeURIComponent(id)+"&do=delete&rnd=" + rnd ;
            xhr.open('GET',url,true);
            xhr.timeout = 1000;
            xhr.addEventListener('timeout',function () {
                loadingMessage.classList.add("message-error");
                loadingMessage.textContent = "بروز خطا.";
            })
            xhr.send();
        }
   }
}
function getMessages(start){
    const xhr = new XMLHttpRequest();
    xhr.addEventListener('load',function () {
       if((xhr.status >= 200 && xhr.status <= 300) || xhr.status === 304){
           const response = JSON.parse(xhr.responseText);
           responseLoadAjaxForGetComments(response,start);
       }
    });
    xhr.open('POST',"http://sarikhani.id.ir/managePanel/includes/messageManage.php",true);
    let data = new FormData();
    data.append("flag","getMessage");
    data.append("start",start);
    xhr.send(data);
}
function getCountMessages() {
    const xhr = new XMLHttpRequest();
    xhr.addEventListener('load',function () {
        if((xhr.status >= 200 && xhr.status <= 300) || xhr.status === 304){
            const response = JSON.parse(xhr.responseText);
            const header = document.querySelector('#messages header');
            let html = '<a href="./?action=messages&comment=all"> کل پیام ها: ' +response.all+ '</a>';
            html += '<a href="./?action=messages&comment=new"> پیام های خوانده نشده: '+response.new+'</a>';
            html += ' <a href="./?action=messages&comment=answer"> پیام های پاسخ داده: '+response.answered+'</a>';
            html += ' <a href="./?action=messages&comment=noAnswer"> پیام های بدون پاسخ: '+response.noAnswered+'</a>';
            header.innerHTML = html;
        }
    });
    xhr.open("POST","http://sarikhani.id.ir/managePanel/includes/messageManage.php",true);
    let data = new FormData();
    data.append("flag",'getCountMessage');
    xhr.send(data);

}
function sortCommentsAjaxHandler(event) {
    loadingMessage.innerHTML = "";
    loadingMessage.classList.remove("message-error");
    loadingMessage.classList.remove("message-success");
    event.preventDefault();
    if(event.target.nodeName === 'A'){
        const actionPos = event.target.href.lastIndexOf('comment=');
        const action = event.target.href.substr(actionPos+8);
        const xhr = new XMLHttpRequest();
        xhr.addEventListener('load',function () {
            if((xhr.status >= 200 && xhr.status <= 300) || xhr.status === 304){
                const response = JSON.parse(xhr.responseText);
                responseLoadAjaxForGetComments(response,0);
            }
        });
        xhr.addEventListener('readystatechange',showLoading);
        xhr.open("POST","http://sarikhani.id.ir/managePanel/includes/messageManage.php",true);
        const data = new FormData();
        data.append("comment",action);
        xhr.send(data);
    }

}
function responseLoadAjaxForGetComments(response,start){
    let html = " <tr>\n" +
        "           <th>ردیف</th>\n" +
        "           <th>تاریخ ایجاد</th>\n" +
        "           <th>تاریخ پاسخ</th>\n" +
        "           <th>چکیده پیام</th>\n" +
        "           <th>فرستنده</th>\n" +
        "           <th>عملیات</th>\n" +
        "       </tr>";
    let classtr = "";
    let optionsDate = {
        year : 'numeric',
        month : 'numeric',
        day : 'numeric',
        hour : 'numeric',
        minute : 'numeric'
    };
    for(let i = 0 ; i < response.length ; i++){
        let creation_time = (response[i].creation_time) * 1000;
        creation_time = new Date(creation_time).toLocaleDateString('fa-IR',optionsDate);
        let answer_time = "-----";
        if(response[i].answer_time > 0) {
            answer_time = (response[i].answer_time) * 1000;
            answer_time = new Date(answer_time).toLocaleDateString('fa-IR',optionsDate);
        }
        let counter = i +1;
        let title = "";
        if(response[i].readed == 0) {
            title = ' title="پیام جدید" ';
            classtr = ' class="noReadedComment" '
        }
        else {
            title = '';
            classtr = "";
        }
        html += "<tr><td>"+counter+"</td>\n";
        html += "<td>"+creation_time+"</td>\n";
        html += "<td>"+answer_time+"</td>\n";
        html += "<td "+ classtr  +" "+ title +">"+response[i].comment.substr(0,20)+"</td>\n";
        html += "<td>"+response[i].email+"</td>\n";
        html += '<td>\n' +
            '                      <a href="./?action=messages&do=view&commentID='+response[i].id+'"><img src="http://sarikhani.id.ir/images/eye.png" alt="مشاهده" title="مشاهده" class="groupMessage"/></a>\n' +
            '                      <img data-name="delete" data-start="'+start+'" data-id="'+response[i].id+'" src="http://sarikhani.id.ir/images/delete.png" alt="حذف" title="حذف" class="groupMessage"/>\n' +
            '                       <a href="./?action=messages&do=view&commentID='+response[i].id+'"><img  src="http://sarikhani.id.ir/images/Email.png" alt="پاسخ" title="پاسخ" class="groupMessage"/></a>\n' +
            '                   </td></tr>';
    }
    massagesShow.innerHTML = html;
    document.querySelectorAll('.noReadedComment').forEach(item=>{item.classList.add('noReadedComment')});
    loadingMessage.innerHTML = "";

}
function showLoading(event) {
    if(event.target.readyState >0 && event.target.readyState <4) {
        loadingMessage.innerHTML = loading;
    }
}

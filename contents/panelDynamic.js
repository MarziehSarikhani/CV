const body = document.getElementsByTagName('body')[0];
const imagePanel = document.getElementById('imagePanel');
const close = document.getElementById('close');
const control = document.getElementById('control');
const picturs = document.getElementById('picturs');
let showTarget;
let firstImage;
let lastImage;
if(picturs){
    picturs.addEventListener('click',showHandler)
    if(picturs.firstElementChild)
        firstImage = picturs.firstElementChild.firstElementChild;
    if(picturs.lastElementChild)
        lastImage = picturs.lastElementChild.firstElementChild;
}

let item;
let i = 0;
close.addEventListener('click',closeHandler);
function closeHandler(){
    imagePanel.classList.add('fadeOut');
    imagePanel.classList.add('hide');
    body.classList.remove('overflowHidden');
    imagePanel.removeChild(item);
};

control.addEventListener('click',controlHandler)
function showHandler(event){
    event.preventDefault();
    if(event.target.nodeName === 'IMG'){     
        showTarget = event.target;
        let src = showTarget.src;
        showPanel(src);
    }
}
function showPanel(src) {
    item = document.createElement('article');
    src = src.replace('thumbnails','gallery');
    item.innerHTML = '<article class="item" style="display:block;"><div class="onlyPic"><img src="' + src + '" /></div></article>';
    imagePanel.insertBefore(item,close);
    imagePanel.classList.remove('fadeOut')
    body.classList.add('overflowHidden');
    imagePanel.classList.remove('hide');
}
function controlHandler(event){
    if(event.target.id === 'nextProduct')
        showNext();
    else if (event.target.id === 'prevProduct')
        showPrev();    
}
function showNext() {
            let src;
            if (!showTarget.isSameNode(lastImage)) {
                showTarget = showTarget.parentNode.nextElementSibling.children[0];
            } else {
                showTarget = firstImage;
            }            
            src = showTarget.src;
            showInPanel(src);
        }
function showPrev() {
                    let src;
                    if (!showTarget.isSameNode(firstImage)) {
                        showTarget = showTarget.parentNode.previousElementSibling.children[0];                       
                    } else {
                        showTarget = lastImage;                       
                    }
                    src = showTarget.src;
                    showInPanel(src);
                }
imagePanel.addEventListener('keydown',keydownHandler)
function keydownHandler(event){
    if(event.key === 'ArrowRight' || event.key === 'ArrowUp')
        showNext();
    else if(event.key === 'ArrowLeft' || event.key === 'ArrowDown' )
        showPrev()
    else if(event.key === 'Escape')
        closeHandler();

}



function showInPanel(src) {
    src = src.replace('thumbnails','gallery');
    imagePanel.removeChild(item);
    item = document.createElement('article');
    item.innerHTML = '<article class="item" style="display:block;"><div class="onlyPic"><img src="' + src + '" /></div></article>';
    imagePanel.insertBefore(item,close);                   
                }


const body = document.getElementsByTagName('body')[0];
const mainSection = document.getElementById('mainSection');
const descriptionPanel = document.getElementById('descriptionPanel');
const showDescription = document.querySelectorAll('.showDescription');
if(descriptionPanel){
    const close = descriptionPanel.querySelector('#close');
    close.addEventListener('click',closeHandler);
}


mainSection.addEventListener('click',clickHandler);
mainSection.addEventListener('keydown',keydownHandler);

let itemOpen;
function clickHandler(event){
    if(event.target.nodeName === 'A'){
    for(let i = 0; i<showDescription.length ; i++){
        if(event.target === showDescription[i]){
            event.preventDefault();
            let anchor = event.target.dataset.anchor;
            itemOpen = descriptionPanel.querySelector('#'+anchor );        
            descriptionPanel.classList.remove('fadeOut');
            descriptionPanel.classList.remove('hide');
            itemOpen.classList.remove('hide');
            body.classList.add('overflowHidden');
            descriptionPanel.classList.remove('fadeOut');
            mainSection.removeEventListener('click',clickHandler);
            return;
            }
        }        
    }
}

function closeHandler(event){
    descriptionPanel.classList.add('fadeOut');
    descriptionPanel.classList.add('hide');
    itemOpen.classList.add('hide');
    body.classList.remove('overflowHidden');
    event.stopPropagation();
    mainSection.addEventListener('click',clickHandler);
}

function keydownHandler(event){
    if(event.key === 'Escape')
          closeHandler(event);                   
                }      
      
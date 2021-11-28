"use strict"
const ulSlider = document.querySelector('#sliderProduct ul');
const nextprev = document.getElementById('nextprev');
let ulSliderFirstChild;
let ulSliderLastChild;
let activeSlide;
let ulSliderFirstChildClone;
let ulSliderLastChildClone;
let slideShowInterval;
let temp;
if(ulSlider){
    nextprev.addEventListener('click',nextprevHandler);
     ulSliderFirstChild = ulSlider.firstElementChild;
     ulSliderLastChild = ulSlider.lastElementChild;
     activeSlide = ulSliderFirstChild;
     ulSliderFirstChildClone = ulSliderFirstChild.cloneNode(true);
     ulSliderLastChildClone = ulSliderLastChild.cloneNode(true);
    ulSlider.appendChild(ulSliderFirstChildClone);
    ulSlider.insertBefore(ulSliderLastChildClone,ulSliderFirstChild);
    const widthUL = (ulSlider.childElementCount) * 400 ;
    ulSlider.style.width = widthUL + 'px';
    temp = (ulSlider.childElementCount - 2)*400;
    slideShowInterval = setInterval(function () {
        nextSlideClickHandler();
    },3000);
}
let canClick = true;
let left = -400;



function nextprevHandler(event){
    if(event.target.id === 'nextProduct')
       nextSlideClickHandler();
    else if(event.target.id === 'prevProduct')
       prevSlideClickHandler();
}
function nextSlideClickHandler(){
    if (!canClick)  return;
    else
        canClick = false;
    scrollingSlide('-',activeSlide.nextElementSibling,ulSliderFirstChildClone,ulSliderFirstChild);
}
function prevSlideClickHandler(){
    if(!canClick)
        return;
    else
        canClick = false;
    scrollingSlide('+',activeSlide.previousElementSibling,ulSliderLastChildClone,ulSliderLastChild);
}

function scrollingSlide(operator,destinationActive,compareSlide,firstActiveSlide){
    ulSlider.style.transition = "1s";
    if(operator === '+')
        left += 400;
    else  left  -= 400;
    ulSlider.style.left = left + "px";
    setTimeout(function(){
        activeSlide = destinationActive;
        if(activeSlide.isSameNode(compareSlide)){
            ulSlider.style.transition = "0s";
            if(operator === '+')
                left = -(temp);
            else  left = -400;
            ulSlider.style.left = left + 'px';
            activeSlide = firstActiveSlide;
        }
        canClick = true;
        clearInterval(slideShowInterval);
        slideShowInterval = setInterval(function () {
            nextSlideClickHandler();
        }, 3000);
    },1000);
}

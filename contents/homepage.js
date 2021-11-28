"use strict"
const body = document.getElementsByTagName('body')[0];
const fullpage = document.getElementById('fullpage');
let sectionActive = fullpage.querySelector('.active');
const sideNav = document.getElementById('sideNav');
const bullets = sideNav.querySelectorAll('.bullet');
let activeBullet = sideNav.querySelector('.activeBullet');
let lastPage = fullpage.lastElementChild;
let firstPage = fullpage.firstElementChild;

let viewportHeight = getWindowHeight(); 
let viewportWidth = getWindowWidth();
let isMobile = true;
let scrollDirection;  
let canScroll = true;
//cheks for passive event support
var g_supportsPassive = false;
try {
    var opts = Object.defineProperty({}, 'passive', {
        get: function () {
            g_supportsPassive = true;
        }
    });
    window.addEventListener("testPassive", null, opts);
    window.removeEventListener("testPassive", null, opts);
} catch (e) { }
    //timeouts
    let resizeId;
    let scrollId;    

if (!isSmallScreen()) {//Descktop Device
        isMobile = false;
        window.addEventListener('keydown',keydownHandler);
        addMouseWheelHandler();// window.addEventListener('wheel',MouseWheelHandler,{passive: false});
}//end if

    window.addEventListener('resize', resizeHandler);   
    sideNav.addEventListener('click',bulletClickHandler);
function resizeHandler() {
                //in order to call the functions only when the resize is finished
                clearTimeout(resizeId);
                    resizeId = setTimeout(function () {
                        var newViewportHeight = getWindowHeight();
                        var newViewportWidth = getWindowWidth();
                        if (viewportHeight !== newViewportHeight || viewportWidth !== newViewportWidth) {
                            viewportHeight = newViewportHeight;
                            viewportWidth = newViewportWidth; 
                            if (!isMobile && isSmallScreen() ) { //from Descktop to mobile  
                                scrollDirection = 'up';
                                if(!isFirstOrLastPage())
                                   scrollingUp();
                                sectionActive.nextElementSibling.classList.remove('fadeIn');
                                removeMouseWheelHandler();
                                window.removeEventListener('keydown',keydownHandler);
                                body.classList.remove('hiddenScrollbarInEdge');  
                                body.classList.remove('hiddenScrollbar');                       
                                isMobile = true;
                            } else if (isMobile && !isSmallScreen() ){//from mobile to Descktop
                                setTimeout(function(){                                                    
                                    addMouseWheelHandler();
                                    window.addEventListener('keydown',keydownHandler);
                                },500);
                                body.classList.add('hiddenScrollbarInEdge'); 
                                body.classList.add('hiddenScrollbar');
                                isMobile = false;  
                            }//end of else
                        }
                    }, 200);        
            }//end function resizeHandler
function bulletClickHandler(event){
           var anchor = event.target.dataset.anchor;
           scrollDirection = anchor; 
           if(!hasClass(event.target,'activeBullet')){
               canScroll = false;
               scrollHandler();
           }
           
        }
//when scrolling
function scrollHandler() {;
        let scrolling = (scrollDirection === 'down') ? scrollingDown : scrollingUp;
        scrolling();        
    }//end function scrollHandler         
function scrollingDown() {
        fullpage.classList.remove('slideToBottom');
        fullpage.classList.add('slideToUp');
        sectionActive.classList.add('fadeIn');
        sectionActive.nextElementSibling.classList.remove('fadeIn');
        fullpage.addEventListener('transitionend', endScrolling(sectionActive, sectionActive.nextElementSibling,
                                                                activeBullet,activeBullet.nextElementSibling));
    }
function scrollingUp() {
        fullpage.classList.remove('slideToUp');
        fullpage.classList.add('slideToBottom');
        sectionActive.classList.add('fadeIn');
        sectionActive.previousElementSibling.classList.remove('fadeIn');
        fullpage.addEventListener('transitionend', endScrolling(sectionActive, sectionActive.previousElementSibling,
                                                                activeBullet,activeBullet.previousElementSibling));
    }
function endScrolling(currentSection, destinationSection,currentBullet,destinationBullet) {
        currentSection.classList.remove('active');
        destinationSection.classList.add('active');
        sectionActive = destinationSection;
        currentBullet.classList.remove('activeBullet');
        destinationBullet.classList.add('activeBullet');
        activeBullet = destinationBullet;        
        setTimeout(function () {
            canScroll = true;     
        }, 1000);
    }//end function endTransition

function keydownHandler(event){
                if(event.key === 'ArrowDown')
                       scrollDirection = 'down';
                    else if(event.key === 'ArrowUp' )
                       scrollDirection = 'up'; 
                if(isFirstOrLastPage())
                   return;
                if(canScroll){
                    if(event.key === 'ArrowDown' || event.key === 'ArrowUp'){
                        canScroll = false; 
                        scrollHandler();                 
                    }                      
                }   
            }
function addMouseWheelHandler(){
                var prefix = '';
                var _addEventListener;

                if (window.addEventListener){
                    _addEventListener = "addEventListener";
                }else{
                    _addEventListener = "attachEvent";
                    prefix = 'on';
                }
        
        //        // detect available wheel event
                var support = 'onwheel' in document.createElement('div') ? 'wheel' : // Modern browsers support "wheel"
                          document.onmousewheel !== undefined ? 'mousewheel' : // Webkit and IE support at least "mousewheel"
                          'DOMMouseScroll'; // let's assume that remaining browsers are older Firefox
                var passiveEvent = g_supportsPassive ? {passive: false }: false;
                if(support === 'DOMMouseScroll'){
                    document[ _addEventListener ](prefix + 'MozMousePixelScroll', MouseWheelHandler, passiveEvent);
                }

        //        //handle MozMousePixelScroll in older Firefox
                else{
                    document[ _addEventListener ](prefix + support, MouseWheelHandler, passiveEvent);
                }      
       
 }

function MouseWheelHandler(event){       
                event = event || window.event;
                var value = event.wheelDelta || -event.deltaY || -event.detail;
                if(value < 0 )
                    scrollDirection = 'down';
                  else if (value > 0)
                    scrollDirection = 'up';   
        
                if(isFirstOrLastPage())
                  return;

                if(canScroll){
                    if(value < 0 || value > 0){
                        canScroll = false; 
                        scrollHandler();                 
                    }                                        
                }      
                event.preventDefault();       
 }

function isFirstOrLastPage(){
     if( (firstPage.isSameNode(sectionActive) && (scrollDirection === 'up' ) )
            || (lastPage.isSameNode(sectionActive) && (scrollDirection === 'down')))
        return true;
    else
        return false;    
 }

function removeMouseWheelHandler(){
                if (document.addEventListener) {
                    document.removeEventListener('mousewheel', MouseWheelHandler, false); //IE9, Chrome, Safari, Oper
                    document.removeEventListener('wheel', MouseWheelHandler, false); //Firefox
                    document.removeEventListener('MozMousePixelScroll', MouseWheelHandler, false); //old Firefox
                } else {
                    document.detachEvent('onmousewheel', MouseWheelHandler); //IE 6/7/8
                }
            }
function isSmallScreen() {//mobile Device return true
                return ((viewportWidth < 960 || viewportHeight < 600));
            }
function getWindowHeight() {
            return 'innerHeight' in window ? window.innerHeight : document.documentElement.offsetHeight;
     }
function getWindowWidth() {
            return window.innerWidth;
        }
function hasClass(el, className) {
    if (el == null) {
        return false;
    }
    if (el.classList) {
        return el.classList.contains(className);
    }
    //return new RegExp('(^| )' + className + '( |$)', 'gi').test(el.className);
}


// Close btn
const notificationBar = document.querySelector('.notification-bar');
if (notificationBar) {
    const closeBtn = document.querySelector('.notification-bar .close-btn');
    closeBtn.addEventListener("click", () => {
        notificationBar.classList.add("visibility");
    });
}
// Banner
const banner = document.querySelector('.index-body .banner');
let count=0;
function changeBanner(){
    count++;
    if(count==1){
        banner.style.backgroundImage = 'url("images/home/banner1.jpg")'; 
    }else if(count == 2){
        banner.style.backgroundImage = 'url("images/home/banner2.jpg")'; 
    }else if(count == 3){
        banner.style.backgroundImage = 'url("images/home/banner3.jpg")'; 
    }else if(count == 4){
        banner.style.backgroundImage = 'url("images/home/bb8.jpg")'; 
    }else if(count == 5){
        banner.style.backgroundImage = 'url("images/home/bb19.jpg")'; 
    }else if(count == 6){
        banner.style.backgroundImage = 'url("images/home/bb12.jpg")'; 
    }else if(count == 7){
        banner.style.backgroundImage = 'url("images/home/bb21.jpg")'; 
    }else if(count == 8){
        banner.style.backgroundImage = 'url("images/home/banner5.jpg")'; 
        count=0;
    }
}
const progress = document.getElementById("banner-progress");
const time = 10000;
var timeCount = 0;
const timeSkip = 25;
progress.max = time;
function ChangeProgress(){
    progress.value = timeCount;
    timeCount=timeCount+timeSkip;
    if(timeCount==time){
        timeCount=0;
        changeBanner();
    }
}
setInterval(ChangeProgress,timeSkip);
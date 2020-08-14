// Show alert when adding an item to the cart
const addToCartForm = document.querySelector(".item-body .item-details .add-cart-row form.add-to-cart-form");
addToCartForm.addEventListener("submit",(e)=>{
   e.preventDefault();
   showAlert("Confrim","Add this item to the cart?","",()=>addToCartForm.submit(),"Add","","")
});

// Display the Rate model
const addRatingBtn = document.querySelector(".item-body .item-details .review-row a.add-rating");
const addRatingOverlay =document.getElementById("rating-overlay");
addRatingBtn.addEventListener("click",()=>{
   addRatingOverlay.classList.remove("visibility");
});
// Close model
const closeBtn = addRatingOverlay.querySelector(".btn-row .btn-close");
closeBtn.addEventListener("click",()=>{
   addRatingOverlay.classList.add("visibility");
});

// Set the input to the selected value
const ratingInput = addRatingOverlay.querySelector("form#start-rating-form .rate-slider input#star-rating-input");
const addRatingForm  =document.getElementById('start-rating-form');
addRatingForm.addEventListener("submit",(e)=>{
   e.preventDefault();
   if(ratingInput.value>0 && ratingInput.value <=5){
      addRatingForm.submit();
   }else{
      // addRatingOverlay.classList.add("visibility");
   }
   
});

const starImages = addRatingOverlay.querySelectorAll('img.star');
function ClickStar(star){
   starImages.forEach(image=>{
      image.classList.remove("star-full");
      image.classList.add("star-empty");
   });
   let starValue = parseInt(star.id.substring(5));
   ratingInput.value=starValue;
   for(var i=1;i<=starValue;i++){
      document.getElementById("star-"+i).classList.remove("star-empty");
      document.getElementById("star-"+i).classList.add("star-full");
   }
}
starImages.forEach(img=>{
   img.addEventListener("click",()=>ClickStar(img));
});

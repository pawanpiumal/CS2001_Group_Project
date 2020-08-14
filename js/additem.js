// Image related nodes
const imageInput = document.querySelector('input[name="itemImage"]');
const imageChangeBtn = document.getElementById("image-change-btn");
const imageDiv = document.getElementById("item-image");
const imageRemoveBtn =document.getElementById("remove-image-btn");
imageChangeBtn.addEventListener("click", () => {
    imageInput.click();
});

// Check the image file input to see whether the file type is accepted or not
// If accepted then show the image in the image div node
imageInput.addEventListener("input", () => {
    var fileTypes = ['jpg', 'jpeg', 'png'];
    var extension = imageInput.files[0].name.split('.').pop().toLowerCase();
    var isSuccess = fileTypes.indexOf(extension) > -1;
    if (isSuccess) {
        var fileReader = new FileReader();
        fileReader.onload = function () {
            imageDiv.style = "background-image: url(" + fileReader.result + ");"
        }
        fileReader.readAsDataURL(imageInput.files[0]);
        imageRemoveBtn.style.visibility  = "visible";
    }
});

// Remove the image from the div and set the default image
imageRemoveBtn.addEventListener("click",()=>{
    if(imageInput.value!="" ){
        showAlert("Confrim","Do you want to remove the selected image?","danger",()=>{
            imageInput.value="";
            imageDiv.style.backgroundImage ="url(images/items/NoImage.png)";
            imageRemoveBtn.style.visibility  = "hidden";
        },"Remove","","Close");
    }
});
// Submit the form when submit button clicked
// If an image is not selected or the image was set to the default image show a warning
const imageLocation = document.querySelector(".add-item-body .image-body #item-image input[type='text']");
const addItemForm = document.getElementById("add-item-form");
addItemForm.addEventListener("submit", (e) => {
    e.preventDefault();
    close = () => {
        return false;
    }
    const continueOp=()=>{
        addItemForm.submit();
    }
    if (imageInput.value == "" && imageLocation.value.toLowerCase()=="images/items/NoImage.png".toLowerCase() ) {
        showAlert("Confirm", "Image is not selected do you want to continue?", "danger",continueOp, "Continue", "", "Close");
    }else{
        continueOp();
    }
});

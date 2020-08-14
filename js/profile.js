// Mobile Number validations
// Do net enter more than some count of characters depending on the mobile number type
const mobileNumber = document.querySelector('input[name="mobile"]');
mobileNumber.addEventListener('input', () => {
    if (mobileNumber.value.length > 9 && (mobileNumber.value.substr(0, 1) != 0 && mobileNumber.value.substr(0, 2) != 94)) {
        mobileNumber.value = mobileNumber.value.substr(0, 9);
    } else if (mobileNumber.value.length > 10 && mobileNumber.value.substr(0, 1) == 0) {
        mobileNumber.value = mobileNumber.value.substr(0, 10);
    } else if (mobileNumber.value.length > 11 && mobileNumber.value.substr(0, 2) == 94) {
        mobileNumber.value = mobileNumber.value.substr(0, 11);
    }
});

// Image uploading alert
const imageInput = document.querySelector('input[name="imageFile"]');
const imageUpload = document.getElementById("image-upload");
const imageForm = document.getElementById("image-form");
const deleteImage = document.querySelector("#image-delete");
imageUpload.addEventListener("click", () => {
    if (imageInput.value == "") {
        imageInput.click();
    } else {
        showAlert("Confirm", "Do you want to Update the Image", "", () => imageForm.submit(), "Upload");
    }
});

// Image remove
deleteImage.addEventListener("click", () => {
    if (imageInput.value != "") {
        imageInput.value = "";
        imageForm.style = "background-image: url(images/users/user.png);"
        deleteImage.classList.add("visibility-btn");
    }
});

// Image setting to the div background
imageInput.addEventListener("input", () => {
    var fileTypes = ['jpg', 'jpeg', 'png', 'gif'];
    var extension = imageInput.files[0].name.split('.').pop().toLowerCase();
    var isSuccess = fileTypes.indexOf(extension) > -1;
    if (isSuccess) {
        var fileReader = new FileReader();
        fileReader.onload = function () {
            imageForm.style = "background-image: url(" + fileReader.result + ");"
        }
        fileReader.readAsDataURL(imageInput.files[0]);
        deleteImage.classList.remove("visibility-btn");
        showAlert("Confirm", "Do you want to Update the Image", "", () => imageForm.submit(), "Upload");
    }
});

// Form submission validations
const editForm = document.getElementById("edit-details-form");
const errorMsg = document.getElementById("error-edit");
const formInputs = document.querySelectorAll(".edit-profile form.edit-details input");
const password = document.querySelector('input[name="password"]');
const confirmPassword = document.querySelector('input[name="confirmPassword"]');
editForm.addEventListener("submit", (e) => {
    e.preventDefault();
    const formInputsArr = [...formInputs];
    if (mobileNumber.value != "" && (mobileNumber.value.length < 9 || mobileNumber.value.length > 12)) {
        showAlertOK("Error!", "Invalid mobile number.", "danger", "", "Close");
        errorMsg.innerHTML = "Error! Invalid mobile number.";
        return false;
    } else {
        errorMsg.innerHTML = "";
    }
    if ((password.value != "" || confirmPassword.value != "") && confirmPassword.value != password.value) {
        showAlertOK("Error!", "Password and Confirm-Password must match.", "danger", "", "Close");
        errorMsg.innerHTML = "Error! Password and Confirm-Password must match.";
        return false;
    } else {
        errorMsg.innerHTML = "";
    }
    if (formInputsArr.map(er => er.value == "" ? false : true).includes(true)) {
        errorMsg.innerHTML = "";
        showAlert("Confirm", "Do you want to change the details?", "", () => editForm.submit(), "Save", "", "");
    } else {
        showAlertOK("Error!", "One or more changes are required.", "danger", "", "Close");
        errorMsg.innerHTML = "Error! One or more changes are required.";
    }

});
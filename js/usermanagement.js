// Getting all the edit button forms in the Table
const editForms = document.querySelectorAll('.user-management-body .user-table table .action-btn-row form.edit-form');

// Overlay of the Edit Dialog
const overlay = document.getElementById('overlay-edit');
// Edit dialog buttons
const alertCloseBtn = document.querySelector('.overlay .alert .btn-row .btn-close');
const alertSaveBtn = document.querySelector('.overlay .alert .btn-row .btn-save');

// Edit Dialog Input Fields
const editDialogUid = document.querySelector('.alert input[name="uid"]');
const editDialogHiddenUid = document.querySelector('.alert input[name="editUID"]');
const editDialogUsername = document.querySelector('.alert input[name="username"]');
const editDialogEmail = document.querySelector('.alert input[name="email"]');
const editDialogMobile = document.querySelector('.alert input[name="mobile"]');
const editDialogAddress = document.querySelector('.alert input[name="address"]');
const editDialogPassword = document.querySelector('.alert input[name="password"]');
const editDialogConfirmPassword = document.querySelector('.alert input[name="confirmPassword"]');

// Function to Open the edit dialog and set the relavent details to the input fields
function openEditDialog(e, eForm) {
    e.preventDefault();
    // Show the dialog
    overlay.classList.remove('visibility');
    //Disable the scrollbar
    document.body.classList.add('model-open');
    // Setting the inputs to the relavent fields
    editDialogUid.value = eForm.elements['uid'].value;
    editDialogHiddenUid.value = eForm.elements['uid'].value;
    editDialogUsername.value = eForm.elements['username'].value;
    editDialogEmail.value = eForm.elements['email'].value;
    editDialogMobile.value = eForm.elements['mobile'].value;
    editDialogAddress.value = eForm.elements['address'].value;
}


// Edit dialog Close Button function
function closeEditDialog() {
    // Hide the dialog 
    overlay.classList.add('visibility');
    // Re-enable the scrollbar
    document.body.classList.remove('model-open');
    // Deleting the inputs in the fields
    editDialogUid.value = "";
    editDialogUsername.value = "";
    editDialogEmail.value = "";
    editDialogMobile.value = "";
    editDialogAddress.value = "";
    editDialogPassword.value = "";
    editDialogConfirmPassword.value = "";
}
alertCloseBtn.addEventListener("click", closeEditDialog);


// Add the Edit button function to all the edit button forms in the table
[...editForms].forEach(eForm => {
    eForm.addEventListener("submit", e => openEditDialog(e, eForm));
});

// Getting all the delete button forms in the Table
const deleteForms = document.querySelectorAll('.user-management-body .user-table table .action-btn-row form.delete-form');

function deleteDialog(e, dForm) {
    e.preventDefault();
    showAlert("Confirm", "Do you want to delete this user account?", "danger", () => dForm.submit(), "Delete", "", "");
}

// Add the Delete button function to all the delete button forms in the table
[...deleteForms].forEach(dForm => {
    dForm.addEventListener("submit", e => deleteDialog(e, dForm));
});

// Error Message
const editDialogError = document.getElementById("edit-dialog-error");

// Edit dialog validations
const editDialogForm = document.querySelector(".overlay .alert form.edit-info");
editDialogForm.addEventListener("submit", e => {
    e.preventDefault();
    if (editDialogMobile.value.length > 12 || editDialogMobile.value.length < 9) {
        editDialogError.innerHTML = "Error! Error! Invalid mobile number.";
    } else {
        editDialogError.innerHTML = "";
    }
    if (editDialogPassword.value !== editDialogConfirmPassword.value) {
        editDialogError.innerHTML = "Error! Password and Confirm-Password must match.";
    } else {
        editDialogError.innerHTML = "";
    }
    if(editDialogError.innerHTML == ""){
        editDialogForm.submit();
    }
});


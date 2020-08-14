// Getting all the Edit Form buttons
const editFormBtns = document.getElementsByClassName('edit-form');

// Show alert before submitting edit form function
function editFormFunc(e, eForm) {
    e.preventDefault();
    showAlert("Confirm", "Do you want to edit this item details?", "", () => eForm.submit(), "Edit", "", "Close");
}

// Adding the function to all the edit form buttons in the table
[...editFormBtns].forEach(edForm => {
    edForm.addEventListener("submit", e => editFormFunc(e, edForm));
});

// Getting all the Delete Form buttons
const deleteFormBtns = document.getElementsByClassName('delete-form');

// Show alert before submitting Delete form function
function deleteFormFunc(e, dForm) {
    e.preventDefault();
    showAlert("Confirm", "Do you want to Delete this item?", "danger", () => dForm.submit(), "Delete", "", "Close");
}
// Adding the function to all the delete form buttons in the table
[...deleteFormBtns].forEach(dForm => {
    dForm.addEventListener("submit", e => deleteFormFunc(e, dForm));
});
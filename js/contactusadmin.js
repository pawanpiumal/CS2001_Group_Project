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


// Getting all the subject and message buttons
const subjectMessages = document.querySelectorAll('.tooltip');
const messageAlertOverlay = document.getElementById("message-alert");
const alertSubject = messageAlertOverlay.querySelector('.alert .alert-subject');
const alertMessage = messageAlertOverlay.querySelector('.alert .alert-message');
const closeBtn = messageAlertOverlay.querySelector('.alert .btn-row button.btn-close');
[...subjectMessages].forEach(tooltip => {
    tooltip.addEventListener("click", () => {
        document.body.classList.add('model-open');
        messageAlertOverlay.classList.remove('visibility');
        var row = tooltip.parentNode.parentNode;
        alertSubject.innerHTML = row.querySelector('.subject-long').innerHTML;
        alertMessage.innerHTML = row.querySelector('.message-long').innerHTML;
    });

});

function modelClose() {
    messageAlertOverlay.classList.add('visibility');
    document.body.classList.remove('model-open');
    alertSubject.innerHTML = "";
    alertMessage.innerHTML = "";
}

closeBtn.addEventListener("click",modelClose);
messageAlertOverlay.addEventListener("click",modelClose );
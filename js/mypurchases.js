// Getting all the Delete button froms from the table
const deleteForms = document.querySelectorAll('.my-purchases-body .my-purchases-table tbody form.delete-form');

// Ask for confirmation for the delete form
function dFormFunction(event, dForm){
    event.preventDefault();
    showAlert("Confirm","Do you want to delete this purchase?","danger",()=>dForm.submit(),"Delete","","");
}

// Assigning the event to the Buttons
[...deleteForms].forEach(dForm=>{
    dForm.addEventListener("submit",(e)=>dFormFunction(e,dForm));
});

// Getting all the Item List buttons
const itemListBtns =document.querySelectorAll('.my-purchases-body .my-purchases-table table tbody .list-btn');
const overlay = document.getElementById('item-list-overlay');
const itemListBody = document.querySelector('.overlay .alert .alert-body-list');

// Function to show the dialog if the item list btn is pressed
function showDialogItemList(itemListBtn){
    // Get the hidden inputs in the div
    const inputs = itemListBtn.querySelectorAll(".item-list-item");
    itemListBody.innerHTML ="";
    inputs.forEach(inp=>{
        let p = document.createElement('p');
        p.innerHTML = inp.value;
        itemListBody.appendChild(p);
    });
    document.body.classList.add('model-open');
    overlay.classList.remove("visibility");
}
itemListBtns.forEach(btn=>{
    btn.addEventListener("click",()=>showDialogItemList(btn))
});

// Close the dialog
const closeBtn  = document.querySelector('#item-list-overlay .alert .btn-row .btn-close');
closeBtn.addEventListener("click",()=>{
    overlay.classList.add("visibility");
    document.body.classList.remove('model-open');
});
const alertWindow = overlay.querySelector('.alert');
alertWindow.addEventListener("click",(e)=>{
    e.stopPropagation();
});

overlay.addEventListener("click",(e)=>{
    overlay.classList.add("visibility");
    document.body.classList.remove('model-open');
});
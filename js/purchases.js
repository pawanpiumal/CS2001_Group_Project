// Getting all the Delete button froms from the table
const deleteForms = document.querySelectorAll('.purchases-body .purchases-table tbody form.delete-form');

// Ask for confirmation for the delete form
function dFormFunction(event, dForm){
    event.preventDefault();
    showAlert("Confirm","Do you want to delete this purchase?","danger",()=>dForm.submit(),"Delete","","");
}

// Assigning the event to the Buttons
[...deleteForms].forEach(dForm=>{
    dForm.addEventListener("submit",(e)=>dFormFunction(e,dForm));
});

// Getting all the Complete Buttons from the Table
const completeForms = document.querySelectorAll('.purchases-body .purchases-table tbody form.completed-form');

// Ask for confirmation to complete the purhcase
function completeFunction(e,cForm){
    e.preventDefault();
    showAlert("Confirm","Do you want to complete this purchase?","",()=>cForm.submit(),"Complete","","");
}

// Assigning the Complete Function to all the Complete Buttons
[...completeForms].forEach(cForm=>{
    cForm.addEventListener("submit",(e)=>completeFunction(e,cForm));
});

// Getting all the Item List buttons
const itemListBtns =document.querySelectorAll('.purchases-body .purchases-table table tbody .list-btn');
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
    overlay.classList.remove("visibility")
    document.body.classList.add('model-open');
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
overlay.addEventListener("click",()=>{
    overlay.classList.add("visibility");
    document.body.classList.remove('model-open');
});
// Select All button
const selectAll = document.querySelector('input[name="selectAll"]');
// All the Select Buttons in the cart
const selectButtons = document.querySelectorAll('.item-body input[type="checkbox"]');
// Check all the select buttons in the cart when select all button is checked
// and uncheck all the select buttons in the cart when the select all button is unchecked
if(selectAll){
    selectAll.addEventListener("input", () => {
        if (selectAll.checked) {
            selectButtons.forEach(btn => {
                btn.checked = true;
            });
        } else {
            selectButtons.forEach(btn => {
                btn.checked = false;
            });
        }
        updateSummery();
    });
}


//Check the select all button if all the select buttons in the cart is checked
// Update the summery
selectButtons.forEach(btn => {
    btn.addEventListener("input", () => {
        updateSummery();
        if ([...selectButtons].map(element => {
            if (element.checked == false) {
                return true;
            } else {
                return false;
            }
        }).includes(true)) {
            selectAll.checked = false;
        } else {
            selectAll.checked = true;
        }
    });
});

// Ask for confirmation when pressed buy button
const buyForm = document.querySelector('.cart-body form.buy-form');
const buyBtn = document.querySelector(".cart-body .summery button.cart-button");
buyBtn.addEventListener("click", () => {
    // Cancel the request if none of the items are selected
    if (![...selectButtons].map(btn => btn.checked == true ? true : false).includes(true)) {
        showAlertOK("Error", "Select items to buy.", "danger", "", "OK");
    } else {
        function buyFormFunction(){
            const inputBuy = document.createElement('input');
            inputBuy.type="hidden";
            inputBuy.name= "buyCIDArray";
            CIDArray =  [];
            [...selectButtons].forEach(btn=>{
                if(btn.checked) CIDArray.push(btn.value)
            });
            const existInput =buyForm.querySelector('input[name="buyCIDArray"]');
            inputBuy.value = JSON.stringify(CIDArray);
            if(!existInput){
                buyForm.appendChild(inputBuy);
            }else{
                existInput.remove();
                buyForm.appendChild(inputBuy);
            }
            buyForm.submit();
        }
        showAlert("Confirm", "Do you want to buy the selected items ?", "", buyFormFunction, "Yes", "", "No");
    }
});

// Getting all the Delete Buttons from the cart
const deleteButtons = document.querySelectorAll('.cart-body .item-body form.delete-form');
function delFunction(e, dForm) {
    e.preventDefault();
    showAlert("Confirm", "Do you want to delete this item from the cart?", "danger", () => dForm.submit(), "Delete", "", "");
}
[...deleteButtons].forEach(delBtn => {
    delBtn.addEventListener("click", (e) => delFunction(e, delBtn));
});

// Select the checkbox if the item body is clicked
const itemBodies = document.querySelectorAll(".cart-body .item-list .item-body");
function selectBox(itemBody, e) {
    if (e.target.classList != 'bin' && e.target.classList != "checkmark")
        itemBody.querySelector(".checkbox input[type='checkbox']").click();
}
itemBodies.forEach(item => {
    item.addEventListener("click", (e) => selectBox(item, e));
});

// Update the Summery Box when an item is selected
const summerySubtotal = document.getElementById('summery-subtotal');
const summeryDiscount = document.getElementById('summery-discount');
const summeryTotal = document.getElementById('summery-total');
function updateSummery() {
    let priceInputs= [];
    let discountInputs =[];
    itemBodies.forEach(itemBody=>{
        if(itemBody.querySelector(".checkbox input[type='checkbox']").checked){
            priceInputs.push(itemBody.querySelector('.item-details input[name="price"]'));
            discountInputs.push(itemBody.querySelector('.item-details input[name="discount"]'));
        }
    })
    let price = 0, discount = 0;
    priceInputs.forEach(priceInp => price += parseFloat(priceInp.value));
    discountInputs.forEach(discInp => discount += parseFloat(discInp.value));
    summerySubtotal.innerHTML = "LKR "+price.toFixed(2);
    summeryDiscount.innerHTML = "LKR "+discount.toFixed(2);
    summeryTotal.innerHTML = "LKR "+(price-discount).toFixed(2);
}
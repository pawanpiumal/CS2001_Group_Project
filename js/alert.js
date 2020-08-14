function showAlert(title, body, type, saveAction, mainBtnText, closeAction,secondbtnText) {
    // Create the overlay
    // This blur the background
    const overlay = document.createElement("div");
    overlay.classList.add("overlay");

    // Main alert dialog box
    const alertDialog = document.createElement("div");
    alertDialog.classList.add("alert");

    // If the type is danger the alert border color is set to red
    if (type == "danger") {
        alertDialog.classList.add("alert-danger");
    }

    const alertTitle = document.createElement("h1");
    alertTitle.classList.add("alert-title");
    alertTitle.innerHTML = title;

    const alertBody = document.createElement("p");
    alertBody.classList.add("alert-body");
    alertBody.innerHTML = body;


    const btnClose = document.createElement("button");
    btnClose.classList.add("btn-close");
    btnClose.innerHTML = secondbtnText || "Close";
    const btnSave = document.createElement("button");
    btnSave.classList.add("btn-save");
    btnSave.innerHTML = mainBtnText || "Save";
    const btnRow = document.createElement("div");
    btnRow.classList.add("btn-row");
    btnRow.appendChild(btnClose);
    btnRow.appendChild(btnSave);

    // Appeding the elements to the alert node
    alertDialog.appendChild(alertTitle);
    alertDialog.appendChild(alertBody);
    alertDialog.appendChild(btnRow);
    overlay.appendChild(alertDialog);
    document.body.appendChild(overlay);


    // Functions to close the alert dialog
    function alertClose(e) {
        if (document.body.contains(overlay)) {
            document.body.removeChild(overlay);
        }
        return false;
    }
    if (saveAction)
        btnSave.addEventListener("click", saveAction);
    btnSave.addEventListener("click", (e) => alertClose(e));

    btnClose.addEventListener("click", (e) => alertClose(e));
    if (closeAction)
        btnClose.addEventListener("click", closeAction);

    overlay.addEventListener("click", (e) => alertClose(e));
    // If the alert dialog is clicked the parents close function is not executed.
    alertDialog.addEventListener("click",(e)=>{
        e.stopPropagation();
    });
}

function showAlertOK(title, body, type, saveAction, mainBtnText) {
    const overlay = document.createElement("div");
    overlay.classList.add("overlay");

    const alertDialog = document.createElement("div");
    alertDialog.classList.add("alert");

    if (type == "danger") {
        alertDialog.classList.add("alert-danger");
    }

    const alertTitle = document.createElement("h1");
    alertTitle.classList.add("alert-title");
    alertTitle.innerHTML = title;

    const alertBody = document.createElement("p");
    alertBody.classList.add("alert-body");
    alertBody.innerHTML = body;


    const btnSave = document.createElement("button");
    btnSave.classList.add("btn-save");
    btnSave.innerHTML = mainBtnText || "OK";
    const btnRow = document.createElement("div");
    btnRow.classList.add("btn-row");
    btnRow.appendChild(btnSave);


    alertDialog.appendChild(alertTitle);
    alertDialog.appendChild(alertBody);
    alertDialog.appendChild(btnRow);
    overlay.appendChild(alertDialog);
    document.body.appendChild(overlay);


    function alertClose(e) {
        if (document.body.contains(overlay)) {
            document.body.removeChild(overlay);
        }
        return false;
    }
    if (saveAction){
        btnSave.addEventListener("click", saveAction);
        overlay.addEventListener("click", saveAction);
        
    }
    btnSave.addEventListener("click", (e) => alertClose(e));

    overlay.addEventListener("click", (e) => alertClose(e));
    alertDialog.addEventListener("click",(e)=>{
        e.stopPropagation();
    });
}

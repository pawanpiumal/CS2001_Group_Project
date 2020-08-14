const loginForm = document.querySelector('form.login-form');
const emailInput = document.querySelector('input[name="email"]');
const passwordInput = document.querySelector('input[name="password"]');
const errorMsg = document.getElementById("error-login");
loginForm.addEventListener('submit',e=>{
    e.preventDefault();
    if(emailInput.value===""){
        errorMsg.innerHTML = "Error! Email can't be empty.";
    }else if (!/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(String(emailInput.value).toLowerCase())) {
        errorMsg.innerHTML = "Error! Email format is incorrect.";
    } else if(passwordInput.value===""){
        errorMsg.innerHTML = "Error! Password can't be empty.";
    }else {
        errorMsg.innerHTML = "";
        loginForm.submit();
    }
});
const userType = document.getElementById('user-type');
// alert(userType.list.options[1].value);
userType.addEventListener("change", (e) => {
    if (userType.value !== "") {
        userType.style = "color:black";
    } else {
        userType.style = "color:#757575";
    }
});

if(userType.value=="user" || userType.value == "admin"){
    userType.style = "color:black";
}

const registerForm = document.querySelector('.login-form.signup-form');
const usernameField = document.querySelector('input[name="username"]');
const emailField = document.querySelector('input[name="email"]');
const passwordField = document.querySelector('input[name="password"]');
const confirmPasswordField = document.querySelector('input[name="confirmPassword"]');
const errorMsg = document.getElementById("errorRegister");
registerForm.addEventListener('submit', (e) => {
    e.preventDefault();
    if (usernameField.value === "") {
        errorMsg.innerHTML = "Error! Username can't be empty.";
    } else if (emailField.value === "") {
        errorMsg.innerHTML = "Error! Email can't be empty.";
    } else if (!/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(String(emailField.value).toLowerCase())) {
        errorMsg.innerHTML = "Error! Email format is incorrect.";
    } else if (passwordField.value === "") {
        errorMsg.innerHTML = "Error! Password can't be empty.";
    } else if (confirmPasswordField.value === "") {
        errorMsg.innerHTML = "Error! Confirm-Password can't be empty.";
    } else if (passwordField.value !== confirmPasswordField.value) {
        errorMsg.innerHTML = "Error! Password and Confirm-Password mismatch.";
    } else if (userType.value === "") {
        errorMsg.innerHTML = "Error! User type should be User or Admin."
    } else {
        errorMsg.innerHTML = "";
        registerForm.submit();
    }
});
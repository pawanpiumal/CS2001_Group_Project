// Close btn
const notificationBar = document.querySelector('.notification-bar');
if (notificationBar) {
    const closeBtn = document.querySelector('.notification-bar .close-btn');
    closeBtn.addEventListener("click", () => {
        notificationBar.classList.add("visibility");
    });
}

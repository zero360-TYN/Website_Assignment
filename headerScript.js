//-------------------USER ICON-----------------------

const userBtn = document.getElementById("userbtn");
const userMenu = document.getElementById("usermenu");

userBtn.addEventListener("click", () => {
    userMenu.classList.toggle("show");
});

document.addEventListener("click", (e) => {
    if (!userBtn.contains(e.target) && !userMenu.contains(e.target)) {
        userMenu.classList.remove("show");
    }
})


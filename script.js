
const userBtn = document.getElementById("userbtn");
const userMenu = document.getElementById("usermenu");

// userBtn.addEventListener("click", () => {
//     userMenu.classList.toggle("show");
// });

document.addEventListener("click", (e) => {
    if (!userBtn.contains(e.target) && !userMenu.contains(e.target)) {
        userMenu.classList.remove("show");
    }
})
//user dropwarp menu
document.addEventListener('DOMContentLoaded', () => {
    console.log("JS 已连接");
    const avatar = document.getElementById('userAvatar');
    const menu = document.getElementById('userDropdown');

    if (avatar && menu) {
        avatar.addEventListener('click', (e) => {
            e.stopPropagation();
            menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
        });

        window.addEventListener('click', () => {
            menu.style.display = 'none';
        });
    }
});

function addData() {
    let currentRow = null;
    var product_name = document.getElementById("pdtnme").value;
    var product_price = document.getElementById("pdtpc").value;
    var table = document.getElementById("pdtable");
    

    if (product_name == "" || product_price == "") {
        alert("Please fill all field!");
        return;
    } else if (product_price == 0){
        alert("Price cannot be zero!");
        return;
    } else if (product_price < 0) {
        alert("Price cannot be negative!");
        return;
    }

    for(var i = 1; i < table.rows.length; i++) {
        existingname = table.rows[i].cells[2].innerHTML;

        if(product_name.toLowerCase() == existingname.toLowerCase()) {
            alert("This product name already exists!");
            return;
        }
    }

    var row = table.insertRow();

    var cell0 = row.insertCell(0);
    var cell1 = row.insertCell(1);
    var cell2 = row.insertCell(2);
    var cell3 = row.insertCell(3);
    var cell4 = row.insertCell(4);
    var cell5 = row.insertCell(5);
    var cell6 = row.insertCell(6);

    cell0.innerHTML = "PD"+ Math.floor(Math.random() * 1000);
    cell1.innerHTML = "-";
    cell2.innerHTML = product_name;
    cell3.innerHTML = "-";
    cell4.innerHTML = parseFloat(product_price).toFixed(2);
    cell5.innerHTML = "-";
    cell6.innerHTML = '<button class="pdtet">Edit</button><button class="pdtdl">Delete</button>';

    document.getElementById("pdtnme").value = "";
    document.getElementById("pdtpc").value = "";

    var pdtdlbtn = cell6.querySelector(".pdtdl");
    
    pdtdlbtn.addEventListener("click", function() {
        currentRow = row;
        document.getElementById("deleterow").style.display = "flex";
    });
    document.getElementById("yesdelete").onclick = function() {
        if (currentRow) {
            currentRow.remove();
        }
        document.getElementById("deleterow").style.display = "none";
    }
    document.getElementById("canceldelete").onclick = function() {
        document.getElementById("deleterow").style.display = "none";
    }

}
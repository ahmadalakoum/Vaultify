document.getElementById("updateProfileForm").addEventListener("submit",async (e)=>{
    e.preventDefault();
    const phone = document.getElementById("phone").value.trim();
    const address = document.getElementById("address").value.trim();
    const message = document.getElementById("updateMessage");

    if (!phone && !address) {
        message.textContent = "Please enter at least one field to update.";
        message.style.color = "red";
        return;
    }

    const requestBody = {};
    if (phone) requestBody.phone = phone;
    if (address) requestBody.address = address;
    const updateProfileURL=`${config.apiBaseUrl}/auth/update_profile.php`;

    const response = await fetch(updateProfileURL,{
        method:"POST",
        headers:{
            "Content-Type":"application/json",
            "Authorization": `Bearer ${localStorage.getItem("userID")}`
        },
        body:JSON.stringify(requestBody)
    });
    const result = await response.json();
    if(response.ok){
        message.textContent = result.message;
        message.style.color = "green";
        setTimeout(()=>{
            window.location.href="./profile.html";
        },2000);
    } else{
        message.textContent = result.message;
        message.style.color = "red";
    }
})
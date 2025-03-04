document.getElementById("change-password-form").addEventListener("submit",async (e)=>{
    e.preventDefault();
    const oldPassword = document.getElementById("currentPassword").value.trim();
    const newPassword = document.getElementById("newPassword").value.trim();
    const confirmPassword = document.getElementById("confirmPassword").value.trim();
    const message = document.getElementById("message");
    message.textContent = '';
    if(!oldPassword ||!newPassword ||!confirmPassword){
        message.textContent = "All fields are required";
        return;
    }
    const userID=localStorage.getItem("userID");
    if(newPassword!==confirmPassword){
        message.textContent = "Passwords do not match";
        return;
    }
    const changePasswordURL=`${config.apiBaseUrl}/auth/change_password.php`;
    const response = await fetch(changePasswordURL,{
        method:"POST",
        headers:{
            "Content-Type":"application/json",
            "Authorization": `Bearer ${localStorage.getItem("userID")}`
        },
        body:JSON.stringify({currentPassword:oldPassword, newPassword,confirmPassword})

    })
    const result = await response.json();
    if(response.ok){
        message.style.color = "green";
        message.textContent = result.message;
        if(result.message === "success"){
            setTimeout(()=>{
                window.location.href = "login.html";
            },2000);
        }
        else{
            message.textContent = result.message;
        }
    }
    else{
        message.style.color = "red";
        message.textContent = result.message;
    }
})
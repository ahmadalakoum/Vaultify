document.getElementById("signupForm").addEventListener("submit",async (e)=>{
    e.preventDefault();
    const email = document.getElementById("email").value.trim();
    const phone = document.getElementById("phone").value.trim();
    const address = document.getElementById("address").value.trim();
    const password = document.getElementById("password").value.trim();
    const confirmPassword = document.getElementById("confirmPassword").value.trim();
    const message = document.getElementById("message");

    message.textContent='';
    //check if passwords match
    if(password !== confirmPassword) {
        message.textContent = "Passwords do not match";
        return;
    }

    const response = await fetch("http://localhost/Vaultify/vaultify-server/v1/auth/signup.php",{
        method:"POST",
        headers:{
            "Content-Type":"application/json",
        },
        body:JSON.stringify({email,phone,address,password,confirmPassword})

    });
    const result = await response.json();
    console.log(result);
    if(response.ok){
        message.style.color = "green";
        message.textContent = result.message;
        setTimeout(() => {
            window.location.href = "login.html"; // Redirect to login
        }, 1500);
    }else{
        message.style.color = "red";
        message.textContent = result.message;
    }

});
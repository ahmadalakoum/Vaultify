
document.getElementById("loginForm").addEventListener("submit",async (e)=>{
    e.preventDefault();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();
    const message = document.getElementById("message");
    message.textContent='';
    if(!email ||!password){
        message.textContent = "All fields are required";
        return;
    }
    const loginURL=`${config.apiBaseUrl}/auth/login.php`;
    const response = await fetch(loginURL,{
        method:"POST",
        headers:{
            "Content-Type":"application/json"
        },
        body:JSON.stringify({email,password})
    })
    const result = await response.json();
    console.log(result.user.id);
    console.log(result.user.username);
    console.log(result.user.role);
    if(response.ok){
        message.style.color = "green";
        message.textContent = result.message;
        localStorage.setItem("userID",result.user.id);
        localStorage.setItem("username",result.user.username);
        localStorage.setItem("role",result.user.role);
        setTimeout(() => {
            window.location.href = "../index.html"; 
        }, 1500);
    }

});
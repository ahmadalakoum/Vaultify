
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
    console.log(result);
    if(response.ok){
        if(result.status === 'success'){
            message.style.color = "green";
            message.textContent = result.message;
            localStorage.setItem("userID",result.user.id);
            localStorage.setItem("username",result.user.username);
            localStorage.setItem("role",result.user.role);
            if(result.user.role=='admin'){
                setTimeout(() => {
                    window.location.href = "../admin/admin-dashboard.html"; 
                }, 1500);
            }else{
                setTimeout(() => {
                    window.location.href = "../index.html"; 
                }, 1500);
            }
        }else{
            message.style.color = "red";
            message.textContent = result.message;
        }
    }

});
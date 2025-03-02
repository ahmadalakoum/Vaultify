document.addEventListener("DOMContentLoaded",async ()=>{
    const email = document.getElementById("email");
    const phone = document.getElementById("phone");
    const address= document.getElementById("address");
    const status = document.getElementById("status");
    const limit = document.getElementById("daily_limit");

    const userID=localStorage.getItem("userID");
    console.log(userID);
    const profileURL=`${config.apiBaseUrl}/auth/user_profile.php`;
    const response = await fetch(profileURL,{
        method:"GET",
        credentials:"include",
        headers:{
            "Content-Type":"application/json",
            "Authorization": `Bearer ${localStorage.getItem("userID")}`
        }
    });
    const result = await response.json();

    if(response.ok){
        email.textContent = result.data.email;
        phone.textContent = result.data.phone;
        address.textContent = result.data.address;
        status.textContent = result.data.verification_status;
        limit.textContent = result.data.daily_limit;
    }
})

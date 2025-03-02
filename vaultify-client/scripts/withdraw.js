document.getElementById("withdrawForm").addEventListener("submit",async (e)=>{
    e.preventDefault();
    const walletID = document.getElementById("wallet_id").value.trim();
    const amount = document.getElementById("amount").value.trim();
    const message = document.getElementById("responseMessage");
    message.textContent = '';
    if(!walletID ||!amount){
        message.textContent = "All fields are required";
        return;
    }
    const withdrawURL=`${config.apiBaseUrl}/wallet/withdraw.php`;
    const response = await fetch(withdrawURL,{
        method:"POST",
        headers:{
            "Content-Type":"application/json",
            "Authorization": `Bearer ${localStorage.getItem("userID")}}`
        },
        body:JSON.stringify({wallet_id:walletID, amount})
    });
    const result = await response.json();
    if(response.ok){
        message.style.color = "green";
        message.textContent = result.message;
        setTimeout(()=>{
            window.location.href="./view_wallets.html";
        },2000);
        
    }else{
        message.style.color = "red";
        message.textContent = result.message;
        document.getElementById("depositForm").reset();
    }
})
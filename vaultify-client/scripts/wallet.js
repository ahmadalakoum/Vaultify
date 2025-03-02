document.getElementById("wallet-form").addEventListener("submit",async (e)=>{
    e.preventDefault();
    const wallet_name = document.getElementById("wallet-name").value.trim();
    const currency = document.getElementById("currency").value.trim();
    const message = document.getElementById("wallet-message");

    message.textContent = '';
    const userID=localStorage.getItem("userID");
    if(!wallet_name ||!currency){
        message.textContent = "All fields are required";
        return;
    }
    console.log(wallet_name,currency);
    const walletURL=`${config.apiBaseUrl}/wallet/create_wallet.php`;
    const response = await fetch(walletURL,{
        method:"POST",
        headers:{
            "Content-Type":"application/json",
            "Authorization": `Bearer ${localStorage.getItem("userID")}}`
        },
        body:JSON.stringify({wallet_name,currency})
    });
    const result = await response.json();
    if(response.ok){
        message.style.color = "green";
        message.textContent = result.message;
        window.location.href="../index.html";
    }else{
        message.style.color = "red";
        message.textContent = result.message;
    }
})
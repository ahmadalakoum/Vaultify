document.getElementById("transferForm").addEventListener("submit",async (e)=>{
    e.preventDefault();
    const urlParams = new URLSearchParams(window.location.search);

    // Get the wallet_id from the query string
    const walletID = urlParams.get('wallet_id');
    const receiverUsername= document.getElementById("receiverUsername").value.trim();
    const receiverWalletName= document.getElementById("wallet_name").value.trim();
    const amount = document.getElementById("amount").value.trim();

    const msg= document.getElementById("responseMessage");
    msg.textContent='';
    if(!receiverUsername ||!receiverWalletName ||!amount){
        msg.textContent="All fields are required";
        return;
    }
    const userID=localStorage.getItem("userID");
    const transferURL=`${config.apiBaseUrl}/wallet/internal_transfer.php?senderWalletID=${walletID}`;
    const response = await fetch(transferURL,{
        method:"POST",
        headers:{
            "Content-Type":"application/json",
            "Authorization": `Bearer ${localStorage.getItem("userID")}`
        },
        body:JSON.stringify({receiverUsername, receiverWalletName, amount})
    })
    const result = await response.json();
    if(response.ok){
        if(result.status === "success"){

        
        msg.style.color="green";
        msg.textContent=result.message;
        setTimeout(()=>{
            window.location.href="./view_wallets.html";
        },2000);
    }else{
        msg.style.color="red";
        msg.textContent=result.message;
        document.getElementById("depositForm").reset();
    }
    }
})
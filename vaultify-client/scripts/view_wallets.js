document.addEventListener("DOMContentLoaded",async (e)=>{
    const walletsList= document.getElementById("wallets-list");

    const response = await fetch(`${config.apiBaseUrl}/wallet/view_wallets.php`,{
        method:"GET",
        credentials:"include",
        headers:{
            "Content-Type":"application/json",
            "Authorization": `Bearer ${localStorage.getItem("userID")}`
        }
    })
    const result = await response.json();
    if(response.ok){
        result.wallets.forEach(wallet=>{
            const walletDiv = document.createElement("div");
                walletDiv.classList.add("wallet");

                walletDiv.innerHTML = `
                <span>id:${wallet.id}</span>
                    <h3>${wallet.wallet_name}</h3>
                    <p>Balance: ${wallet.balance} ${wallet.currency}</p>
                    <a href="deposit.html">Deposit</a>
                        <a href="withdraw.html">Withdraw</a>
                        <a href="transfer.html">Transfer</a>
                        <a href="history.html">Transactions</a>
                `;

                walletsList.appendChild(walletDiv);
        })
    }else{
        console.error("Error fetching wallets",result);
    }
})
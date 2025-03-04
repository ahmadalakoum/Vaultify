document.addEventListener("DOMContentLoaded", async () => {
    const transactionsList = document.getElementById("transactions-list");
    const filterDropdown = document.getElementById("filter");
    const urlParams = new URLSearchParams(window.location.search);
    const walletID = urlParams.get("wallet_id");
    
    const transactionHistoryURL = `${config.apiBaseUrl}/wallet/transaction_history.php?wallet_id=${walletID}`;
    
    async function fetchTransactions() {
        const response = await fetch(transactionHistoryURL, {
            method: "GET",
            credentials: "include",
            headers: {
                "Content-Type": "application/json",
                "Authorization": `Bearer ${localStorage.getItem("userID")}`,
            },
        });

        const result = await response.json();
        return result.transactions || [];
    }

    function displayTransactions(transactions, filter = "all") {
        transactionsList.innerHTML = ""; // Clear the list before adding new items

        const filteredTransactions = transactions.filter(transaction => 
            filter === "all" || transaction.type.toLowerCase() === filter
        );

        if (filteredTransactions.length === 0) {
            transactionsList.innerHTML = "<p>No transactions found.</p>";
            return;
        }

        filteredTransactions.forEach(transaction => {
            const transactionDiv = document.createElement("div");
            transactionDiv.classList.add("transaction");
            transactionDiv.innerHTML = `
                <span>Type: ${transaction.type}</span>
                <span>Amount: ${transaction.amount}</span>
                <span>Date: ${transaction.timestamp}</span>
            `;
            transactionsList.appendChild(transactionDiv);
        });
    }

    const transactions = await fetchTransactions();
    displayTransactions(transactions);

    // Update transactions when filter changes
    filterDropdown.addEventListener("change", () => {
        const selectedFilter = filterDropdown.value;
        displayTransactions(transactions, selectedFilter);
    });
});

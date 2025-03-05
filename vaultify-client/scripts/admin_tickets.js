document.addEventListener("DOMContentLoaded",async (e)=>{
    const role = localStorage.getItem("role");
    if(role!=="admin"){
        window.location.href = "../pages/login.html";
    }
    const view_ticketsURL= `${config.apiBaseUrl}/admin/tickets/view_tickets.php`;
    const response = await fetch(view_ticketsURL,{
        method:"GET",
        credentials:"include",
        headers:{
            "Content-Type":"application/json",
            "Authorization": `Bearer ${localStorage.getItem("userID")}`
        }
    });
    const result = await response.json();
    if(response.ok){
        if(result.status=== "success"){
            const tickets = result.tickets;
            const ticketsBody = document.getElementById("tickets-body");
            ticketsBody.innerHTML = "";
            tickets.forEach(ticket => {
                const row = `
                    <tr>
                        <td>${ticket.id}</td>
                        <td>${ticket.email}</td>
                        <td>${ticket.subject}</td>
                        <td>${ticket.description}</td>
                        <td>${ticket.status}</td>
                    </tr>
                `;
                ticketsBody.innerHTML += row;
            });
        }
    }

})
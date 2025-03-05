document.getElementById("ticket-form").addEventListener("submit",async(e)=>{
    e.preventDefault();
    const subject = document.getElementById("subject").value.trim();
    const description = document.getElementById("description").value.trim();
    const message = document.getElementById("responseMessage");
    message.textContent = '';
    if(!subject ||!description){
        message.textContent = "All fields are required";
        return;
    }
    const ticketURL=`${config.apiBaseUrl}/tickets/create_ticket.php`;
    const response = await fetch(ticketURL,{
        method:"POST",
        headers:{
            "Content-Type":"application/json",
            "Authorization": `Bearer ${localStorage.getItem("userID")}`
        },
        body:JSON.stringify({subject,description})
    });
    const result = await response.json();
    if(response.ok){
        if(result.status==="success"){
            message.style.color = "green";
            message.textContent = result.message;
            document.getElementById("ticket-form").reset();
            setTimeout(()=>{
                window.location.href="../index.html";
            },2000);
        }else{
            message.style.color = "red";
            message.textContent = result.message;
        }
    }
})
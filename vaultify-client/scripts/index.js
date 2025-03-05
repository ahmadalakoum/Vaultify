function updateNavbar() {
    const navLinks = document.getElementById("nav-links");
    const userProfile = document.getElementById("user-profile");
    const usernameElement = document.getElementById("username");
    const logoutBtn = document.getElementById("logout-btn");

    // Check if user is logged in by checking localStorage for userID
    const userID = localStorage.getItem("userID");
    const username = localStorage.getItem("username");
    console.log(userID, username);

    const joinBtn=document.getElementById("join");

    if (userID) {

        // User is logged in, display profile and logout button
        usernameElement.textContent = username;
        userProfile.style.display = "flex";
        navLinks.innerHTML = `
            <li><a href="/index.html">Home</a></li>
            <li><a href="/pages/create_wallet.html">New wallet</a></li>
            <li><a href="/pages/view_wallets.html">View wallets</a></li>
            <li><a href="/pages/profile.html">Profile</a></li>

        `;
        logoutBtn.style.display = "block";

        // Handle Logout Button Click
        logoutBtn.onclick = () => {
            localStorage.removeItem("userID");
            localStorage.removeItem("username");
            window.location.href = window.location.origin + "/pages/login.html"; // Redirect to login page
        };
    } else {
        // User is not logged in, show sign up/login links
        userProfile.style.display = "none";
        navLinks.innerHTML = `
            <li><a href="index.html">Home</a></li>
            <li><a href="./pages/signup.html">Sign Up</a></li>
            <li><a href="./pages/login.html">Login</a></li>
        `;
    }
}

// Call the function to update the navbar on page load
window.onload = updateNavbar;
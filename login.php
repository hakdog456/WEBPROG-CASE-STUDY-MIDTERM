<?php
session_start(); // Start session to store user info

// Connect to MySQL
$conn = new mysqli("localhost", "root", "", "adoptiondb");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = ""; // Initialize error message

// Check if form submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare statement to prevent SQL injection - UPDATED to include privilege
    $stmt = $conn->prepare("SELECT userId, name, username, password, privilege FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Check if email exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $name, $username, $hashed_password, $privilege);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $hashed_password)) {
            // Successful login
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $name;
            $_SESSION['username'] = $username;
            $_SESSION['privilege'] = $privilege; // Store privilege in session
            $_SESSION['login_success'] = true; // Flag for localStorage storage

            // Don't redirect here - let JavaScript handle it
            // header("Location: index.php");
            // exit();
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "No account found with that email.";
    }

    $stmt->close();
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login Page</title>
        <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;600&family=Slackey&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="globalFooterNav.css">
        <script defer src="global.js" ></script>
    <style>
      /*Global Styles*/
      * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
      }

      /* Body */
      body {
        position: relative;
        background-image: url(images/login-images/heather-green-1GuZ9y1qAT8-unsplash-3.webp);
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        display: flex;
        align-items: center;
        flex-direction: column;
        min-height: 100vh;
        padding: 1rem;
        font-size: 16px;
        padding-bottom: 230px;
        padding-top: 70px;
      }

      /* Main Frame*/
      .mainframe{
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        max-width: 100%;
        gap: 2rem;
        flex-wrap: nowrap;
        padding: 3vw;
      }
      .mobile-image{
        display: none;
      }

      .cat-image{
        flex: 0 1 auto;
        width: auto;
        max-width: 60rem;
        min-width: 35rem;
        height: auto;
      }

      .cat-image img{
        mix-blend-mode: overlay;
        width: 100%;
        height: 37rem;
        object-fit: contain;
        display: block;
      }

      form{
        display: flex;
        flex-direction: column;
        border: 0.125rem solid #725134;
        width: 37.8rem;
        height: 37rem;
        border-radius: 2rem;
        padding: 2rem;
        position: relative;
        flex: 0 1 auto;
      }

      form::before{
        content: "";
        position: absolute;
        inset: 0;
        background: rgba(68, 78, 39, 0.5);
        mix-blend-mode: overlay;
        border-radius: inherit;
        z-index: 0;
      }
      
      .maintext{
        margin-bottom: 2rem;
        position: relative;
        z-index: 1;
      }

      form h2{
        text-align: center;
        font-family: 'slackey';
        font-weight: 700;
        font-size: 2rem;
        margin-bottom: 0.5rem;
      }

      form p{
        font-family: 'chakra petch';
        font-size: 1.25rem;
        font-weight: 500;
      }

      .form-group{
        display: grid;
        grid-template-columns: 120px 1fr;
        align-items: center;
        position: relative;
        width: 100%;
        gap: 1rem;
        margin-bottom: 1.5rem;
        z-index: 1;
      }

      .form-group label{
        font-family: 'slackey';
        font-size: 1.25rem;
        font-weight: 600;
        min-width: max-content;
      }

      .form-group input{
        width: 100%;
        height: 3.56rem;
        border-radius: 3rem;
        padding: 0 1.5rem;
        padding-right: 3.5rem;
        border: 0.0625rem solid #444E27;
        background-color: #ffffff00;
        font-size: 1.25rem;
        font-family: 'chakra petch';
        color: #444E27;
      }

      .form-group input:-webkit-autofill,
      .form-group input:-webkit-autofill:hover,
      .form-group input:-webkit-autofill:focus,
      .form-group input:-webkit-autofill:active {
        -webkit-box-shadow: 0 0 0 30px transparent inset !important;
        -webkit-text-fill-color: #444E27 !important;
        transition: background-color 5000s ease-in-out 0s;
      }

      .form-group input::placeholder{
        color: #444E2780;
      }

      .form-group input:focus{
        outline: none;
        background-color: rgba(255, 255, 255, 0.05);
      }

      .form-group .eyeicon{
        position: absolute;
        right: 1.5rem;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        width: 1.5rem;
        height: 1.5rem;
        transition: opacity 0.3s ease;
        flex-shrink: 0;
      }

      .form-group .eyeicon:hover{
        opacity: 0.7;
      }

      .form-options{
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
        z-index: 1;
        width: 100%;
        font-size: 1.25rem;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.5rem;
        font-family: 'chakra petch';
      }

      .remember-me{
        display: flex;
        align-items: center;
        gap: 0.6rem;
      }

      .form-options input[type="checkbox"]{
        width: 1.25rem;
        height: 1.25rem;
        cursor: pointer;
        accent-color: #fafafa00;
      }

      .forgot-password a{
        color: #444E27;
        text-decoration: none;
        transition: opacity 0.3s ease;
      }

      .forgot-password a:hover{
        opacity: 0.7;
        text-decoration: underline;
      }

      .login-button{
        display: flex;
        justify-content: flex-end;
        margin-bottom: 4rem;
        position: relative;
        z-index: 1;
      }

      .login-button input{
        width: 12.5rem;
        height: 4.18rem;
        border-radius: 3rem;
        background-color: rgba(71, 50, 5, 0.27);
        color: #444E27;
        font-family: 'chakra petch';
        font-size: 1.25rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
      }

      .login-button input:hover{
        background-color: rgba(71, 50, 5, 0.55);
        transform: translateY(-0.125rem);
        box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.2);
      }

      .login-button input:active{
        transform: translateY(0);
        background-color: rgba(71, 50, 5, 0.65);
      }

      .signup-link{
        font-size: 1rem;
        font-weight: 500;
        position: absolute;
        bottom: 1.5rem;
        left: 2.5rem;
        font-family: 'chakra petch';
        z-index: 1;
      }

      .signup-link a{
        color: #444E27;
        font-weight: 600;
        text-decoration: none;
        transition: opacity 0.3s ease;
      }

      .signup-link a:hover{
        opacity: 0.7;
        text-decoration: underline;
      }

      /* Responsive Design */
      @media (max-width: 1200px) {
        .mainframe {
          flex-wrap: nowrap;
          gap: 1rem;
        }

        .cat-image {
          flex: 1 1 auto;
          width: auto;
          max-width: 60rem;
          min-width: 35rem;
          height: auto;
        }

        form {
          width: 37.8rem;
          min-width: 27.3rem;
          flex: 0 1 auto;
        }
      }
      @media (max-width: 1024px) {
        .mainframe {
          flex-direction: column;
          padding: 1.5rem;
        }

        .cat-image {
          display: none;
        }

        form {
          width: 90%;
          max-width: 28rem;
          height: auto;
          padding: 2rem;
          margin: 0 auto;
        }

        .form-group {
          grid-template-columns: 1fr;
          gap: 0.5rem;
          text-align: left;
        }

        .form-group label {
          font-size: 1rem;
        }

        .form-group input {
          font-size: 1rem;
          height: 3rem;
          padding-right: 3.5rem;
        }

        .form-group .eyeicon {
          right: 1rem;
          width: 1.25rem;
          height: 1.25rem;
          transform: translateY(20%);
        }

        .form-options {
          font-size: 0.875rem;
        }

        .login-button {
          justify-content: center;
          margin-bottom: 2rem;
        }

        .login-button input {
          width: 100%;
          max-width: 12rem;
        }

        .signup-link {
          position: static;
          margin-top: 1rem;
          text-align: center;
        }
      }

      @media (max-width: 768px) {
        .mainframe {
          flex-direction: column;
          padding: 1.5rem;
        }

        form {
          width: 28rem;
          max-width: 100%;
          min-width: unset;
          padding: 2rem;
          border-radius: 1.5rem;
          height: auto;
          margin-bottom: 1rem;
        }

        .form-group {
          grid-template-columns: 1fr;
          gap: 0.5rem;
          text-align: left;
        }

        .form-group label {
          margin-right: 0;
          font-size: 1rem;
        }

        .form-group input {
          font-size: 1rem;
          height: 3rem;
          padding-right: 3.5rem;
        }

        .form-group .eyeicon {
          right: 1rem;
          width: 1.25rem;
          height: 1.25rem;
        }

        .login-button {
          justify-content: center;
        }

        .login-button input {
          width: 100%;
          max-width: 12rem;
        }

        .signup-link {
          position: static;
          margin-top: 1rem;
          text-align: center;
        }
        .mobile-image {
          display: block;
          width: 33rem;
          max-width: 100%;
          margin-top: 0.3rem;
          
        }

        .mobile-image img {
          width: 100%;
          height: auto;
          object-fit: cover;
          border-radius: 1rem;
          mix-blend-mode: overlay;
        }
      }

      @media (max-width: 480px) {
        body{
          padding-bottom: 150px;
        }
        form {
          width: 90%;
          padding: 1.5rem;
          border-radius: 1.5rem;
        }

        .form-group input {
          font-size: 0.9rem;
          height: 2.8rem;
          padding: 0 1rem;
          padding-right: 3rem;
        }

        .form-group .eyeicon {
          right: 0.75rem;
          width: 1.125rem;
          height: 1.125rem;
        }

        form h2 {
          font-size: 1.5rem;
        }

        .form-options {
          flex-direction: column;
          align-items: flex-start;
        }

        .forgot-password {
          align-self: flex-end;
        }
      }
    </style>
    
    </head>
    <body>

          <!-- NAV BAR -->
      <div class="nav">
          <!-- Burger menu button for mobile -->
          <div class="burgerMenuCon" >
              <div class="burgerMenuBtn" >
                  <div></div>
                  <div></div>
                  <div></div>
              </div>
          </div>

          <div class="navLogo">
              <img src="images/homeImages/Jurassic Bark.webp" alt="">
          </div>
          <div class="mainNav">
              <p><a href="index.html">Home</a></p>
              <p> <a href="aboutUs.html">About Us</a></p>
              <p> <a href="ourAnimals.html">Our Animals</a></p>
              <p><a href="contact.html">Contact Us</a></p>
              <p><a href="faqs.html">FAQs</a></p>
          </div>
          <div class="navControls">
              <!-- Search SVG -->
              <svg class="searchIcon" width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M18.3333 31.6667C25.6971 31.6667 31.6667 25.6971 31.6667 18.3333C31.6667 10.9695 25.6971 5 18.3333 5C10.9695 5 5 10.9695 5 18.3333C5 25.6971 10.9695 31.6667 18.3333 31.6667Z" stroke="#223125" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M35 35L27.75 27.75" stroke="#223125" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>

              <!-- User Svg -->
              <svg class="userIcon" width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M33.3337 35V31.6667C33.3337 29.8986 32.6313 28.2029 31.381 26.9526C30.1308 25.7024 28.4351 25 26.667 25H13.3337C11.5655 25 9.86986 25.7024 8.61961 26.9526C7.36937 28.2029 6.66699 29.8986 6.66699 31.6667V35" stroke="#223125" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M19.9997 18.3333C23.6816 18.3333 26.6663 15.3486 26.6663 11.6667C26.6663 7.98477 23.6816 5 19.9997 5C16.3178 5 13.333 7.98477 13.333 11.6667C13.333 15.3486 16.3178 18.3333 19.9997 18.3333Z" stroke="#223125" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>

          </div>
      </div>

       <section class="mainframe">
        <div class="cat-image">
            <img src="images/login-images/Dogs image.webp" alt="catimage">
        </div>
        <form action="login.php" method="POST">
            <div class="maintext">
                <h2>Welcome Back!</h2>
                <p>Tails are wagging back in anticipation.</p>
            </div>

            <?php if (!empty($error)) : ?>
                <div class="error-message" style="color:red; margin-bottom:10px;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="text" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <svg class="eyeicon" id="eye" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path id="eyePath1" d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z" stroke="#444E27" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path id="eyePath2" d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="#444E27" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <line id="eyeSlash" x1="3" y1="3" x2="21" y2="21" stroke="#444E27" stroke-width="2" stroke-linecap="round" style="display: none;"/>
                </svg>
            </div>
            <div class="form-options">
                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember Me</label>
                </div>
                <div class="forgot-password">
                    <a href="#">Forgot Password?</a>
                </div>
            </div>
            <div class="login-button">
                <input type="submit" value="Login">
            </div>
            <div class="signup-link">
                <p>Don't have an account? <a href="sign-up.php">Sign Up</a></p>
            </div>
        </form>
        <div class="mobile-image">
            <img src="images/login-images/Dogs image (1).webp" alt="dogimage">
        </div>
    </section>

    <script>
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye');
        const eyeSlash = document.getElementById('eyeSlash');

        eyeIcon.addEventListener('click', function() {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeSlash.style.display = 'block';
            } else {
                passwordInput.type = 'password';
                eyeSlash.style.display = 'none';
            }
        });

        // ===== NEW: localStorage functionality =====
        
        // Check if user is already logged in on page load
        window.addEventListener('DOMContentLoaded', function() {
            const storedUser = localStorage.getItem('jurassicBark_user');
            
            if (storedUser) {
                const userData = JSON.parse(storedUser);
                // Redirect to home if already logged in
                window.location.href = 'index.php';
            }
        });
    </script>

    <?php if (isset($_SESSION['login_success']) && $_SESSION['login_success']): ?>
    <script>
        // Store user data in localStorage after successful login
        const userData = {
            userId: <?php echo $_SESSION['user_id']; ?>,
            name: "<?php echo addslashes($_SESSION['user_name']); ?>",
            username: "<?php echo addslashes($_SESSION['username']); ?>",
            privilege: "<?php echo $_SESSION['privilege']; ?>",
            loginTime: new Date().toISOString()
        };
        
        localStorage.setItem('jurassicBark_user', JSON.stringify(userData));
        
        // Redirect after storing
        window.location.href = 'index.php';
    </script>
    <?php 
        unset($_SESSION['login_success']); // Clear the flag
    ?>
    <?php endif; ?>

      <!-- FOOTER -->
     <div class="HomeFooter">
        <div class="footerBg" ></div>
        <div class="foot1" >
            <div class="footerLogoIcons" >
                <img class="footLogo" src="images/homeImages/Jurassic Bark.svg" alt="">
                <div class="smallIconsCon">
                    <img class="smallIcons" src="images/homeImages/facebook.svg" alt="">
                    <img class="smallIcons" src="images/homeImages/instagram.svg" alt="">
                    <img class="smallIcons" src="images/homeImages/tiktok.svg" alt="">            </div>
                </div>
            <div class="foot1Text" >
                © 2025 Jurassic Bark. All Rights Reserved.
                <br>
                Registered Nonstock, Nonprofit Organization – SEC Registration No. CN2025-XXXXXX
                <br>   
                Accredited under the Animal Welfare Act of 1998 (Republic Act No. 8485, as amended by RA 10631).
            </div>

        </div>

        <!-- bottom foot1 text for mobile -->
        <div class="bottomFoot1Text" >
            © 2025 Jurassic Bark. All Rights Reserved.
            <br>
            Registered Nonstock, Nonprofit Organization – SEC Registration No. CN2025-XXXXXX
            <br>   
            Accredited under the Animal Welfare Act of 1998 (Republic Act No. 8485, as amended by RA 10631).
        </div>
            
        <div class="foot2" >
            Home
            <br>
            About Us
            <br>
            Our Animals
            <br>
            Contact Us
            <br>
            FAQs
        </div>
        <div class="foot3" >
            Privacy Policy
            <br>
            Terms of Service
            <br>
            Adoption Policy
            <br>
            Licensing or nonprofit 
            <br>
            registration info
        </div>
        <div class="foot4" >
            Tarlac State University - San Isidro Campus, 
            <br>
            Brgy. San Isidro, Tarlac City, 2300 Tarlac
            <br>
            +63 912 345 6789
            <br>
            jurassicbark@gmail.com
            <br>
            MON to SAT, 10 AM - 6 PM
            <br>
            No Noon Time Break
        </div>
     </div>
    </body>
</html>
<?php
// Get pet ID from URL
$petId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Database connection and pet fetching
$pet = null;
$error = null;

if ($petId > 0) {
    try {
        // Database configuration
        $host = '127.0.0.1';
        $dbname = 'adoptiondb';
        $username = 'root';
        $password = '';
        
        // Create PDO connection
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Fetch specific pet from database
        $stmt = $pdo->prepare("SELECT petID, name, type, breed, age, price, details, imageDirectory FROM pets WHERE petID = :petId");
        $stmt->bindParam(':petId', $petId, PDO::PARAM_INT);
        $stmt->execute();
        
        $pet = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$pet) {
            $error = "Pet not found.";
        }
        
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
} else {
    $error = "Invalid pet ID.";
}

// Parse pet details if pet exists
$details = [];
$gender = 'Unknown';
$color = 'Unknown';
$personality = 'No description available';
$vaccinated = 'Unknown';
$neutered = 'Unknown';
$coatLength = 'Unknown';

if ($pet) {
    $details = json_decode($pet['details'], true);
    if ($details) {
        $gender = isset($details['gender']) ? $details['gender'] : 'Unknown';
        $color = isset($details['color']) ? $details['color'] : 'Unknown';
        $personality = isset($details['personality']) ? $details['personality'] : 'No description available';
        $vaccinated = isset($details['vaccinated']) ? ($details['vaccinated'] ? 'Yes' : 'No') : 'Unknown';
        $neutered = isset($details['spayed_neutered']) ? ($details['spayed_neutered'] ? 'Yes' : 'No') : 'Unknown';
        $coatLength = isset($details['coat_length']) ? $details['coat_length'] : 'Unknown';
    }
}

$ageText = $pet ? ($pet['age'] > 1 ? $pet['age'] . ' years old' : $pet['age'] . ' year old') : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jurrasic Bark | <?php echo $pet ? htmlspecialchars($pet['name']) : 'Pet Detail'; ?></title>
    <link rel="stylesheet" href="petDetails.css">
    <link rel="stylesheet" href="global-application-styles.css">
    <link rel="stylesheet" href="globalFooterNav.css">
    <script defer src="petDetails.js" ></script>
    <script defer src="global.js" ></script>
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
            <p> <a href="ourAnimals.php">Our Animals</a></p>
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

    <div class="navMobileSlide">
        <div class="navItem">
            <a href="index.html"><p>HOME</p></a>
        </div>
        
        <div class="navItem">
            <a href="aboutUs.html"><p>ABOUT US</p></a>
        </div>

        <div class="navItem">
            <a href="ourAnimals.php"><p>OUR ANIMALS</p></a>
        </div>

        <div class="navItem">
            <a href="contact.html"><p>CONTACT US</p></a>
        </div>

        <div class="navItem">
            <a href="faqs.html"><p>FAQs</p></a>
        </div>

    </div>    

    
    <!-- PET DETAILS -->
    <?php if ($error): ?>
        <div style="text-align: center; padding: 50px; width: 82%;">
            <h2>Error</h2>
            <p><?php echo htmlspecialchars($error); ?></p>
            <p><a href="ourAnimals.php" style="color: #223125; text-decoration: underline;">Back to Animals List</a></p>
        </div>
    <?php elseif ($pet): ?>
    <div class="petDetailsCon">
        <div class="petDetailsBg"></div>

        <div class="imageCon">
            <div class="imageHolder">
                <img class="petImage" 
                     src="<?php echo htmlspecialchars($pet['imageDirectory']); ?>" 
                     alt="<?php echo htmlspecialchars($pet['name']); ?>"
                     onerror="this.src='images/homeImages/petPics/default-pet.jpg'">        
            </div>
        </div>
        
        <div class="petContent">
            <div class="petText">
                <p class="name"><?php echo htmlspecialchars($pet['name']); ?></p>
                <p class="details">
                    <p class="petTypeText" style="text-align: center" >
                        <?php echo htmlspecialchars($pet['breed']); ?> | <?php echo $ageText; ?> | <?php echo htmlspecialchars($gender); ?>
                    </p>
                    <br>
                    Coat Length: <?php echo htmlspecialchars($coatLength); ?> <br> 
                    Color: <?php echo htmlspecialchars($color); ?> <br>
                    Vaccinated: <?php echo htmlspecialchars($vaccinated); ?> <br> 
                    Neutered/Spayed: <?php echo htmlspecialchars($neutered); ?>
                    <br><br>
                    <?php echo htmlspecialchars($personality); ?>
                    <br><br>
                    Adoption Fee: ₱<?php echo number_format($pet['price'], 2); ?>
                </p>
            </div>  

            <div class="petControls">
                <img class="heartIcon" src="images/ourAnimalsImages/heart icon.svg" alt="">
                <div class="adoption" >
                    <div class="adoptionBg"></div>
                    <p class="adoptionText">Considering <?php echo htmlspecialchars($pet['name']); ?> for adoption?</p>
                    <a href="user-application-page.html?petId=<?php echo $pet['petID']; ?>" class="adoptionBtn">Apply Now</a>
                    <div class="adoptionBtn">Read FAQs</div>
                </div>
            </div>
        </div>

        <!-- Bottom Pet Details Con For media Breakage -->
        <div id="bottomAdoption" class="adoption" >
            <div class="bottomAdoptionBg adoptionBg"></div>
            <p class="bottomAdoptionText adoptionText">Considering <?php echo htmlspecialchars($pet['name']); ?> for adoption?</p>
            <a href="user-application-page.html?petId=<?php echo $pet['petID']; ?>" class="bottomAdoptionBtn adoptionBtn">Apply Now</a>
            <div class="bottomAdoptionBtn adoptionBtn">Read FAQs</div>
        </div>
            
    </div>
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
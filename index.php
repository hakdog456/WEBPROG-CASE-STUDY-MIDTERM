

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="index.css">
    <script defer src="index.js" ></script>

    <!-- FONTS -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Archivo+Black&family=Chakra+Petch:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Noto+Sans+TC:wght@100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Archivo+Black&family=Chakra+Petch:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Noto+Sans+TC:wght@100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Slackey&display=swap');
    </style>
    
</head>
<body>

    <!-- Page Image Background -->
    <div class="homeBg1" ></div>
    <!-- <div class="homeBg2" ></div> -->
    <!-- <img  src="images/homeImages/home-bg-1.webp" alt=""> -->
    <!-- <img  src="images/homeImages/Home-Bg-2.webp" alt=""> -->

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
            <p><a href="index.php">Home</a></p>
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
            <a href="user-profile.html">
                <svg class="userIcon" width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M33.3337 35V31.6667C33.3337 29.8986 32.6313 28.2029 31.381 26.9526C30.1308 25.7024 28.4351 25 26.667 25H13.3337C11.5655 25 9.86986 25.7024 8.61961 26.9526C7.36937 28.2029 6.66699 29.8986 6.66699 31.6667V35" stroke="#223125" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M19.9997 18.3333C23.6816 18.3333 26.6663 15.3486 26.6663 11.6667C26.6663 7.98477 23.6816 5 19.9997 5C16.3178 5 13.333 7.98477 13.333 11.6667C13.333 15.3486 16.3178 18.3333 19.9997 18.3333Z" stroke="#223125" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>

        </div>
    </div>

    <div class="navMobileSlide">
        <div class="navItem">
            <a href="index.php"><p>HOME</p></a>
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

    <!-- MAIN CONTENT -->
    <!-- Home Back ground -->
    <div class="homeIntroImgBg" ></div>
    <div class="homeIntro" >
        <!-- Main Content -->
        <div class="mainContent">
            <img class="homeIntroLogo" src="images/3d Logo.png" alt="">
            <h1>
                EVERY TAIL
                <br>
                HAS A TALE
            </h1>
            <h2>Start yours today!</h2>
            <div class="startYoursBtn" >Meet Our Animals</div>
        </div>
        <!-- Blurry Branch Img -->
        <div class="blurryBranchCon">
            <img class="blurryBranchImg" src="images/homeImages/blury-branch.webp" alt="">
        </div>
    </div>


    <!-- OUT PETS -->
     <div class="ourPets">
        <!-- <div class="ourPetsHeading">
            <div class="ourPetsTitle"><h2>OUR PETS</h2></div>
            <div class="ourPetsViewMoreAnimalsBtn">View more animals</div>
        </div> -->

        <div class="ourPetsContent">
            <div class="btnCon">
                <button class="prevBtn" >&lt;</button>
            </div>
            <div class="carouselCon">
                <!-- <div class="petPic place1 ">
                    <img src="images/Bark-Twaine-BEAGLE 1.png" alt="">
                </div>
                <div class="petPic place2 ">
                    <img src="images/Nina-Tucker-GERMAN_SHEPHERD 1.png" alt="">
                </div>
                <div class="petPic place3 ">
                    <img src="images/Turing-POODLE 1.png" alt="">
                </div> -->
                <!-- <div class="petPic place3 next3"></div> -->
            </div>
            <div class="btnCon">
                <button class="nextBtn" >&gt;</button>
            </div>
        </div>
     </div>

    <!-- HOME INFORMATION -->
    <div class="homeInformation">

        <!-- Our Story -->
        <div class="ourStory">
            <div class="ourStoryimageHolder">
                <!-- side branch -->
                <img class="sideBranchImg" src="images/homeImages/side-branch.webp" alt="">
    
                <!-- Our Story Image Dogs -->
                <img class="ourStoryDogsImg" src="images/homeImages/ourStory-Image.webp" alt="">
            </div>
            <div class="ourStorycontentHolder">
                <h2>OUR STORY</h2>
                <p>Jurassic Bark began with a simple dream — to give every abandoned, neglected, or abused animal a second chance at life.
                    <br><br>
                    Founded in 2022 by a group of animal lovers in Tarlac City, we started as a small rescue initiative that helped stray dogs and cats, and eventually small animals and birds and fish to find loving homes. What began with a handful of volunteers and rescued pets has grown into a fully registered animal adoption center dedicated to saving lives every single day.
                    <br><br>
                    Our name, Jurassic Bark, reminds us of the timeless loyalty and love that animals offer — because no matter how the world changes, compassion for animals should never go extinct.
                    <br><br>
                    Today, we continue to rescue, rehabilitate, and rehome animals in need across the Philippines. Each wagging tail, healed paw, and successful adoption is a testament to what kindness, teamwork, and community can achieve.
                </p>
            </div>
        </div>

        <!-- Our Mission -->
        <div class="ourMission">
            <div class="ourMissionContentHolder">
                <h2>OUR MISSION</h2>
                <p>
                    At Jurassic Bark, our mission is to rescue, rehabilitate, and rehome animals in need, giving them the opportunity to live safe, happy, and healthy lives with loving families. 
                    <br><br>
                    We are dedicated to providing shelter, nourishment, and medical care for abandoned or abused animals, ensuring they receive the compassion and support they deserve. Beyond rescue, we strive to promote responsible pet ownership through education and community outreach, encouraging people to treat animals with kindness and respect.
                </p>
            </div>

            <div class="ourMissionImageHolder">
                <!-- Cat Image for Our Mission -->
                <img class="catImg" src="images/homeImages/cat-image.webp" alt="">

                <!-- Right Branch after cat -->
                <img class="rightBranchImg" src="images/homeImages/BranchRight.webp" alt="">

            </div>
            
        </div>
       
        <div class="adoptionProcess">

            <div class="adoptionProcessImageHolder">
                <!-- Adoption Process Image -->
                <img class="adoptionProcessImg" src="images/homeImages/Adoption-Process-Panel.webp" alt="">
            </div>

            <div class="adoptionProcessContent">
                <h2>ADOPTION PROCESS</h2>
                <p>
                    1. Find your future furry friend by browsing lovable profiles tailored to your lifestyle and home.
                    <br><br>
                    2. Tell us about your home and heart. our quick form helps match you with the perfect companion.
                    <br><br>
                    3. Once approved, your adoption fee covers vaccinations, vet care, and a fresh start for your new best friend.
                    <br><br>
                    4. Get ready to welcome your pet home with pickup instructions, starter tips, and a cozy checklist.
                    <br><br>
                    5. Stay connected with care guides, community support, and resources to help your bond grow stronger every day.
                </p>
            </div>
        </div>
        
        
    </div>

    <!-- CONTACT US -->
    <div class="contactUs">
        <div class="contactUsImageHolder" >
            <!-- Cat Contact US Picture -->
            <img class="contactUsCat" src="images/homeImages/cat.webp" alt="">
        </div>       

        <div class="contactUsContent" >
            <div class="contactUsForm">
                <div class="bg"></div>
                <p class="contactHead" >CONTACT US!</p>
                <div class="input">
                    <p>NAME: </p>
                    <input type="text">
                </div>
                <div class="input">
                    <p>EMAIL: </p>
                    <input type="text">
                </div>
                <div class="input">
                    <p>SUBJECT: </p>
                    <input type="text">
                </div>
                <div id="msgInput" class="input">
                    <p>MESSAGE: </p>
                    <textarea id="msgTxtArea" name="" id=""></textarea>
                </div>
                <div class="contactUsfooter" >
                    <p class="sideText" >or call 0912-345-6789</p>
                    <button class="sendMsgBtn" >Send Message</button>
                </div>
            </div>
        </div>
    </div>


    <!-- GRASS FOOTER -->
    <div class="grassFooter"></div>
    <img class="grassFooter" src="images/homeImages/grass-footer.webp" alt="">
     
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
                Registered Nonstock, Nonprofit Organization — SEC Registration No. CN2025-XXXXXX
                <br>   
                Accredited under the Animal Welfare Act of 1998 (Republic Act No. 8485, as amended by RA 10631).
            </div>

        </div>

        <!-- bottom foot1 text for mobile -->
        <div class="bottomFoot1Text" >
            © 2025 Jurassic Bark. All Rights Reserved.
            <br>
            Registered Nonstock, Nonprofit Organization — SEC Registration No. CN2025-XXXXXX
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

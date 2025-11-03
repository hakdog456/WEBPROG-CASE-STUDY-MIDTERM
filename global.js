
// Button Functions 

// NAV BUTTON FUNCTION
const navBurger = document.querySelector(".burgerMenuBtn")
const navSlide = document.querySelector(".navMobileSlide")

navBurger.addEventListener("click", () => {
  
  navSlide.classList.toggle("appear")

  navBurger.classList.toggle("burgerActive")
})
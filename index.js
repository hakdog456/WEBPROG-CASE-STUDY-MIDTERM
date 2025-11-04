let nextBtn = document.querySelector(".nextBtn")
let prevBtn = document.querySelector(".prevBtn")

// let place1 = document.querySelector(".place1")
// let place2 = document.querySelector(".place2")
// let place3 = document.querySelector(".place3")

let carouselCon = document.querySelector(".carouselCon")

let picSrcs = [
  "images/homeImages/petPics/Adolf-Kitler-PERSIAN 1.webp",
  "images/homeImages/petPics/Andarna-GUINEA_PIG 1.webp",
  "images/homeImages/petPics/Anthony-BOXER 1.webp",
  "images/homeImages/petPics/Bark-Twaine-BEAGLE 1.webp",
  "images/homeImages/petPics/Biscuit-MAINE-COON 1.webp",
  "images/homeImages/petPics/Blue-BORDER_COLLIE 1.webp",
  "images/homeImages/petPics/Bob-DUTCH_RABBIT 1.webp",
  "images/homeImages/petPics/Box-LABRADOR_RETRIEVER 1.webp",
  "images/homeImages/petPics/Broccoli-ORANGE-TABBY 1.webp",
  "images/homeImages/petPics/Bucchi-RAGDOLL 1.webp",
  "images/homeImages/petPics/Cheddar-BRITISH-SHORTHAIR 1.webp",
  "images/homeImages/petPics/Cheetos-BENGAL 1.webp",
  "images/homeImages/petPics/Cheetos-SYRIAN_HAMSTER 1.webp",
  "images/homeImages/petPics/Columbina-MINI_REX_RABBIT 1.webp",
  "images/homeImages/petPics/Coraline-ANGELFISH 1.webp",
  "images/homeImages/petPics/Cupcake-GUINEA_PIG_SILKIE 1.webp",
  "images/homeImages/petPics/Daisy-RAGDOLL 1.webp",
  "images/homeImages/petPics/Dobby-DEVON-REX 1.webp",
  "images/homeImages/petPics/Dory-BLUE_TANG 1.webp",
  "images/homeImages/petPics/Fin_Diesel-BETTA 1.webp",
  "images/homeImages/petPics/Gill-MOORISH_IDOL 1.webp",
  "images/homeImages/petPics/Gillbert-CLOWN_PLECO 1.webp",
  "images/homeImages/petPics/Gizmo-PAPILLON 1.webp",
  "images/homeImages/petPics/Gojo_Satoru-BETTA 1.webp",
  "images/homeImages/petPics/Goldeen-GOLDFISH_RANCHU 1.webp",
  "images/homeImages/petPics/Goldilocks-GOLDFISH 1.webp",
  "images/homeImages/petPics/Hiroshima-SHIBA_INU 1.webp",
  "images/homeImages/petPics/Iris-CORGI 1.webp",
  "images/homeImages/petPics/Jamie_Lannister-LION_FISH 1.webp",
  "images/homeImages/petPics/Javascript-GUPPY 1.webp",
  "images/homeImages/petPics/Joffrey-MALTESE 1.webp",
  "images/homeImages/petPics/Loki-NORWEGIAN-FOREST-CAT 1.webp",
  "images/homeImages/petPics/Lord-Voldemort-SIAMESE 1.webp",
  "images/homeImages/petPics/Luiz-FRENCH_BULLDOG 1.webp",
  "images/homeImages/petPics/Luke-MAINE-COON 1.webp",
  "images/homeImages/petPics/Luna-SIAMESE 1.webp",
  "images/homeImages/petPics/Lunch-BICHON_FRISE 1.webp",
  "images/homeImages/petPics/Magikarp-ZEBRA_DANIO 1.webp",
  "images/homeImages/petPics/Marshmallow-HOLLAND_LOP_RABBIT 1.webp",
  "images/homeImages/petPics/Mozart-GUINEA_PIG_TEDDY 1.webp",
  "images/homeImages/petPics/Mr-Sprinkles-EXOTIC-SHORTHAIR 1.webp",
  "images/homeImages/petPics/Nemo-CLOWNFISH 1.webp",
  "images/homeImages/petPics/Niggel-CHIHUAHUA 1.webp",
  "images/homeImages/petPics/Nina-Tucker-GERMAN_SHEPHERD 1.webp",
  "images/homeImages/petPics/Noodles-DOMESTIC-SHORTHAIR 1.webp",
  "images/homeImages/petPics/Nuggets-SYRIAN_HAMSTER 1.webp",
  "images/homeImages/petPics/Olaf-SHIH_TZU 1.webp",
  "images/homeImages/petPics/Paul-Atriedes-SIBERIAN_HUSKY 1.webp",
  "images/homeImages/petPics/Pebble-GUINEA_PIG_AMERICAN 1.webp",
  "images/homeImages/petPics/Piattos-GUINEA_PIG_PERUVIAN 1.webp",
  "images/homeImages/petPics/Pickles-SCOTTISH-FOLD 1.webp",
  "images/homeImages/petPics/Pikachu-KOI 1.webp",
  "images/homeImages/petPics/Pippin-ROBOROVSKI_HAMSTER 1.webp",
  "images/homeImages/petPics/Poppy-PERSIAN 1.webp",
  "images/homeImages/petPics/Poseidon-DISCUS 1.webp",
  "images/homeImages/petPics/Rachmaninov-MINI_LOP_RABBIT 1.webp",
  "images/homeImages/petPics/Salonpas-BOSTON_TERRIER 1.webp",
  "images/homeImages/petPics/Sardinas-KOI 1.webp",
  "images/homeImages/petPics/Sharkira-BALA_SHARK 1.webp",
  "images/homeImages/petPics/Sir-Waggington-GOLDEN_RETRIEVER 1.webp",
  "images/homeImages/petPics/Smeagol-NETHERLAND_DWARF_RABBIT 1.webp",
  "images/homeImages/petPics/Snowball-LIONHEAD_RABBIT 1.webp",
  "images/homeImages/petPics/Syntax-BETTA 1.webp",
  "images/homeImages/petPics/Taco-DOMESTIC-SHORTHAIR 1.webp",
  "images/homeImages/petPics/Tairn-DWARF_HAMSTER 1.webp",
  "images/homeImages/petPics/Tchaikovsky-DWARF_HAMSTER 1.webp",
  "images/homeImages/petPics/The_Rock-DRAWF_RABBIT 1.webp",
  "images/homeImages/petPics/Tiramisu-BURMESE 1.webp",
  "images/homeImages/petPics/Totoro-GOLDFISH 1.webp",
  "images/homeImages/petPics/Turing-POODLE 1.webp",
  "images/homeImages/petPics/Warhammer-ROBOROVSKI_HAMSTER 1.webp",
  "images/homeImages/petPics/Wiener-DASCHUND 1.webp"
]

let index = 0

function addPetPic(src){
  let div = document.createElement("div")
  let img = document.createElement("img")
  
  img.src = src
  div.append(img)
  
  div.classList.add("petPic")
  carouselCon.append(div)
}


// Creating Pet Pics and inserting img sources
for (let i = 0; i < picSrcs.length; i++){
  addPetPic(picSrcs[i])
}

// let pics = [place1, place2, place3]
let pics = []

let newPics = Array.from(carouselCon.children)

newPics.forEach(pic => {
  pics.push(pic)
})

console.log(pics)


function updatePositions() {
  pics.forEach((pic, i) => {
    pic.classList.remove("left", "center", "right", "hidden");
    if (i === index){
      pic.classList.add("center");
    } 
    else if (i === (index - 1 + pics.length) % pics.length){
      pic.classList.add("left");
    }
    else if (i === (index + 1) % pics.length){
      pic.classList.add("right");
    }
    else{
      pic.classList.add("hidden");
    }
  });
}

nextBtn.addEventListener("click", () => {
  index = (index + 1) % pics.length;
  updatePositions();
});

prevBtn.addEventListener("click", () => {
  index = (index - 1 + pics.length) % pics.length;
  updatePositions();
});

updatePositions();







// Button Functions 
const navBurger = document.querySelector(".burgerMenuBtn")
const navSlide = document.querySelector(".navMobileSlide")

navBurger.addEventListener("click", () => {
  
  navSlide.classList.toggle("appear")

  navBurger.classList.toggle("burgerActive")
})

const meetPetsBtn = document.querySelector(".startYoursBtn")
meetPetsBtn.addEventListener("click", () =>{
  window.location.href = "ourAnimals.html"
} )
let nextBtn = document.querySelector(".nextBtn")
let prevBtn = document.querySelector(".prevBtn")

let place1 = document.querySelector(".place1")
let place2 = document.querySelector(".place2")
let place3 = document.querySelector(".place3")

// console.log(place1, place2, place3)

let index = 0

let pics = [place1, place2, place3]

function updatePositions() {
  pics.forEach((pic, i) => {
    pic.classList.remove("left", "center", "right", "hidden");
    if (i === index) pic.classList.add("center");
    else if (i === (index - 1 + pics.length) % pics.length)
      pic.classList.add("left");
    else if (i === (index + 1) % pics.length)
      pic.classList.add("right");
    else pic.classList.add("hidden");
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






// for (let i = 0; i < pics.length; i++){
//     pics[i].counter = i
// }

// let action = "next"

// function removeAnims(element){
//     element.classList.remove("next0")
//     element.classList.remove("next1")
//     element.classList.remove("next2")
//     element.classList.remove("next3")
//     element.classList.remove("back0")
//     element.classList.remove("back1")
//     element.classList.remove("back2")
//     element.classList.remove("back3")
// }

// function forwards(){
//     for (let i = 0; i < pics.length; i++){
//         if (pics[i].counter == 0){
//             removeAnims(pics[i])
//             pics[i].classList.add("next0")
//         }
//         if (pics[i].counter == 1){
//             removeAnims(pics[i])
//             pics[i].classList.add("next1")
//         }
//         if (pics[i].counter == 2){
//             removeAnims(pics[i])
//             pics[i].classList.add("next2")
//         }
//         if (pics[i].counter == 3){
//             removeAnims(pics[i])
//             pics[i].classList.add("next3")
//             pics[i].counter = -1
//         }    
//         pics[i].counter++
//         console.log(pics[i].counter)
//     }
// }

//  function previous(){
//         for (let i = 0; i < pics.length; i++){
//             if (pics[i].counter == 0){
//                 removeAnims(pics[i])
//                 pics[i].classList.add("back0")
//                 pics[i].counter = 4
//             }
//             if (pics[i].counter == 1){
//                 removeAnims(pics[i])
//                 pics[i].classList.add("back1")
//             }
//             if (pics[i].counter == 2){
//                 removeAnims(pics[i])
//                 pics[i].classList.add("back2")
//             }
//             if (pics[i].counter == 3){
//                 removeAnims(pics[i])
//                 pics[i].classList.add("back3")
//             }    
//             pics[i].counter--
//             console.log(pics[i].counter)
//         }   
// }


// nextBtn.addEventListener("click", () => {
    

//     if (action != "next"){
//         previous()
//         action = "next" 
//     }

//     forwards()
// })

// prevBtn.addEventListener("click", () => {

   
//     if (action != "prev"){
//         forwards()
//         action = "prev"
//     }

//     previous()
// })


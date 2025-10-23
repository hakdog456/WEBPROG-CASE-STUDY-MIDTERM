let nextBtn = document.querySelector(".nextBtn")

let place1 = document.querySelector(".place1")
let place2 = document.querySelector(".place2")
let place3 = document.querySelector(".place3")

// console.log(place1, place2, place3)

let pics = [place1, place2, place3]

for (let i = 0; i < pics.length; i++){
    pics[i].counter = i
}

// let index = 0

nextBtn.addEventListener("click", () => {
    
    for (let i = 0; i < pics.length; i++){
        if (pics[i].counter == 0){
            pics[i].classList.remove("next3")
            pics[i].classList.add("next0")
        }
        if (pics[i].counter == 1){
            pics[i].classList.remove("next0")
            pics[i].classList.add("next1")
        }
        if (pics[i].counter == 2){
            pics[i].classList.remove("next1")
            pics[i].classList.add("next2")
        }
        if (pics[i].counter == 3){
            pics[i].classList.remove("next2")
            pics[i].classList.add("next3")
            pics[i].counter = -1
        }    
        pics[i].counter++
    }

})
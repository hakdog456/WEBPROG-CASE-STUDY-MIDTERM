// Fetch pets from ourAnimals.php and render dynamically
let allPets = [];

document.addEventListener('DOMContentLoaded', () => {
	const petsList = document.getElementById('petsList');
	const searchBar = document.querySelector('.searchBar');
	let searchInput = searchBar.querySelector('input');
	if (!searchInput) {
		searchInput = document.createElement('input');
		searchInput.type = 'text';
		searchInput.placeholder = 'Search pets...';
		searchInput.style = 'border:none;outline:none;background:transparent;width:90%;font-size:1.1em;padding:0.5em;';
		searchBar.appendChild(searchInput);
	}

	fetch('ourAnimals.php?json=1')
		.then(res => res.json())
		.then(pets => {
			allPets = pets;
			renderPets(pets);
		})
		.catch(() => {
			petsList.innerHTML = '<p>Failed to load pets.</p>';
		});

	searchInput.addEventListener('input', (e) => {
		const query = e.target.value.trim().toLowerCase();
		if (!query) {
			renderPets(allPets);
			return;
		}
		const filtered = allPets.filter(pet => {
			let details = {};
			if (pet.details) {
				try { details = JSON.parse(pet.details); } catch {}
			}
			const gender = details.gender || '';
			const text = [pet.name, pet.type, pet.breed, gender, pet.age, pet.price, pet.imageDirectory]
				.map(x => String(x).toLowerCase()).join(' ');
			return text.includes(query);
		});
		renderPets(filtered);
	});

	function renderPets(pets) {
		petsList.innerHTML = '';
		pets.forEach(pet => {
			let details = {};
			if (pet.details) {
				try { details = JSON.parse(pet.details); } catch {}
			}
			const gender = details.gender || 'Unknown';
			const ageText = pet.age > 1 ? `${pet.age} yrs. old` : `${pet.age} yr. old`;
			const petCard = document.createElement('div');
			petCard.className = 'petCard';
			petCard.innerHTML = `
				<div class="petCardBg"></div>
				<div class="petPicCon">
					<img src="${pet.imageDirectory}" alt="${pet.name}">
				</div>
				<div class="petDetails">
					<p class="petName">${pet.name}</p>
					<p class="petGender">${gender}</p>
					<p class="petType">${pet.breed}, ${ageText}</p>
				</div>
				<div class="buttons">
					<img class="heartBtn" src="images/ourAnimalsImages/heart icon.svg" alt="">
					<a href="petDetails.html">
						<div class="knowMoreBtn"><p>Know More</p></div>
					</a>
				</div>
			`;
			// Heart button functionality
			const heartBtn = petCard.querySelector('.heartBtn');
			if (heartBtn) {
				heartBtn.addEventListener('click', (e) => {
					e.preventDefault();
					e.stopPropagation();
					toggleLike(pet, heartBtn);
				});
				// Check if pet is already liked
				checkIfLiked(pet, heartBtn);
			}
			// Know More button click
			const knowMoreBtn = petCard.querySelector('.knowMoreBtn');
			if (knowMoreBtn) {
				knowMoreBtn.addEventListener('click', (e) => {
					e.preventDefault();
					localStorage.setItem('petID', pet.petId || pet.petID || pet.petID);
					window.location.href = 'petDetails.html';
				});
			}
			petsList.appendChild(petCard);
		});
	}

	function getUserId() {
		let username = localStorage.getItem('username');
		let userId = localStorage.getItem('userId');
		console.log('==== getUserId ====');
		console.log('username from localStorage:', username);
		console.log('userId from localStorage:', userId);
		
		// Check for jurassicBark_user JSON
		if (!username || !userId) {
			const jurassicBarkUser = localStorage.getItem('jurassicBark_user');
			console.log('jurassicBark_user found:', !!jurassicBarkUser);
			if (jurassicBarkUser) {
				try {
					const user = JSON.parse(jurassicBarkUser);
					console.log('Parsed jurassicBark_user:', user);
					username = user.username;
					userId = user.userId;
					console.log('✅ Resolved from jurassicBark_user - username:', username, 'userId:', userId);
				} catch (e) {
					console.error('❌ Failed to parse jurassicBark_user:', e);
				}
			}
		}
		
		console.log('Final result - username:', username, 'userId:', userId);
		return { username, userId: parseInt(userId) || 0 };
	}

	function checkIfLiked(pet, heartBtn) {
		const { username, userId } = getUserId();
		console.log('==== checkIfLiked ====');
		console.log('pet:', pet);
		console.log('userId:', userId);
		console.log('username:', username);
		if (!userId && !username) {
			console.log('❌ No userId or username found, returning early');
			return;
		}
		
		const petId = pet.petId || pet.petID;
		console.log('petId for check:', petId);
		const params = new URLSearchParams({
			action: 'check',
			petId: petId,
			...(userId && { userId: userId }),
			...(username && !userId && { username: username })
		});
		console.log('Fetching:', 'manage-liked-pet.php?' + params);
		fetch('manage-liked-pet.php?' + params)
			.then(res => {
				console.log('Response status:', res.status);
				return res.json();
			})
			.then(data => {
				console.log('Response data:', data);
				if (data.liked) {
					console.log('✅ Pet is liked, adding class');
					heartBtn.classList.add('liked');
					heartBtn.classList.remove('unliked');
				} else {
					console.log('❌ Pet is not liked');
				}
			})
			.catch(err => {
				console.error('❌ Error checking if liked:', err);
			});
	}

	function toggleLike(pet, heartBtn) {
		const { username, userId } = getUserId();
		console.log('==== toggleLike ====');
		console.log('pet:', pet);
		console.log('userId:', userId);
		console.log('username:', username);
		
		if (!userId && !username) {
			console.error('❌ No userId or username found! Alert: Please log in');
			alert('Please log in to like pets.');
			return;
		}

		const isLiked = heartBtn.classList.contains('liked');
		const action = isLiked ? 'remove' : 'add';
		
		const petId = pet.petId || pet.petID;
		console.log('petId:', petId);
		console.log('isLiked (has class):', isLiked);
		console.log('action:', action);
		
		const params = new URLSearchParams({
			action: action,
			petId: petId,
			...(userId && { userId: userId }),
			...(username && !userId && { username: username })
		});

		console.log('Fetching:', 'manage-liked-pet.php?' + params);
		fetch('manage-liked-pet.php?' + params)
			.then(res => {
				console.log('Response status:', res.status);
				console.log('Response headers:', res.headers);
				return res.text();
			})
			.then(text => {
				console.log('Raw response text:', text);
				try {
					const data = JSON.parse(text);
					console.log('Response data:', data);
					if (data.success) {
						console.log('✅ Successfully toggled like');
						heartBtn.classList.remove('liked', 'unliked');
						if (data.liked) {
							console.log('Adding liked class');
							heartBtn.classList.add('liked');
						} else {
							console.log('Adding unliked class');
							heartBtn.classList.add('unliked');
						}
					} else {
						console.error('❌ Server returned success=false:', data);
					}
				} catch (e) {
					console.error('❌ Failed to parse JSON:', e);
					console.error('Raw text was:', text);
				}
			})
			.catch(err => console.error('❌ Error toggling like:', err));
	}
});

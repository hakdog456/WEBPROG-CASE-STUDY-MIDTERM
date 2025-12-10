// User Profile Pet Cards - Dynamic Rendering
document.addEventListener('DOMContentLoaded', () => {
	console.log('====== User Profile JS Loaded ======');
	
	const { username, userId } = getUserId();
	console.log('User ID:', userId);
	console.log('Username:', username);

	if (!userId && !username) {
		console.error('❌ No user found in localStorage');
		document.querySelector('.pet-items').innerHTML = '<p style="text-align:center; padding:20px;">Please log in to view your pets.</p>';
		return;
	}

	// Fetch user's pets
	const params = new URLSearchParams();
	if (userId) params.append('userId', userId);
	if (username) params.append('username', username);

	fetch('get-user-pets.php?' + params)
		.then(res => res.json())
		.then(data => {
			console.log('User pets data:', data);
			if (data.success && data.pets.length > 0) {
				renderPetCards(data.pets);
			} else {
				document.querySelector('.pet-items').innerHTML = '<p style="text-align:center; padding:20px;">No pets found.</p>';
			}
		})
		.catch(err => {
			console.error('❌ Error fetching user pets:', err);
			document.querySelector('.pet-items').innerHTML = '<p style="text-align:center; padding:20px; color:red;">Error loading pets.</p>';
		});

	function getUserId() {
		console.log('==== getUserId ====');
		let username = localStorage.getItem('username');
		let userId = localStorage.getItem('userId');
		
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
		
		return { username, userId };
	}

	function renderPetCards(pets) {
		console.log('==== renderPetCards ====');
		console.log('Rendering', pets.length, 'pet cards');
		
		const petItemsContainer = document.querySelector('.pet-items');
		petItemsContainer.innerHTML = ''; // Clear existing hardcoded cards

		pets.forEach((pet, index) => {
			let details = {};
			if (pet.details) {
				try {
					details = JSON.parse(pet.details);
				} catch (e) {
					console.warn('Failed to parse details for pet:', pet.petId);
				}
			}

			const gender = details.gender || 'Unknown';
			const ageText = pet.age > 1 ? `${pet.age} yrs. old` : `${pet.age} yr. old`;
			
			// Determine button text and navigation based on pet source and transaction status
			let buttonText = 'Know More';
			let navigationUrl = '';
			
			if (pet.petSource === 'liked') {
				buttonText = 'Know More';
				navigationUrl = 'petDetails.html';
			} else if (pet.petSource === 'adopted') {
				// Adopted pets: can have status "Paid", "Paid - Approved", "Adopted-Final"
				buttonText = 'Adopted';
				navigationUrl = 'user-adopted.html';
			} else if (pet.petSource === 'transaction') {
				// Transaction pets: check status
				const status = pet.transactionStatus || 'Unknown';
				
				if (status === 'Application Placed' || status === 'Application Approved') {
					buttonText = 'Pending';
					navigationUrl = 'user-application-page.html';
				} else if (status === 'Meet and Greet Scheduled') {
					buttonText = 'Meet and Greet';
					navigationUrl = 'user-application-page.html';
				} else if (status === 'Ready for Adoption') {
					buttonText = 'Payment';
					navigationUrl = 'user-application-page.html';
				} else {
					buttonText = status;
					navigationUrl = 'user-application-page.html';
				}
			}

			const petCard = document.createElement('article');
			petCard.className = 'pet-card';
			petCard.setAttribute('role', 'article');
			petCard.innerHTML = `
				<img src="${pet.imageDirectory}" alt="${pet.name}" class="pet-img">
				<div class="pet-row">
					<span>${pet.name}</span>
					<span>${gender}</span>
				</div>
				<div class="pet-breed-age">${pet.breed}, ${ageText}</div>
				<div class="pet-actions">
					<div class="heart" aria-hidden="true">
						<img class="heart-icon" src="svgs/heart.svg" alt="Like">
					</div>
					<button class="pet-btn" data-pet-id="${pet.petId}" data-nav-url="${navigationUrl}">${buttonText}</button>
				</div>
			`;

			// Heart button functionality
			const heartIcon = petCard.querySelector('.heart-icon');
			if (heartIcon) {
				heartIcon.addEventListener('click', (e) => {
					e.preventDefault();
					e.stopPropagation();
					toggleLike(pet, heartIcon);
				});

				// Check if already liked
				checkIfLiked(pet, heartIcon);
			}

			// Pet button navigation
			const petBtn = petCard.querySelector('.pet-btn');
			if (petBtn) {
				petBtn.addEventListener('click', (e) => {
					e.preventDefault();
					e.stopPropagation();
					const petId = petBtn.getAttribute('data-pet-id');
					const navUrl = petBtn.getAttribute('data-nav-url');
					
					console.log('==== Pet Button Clicked ====');
					console.log('petId:', petId);
					console.log('Navigation URL:', navUrl);
					
					// Set petId in localStorage
					localStorage.setItem('petID', petId);
					console.log('✅ Set petID in localStorage:', petId);
					
					// Also store selectedPet for potential fallback
					const selectedPet = pets.find(p => p.petId == petId);
					if (selectedPet) {
						localStorage.setItem('selectedPet', JSON.stringify(selectedPet));
						console.log('✅ Set selectedPet in localStorage');
					}
					
					// Navigate
					if (navUrl) {
						console.log('Navigating to:', navUrl);
						window.location.href = navUrl;
					}
				});
			}

			petItemsContainer.appendChild(petCard);
		});

		console.log('✅ Pet cards rendered');
	}

	function checkIfLiked(pet, heartIcon) {
		const { username, userId } = getUserId();
		const petId = pet.petId;

		if (!userId && !username) {
			console.log('No user ID - skipping like check');
			return;
		}

		const params = new URLSearchParams({
			action: 'check',
			petId: petId,
			...(userId && { userId: userId }),
			...(username && !userId && { username: username })
		});

		fetch('manage-liked-pet.php?' + params)
			.then(res => res.text())
			.then(text => {
				console.log('Check like response:', text);
				try {
					const data = JSON.parse(text);
					if (data.liked) {
						console.log('✅ Pet is liked, adding class');
						heartIcon.classList.add('liked');
						heartIcon.classList.remove('unliked');
					} else {
						console.log('❌ Pet is not liked');
					}
				} catch (e) {
					console.error('❌ Failed to parse JSON:', e);
				}
			})
			.catch(err => {
				console.error('❌ Error checking if liked:', err);
			});
	}

	function toggleLike(pet, heartIcon) {
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

		const isLiked = heartIcon.classList.contains('liked');
		const action = isLiked ? 'remove' : 'add';

		const petId = pet.petId;
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
						heartIcon.classList.remove('liked', 'unliked');
						if (data.liked) {
							console.log('Adding liked class');
							heartIcon.classList.add('liked');
						} else {
							console.log('Adding unliked class');
							heartIcon.classList.add('unliked');
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

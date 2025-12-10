(() => {
	console.log('==== load-pet-header.js ====');
	
	// Get petID from localStorage
	let petID = localStorage.getItem('petID');
	console.log('petID from localStorage:', petID);
	
	if (!petID) {
		console.error('❌ No petID found in localStorage');
		return;
	}

	// Try to get pet from selectedPet first (already cached)
	let pet = null;
	const selectedPetStr = localStorage.getItem('selectedPet');
	if (selectedPetStr) {
		try {
			pet = JSON.parse(selectedPetStr);
			if (pet.petId != petID && pet.petID != petID) {
				pet = null; // Wrong pet, need to fetch
			} else {
				console.log('✅ Using cached selectedPet:', pet);
			}
		} catch (e) {
			console.error('❌ Failed to parse selectedPet:', e);
		}
	}

	// If no cached pet or doesn't match, fetch all pets and find it
	if (!pet) {
		console.log('Fetching pets from API...');
		fetch('ourAnimals.php?json=1')
			.then(res => res.json())
			.then(pets => {
				console.log('Fetched pets:', pets);
				pet = pets.find(p => p.petId == petID || p.petID == petID);
				if (pet) {
					console.log('✅ Found pet:', pet);
					populatePetHeader(pet);
				} else {
					console.error('❌ Pet not found with ID:', petID);
				}
			})
			.catch(err => console.error('❌ Error fetching pets:', err));
	} else {
		// Use cached pet
		populatePetHeader(pet);
	}

	function populatePetHeader(pet) {
		console.log('Populating pet header with:', pet);
		
		const petHeader = document.querySelector('.pet-header');
		if (!petHeader) {
			console.error('❌ .pet-header element not found');
			return;
		}

		// Parse details if it's a string
		let details = {};
		if (pet.details) {
			try {
				details = JSON.parse(pet.details);
			} catch (e) {
				console.error('❌ Failed to parse pet details:', e);
			}
		}

		const gender = details.gender || 'Unknown';
		const ageText = pet.age > 1 ? `${pet.age} yrs. old` : `${pet.age} yr. old`;

		console.log('Gender:', gender, 'Age:', ageText);

		// Find or create pet-image div
		let petImageDiv = petHeader.querySelector('.pet-image');
		if (!petImageDiv) {
			petImageDiv = document.createElement('div');
			petImageDiv.className = 'pet-image';
			petImageDiv.style.cssText = 'width: 250px !important; height: 250px !important; border-radius: 20px; overflow: hidden; flex-shrink: 0; min-width: 250px !important; max-width: 250px !important; min-height: 250px !important; max-height: 250px !important;';
			petHeader.insertBefore(petImageDiv, petHeader.firstChild);
		}
		
		// Update or create image
		let img = petImageDiv.querySelector('img');
		if (!img) {
			img = document.createElement('img');
			img.style.cssText = 'width: 250px !important; height: 250px !important; object-fit: cover; display: block; border-radius: 20px;';
			petImageDiv.appendChild(img);
		}
		img.src = pet.imageDirectory;
		img.alt = pet.name;
		console.log('✅ Image updated:', pet.imageDirectory);

		// Find or create pet-info div
		let petInfoDiv = petHeader.querySelector('.pet-info');
		if (!petInfoDiv) {
			petInfoDiv = document.createElement('div');
			petInfoDiv.className = 'pet-info';
			petInfoDiv.style.cssText = 'flex: 1; display: flex; flex-direction: column; justify-content: center;';
			petHeader.appendChild(petInfoDiv);
		}

		// Clear and update pet info content
		petInfoDiv.innerHTML = `
			<h2>${pet.name}</h2>
			<p>${pet.breed}</p>
			<p>${ageText}</p>
			<p>${gender}</p>
		`;
		console.log('✅ Pet info updated');

		// Update document title
		document.title = `Meet and Greet - ${pet.name}`;
		console.log('✅ Document title updated');
	}
})();

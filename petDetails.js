(() => {
	const petImage = document.querySelector('.petImage');
	const nameEl = document.querySelector('.name');
	const metaEl = document.querySelector('.petTypeText');
	const detailsEl = document.getElementById('petDetailsCopy');
	const adoptionPrompt = document.getElementById('adoptionPrompt');
	const bottomAdoptionPrompt = document.getElementById('bottomAdoptionPrompt');
	const applyBtn = document.getElementById('applyBtn');
	const bottomApplyBtn = document.getElementById('bottomApplyBtn');
	const heartIcon = document.querySelector('.heartIcon');

	const setApplyTargets = (petId) => {
		const href = `user-application-page.html?petId=${petId}`;
		[applyBtn, bottomApplyBtn].forEach((btn) => {
			if (!btn) return;
			btn.href = href;
			btn.onclick = () => {
				localStorage.setItem('petID', String(petId));
				return true;
			};
		});
	};

	const formatMeta = (pet) => {
		const parts = [];
		if (pet.breed) parts.push(pet.breed);
		if (pet.age !== undefined && pet.age !== null) parts.push(`${pet.age} yrs old`);
		if (pet.detailsDecoded?.gender) parts.push(pet.detailsDecoded.gender);
		return parts.join(' | ');
	};

	const formatDetails = (pet) => {
		const d = pet.detailsDecoded || {};
		const lines = [];
		if (d.coat_length) lines.push(`Coat Length: ${d.coat_length}`);
		if (d.vaccinated !== undefined) lines.push(`Vaccinated: ${d.vaccinated ? 'Yes' : 'No'}`);
		if (d.spayed_neutered !== undefined) lines.push(`Spayed/Neutered: ${d.spayed_neutered ? 'Yes' : 'No'}`);
		if (d.color) lines.push(`Color: ${d.color}`);
		if (d.personality) lines.push('', d.personality);
		return lines.filter(Boolean).join('\n');
	};

	const getUserId = () => {
		let username = localStorage.getItem('username');
		let userId = localStorage.getItem('userId');
		console.log('==== getUserId (petDetails) ====');
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
	};

	const checkIfLiked = (petId) => {
		const { username, userId } = getUserId();
		console.log('==== checkIfLiked (petDetails) ====');
		console.log('petId:', petId);
		console.log('userId:', userId);
		console.log('username:', username);
		console.log('heartIcon exists:', !!heartIcon);
		if ((!userId && !username) || !heartIcon) {
			console.log('❌ Early return: no userId/username or heartIcon');
			return;
		}
		
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
					heartIcon.classList.add('liked');
					heartIcon.classList.remove('unliked');
				} else {
					console.log('❌ Pet is not liked');
				}
			})
			.catch(err => {
				console.error('❌ Error checking if liked:', err);
			});
	};

	const toggleLike = (petId) => {
		const { username, userId } = getUserId();
		console.log('==== toggleLike (petDetails) ====');
		console.log('petId:', petId);
		console.log('userId:', userId);
		console.log('username:', username);
		console.log('heartIcon exists:', !!heartIcon);
		
		if (!userId && !username) {
			console.error('❌ No userId or username found! Alert: Please log in');
			alert('Please log in to like pets.');
			return;
		}

		const isLiked = heartIcon.classList.contains('liked');
		const action = isLiked ? 'remove' : 'add';
		
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
	};

	const renderPet = (pet) => {
		if (nameEl) nameEl.textContent = pet.name || 'Pet Details';
		if (petImage && pet.imageDirectory) {
			petImage.src = pet.imageDirectory;
		}
		if (metaEl) metaEl.textContent = formatMeta(pet);
		if (detailsEl) detailsEl.textContent = formatDetails(pet);
		if (adoptionPrompt) adoptionPrompt.textContent = `Considering ${pet.name || 'this pet'} for adoption?`;
		if (bottomAdoptionPrompt) bottomAdoptionPrompt.textContent = `Considering ${pet.name || 'this pet'} for adoption?`;
		setApplyTargets(pet.petId);
		localStorage.setItem('petID', String(pet.petId));
		
		// Setup heart functionality
		if (heartIcon) {
			heartIcon.style.cursor = 'pointer';
			heartIcon.addEventListener('click', (e) => {
				e.preventDefault();
				e.stopPropagation();
				toggleLike(pet.petId);
			});
			checkIfLiked(pet.petId);
		}
	};

	const getPetId = () => {
		const fromQuery = new URLSearchParams(window.location.search).get('petId');
		if (fromQuery && !Number.isNaN(Number(fromQuery))) return Number(fromQuery);
		const fromStorage = localStorage.getItem('petID');
		if (fromStorage && !Number.isNaN(Number(fromStorage))) return Number(fromStorage);
		return null;
	};

	const findPet = (petId, cachedPet) => {
		if (cachedPet && Number(cachedPet.petId) === Number(petId)) return cachedPet;
		const stored = localStorage.getItem('selectedPet');
		if (stored) {
			try {
				const parsed = JSON.parse(stored);
				if (Number(parsed.petId) === Number(petId)) return parsed;
			} catch (e) {
				console.warn('Could not parse stored pet');
			}
		}
		return null;
	};

	const fetchPet = async (petId) => {
		try {
			const res = await fetch('get-all-pets.php');
			const data = await res.json();
			if (data.success && Array.isArray(data.pets)) {
				return data.pets.find(p => Number(p.petId) === Number(petId)) || null;
			}
		} catch (e) {
			console.error('Failed to fetch pets:', e);
		}
		return null;
	};

	const init = async () => {
		const petId = getPetId();
		if (!petId) {
			console.error('No petId found');
			return;
		}

		let pet = findPet(petId);
		if (!pet) {
			pet = await fetchPet(petId);
		}

		if (!pet) {
			console.error('Pet not found');
			return;
		}

		renderPet(pet);
	};

	document.addEventListener('DOMContentLoaded', init);
})();

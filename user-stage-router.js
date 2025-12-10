(() => {
	// Get petID and username from localStorage
	let petID = localStorage.getItem('petID');
	let username = localStorage.getItem('username');
	
	// If username not found, try to get it from jurassicBark_user JSON
	if (!username) {
		const jurassicBarkUser = localStorage.getItem('jurassicBark_user');
		if (jurassicBarkUser) {
			try {
				const user = JSON.parse(jurassicBarkUser);
				username = user.username;
				console.log('[user-stage-router] Got username from jurassicBark_user:', username);
			} catch (e) {
				console.error('[user-stage-router] Failed to parse jurassicBark_user:', e);
			}
		}
	}
	
	console.log('[user-stage-router] init', { petID, username });

	// If either is missing, redirect to application page as default
	if (!petID || !username) {
		window.location.href = 'user-application-page.html';
		console.warn('Missing petID or username in localStorage, redirecting to application page');
		return;
	}

	// Get current page filename
	const currentPage = window.location.pathname.split('/').pop() || 'index.html';
	console.log('[user-stage-router] currentPage', currentPage);

	// Fetch the transaction data from the backend
	const findUserTransaction = async () => {
		try {
			// Call a PHP endpoint to find the transaction
			const response = await fetch('get-transaction-by-pet-user.php', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify({
					petID: Number(petID),
					username: username
				})
			});
			console.log('[user-stage-router] fetch status', response.status);

			const data = await response.json();
			console.log('[user-stage-router] response data', data);

			// If no transaction found, go to application page
			if (!data || !data.transaction) {
				console.warn('[user-stage-router] no transaction found, redirecting to application');
				// Only redirect if not already on the target page
				if (currentPage !== 'user-application-page.html') {
					window.location.href = 'user-application-page.html';
				}
				return;
			}

			const transaction = data.transaction;
			const status = transaction.status?.trim();
			let targetPage = 'user-application-page.html';
			console.log('[user-stage-router] transaction status', status);

			// Determine target page based on status
			if (status === "Application Placed" || status === 'Application Approved' || status === 'Meet and Greet Scheduled') {
				targetPage = 'meet-greet-page.html';
			} else if (status === 'Ready for Adoption') {
				targetPage = 'payment-page.html';
			} else if (status === 'Adopted-Final' || status === "Paid" || status === "Paid - Approved") {
				targetPage = 'user-adopted.html';
			}

			console.log('[user-stage-router] targetPage', targetPage, 'currentPage', currentPage);

			// Only redirect if not already on the target page
			if (currentPage !== targetPage) {
				console.log('[user-stage-router] redirecting to', targetPage);
				window.location.href = targetPage;
			}
		} catch (err) {
			console.error('Error finding transaction:', err);
			// On error, go to application page as default (if not already there)
			if (currentPage !== 'user-application-page.html') {
				window.location.href = 'user-application-page.html';
			}
		}
	};

	// Execute the routing logic
	findUserTransaction();
})();

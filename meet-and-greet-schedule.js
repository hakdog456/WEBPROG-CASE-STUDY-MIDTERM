(() => {
	let currentTransaction = null;
	const transactionId = new URLSearchParams(window.location.search).get('transactionId');

	const dateInput = document.getElementById('meet-date');
	const timeInput = document.getElementById('meet-time');
	const locationInput = document.getElementById('meet-location');
	const messageInput = document.getElementById('meet-message');
	const submitBtn = document.querySelector('.btn-primary');

	if (!dateInput || !timeInput || !locationInput || !messageInput || !submitBtn) {
		console.warn('Form elements not found');
		return;
	}

	const fetchTransaction = async () => {
		try {
			let data;
			if (transactionId) {
				const response = await fetch('get-transaction-by-id.php?transactionId=' + transactionId);
				data = await response.json();
			} else {
				const response = await fetch('ensure-transaction-by-status.php?status=' + encodeURIComponent('Application Approved'));
				data = await response.json();
			}

			if (!data.success) {
				console.error('Failed to fetch transaction:', data.message);
				return;
			}

			currentTransaction = data.transaction;
			console.log('Loaded transaction:', currentTransaction);
		} catch (err) {
			console.error('Failed to fetch transaction:', err);
		}
	};

	const submitSchedule = async () => {
		if (!currentTransaction) {
			alert('No transaction loaded');
			return;
		}

		const date = dateInput.value.trim();
		const time = timeInput.value.trim();
		const location = locationInput.value.trim();
		const message = messageInput.value.trim();

		if (!date || !time || !location) {
			alert('Date, Time, and Location are required.');
			return;
		}

		// Combine date and time into meetGreetDateTime format (YYYY-MM-DD HH:MM:SS)
		let meetGreetDateTime = '';
		try {
			// Parse date (expecting format like YYYY-MM-DD or similar)
			const dateObj = new Date(date);
			const timeObj = time.split(':'); // expecting HH:MM
			if (timeObj.length !== 2) throw new Error('Invalid time format');

			const year = dateObj.getFullYear();
			const month = String(dateObj.getMonth() + 1).padStart(2, '0');
			const day = String(dateObj.getDate()).padStart(2, '0');
			const hours = String(timeObj[0]).padStart(2, '0');
			const minutes = String(timeObj[1]).padStart(2, '0');

			meetGreetDateTime = `${year}-${month}-${day} ${hours}:${minutes}:00`;
		} catch (err) {
			alert('Invalid date/time format. Use YYYY-MM-DD for date and HH:MM for time.');
			return;
		}

		try {
			const response = await fetch('update-meet-and-greet.php', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify({
					transactionId: currentTransaction.transactionId,
					meetGreetDateTime,
					location,
					message
				})
			});

			const data = await response.json();

			if (!data.success) {
				throw new Error(data.message || 'Update failed');
			}

			alert('Meet and Greet scheduled successfully');
			console.log('Updated transaction:', data.transaction);

			// Navigate to next page
			setTimeout(() => {
				window.location.href = 'meet-and-greet-evaluation.html?transactionId=' + currentTransaction.transactionId;
			}, 500);
		} catch (err) {
			console.error('Failed to update:', err);
			alert(`Could not schedule meet and greet: ${err.message}`);
		}
	};

	submitBtn.addEventListener('click', submitSchedule);

	// Load transaction on page load
	fetchTransaction();
})();

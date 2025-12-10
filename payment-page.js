(() => {
	let currentTransaction = null;
	const transactionId = new URLSearchParams(window.location.search).get('transactionId');

	const payButton = document.querySelector('.pay-button');
	const totalElement = document.querySelector('.total-section span:last-child');

	const formatAmount = (value) => {
		const num = parseFloat(value);
		return Number.isFinite(num) ? `₱${num.toFixed(2)}` : '';
	};

	if (!payButton || !totalElement) {
		console.warn('Payment elements not found');
		return;
	}

	const fetchTransaction = async () => {
		try {
			let data;
			if (transactionId) {
				const response = await fetch('get-transaction-by-id.php?transactionId=' + transactionId);
				data = await response.json();
			} else {
				const response = await fetch('ensure-transaction-by-status.php?status=' + encodeURIComponent('Ready for Adoption'));
				data = await response.json();
			}

			if (!data.success) {
				console.error('Failed to fetch transaction:', data.message);
				return;
			}

			currentTransaction = data.transaction;
			console.log('Loaded transaction:', currentTransaction);

			// Set total based on pet price (fallback to userPayment)
			const amount = currentTransaction.petPrice ?? currentTransaction.userPayment;
			if (totalElement) {
				totalElement.textContent = formatAmount(amount);
			}
		} catch (err) {
			console.error('Failed to fetch transaction:', err);
		}
	};

	const processPayment = async () => {
		if (!currentTransaction) {
			alert('No transaction loaded');
			return;
		}

		const total = totalElement.textContent.trim();

		if (!total) {
			alert('Total amount is missing');
			return;
		}

		try {
			const response = await fetch('update-payment.php', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify({
					transactionId: currentTransaction.transactionId,
					total
				})
			});

			const data = await response.json();

			if (!data.success) {
				throw new Error(data.message || 'Payment failed');
			}

			alert('Payment successful! Adoption finalized.');
			console.log('Updated transaction:', data.transaction);

			// Navigate to adopted page
			setTimeout(() => {
				window.location.href = 'user-adopted.html?transactionId=' + currentTransaction.transactionId;
			}, 500);
		} catch (err) {
			console.error('Failed to process payment:', err);
			alert(`Payment failed: ${err.message}`);
		}
	};

	payButton.addEventListener('click', processPayment);

	// Load transaction on page load
	fetchTransaction();
})();

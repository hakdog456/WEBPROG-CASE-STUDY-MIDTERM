(() => {
	let currentTransaction = null;
	const transactionIdParam = new URLSearchParams(window.location.search).get('transactionId');
	const transactionId = transactionIdParam || localStorage.getItem('transactionID');

	const approveBtn = document.querySelector('.btn-primary');
	const refundBtn = document.querySelector('.btn-secondary');
	const dateValue = document.querySelector('.detail-row:nth-child(2) .detail-value');
	const totalValue = document.querySelector('.detail-row:nth-child(3) .detail-value');
	const statusValue = document.querySelector('.detail-row:nth-child(4) .detail-value');

	if (!approveBtn || !refundBtn) {
		console.warn('Action buttons not found');
		return;
	}

	const fetchTransaction = async () => {
		try {
			let data;
			if (transactionId) {
				const response = await fetch('get-transaction-by-id.php?transactionId=' + transactionId);
				data = await response.json();
			} else {
				const response = await fetch('ensure-transaction-by-status.php?status=' + encodeURIComponent('Paid'));
				data = await response.json();
			}

			if (!data.success) {
				console.error('Failed to fetch transaction:', data.message);
				return;
			}

			currentTransaction = data.transaction;
			console.log('Loaded transaction:', currentTransaction);

			const evaluation = currentTransaction.evaluationDecoded || {};
			const payment = evaluation.payment || {};

			// Populate payment details from evaluation JSON, fall back to defaults
			if (dateValue) {
				if (payment.date) {
					const paidDate = new Date(payment.date);
					dateValue.textContent = paidDate.toLocaleDateString('en-US', {
						year: 'numeric',
						month: 'long',
						day: 'numeric'
					});
				} else {
					dateValue.textContent = '';
				}
			}

			if (totalValue) {
				if (payment.total || payment.amount) {
					const numeric = parseFloat(String(payment.total || payment.amount).replace(/[^0-9.]/g, ''));
					totalValue.textContent = Number.isFinite(numeric) ? `₱${numeric.toFixed(2)}` : String(payment.total || payment.amount || '');
				} else if (currentTransaction.userPayment) {
					totalValue.textContent = `₱${parseFloat(currentTransaction.userPayment).toFixed(2)}`;
				} else {
					totalValue.textContent = '';
				}
			}

			if (statusValue) {
				statusValue.textContent = payment.status || 'Unpaid';
			}
		} catch (err) {
			console.error('Failed to fetch transaction:', err);
		}
	};

	const updateStatus = async (newStatus) => {
		if (!currentTransaction) {
			alert('No transaction loaded');
			return;
		}

		try {
			const response = await fetch('update-transaction-status.php', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify({
					transactionId: currentTransaction.transactionId,
					status: newStatus
				})
			});

			const data = await response.json();

			if (!data.success) {
				throw new Error(data.message || 'Update failed');
			}

			alert(`Status updated to: ${newStatus}`);
			console.log('Updated transaction:', data.transaction);

			// Navigate to confirmation page on approve
			if (newStatus === 'Paid - Approved') {
				setTimeout(() => {
					window.location.href = 'admin-ready-for-adoption-confirmation.html?transactionId=' + currentTransaction.transactionId;
				}, 500);
			} else {
				// Refunded: go back to admin review
				setTimeout(() => {
					window.location.href = 'admin-review.html';
				}, 500);
			}
		} catch (err) {
			console.error('Failed to update status:', err);
			alert(`Could not update status: ${err.message}`);
		}
	};

	approveBtn.addEventListener('click', () => {
		updateStatus('Paid - Approved');
	});

	refundBtn.addEventListener('click', () => {
		updateStatus('Refunded');
	});

	// Load transaction on page load
	fetchTransaction();
})();

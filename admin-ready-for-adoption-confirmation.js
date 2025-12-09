(() => {
	let currentTransaction = null;
	const transactionId = new URLSearchParams(window.location.search).get('transactionId');

	const approveBtn = document.querySelector('.btn-primary');
	const rejectBtn = document.querySelector('.btn-secondary');
	const verifyText = document.querySelector('.verify-text');

	if (!approveBtn || !rejectBtn) {
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
				const response = await fetch('ensure-transaction-by-status.php?status=' + encodeURIComponent('Paid - approved'));
				data = await response.json();
			}

			if (!data.success) {
				console.error('Failed to fetch transaction:', data.message);
				return;
			}

			currentTransaction = data.transaction;
			console.log('Loaded transaction:', currentTransaction);

			// Try to populate the name from evaluation if available
			if (verifyText) {
				const evaluation = typeof currentTransaction.evaluation === 'string'
					? JSON.parse(currentTransaction.evaluation || '{}')
					: (currentTransaction.evaluationDecoded || currentTransaction.evaluation || {});
				
				const appInfo = evaluation.applicantInfo || {};
				const name = appInfo.firstName 
					? `${appInfo.firstName} ${appInfo.lastName || ''}`.trim()
					: '[name]';
				
				verifyText.textContent = `Verify and approve Bark Twain's adoption to ${name}?`;
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

			// Reload or redirect as needed
			setTimeout(() => {
				window.location.href = 'admin-review.html';
			}, 500);
		} catch (err) {
			console.error('Failed to update status:', err);
			alert(`Could not update status: ${err.message}`);
		}
	};

	approveBtn.addEventListener('click', () => {
		updateStatus('Adopted-Final');
	});

	rejectBtn.addEventListener('click', () => {
		updateStatus('Application Rejected');
	});

	// Load transaction on page load
	fetchTransaction();
})();

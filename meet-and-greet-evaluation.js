(() => {
	let currentTransaction = null;
	const transactionId = new URLSearchParams(window.location.search).get('transactionId');

	const approveBtn = document.querySelector('.btn-primary');
	const rejectBtn = document.querySelector('.btn-secondary');
	const scoreDisplay = document.querySelector('.overall-score');

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
				const response = await fetch('ensure-transaction-by-status.php?status=' + encodeURIComponent('Meet and Greet Scheduled'));
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

	const collectScores = () => {
		const scores = {};
		for (let i = 1; i <= 8; i++) {
			const selected = document.querySelector(`input[name="q${i}"]:checked`);
			scores[`Q${i}`] = selected ? parseInt(selected.value) : null;
		}
		return scores;
	};

	const calculateAverage = (scores) => {
		const values = Object.values(scores).filter(v => v !== null);
		if (values.length === 0) return 0;
		const sum = values.reduce((acc, v) => acc + v, 0);
		return parseFloat((sum / values.length).toFixed(1));
	};

	const submitEvaluation = async (newStatus) => {
		if (!currentTransaction) {
			alert('No transaction loaded');
			return;
		}

		const scores = collectScores();
		const average = calculateAverage(scores);

		// Check if at least one question is answered
		if (Object.values(scores).every(v => v === null)) {
			alert('Please answer at least one evaluation question.');
			return;
		}

		try {
			const response = await fetch('update-evaluation.php', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify({
					transactionId: currentTransaction.transactionId,
					scores,
					average,
					status: newStatus
				})
			});

			const data = await response.json();

			if (!data.success) {
				throw new Error(data.message || 'Update failed');
			}

			alert(`Evaluation submitted and status updated to: ${newStatus}`);
			console.log('Updated transaction:', data.transaction);

			// Navigate based on status
			if (newStatus === 'Ready for Adoption') {
				setTimeout(() => {
					window.location.href = 'admin-ready-for-adoption.html?transactionId=' + currentTransaction.transactionId;
				}, 500);
			} else {
				// Rejected: could reload or redirect elsewhere
				setTimeout(() => {
					window.location.href = 'admin-review.html';
				}, 500);
			}
		} catch (err) {
			console.error('Failed to submit evaluation:', err);
			alert(`Could not submit evaluation: ${err.message}`);
		}
	};

	approveBtn.addEventListener('click', (e) => {
		e.preventDefault();
		submitEvaluation('Ready for Adoption');
	});

	rejectBtn.addEventListener('click', () => {
		submitEvaluation('Application Rejected');
	});

	// Load transaction on page load
	fetchTransaction();
})();

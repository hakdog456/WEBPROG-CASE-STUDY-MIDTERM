(() => {
	// Get transactionID from localStorage
	const transactionID = localStorage.getItem('transactionID');

	// If no transaction ID, don't do anything
	if (!transactionID) {
		return;
	}

	// Get current page filename
	const currentPage = window.location.pathname.split('/').pop() || 'index.html';

	// Fetch the transaction data from the backend
	const findAdminTransaction = async () => {
		try {
			const finalResponse = await fetch(`get-transaction-by-id.php?transactionId=${transactionID}`);
			const data = await finalResponse.json();

			// If no transaction found, go back to transactions list
			if (!data || !data.transaction) {
				if (currentPage !== 'data-overview.html') {
					window.location.href = 'data-overview.html';
				}
				return;
			}

			const transaction = data.transaction;
			const status = transaction.status?.trim();
			let targetPage = 'data-overview.html';

			// Determine target page based on status
			if (status === 'Application Placed') {
				targetPage = 'admin-review.html';
			} else if (status === 'Application Approved') {
				targetPage = 'meet-and-greet-sechdule.html';
			} else if (status === 'Meet and Greet Scheduled') {
				targetPage = 'meet-and-greet-evaluation.html';
			} else if (status === 'Ready for Adoption' || status === 'Paid') {
				targetPage = 'admin-ready-for-adoption.html';
			} else if (status === 'Paid - Approved') {
				targetPage = 'admin-ready-for-adoption-confirmation.html';
			} else if (status === 'Adopted-Final') {
				targetPage = 'data-overview.html';
			}

			// Only redirect if not already on the target page
			const hasQueryId = new URLSearchParams(window.location.search).has('transactionId');
			const needsId = targetPage !== 'transactions-list.html';
			const targetUrl = needsId ? `${targetPage}?transactionId=${transactionID}` : targetPage;

			if (currentPage !== targetPage) {
				window.location.href = targetUrl;
			} else if (needsId && !hasQueryId) {
				// Add missing transactionId to current page without changing page otherwise
				window.location.href = targetUrl;
			}
		} catch (err) {
			console.error('Error finding transaction:', err);
			// On error, go back to transactions list (if not already there)
			if (currentPage !== 'data-overview.html') {
				window.location.href = 'data-overview.html';
			}
		}
	};

	// Execute the routing logic
	findAdminTransaction();
})();

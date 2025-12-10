(() => {
	let currentTransaction = null;

	const form = document.querySelector('.form-container form');
	const approveBtn = document.querySelector('.btn-primary');
	const rejectBtn = document.querySelector('.btn-secondary');

	if (!form || !approveBtn || !rejectBtn) {
		console.warn('Form or buttons not found');
		return;
	}

	const setTextField = (field, value) => {
		const el = form.querySelector(`[data-field="${field}"]`);
		if (el) {
			el.value = value || '';
			console.log(`Set field "${field}" to "${value}"`);
		} else {
			console.warn(`Field element not found for data-field="${field}"`);
		}
	};

	const setRadioValue = (name, value) => {
		if (!value) return;
		const normalized = value.toLowerCase().trim();
		const radios = form.querySelectorAll(`input[name="${name}"]`);
		radios.forEach((radio) => {
			const label = form.querySelector(`label[for="${radio.id}"]`);
			if (label) {
				const labelText = label.textContent.toLowerCase().trim();
				if (labelText === normalized || labelText.includes(normalized)) {
					radio.checked = true;
				}
			}
		});
	};

	const setCheckboxState = (id, checked) => {
		const el = form.querySelector(`#${id}`);
		if (el) el.checked = checked || false;
	};

	const populateForm = (evaluation) => {
		if (!evaluation) {
			console.warn('No evaluation data to populate');
			return;
		}

		console.log('Populating form with evaluation:', evaluation);

		const appInfo = evaluation.applicantInfo || {};
		const homeEnv = evaluation.homeEnvironment || {};
		const petPref = evaluation.petPreferences || {};
		const agreement = evaluation.agreement || {};

		console.log('Applicant info:', appInfo);
		console.log('Home environment:', homeEnv);
		console.log('Pet preferences:', petPref);
		console.log('Agreement:', agreement);

		// Applicant Information
		setTextField('firstName', appInfo.firstName);
		setTextField('middleName', appInfo.middleName);
		setTextField('lastName', appInfo.lastName);
		setTextField('suffix', appInfo.suffix);
		setTextField('occupation', appInfo.occupation);
		setTextField('employer', appInfo.employer);
		setTextField('employerAddress', appInfo.employerAddress);
		setTextField('email', appInfo.email);
		setTextField('phone', appInfo.phone);
		setTextField('address', appInfo.address);

		// Home Environment
		setRadioValue('housing', homeEnv.housing);
		setRadioValue('rent', homeEnv.rent);
		setTextField('landlordName', homeEnv.landlordName);
		setTextField('landlordPhone', homeEnv.landlordPhone);
		setTextField('adultsInHousehold', homeEnv.adultsInHousehold);
		setTextField('childrenInHousehold', homeEnv.childrenInHousehold);
		setRadioValue('other-pets', homeEnv.otherPets);
		setRadioValue('prev-pets', homeEnv.previousPets);
		setTextField('averageAloneTime', homeEnv.averageAloneTime);

		// Pet Preferences - match the exact reason texts from submit-application.js
		const reasons = petPref.reasons || [];
		
		// Check each reason and match it to the checkbox
		// The labels are: "Companion for child", "Companion for other pets", "Security", 
		// "House pet", "Working animal/Pest control", "Breeding", "Other (please specify):"
		reasons.forEach(reason => {
			const reasonTrimmed = reason.trim();
			if (reasonTrimmed === 'Companion for child') {
				setCheckboxState('companion-child', true);
			} else if (reasonTrimmed === 'Companion for other pets') {
				setCheckboxState('companion-pet', true);
			} else if (reasonTrimmed === 'Security') {
				setCheckboxState('security', true);
			} else if (reasonTrimmed === 'House pet') {
				setCheckboxState('house-pet', true);
			} else if (reasonTrimmed === 'Working animal/Pest control') {
				setCheckboxState('working', true);
			} else if (reasonTrimmed === 'Breeding') {
				setCheckboxState('breeding', true);
			} else if (reasonTrimmed.startsWith('Other')) {
				setCheckboxState('other-reason', true);
			}
		});
		setTextField('otherReason', petPref.otherReasonDetail);

		setRadioValue('gift', petPref.gift);
		setTextField('giftRecipientName', petPref.giftRecipientName);
		setTextField('giftRecipientPhone', petPref.giftRecipientPhone);
		setRadioValue('financial', petPref.financialPrepared);

		// Agreement
		setCheckboxState('understand', agreement.understand);
		setCheckboxState('certify', agreement.certify);
		setTextField('signature', agreement.signature);
	};

	const fetchTransactionById = async () => {
		try {
			// Get transactionID from localStorage
			const transactionID = localStorage.getItem('transactionID');
			
			console.log('TransactionID from localStorage:', transactionID);
			
			if (!transactionID) {
				console.warn('No transactionID in localStorage');
				alert('No transaction selected. Please select a transaction from the list.');
				window.location.href = 'transactions-list.html';
				return;
			}

			const url = `get-transaction-by-id.php?transactionId=${encodeURIComponent(transactionID)}`;
			console.log('Fetching URL:', url);
			
			const response = await fetch(url);
			console.log('Response status:', response.status);
			
			let data = null;
			let text = '';
			try {
				text = await response.text();
				console.log('Response text:', text);
				data = text ? JSON.parse(text) : null;
			} catch (parseErr) {
				console.error('Failed to parse transaction response', parseErr, text);
				throw parseErr;
			}

			if (!data.success || !data.transaction) {
				console.warn('No transaction found');
				alert('Transaction not found.');
				return;
			}

			currentTransaction = data.transaction;
			
			// Parse the evaluation JSON - check evaluationDecoded first, then evaluation
			let evaluation = data.transaction.evaluationDecoded || data.transaction.evaluation;
			
			if (typeof evaluation === 'string') {
				try {
					evaluation = JSON.parse(evaluation);
				} catch (e) {
					console.error('Failed to parse evaluation JSON', e);
					evaluation = {};
				}
			}

			// Check if this is a seeded/placeholder transaction with no real data
			if (evaluation && evaluation.seeded) {
				console.warn('This is a placeholder transaction with no application data');
				// Still populate whatever data exists
			}

			populateForm(evaluation);
			console.log('Loaded transaction:', currentTransaction);
			console.log('Evaluation data:', evaluation);
		} catch (err) {
			console.error('Failed to fetch transaction:', err);
			alert('Failed to load transaction data.');
		}
	};

	const updateTransactionStatus = async (newStatus) => {
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

			alert(`Application ${newStatus}`);
			currentTransaction.status = newStatus;
			console.log('Updated transaction:', currentTransaction);

			// Optionally reload the next transaction
			// fetchFirstTransaction();
		} catch (err) {
			console.error('Failed to update status:', err);
			alert(`Could not update application: ${err.message}`);
		}
	};

	approveBtn.addEventListener('click', () => {
		updateTransactionStatus('Application Approved');
		setTimeout(() => {
			window.location.href = 'meet-and-greet-sechdule.html?transactionId=' + currentTransaction.transactionId;
		}, 500);
	});

	rejectBtn.addEventListener('click', () => {
		updateTransactionStatus('Application Rejected');
	});

	// Load the transaction from localStorage on page load
	fetchTransactionById();
})();

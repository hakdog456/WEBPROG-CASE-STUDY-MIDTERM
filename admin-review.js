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
		// Try data-field first, then search by label text proximity
		let el = form.querySelector(`[data-field="${field}"]`);
		
		if (!el) {
			// Search all labels for matching field name, then get the next input
			const labels = Array.from(form.querySelectorAll('label'));
			const label = labels.find((l) => {
				const text = l.textContent.toLowerCase();
				return text.includes(field.toLowerCase()) || text.includes(field.replace(/([A-Z])/g, ' $1').toLowerCase());
			});
			if (label) {
				// Get the parent form-group and find the input
				const formGroup = label.closest('.form-group');
				el = formGroup ? formGroup.querySelector('input') : null;
			}
		}
		
		if (el) el.value = value || '';
	};

	const setRadioValue = (name, value) => {
		if (!value) return;
		const normalized = value.toLowerCase().trim();
		const radios = form.querySelectorAll(`input[name="${name}"]`);
		radios.forEach((radio) => {
			const label = form.querySelector(`label[for="${radio.id}"]`);
			if (label && label.textContent.toLowerCase().includes(normalized)) {
				radio.checked = true;
			}
		});
	};

	const setCheckboxState = (id, checked) => {
		const el = form.querySelector(`#${id}`);
		if (el) el.checked = checked || false;
	};

	const populateForm = (evaluation) => {
		if (!evaluation) return;

		const appInfo = evaluation.applicantInfo || {};
		const homeEnv = evaluation.homeEnvironment || {};
		const petPref = evaluation.petPreferences || {};
		const agreement = evaluation.agreement || {};

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

		// Pet Preferences
		const reasons = petPref.reasons || [];
		const reasonMap = {
			'Companion for child': 'companion-child',
			'Companion for other pets': 'companion-pet',
			'Security': 'security',
			'House pet': 'house-pet',
			'Working animal/Pest control': 'working',
			'Breeding': 'breeding'
		};
		Object.entries(reasonMap).forEach(([label, id]) => {
			setCheckboxState(id, reasons.includes(label));
		});
		setCheckboxState('other-reason', reasons.some((r) => !Object.values(reasonMap).some((id) => r === id)));
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

	const fetchFirstTransaction = async () => {
		try {
			const response = await fetch('ensure-transaction-by-status.php?status=' + encodeURIComponent('Application Placed'));
			let data = null;
			let text = '';
			try {
				text = await response.text();
				data = text ? JSON.parse(text) : null;
			} catch (parseErr) {
				console.error('Failed to parse transaction response', parseErr, text);
				throw parseErr;
			}

			if (!data.success || !data.transaction) {
				console.warn('No transaction found');
				return;
			}

			currentTransaction = data.transaction;
			const evaluation = typeof data.transaction.evaluation === 'string'
				? JSON.parse(data.transaction.evaluation)
				: (data.transaction.evaluationDecoded || data.transaction.evaluation);

			populateForm(evaluation);
			console.log('Loaded transaction:', currentTransaction);
		} catch (err) {
			console.error('Failed to fetch transaction:', err);
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

	// Load the first transaction on page load
	fetchFirstTransaction();
})();

(() => {
	const form = document.querySelector('.form-container form');
	const submitBtn = form?.querySelector('.submit-arrow');

	if (!form || !submitBtn) {
		return;
	}

	const getTextField = (field) => {
		const el = form.querySelector(`[data-field="${field}"]`);
		return el ? el.value.trim() : '';
	};

	const getRadioValue = (name) => {
		const checked = form.querySelector(`input[name="${name}"]:checked`);
		if (!checked) return '';
		const label = form.querySelector(`label[for="${checked.id}"]`);
		return label ? label.textContent.trim() : checked.id;
	};

	const getCheckboxState = (id) => form.querySelector(`#${id}`)?.checked || false;

	const collectAdoptionReasons = () => {
		const reasonIds = [
			'companion-child',
			'companion-pet',
			'security',
			'house-pet',
			'working',
			'breeding',
			'other-reason'
		];

		return reasonIds
			.filter((id) => getCheckboxState(id))
			.map((id) => {
				const label = form.querySelector(`label[for="${id}"]`);
				return label ? label.textContent.trim() : id;
			});
	};

	const getPetId = () => {
		const params = new URLSearchParams(window.location.search);
		const fromQuery = params.get('petId');
		if (fromQuery && !Number.isNaN(Number(fromQuery))) {
			return Number(fromQuery);
		}

		const dataHolder = document.querySelector('[data-pet-id]');
		const fromDataAttr = dataHolder?.dataset.petId;
		if (fromDataAttr && !Number.isNaN(Number(fromDataAttr))) {
			return Number(fromDataAttr);
		}

		const fromStorage = localStorage.getItem('petID');
		if (fromStorage && !Number.isNaN(Number(fromStorage))) {
			return Number(fromStorage);
		}

		return null;
	};

	const getUserId = () => {
		const params = new URLSearchParams(window.location.search);
		const fromQuery = params.get('userId');
		if (fromQuery && !Number.isNaN(Number(fromQuery))) {
			return Number(fromQuery);
		}

		const fromStorage = localStorage.getItem('userId');
		if (fromStorage && !Number.isNaN(Number(fromStorage))) {
			return Number(fromStorage);
		}

		return null;
	};

	const buildPayload = () => {
		const applicantInfo = {
			firstName: getTextField('firstName'),
			middleName: getTextField('middleName'),
			lastName: getTextField('lastName'),
			suffix: getTextField('suffix'),
			occupation: getTextField('occupation'),
			employer: getTextField('employer'),
			employerAddress: getTextField('employerAddress'),
			email: getTextField('email'),
			phone: getTextField('phone'),
			address: getTextField('address')
		};

		const homeEnvironment = {
			housing: getRadioValue('housing'),
			rent: getRadioValue('rent'),
			landlordName: getTextField('landlordName'),
			landlordPhone: getTextField('landlordPhone'),
			adultsInHousehold: getTextField('adultsInHousehold'),
			childrenInHousehold: getTextField('childrenInHousehold'),
			otherPets: getRadioValue('other-pets'),
			previousPets: getRadioValue('prev-pets'),
			averageAloneTime: getTextField('averageAloneTime')
		};

		const petPreferences = {
			reasons: collectAdoptionReasons(),
			otherReasonDetail: getTextField('otherReason'),
			gift: getRadioValue('gift'),
			giftRecipientName: getTextField('giftRecipientName'),
			giftRecipientPhone: getTextField('giftRecipientPhone'),
			financialPrepared: getRadioValue('financial')
		};

		const agreement = {
			understand: getCheckboxState('understand'),
			certify: getCheckboxState('certify'),
			signature: getTextField('signature')
		};

		return {
			petId: getPetId(),
			userId: getUserId(),
			userPayment: 0,
			meetGreetDateTime: new Date().toISOString().slice(0, 19).replace('T', ' '),
			status: 'Application Placed',
			location: applicantInfo.address,
			evaluation: {
				applicantInfo,
				homeEnvironment,
				petPreferences,
				agreement,
				submittedAt: new Date().toISOString()
			}
		};
	};

	const validatePayload = (payload) => {
		const errors = [];
		if (!payload.petId) errors.push('Pet selection is missing.');
		if (!payload.evaluation?.applicantInfo?.email) errors.push('Email is required.');
		if (!payload.evaluation?.applicantInfo?.phone) errors.push('Phone is required.');
		return errors;
	};

	const setLoading = (loading) => {
		submitBtn.disabled = loading;
		submitBtn.classList.toggle('is-loading', loading);
	};

	submitBtn.addEventListener('click', async () => {
		const payload = buildPayload();
		const errors = validatePayload(payload);

		if (errors.length) {
			alert(errors.join('\n'));
			return;
		}

		setLoading(true);

		try {
			const response = await fetch('submit-application.php', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify(payload)
			});

			let data = null;
			let text = '';
			try {
				text = await response.text();
				data = text ? JSON.parse(text) : null;
			} catch (e) {
				console.warn('Response not JSON', e, text);
			}

			if (!response.ok || !data?.success) {
				const message = data?.message || `Unable to submit application (status ${response.status}).`;
				throw new Error(message);
			}

			console.log('Transaction created:', data.row || data);

			const createdId = data.transactionId || data.row?.transactionId;
			if (createdId) {
				localStorage.setItem('transactionID', String(createdId));
			}

			alert('Application submitted successfully.');
			const target = createdId ? `meet-greet-page.html?transactionId=${createdId}` : 'meet-greet-page.html';
			window.location.href = target;
			
		} catch (err) {
			console.error('Submit failed', err);
			alert(`Could not submit application: ${err.message}`);
		} finally {
			setLoading(false);
		}
	});
})();

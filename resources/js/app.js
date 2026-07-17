const form = document.querySelector('[data-contact-form]');

if (form instanceof HTMLFormElement) {
    const status = form.querySelector('[data-form-status]');
    const button = form.querySelector('button[type="submit"]');

    const setStatus = (message, type = '') => {
        if (!status) {
            return;
        }

        status.textContent = message;
        status.className = ['form-status', type].filter(Boolean).join(' ');
    };

    const setLoading = (isLoading) => {
        if (!(button instanceof HTMLButtonElement)) {
            return;
        }

        button.disabled = isLoading;
        button.innerHTML = isLoading
            ? '<span>Отправляем...</span><span aria-hidden="true">↻</span>'
            : '<span>Отправить заявку</span><span aria-hidden="true">→</span>';
    };

    const validationMessage = (errors) => {
        if (!errors || typeof errors !== 'object') {
            return 'Проверьте поля формы и попробуйте ещё раз.';
        }

        return Object.values(errors)
            .flat()
            .filter(Boolean)
            .join(' ');
    };

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        setLoading(true);
        setStatus('');

        const formData = new FormData(form);
        const payload = Object.fromEntries(formData.entries());

        try {
            const response = await fetch('/api/contact', {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(payload),
            });

            const data = await response.json().catch(() => ({}));

            if (response.ok) {
                const requestId = data?.data?.request_id;
                form.reset();
                setStatus(
                    requestId
                        ? `Заявка отправлена. Номер обращения: ${requestId}`
                        : 'Заявка отправлена.',
                    'success',
                );

                return;
            }

            if (response.status === 422) {
                setStatus(validationMessage(data.errors), 'warning');
                return;
            }

            if (response.status === 429) {
                setStatus('Слишком много запросов. Попробуйте отправить форму чуть позже.', 'warning');
                return;
            }

            setStatus(data.message || 'Не удалось отправить заявку. Попробуйте позже.', 'error');
        } catch (error) {
            setStatus('Не удалось подключиться к API. Проверьте, что сервер запущен.', 'error');
        } finally {
            setLoading(false);
        }
    });
}

import { ApiError, apiClient } from '../core/api-client.js';

function setBusy(form, isBusy) {
    const button = form.querySelector('[data-submit-button]');

    if (!button) {
        return;
    }

    if (!button.dataset.idleLabel) {
        button.dataset.idleLabel = button.textContent.trim();
    }

    button.disabled = isBusy;
    button.textContent = isBusy ? 'Please wait' : button.dataset.idleLabel;
    form.setAttribute('aria-busy', String(isBusy));
}

function clearErrors(form) {
    form.querySelectorAll('[data-client-error]').forEach((message) => message.remove());
    form.querySelectorAll('[aria-invalid="true"]').forEach((control) => {
        control.removeAttribute('aria-invalid');

        if (control.dataset.originalDescribedBy) {
            control.setAttribute('aria-describedby', control.dataset.originalDescribedBy);
        } else {
            control.removeAttribute('aria-describedby');
        }

        delete control.dataset.originalDescribedBy;
    });
}

function showFieldError(form, name, messages) {
    const control = form.elements.namedItem(name);

    if (!(control instanceof HTMLElement)) {
        return;
    }

    const message = document.createElement('p');
    const messageId = `${control.id || name}-client-error`;
    message.id = messageId;
    message.className = 'field__message field__message--error';
    message.dataset.clientError = 'true';
    message.textContent = Array.isArray(messages) ? messages[0] : String(messages);

    control.dataset.originalDescribedBy = control.getAttribute('aria-describedby') || '';
    control.setAttribute('aria-invalid', 'true');
    control.setAttribute('aria-describedby', messageId);
    control.insertAdjacentElement('afterend', message);
}

function showFeedback(container, message, variant = 'danger') {
    if (!container) {
        return;
    }

    container.hidden = false;
    container.classList.remove('alert--danger', 'alert--success', 'alert--warning', 'alert--info');
    container.classList.add('alert', `alert--${variant}`);
    container.setAttribute('role', variant === 'danger' ? 'alert' : 'status');
    container.textContent = message;
}

function clearFeedback(container) {
    if (!container) {
        return;
    }

    container.hidden = true;
    container.textContent = '';
    container.removeAttribute('role');
}

function loginPayload(form) {
    return {
        email: form.elements.email.value.trim(),
        password: form.elements.password.value,
    };
}

function registerPayload(form) {
    return {
        name: form.elements.name.value.trim(),
        email: form.elements.email.value.trim(),
        password: form.elements.password.value,
        role: form.elements.role.value,
    };
}

async function submitLogin(form) {
    return apiClient.post('auth/login', loginPayload(form));
}

async function submitRegistration(form) {
    if (form.elements.password.value !== form.elements.password_confirmation.value) {
        showFieldError(form, 'password_confirmation', 'The passwords do not match.');
        form.elements.password_confirmation.focus();
        return false;
    }

    const payload = registerPayload(form);
    await apiClient.post('auth/register', payload);
    await apiClient.post('auth/login', {
        email: payload.email,
        password: payload.password,
    });

    return true;
}

function handleApiError(form, feedback, error) {
    if (error instanceof ApiError) {
        Object.entries(error.errors).forEach(([name, messages]) => {
            showFieldError(form, name, messages);
        });

        showFeedback(feedback, error.message);
        return;
    }

    showFeedback(feedback, 'Something went wrong. Please try again.');
}

function setupAuthForms() {
    document.querySelectorAll('[data-auth-form]').forEach((form) => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const feedback = form.closest('.auth-form-wrap')?.querySelector('[data-auth-feedback]');
            clearErrors(form);
            clearFeedback(feedback);
            setBusy(form, true);

            try {
                const completed = form.dataset.authForm === 'register'
                    ? await submitRegistration(form)
                    : await submitLogin(form);

                if (completed !== false) {
                    window.location.assign(form.dataset.successUrl || '/app');
                }
            } catch (error) {
                handleApiError(form, feedback, error);
            } finally {
                setBusy(form, false);
            }
        });
    });
}

function setupLogout() {
    document.querySelectorAll('[data-logout-button]').forEach((button) => {
        button.addEventListener('click', async () => {
            const feedback = document.querySelector('[data-auth-feedback]');
            button.disabled = true;
            button.textContent = 'Signing out';

            try {
                await apiClient.post('auth/logout');
                window.location.assign(button.dataset.loginUrl || '/login');
            } catch (error) {
                const message = error instanceof ApiError ? error.message : 'Unable to sign out. Please try again.';
                showFeedback(feedback, message);
                button.disabled = false;
                button.textContent = 'Sign out';
            }
        });
    });
}

export function setupAuthentication() {
    setupAuthForms();
    setupLogout();
}

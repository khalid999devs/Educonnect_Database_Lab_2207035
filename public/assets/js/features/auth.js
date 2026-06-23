import { ApiError, apiClient } from '../core/api-client.js';
import {
    clearFeedback,
    clearFormErrors,
    setFormBusy,
    showApiErrors,
    showFeedback,
    showFieldError,
} from '../core/forms.js';

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

function setupAuthForms() {
    document.querySelectorAll('[data-auth-form]').forEach((form) => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const feedback = form.closest('.auth-form-wrap')?.querySelector('[data-auth-feedback]');
            clearFormErrors(form);
            clearFeedback(feedback);
            setFormBusy(form, true);

            try {
                const completed = form.dataset.authForm === 'register'
                    ? await submitRegistration(form)
                    : await submitLogin(form);

                if (completed !== false) {
                    window.location.assign(form.dataset.successUrl || '/app');
                }
            } catch (error) {
                showApiErrors(form, feedback, error);
            } finally {
                setFormBusy(form, false);
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

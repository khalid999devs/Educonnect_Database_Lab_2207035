import { ApiError } from './api-client.js';

export function setFormBusy(form, isBusy, busyLabel = 'Please wait') {
    const button = form.querySelector('[data-submit-button]');

    if (!button) {
        return;
    }

    if (!button.dataset.idleLabel) {
        button.dataset.idleLabel = button.textContent.trim();
    }

    button.disabled = isBusy;
    button.textContent = isBusy ? busyLabel : button.dataset.idleLabel;
    form.setAttribute('aria-busy', String(isBusy));
}

export function clearFormErrors(form) {
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

export function showFieldError(form, name, messages) {
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

export function showFeedback(container, message, variant = 'danger') {
    if (!container) {
        return;
    }

    container.hidden = false;
    container.classList.remove('alert--danger', 'alert--success', 'alert--warning', 'alert--info');
    container.classList.add('alert', `alert--${variant}`);
    container.setAttribute('role', variant === 'danger' ? 'alert' : 'status');
    container.textContent = message;
}

export function clearFeedback(container) {
    if (!container) {
        return;
    }

    container.hidden = true;
    container.textContent = '';
    container.removeAttribute('role');
}

export function showApiErrors(form, feedback, error) {
    if (error instanceof ApiError) {
        Object.entries(error.errors).forEach(([name, messages]) => {
            showFieldError(form, name, messages);
        });

        showFeedback(feedback, error.message);
        return;
    }

    showFeedback(feedback, 'Something went wrong. Please try again.');
}

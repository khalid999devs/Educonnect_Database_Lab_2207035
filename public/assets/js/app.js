import { apiClient } from './core/api-client.js';
import { setupAuthentication } from './features/auth.js';

function setupSidebar() {
    const shell = document.querySelector('[data-app-shell]');
    const toggle = document.querySelector('[data-sidebar-toggle]');
    const backdrop = document.querySelector('[data-sidebar-backdrop]');

    if (!shell || !toggle || !backdrop) {
        return;
    }

    const setOpen = (isOpen) => {
        shell.dataset.sidebarOpen = String(isOpen);
        toggle.setAttribute('aria-expanded', String(isOpen));
    };

    toggle.addEventListener('click', () => {
        setOpen(shell.dataset.sidebarOpen !== 'true');
    });

    backdrop.addEventListener('click', () => setOpen(false));

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            setOpen(false);
        }
    });
}

function setupDismissibleAlerts() {
    document.querySelectorAll('[data-alert-dismiss]').forEach((button) => {
        button.addEventListener('click', () => {
            button.closest('[data-alert]')?.remove();
        });
    });
}

function initialize() {
    setupSidebar();
    setupDismissibleAlerts();
    setupAuthentication();
}

window.Educonnect = Object.freeze({ api: apiClient });

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initialize);
} else {
    initialize();
}

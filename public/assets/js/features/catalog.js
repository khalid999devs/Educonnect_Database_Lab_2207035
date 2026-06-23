import { ApiError, apiClient } from '../core/api-client.js';
import { clearFeedback, showFeedback } from '../core/forms.js';
import { getCatalogConfig } from './catalog-config.js';
import { renderCatalogDetail, renderCatalogGrid } from './catalog-renderer.js';

function replaceOptions(select, items, placeholder) {
    const prompt = document.createElement('option');
    prompt.value = '';
    prompt.textContent = placeholder;
    const options = items.map((item) => {
        const option = document.createElement('option');
        option.value = item.id;
        option.textContent = item.name;
        return option;
    });
    select.replaceChildren(prompt, ...options);
    select.disabled = false;
}

function configureFilters(root, config, references) {
    const category = root.querySelector('[data-category-filter]');
    const field = root.querySelector('[data-field-filter]');
    const extra = root.querySelector('[data-extra-filter]');

    category.name = config.categoryParameter;
    replaceOptions(category, references[config.categoryReference], 'All categories');
    replaceOptions(field, references.academic_fields, 'All academic fields');

    extra.name = config.extraParameter;
    root.querySelector('[data-extra-filter-label]').textContent = config.extraLabel;
    extra.replaceChildren(...config.extraOptions.map(([value, label]) => {
        const option = document.createElement('option');
        option.value = value;
        option.textContent = label;
        return option;
    }));
}

function queryFor(form, page) {
    const query = new URLSearchParams({
        status: 'APPROVED',
        per_page: '9',
        page: String(page),
    });

    new FormData(form).forEach((value, key) => {
        const normalized = String(value).trim();
        if (normalized) {
            query.set(key, normalized);
        }
    });

    return query;
}

function showCatalogState(root, state) {
    root.querySelector('[data-catalog-loading]').hidden = state !== 'loading';
    root.querySelector('[data-catalog-error]').hidden = state !== 'error';
    root.querySelector('[data-catalog-content]').hidden = state !== 'content';
}

function updatePagination(root, paginator) {
    const page = Number(paginator.current_page) || 1;
    const lastPage = Number(paginator.last_page) || 1;
    root.querySelector('[data-page-previous]').disabled = page <= 1;
    root.querySelector('[data-page-next]').disabled = page >= lastPage;
    root.querySelector('[data-pagination-label]').textContent = `Page ${page} of ${lastPage}`;
    root.querySelector('[data-results-summary]').textContent = `${Number(paginator.total) || 0} results`;
    root.querySelector('[data-page-summary]').textContent = `Showing page ${page}`;
}

async function loadCatalog(root, state) {
    showCatalogState(root, 'loading');

    try {
        const query = queryFor(state.form, state.page);
        const response = await apiClient.get(`${state.config.endpoint}?${query}`);
        const paginator = response.data;
        state.items = new Map(paginator.data.map((item) => [String(item.id), item]));
        renderCatalogGrid(root.querySelector('[data-catalog-grid]'), paginator.data, state.config);
        updatePagination(root, paginator);
        showCatalogState(root, 'content');
    } catch (error) {
        const message = error instanceof ApiError ? error.message : 'The catalog could not be loaded.';
        root.querySelector('[data-catalog-error-message]').textContent = message;
        showCatalogState(root, 'error');
    }
}

async function loadDetail(root, state, id) {
    const dialog = root.querySelector('[data-catalog-dialog]');
    const feedback = dialog.querySelector('[data-detail-feedback]');
    dialog.querySelector('[data-detail-loading]').hidden = false;
    dialog.querySelector('[data-detail-content]').hidden = true;
    clearFeedback(feedback);
    dialog.showModal();

    try {
        const response = await apiClient.get(`${state.config.endpoint}/${id}`);
        state.detailItem = response.data;
        state.detailAction = renderCatalogDetail(
            dialog,
            state.detailItem,
            state.config,
            state.completedActions.has(String(id)),
        );
        dialog.querySelector('[data-detail-content]').hidden = false;
    } catch (error) {
        const message = error instanceof ApiError ? error.message : 'Unable to load item details.';
        showFeedback(feedback, message);
    } finally {
        dialog.querySelector('[data-detail-loading]').hidden = true;
    }
}

async function runCatalogAction(root, state) {
    if (!state.detailAction || !state.detailItem) {
        return;
    }

    const dialog = root.querySelector('[data-catalog-dialog]');
    const button = dialog.querySelector('[data-catalog-action]');
    const feedback = dialog.querySelector('[data-detail-feedback]');
    button.disabled = true;
    button.textContent = 'Please wait';
    clearFeedback(feedback);

    try {
        const response = await apiClient.post(state.detailAction.endpoint, {});
        state.completedActions.add(String(state.detailItem.id));
        button.textContent = state.detailAction.completedLabel;
        showFeedback(feedback, response.message, 'success');
    } catch (error) {
        if (error instanceof ApiError && error.status === 409) {
            state.completedActions.add(String(state.detailItem.id));
            button.textContent = state.detailAction.completedLabel;
            showFeedback(feedback, error.message, 'info');
            return;
        }

        button.disabled = false;
        button.textContent = state.detailAction.label;
        showFeedback(feedback, error instanceof ApiError ? error.message : 'The action could not be completed.');
    }
}

export async function setupCatalog() {
    const root = document.querySelector('[data-catalog]');

    if (!root) {
        return;
    }

    const config = getCatalogConfig(root.dataset.catalogType);
    if (!config) {
        return;
    }

    const state = {
        config,
        form: root.querySelector('[data-catalog-filters]'),
        page: 1,
        items: new Map(),
        detailItem: null,
        detailAction: null,
        completedActions: new Set(),
    };

    try {
        const references = await apiClient.get('reference-data');
        configureFilters(root, config, references.data);
    } catch (error) {
        showFeedback(
            root.querySelector('[data-catalog-feedback]'),
            error instanceof ApiError ? error.message : 'Some filters are unavailable.',
            'warning',
        );
    }

    state.form.addEventListener('submit', (event) => {
        event.preventDefault();
        state.page = 1;
        loadCatalog(root, state);
    });

    root.querySelector('[data-filter-clear]').addEventListener('click', () => {
        state.form.reset();
        state.page = 1;
        loadCatalog(root, state);
    });
    root.querySelector('[data-catalog-retry]').addEventListener('click', () => loadCatalog(root, state));
    root.querySelector('[data-page-previous]').addEventListener('click', () => {
        state.page = Math.max(1, state.page - 1);
        loadCatalog(root, state);
    });
    root.querySelector('[data-page-next]').addEventListener('click', () => {
        state.page += 1;
        loadCatalog(root, state);
    });
    root.querySelector('[data-catalog-grid]').addEventListener('click', (event) => {
        const button = event.target.closest('[data-detail-id]');
        if (button) {
            loadDetail(root, state, button.dataset.detailId);
        }
    });
    root.querySelector('[data-catalog-action]').addEventListener('click', () => runCatalogAction(root, state));

    loadCatalog(root, state);
}

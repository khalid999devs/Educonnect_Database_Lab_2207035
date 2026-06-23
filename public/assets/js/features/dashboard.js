import { ApiError, apiClient } from '../core/api-client.js';

function setText(root, selector, value) {
    const element = root.querySelector(selector);

    if (element) {
        element.textContent = value;
    }
}

function titleCase(value) {
    if (!value) {
        return 'Not set';
    }

    return String(value)
        .toLowerCase()
        .replace(/(^|_)([a-z])/g, (_, separator, letter) => `${separator ? ' ' : ''}${letter.toUpperCase()}`);
}

function safeExternalUrl(value) {
    try {
        const url = new URL(value);
        return ['http:', 'https:'].includes(url.protocol) ? url.href : null;
    } catch {
        return null;
    }
}

function renderProfile(root, dashboard) {
    const profile = dashboard.student_profile || {};
    const completion = Math.max(0, Math.min(100, Number(dashboard.profile_completion) || 0));
    const progress = root.querySelector('[data-profile-progress]');

    setText(root, '[data-profile-name]', profile.user?.name || 'Student profile');
    setText(root, '[data-profile-completion]', `${Math.round(completion)}%`);
    setText(root, '[data-profile-university]', profile.university?.name || 'Not set');
    setText(root, '[data-profile-field]', profile.academic_field?.name || 'Not set');
    setText(root, '[data-profile-semester]', profile.semester ? `Semester ${profile.semester}` : 'Not set');
    setText(root, '[data-profile-level]', titleCase(profile.skill_level));

    if (progress) {
        progress.value = completion;
        progress.textContent = `${Math.round(completion)}%`;
    }
}

function renderMetrics(root, dashboard) {
    const metrics = {
        '[data-count-saved-resources]': dashboard.saved_resources_count,
        '[data-count-saved-templates]': dashboard.saved_templates_count,
        '[data-count-documents]': dashboard.uploaded_documents_count,
        '[data-count-research-topics]': dashboard.research_topics_count,
    };

    Object.entries(metrics).forEach(([selector, value]) => {
        setText(root, selector, String(Number(value) || 0));
    });
}

function resourceMeta(resource, includeMatch) {
    const meta = document.createElement('div');
    meta.className = 'resource-row__meta';

    const rating = Number(resource.average_rating) || 0;
    const ratingText = document.createElement('span');
    ratingText.textContent = rating > 0 ? `${rating.toFixed(1)} rating` : 'Not rated';
    meta.append(ratingText);

    const difficulty = document.createElement('span');
    difficulty.textContent = titleCase(resource.difficulty_level);
    meta.append(difficulty);

    if (includeMatch && resource.recommendation_score !== undefined) {
        const match = document.createElement('span');
        match.className = 'resource-row__match';
        match.textContent = `${Math.round(Number(resource.recommendation_score) || 0)}% match`;
        meta.append(match);
    }

    return meta;
}

function resourceRow(resource, includeMatch) {
    const row = document.createElement('article');
    row.className = 'resource-row';

    const main = document.createElement('div');
    main.className = 'resource-row__main';

    const category = document.createElement('p');
    category.className = 'resource-row__category';
    category.textContent = resource.category?.name || 'Academic resource';

    const title = document.createElement('h3');
    title.className = 'resource-row__title';
    title.textContent = resource.title || 'Untitled resource';

    main.append(category, title, resourceMeta(resource, includeMatch));
    row.append(main);

    const url = safeExternalUrl(resource.resource_url);
    if (url) {
        const link = document.createElement('a');
        link.className = 'resource-row__link';
        link.href = url;
        link.target = '_blank';
        link.rel = 'noopener noreferrer';
        link.textContent = 'Open';
        link.setAttribute('aria-label', `Open ${resource.title || 'resource'} in a new tab`);
        row.append(link);
    }

    return row;
}

function renderList(container, resources, emptyMessage, includeMatch = false) {
    container.replaceChildren();

    if (!Array.isArray(resources) || resources.length === 0) {
        const empty = document.createElement('p');
        empty.className = 'resource-empty';
        empty.textContent = emptyMessage;
        container.append(empty);
        return;
    }

    resources.slice(0, 5).forEach((resource) => {
        container.append(resourceRow(resource, includeMatch));
    });
}

function showState(root, state) {
    root.querySelector('[data-dashboard-loading]').hidden = state !== 'loading';
    root.querySelector('[data-dashboard-error]').hidden = state !== 'error';
    root.querySelector('[data-dashboard-content]').hidden = state !== 'content';
}

async function loadDashboard(root) {
    const studentId = root.dataset.studentId;
    showState(root, 'loading');

    try {
        const response = await apiClient.get(`dashboard/student/${studentId}`);
        const dashboard = response.data;
        renderProfile(root, dashboard);
        renderMetrics(root, dashboard);
        renderList(
            root.querySelector('[data-recommendation-list]'),
            dashboard.recommendations,
            'No recommendations are available yet.',
            true,
        );
        renderList(
            root.querySelector('[data-recent-list]'),
            dashboard.recent_resources,
            'No resources have been added yet.',
        );
        showState(root, 'content');
    } catch (error) {
        const message = error instanceof ApiError ? error.message : 'We could not load your workspace.';
        setText(root, '[data-dashboard-error-message]', message);
        showState(root, 'error');
    }
}

export function setupDashboard() {
    const root = document.querySelector('[data-dashboard]');

    if (!root) {
        return;
    }

    root.querySelector('[data-dashboard-retry]').addEventListener('click', () => loadDashboard(root));
    loadDashboard(root);
}

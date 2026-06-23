export function safeExternalUrl(value) {
    try {
        const url = new URL(value);
        return ['http:', 'https:'].includes(url.protocol) ? url.href : null;
    } catch {
        return null;
    }
}

function tag(label, accent = false) {
    const element = document.createElement('span');
    element.className = accent ? 'catalog-tag catalog-tag--accent' : 'catalog-tag';
    element.textContent = label;
    return element;
}

function catalogCard(item, config) {
    const card = document.createElement('article');
    card.className = 'catalog-card';
    card.dataset.tone = config.tone;

    const category = document.createElement('p');
    category.className = 'catalog-card__category';
    category.textContent = item.category?.name || 'Academic collection';

    const title = document.createElement('h2');
    title.textContent = item[config.titleKey] || 'Untitled item';

    const description = document.createElement('p');
    description.className = 'catalog-card__description';
    description.textContent = item.description || 'No description is available.';

    const tags = document.createElement('div');
    tags.className = 'catalog-card__meta';
    config.tags(item).slice(0, 2).forEach((label, tagIndex) => tags.append(tag(label, tagIndex === 0)));

    const footer = document.createElement('footer');
    footer.className = 'catalog-card__footer';

    const stat = document.createElement('span');
    stat.className = 'catalog-card__stat';
    stat.textContent = config.stat(item);

    const open = document.createElement('button');
    open.className = 'catalog-card__open';
    open.type = 'button';
    open.dataset.detailId = item.id;
    open.textContent = 'View details';
    open.setAttribute('aria-label', `View details for ${title.textContent}`);

    footer.append(stat, open);
    card.append(category, title, description, tags, footer);
    return card;
}

export function renderCatalogGrid(container, items, config) {
    container.replaceChildren();

    if (!items.length) {
        const empty = document.createElement('div');
        empty.className = 'catalog-empty';
        const title = document.createElement('h2');
        title.textContent = 'No matching items';
        const message = document.createElement('p');
        message.textContent = 'Try clearing one or more filters.';
        empty.append(title, message);
        container.append(empty);
        return;
    }

    items.forEach((item) => container.append(catalogCard(item, config)));
}

function detailFact(label, value) {
    const wrapper = document.createElement('div');
    const term = document.createElement('dt');
    const description = document.createElement('dd');
    term.textContent = label;
    description.textContent = value;
    wrapper.append(term, description);
    return wrapper;
}

export function renderCatalogDetail(dialog, item, config, actionCompleted = false) {
    dialog.querySelector('[data-detail-category]').textContent = item.category?.name || 'Academic item';
    dialog.querySelector('[data-detail-title]').textContent = item[config.titleKey] || 'Untitled item';
    dialog.querySelector('[data-detail-description]').textContent = item.description || 'No description is available.';

    const facts = dialog.querySelector('[data-detail-facts]');
    facts.replaceChildren(...config.facts(item).map(([label, value]) => detailFact(label, value)));

    const link = dialog.querySelector('[data-detail-link]');
    const url = safeExternalUrl(item[config.urlKey]);
    link.hidden = !url;
    if (url) {
        link.href = url;
    }

    const action = config.action?.(item) || null;
    const actionButton = dialog.querySelector('[data-catalog-action]');
    actionButton.hidden = !action;
    actionButton.disabled = actionCompleted;
    actionButton.textContent = action ? (actionCompleted ? action.completedLabel : action.label) : '';

    return action;
}

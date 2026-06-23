function titleCase(value) {
    if (!value) {
        return 'All';
    }

    return String(value)
        .toLowerCase()
        .replace(/(^|_)([a-z])/g, (_, separator, letter) => `${separator ? ' ' : ''}${letter.toUpperCase()}`);
}

function rating(value) {
    const score = Number(value) || 0;
    return score > 0 ? `${score.toFixed(1)} rating` : 'Not rated';
}

function price(item) {
    return item.is_paid ? `BDT ${Number(item.price).toFixed(0)}` : 'Free';
}

export const catalogConfigs = {
    resources: {
        endpoint: 'resources',
        titleKey: 'title',
        urlKey: 'resource_url',
        categoryReference: 'resource_categories',
        categoryParameter: 'resource_category_id',
        extraLabel: 'Difficulty',
        extraParameter: 'difficulty_level',
        extraOptions: [
            ['', 'All levels'],
            ['BEGINNER', 'Beginner'],
            ['INTERMEDIATE', 'Intermediate'],
            ['ADVANCED', 'Advanced'],
        ],
        tone: 'green',
        tags: (item) => [titleCase(item.difficulty_level), item.academic_field?.name].filter(Boolean),
        stat: (item) => `${Number(item.save_count) || 0} saves, ${rating(item.average_rating)}`,
        facts: (item) => [
            ['Category', item.category?.name || 'General'],
            ['Academic field', item.academic_field?.name || 'All fields'],
            ['Task', item.task?.name || 'General study'],
            ['Difficulty', titleCase(item.difficulty_level)],
            ['Rating', rating(item.average_rating)],
            ['Saved by', `${Number(item.save_count) || 0} students`],
        ],
        action: (item) => ({
            endpoint: `resources/${item.id}/save`,
            label: 'Save resource',
            completedLabel: 'Resource saved',
        }),
    },
    tools: {
        endpoint: 'tools',
        titleKey: 'name',
        urlKey: 'website_url',
        categoryReference: 'tool_categories',
        categoryParameter: 'tool_category_id',
        extraLabel: 'Access',
        extraParameter: 'is_free',
        extraOptions: [
            ['', 'Free and paid'],
            ['1', 'Free'],
            ['0', 'Paid'],
        ],
        tone: 'blue',
        tags: (item) => [item.is_free ? 'Free' : 'Paid', item.task?.name].filter(Boolean),
        stat: (item) => item.academic_field?.name || 'All academic fields',
        facts: (item) => [
            ['Category', item.category?.name || 'General'],
            ['Academic field', item.academic_field?.name || 'All fields'],
            ['Best for', item.task?.name || 'General study'],
            ['Access', item.is_free ? 'Free' : 'Paid'],
        ],
        action: null,
    },
    templates: {
        endpoint: 'templates',
        titleKey: 'title',
        urlKey: 'template_url',
        categoryReference: 'template_categories',
        categoryParameter: 'template_category_id',
        extraLabel: 'Access',
        extraParameter: 'is_paid',
        extraOptions: [
            ['', 'Free and paid'],
            ['0', 'Free'],
            ['1', 'Paid'],
        ],
        tone: 'orange',
        tags: (item) => [price(item), item.university?.name].filter(Boolean),
        stat: (item) => `${Number(item.download_count) || 0} downloads, ${rating(item.average_rating)}`,
        facts: (item) => [
            ['Category', item.category?.name || 'General'],
            ['University', item.university?.name || 'Any university'],
            ['Academic field', item.academic_field?.name || 'All fields'],
            ['Access', price(item)],
            ['Rating', rating(item.average_rating)],
            ['Downloads', String(Number(item.download_count) || 0)],
        ],
        action: (item) => item.is_paid ? null : ({
            endpoint: `templates/${item.id}/save`,
            label: 'Save template',
            completedLabel: 'Template saved',
        }),
    },
};

export function getCatalogConfig(type) {
    return catalogConfigs[type] || null;
}

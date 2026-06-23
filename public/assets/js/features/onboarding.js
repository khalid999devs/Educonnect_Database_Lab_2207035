import { ApiError, apiClient } from '../core/api-client.js';
import {
    clearFeedback,
    clearFormErrors,
    setFormBusy,
    showApiErrors,
    showFeedback,
} from '../core/forms.js';

const goalTasks = {
    ASSIGNMENT: 'Assignment Writing',
    RESEARCH: 'Research Paper Search',
    LAB: 'Lab Report Preparation',
    CODING: 'Coding Practice',
    EXAM: 'Exam Preparation',
    PROJECT: 'Project Development',
};

function replaceOptions(select, items, placeholder, formatLabel) {
    select.replaceChildren();

    const prompt = document.createElement('option');
    prompt.value = '';
    prompt.textContent = placeholder;
    select.append(prompt);

    items.forEach((item) => {
        const option = document.createElement('option');
        option.value = item.id;
        option.textContent = formatLabel(item);
        select.append(option);
    });

    select.disabled = false;
}

function markSelectUnavailable(select, label) {
    const option = document.createElement('option');
    option.value = '';
    option.textContent = label;
    select.replaceChildren(option);
    select.disabled = true;
}

async function loadReferenceData(root, form) {
    const feedback = root.querySelector('[data-onboarding-feedback]');
    const retry = root.querySelector('[data-reference-retry]');
    const universitySelect = form.querySelector('[data-university-select]');
    const fieldSelect = form.querySelector('[data-field-select]');
    const submit = form.querySelector('[data-submit-button]');

    retry.hidden = true;
    submit.disabled = true;
    clearFeedback(feedback);

    try {
        const response = await apiClient.get('reference-data');
        replaceOptions(
            universitySelect,
            response.data.universities,
            'Select your university',
            (item) => item.city ? `${item.name}, ${item.city}` : item.name,
        );
        replaceOptions(
            fieldSelect,
            response.data.academic_fields,
            'Select your academic field',
            (item) => item.name,
        );
        submit.disabled = false;
    } catch (error) {
        markSelectUnavailable(universitySelect, 'Universities unavailable');
        markSelectUnavailable(fieldSelect, 'Academic fields unavailable');
        const message = error instanceof ApiError ? error.message : 'Unable to load profile options.';
        showFeedback(feedback, message);
        retry.hidden = false;
    }
}

function buildPayload(form) {
    const goalType = form.elements.goal_type.value;
    const payload = {
        university_id: Number(form.elements.university_id.value),
        academic_field_id: Number(form.elements.academic_field_id.value),
        department: form.elements.department.value.trim() || null,
        semester: form.elements.semester.value.trim() || null,
        skill_level: form.elements.skill_level.value,
        preferences: [],
    };

    if (goalType) {
        payload.preferences.push({
            goal_type: goalType,
            preference_key: 'primary_task',
            preference_value: goalTasks[goalType],
        });
    }

    return payload;
}

export function setupOnboarding() {
    const root = document.querySelector('[data-onboarding]');
    const form = root?.querySelector('[data-onboarding-form]');

    if (!root || !form) {
        return;
    }

    const feedback = root.querySelector('[data-onboarding-feedback]');
    const retry = root.querySelector('[data-reference-retry]');

    retry.addEventListener('click', () => loadReferenceData(root, form));
    loadReferenceData(root, form);

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearFormErrors(form);
        clearFeedback(feedback);
        setFormBusy(form, true, 'Saving profile');

        try {
            await apiClient.post('students/onboarding', buildPayload(form));
            window.location.assign(root.dataset.successUrl || '/app');
        } catch (error) {
            if (error instanceof ApiError && error.status === 409) {
                window.location.assign(root.dataset.successUrl || '/app');
                return;
            }

            showApiErrors(form, feedback, error);
        } finally {
            setFormBusy(form, false);
        }
    });
}

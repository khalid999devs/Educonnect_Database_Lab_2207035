export class ApiError extends Error {
    constructor(message, status = 0, errors = {}) {
        super(message);
        this.name = 'ApiError';
        this.status = status;
        this.errors = errors;
    }
}

export class ApiClient {
    constructor(baseUrl) {
        this.baseUrl = baseUrl.replace(/\/$/, '');
    }

    get(path, options = {}) {
        return this.request(path, { ...options, method: 'GET' });
    }

    post(path, body, options = {}) {
        return this.request(path, { ...options, method: 'POST', body });
    }

    put(path, body, options = {}) {
        return this.request(path, { ...options, method: 'PUT', body });
    }

    delete(path, options = {}) {
        return this.request(path, { ...options, method: 'DELETE' });
    }

    async request(path, options = {}) {
        const requestOptions = this.buildOptions(options);
        let response;

        try {
            response = await fetch(`${this.baseUrl}/${path.replace(/^\//, '')}`, requestOptions);
        } catch {
            throw new ApiError('Unable to reach Educonnect. Check your connection and try again.');
        }

        const payload = await this.parseResponse(response);

        if (!response.ok || payload?.success === false) {
            throw new ApiError(
                payload?.message || 'The request could not be completed.',
                response.status,
                payload?.errors || {},
            );
        }

        return payload;
    }

    buildOptions(options) {
        const headers = new Headers(options.headers || {});
        headers.set('Accept', 'application/json');
        headers.set('X-Requested-With', 'XMLHttpRequest');

        let body = options.body;
        if (body && !(body instanceof FormData) && typeof body !== 'string') {
            headers.set('Content-Type', 'application/json');
            body = JSON.stringify(body);
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (csrfToken) {
            headers.set('X-CSRF-TOKEN', csrfToken);
        }

        return {
            ...options,
            body,
            headers,
            credentials: 'same-origin',
        };
    }

    async parseResponse(response) {
        const contentType = response.headers.get('content-type') || '';

        if (response.status === 204) {
            return null;
        }

        if (contentType.includes('application/json')) {
            return response.json();
        }

        const text = await response.text();
        return text ? { message: text } : null;
    }
}

const baseUrl = document.querySelector('meta[name="api-base-url"]')?.content || '/api/v1';

export const apiClient = new ApiClient(baseUrl);

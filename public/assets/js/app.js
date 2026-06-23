/**
 * School e-Café - Main Application JS
 */
const ECafe = {
    csrfToken: document.querySelector('meta[name="csrf-token"]')?.content || '',

    init() {
        this.initDarkMode();
        this.initSpinner();
        this.initToasts();
        this.initSidebar();
    },

    initDarkMode() {
        const toggle = document.getElementById('darkModeToggle');
        const saved = localStorage.getItem('ecafe-theme') || 'light';
        document.documentElement.setAttribute('data-theme', saved);
        if (toggle) {
            toggle.addEventListener('click', () => {
                const current = document.documentElement.getAttribute('data-theme');
                const next = current === 'dark' ? 'light' : 'dark';
                document.documentElement.setAttribute('data-theme', next);
                localStorage.setItem('ecafe-theme', next);
            });
        }
    },

    initSpinner() {
        this.spinner = document.getElementById('global-spinner');
    },

    showSpinner() { this.spinner?.classList.add('active'); },
    hideSpinner() { this.spinner?.classList.remove('active'); },

    initToasts() {
        document.querySelectorAll('[data-toast]').forEach(el => {
            const toast = new bootstrap.Toast(el);
            toast.show();
        });
    },

    initSidebar() {
        const toggle = document.querySelector('.sidebar-toggle');
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.querySelector('.sidebar-overlay');
        toggle?.addEventListener('click', () => {
            sidebar?.classList.toggle('open');
            overlay?.classList.toggle('active');
        });
        overlay?.addEventListener('click', () => {
            sidebar?.classList.remove('open');
            overlay?.classList.remove('active');
        });
    },

    async fetch(url, options = {}) {
        this.showSpinner();
        try {
            const headers = {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': this.csrfToken,
                ...options.headers,
            };
            const response = await fetch(url, { ...options, headers });
            return await response.json();
        } finally {
            this.hideSpinner();
        }
    },

    toast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        if (!container) return;
        const id = 'toast-' + Date.now();
        const bg = type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-primary';
        container.insertAdjacentHTML('beforeend', `
            <div id="${id}" class="toast align-items-center text-white ${bg} border-0" role="alert">
                <div class="d-flex"><div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>
            </div>`);
        new bootstrap.Toast(document.getElementById(id)).show();
    }
};

document.addEventListener('DOMContentLoaded', () => ECafe.init());

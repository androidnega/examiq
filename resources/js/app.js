import './bootstrap';
import Alpine from 'alpinejs';
import { initLecturerSubmissionForms } from './submission-form';

document.addEventListener('alpine:init', () => {
    Alpine.data('examiqSidebar', () => ({
        collapsed: false,

        init() {
            try {
                // v2 key: default expanded so nav labels show until user collapses again.
                this.collapsed = localStorage.getItem('examiq_sidebar_collapsed_v2') === '1';
            } catch {
                this.collapsed = false;
            }
        },

        toggle() {
            this.collapsed = !this.collapsed;
            try {
                localStorage.setItem('examiq_sidebar_collapsed_v2', this.collapsed ? '1' : '0');
            } catch {
                /* ignore */
            }
        },
    }));

    Alpine.data('examiqProfileMenu', () => ({
        open: false,

        toggle() {
            this.open = !this.open;
        },

        close() {
            this.open = false;
        },
    }));
});

window.Alpine = Alpine;
Alpine.start();

const bootSubmissionUi = () => initLecturerSubmissionForms();
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootSubmissionUi);
} else {
    bootSubmissionUi();
}

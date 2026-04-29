function initDropzones(root = document) {
    root.querySelectorAll('.js-dropzone').forEach((zone) => {
        const input = zone.querySelector('input[type="file"]');
        const label = zone.querySelector('.js-dropzone-label');
        if (!input || !label) {
            return;
        }

        const setLabel = (text) => {
            label.textContent = text;
        };

        input.addEventListener('change', () => {
            const f = input.files?.[0];
            const def = label.getAttribute('data-default') ?? '';
            setLabel(f ? f.name : def);
        });

        zone.addEventListener('dragover', (e) => {
            e.preventDefault();
            zone.classList.add('border-gray-500', 'bg-white');
        });

        zone.addEventListener('dragleave', () => {
            zone.classList.remove('border-gray-500', 'bg-white');
        });

        zone.addEventListener('drop', (e) => {
            e.preventDefault();
            zone.classList.remove('border-gray-500', 'bg-white');
            const files = e.dataTransfer?.files;
            if (!files?.length || !input) {
                return;
            }
            const dt = new DataTransfer();
            dt.items.add(files[0]);
            input.files = dt.files;
            input.dispatchEvent(new Event('change', { bubbles: true }));
        });
    });
}

function renderErrors(container, errors) {
    container.innerHTML = '';
    if (!errors || typeof errors !== 'object') {
        container.textContent = 'Something went wrong.';
        return;
    }
    const ul = document.createElement('ul');
    ul.className = 'list-disc space-y-1 pl-5';
    Object.values(errors).forEach((msgs) => {
        (Array.isArray(msgs) ? msgs : [msgs]).forEach((msg) => {
            const li = document.createElement('li');
            li.textContent = msg;
            ul.appendChild(li);
        });
    });
    container.appendChild(ul);
}

function initAjaxSubmissionForms() {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!token) {
        return;
    }

    document.querySelectorAll('form[data-ajax-submit="true"]').forEach((form) => {
        const progress = form.querySelector('.js-upload-progress');
        const submitBtn = form.querySelector('.js-submit-btn');
        const errorBox = form.querySelector('[data-form-errors]');

        form.addEventListener('submit', (e) => {
            e.preventDefault();

            if (submitBtn) {
                submitBtn.disabled = true;
            }
            if (progress) {
                progress.style.width = '0%';
            }
            if (errorBox) {
                errorBox.classList.add('hidden');
                errorBox.innerHTML = '';
            }

            const xhr = new XMLHttpRequest();
            xhr.open(form.method.toUpperCase(), form.action);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.setRequestHeader('X-CSRF-TOKEN', token);

            xhr.upload.addEventListener('progress', (ev) => {
                if (!progress || !ev.lengthComputable) {
                    return;
                }
                const pct = Math.round((ev.loaded / ev.total) * 100);
                progress.style.width = `${pct}%`;
            });

            xhr.addEventListener('load', () => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                }

                if (xhr.status === 422) {
                    if (progress) {
                        progress.style.width = '0%';
                    }
                    try {
                        const data = JSON.parse(xhr.responseText);
                        if (errorBox) {
                            renderErrors(errorBox, data.errors);
                            errorBox.classList.remove('hidden');
                        }
                    } catch {
                        if (errorBox) {
                            errorBox.textContent = 'Validation failed.';
                            errorBox.classList.remove('hidden');
                        }
                    }
                    return;
                }

                if (xhr.status >= 200 && xhr.status < 300) {
                    if (progress) {
                        progress.style.width = '100%';
                    }
                    try {
                        const data = JSON.parse(xhr.responseText);
                        if (data.redirect) {
                            window.location.assign(data.redirect);
                            return;
                        }
                    } catch {
                        /* fall through */
                    }
                    window.location.reload();
                    return;
                }

                if (progress) {
                    progress.style.width = '0%';
                }
                if (errorBox) {
                    errorBox.textContent = 'Upload failed. Please try again.';
                    errorBox.classList.remove('hidden');
                }
            });

            xhr.addEventListener('error', () => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                }
                if (progress) {
                    progress.style.width = '0%';
                }
                if (errorBox) {
                    errorBox.textContent = 'Network error. Check your connection.';
                    errorBox.classList.remove('hidden');
                }
            });

            xhr.send(new FormData(form));
        });
    });
}

function initMultiStepForms(root = document) {
    root.querySelectorAll('form[data-form-wizard="true"]').forEach((form) => {
        const steps = Array.from(form.querySelectorAll('[data-wizard-step]'));
        if (steps.length === 0) {
            return;
        }

        const indicator = form.querySelector('[data-wizard-indicator]');
        let current = 0;

        steps.forEach((step, idx) => {
            if (step.querySelector('.text-red-600')) {
                current = idx;
            }
        });

        const setStep = (index) => {
            current = Math.max(0, Math.min(index, steps.length - 1));
            steps.forEach((step, idx) => {
                step.classList.toggle('hidden', idx !== current);
            });
            if (indicator) {
                indicator.textContent = `Step ${current + 1} of ${steps.length}`;
            }
        };

        const validateCurrentStep = () => {
            const controls = Array.from(
                steps[current].querySelectorAll('input, select, textarea')
            ).filter((el) => !el.disabled && el.type !== 'hidden');

            for (const control of controls) {
                if (!control.checkValidity()) {
                    control.reportValidity();
                    return false;
                }
            }

            return true;
        };

        form.querySelectorAll('[data-wizard-next]').forEach((btn) => {
            btn.addEventListener('click', () => {
                if (!validateCurrentStep()) {
                    return;
                }
                setStep(current + 1);
            });
        });

        form.querySelectorAll('[data-wizard-prev]').forEach((btn) => {
            btn.addEventListener('click', () => setStep(current - 1));
        });

        setStep(current);
    });
}

export function initLecturerSubmissionForms() {
    initDropzones();
    initMultiStepForms();
    initAjaxSubmissionForms();
}

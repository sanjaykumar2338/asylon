import './bootstrap';

import Alpine from 'alpinejs';
import Swal from 'sweetalert2';

window.Alpine = Alpine;
window.Swal = Swal;

Alpine.start();

const bindSwalConfirms = () => {
    if (!window.Swal) {
        return;
    }

    document
        .querySelectorAll('form[data-swal-confirm]')
        .forEach((form) => {
            if (form.dataset.swalBound === 'true') {
                return;
            }

            form.dataset.swalBound = 'true';

            form.addEventListener('submit', (event) => {
                if (form.dataset.swalBypass === 'true') {
                    return;
                }

                event.preventDefault();

                const title =
                    form.dataset.swalTitle || 'Are you sure?';
                const message =
                    form.dataset.swalMessage ||
                    'This action cannot be undone.';
                const icon = form.dataset.swalIcon || 'warning';
                const confirmButtonText =
                    form.dataset.swalConfirmButton || 'Yes, continue';
                const cancelButtonText =
                    form.dataset.swalCancelButton || 'Cancel';

                Swal.fire({
                    icon,
                    title,
                    text: message,
                    showCancelButton: true,
                    confirmButtonColor: '#2563eb',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText,
                    cancelButtonText,
                    focusCancel: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.dataset.swalBypass = 'true';
                        if (typeof form.requestSubmit === 'function') {
                            form.requestSubmit();
                        } else {
                            form.submit();
                        }
                        delete form.dataset.swalBypass;
                    }
                });
            });
        });
};

document.addEventListener('DOMContentLoaded', bindSwalConfirms);

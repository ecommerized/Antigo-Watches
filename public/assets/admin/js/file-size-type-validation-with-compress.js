(function () {
    'use strict';

    const lastValidFiles = new Map();
    const imageFileSizeData = $('.image-file-size-data-to-js') ;
    const maxUploadSizeForImage = imageFileSizeData.data('max-upload-size-for-image');
    const maxUploadSizeForFile = imageFileSizeData.data('max-upload-size-for-file');
    const postMaxSize = imageFileSizeData.data('post-max-size');
    function parseAccept(accept) {
        if (!accept) return [];
        return accept.split(',').map(s => s.trim().toLowerCase()).filter(Boolean);
    }

    function fileMatchesAccept(file, accepted) {
        if (!accepted || !accepted.length) return true;
        const fileType = (file.type || '').toLowerCase();
        const name = file.name || '';
        const ext = '.' + name.split('.').pop().toLowerCase();
        for (const a of accepted) {
            if (a.startsWith('.') && ext === a) return true;
            if (a.includes('/') && a.endsWith('/*') && fileType.startsWith(a.split('/')[0] + '/')) return true;
            if (a === fileType) return true;
            if (a === ext) return true;
        }
        return false;
    }

    function getMaxBytes(input) {
        const m = $(input).attr('data-maxFileSize');
        const file = input.files && input.files[0];
        let defaultFileSize = maxUploadSizeForImage;
        if (file) {
            const fileName = file.name.toLowerCase();
            const ext = fileName.split('.').pop();

            const fileExtensions = ['txt', 'rtf', 'doc', 'docx', 'pdf', 'odt', 'xls', 'xlsx', 'csv', 'ppt', 'pptx', 'log'];
            if (fileExtensions.includes(ext)) {
                defaultFileSize = maxUploadSizeForFile;
            }
        }
        const mb = (m && !isNaN(parseFloat(m))) ? parseFloat(m) : parseFloat(defaultFileSize);
        return mb * 1024 * 1024;
    }

    function showError(msg) {
        if (typeof toastr !== 'undefined' && toastr.error) toastr.error(msg);
        else { console.error(msg); alert(msg); }
    }

    function restorePreview(input, file) {
        const previewImg = document.querySelector(`[data-preview-for="${input.id}"]`);
        if (!previewImg) return;
        if (file) previewImg.src = URL.createObjectURL(file);
        else {
            const placeholder = input.getAttribute('data-placeholder');
            previewImg.src = placeholder || '';
        }
    }

    async function compressImage(file, quality = 0.7, maxWidth = 1024, maxHeight = 1024) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = (event) => {
                const img = new Image();
                img.onload = () => {
                    let width = img.width;
                    let height = img.height;

                    // Maintain aspect ratio
                    if (width > height) {
                        if (width > maxWidth) {
                            height = Math.round((height * maxWidth) / width);
                            width = maxWidth;
                        }
                    } else {
                        if (height > maxHeight) {
                            width = Math.round((width * maxHeight) / height);
                            height = maxHeight;
                        }
                    }

                    // Resize using canvas
                    const canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');

                    // Clear + draw
                    ctx.clearRect(0, 0, width, height);
                    ctx.drawImage(img, 0, 0, width, height);

                    // Convert to blob
                    canvas.toBlob(
                        (blob) => {
                            if (!blob) return reject(new Error('Compression failed'));
                            const compressedFile = new File([blob], file.name, { type: file.type });
                            resolve(compressedFile);
                        },
                        file.type || 'image/jpeg',
                        quality
                    );
                };
                img.onerror = reject;
                img.src = event.target.result;
            };
            reader.onerror = reject;
            reader.readAsDataURL(file);
        });
    }

    function validatingChangeHandler(ev) {
        if (!ev || !ev.target || ev.target.tagName !== 'INPUT' || ev.target.type !== 'file') return;

        const input = ev.target;
        const files = Array.from(input.files || []);
        if (!files.length) return;

        const accepted = parseAccept(input.getAttribute('accept') || '');
        const maxBytes = getMaxBytes(input);
        const maxPostBytes = parseFloat(postMaxSize) * 1024 * 1024;

        let validFile = null;
        let anyInvalid = false;

        for (const file of files) {
            const name = file.name || 'file';
            if (!fileMatchesAccept(file, accepted)) {
                showError(`"${name}" is not an allowed file type. Only "${input.getAttribute('accept')}" file types are accepted`);
                anyInvalid = true;
                break;
            }

            if (file.size > maxBytes) {
                const mb = Math.round((maxBytes / (1024 * 1024)) * 100) / 100;
                showError(`"${name}" exceeds the maximum file size limit of ${mb}MB.`);
                anyInvalid = true;
                break;
            }

            if (file.size > maxPostBytes) {
                const mb = Math.round((maxPostBytes / (1024 * 1024)) * 100) / 100;
                showError(`Maximum ${mb}MB can be uploaded.`);
                anyInvalid = true;
                break;
            }

            validFile = file;

        }

        if (anyInvalid) {
            const lastFile = lastValidFiles.get(input);
            if (lastFile) {
                const dt = new DataTransfer();
                dt.items.add(lastFile);
                input.files = dt.files;
            } else {
                input.value = '';
            }
            restorePreview(input, lastValidFiles.get(input));
            ev.stopImmediatePropagation();
            ev.preventDefault();
            return false;
        }

        if (validFile) {
            const dt = new DataTransfer();
            dt.items.add(validFile);
            input.files = dt.files;
            lastValidFiles.set(input, validFile);
            restorePreview(input, validFile);
        }

        return true;
    }

    document.addEventListener('change', validatingChangeHandler, true);

})();

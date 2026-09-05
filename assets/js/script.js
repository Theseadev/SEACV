document.addEventListener('DOMContentLoaded', function () {
    // ====== IMAGE PREVIEW ======
    const imageInputs = document.querySelectorAll('input[type="file"]');
    imageInputs.forEach(input => {
        input.addEventListener('change', function () {
            const previewDiv = this.nextElementSibling;
            if (previewDiv && previewDiv.classList.contains('image-preview')) {
                previewDiv.innerHTML = '';

                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.style.maxWidth = '100%';
                        img.style.height = 'auto';
                        img.style.borderRadius = '10px';
                        img.style.border = '1px solid var(--neon-cyan)';
                        previewDiv.appendChild(img);
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            }
        });
    });

    // ====== NAV TAB FILTER ======
    const navTabs = document.querySelectorAll('.nav-tab');
    const defaultCategory = document.querySelector('.nav-tab.active')?.dataset.category || '';
    filterProducts(defaultCategory);

    navTabs.forEach(tab => {
        tab.addEventListener('click', function () {
            const category = this.getAttribute('data-category');

            navTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            filterProducts(category);
        });
    });

    // ====== FORM VALIDATION ======
    const productForms = document.querySelectorAll('.product-form');
    productForms.forEach(form => {
        form.addEventListener('submit', function (e) {
            const nameInput = this.querySelector('input[name="name"]');
            const priceInput = this.querySelector('input[name="price"]');
            const categorySelect = this.querySelector('select[name="category"]');
            const imageInput = this.querySelector('input[name="image"]');

            let isValid = true;

            // Clear previous error messages
            const errorElements = this.querySelectorAll('.error-message');
            errorElements.forEach(el => el.remove());

            // Validate product name
            if (nameInput && nameInput.value.trim() === '') {
                isValid = false;
                showError(nameInput, 'Nama produk tidak boleh kosong');
            }

            // Validate price
            if (priceInput && (priceInput.value.trim() === '' || isNaN(priceInput.value) || parseFloat(priceInput.value) <= 0)) {
                isValid = false;
                showError(priceInput, 'Harga harus berupa angka lebih dari 0');
            }

            // Validate category
            if (categorySelect && categorySelect.value === '') {
                isValid = false;
                showError(categorySelect, 'Pilih kategori produk');
            }

            // Validate image on add form only
            if (imageInput && this.getAttribute('action')?.includes('addproduct.php')) {
                if (!imageInput.files || imageInput.files.length === 0) {
                    isValid = false;
                    showError(imageInput, 'Gambar produk wajib diupload');
                }
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    });

    // ====== MODAL IMAGE VIEWER ======
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');

    window.openImage = function (src) {
        if (modal && modalImg) {
            modal.style.display = 'block';
            modalImg.src = src;
        }
    };

    window.closeModal = function () {
        if (modal) {
            modal.style.display = 'none';
        }
    };

    if (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                closeModal();
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    }
});

// ====== FILTER FUNCTION ======
function filterProducts(category) {
    const productCards = document.querySelectorAll('.product-card');

    productCards.forEach(card => {
        const cardCategory = card.getAttribute('data-category');
        const isVisible = (category === '' || cardCategory === category);
        card.classList.toggle('hidden', !isVisible);
        card.style.display = isVisible ? 'block' : 'none';
    });
}

// ====== SHOW FORM ERROR ======
function showError(element, message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.style.color = 'red';
    errorDiv.style.fontSize = '0.8rem';
    errorDiv.style.marginTop = '5px';
    errorDiv.textContent = message;
    element.parentNode.appendChild(errorDiv);
}

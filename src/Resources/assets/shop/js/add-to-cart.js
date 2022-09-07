export default class AddToCart {
    /**
     * @param {HTMLElement} button
     */
    constructor(button) {
        /**
         * @type {HTMLElement}
         */
        this.button = button;

        /**
         * @type {Element|null}
         */
        this.buttonMessageContainer = this.button.querySelector('.button-message');
    }

    static maybeInit() {
        const button = document.getElementById('sylius_add_to_cart_requestSample');

        if (button === null) {
            return;
        }

        (new AddToCart(button)).init();
    }

    init() {
        if (document.getElementById('sylius-variants-pricing') !== null) {
            document.querySelectorAll('[name*="sylius_add_to_cart[cartItem][variant]"]').forEach(element => {
                element.addEventListener('change', event => this.handleProductOptionsChange(event));
            });
        } else if (document.getElementById('sylius-product-variants') !== null) {
            document.querySelectorAll('[name="sylius_add_to_cart[cartItem][variant]"]').forEach(element => {
                element.addEventListener('change', event => this.handleProductVariantsChange(event));
            });
        }

        this.button.addEventListener('click', event => this.requestASample(event));
    }

    /**
     * @param {Event} event
     */
    handleProductOptionsChange(event) {
        const pricingContainer = document.getElementById('sylius-variants-pricing');
        let optionPricingSelector = '';

        document.querySelectorAll('#sylius-product-adding-to-cart select[data-option]').forEach(element => {
            const option = element.options[element.options.selectedIndex];

            optionPricingSelector += `[data-${element.dataset.option}="${option.value}"]`;
        });

        const optionPricing = pricingContainer.querySelector(optionPricingSelector);

        if (optionPricing === null) {
            this.updateButtonDisplayText(this.button.dataset.sampleMessage ?? 'Request a Sample');

            return;
        }

        const isFreeSample = (optionPricing.dataset.freeSample ?? false) === 'yes';

        if (isFreeSample) {
            this.updateButtonDisplayText(this.button.dataset.freeSampleMessage ?? this.button.dataset.sampleMessage ?? 'Request a Sample');
        } else {
            const samplePrice = optionPricing.dataset.samplePrice ?? null;

            if (samplePrice !== null) {
                this.updateButtonDisplayText((this.button.dataset.paidSampleMessage ?? this.button.dataset.sampleMessage ?? 'Request a Sample').replace('%price%', samplePrice));
            } else {
                this.updateButtonDisplayText(this.button.dataset.sampleMessage ?? 'Request a Sample');
            }
        }
    }

    /**
     * @param {Event} event
     */
    handleProductVariantsChange(event) {
        const priceRow = event.currentTarget.closest('tr').querySelector('.sylius-product-variant-price');
        const isFreeSample = (priceRow.dataset.freeSample ?? false) === 'yes';

        if (isFreeSample) {
            this.updateButtonDisplayText(this.button.dataset.freeSampleMessage ?? this.button.dataset.sampleMessage ?? 'Request a Sample');
        } else {
            const samplePrice = priceRow.dataset.samplePrice ?? null;

            if (samplePrice !== null) {
                this.updateButtonDisplayText((this.button.dataset.paidSampleMessage ?? this.button.dataset.sampleMessage ?? 'Request a Sample').replace('%price%', samplePrice));
            } else {
                this.updateButtonDisplayText(this.button.dataset.sampleMessage ?? 'Request a Sample');
            }
        }
    }

    /**
     * @param {Event} event
     */
    async requestASample(event) {
        event.preventDefault();

        // The below replicates the `sylius-add-to-cart` JavaScript module logic, minus any framework coupling
        const validationElement = document.getElementById('sylius-cart-validation-error');
        const form = document.getElementById('sylius-product-adding-to-cart');
        const data = new FormData(form);

        data.append(this.button.name, '1');

        form.classList.add('loading');

        const response = await fetch(form.action, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
            method: form.method ?? 'POST',
            body: data,
        });

        if (response.ok) {
            validationElement.classList.add('hidden');

            const redirectUrl = form.dataset.redirect ?? null;

            if (redirectUrl) {
                window.location.href = redirectUrl;
            } else {
                window.location.reload();
            }
        } else {
            const responseBody = await response.json();

            let validationMessage = '';

            Object.entries(responseBody.errors.errors).forEach(([, message]) => {
                validationMessage += message;
            });

            validationElement.innerHTML = validationMessage;

            validationElement.classList.remove('hidden');
            form.classList.remove('loading');
        }
    }

    /**
     * @param {String} text
     */
    updateButtonDisplayText(text) {
        if (this.buttonMessageContainer !== null) {
            this.buttonMessageContainer.innerText = text;
        } else {
            this.button.innerText = text;
        }
    }
}

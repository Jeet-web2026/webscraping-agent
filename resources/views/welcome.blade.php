<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Web Research</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="min-h-screen bg-[#0b1220] text-white">

    <div
        x-data="researchForm()"
        class="min-h-screen">

        {{-- HEADER --}}
        <header class="border-b border-slate-800 bg-[#111827]">
            <div class="mx-auto flex h-[68px] max-w-6xl items-center justify-between px-6">

                <div class="flex justify-between items-center w-full">
                    <h1 class="text-[17px] font-semibold tracking-tight">
                        Web Research
                    </h1>
                    <div class="relative" id="token-menu-root">
                        <button type="button" id="token-menu-btn" aria-label="More options"
                            class="p-1.5 rounded-md text-gray-500 cursor-pointer">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                <circle cx="12" cy="5" r="1.8" />
                                <circle cx="12" cy="12" r="1.8" />
                                <circle cx="12" cy="19" r="1.8" />
                            </svg>
                        </button>

                        <div id="token-menu"
                            class="hidden absolute right-0 mt-5 w-52 rounded bg-slate-900 border border-gray-800 shadow-lg py-1 z-40 text-white">
                            <button type="button" id="token-open"
                                class="w-full text-left px-3 py-2 text-sm cursor-pointer">
                                Create / Update API token
                            </button>
                        </div>
                    </div>
                </div>

                <div id="token-modal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/40 p-4">
                    <form method="POST" action="#" class="w-full max-w-3xl max-h-[90vh] overflow-y-auto rounded-xl  bg-slate-900 border border-gray-800 p-5 shadow-xl">
                        @csrf

                        <h2 class="text-base font-semibold">Create / Update API token</h2>

                        {{-- one group per token is rendered here by JS --}}
                        <div id="token-fields" class="flex gap-2 items-center w-full"></div>

                        <div class="mt-5 flex justify-end gap-2">
                            <button type="button" id="token-close"
                                class="rounded-md px-3 py-2 text-sm text-gray-200 bg-gray-500">
                                Close
                            </button>
                            <button type="submit"
                                class="rounded-md bg-gray-900 px-3 py-2 text-sm text-white bg-green-800">
                                Save
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </header>


        <main class="mx-auto max-w-6xl px-6 py-7">
            @session('success')
            <div class="mb-5 rounded-md border border-green-600 bg-green-600/10 px-4 py-3 text-sm text-green-400 mb-5">
                {{ session('success') }}
            </div>
            @endsession

            <form
                x-data="researchForm()"
                @submit.prevent="submitForm($event)"
                action="{{ route('ai-request.store') }}"
                method="POST"
                class="space-y-5 overflow-hidden">

                @csrf

                <section
                    class="rounded-xl border border-slate-800 bg-[#111827]">

                    <div class="border-b border-slate-800 px-5 py-3.5 flex items-center gap-1">
                        <p class="text-[15px] font-semibold uppercase tracking-[0.08em] text-blue-400">
                            Preferred Sources
                        </p>
                        <span class="text-red-500">*</span>
                    </div>


                    <div class="space-y-5 p-5">


                        {{-- SOURCES --}}
                        <div>

                            <div class="flex flex-wrap gap-2">

                                @foreach([
                                'search' => 'Search Engines',
                                'ecommerce' => 'E-commerce'
                                ] as $value => $label)

                                <label class="cursor-pointer">

                                    <input
                                        type="radio"
                                        name="source"
                                        value="{{ $value }}"
                                        class="peer sr-only">

                                    <span class="inline-flex h-8 items-center rounded-md border border-slate-700 bg-slate-900 px-3 text-[11px] text-slate-400 transition peer-checked:border-blue-500 peer-checked:bg-blue-500/10 peer-checked:text-blue-300">
                                        {{ $label }}
                                    </span>

                                </label>

                                @endforeach

                            </div>

                        </div>

                    </div>

                </section>

                {{-- ========================================== --}}
                {{-- SEARCH TYPE --}}
                {{-- ========================================== --}}

                <div class="flex flex-col lg:flex-row w-full gap-3">
                    <section class="rounded-xl border border-slate-800 bg-[#111827] w-1/3">

                        <div class="border-b border-slate-800 px-5 py-3.5 flex items-center gap-1">
                            <p class="text-[15px] font-semibold uppercase tracking-[0.08em] text-blue-400">
                                Search Type
                            </p>
                            <span class="text-red-500">*</span>
                        </div>

                        <div class="p-4 w-full">

                            <select
                                name="type"
                                class="w-full rounded-lg border border-slate-700 bg-slate-900 px-4 py-3 text-[13px] font-semibold text-slate-100 transition focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">

                                <option value="service">Service</option>
                                <option value="seller">Seller</option>

                            </select>

                        </div>

                    </section>
                    <section class="rounded-xl border border-slate-800 bg-[#111827] w-2/3">

                        <div class="border-b border-slate-800 px-5 py-3.5 flex items-center gap-1">
                            <p class="text-[15px] font-semibold uppercase tracking-[0.08em] text-blue-400">
                                Product Information
                            </p>
                            <span class="text-red-500">*</span>
                        </div>

                        <div class="flex flex-col lg:flex-row">
                            <div class="p-4 w-full pe-0">

                                {{-- Used for Search Engines --}}
                                <input type="text" id="product_category_text" name="product_category"
                                    value="{{ old('product_category') }}"
                                    placeholder="Category"
                                    class="w-full rounded-lg border border-slate-700 bg-slate-900 px-4 py-3 text-[13px] font-semibold text-slate-100 transition focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">

                                {{-- Used for E-commerce --}}
                                <select id="product_category_select" name="product_category" disabled
                                    class="hidden w-full rounded-lg border border-slate-700 bg-slate-900 px-4 py-3 text-[13px] font-semibold text-slate-100 transition focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <option class="bg-slate-900 text-sm" value="">Select category</option>
                                    @foreach (config('amazon.categories') as $slug => $label)
                                    <option class="bg-slate-900 text-sm" value="{{ $slug }}" @selected(old('product_category')===$slug)>
                                        {{ $label }}
                                    </option>
                                    @endforeach
                                </select>

                            </div>
                            <div class="p-4 w-full">

                                <input type="text" id="product_name" name="product_name" placeholder="Product Name" class="w-full rounded-lg border border-slate-700 bg-slate-900 px-4 py-3 text-[13px] font-semibold text-slate-100 transition focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">

                            </div>
                        </div>

                    </section>
                </div>


                {{-- ========================================== --}}
                {{-- PRODUCT --}}
                {{-- ========================================== --}}

                <section
                    class="rounded-xl border border-slate-800 bg-[#111827]">

                    <div class="border-b border-slate-800 px-5 py-3.5">
                        <p class="text-[15px] font-semibold uppercase tracking-[0.08em] text-blue-400">
                            Search Location
                        </p>
                    </div>


                    <div class="space-y-2 p-5">


                        {{-- LOCATION --}}
                        <div>

                            <div class="grid grid-cols-5 gap-3">

                                <div>
                                    <label class="field-label">
                                        Country
                                        <span class="text-red-500">*</span>
                                    </label>

                                    <select name="country" class="field-input" id="country_field">
                                        <option value="">Select country</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="field-label">
                                        State
                                        <span class="text-red-500">*</span>
                                    </label>

                                    <select name="state" class="field-input" id="state_field">
                                        <option value="">Select state</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="field-label">
                                        District
                                        <span class="text-red-500">*</span>
                                    </label>

                                    <select name="district" class="field-input" id="district_field">
                                        <option value="">Select district</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="field-label">
                                        PIN Code
                                        <span class="text-red-500">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        name="pincode"
                                        maxlength="6"
                                        placeholder="700024"
                                        class="field-input">
                                </div>

                                <div>
                                    <label class="field-label">
                                        Block
                                    </label>

                                    <input
                                        type="text"
                                        name="block"
                                        placeholder="Garden Reach"
                                        class="field-input">
                                </div>


                            </div>

                        </div>
                        <span class="text-gray-500 text-xs">Note: If you give block information, it will be used to refine the search results.</span>

                    </div>

                </section>


                {{-- ========================================== --}}
                {{-- SUBMIT --}}
                {{-- ========================================== --}}

                <div class="flex items-center justify-between rounded-xl border border-slate-800 bg-[#111827] px-5 py-4">
                    <div>
                        <p class="text-[13px] font-medium" x-text="polling ? 'Processing…' : 'Ready to search?'"></p>
                        <p class="mt-0.5 text-[11px] text-slate-500" x-show="!polling">
                            AI will retrieve and process the requested information.
                        </p>
                        <p class="mt-0.5 text-[11px] text-red-400" x-show="errorMessage" x-text="errorMessage"></p>
                    </div>

                    <button
                        type="submit"
                        :disabled="loading || polling"
                        class="h-9 rounded-md bg-blue-600 px-5 text-[15px] cursor-pointer font-semibold text-white transition hover:bg-blue-500 disabled:opacity-50">
                        <span x-text="loading ? 'Submitting…' : (polling ? 'Working…' : 'Search')"></span>
                    </button>
                </div>

                <div x-show="downloadUrl" class="rounded-xl border border-emerald-800 bg-emerald-500/10 px-5 py-4">
                    <a :href="downloadUrl" target="_blank" class="text-emerald-300 font-semibold text-sm">
                        📄 Download your document
                    </a>
                </div>

                <section class="flex gap-2.5 items-center image_show overflow-x-auto">

                </section>

            </form>

        </main>

    </div>


    <style>
        body {
            font-family: 'Figtree', sans-serif;
        }

        .field-label {
            display: block;
            margin-bottom: 6px;
            font-size: 13px;
            font-weight: 500;
            color: rgb(148 163 184);
        }

        .field-input {
            width: 100%;
            height: 40px;
            border-radius: 7px;
            border: 1px solid rgb(51 65 85);
            background: rgb(30 41 59 / 0.55);
            padding: 0 12px;
            font-size: 12px;
            color: rgb(226 232 240);
            outline: none;
            transition: 150ms ease;
        }

        .field-input::placeholder {
            color: rgb(100 116 139);
        }

        .field-input:focus {
            border-color: rgb(59 130 246);
            box-shadow: 0 0 0 2px rgb(59 130 246 / 0.08);
        }

        select.field-input {
            cursor: pointer;
        }
    </style>


    <script>
        function researchForm() {
            return {
                type: 'product',
                loading: false,
                polling: false,
                status: null,
                downloadUrl: null,
                errorMessage: null,
                requestId: null,

                async submitForm(event) {
                    this.loading = true;
                    this.errorMessage = null;
                    this.downloadUrl = null;
                    this.status = null;

                    const form = event.target;
                    const formData = new FormData(form);

                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: formData,
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            throw new Error(`Request failed: ${data.message}`);
                        }

                        this.requestId = data.id;
                        this.status = data.status;

                        this.startPolling();

                    } catch (err) {
                        this.errorMessage = err.message;
                    } finally {
                        this.loading = false;
                    }
                },

                startPolling() {
                    if (this.polling) return;
                    this.polling = true;
                    this.pollStatus();
                },

                async pollStatus() {
                    if (!this.requestId) return;

                    try {
                        const response = await fetch(`/research-requests/${this.requestId}`, {
                            method: 'GET',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                        });

                        const data = await response.json();
                        this.status = data.status;
                        console.log(data)

                        if (data.status === 'completed') {
                            this.downloadUrl = data.download_url;
                            let html = '';
                            const images = data.image_urls || [];
                            const links = data.links || [];

                            images.forEach((imageUrl, i) => {
                                const link = links[i] || imageUrl;

                                html += `
                                    <a href="${link}" target="_blank" rel="noopener noreferrer" class="flex-shrink-0">
                                        <img src="${imageUrl}" alt="Image" loading="lazy"
                                            class="h-24 w-24 object-cover rounded-md border border-slate-700">
                                    </a>
                                `;
                            });

                            document.querySelector('.image_show').innerHTML = html;

                            this.polling = false;
                            return;
                        }

                        if (data.status === 'failed') {
                            this.errorMessage = data.error || 'Request failed.';
                            this.polling = false;
                            return;
                        }

                        setTimeout(() => this.pollStatus(), 3000);

                    } catch (err) {
                        this.errorMessage = err;
                        this.polling = false;
                    }
                },
            };
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadCountries();
        });

        async function loadCountries() {
            const select = document.getElementById('country_field');
            const optionClass = 'bg-slate-900 text-sm';

            const setIndiaDefault = () => {
                select.innerHTML =
                    `<option class="${optionClass}" value="">Select country</option>` +
                    `<option class="${optionClass}" selected data-value="IN" value="India">India</option>`;
                select.dispatchEvent(new Event('change'));
            };

            try {
                const response = await fetch('https://api.countrystatecity.in/v1/countries', {
                    headers: {
                        'X-CSCAPI-KEY': 'b0f3fbe2ab09c333b8fd2d0a64da260ff657f320ca3e3a5db7d1f987d8251829'
                    }
                });

                if (response.status === 429) {
                    console.warn('Rate limit hit (429). Defaulting to India.');
                    setIndiaDefault();
                    return;
                }

                if (!response.ok) throw new Error(`HTTP ${response.status}`);

                const countries = await response.json();

                select.innerHTML =
                    `<option class="${optionClass}" value="">Select country</option>` +
                    countries.map(c =>
                        `<option class="${optionClass}" ${c.iso2 === 'IN' ? 'selected' : ''} data-value="${c.iso2}" value="${c.name}">${c.name}</option>`
                    ).join('');

                select.dispatchEvent(new Event('change'));
            } catch (err) {
                console.error('Failed to load countries:', err);
                setIndiaDefault();
            }
        }

        const OPT_CLASS = 'bg-slate-900 text-sm';
        const API_BASE = 'https://api.countrystatecity.in/v1';
        const API_HEADERS = {
            'X-CSCAPI-KEY': 'b0f3fbe2ab09c333b8fd2d0a64da260ff657f320ca3e3a5db7d1f987d8251829'
        };

        document.getElementById('country_field').addEventListener('change', async (e) => {
            const iso2 = e.target.selectedOptions[0]?.dataset.value;
            const stateSelect = document.getElementById('state_field');
            stateSelect.setAttribute('disabled', 'disabled');
            stateSelect.length = 1;

            if (!iso2) return;

            const setWestBengalDefault = () => {
                stateSelect.innerHTML =
                    `<option class="${OPT_CLASS}" value="">Select state</option>` +
                    `<option class="${OPT_CLASS}" selected data-value="WB" value="West Bengal">West Bengal</option>`;
                stateSelect.removeAttribute('disabled');
                stateSelect.dispatchEvent(new Event('change')); // triggers city load
            };

            try {
                const response = await fetch(`${API_BASE}/countries/${iso2}/states`, {
                    headers: API_HEADERS
                });

                if (response.status === 429) {
                    console.warn('Rate limit hit (429) on states.');
                    if (iso2 === 'IN') setWestBengalDefault();
                    else stateSelect.removeAttribute('disabled');
                    return;
                }

                if (!response.ok) throw new Error(`HTTP ${response.status}`);

                const states = await response.json();

                stateSelect.innerHTML =
                    `<option class="${OPT_CLASS}" value="">Select state</option>` +
                    states.map(s =>
                        `<option class="${OPT_CLASS}" ${iso2 === 'IN' && s.iso2 === 'WB' ? 'selected' : ''} data-value="${s.iso2}" value="${s.name}">${s.name}</option>`
                    ).join('');
                stateSelect.removeAttribute('disabled');

                // Optional: auto-load cities when West Bengal is preselected
                if (stateSelect.value) stateSelect.dispatchEvent(new Event('change'));
            } catch (err) {
                console.error('Failed to load states:', err);
                if (iso2 === 'IN') setWestBengalDefault();
                else stateSelect.removeAttribute('disabled');
            }
        });

        document.getElementById('state_field').addEventListener('change', async (e) => {
            const stateCode = e.target.selectedOptions[0]?.dataset.value;
            const countryCode = document.getElementById('country_field').selectedOptions[0]?.dataset.value;
            const citySelect = document.getElementById('district_field');
            citySelect.setAttribute('disabled', 'disabled');
            citySelect.length = 1;

            if (!countryCode || !stateCode) return;

            const setKolkataDefault = () => {
                citySelect.innerHTML =
                    `<option class="${OPT_CLASS}" value="">Select district</option>` +
                    `<option class="${OPT_CLASS}" selected value="Kolkata">Kolkata</option>`;
                citySelect.removeAttribute('disabled');
            };

            const isWestBengal = countryCode === 'IN' && stateCode === 'WB';

            try {
                const response = await fetch(
                    `${API_BASE}/countries/${countryCode}/states/${stateCode}/cities`, {
                        headers: API_HEADERS
                    }
                );

                if (response.status === 429) {
                    console.warn('Rate limit hit (429) on cities.');
                    if (isWestBengal) setKolkataDefault();
                    else citySelect.removeAttribute('disabled');
                    return;
                }

                if (!response.ok) throw new Error(`HTTP ${response.status}`);

                const cities = await response.json();

                citySelect.innerHTML =
                    `<option class="${OPT_CLASS}" value="">Select district</option>` +
                    cities.map(c =>
                        `<option class="${OPT_CLASS}" ${isWestBengal && c.name === 'Kolkata' ? 'selected' : ''} value="${c.name}">${c.name}</option>`
                    ).join('');
                citySelect.removeAttribute('disabled');
            } catch (err) {
                console.error('Failed to load cities:', err);
                if (isWestBengal) setKolkataDefault();
                else citySelect.removeAttribute('disabled');
            }
        });

        const sourceRadios = document.querySelectorAll('input[name="source"]');
        const categoryText = document.getElementById('product_category_text');
        const categorySelect = document.getElementById('product_category_select');

        function toggleCategoryField() {
            const isEcommerce = document.querySelector('input[name="source"]:checked')?.value === 'ecommerce';

            categoryText.classList.toggle('hidden', isEcommerce);
            categoryText.disabled = isEcommerce;

            categorySelect.classList.toggle('hidden', !isEcommerce);
            categorySelect.disabled = !isEcommerce;
        }

        sourceRadios.forEach(radio => radio.addEventListener('change', toggleCategoryField));
        toggleCategoryField();

        function fetchApiTokens() {
            return fetch('/api-tokens', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    },
                })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error(`Request failed with status ${response.status}`);
                    }
                    return response.json();
                })
                .catch((error) => {
                    console.error('Failed to fetch API tokens:', error);
                    return null;
                });
        }

        const root = document.getElementById('token-menu-root');
        const menu = document.getElementById('token-menu');
        const modal = document.getElementById('token-modal');
        const fieldsBox = document.getElementById('token-fields');

        const closeMenu = () => menu.classList.add('hidden');
        const closeModal = () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        };

        const inputClass = 'mt-1 w-full rounded-md border border-gray-600 px-3 py-2 text-sm';

        function buildTokenGroup(item, index) {
            const group = document.createElement('div');
            group.className = 'w-1/2';

            group.innerHTML = `
        <input type="hidden" name="tokens[${index}][id]">

        <label class="mt-4 block text-sm font-medium text-white">
            Name
            <input type="text" name="tokens[${index}][name]" required class="${inputClass}">
        </label>

        <label class="mt-4 block text-sm font-medium text-white">
            Token
            <input type="text" name="tokens[${index}][token]" required class="${inputClass}">
        </label>

        <label class="mt-4 block text-sm font-medium text-white">
            Token Source
            <input type="text" name="tokens[${index}][token_source]" class="${inputClass}">
        </label>
    `;

            // set values via .value so API data is never injected as HTML
            ['id', 'name', 'token', 'token_source'].forEach((field) => {
                group.querySelector(`[name="tokens[${index}][${field}]"]`).value = item[field] ?? '';
            });

            return group;
        }

        function fillTokenForm(items) {
            fieldsBox.innerHTML = '';

            // if there is no data yet, show one empty group
            (items.length ? items : [{}]).forEach((item, index) => {
                fieldsBox.appendChild(buildTokenGroup(item, index));
            });
        }

        document.getElementById('token-menu-btn').addEventListener('click', () => menu.classList.toggle('hidden'));

        document.getElementById('token-open').addEventListener('click', () => {
            closeMenu();
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            fetchApiTokens().then((data) => {
                // works for a list response or a single object
                const items = Array.isArray(data) ? data : (data ? [data] : []);

                fillTokenForm(items);
            });
        });

        document.getElementById('token-close').addEventListener('click', closeModal);
        modal.addEventListener('mousedown', (e) => {
            if (e.target === modal) closeModal();
        });
        document.addEventListener('mousedown', (e) => {
            if (!root.contains(e.target)) closeMenu();
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeMenu();
                closeModal();
            }
        });
    </script>

</body>

</html>
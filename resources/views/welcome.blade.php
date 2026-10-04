<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>AI Research</title>

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

                <div>
                    <h1 class="text-[17px] font-semibold tracking-tight">
                        AI Research
                    </h1>

                    <p class="text-[11px] text-slate-400">
                        Internet & customer intelligence
                    </p>
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

                {{-- ========================================== --}}
                {{-- SEARCH TYPE --}}
                {{-- ========================================== --}}

                <div class="flex flex-col lg:flex-row w-full gap-3">
                    <section class="rounded-xl border border-slate-800 bg-[#111827] w-1/3">

                        <div class="border-b border-slate-800 px-5 py-3.5">
                            <p class="text-[15px] font-semibold uppercase tracking-[0.08em] text-blue-400">
                                Search Type
                            </p>
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

                        <div class="border-b border-slate-800 px-5 py-3.5">
                            <p class="text-[15px] font-semibold uppercase tracking-[0.08em] text-blue-400">
                                Product Information
                            </p>
                        </div>

                        <div class="flex flex-col lg:flex-row">
                            <div class="p-4 w-full">

                                <input type="text" id="product_category" name="product_category" placeholder="Category" class="w-full rounded-lg border border-slate-700 bg-slate-900 px-4 py-3 text-[13px] font-semibold text-slate-100 transition focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">

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


                    <div class="space-y-5 p-5">


                        {{-- LOCATION --}}
                        <div>

                            <div class="grid grid-cols-5 gap-3">

                                <div>
                                    <label class="field-label">
                                        Country
                                    </label>

                                    <select name="country" class="field-input" id="country_field">
                                        <option value="">Select country</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="field-label">
                                        State
                                    </label>

                                    <select name="state" class="field-input" id="state_field">
                                        <option value="">Select state</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="field-label">
                                        District
                                    </label>

                                    <select name="district" class="field-input" id="district_field">
                                        <option value="">Select district</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="field-label">
                                        PIN Code
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

                    </div>

                </section>

                <section
                    class="rounded-xl border border-slate-800 bg-[#111827]">

                    <div class="border-b border-slate-800 px-5 py-3.5">
                        <p class="text-[15px] font-semibold uppercase tracking-[0.08em] text-blue-400">
                            Preferred Sources
                        </p>
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
                        class="h-9 rounded-md bg-blue-600 px-5 text-[15px] font-semibold text-white transition hover:bg-blue-500 disabled:opacity-50">
                        <span x-text="loading ? 'Submitting…' : (polling ? 'Working…' : 'Search with AI')"></span>
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

                        if (!response.ok) {
                            throw new Error(`Request failed: ${response.status}`);
                        }

                        const data = await response.json();
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
                            (data.image_urls || []).forEach(image_url => {
                                html += `
                                    <img src="${image_url}" alt="Product image" class="h-40 w-full rounded-lg object-cover border border-slate-700" loading="lazy">
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
            try {
                const response = await fetch('https://api.countrystatecity.in/v1/countries', {
                    headers: {
                        'X-CSCAPI-KEY': 'b0f3fbe2ab09c333b8fd2d0a64da260ff657f320ca3e3a5db7d1f987d8251829'
                    }
                });

                if (!response.ok) throw new Error(`HTTP ${response.status}`);

                const countries = await response.json();
                const select = document.getElementById('country_field');

                select.innerHTML = '<option class="bg-slate-900 text-sm" value="">Select country</option>' +
                    countries.map(c => `<option class="bg-slate-900 text-sm" ${c.iso2 === 'IN' ? 'selected' : ''} data-value="${c.iso2}" value="${c.name}">${c.name}</option>`).join('');

                select.dispatchEvent(new Event('change'));
            } catch (err) {
                console.error('Failed to load countries:', err);
            }
        }

        document.getElementById('country_field').addEventListener('change', async (e) => {
            const iso2 = e.target.selectedOptions[0]?.dataset.value; 
            const stateSelect = document.getElementById('state_field');
            stateSelect.setAttribute('disabled', 'disabled');
            stateSelect.length = 1;

            if (!iso2) return;

            const response = await fetch(
                `https://api.countrystatecity.in/v1/countries/${iso2}/states`, {
                    headers: {
                        'X-CSCAPI-KEY': 'b0f3fbe2ab09c333b8fd2d0a64da260ff657f320ca3e3a5db7d1f987d8251829'
                    }
                }
            );
            const states = await response.json();

            stateSelect.innerHTML = '<option class="bg-slate-900 text-sm" value="">Select state</option>' +
                states.map(s => `<option class="bg-slate-900 text-sm" data-value="${s.iso2}" value="${s.name}">${s.name}</option>`).join('');
            stateSelect.removeAttribute('disabled');
        });

        document.getElementById('state_field').addEventListener('change', async (e) => {
            const stateCode = e.target.selectedOptions[0]?.dataset.value; 
            const countryCode = document.getElementById('country_field').selectedOptions[0]?.dataset.value;
            const citySelect = document.getElementById('district_field');
            citySelect.setAttribute('disabled', 'disabled');
            citySelect.length = 1;

            if (!countryCode || !stateCode) return;

            const response = await fetch(
                `https://api.countrystatecity.in/v1/countries/${countryCode}/states/${stateCode}/cities`, {
                    headers: {
                        'X-CSCAPI-KEY': 'b0f3fbe2ab09c333b8fd2d0a64da260ff657f320ca3e3a5db7d1f987d8251829'
                    }
                }
            );
            const cities = await response.json();

            citySelect.innerHTML = '<option class="bg-slate-900 text-sm" value="">Select district</option>' +
                cities.map(c => `<option class="bg-slate-900 text-sm" value="${c.name}">${c.name}</option>`).join('');
            citySelect.removeAttribute('disabled');
        });
    </script>

</body>

</html>
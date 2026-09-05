<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

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

                <div class="rounded-md border border-slate-700 bg-slate-800/70 px-3 py-1.5">
                    <span class="text-[11px] font-medium text-slate-300">
                        AI Powered
                    </span>
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
                action="{{ route('ai-request.store') }}"
                method="POST"
                class="space-y-5">

                @csrf

                {{-- ========================================== --}}
                {{-- SEARCH TYPE --}}
                {{-- ========================================== --}}

                <section class="rounded-xl border border-slate-800 bg-[#111827]">

                    <div class="border-b border-slate-800 px-5 py-3.5">
                        <p class="text-[15px] font-semibold uppercase tracking-[0.08em] text-blue-400">
                            Research Type
                        </p>
                    </div>

                    <div class="grid grid-cols-3 gap-3 p-4">

                        {{-- PRODUCT --}}
                        <button
                            type="button"
                            @click="type = 'product'"
                            :class="type === 'product'
                            ? 'border-blue-500 bg-blue-500/10'
                            : 'border-slate-700 bg-slate-900 hover:border-slate-600'"
                            class="rounded-lg border px-4 py-3 text-left transition">
                            <div class="flex items-center justify-between">

                                <div>
                                    <p class="text-[13px] font-semibold">
                                        Product
                                    </p>

                                    <p class="mt-0.5 text-[11px] text-slate-400">
                                        Product, price & seller information
                                    </p>
                                </div>

                                <span
                                    x-show="type === 'product'"
                                    class="h-2 w-2 rounded-full bg-blue-400"></span>

                            </div>
                        </button>


                        {{-- SERVICE --}}
                        <button
                            type="button"
                            @click="type = 'service'"
                            :class="type === 'service'
                            ? 'border-emerald-500 bg-emerald-500/10'
                            : 'border-slate-700 bg-slate-900 hover:border-slate-600'"
                            class="rounded-lg border px-4 py-3 text-left transition">
                            <div class="flex items-center justify-between">

                                <div>
                                    <p class="text-[13px] font-semibold">
                                        Service
                                    </p>

                                    <p class="mt-0.5 text-[11px] text-slate-400">
                                        Find services by PIN code
                                    </p>
                                </div>

                                <span
                                    x-show="type === 'service'"
                                    class="h-2 w-2 rounded-full bg-emerald-400"></span>

                            </div>
                        </button>


                        {{-- CUSTOMER --}}
                        <button
                            type="button"
                            @click="type = 'customer'"
                            :class="type === 'customer'
                            ? 'border-purple-500 bg-purple-500/10'
                            : 'border-slate-700 bg-slate-900 hover:border-slate-600'"
                            class="rounded-lg border px-4 py-3 text-left transition">
                            <div class="flex items-center justify-between">

                                <div>
                                    <p class="text-[13px] font-semibold">
                                        Customer + AI
                                    </p>

                                    <p class="mt-0.5 text-[11px] text-slate-400">
                                        Database analysis & AI answers
                                    </p>
                                </div>

                                <span
                                    x-show="type === 'customer'"
                                    class="h-2 w-2 rounded-full bg-purple-400"></span>

                            </div>
                        </button>

                    </div>

                    <input
                        type="hidden"
                        name="type"
                        :value="type">

                </section>


                {{-- ========================================== --}}
                {{-- PRODUCT --}}
                {{-- ========================================== --}}

                <section
                    x-show="type === 'product'"
                    class="rounded-xl border border-slate-800 bg-[#111827]">

                    <div class="border-b border-slate-800 px-5 py-3.5">
                        <p class="text-[15px] font-semibold uppercase tracking-[0.08em] text-blue-400">
                            Product Information
                        </p>
                    </div>


                    <div class="space-y-5 p-5">

                        {{-- Product fields --}}
                        <div class="grid grid-cols-2 gap-4">

                            <div>
                                <label class="field-label">
                                    Product / Brand
                                </label>

                                <input
                                    type="text"
                                    name="product_name"
                                    placeholder="e.g. Ashirvaad Atta"
                                    class="field-input">
                            </div>

                            <div>
                                <label class="field-label">
                                    Search Keyword
                                </label>

                                <input
                                    type="text"
                                    name="product_keyword"
                                    placeholder="e.g. Ashirvaad Whole Wheat Atta 5kg"
                                    class="field-input">
                            </div>

                        </div>


                        {{-- LOCATION --}}
                        <div>

                            <p class="mb-3 text-[15px] font-semibold text-slate-300">
                                Search Location
                            </p>

                            <div class="grid grid-cols-5 gap-3">

                                <div>
                                    <label class="field-label">
                                        Country
                                    </label>

                                    <select name="country" class="field-input">
                                        <option>India</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="field-label">
                                        State
                                    </label>

                                    <select name="state" class="field-input">
                                        <option>West Bengal</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="field-label">
                                        District
                                    </label>

                                    <select name="district" class="field-input">
                                        <option>Kolkata</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="field-label">
                                        Block / Area
                                    </label>

                                    <input
                                        type="text"
                                        name="block"
                                        placeholder="Garden Reach"
                                        class="field-input">
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

                            </div>

                        </div>


                        {{-- INFORMATION --}}
                        <div>

                            <p class="mb-3 text-[15px] font-semibold text-slate-300">
                                Information Required
                            </p>

                            <div class="flex flex-wrap gap-2">

                                @foreach([
                                'photo' => 'Recent Photo',
                                'video' => 'Product Video',
                                'price' => 'Product Rate',
                                'comparison' => 'Price Comparison',
                                'feedback' => 'Feedback',
                                'seller' => 'Seller',
                                'address' => 'Seller Address',
                                'contact' => 'Seller Contact',
                                'website' => 'Website',
                                'video_link' => 'Video Link',
                                'availability' => 'Availability',
                                ] as $value => $label)

                                <label class="cursor-pointer">

                                    <input
                                        type="checkbox"
                                        name="requirements[]"
                                        value="{{ $value }}"
                                        class="peer sr-only">

                                    <span class="inline-flex h-8 items-center rounded-md border border-slate-700 bg-slate-900 px-3 text-[11px] text-slate-400 transition peer-checked:border-blue-500 peer-checked:bg-blue-500/10 peer-checked:text-blue-300">
                                        {{ $label }}
                                    </span>

                                </label>

                                @endforeach

                            </div>

                        </div>


                        {{-- SOURCES --}}
                        <div>

                            <p class="mb-3 text-[15px] font-semibold text-slate-300">
                                Preferred Sources
                            </p>

                            <div class="flex flex-wrap gap-2">

                                @foreach([
                                'search' => 'Search Engines',
                                'official' => 'Official Website',
                                'ecommerce' => 'E-commerce',
                                'news' => 'News'
                                ] as $value => $label)

                                <label class="cursor-pointer">

                                    <input
                                        type="checkbox"
                                        name="sources[]"
                                        value="{{ $value }}"
                                        class="peer sr-only">

                                    <span class="inline-flex h-8 items-center rounded-md border border-slate-700 bg-slate-900 px-3 text-[11px] text-slate-400 transition peer-checked:border-blue-500 peer-checked:bg-blue-500/10 peer-checked:text-blue-300">
                                        {{ $label }}
                                    </span>

                                </label>

                                @endforeach

                            </div>

                        </div>


                        {{-- INSTRUCTION --}}
                        <div>

                            <label class="field-label">
                                Additional Instruction
                            </label>

                            <textarea
                                type="text"
                                name="instructions"
                                placeholder="e.g. Compare current prices from reliable sources"
                                class="field-input" style="height: 80px; padding-top: 10px;"></textarea>

                        </div>

                    </div>

                </section>


                {{-- ========================================== --}}
                {{-- SERVICE --}}
                {{-- ========================================== --}}

                <section
                    x-show="type === 'service'"
                    class="rounded-xl border border-slate-800 bg-[#111827]">

                    <div class="border-b border-slate-800 px-5 py-3.5">
                        <p class="text-[15px] font-semibold uppercase tracking-[0.08em] text-emerald-400">
                            Service Information
                        </p>
                    </div>

                    <div class="space-y-5 p-5">

                        <div class="grid grid-cols-2 gap-4">

                            <div>
                                <label class="field-label">
                                    Service Name
                                </label>

                                <input
                                    type="text"
                                    name="service_name"
                                    placeholder="e.g. AC Repair"
                                    class="field-input">
                            </div>

                            <div>
                                <label class="field-label">
                                    PIN Code
                                </label>

                                <input
                                    type="text"
                                    name="service_pincode"
                                    maxlength="6"
                                    placeholder="700024"
                                    class="field-input">
                            </div>

                        </div>


                        <div>

                            <p class="mb-3 text-[15px] font-semibold text-slate-300">
                                Service Location
                            </p>

                            <div class="grid grid-cols-4 gap-3">

                                <input
                                    type="text"
                                    name="service_state"
                                    placeholder="State"
                                    class="field-input">

                                <input
                                    type="text"
                                    name="service_district"
                                    placeholder="District"
                                    class="field-input">

                                <input
                                    type="text"
                                    name="service_block"
                                    placeholder="Block / Area"
                                    class="field-input">

                                <select
                                    name="service_radius"
                                    class="field-input">
                                    <option>Exact PIN</option>
                                    <option>5 KM</option>
                                    <option>10 KM</option>
                                    <option>25 KM</option>
                                    <option>District-wide</option>
                                </select>

                            </div>

                        </div>


                        <div>

                            <p class="mb-3 text-[15px] font-semibold text-slate-300">
                                Information Required
                            </p>

                            <div class="flex flex-wrap gap-2">

                                @foreach([
                                'organization' => 'Organization',
                                'owner' => 'Owner Name',
                                'designation' => 'Designation',
                                'address' => 'Address',
                                'email' => 'Email',
                                'mobile' => 'Mobile',
                                'website' => 'Website',
                                'service' => 'Service',
                                'rate' => 'Service Rate',
                                'person' => 'Contact Person',
                                ] as $value => $label)

                                <label class="cursor-pointer">

                                    <input
                                        type="checkbox"
                                        name="service_requirements[]"
                                        value="{{ $value }}"
                                        class="peer sr-only">

                                    <span class="inline-flex h-8 items-center rounded-md border border-slate-700 bg-slate-900 px-3 text-[11px] text-slate-400 transition peer-checked:border-emerald-500 peer-checked:bg-emerald-500/10 peer-checked:text-emerald-300">
                                        {{ $label }}
                                    </span>

                                </label>

                                @endforeach

                            </div>

                        </div>

                    </div>

                </section>


                {{-- ========================================== --}}
                {{-- CUSTOMER --}}
                {{-- ========================================== --}}

                <section
                    x-show="type === 'customer'"
                    class="rounded-xl border border-slate-800 bg-[#111827]">

                    <div class="border-b border-slate-800 px-5 py-3.5">
                        <p class="text-[15px] font-semibold uppercase tracking-[0.08em] text-purple-400">
                            Customer + AI
                        </p>
                    </div>

                    <div class="space-y-5 p-5">

                        <div class="grid grid-cols-3 gap-3">

                            <div>
                                <label class="field-label">
                                    Customer ID
                                </label>

                                <input
                                    type="text"
                                    name="customer_id"
                                    placeholder="CUST-1025"
                                    class="field-input">
                            </div>

                            <div>
                                <label class="field-label">
                                    Customer Name
                                </label>

                                <input
                                    type="text"
                                    name="customer_name"
                                    placeholder="Customer name"
                                    class="field-input">
                            </div>

                            <div>
                                <label class="field-label">
                                    Mobile
                                </label>

                                <input
                                    type="text"
                                    name="customer_mobile"
                                    placeholder="Mobile number"
                                    class="field-input">
                            </div>

                        </div>


                        <div>

                            <p class="mb-3 text-[15px] font-semibold text-slate-300">
                                AI Action
                            </p>

                            <div class="flex flex-wrap gap-2">

                                @foreach([
                                'profile' => 'Customer Profile',
                                'transactions' => 'Transactions',
                                'analysis' => 'Data Analysis',
                                'due' => 'Due Calculation',
                                'warning' => 'Warning / Reminder',
                                'notification' => 'Notification',
                                'whatsapp' => 'WhatsApp',
                                'telegram' => 'Telegram',
                                'email' => 'Email Promotion',
                                'content' => 'AI Content',
                                ] as $value => $label)

                                <label class="cursor-pointer">

                                    <input
                                        type="checkbox"
                                        name="customer_actions[]"
                                        value="{{ $value }}"
                                        class="peer sr-only">

                                    <span class="inline-flex h-8 items-center rounded-md border border-slate-700 bg-slate-900 px-3 text-[11px] text-slate-400 transition peer-checked:border-purple-500 peer-checked:bg-purple-500/10 peer-checked:text-purple-300">
                                        {{ $label }}
                                    </span>

                                </label>

                                @endforeach

                            </div>

                        </div>


                        <div class="grid grid-cols-2 gap-3">

                            <div>
                                <label class="field-label">
                                    From Date
                                </label>

                                <input
                                    type="date"
                                    name="date_from"
                                    class="field-input">
                            </div>

                            <div>
                                <label class="field-label">
                                    To Date
                                </label>

                                <input
                                    type="date"
                                    name="date_to"
                                    class="field-input">
                            </div>

                        </div>


                        <div>

                            <label class="field-label">
                                Ask AI
                            </label>

                            <input
                                type="text"
                                name="question"
                                placeholder="e.g. Show total purchase and outstanding due for the last 6 months"
                                class="field-input">

                        </div>

                    </div>

                </section>


                {{-- ========================================== --}}
                {{-- SUBMIT --}}
                {{-- ========================================== --}}

                <div class="flex items-center justify-between rounded-xl border border-slate-800 bg-[#111827] px-5 py-4">

                    <div>
                        <p class="text-[13px] font-medium">
                            Ready to search?
                        </p>

                        <p class="mt-0.5 text-[11px] text-slate-500">
                            AI will retrieve and process the requested information.
                        </p>
                    </div>

                    <button
                        type="submit"
                        class="h-9 rounded-md bg-blue-600 px-5 text-[15px] font-semibold text-white transition hover:bg-blue-500">
                        Search with AI
                    </button>

                </div>

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
            };
        }
    </script>

</body>

</html>
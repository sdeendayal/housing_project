
@extends('layouts.app')

@section('title', 'About Us - Who\'s Who')

@section('content')

@php
$officers = [
    ['en'=>'Shri Nayab Singh Saini','hi'=>'श्री नायब सिंह सैनी','en_designation'=>'Hon’ble Chief Minister','hi_designation'=>'माननीय मुख्यमंत्री','phone'=>'0172-2749396 / 2749409','email'=>'cmharyana@nic.in'],
    ['en'=>'Shri Mohammad Shayin, IAS','hi'=>'श्री मोहम्मद शाईन, आई.ए.एस','en_designation'=>'Commissioner & Secretary, Govt. of Haryana','hi_designation'=>'आयुक्त एवं सचिव, हरियाणा सरकार','phone'=>'0172-2711658','email'=>'commissionersecretaryhfa@gmail.com'],
    ['en'=>'Smt. Neetu Vadhera','hi'=>'श्रीमती नीतू वधेरा','en_designation'=>'Secretary to Commissioner & Secretary, HFA','hi_designation'=>'सचिव, आयुक्त एवं सचिव','phone'=>'0172-2711658','email'=>'commissionersecretaryhfa@gmail.com'],
    ['en'=>'Shri Chandra Shekhar Khare, IAS','hi'=>'श्री चन्द्र शेखर खरे, आई.ए.एस','en_designation'=>'Director General','hi_designation'=>'महानिदेशक','phone'=>'0172-2568005','email'=>'director-hfa@hry.gov.in'],
    ['en'=>'Smt. Nancy','hi'=>'श्रीमती नैंसी','en_designation'=>'Private Assistant to Director, HFA','hi_designation'=>'निजी सहायक / निदेशक','phone'=>'0172-2568005','email'=>'director-hfa@hry.gov.in'],
    ['en'=>'Smt. Ruchi Singh Bedi, HCS','hi'=>'श्रीमती रुचि सिंह बेदी, एच.सी.एस','en_designation'=>'Additional Director','hi_designation'=>'अतिरिक्त निदेशक','phone'=>'0172-2578288','email'=>'director-hfa@hry.gov.in'],
    ['en'=>'Shri Lalit Kumar','hi'=>'श्री ललित कुमार','en_designation'=>'Accounts Officer','hi_designation'=>'लेखा अधिकारी','phone'=>'0172-2568006','email'=>'director-hfa@hry.gov.in'],
    ['en'=>'Shri Aman Godara','hi'=>'श्री अमन गोदारा','en_designation'=>'Assistant Town Planner','hi_designation'=>'सहायक नगर योजनाकार','phone'=>'0172-2568005','email'=>'director-hfa@hry.gov.in'],
    ['en'=>'Shri Mahender Singh','hi'=>'श्री महेन्द्र सिंह','en_designation'=>'Urban Economist (PMAY-U)','hi_designation'=>'शहरी अर्थशास्त्री (पी.एम.ए.वाई.-यू)','phone'=>'0172-2568005','email'=>'director-hfa@hry.gov.in'],
    ['en'=>'Shri Harpreet Singh','hi'=>'श्री हरप्रीत सिंह','en_designation'=>'Urban Finance Specialist (PMAY-U)','hi_designation'=>'नगरीय वित्त विशेषज्ञ (पी.एम.ए.वाई.-यू)','phone'=>'0172-2568005','email'=>'director-hfa@hry.gov.in'],
    ['en'=>'Shri Sandeep Kumar','hi'=>'श्री संदीप कुमार','en_designation'=>'Civil Engineer (PMAY-U)','hi_designation'=>'नागरिक अभियंता (पी.एम.ए.वाई.-यू)','phone'=>'0172-2568005','email'=>'director-hfa@hry.gov.in'],
    ['en'=>'Ms. Vinita Parashar','hi'=>'सुश्री विनीता पराशर','en_designation'=>'MIS (PMAY-U)','hi_designation'=>'एम.आई.एस. (पी.एम.ए.वाई.-यू)','phone'=>'0172-2568005','email'=>'director-hfa@hry.gov.in'],
    ['en'=>'Smt. Seema Sharma','hi'=>'श्रीमति सीमा शर्मा','en_designation'=>'CBT (PMAY-U)','hi_designation'=>'सीबीटी (पी.एम.ए.वाई.-यू)','phone'=>'0172-2568005','email'=>'director-hfa@hry.gov.in'],
    ['en'=>'Shri Devender Singh','hi'=>'श्री देवेन्द्र सिंह','en_designation'=>'Coordinator (PMAY-G)','hi_designation'=>'समन्वयक (पी.एम.ए.वाई.-जी)','phone'=>'0172-2568005','email'=>'director-hfa@hry.gov.in'],
    ['en'=>'Smt. Nancy','hi'=>'श्रीमति नैन्सी','en_designation'=>'MIS (PMAY-G)','hi_designation'=>'एम.आई.एस. (पी.एम.ए.वाई.-जी)','phone'=>'0172-2568005','email'=>'director-hfa@hry.gov.in'],
    ['en'=>'Ms. Raveena','hi'=>'सुश्री रवीना','en_designation'=>'Finance Specialist (PMAY-G)','hi_designation'=>'वित्त विशेषज्ञ (पी.एम.ए.वाई.-जी)','phone'=>'0172-2568005','email'=>'director-hfa@hry.gov.in'],
];
@endphp

<main class="lg:col-span-2">
    <section class="bg-[#eef3f9] py-8">
        <div class="max-w-7xl mx-auto px-4">

            {{-- Heading and Language Dropdown --}}
            <div class="flex flex-col md:flex-row items-center justify-between gap-4 mb-8">

                <div class="text-center flex-1">
                    <div class="inline-flex items-center gap-2 bg-blue-100 text-blue-700 px-5 py-2 rounded-full text-xs font-semibold shadow-sm">
                        <span class="material-symbols-outlined text-[17px]">groups</span>
                        <span data-who-lang="en">Who's Who</span>
                        <span data-who-lang="hi" hidden>कौन क्या है</span>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <label for="whoLanguage" class="text-sm font-semibold text-slate-700">
                        Language:
                    </label>

                    <select id="whoLanguage"
                        class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">

                        <option value="en" selected>English</option>
                        <option value="hi" selected>हिन्दी</option>
                    </select>
                </div>
            </div>

            {{-- Table Card --}}
            <div class="bg-white rounded-[26px] shadow-lg border border-slate-200 px-4 md:px-6 py-6">

                <div class="overflow-x-auto">

                    <table id="whoTable" class="w-full text-[13px] text-left border-collapse">

                        <thead>
                            <tr class="bg-gradient-to-r from-[#0f75c8] to-[#0b3c74] text-white">

                                <th class="px-4 py-4 font-semibold rounded-tl-xl whitespace-nowrap">
                                    <span data-who-lang="en">Sr. No.</span>
                                    <span data-who-lang="hi" hidden>क्रमांक</span>
                                </th>

                                <th class="px-4 py-4 font-semibold whitespace-nowrap">
                                    <span data-who-lang="en">Officer Name</span>
                                    <span data-who-lang="hi" hidden>अधिकारी का नाम</span>
                                </th>

                                <th class="px-4 py-4 font-semibold whitespace-nowrap">
                                    <span data-who-lang="en">Designation</span>
                                    <span data-who-lang="hi" hidden>पद</span>
                                </th>

                                <th class="px-4 py-4 font-semibold whitespace-nowrap">
                                    <span data-who-lang="en">Telephone</span>
                                    <span data-who-lang="hi" hidden>दूरभाष</span>
                                </th>

                                <th class="px-4 py-4 font-semibold rounded-tr-xl whitespace-nowrap">
                                    <span data-who-lang="en">E-mail</span>
                                    <span data-who-lang="hi" hidden>ई-मेल</span>
                                </th>

                            </tr>
                        </thead>

                        <tbody class="text-slate-700">

                            @foreach($officers as $index => $officer)

                            <tr class="border-b border-slate-100 hover:bg-blue-50 transition">

                                <td class="px-4 py-4 font-semibold">
                                    {{ $index + 1 }}
                                </td>

                                <td class="px-4 py-4 font-medium whitespace-nowrap">
                                    <span data-who-lang="en">{{ $officer['en'] }}</span>
                                    <span data-who-lang="hi" hidden>{{ $officer['hi'] }}</span>
                                </td>

                                <td class="px-4 py-4">
                                    <span data-who-lang="en">{{ $officer['en_designation'] }}</span>
                                    <span data-who-lang="hi" hidden>{{ $officer['hi_designation'] }}</span>
                                </td>

                                <td class="px-4 py-4 whitespace-nowrap">
                                    {{ $officer['phone'] }}
                                </td>

                                <td class="px-4 py-4 text-blue-700">
                                    <a href="mailto:{{ $officer['email'] }}" class="hover:underline">
                                        {{ $officer['email'] }}
                                    </a>
                                </td>

                            </tr>

                            @endforeach

                        </tbody>
                    </table>

                </div>
            </div>

        </div>
    </section>
</main>

@include('partials.rightSidebar')

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const dropdown = document.getElementById('whoLanguage');

    const languageElements = document.querySelectorAll('[data-who-lang]');

    if (!dropdown) return;

    function changeWhoLanguage(language) {

        languageElements.forEach(function (element) {

            element.hidden = element.dataset.whoLang !== language;

        });

    }

    dropdown.addEventListener('change', function () {

        changeWhoLanguage(this.value);

    });

    // Default language
    changeWhoLanguage('hi');

});
</script>
@endpush

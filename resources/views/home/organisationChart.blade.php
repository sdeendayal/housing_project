
@extends('layouts.app')

@section('title', 'Organisation Chart - Housing For All')

@section('content')

@php
$branches = [
    [
        'en' => 'DTP (1)',
        'hi' => 'डी.टी.पी. (1)',
        'children' => [
            ['en'=>'ATP (1)', 'hi'=>'ए.टी.पी. (1)'],
            ['en'=>'Planning Assistant (2)', 'hi'=>'योजना सहायक (2)'],
        ]
    ],
    [
        'en' => 'Executive Engineer (1)',
        'hi' => 'कार्यकारी अभियंता (1)',
        'children' => [
            ['en'=>'Asstt. Engineer (2)', 'hi'=>'सहायक अभियंता (2)'],
            ['en'=>'Jr. Engineer (2)', 'hi'=>'कनिष्ठ अभियंता (2)'],
        ]
    ],
    [
        'en' => 'Addl. Dir./Jt. Dir. Establishment (1)',
        'hi' => 'अतिरिक्त निदेशक / संयुक्त निदेशक (स्थापना) (1)',
        'children' => [
            ['en'=>'Supdt. (1)', 'hi'=>'अधीक्षक (1)'],
            ['en'=>'Personal Assistant (1)', 'hi'=>'निजी सहायक (1)'],
            ['en'=>'Assistant (2), Steno/Clerk (4), Store Keeper (1)', 'hi'=>'सहायक (2), आशुलिपिक / लिपिक (4), भंडारपाल (1)'],
            ['en'=>'Peon (10)', 'hi'=>'चपरासी (10)'],
        ]
    ],
    [
        'en' => 'Account Officer (1)',
        'hi' => 'लेखा अधिकारी (1)',
        'children' => [
            ['en'=>'Steno Officer (1)', 'hi'=>'आशुलिपिक अधिकारी (1)'],
            ['en'=>'Account Asstt. (1)', 'hi'=>'लेखा सहायक (1)'],
            ['en'=>'Steno/Clerk (2)', 'hi'=>'आशुलिपिक / लिपिक (2)'],
        ]
    ],
    [
        'en' => 'ADA',
        'hi' => 'ए.डी.ए.',
        'children' => [
            ['en'=>'Steno/Clerk (1)', 'hi'=>'आशुलिपिक / लिपिक (1)'],
        ]
    ],
    [
        'en' => 'Project Manager (IT) (1)',
        'hi' => 'परियोजना प्रबंधक (आई.टी.) (1)',
        'children' => [
            ['en'=>'Database Administrator (1)', 'hi'=>'डेटाबेस प्रशासक (1)'],
            ['en'=>'Software Developer (2)', 'hi'=>'सॉफ्टवेयर डेवलपर (2)'],
            ['en'=>'Network Manager (1)', 'hi'=>'नेटवर्क प्रबंधक (1)'],
            ['en'=>'Data Analyst (2)', 'hi'=>'डेटा विश्लेषक (2)'],
            ['en'=>'Data Entry Operator (6)', 'hi'=>'डेटा एंट्री ऑपरेटर (6)'],
        ]
    ],
];
@endphp

<style>

.org-chart-section {
    background: #fff;
    padding: 35px 20px;
    overflow-x: auto;
}

.org-chart {
    width: 1100px;
    margin: auto;
    display: flex;
    flex-direction: column;
    align-items: center;
    font-family: Arial, "Noto Sans Devanagari", sans-serif;
}

.org-box {
    background: #fff;
    border: 1px solid #444;
    border-radius: 9px;
    padding: 9px 8px;
    width: 125px;
    min-height: 40px;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    font-size: 11px;
    line-height: 1.35;
    color: #222;
    box-sizing: border-box;
    box-shadow: none;
}

.org-root {
    width: 170px;
    min-height: 55px;
    font-size: 12px;
}

.org-root-arrow {
    height: 40px;
    font-size: 24px;
    line-height: 40px;
    color: #222;
}

.org-branches {
    width: 100%;
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    position: relative;
    gap: 10px;
    padding-top: 27px;
}

/* Horizontal connecting line */
.org-branches::before {
    content: '';
    position: absolute;
    top: 0;
    left: 4%;
    right: 4%;
    height: 1px;
    background: #333;
}

/* Vertical branch connectors */
.org-branch {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    padding-top: 10px;
}

/* Branch line */
.org-branch::before {
    content: '';
    position: absolute;
    top: -27px;
    height: 27px;
    width: 1px;
    background: #333;
}

.org-branch .org-box {
    width: 100%;
    max-width: 145px;
    min-height: 42px;
    padding: 8px 5px;
}

/* Downward arrows */
.org-arrow {
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    line-height: 1;
    color: #333;
}

.org-branch .org-box span {
    overflow-wrap: anywhere;
}

@media (max-width: 768px) {
    .org-chart-section {
        padding: 25px 10px;
    }
}

</style>

<main class="lg:col-span-2">

    <section class="org-chart-section">

        {{-- Heading --}}
        <div class="max-w-7xl mx-auto">

            <div class="flex justify-between items-center mb-8">

                <div class="text-center flex-1">
                    <span class="inline-flex items-center gap-2 bg-blue-100 text-blue-700 px-5 py-2 rounded-full text-xs font-semibold">
                        <span class="material-symbols-outlined">account_tree</span>
                        <span data-org-lang="en">Organisation Chart</span>
                        <span data-org-lang="hi" hidden>संगठन चार्ट</span>
                    </span>
                </div>

                <select id="orgLanguage"
                    class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold shadow-sm">

                    <option value="en">English</option>
                    <option value="hi">हिन्दी</option>

                </select>

            </div>

        </div>

        <div class="org-chart">

            {{-- Root --}}
            <div class="org-box org-root">

                <span data-org-lang="en">
                    Director General / Director (IAS)
                </span>

                <span data-org-lang="hi" hidden>
                    महानिदेशक / निदेशक (आई.ए.एस.)
                </span>

            </div>

            <div class="org-root-arrow">↓</div>           

            {{-- Six Branches --}}
            <div class="org-branches">

                @foreach($branches as $branch)

                    <div class="org-branch">

                        {{-- Main Position --}}
                        <div class="org-box">

                            <span data-org-lang="en">
                                {{ $branch['en'] }}
                            </span>

                            <span data-org-lang="hi" hidden>
                                {{ $branch['hi'] }}
                            </span>

                        </div>

                        {{-- Sub Positions --}}
                        @foreach($branch['children'] as $child)

                            <div class="org-arrow">↓</div>

                            <div class="org-box">

                                <span data-org-lang="en">
                                    {{ $child['en'] }}
                                </span>

                                <span data-org-lang="hi" hidden>
                                    {{ $child['hi'] }}
                                </span>

                            </div>

                        @endforeach

                    </div>

                @endforeach

            </div>

        </div>

    </section>

</main>

@include('partials.rightSidebar')

@endsection

@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    const dropdown = document.getElementById('orgLanguage');

    const elements = document.querySelectorAll('[data-org-lang]');

    if (!dropdown) return;

    function changeOrgLanguage(language) {

        elements.forEach(function (element) {

            element.hidden = element.dataset.orgLang !== language;

        });

    }

    dropdown.addEventListener('change', function () {

        changeOrgLanguage(this.value);

    });

    // Default language
    changeOrgLanguage('en');

});
</script>

@endpush

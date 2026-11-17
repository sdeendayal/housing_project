
@extends('layouts.app')

@section('title', 'Our Vision - Department of Housing For All')

@section('content')

<main class="lg:col-span-2">

    <section class="bg-gradient-to-b from-slate-50 to-white py-10 border-b border-slate-200">

        <div class="max-w-7xl mx-auto px-4">

            {{-- Heading and Language Selector --}}
            <div class="flex flex-col md:flex-row items-center justify-between gap-5 mb-10">

                <div class="text-center flex-1">

                    <div class="inline-flex items-center gap-2 bg-blue-100 text-blue-700 px-5 py-2 rounded-full text-xs font-semibold mb-3">

                        <span class="material-symbols-outlined text-[17px]">
                            visibility
                        </span>

                        <span data-vision-lang="en" hidden>OUR VISION</span>
                        <span data-vision-lang="hi">हमारा लक्ष्य</span>

                    </div>

                    <h1 class="text-2xl md:text-3xl font-bold text-civic-blue mb-3">

                        <span data-vision-lang="en" hidden>Our Vision</span>
                        <span data-vision-lang="hi">हमारा लक्ष्य</span>

                    </h1>

                    <p class="text-slate-600 text-base md:text-lg font-medium">

                        <span data-vision-lang="en" hidden>
                            Affordable Homes. Dignified Lives. Sustainable Communities.
                        </span>

                        <span data-vision-lang="hi">
                            किफायती आवास, सम्मानजनक जीवन और बेहतर भविष्य
                        </span>

                    </p>

                </div>

                

            </div>


            {{-- ENGLISH CONTENT --}}
            <div data-vision-content="en" hidden>

                {{-- Vision Statement --}}
                <div class="bg-gradient-to-r from-blue-600 to-blue-900 rounded-2xl p-7 md:p-10 text-white shadow-lg mb-8">

                    <div class="flex items-center gap-3 mb-5">

                        <span class="material-symbols-outlined text-3xl">
                            home_work
                        </span>

                        <h2 class="text-xl md:text-2xl font-bold">
                            Our Vision
                        </h2>

                    </div>

                    <p class="text-base md:text-lg leading-9 font-medium">
                        “To build an inclusive and sustainable Haryana where every eligible family has access to safe, secure, affordable and dignified housing, supported by essential infrastructure and opportunities for a better quality of life.”
                    </p>

                </div>


                {{-- Vision Details --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    <article class="bg-white rounded-2xl shadow-md border border-slate-200 p-6 md:p-8">

                        <div class="flex items-center gap-3 mb-5">

                            <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-blue-700">

                                <span class="material-symbols-outlined text-2xl">
                                    home
                                </span>

                            </div>

                            <h2 class="text-xl font-bold text-civic-blue">
                                <!-- Housing with Dignity -->
                            </h2>

                        </div>

                        <p class="text-slate-600 text-sm leading-8">
                            The Department of Housing For All envisions housing as more than a physical structure—it is a foundation for dignity, security, social empowerment and economic well-being.
                        </p>

                    </article>


                    <article class="bg-white rounded-2xl shadow-md border border-slate-200 p-6 md:p-8">

                        <div class="flex items-center gap-3 mb-5">

                            <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-blue-700">

                                <span class="material-symbols-outlined text-2xl">
                                    policy
                                </span>

                            </div>

                            <h2 class="text-xl font-bold text-civic-blue">
                                Policies
                            </h2>

                        </div>

                        <p class="text-slate-600 text-sm leading-8">
                            Our vision is to bridge the gap between housing needs and affordability through inclusive policies, innovative housing solutions, transparent governance and effective implementation of Central and State Government initiatives.
                        </p>

                    </article>


                    <article class="bg-white rounded-2xl shadow-md border border-slate-200 p-6 md:p-8">

                        <div class="flex items-center gap-3 mb-5">

                            <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-blue-700">

                                <span class="material-symbols-outlined text-2xl">
                                    location_city
                                </span>

                            </div>

                            <h2 class="text-xl font-bold text-civic-blue">
                                Sustainable Communities
                            </h2>

                        </div>

                        <p class="text-slate-600 text-sm leading-8">
                            We aspire to develop well-planned and sustainable communities with access to essential civic amenities, public transportation, employment opportunities, education, healthcare and other community services, ensuring that affordable housing also translates into affordable and quality living.
                        </p>

                    </article>


                    <article class="bg-white rounded-2xl shadow-md border border-slate-200 p-6 md:p-8">

                        <div class="flex items-center gap-3 mb-5">

                            <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-blue-700">

                                <span class="material-symbols-outlined text-2xl">
                                    devices
                                </span>

                            </div>

                            <h2 class="text-xl font-bold text-civic-blue">
                                Technology Driven Governance
                            </h2>

                        </div>

                        <p class="text-slate-600 text-sm leading-8">
                            Through technology-driven service delivery, institutional coordination and citizen-centric governance, the Department is committed to making housing opportunities accessible, equitable and sustainable for eligible households across urban and rural Haryana.
                        </p>

                    </article>

                </div>


                {{-- Closing Vision --}}
                <div class="mt-8 rounded-2xl border border-blue-100 bg-blue-50 p-7 text-center">

                    <span class="material-symbols-outlined text-4xl text-blue-700 mb-3">
                        emoji_objects
                    </span>

                    <h2 class="text-xl font-bold text-civic-blue mb-3">
                        Our Vision
                    </h2>

                    <p class="text-blue-900 font-semibold leading-8">
                        Beyond Housing — Creating Opportunities, Empowering Communities and Building a Better Haryana.
                    </p>

                </div>

            </div>


            {{-- HINDI CONTENT --}}
            <div data-vision-content="hi">

                {{-- Vision Statement --}}
                <div class="bg-gradient-to-r from-blue-600 to-blue-900 rounded-2xl p-7 md:p-10 text-white shadow-lg mb-8">

                    <div class="flex items-center gap-3 mb-5">

                        <span class="material-symbols-outlined text-3xl">
                            home_work
                        </span>

                        <h2 class="text-xl md:text-2xl font-bold">
                            हमारा लक्ष्य
                        </h2>

                    </div>

                    <p class="text-base md:text-lg leading-9 font-medium">
                        “एक ऐसे विकसित और खुशहाल हरियाणा का निर्माण करना, जहाँ हर पात्र परिवार को सुरक्षित, किफायती और सम्मानजनक आवास मिल सके तथा बेहतर जीवन जीने के लिए सभी जरूरी सुविधाएँ और अवसर उपलब्ध हों।”
                    </p>

                </div>


                {{-- Vision Details --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    <article class="bg-white rounded-2xl shadow-md border border-slate-200 p-6 md:p-8">

                        <div class="flex items-center gap-3 mb-5">

                            <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-blue-700">

                                <span class="material-symbols-outlined text-2xl">
                                    home
                                </span>

                            </div>

                            <h2 class="text-xl font-bold text-civic-blue">
                                सम्मानजनक आवास
                            </h2>

                        </div>

                        <p class="text-slate-600 text-sm leading-8">
                            <strong>हाउसिंग फॉर ऑल विभाग, हरियाणा सरकार</strong> का मानना है कि घर केवल चार दीवारों और छत का नाम नहीं है, बल्कि यह हर परिवार की सुरक्षा, सम्मान, खुशहाली और बेहतर भविष्य की नींव है।
                        </p>

                    </article>


                    <article class="bg-white rounded-2xl shadow-md border border-slate-200 p-6 md:p-8">

                        <div class="flex items-center gap-3 mb-5">

                            <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-blue-700">

                                <span class="material-symbols-outlined text-2xl">
                                    policy
                                </span>

                            </div>

                            <h2 class="text-xl font-bold text-civic-blue">
                                समावेशी नीतियाँ
                            </h2>

                        </div>

                        <p class="text-slate-600 text-sm leading-8">
                            हमारा लक्ष्य केंद्र और राज्य सरकार की विभिन्न आवास योजनाओं के माध्यम से आर्थिक रूप से कमजोर और जरूरतमंद परिवारों के लिए घर का सपना साकार करना है। इसके लिए विभाग पारदर्शी व्यवस्था, नई तकनीक और बेहतर योजनाओं के माध्यम से पात्र परिवारों तक आवास सुविधाएँ पहुँचाने के लिए प्रतिबद्ध है।
                        </p>

                    </article>


                    <article class="bg-white rounded-2xl shadow-md border border-slate-200 p-6 md:p-8">

                        <div class="flex items-center gap-3 mb-5">

                            <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-blue-700">

                                <span class="material-symbols-outlined text-2xl">
                                    location_city
                                </span>

                            </div>

                            <h2 class="text-xl font-bold text-civic-blue">
                                सुव्यवस्थित एवं सुविधाजनक आवासीय क्षेत्र
                            </h2>

                        </div>

                        <p class="text-slate-600 text-sm leading-8">
                            विभाग का विजन केवल किफायती मकान या प्लॉट उपलब्ध करवाने तक सीमित नहीं है, बल्कि ऐसे <strong>सुव्यवस्थित और सुविधाजनक आवासीय क्षेत्र विकसित करना</strong> भी है, जहाँ लोगों को सड़क, बिजली, पानी, सफाई, सार्वजनिक परिवहन, रोजगार, शिक्षा, स्वास्थ्य सेवाएँ और रोजमर्रा की अन्य जरूरी सुविधाएँ आसानी से मिल सकें।
                        </p>

                    </article>


                    <article class="bg-white rounded-2xl shadow-md border border-slate-200 p-6 md:p-8">

                        <div class="flex items-center gap-3 mb-5">

                            <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-blue-700">

                                <span class="material-symbols-outlined text-2xl">
                                    devices
                                </span>

                            </div>

                            <h2 class="text-xl font-bold text-civic-blue">
                                तकनीक आधारित सुशासन
                            </h2>

                        </div>

                        <p class="text-slate-600 text-sm leading-8">
                            विभाग आधुनिक तकनीक, ऑनलाइन सेवाओं और विभिन्न सरकारी संस्थाओं के आपसी सहयोग से आवास योजनाओं को अधिक सरल, पारदर्शी और लोगों की जरूरतों के अनुसार बेहतर बनाने के लिए निरंतर प्रयासरत है, ताकि हरियाणा के शहरी और ग्रामीण क्षेत्रों में पात्र परिवारों को समान अवसर मिल सकें।
                        </p>

                    </article>

                </div>


                {{-- Closing Vision --}}
                <div class="mt-8 rounded-2xl border border-blue-100 bg-blue-50 p-7 text-center">

                    <span class="material-symbols-outlined text-4xl text-blue-700 mb-3">
                        emoji_objects
                    </span>

                    <h2 class="text-xl font-bold text-civic-blue mb-3">
                        हमारा विजन
                    </h2>

                    <p class="text-blue-900 font-semibold leading-8">
                        सिर्फ मकान नहीं, बल्कि हर परिवार के लिए सुरक्षित घर, बेहतर अवसर, खुशहाल जीवन और समृद्ध हरियाणा का निर्माण।
                    </p>

                </div>

            </div>

        </div>

    </section>

</main>

@include('partials.rightSidebar')

@endsection

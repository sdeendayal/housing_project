@extends('layouts.app')

@section('title', 'About Us - Department of Housing For All')

@section('content')
<main class="lg:col-span-2">
    <section class="bg-gradient-to-b from-slate-50 to-white py-10 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4">           

            {{-- English content --}}
            <div data-about-language="en">
                <div class="text-center mb-10">
                    <div class="inline-flex items-center gap-2 bg-blue-100 text-civic-blue px-4 py-1.5 rounded-full text-xs font-semibold mb-3">
                        <span class="material-symbols-outlined text-[16px]">apartment</span>
                        INTRODUCTION
                    </div>
                    <h1 class="text-2xl md:text-3xl font-bold text-civic-blue mb-4">Department of Housing For All, Government of Haryana</h1>
                    <p class="max-w-5xl mx-auto text-slate-600 text-base leading-8">
                        The Department of Housing For All, Government of Haryana, was established on 15th December 2020 as the nodal department for the planning, promotion, development and implementation of housing initiatives across urban and rural areas of the State, with special emphasis on economically weaker and socially disadvantaged sections of society.
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2">
                        <article class="bg-white rounded-2xl shadow-md border border-slate-200 overflow-hidden">
                            <div class="bg-[linear-gradient(90deg,rgba(6,127,208,1)_0%,rgba(0,51,88,1)_100%)] px-5 py-4">
                                <h2 class="text-xl font-semibold text-white flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[21px]">apartment</span>
                                    Introduction
                                </h2>
                            </div>
                            <div class="p-6 space-y-5 text-sm text-slate-700 leading-8">
                                <p>Guided by the vision of “Housing for All”, the Department is committed to providing safe, affordable and dignified housing through effective policy formulation, transparent governance and coordinated implementation of Central and State Government housing schemes.</p>
                                <p>Under the <strong>Mukhya Mantri Shehri Awas Yojana (MMSAY)</strong>, the State Government facilitates the allotment of one-marla (30 sq. yards) residential plots to eligible economically weaker families in urban areas. Similarly, under the <strong>Mukhya Mantri Gramin Awas Yojana (MMGAY)</strong>, eligible rural families are provided 50 sq. yards plots in Mahagram Panchayats and 100 sq. yards plots in regular Gram Panchayats, enabling them to fulfil their aspiration of homeownership.</p>
                                <p>To support the construction of pucca houses, financial assistance of up to <strong>₹2.50 lakh</strong> under <strong>Pradhan Mantri Awas Yojana–Urban (PMAY-U 2.0)</strong> and <strong>₹1.38 lakh</strong> under <strong>Pradhan Mantri Awaas Yojana–Gramin (PMAY-G)</strong> is provided to eligible beneficiaries, as per the respective scheme guidelines.</p>
                                <p>The Department also facilitates the transparent allotment of residential flats earmarked for <strong>Economically Weaker Sections (EWS)</strong> in licensed residential colonies under the applicable policies of the Town and Country Planning Department, Haryana.</p>
                                <p>Working in coordination with Government departments, Urban Local Bodies, rural development institutions and other stakeholders, the Department promotes technology-driven, transparent and citizen-centric housing delivery mechanisms.</p>
                                <p>Beyond providing residential plots and houses, the Department strives to develop inclusive, well-planned and sustainable communities supported by essential infrastructure, civic amenities and improved living conditions.</p>
                                <div class="rounded-xl border border-blue-100 bg-blue-50 p-4 font-semibold text-civic-blue">
                                    Our Commitment: Affordable Homes, Dignified Living and a Better Future for Every Eligible Family in Haryana.
                                </div>
                            </div>
                        </article>
                    </div>

                    <aside class="space-y-5">
                        <article class="bg-white rounded-2xl shadow-md border border-slate-200 p-5">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="h-12 w-12 rounded-xl bg-blue-100 flex items-center justify-center text-civic-blue">
                                    <span class="material-symbols-outlined text-2xl">visibility</span>
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold text-civic-blue">Our Vision</h2>
                                    <p class="text-xs text-slate-500">Affordable Homes. Dignified Lives. Sustainable Communities.</p>
                                </div>
                            </div>
                            <p class="text-slate-600 leading-7 text-sm font-medium">“To build an inclusive and sustainable Haryana where every eligible family has access to safe, secure, affordable and dignified housing, supported by essential infrastructure and opportunities for a better quality of life.”</p>
                        </article>

                        <article class="bg-white rounded-2xl shadow-md border border-slate-200 p-5">
                            <h3 class="text-lg font-bold text-civic-blue mb-3">Our Vision and Approach</h3>
                            <div class="space-y-3 text-sm leading-7 text-slate-600">
                                <p>The Department of Housing For All envisions housing as more than a physical structure—it is a foundation for dignity, security, social empowerment and economic well-being.</p>
                                <p>Our vision is to bridge the gap between housing needs and affordability through inclusive policies, innovative housing solutions, transparent governance and effective implementation of Central and State Government initiatives.</p>
                                <p>We aspire to develop well-planned and sustainable communities with access to essential civic amenities, public transportation, employment opportunities, education, healthcare and other community services, ensuring that affordable housing also translates into affordable and quality living.</p>
                                <p>Through technology-driven service delivery, institutional coordination and citizen-centric governance, the Department is committed to making housing opportunities accessible, equitable and sustainable for eligible households across urban and rural Haryana.</p>
                            </div>
                            <div class="mt-4 rounded-xl bg-blue-50 border border-blue-100 p-4 text-sm font-semibold leading-6 text-civic-blue">
                                Our Vision: Beyond Housing — Creating Opportunities, Empowering Communities and Building a Better Haryana.
                            </div>
                        </article>

                        <div class="bg-[linear-gradient(135deg,rgba(6,127,208,1)_0%,rgba(0,51,88,1)_100%)] rounded-2xl p-6 text-white shadow-lg">
                            <h3 class="text-xl font-bold mb-5">Department Highlights</h3>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between border-b border-white/20 pb-3"><span class="text-xs uppercase tracking-wide">Established</span><span class="text-xl font-bold">15 December 2020</span></div>
                                <div class="flex items-center justify-between border-b border-white/20 pb-3"><span class="text-xs uppercase tracking-wide">Coverage</span><span class="text-lg font-bold">Urban &amp; Rural</span></div>
                                <div class="flex items-center justify-between"><span class="text-xs uppercase tracking-wide">Focus</span><span class="text-lg font-bold">Housing For All</span></div>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>

            {{-- Hindi content --}}
            <div data-about-language="hi" hidden>
                <div class="text-center mb-10">
                    <div class="inline-flex items-center gap-2 bg-blue-100 text-civic-blue px-4 py-1.5 rounded-full text-xs font-semibold mb-3">
                        <span class="material-symbols-outlined text-[16px]">apartment</span>
                        परिचय
                    </div>
                    <h1 class="text-2xl md:text-3xl font-bold text-civic-blue mb-4">हाउसिंग फॉर ऑल विभाग, हरियाणा सरकार</h1>
                    <p class="max-w-5xl mx-auto text-slate-600 text-base leading-8">हाउसिंग फॉर ऑल विभाग, हरियाणा सरकार का गठन 15 दिसम्बर, 2020 को किया गया था। यह विभाग हरियाणा के शहरी और ग्रामीण क्षेत्रों में आवास योजनाओं को तैयार करने, उनका विकास करने और उन्हें लागू करने के लिए नोडल विभाग के रूप में कार्य करता है। विभाग का मुख्य उद्देश्य आर्थिक रूप से कमजोर और जरूरतमंद परिवारों को अपना घर बनाने का अवसर प्रदान करना है।</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2">
                        <article class="bg-white rounded-2xl shadow-md border border-slate-200 overflow-hidden">
                            <div class="bg-[linear-gradient(90deg,rgba(6,127,208,1)_0%,rgba(0,51,88,1)_100%)] px-5 py-4">
                                <h2 class="text-xl font-semibold text-white flex items-center gap-2"><span class="material-symbols-outlined text-[21px]">apartment</span>परिचय</h2>
                            </div>
                            <div class="p-6 space-y-5 text-sm text-slate-700 leading-8">
                                <p>“हर परिवार का अपना घर” के लक्ष्य के साथ विभाग केंद्र और राज्य सरकार की विभिन्न आवास योजनाओं के माध्यम से पात्र परिवारों को सुरक्षित, किफायती और सम्मानजनक आवास उपलब्ध करवाने के लिए लगातार प्रयास कर रहा है।</p>
                                <p><strong>मुख्यमंत्री शहरी आवास योजना (MMSAY)</strong> के तहत शहरी क्षेत्रों में आर्थिक रूप से कमजोर पात्र परिवारों को एक मरला (30 वर्ग गज) के आवासीय प्लॉट दिए जाते हैं। इसी तरह, <strong>मुख्यमंत्री ग्रामीण आवास योजना (MMGAY)</strong> के तहत ग्रामीण क्षेत्रों के पात्र परिवारों को महाग्राम पंचायतों में 50 वर्ग गज और सामान्य ग्राम पंचायतों में 100 वर्ग गज के आवासीय प्लॉट उपलब्ध करवाए जाते हैं, ताकि जरूरतमंद परिवार अपने घर का सपना पूरा कर सकें।</p>
                                <p>इन प्लॉटों पर पक्का मकान बनाने के लिए पात्र लाभार्थियों को <strong>प्रधानमंत्री आवास योजना–शहरी 2.0 (PMAY-U 2.0)</strong> के तहत <strong>₹2.50 लाख तक</strong> और <strong>प्रधानमंत्री आवास योजना–ग्रामीण (PMAY-G)</strong> के तहत <strong>₹1.38 लाख</strong> की आर्थिक सहायता भी संबंधित योजनाओं के नियमों के अनुसार दी जाती है।</p>
                                <p>इसके अलावा, विभाग द्वारा नगर एवं ग्राम आयोजना विभाग, हरियाणा की लागू नीतियों के तहत निजी बिल्डरों की लाइसेंस प्राप्त आवासीय कॉलोनियों में आर्थिक रूप से कमजोर वर्ग (EWS) के लिए आरक्षित फ्लैटों का आवंटन भी पारदर्शी तरीके से किया जाता है।</p>
                                <p>विभाग अन्य सरकारी विभागों, शहरी स्थानीय निकायों, ग्रामीण विकास संस्थाओं और संबंधित एजेंसियों के साथ मिलकर काम करता है। आवास योजनाओं का लाभ सही पात्र परिवारों तक आसानी से और पारदर्शी तरीके से पहुँचाने के लिए ऑनलाइन आवेदन, पात्रता की जाँच, ऑनलाइन ड्रॉ और डिजिटल सेवाओं का उपयोग किया जा रहा है।</p>
                                <p>विभाग का उद्देश्य केवल प्लॉट या मकान उपलब्ध करवाना ही नहीं, बल्कि ऐसे आवासीय क्षेत्र विकसित करना भी है, जहाँ लोगों को सड़क, बिजली, पानी, सफाई, परिवहन और अन्य जरूरी सुविधाएँ मिल सकें, ताकि वे अपने परिवार के साथ सुरक्षित और बेहतर जीवन जी सकें।</p>
                                <div class="rounded-xl border border-blue-100 bg-blue-50 p-4 font-semibold text-civic-blue">हमारा संकल्प: हर पात्र परिवार को किफायती आवास, सम्मानजनक जीवन और बेहतर भविष्य का अवसर प्रदान करना।</div>
                            </div>
                        </article>
                    </div>

                    <aside class="space-y-5">
                        <article class="bg-white rounded-2xl shadow-md border border-slate-200 p-5">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="h-12 w-12 rounded-xl bg-blue-100 flex items-center justify-center text-civic-blue"><span class="material-symbols-outlined text-2xl">visibility</span></div>
                                <div><h2 class="text-lg font-bold text-civic-blue">हमारा लक्ष्य</h2><p class="text-xs text-slate-500">किफायती आवास, सम्मानजनक जीवन और बेहतर भविष्य</p></div>
                            </div>
                            <p class="text-slate-600 leading-8 text-sm font-medium">“एक ऐसे विकसित और खुशहाल हरियाणा का निर्माण करना, जहाँ हर पात्र परिवार को सुरक्षित, किफायती और सम्मानजनक आवास मिल सके तथा बेहतर जीवन जीने के लिए सभी जरूरी सुविधाएँ और अवसर उपलब्ध हों।”</p>
                        </article>

                        <article class="bg-white rounded-2xl shadow-md border border-slate-200 p-5">
                            <h3 class="text-lg font-bold text-civic-blue mb-3">हमारा लक्ष्य और दृष्टिकोण</h3>
                            <div class="space-y-3 text-sm leading-8 text-slate-600">
                                <p>हाउसिंग फॉर ऑल विभाग, हरियाणा सरकार का मानना है कि घर केवल चार दीवारों और छत का नाम नहीं है, बल्कि यह हर परिवार की सुरक्षा, सम्मान, खुशहाली और बेहतर भविष्य की नींव है।</p>
                                <p>हमारा लक्ष्य केंद्र और राज्य सरकार की विभिन्न आवास योजनाओं के माध्यम से आर्थिक रूप से कमजोर और जरूरतमंद परिवारों के लिए घर का सपना साकार करना है। इसके लिए विभाग पारदर्शी व्यवस्था, नई तकनीक और बेहतर योजनाओं के माध्यम से पात्र परिवारों तक आवास सुविधाएँ पहुँचाने के लिए प्रतिबद्ध है।</p>
                                <p>विभाग का विजन केवल किफायती मकान या प्लॉट उपलब्ध करवाने तक सीमित नहीं है, बल्कि ऐसे सुव्यवस्थित और सुविधाजनक आवासीय क्षेत्र विकसित करना भी है, जहाँ लोगों को सड़क, बिजली, पानी, सफाई, सार्वजनिक परिवहन, रोजगार, शिक्षा, स्वास्थ्य सेवाएँ और रोजमर्रा की अन्य जरूरी सुविधाएँ आसानी से मिल सकें।</p>
                                <p>विभाग आधुनिक तकनीक, ऑनलाइन सेवाओं और विभिन्न सरकारी संस्थाओं के आपसी सहयोग से आवास योजनाओं को अधिक सरल, पारदर्शी और लोगों की जरूरतों के अनुसार बेहतर बनाने के लिए निरंतर प्रयासरत है, ताकि हरियाणा के शहरी और ग्रामीण क्षेत्रों में पात्र परिवारों को समान अवसर मिल सकें।</p>
                            </div>
                            <div class="mt-4 rounded-xl bg-blue-50 border border-blue-100 p-4 text-sm font-semibold leading-8 text-civic-blue">हमारा विजन: सिर्फ मकान नहीं, बल्कि हर परिवार के लिए सुरक्षित घर, बेहतर अवसर, खुशहाल जीवन और समृद्ध हरियाणा का निर्माण।</div>
                        </article>

                        <div class="bg-[linear-gradient(135deg,rgba(6,127,208,1)_0%,rgba(0,51,88,1)_100%)] rounded-2xl p-6 text-white shadow-lg">
                            <h3 class="text-xl font-bold mb-5">विभाग की मुख्य जानकारी</h3>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between border-b border-white/20 pb-3"><span class="text-xs">स्थापना</span><span class="text-lg font-bold">15 दिसम्बर 2020</span></div>
                                <div class="flex items-center justify-between border-b border-white/20 pb-3"><span class="text-xs">क्षेत्र</span><span class="text-lg font-bold">शहरी एवं ग्रामीण</span></div>
                                <div class="flex items-center justify-between"><span class="text-xs">उद्देश्य</span><span class="text-lg font-bold">हर परिवार को आवास</span></div>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </section>
</main>
@include('partials.rightSidebar')
@endsection



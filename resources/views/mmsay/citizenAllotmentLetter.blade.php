@extends('layouts.mmsayDepartmentAuth')
@section('title', 'MMSAY - Physical Letter')
@section('content')
    <main class="ml-52 pt-20 px-5 pb-5 min-h-screen">
        <div class="max-w-container-max mx-auto space-y-md">

            <style data-purpose="custom-typography">
                @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Noto+Sans+Devanagari:wght@400;600;700&display=swap');

                body {
                    background-color: #f3f4f6;
                    font-family: 'Inter', 'Noto Sans Devanagari', sans-serif;
                }

                .document-container {
                    width: 100%;
                    max-width: 1000px;
                    margin: 20px auto;
                    background: white;
                    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
                }

                .border-orange-frame {
                    border: 4px solid #f48221;
                }
            </style>
            <style media="print">
                @page {
                    size: A4;
                    margin: 0;
                }

                body {
                    background: white !important;
                    padding: 0 !important;
                    margin: 0 !important;
                }

                header[class*="max-w-[1000px]"] {
                    display: none !important;
                }

                .document-container {
                    box-shadow: none !important;
                    margin: 0 auto !important;
                    width: 100% !important;
                    max-width: 100% !important;
                }

                main {
                    padding: 20px !important;
                    space-y: 4 !important;
                }

                section {
                    break-inside: avoid;
                }
            </style>
            <header
                class="max-w-[1000px] mx-auto bg-white border-b border-gray-200 px-6 py-3 flex justify-between items-center rounded-t-lg shadow-sm">
                <h1 class="text-lg font-bold text-gray-800">Allotment Letter</h1>
                <a href="{{ route('allotment-letter-pdf', $property->PropertyAuctionId) }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded flex items-center gap-2 text-sm">

                    Download PDF

                </a>
            </header>
            <!-- END: NavigationBar -->
            <main class="document-container p-6 md:p-10 space-y-8">
                <!-- BEGIN: MainAllotmentBox -->
                <section class="border-orange-frame p-6 md:p-10 flex flex-col items-center"
                    data-purpose="allotment-content">
                    <!-- Logo and Header Labels -->
                    <div class="text-center space-y-4 mb-8">
                        <img alt="Govt. of Haryana Logo" class="mx-auto h-24 w-24 object-contain"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuB5tTMdUxPMSeypX-Kf4HCddf_ocwxeySWavsDJZcL7Vf2R655aOEdOwvZl3ykXYJWchi1PtzFhNa1VVI4E8OakotkJU4fSMaiVqZ-EMlUvEgn8sCrk4mK89yG4cbUt1Bt_ct3_E2N9Q_hUgxoWVPzhPrQGSMhpI9x2KqBNuDA-hI0u3i7rcCmPyqNz7fN880p2Zo1Hd4XY-5zJe2jHIwg2r1znU4YmlAnKjORkPFYwO8Ll2F9wiD1HSEH9kpIQcPY67qfgOPsThw">
                        <h2 class="text-2xl font-bold text-gray-800">हाउसिंग फॉर ऑल विभाग, हरियाणा</h2>
                        <div class="inline-block bg-green-700 text-white px-8 py-1 rounded text-lg font-semibold">आबंटन
                            पत्र</div>
                        <h3 class="text-xl font-bold text-orange-600">मुख्य मंत्री शहरी आवास योजना</h3>
                        <p class="text-sm text-gray-700">हरियाणा सरकार लाभार्थी को एक लाख रुपये की अदायगी पर एक मरला (30
                            वर्ग गज) आवासीय प्लॉट प्रदान करने की स्वीकृति प्रदान करती है।</p>
                    </div>
                    <!-- Details Table -->
                    <div class="w-full overflow-x-auto border border-gray-200 rounded-sm mb-10"
                        data-purpose="details-table">
                        <table class="w-full text-left border-collapse">
                            <tbody class="text-sm">
                                <tr class="border-b border-gray-200">
                                    <th class="py-3 px-4 bg-gray-50 font-semibold text-blue-800 w-1/3">पंजीकरण संख्या
                                    </th>
                                    <td>{{ $property->ApplicationNo }}</td>
                                </tr>
                                <tr class="border-b border-gray-200">
                                    <th class="py-3 px-4 bg-gray-50 font-semibold text-blue-800">परिवार पहचान पत्र
                                        संख्या</th>
                                    <td>{{ $property->PPPId }}</td>
                                </tr>
                                <tr class="border-b border-gray-200">
                                    <th class="py-3 px-4 bg-gray-50 font-semibold text-blue-800">लाभार्थी का पूरा नाम
                                    </th>
                                    <td>{{ strtoupper($property->PrivatePurchaserName) }}</td>
                                </tr>
                                <tr class="border-b border-gray-200">
                                    <th class="py-3 px-4 bg-gray-50 font-semibold text-blue-800">पिता/पति का नाम</th>
                                    <td class="py-3 px-4 text-gray-800">{{ strtoupper($property->PurchaserFatherName) }}
                                    </td>
                                </tr>
                                <tr class="border-b border-gray-200">
                                    <th class="py-3 px-4 bg-gray-50 font-semibold text-blue-800">प्लॉट संख्या</th>
                                    <td class="py-3 px-4 text-gray-800 uppercase">{{ $property->AssetName }}</td>
                                </tr>
                                <tr class="border-b border-gray-200">
                                    <th class="py-3 px-4 bg-gray-50 font-semibold text-blue-800">सेक्टर</th>
                                    <td class="py-3 px-4 text-gray-800">{{ $property->SectorName }}</td>
                                </tr>
                                <tr class="border-b border-gray-200">
                                    <th class="py-3 px-4 bg-gray-50 font-semibold text-blue-800">नगर</th>
                                    <td class="py-3 px-4 text-gray-800">{{ $property->CityName }}</td>
                                </tr>
                                <tr class="">
                                    <th class="py-3 px-4 bg-gray-50 font-semibold text-blue-800">जिला</th>
                                    <td class="py-3 px-4 text-gray-800">{{ $property->DistrictName }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- QR and Verification Info -->
                    <div class="w-full flex justify-between items-end">
                        <div class="flex items-center gap-4">
                            <div class="w-24 h-24 bg-gray-100 border border-gray-300 p-1">
                                <img alt="QR Verification" class="w-full h-full"
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuBnuvNw6bsQ4166vCYtTlpzZeoo6Ds87DZKnmfkcVYMbJzNnEkFbCmNbrw48AOKHUJ6WNinHKXsJXAez_7Ftvd-o8lnraV-Wzm7dZhujWZD5VBJOngiOWyK0uqmOMd9iXS_1ThihTEBJAz_TlQXuwIBS6LFTQiX9wCkEnkKoP_zwJien6VcBgglR6t2GBItxdzlRH8iL1UDYJSZXQxzjR6DX7mMcRCKBRUvxV1VL0PipZe3_P2Qt2ihqELC1hmmiwForP1-Tekp8Q">
                            </div>
                            <span class="text-xs font-semibold text-gray-700">कृपया QR कोड स्कैन करें और विवरण सत्यापित
                                करें</span>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-gray-600">पीपीपी सिस्टम द्वारा सत्यापित डेटा, हस्ताक्षर की
                                आवश्यकता नहीं है</p>
                            <p class="text-[10px] text-red-600 font-bold">*नियम और शर्तें लागू</p>
                        </div>
                    </div>
                </section>
                <!-- END: MainAllotmentBox -->
                <!-- BEGIN: TermsAndConditionsBox -->
                <section class="border-orange-frame p-6" data-purpose="terms-conditions">
                    <h4 class="text-center font-bold text-lg mb-4 underline">नियम और शर्तें</h4>
                    <ol class="text-[11px] leading-relaxed text-gray-800 space-y-1 list-decimal list-inside pl-2">
                        <li class="">यह पीपीपी सिस्टम द्वारा सत्यापित प्रोविजनल आबंटन पत्र वैध है।</li>
                        <li class="">यह प्रोविजनल आबंटन पत्र जारी होने के 30 दिनों के भीतर 10,000/- रुपये तथा शेष
                            80,000/- रुपये की राशि छह बराबर किस्तों में छह महीनों के अन्दर जमा करवानी होगी।</li>
                        <li class="">लाभार्थी को आबंटन पत्र जारी होने की तिथि से 12 मास के अन्दर - 2 आवासीय इकाई
                            का निर्माण शुरू करना होगा और 24 महीनों के अन्दर निर्माण पूर्ण करना होगा।</li>
                        <li class="">प्लॉट का उपयोग केवल आवास निर्माण हेतु करना होगा और आवंटन की तिथि से 10 वर्षों
                            तक इसे पट्टे पर/बेचा नहीं जा सकता। हालांकि, आवासीय निर्माण के लिए होम लोन प्राप्त करने हेतु
                            प्लॉट को बैंक या वित्तीय संस्थान के पास गिरवी रखा जा सकता है।</li>
                        <li class="">लाभार्थी आबंटन पत्र जारी होने की तिथि से 3 वर्ष पश्चात सरकारी सब्सिडी (मूलधन
                            और ब्याज) को ब्याज सहित हाउसिंग फॉर ऑल विभाग को लौटाकर खुले बाजार में आवासीय इकाई बेच सकता
                            है।</li>
                        <li class="">लाभार्थी की मृत्यु होने की स्थिति में, उनके कानूनी उत्तराधिकारी प्लॉट आबंटन
                            की शर्तों और नियमों से बाध्य होंगे।</li>
                        <li class="">आबंटन की किसी भी शर्त और नियम का उल्लंघन होने पर, हाउसिंग फॉर ऑल विभाग
                            लाभार्थी को पर्याप्त सुनवाई का अवसर देकर, प्लॉट का कब्जा लेने का अधिकार रखता है। ऐसे मामले
                            में, लाभार्थी किसी भी मुआवजे का हकदार नहीं होगा।</li>
                        <li class="">लाभार्थी को प्रधानमंत्री आवास योजना शहरी के (बीएलसी) घटक के अंतर्गत घर के
                            निर्माण हेतु रु. 1.5 लाख तक की वित्तीय सहायता का प्रावधान।</li>
                        <li class="">बैंकों/वित्तीय संस्थाओं से घर निर्माण हेतु कम ब्याज पर 6,00,000/- रूपये तक के
                            गृह ऋण (Home Loan) की सुविधा का प्रावधान।</li>
                        <li class="">लाभार्थी द्वारा सभी किस्तों/पूर्ण राशि जमा करवाने पश्चात प्लॉट का कब्जा दिया
                            जाएगा।</li>
                    </ol>
                </section>
            </main>

        </div>
    </main>
@endsection

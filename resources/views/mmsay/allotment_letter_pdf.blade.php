<!DOCTYPE html>
<html lang="hi" style="">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Allotment Letter - Housing For All, Haryana</title>
    <!-- Tailwind CSS v3 CDN -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'haryana-orange': '#f48221',
                        'haryana-green': '#28a745',
                        'civic-blue': '#1e3a8a',
                    },
                    fontFamily: {
                        sans: ['Inter', 'Noto Sans Devanagari', 'sans-serif'],
                    },
                }
            }
        }
    </script>
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
    <style media="print">
    @page {
        size: A4;
        margin: 5mm;
    }

    html,
    body {
        width: 210mm;
        height: 297mm;
        margin: 0;
        padding: 0;
    }

    body {
        background: #fff !important;
    }

    header {
        display: none !important;
    }

    .document-container {
        width: 100% !important;
        max-width: 100% !important;
        min-height: 287mm !important;
        margin: 0 !important;
        padding: 8mm !important;
        box-shadow: none !important;
    }

    .border-orange-frame {
        width: 100% !important;
    }
</style>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&amp;family=Noto+Sans+Devanagari:wght@400;600;700&amp;display=swap"
        data-snapdom="injected-import">
</head>

<body class="bg-gray-100 p-4 md:p-8">
    <!-- BEGIN: NavigationBar -->
    <header
        class="max-w-[1000px] mx-auto bg-white border-b border-gray-200 px-6 py-3 flex justify-between items-center rounded-t-lg shadow-sm">
        <h1 class="text-lg font-bold text-gray-800">Allotment Letter</h1>
        <button
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded flex items-center gap-2 text-sm transition-colors"
            data-purpose="download-action" onclick="window.print()"><svg class="h-4 w-4" fill="none"
                stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" stroke-linecap="round"
                    stroke-linejoin="round" stroke-width="2"></path>
            </svg><span class="">Download PDF</span></button>
    </header>
    <!-- END: NavigationBar -->
    <main class="document-container p-4 space-y-4">
        <!-- BEGIN: MainAllotmentBox -->
        <section class="border-orange-frame p-4 flex flex-col items-center">
            <!-- Logo and Header Labels -->
            <div class="text-center space-y-4 mb-8">
                <img alt="Govt. of Haryana Logo" class="mx-auto h-24 w-24 object-contain" src="/Haryana_emblem.png">
                <h2 class="text-2xl font-bold text-gray-800">हाउसिंग फॉर ऑल विभाग, हरियाणा</h2>
                <div class="inline-block bg-green-700 text-white px-8 py-1 rounded text-lg font-semibold">आबंटन पत्र
                </div>
                <h3 class="text-xl font-bold text-orange-600">मुख्य मंत्री शहरी आवास योजना</h3>
                <p class="text-sm text-gray-700">हरियाणा सरकार लाभार्थी को एक लाख रुपये की अदायगी पर एक मरला (30 वर्ग
                    गज) आवासीय प्लॉट प्रदान करने की स्वीकृति प्रदान करती है।</p>
            </div>
            <!-- Details Table -->
            <div class="w-full overflow-x-auto border border-gray-200 rounded-sm mb-10" data-purpose="details-table">
                <table class="w-full text-left border-collapse">
                    <tbody class="text-sm">
                        <tr class="border-b border-gray-200">
                            <th class="py-3 px-4 bg-gray-50 font-semibold text-blue-800 w-1/3">पंजीकरण संख्या</th>
                            <td class="py-3 px-4 text-gray-800">{{ $property->ApplicationNo }}</td>
                        </tr>
                        <tr class="border-b border-gray-200">
                            <th class="py-3 px-4 bg-gray-50 font-semibold text-blue-800">परिवार पहचान पत्र संख्या</th>
                            <td class="py-3 px-4 text-gray-800">{{ $property->PPPId }}</td>
                        </tr>
                        <tr class="border-b border-gray-200">
                            <th class="py-3 px-4 bg-gray-50 font-semibold text-blue-800">लाभार्थी का पूरा नाम</th>
                            <td class="py-3 px-4 text-gray-800">{{ $property->PrivatePurchaserName }}</td>
                        </tr>
                        <tr class="border-b border-gray-200">
                            <th class="py-3 px-4 bg-gray-50 font-semibold text-blue-800">पिता/पति का नाम</th>
                            <td class="py-3 px-4 text-gray-800">{{ $property->PurchaserFatherName }}</td>
                        </tr>
                        <tr class="border-b border-gray-200">
                            <th class="py-3 px-4 bg-gray-50 font-semibold text-blue-800">प्लॉट संख्या</th>
                            <td class="py-3 px-4 text-gray-800 uppercase">{{ $property->SectorName }}</td>
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
                        <img alt="QR Verification" class="w-full h-full" src="/Screenshot 2026-06-22 114102.png">
                    </div>
                    <span class="text-xs font-semibold text-gray-700">कृपया QR कोड स्कैन करें और विवरण सत्यापित
                        करें</span>
                </div>
                <div class="text-right">
                    <p class="text-[10px] text-gray-600">पीपीपी सिस्टम द्वारा सत्यापित डेटा, हस्ताक्षर की आवश्यकता नहीं
                        है</p>
                    <p class="text-[10px] text-red-600 font-bold">*नियम और शर्तें लागू</p>
                </div>
            </div>
        </section>
        <!-- END: MainAllotmentBox -->
        <!-- BEGIN: TermsAndConditionsBox -->
        <section class="border-orange-frame p-4" data-purpose="terms-conditions">
            <h4 class="text-center font-bold text-lg mb-4 underline">नियम और शर्तें</h4>
            <ol class="text-[11px] leading-relaxed text-gray-800 space-y-1 list-decimal list-inside pl-2">
                <li class="">यह पीपीपी सिस्टम द्वारा सत्यापित प्रोविजनल आबंटन पत्र वैध है।</li>
                <li class="">यह प्रोविजनल आबंटन पत्र जारी होने के 30 दिनों के भीतर 10,000/- रुपये तथा शेष 80,000/-
                    रुपये की राशि छह बराबर किस्तों में छह महीनों के अन्दर जमा करवानी होगी।</li>
                <li class="">लाभार्थी को आबंटन पत्र जारी होने की तिथि से 12 मास के अन्दर - 2 आवासीय इकाई का
                    निर्माण शुरू करना होगा और 24 महीनों के अन्दर निर्माण पूर्ण करना होगा।</li>
                <li class="">प्लॉट का उपयोग केवल आवास निर्माण हेतु करना होगा और आवंटन की तिथि से 10 वर्षों तक इसे
                    पट्टे पर/बेचा नहीं जा सकता। हालांकि, आवासीय निर्माण के लिए होम लोन प्राप्त करने हेतु प्लॉट को बैंक
                    या वित्तीय संस्थान के पास गिरवी रखा जा सकता है।</li>
                <li class="">लाभार्थी आबंटन पत्र जारी होने की तिथि से 3 वर्ष पश्चात सरकारी सब्सिडी (मूलधन और
                    ब्याज) को ब्याज सहित हाउसिंग फॉर ऑल विभाग को लौटाकर खुले बाजार में आवासीय इकाई बेच सकता है।</li>
                <li class="">लाभार्थी की मृत्यु होने की स्थिति में, उनके कानूनी उत्तराधिकारी प्लॉट आबंटन की शर्तों
                    और नियमों से बाध्य होंगे।</li>
                <li class="">आबंटन की किसी भी शर्त और नियम का उल्लंघन होने पर, हाउसिंग फॉर ऑल विभाग लाभार्थी को
                    पर्याप्त सुनवाई का अवसर देकर, प्लॉट का कब्जा लेने का अधिकार रखता है। ऐसे मामले में, लाभार्थी किसी भी
                    मुआवजे का हकदार नहीं होगा।</li>
                <li class="">लाभार्थी को प्रधानमंत्री आवास योजना शहरी के (बीएलसी) घटक के अंतर्गत घर के निर्माण
                    हेतु रु. 1.5 लाख तक की वित्तीय सहायता का प्रावधान।</li>
                <li class="">बैंकों/वित्तीय संस्थाओं से घर निर्माण हेतु कम ब्याज पर 6,00,000/- रूपये तक के गृह ऋण
                    (Home Loan) की सुविधा का प्रावधान।</li>
                <li class="">लाभार्थी द्वारा सभी किस्तों/पूर्ण राशि जमा करवाने पश्चात प्लॉट का कब्जा दिया जाएगा।
                </li>
            </ol>
        </section>
        <!-- END: TermsAndConditionsBox -->
    </main>




</body>

</html>

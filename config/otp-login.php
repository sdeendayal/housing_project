<?php

return [
    'session_context_key' => 'otp_login_context',
    'session_mobile_key' => 'otp_login_mobile',

    'sms_template_id' => env('OTP_SMS_TEMPLATE_ID', '1407178178069128769'),
    'sms_message' => 'Your OTP for logging into the Department of Housing For All is {#numeric#}. This OTP is valid for 10 minutes and should not be shared with anyone. - Department of Housing For All, Haryana.',

    'document_otp_sms' => [
        'verify_possession_certificate' => [
            'template_id' => env('POSSESSION_CERT_OTP_SMS_TEMPLATE_ID', '1407178185566184448'),
            'message' => 'Your OTP for verifying your Possession Certificate is {#numeric#}. This OTP is valid for 10 minutes and should not be shared with anyone. - Department of Housing For All, Haryana.',
        ],
        'verify_allotment_letter' => [
            'template_id' => env('ALLOTMENT_LETTER_OTP_SMS_TEMPLATE_ID', '1407178185571263503'),
            'message' => 'Your OTP for verifying your Allotment Letter is {#numeric#}. This OTP is valid for 10 minutes and should not be shared with anyone. - Department of Housing For All, Haryana.',
        ],
    ],

    'pp_application_status_sms' => [
        'template_id' => env('PP_APPLICATION_STATUS_SMS_TEMPLATE_ID', '1407178185581796954'),
        'message' => 'Your Physical Possession application has been {#alphanumeric#}. - Department of Housing For All, Haryana.',
        'status_labels' => [
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'sent_back' => 'Sent Back for Correction',
        ],
    ],

    'mmgay_possession_scheduled_sms' => [
        'template_id' => env('MMGAY_POSSESSION_SCHEDULED_SMS_TEMPLATE_ID', '1477178539772987860'),
        'message' => 'Dear {#alp#}, Mukhyamantri Gramin Awas Yojana (MMGAY) Physical Possession slots have been scheduled for your Application No. {#alp#}. Please login to https://hfa.haryana.gov.in/ to select your preferred slot. - HFA Haryana',
    ],

    'sms_username' => env('OTP_SMS_USERNAME', 'haryanait-sport'),
    'sms_password' => env('OTP_SMS_PASSWORD', 'sports@1234'),
    'sms_sender_id' => env('OTP_SMS_SENDER_ID', 'GOVHRY'),
    'sms_secure_key' => env('OTP_SMS_SECURE_KEY', 'dca7fc77-9e28-4765-bbaa-07bd43197b2e'),
    'sms_gateway_url' => env('OTP_SMS_GATEWAY_URL', 'https://msdgweb.mgov.gov.in/esms/sendsmsrequestDLT'),

    'contexts' => [
        'citizen' => [
            'otp_purpose' => 'login',
            'login_view' => 'mmsay.citizenLogin',
            'verify_view' => 'mmsay.citizenOtpVerify',
            'login_route' => 'citizen.login',
            'verify_page_route' => 'citizen.login.verify-page',
            'not_registered_message' => 'Mobile number is not registered as a citizen account.',
            'scheme' => 'MMSAY',
            'log_label' => 'Citizen',
        ],
        'mmgav_villager' => [
            'otp_purpose' => 'mmgav_villager_login',
            'login_view' => 'mmgay.citizenLogin',
            'verify_view' => 'mmgay.citizenOtpVerify',
            'login_route' => 'mmgav.villager.login',
            'verify_page_route' => 'mmgav.villager.login.verify-page',
            'not_registered_message' => 'Mobile number is not registered as an MMGAV villager account.',
            'scheme' => 'MMGAY',
            'log_label' => 'MMGAV Villager',
        ],
        'department' => [
            'otp_purpose' => 'department_login',
            'login_view' => 'physical-possession.auth.department-login',
            'verify_view' => 'physical-possession.auth.department-otp-verify',
            'login_route' => 'pp.department.login',
            'verify_page_route' => 'pp.department.login.verify-page',
            'not_registered_message' => 'Mobile number is not registered as a department officer account.',
            'log_label' => 'Department',
        ],
        'ews_citizen' => [
            'otp_purpose' => 'ews_citizen_login',
            'login_view' => 'ews.auth.login',
            'verify_view' => 'ews.auth.otp-verify',
            'login_route' => 'ews.citizen.login',
            'verify_page_route' => 'ews.citizen.login.verify-page',
            'not_registered_message' => 'Mobile number is not registered as an EWS citizen account.',
            'scheme' => 'EWS',
            'log_label' => 'EWS Citizen',
        ],
        'ews_developer' => [
            'otp_purpose' => 'ews_developer_login',
            'login_view' => 'ews.developer.login',
            'verify_view' => 'ews.developer.otp-verify',
            'login_route' => 'ews.developer.login',
            'verify_page_route' => 'ews.developer.login.verify-page',
            'not_registered_message' => 'Mobile number is not registered as an EWS developer account.',
            'scheme' => 'EWS',
            'log_label' => 'EWS Developer',
        ],
    ],
];

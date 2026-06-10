<?php

return [
    'session_context_key' => 'otp_login_context',
    'session_mobile_key' => 'otp_login_mobile',

    'contexts' => [
        'citizen' => [
            'role_group' => 'citizen',
            'login_view' => 'mmsay.citizenLogin',
            'verify_view' => 'mmsay.citizenOtpVerify',
            'login_route' => 'citizen.login',
            'verify_page_route' => 'citizen.login.verify-page',
            'not_registered_message' => 'Mobile number is not registered as a citizen account.',
            'wrong_group_message' => 'This mobile is registered as a Department account. Please use Department Officer Login.',
            'wrong_group_slug' => 'department',
            'log_label' => 'Citizen',
        ],
        'department' => [
            'role_group' => 'department',
            'login_view' => 'physical-possession.auth.department-login',
            'verify_view' => 'physical-possession.auth.department-otp-verify',
            'login_route' => 'pp.department.login',
            'verify_page_route' => 'pp.department.login.verify-page',
            'not_registered_message' => 'Mobile number is not registered as a department officer account.',
            'wrong_group_message' => 'This mobile is registered as a Citizen account. Please use User Login / Apply.',
            'wrong_group_slug' => 'citizen',
            'log_label' => 'Department',
        ],
    ],
];

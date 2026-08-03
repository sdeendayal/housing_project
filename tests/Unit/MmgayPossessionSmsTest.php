<?php

namespace Tests\Unit;

use Tests\TestCase;

class MmgayPossessionSmsTest extends TestCase
{
    /**
     * Test that the MMGAY possession scheduled SMS config is present and formats correctly.
     */
    public function test_mmgay_possession_scheduled_sms_config_and_formatting()
    {
        $config = config('otp-login.mmgay_possession_scheduled_sms');

        $this->assertIsArray($config);
        $this->assertEquals('1477178539772987860', $config['template_id']);
        
        $template = $config['message'];
        $this->assertStringContainsString('{#alp#}', $template);

        // Perform replacement
        $applicantName = 'John Doe';
        $appNumber = 'MMGAY/BHIWANI/12345';

        // Replace the first {#alp#} with the applicant's name
        $pos = strpos($template, '{#alp#}');
        $this->assertNotFalse($pos);
        $template = substr_replace($template, $applicantName, $pos, strlen('{#alp#}'));

        // Replace the second {#alp#} with the application number
        $pos = strpos($template, '{#alp#}');
        $this->assertNotFalse($pos);
        $template = substr_replace($template, $appNumber, $pos, strlen('{#alp#}'));

        $this->assertEquals(
            "Dear John Doe, Mukhyamantri Gramin Awas Yojana (MMGAY) Physical Possession slots have been scheduled for your Application No. MMGAY/BHIWANI/12345. Please login to https://hfa.haryana.gov.in/ to select your preferred slot. - HFA Haryana",
            $template
        );
    }

    /**
     * Test that the MMGAY possession absent SMS config is present and formats correctly.
     */
    public function test_mmgay_possession_absent_sms_config_and_formatting()
    {
        $config = config('otp-login.mmgay_possession_absent_sms');

        $this->assertIsArray($config);
        $this->assertEquals('1477178539806041865', $config['template_id']);
        
        $template = $config['message'];
        $this->assertStringContainsString('{#alp#}', $template);

        // Perform replacement
        $applicantName = 'John Doe';
        $visitDate = '01 Aug 2026';

        // Replace the first {#alp#} with the applicant's name
        $pos = strpos($template, '{#alp#}');
        $this->assertNotFalse($pos);
        $template = substr_replace($template, $applicantName, $pos, strlen('{#alp#}'));

        // Replace the second {#alp#} with the visit date
        $pos = strpos($template, '{#alp#}');
        $this->assertNotFalse($pos);
        $template = substr_replace($template, $visitDate, $pos, strlen('{#alp#}'));

        $this->assertEquals(
            "Dear John Doe, you were found absent during your MMGAY Physical Possession visit on 01 Aug 2026. Your slot has been reset, and a new schedule will be shared shortly. Please login to https://hfa.haryana.gov.in/ for updates. - HFA Haryana",
            $template
        );
    }

    /**
     * Test that the MMSAY/HFA possession scheduled SMS config is present and formats correctly.
     */
    public function test_mmsay_possession_scheduled_sms_config_and_formatting()
    {
        $config = config('otp-login.mmsay_possession_scheduled_sms');

        $this->assertIsArray($config);
        $this->assertEquals('1477178539740088117', $config['template_id']);
        
        $template = $config['message'];
        $this->assertStringContainsString('{#alp#}', $template);

        // Perform replacement
        $appNumber = 'PP-2026-161845';

        // Replace the {#alp#} with the application number
        $pos = strpos($template, '{#alp#}');
        $this->assertNotFalse($pos);
        $template = substr_replace($template, $appNumber, $pos, strlen('{#alp#}'));

        $this->assertEquals(
            "Physical Possession slots have been offered for your Application No. PP-2026-161845. Please login to https://hfa.haryana.gov.in/ to select your preferred slot. - HFA Haryana",
            $template
        );
    }

    /**
     * Test that the MMSAY/HFA possession absent SMS config is present and formats correctly.
     */
    public function test_mmsay_possession_absent_sms_config_and_formatting()
    {
        $config = config('otp-login.mmsay_possession_absent_sms');

        $this->assertIsArray($config);
        $this->assertEquals('1477178539760512498', $config['template_id']);
        
        $template = $config['message'];
        $this->assertStringContainsString('{#alp#}', $template);

        // Perform replacement
        $visitDate = '11 Aug 2026';

        // Replace the {#alp#} with the visit date
        $pos = strpos($template, '{#alp#}');
        $this->assertNotFalse($pos);
        $template = substr_replace($template, $visitDate, $pos, strlen('{#alp#}'));

        $this->assertEquals(
            "You were absent for your Physical Possession visit on 11 Aug 2026. Your slot has been reset. A new schedule will be shared soon. Please login to https://hfa.haryana.gov.in/ for updates. - HFA Haryana",
            $template
        );
    }
}

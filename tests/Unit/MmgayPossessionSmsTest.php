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
}

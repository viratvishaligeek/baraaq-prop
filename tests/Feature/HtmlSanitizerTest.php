<?php

namespace Tests\\Feature;

use App\\Support\\HtmlSanitizer;
use PHPUnit\\Framework\\TestCase;

class HtmlSanitizerTest extends TestCase
{
    public function test_rich_text_keeps_basic_formatting_and_removes_unsafe_markup(): void
    {
        $html = '<p onclick="alert(1)">A <strong>formatted</strong> description <script>alert(2)</script><a href="javascript:alert(3)">link</a></p>';

        $safe = HtmlSanitizer::sanitize($html);

        $this->assertStringContainsString('<strong>formatted</strong>', $safe);
        $this->assertStringContainsString('link</a>', $safe);
        $this->assertStringNotContainsString('onclick', $safe);
        $this->assertStringNotContainsString('<script', $safe);
        $this->assertStringNotContainsString('javascript:', $safe);
    }
}

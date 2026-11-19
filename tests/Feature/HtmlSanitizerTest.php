<?php

namespace Tests\Feature;

use App\Support\HtmlSanitizer;
use PHPUnit\Framework\TestCase;

class HtmlSanitizerTest extends TestCase
{
    public function test_rich_text_keeps_basic_formatting_and_removes_unsafe_markup(): void
    {
        $html = '<h2 onclick="alert(1)">A heading</h2><p>A <strong>formatted</strong> description <script>alert(2)</script><a href="javascript:alert(3)">link</a></p><ul><li>First item</li></ul>';

        $safe = HtmlSanitizer::sanitize($html);

        $this->assertStringContainsString('<h2>A heading</h2>', $safe);
        $this->assertStringContainsString('<strong>formatted</strong>', $safe);
        $this->assertStringContainsString('<li>First item</li>', $safe);
        $this->assertStringContainsString('link</a>', $safe);
        $this->assertStringNotContainsString('onclick', $safe);
        $this->assertStringNotContainsString('<script', $safe);
        $this->assertStringNotContainsString('javascript:', $safe);
    }
    public function test_editor_tables_and_images_are_allowed_but_executable_image_sources_are_removed(): void
    {
        $html = '<table><tbody><tr><td>Details</td></tr></tbody></table><img src="/storage/media/home.jpg" alt="Home" onerror="alert(1)"><img src="javascript:alert(2)" alt="Bad">';

        $safe = HtmlSanitizer::sanitize($html);

        $this->assertStringContainsString('<table>', $safe);
        $this->assertStringContainsString('<td>Details</td>', $safe);
        $this->assertStringContainsString('src="/storage/media/home.jpg"', $safe);
        $this->assertStringNotContainsString('onerror', $safe);
        $this->assertStringNotContainsString('javascript:', $safe);
    }

}

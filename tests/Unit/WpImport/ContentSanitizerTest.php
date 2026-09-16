<?php

namespace Tests\Unit\WpImport;

use App\Services\WpImport\Support\ContentSanitizer;
use Tests\TestCase;

class ContentSanitizerTest extends TestCase
{
    use WpFixtures;

    private ContentSanitizer $sanitizer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sanitizer = ContentSanitizer::fromConfig();
    }

    public function test_it_keeps_the_article_inside_a_hand_written_html_widget(): void
    {
        $body = $this->sanitizer->clean($this->articleHtml());

        $this->assertNotSame('', $body, 'The article is wrapped in a section, which must be unwrapped, not dropped.');
        $this->assertStringContainsString('Boyun fıtığı', $body);
        $this->assertGreaterThan(500, strlen($this->sanitizer->text($body)));
    }

    public function test_it_removes_styles_scripts_and_inline_formatting(): void
    {
        $body = $this->sanitizer->clean($this->articleHtml());

        $this->assertStringNotContainsString('<style', $body);
        $this->assertStringNotContainsString('<script', $body);
        $this->assertStringNotContainsString('style=', $body);
        $this->assertStringNotContainsString('class=', $body);
        $this->assertStringNotContainsString('--primary', $body, 'CSS must not survive as text.');
    }

    public function test_it_keeps_only_the_allowed_elements(): void
    {
        $body = $this->sanitizer->clean($this->articleHtml());

        preg_match_all('/<([a-z0-9]+)\b/i', $body, $matches);

        $allowed = array_keys((array) config('wp-import.sanitizer.elements'));

        $this->assertNotEmpty($matches[1]);
        $this->assertSame([], array_diff(array_unique(array_map('strtolower', $matches[1])), $allowed));
    }

    public function test_it_moves_a_body_heading_down_a_level_because_the_template_owns_the_h1(): void
    {
        $body = $this->sanitizer->clean('<h1>Başlık</h1><h2>Alt</h2><h3>Daha alt</h3><p>Metin</p>');

        $this->assertSame('<h2>Başlık</h2><h3>Alt</h3><h4>Daha alt</h4><p>Metin</p>', $body);
    }

    public function test_it_keeps_links_and_images_but_drops_their_other_attributes(): void
    {
        $body = $this->sanitizer->clean(
            '<p><a href="/iletisim" target="_blank" onclick="x()">Randevu</a>'
            .'<img src="/a.jpg" alt="Ağrı" width="500" loading="lazy"></p>',
        );

        $this->assertStringContainsString('href="/iletisim"', $body);
        $this->assertStringContainsString('alt="Ağrı"', $body);
        $this->assertStringNotContainsString('onclick', $body);
        $this->assertStringNotContainsString('target', $body);
        $this->assertStringNotContainsString('width', $body);
    }

    public function test_it_drops_a_javascript_link(): void
    {
        $body = $this->sanitizer->clean('<p><a href="javascript:alert(1)">tıkla</a></p>');

        $this->assertStringNotContainsString('javascript', $body);
        $this->assertStringContainsString('tıkla', $body);
    }

    public function test_it_removes_empty_paragraphs_the_old_editor_left_behind(): void
    {
        $this->assertSame('<p>Metin</p>', $this->sanitizer->clean('<p>&nbsp;</p><p></p><p>Metin</p><p>  </p>'));
    }

    public function test_plain_text_ignores_scripts_and_collapses_whitespace(): void
    {
        $text = $this->sanitizer->text("<style>p{color:red}</style><p>Bir</p>\n\n<p>iki   üç</p>");

        $this->assertSame('Bir iki üç', $text);
    }

    private function articleHtml(): string
    {
        return (string) $this->widget($this->fixture('post-2290'), 'html')->stringSetting('html');
    }
}

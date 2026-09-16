<?php

namespace Tests\Unit\WpImport;

use App\Services\WpImport\Support\PromotionalClaims;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Turkish health advertising rules forbid guarantees, superlatives and success
 * rates. The claims here are the ones the old site actually made.
 */
class PromotionalClaimsTest extends TestCase
{
    #[DataProvider('claimsFromTheOldSite')]
    public function test_it_rejects_a_bullet_that_is_only_a_claim(string $text): void
    {
        $claims = PromotionalClaims::fromConfig();

        $this->assertTrue($claims->rejects($text), "\"{$text}\" should have been rejected.");
        $this->assertContains($text, $claims->removed());
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function claimsFromTheOldSite(): array
    {
        return [
            'success rate' => ['%98 BAŞARI İMKANI'],
            'painless promise' => ['AĞRISIZ - ACISIZ YÖNTEM'],
            'leading practice' => ['Türkiye\'nin önde gelen kliniği'],
            'superlative' => ['En iyi doktor'],
            'certain cure' => ['Kesin çözüm'],
        ];
    }

    #[DataProvider('legitimateText')]
    public function test_it_keeps_a_factual_bullet(string $text): void
    {
        $this->assertFalse(PromotionalClaims::fromConfig()->rejects($text));
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function legitimateText(): array
    {
        return [
            'procedure fact' => ['HEMEN TABURCU İMKANI'],
            'equipment' => ['TEKNOLOJİK AMELİYATHANE'],
            'credential' => ['UZMAN PROFESÖR GÜVENCESİ'],
            'duration' => ['İşlem ortalama 20-30 dakika sürer.'],
            'anaesthesia' => ['İşlem lokal anestezi altında yapılır.'],
        ];
    }

    public function test_it_removes_the_offending_sentence_and_leaves_the_rest(): void
    {
        $claims = PromotionalClaims::fromConfig();

        $body = $claims->scrub(
            '<p>Nükleoplasti lokal anestezi ile yapılır. Yüksek başarı oranları %70-90 arasındadır. '
            .'Hasta aynı gün taburcu olur.</p>',
        );

        $this->assertStringNotContainsString('%70-90', $body);
        $this->assertStringContainsString('lokal anestezi', $body);
        $this->assertStringContainsString('aynı gün taburcu', $body);
        $this->assertStringContainsString('</p>', $body, 'The markup stays balanced.');
        $this->assertNotEmpty($claims->removed());
    }

    public function test_it_removes_a_claim_from_a_list_without_emptying_the_list(): void
    {
        $claims = PromotionalClaims::fromConfig();

        $body = $claims->scrub('<ul><li>Ameliyatsız yöntem</li><li>Kesin çözüm</li><li>Günübirlik işlem</li></ul>');

        $this->assertStringContainsString('Ameliyatsız yöntem', $body);
        $this->assertStringContainsString('Günübirlik işlem', $body);
        $this->assertStringNotContainsString('Kesin çözüm', $body);
        $this->assertSame(3, substr_count($body, '</li>'));
    }

    public function test_untouched_text_comes_back_unchanged(): void
    {
        $claims = PromotionalClaims::fromConfig();
        $body = '<p>Bel fıtığı tanısı MR ile konur.</p>';

        $this->assertSame($body, $claims->scrub($body));
        $this->assertSame([], $claims->removed());
    }
}

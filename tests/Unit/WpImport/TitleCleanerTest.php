<?php

namespace Tests\Unit\WpImport;

use App\Services\WpImport\Support\TitleCleaner;
use App\Services\WpImport\Support\TurkishText;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class TitleCleanerTest extends TestCase
{
    use WpFixtures;

    #[DataProvider('titlesFromTheOldPlaylist')]
    public function test_it_cleans_a_video_title(string $source, string $expected): void
    {
        $this->assertSame($expected, TitleCleaner::fromConfig()->clean($source));
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function titlesFromTheOldPlaylist(): array
    {
        return [
            'speaker in front' => [
                'Profesör Doktor Hüsnü Süslü Nükleoplasti İşlemi Nedir?',
                'Nükleoplasti İşlemi Nedir?',
            ],
            'speaker with a pipe' => [
                'Doç. Dr. Hüsnü Süslü I BACAK AGRILARI',
                'Bacak ağrıları',
            ],
            'speaker with a dash' => [
                'Prof. Dr. Hüsnü Süslü - NTV Uzman Bakış',
                'NTV Uzman Bakış',
            ],
            'speaker at the end' => [
                'Her Bel Ağrısı Fıtık mıdır? Profesör Doktor Hüsnü Süslü',
                'Her Bel Ağrısı Fıtık mıdır?',
            ],
            'hashtag' => [
                'AĞRILARINIZ YAŞAM KALİTENİZİ DÜŞÜRMESİN #shorts',
                'Ağrılarınız yaşam kalitenizi düşürmesin',
            ],
            'shouting with an acronym' => [
                'Prof. Dr. Hüsnü Süslü  TV100 BİZBİZE\'DE...',
                'TV100 bizbize\'de...',
            ],
            'typo in the source' => [
                'Nöral Foraminal Steroz',
                'Nöral Foraminal Stenoz',
            ],
            'missing soft g' => [
                'Doç. Dr. Hüsnü Süslü I  GÖGÜS VE SIRT AĞRILARI',
                'Göğüs ve sırt ağrıları',
            ],
            'already sentence case' => [
                'Akıllı İlaç Kullanımı',
                'Akıllı İlaç Kullanımı',
            ],
        ];
    }

    public function test_it_cleans_every_title_in_the_real_playlist(): void
    {
        $cleaner = TitleCleaner::fromConfig();
        $playlist = $this->widget($this->fixture('page-752'), 'video-playlist');

        $titles = [];

        foreach ($playlist->rows('tabs') as $tab) {
            $titles[] = $cleaner->clean((string) ($tab['title'] ?? ''));
        }

        $this->assertCount(51, $titles);

        foreach ($titles as $title) {
            $this->assertNotSame('', $title);
            $this->assertStringNotContainsStringIgnoringCase('#shorts', $title);
            $this->assertStringNotContainsString('Steroz', $title);
            $this->assertMatchesRegularExpression('/^\p{Lu}|^\d/u', $title, 'Titles start with a capital.');
        }

        /* One film on the old site was titled with nothing but the name. */
        $named = array_values(array_filter(
            $titles,
            fn (string $title): bool => str_contains(mb_strtolower($title), 'süslü'),
        ));

        $this->assertSame(['Doç. Dr. Hüsnü Süslü'], $named);
    }

    public function test_turkish_lower_case_keeps_the_dotless_i(): void
    {
        $this->assertSame('ağrıları', TurkishText::lower('AĞRILARI'));
        $this->assertSame('işlem', TurkishText::lower('İŞLEM'));
        $this->assertSame('AĞRI', TurkishText::upper('ağrı'));
        $this->assertSame('agri-turleri', TurkishText::slug('Ağrı Türleri'));
        $this->assertSame('bel-ve-bacak-agrilari', TurkishText::slug('Bel ve Bacak Ağrıları'));
    }

    public function test_sentence_case_only_fires_on_shouting(): void
    {
        $this->assertTrue(TurkishText::isShouting('BACAK AGRILARI'));
        $this->assertFalse(TurkishText::isShouting('Bacak Ağrıları'));
        $this->assertSame('Bacak ağrıları', TurkishText::sentenceCase('BACAK AĞRILARI'));
        $this->assertSame('Bacak Ağrıları', TurkishText::sentenceCase('Bacak Ağrıları'));
    }
}

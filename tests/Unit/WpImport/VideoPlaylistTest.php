<?php

namespace Tests\Unit\WpImport;

use App\Services\WpImport\Support\YouTubeUrl;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class VideoPlaylistTest extends TestCase
{
    use WpFixtures;

    #[DataProvider('playlistUrls')]
    public function test_it_reads_the_youtube_id_of_every_link_shape(string $url, ?string $expected): void
    {
        $this->assertSame($expected, YouTubeUrl::id($url));
    }

    /**
     * @return array<string, array{0: string, 1: string|null}>
     */
    public static function playlistUrls(): array
    {
        return [
            'watch' => ['https://www.youtube.com/watch?v=obBgAWjzUpQ', 'obBgAWjzUpQ'],
            'short form' => ['https://www.youtube.com/shorts/JLp83nsJ5YA', 'JLp83nsJ5YA'],
            'leading dash in the id' => ['https://www.youtube.com/shorts/-0BzLM8NrbE', '-0BzLM8NrbE'],
            'with a start time' => ['https://www.youtube.com/watch?v=nNSTXMkG7rE&t=1s', 'nNSTXMkG7rE'],
            'youtu.be' => ['https://youtu.be/K7nfH2tHB0A', 'K7nfH2tHB0A'],
            'embed' => ['https://www.youtube.com/embed/HqO9E6QT_j4', 'HqO9E6QT_j4'],
            'not youtube' => ['https://vimeo.com/235215203', null],
            'empty' => ['', null],
        ];
    }

    public function test_the_real_playlist_holds_fifty_one_entries_and_fifty_films(): void
    {
        $playlist = $this->widget($this->fixture('page-752'), 'video-playlist');
        $tabs = $playlist->rows('tabs');

        $ids = array_values(array_filter(array_map(
            fn (array $tab): ?string => YouTubeUrl::id((string) ($tab['youtube_url'] ?? '')),
            $tabs,
        )));

        $this->assertCount(51, $tabs);
        $this->assertCount(51, $ids, 'Every entry links to YouTube.');
        $this->assertCount(50, array_unique($ids), 'One film is listed twice.');
    }

    public function test_every_entry_carries_a_duration(): void
    {
        $playlist = $this->widget($this->fixture('page-752'), 'video-playlist');

        foreach ($playlist->rows('tabs') as $tab) {
            $this->assertNotEmpty($tab['duration'] ?? null);
            $this->assertIsInt(YouTubeUrl::seconds((string) $tab['duration']));
        }
    }

    #[DataProvider('durations')]
    public function test_it_reads_a_duration_in_seconds(?string $duration, ?int $expected): void
    {
        $this->assertSame($expected, YouTubeUrl::seconds($duration));
    }

    /**
     * @return array<string, array{0: string|null, 1: int|null}>
     */
    public static function durations(): array
    {
        return [
            'short' => ['1:38', 98],
            'programme' => ['31:59', 1919],
            'with hours' => ['1:05:00', 3900],
            'nonsense' => ['bir dakika', null],
            'missing' => [null, null],
        ];
    }
}

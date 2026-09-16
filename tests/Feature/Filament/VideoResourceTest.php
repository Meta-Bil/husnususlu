<?php

namespace Tests\Feature\Filament;

use App\Enums\VideoCategory;
use App\Filament\Resources\Videos\Pages\CreateVideo;
use App\Filament\Resources\Videos\Pages\EditVideo;
use App\Filament\Resources\Videos\Pages\ListVideos;
use App\Filament\Resources\Videos\Schemas\VideoForm;
use App\Models\Video;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;

class VideoResourceTest extends AdminPanelTestCase
{
    public function test_it_lists_videos(): void
    {
        $video = $this->makeVideo();

        Livewire::test(ListVideos::class)
            ->assertOk()
            ->assertCanSeeTableRecords([$video]);
    }

    public function test_it_filters_by_category(): void
    {
        $info = $this->makeVideo();
        $tv = $this->makeVideo('abcdefghijk', VideoCategory::Tv);

        Livewire::test(ListVideos::class)
            ->filterTable('category', VideoCategory::Tv->value)
            ->assertCanSeeTableRecords([$tv])
            ->assertCanNotSeeTableRecords([$info]);
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function youtubeUrls(): array
    {
        return [
            'watch url' => ['https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
            'watch url with extra params' => ['https://www.youtube.com/watch?list=PL1&v=dQw4w9WgXcQ&t=20s', 'dQw4w9WgXcQ'],
            'short url' => ['https://youtu.be/dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
            'embed url' => ['https://www.youtube.com/embed/dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
            'shorts url' => ['https://www.youtube.com/shorts/dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
            'bare id' => ['dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
        ];
    }

    #[DataProvider('youtubeUrls')]
    public function test_it_normalises_youtube_urls_to_an_id(string $input, string $expected): void
    {
        $this->assertSame($expected, VideoForm::youtubeId($input));
    }

    public function test_it_creates_a_video_from_a_pasted_url(): void
    {
        Livewire::test(CreateVideo::class)
            ->fillForm([
                'youtube_id' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'title' => ['tr' => 'Bel ağrısı nedir?'],
                'description' => ['tr' => 'Kısa açıklama'],
                'program' => ['tr' => 'Sağlık Hattı'],
                'category' => VideoCategory::Tv->value,
                'channel' => 'TRT',
                'duration' => '12:34',
                'published_on' => '2024-05-01',
                'sort_order' => 3,
                'is_featured' => true,
                'is_visible' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $video = Video::query()->latest('id')->firstOrFail();

        $this->assertSame('dQw4w9WgXcQ', $video->youtube_id);
        $this->assertSame('Sağlık Hattı', $video->localized('program', 'tr'));
        $this->assertSame(VideoCategory::Tv, $video->category);
    }

    public function test_it_edits_a_video(): void
    {
        $video = $this->makeVideo();

        Livewire::test(EditVideo::class, ['record' => $video->getKey()])
            ->assertOk()
            ->assertFormSet(['title.tr' => 'Video'])
            ->fillForm(['channel' => 'NTV'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('NTV', $video->refresh()->channel);
    }

    private function makeVideo(string $youtubeId = 'dQw4w9WgXcQ', VideoCategory $category = VideoCategory::Info): Video
    {
        return Video::create([
            'youtube_id' => $youtubeId,
            'title' => ['tr' => 'Video'],
            'category' => $category,
        ]);
    }
}

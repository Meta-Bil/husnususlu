<?php

namespace App\Blocks;

use App\Blocks\Concerns\InteractsWithContent;
use App\Enums\VideoCategory;
use App\Filament\Support\LocaleTabs;
use App\Models\Video;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

/**
 * Videos from the gallery: one large, featured film followed by a grid of cards.
 */
class VideoGridBlock extends AbstractBlock
{
    use InteractsWithContent;

    public static function key(): string
    {
        return 'video_grid';
    }

    public static function label(): string
    {
        return 'Video listesi';
    }

    public static function icon(): string
    {
        return 'heroicon-o-play-circle';
    }

    public static function schema(): array
    {
        return [
            LocaleTabs::make(fn (string $locale): array => [
                TextInput::make("eyebrow.{$locale}")->label('Üst etiket')->maxLength(80),
                Textarea::make("title.{$locale}")->label('Başlık')->rows(2),
                TextInput::make("accent.{$locale}")->label('Vurgulu kelime (altın renkli)')->maxLength(80),
                TextInput::make("link_label.{$locale}")
                    ->label('Bağlantı metni')
                    ->placeholder(__('front.all_videos', [], $locale))
                    ->maxLength(80),
            ]),

            TextInput::make('link_url')
                ->label('Bağlantı adresi')
                ->helperText('Boş bırakılırsa video galerisine bağlanır.')
                ->maxLength(255),

            Section::make('Videolar')
                ->schema([
                    Select::make('source')
                        ->label('Kaynak')
                        ->options([
                            'category' => 'Kategoriye göre',
                            'selected' => 'Elle seçilen videolar',
                        ])
                        ->default('category')
                        ->required()
                        ->live(),
                    Select::make('category')
                        ->label('Kategori')
                        ->options(VideoCategory::class)
                        ->placeholder('Tüm kategoriler')
                        ->visible(fn (callable $get): bool => $get('source') !== 'selected'),
                    TextInput::make('limit')
                        ->label('Video sayısı')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(24)
                        ->default(3)
                        ->visible(fn (callable $get): bool => $get('source') !== 'selected'),
                    Select::make('video_ids')
                        ->label('Videolar')
                        ->options(fn (): array => static::videoOptions())
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->visible(fn (callable $get): bool => $get('source') === 'selected'),
                    Toggle::make('show_featured')
                        ->label('İlk videoyu büyük göster')
                        ->default(true),
                ])
                ->columns(1),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function transform(array $data, string $locale): array
    {
        $videos = static::videos($data);
        $cards = $videos->map(fn (Video $video): array => static::card($video, $locale))->all();
        $featured = null;

        if (($data['show_featured'] ?? true) && $cards !== []) {
            $featured = array_shift($cards);
        }

        return [
            'eyebrow' => static::text($data, 'eyebrow', $locale),
            'title' => static::text($data, 'title', $locale),
            'accent' => static::text($data, 'accent', $locale),
            'linkLabel' => static::text($data, 'link_label', $locale) ?? __('front.all_videos', [], $locale),
            'linkUrl' => ($data['link_url'] ?? null) ?: static::galleryUrl($locale),
            'featured' => $featured,
            'items' => $cards,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return Collection<int, Video>
     */
    private static function videos(array $data): Collection
    {
        if (($data['source'] ?? 'category') === 'selected') {
            $ids = array_values(array_filter(array_map('intval', $data['video_ids'] ?? [])));

            if ($ids === []) {
                return new Collection;
            }

            return Video::query()
                ->visible()
                ->whereIn('id', $ids)
                ->get()
                ->sortBy(fn (Video $video): int => array_search($video->id, $ids, true))
                ->values();
        }

        return Video::query()
            ->visible()
            ->when(filled($data['category'] ?? null), fn ($query) => $query->where('category', $data['category']))
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderByDesc('published_on')
            ->limit(max(1, (int) ($data['limit'] ?? 3)))
            ->get();
    }

    /**
     * @return array<string, mixed>
     */
    private static function card(Video $video, string $locale): array
    {
        $program = $video->localized('program', $locale);

        return [
            'title' => $video->localized('title', $locale),
            'description' => $video->localized('description', $locale),
            'channel' => $video->channel,
            'program' => $program,
            'eyebrow' => trim(implode(' · ', array_filter([$video->channel, $program]))) ?: null,
            'duration' => $video->duration,
            'thumbnail' => $video->thumbnailUrl(),
            'url' => $video->watchUrl(),
        ];
    }

    private static function galleryUrl(string $locale): ?string
    {
        return Route::has("{$locale}.videos") ? route("{$locale}.videos") : null;
    }
}

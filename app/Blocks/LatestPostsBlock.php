<?php

namespace App\Blocks;

use App\Blocks\Concerns\InteractsWithContent;
use App\Filament\Support\LocaleTabs;
use App\Models\Post;
use App\Support\Localization\LocaleUrls;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

/**
 * The reading corner: a few blog posts as typographic cards, either the latest
 * ones or a hand-picked selection.
 */
class LatestPostsBlock extends AbstractBlock
{
    use InteractsWithContent;

    public static function key(): string
    {
        return 'latest_posts';
    }

    public static function label(): string
    {
        return 'Blog yazıları';
    }

    public static function icon(): string
    {
        return 'heroicon-o-newspaper';
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
                    ->placeholder(__('front.all_posts', [], $locale))
                    ->maxLength(80),
            ]),

            TextInput::make('link_url')
                ->label('Bağlantı adresi')
                ->helperText('Boş bırakılırsa blog sayfasına bağlanır.')
                ->maxLength(255),

            Section::make('Yazılar')
                ->schema([
                    Select::make('source')
                        ->label('Kaynak')
                        ->options([
                            'latest' => 'En yeni yazılar',
                            'selected' => 'Elle seçilen yazılar',
                        ])
                        ->default('latest')
                        ->required()
                        ->live(),
                    Select::make('post_category_id')
                        ->label('Kategori')
                        ->options(fn (): array => static::postCategoryOptions())
                        ->placeholder('Tüm kategoriler')
                        ->visible(fn (callable $get): bool => $get('source') !== 'selected'),
                    TextInput::make('limit')
                        ->label('Yazı sayısı')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(12)
                        ->default(3)
                        ->visible(fn (callable $get): bool => $get('source') !== 'selected'),
                    Select::make('post_ids')
                        ->label('Yazılar')
                        ->options(fn (): array => static::postOptions())
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->visible(fn (callable $get): bool => $get('source') === 'selected'),
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
        return [
            'eyebrow' => static::text($data, 'eyebrow', $locale),
            'title' => static::text($data, 'title', $locale),
            'accent' => static::text($data, 'accent', $locale),
            'linkLabel' => static::text($data, 'link_label', $locale) ?? __('front.all_posts', [], $locale),
            'linkUrl' => ($data['link_url'] ?? null) ?: static::blogUrl($locale),
            'items' => static::posts($data, $locale)
                ->map(fn (Post $post): array => [
                    'title' => $post->localized('title', $locale),
                    'excerpt' => $post->localized('excerpt', $locale),
                    'category' => $post->category?->localized('name', $locale),
                    'date' => $post->published_at?->translatedFormat('j F Y'),
                    'url' => LocaleUrls::post($post, $locale),
                ])
                ->all(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return Collection<int, Post>
     */
    private static function posts(array $data, string $locale): Collection
    {
        if (($data['source'] ?? 'latest') === 'selected') {
            $ids = array_values(array_filter(array_map('intval', $data['post_ids'] ?? [])));

            if ($ids === []) {
                return new Collection;
            }

            return Post::query()
                ->with('category')
                ->whereIn('id', $ids)
                ->get()
                ->sortBy(fn (Post $post): int => array_search($post->id, $ids, true))
                ->values();
        }

        return Post::query()
            ->with('category')
            ->live($locale)
            ->when(
                filled($data['post_category_id'] ?? null),
                fn ($query) => $query->where('post_category_id', $data['post_category_id']),
            )
            ->orderByDesc('published_at')
            ->limit(max(1, (int) ($data['limit'] ?? 3)))
            ->get();
    }

    private static function blogUrl(string $locale): ?string
    {
        return Route::has("{$locale}.blog.index") ? route("{$locale}.blog.index") : null;
    }
}

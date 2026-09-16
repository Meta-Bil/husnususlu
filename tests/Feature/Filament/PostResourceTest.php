<?php

namespace Tests\Feature\Filament;

use App\Enums\ContentStatus;
use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\Posts\Pages\EditPost;
use App\Filament\Resources\Posts\Pages\ListPosts;
use App\Models\Post;
use App\Models\PostCategory;
use Livewire\Livewire;

class PostResourceTest extends AdminPanelTestCase
{
    public function test_it_lists_posts_with_their_category(): void
    {
        $category = $this->makeCategory();
        $post = $this->makePost();
        $post->forceFill(['post_category_id' => $category->getKey()])->save();

        Livewire::test(ListPosts::class)
            ->assertOk()
            ->assertCanSeeTableRecords([$post])
            ->assertTableColumnStateSet('category', 'Ağrı', $post);
    }

    public function test_it_filters_posts_by_category(): void
    {
        $category = $this->makeCategory();
        $categorised = $this->makePost();
        $categorised->forceFill(['post_category_id' => $category->getKey()])->save();

        $uncategorised = Post::create([
            'title' => ['tr' => 'Kategorisiz yazı'],
            'slug' => ['tr' => 'kategorisiz-yazi'],
            'status' => ContentStatus::Draft,
            'locales_enabled' => ['tr'],
        ]);

        Livewire::test(ListPosts::class)
            ->filterTable('post_category_id', $category->getKey())
            ->assertCanSeeTableRecords([$categorised])
            ->assertCanNotSeeTableRecords([$uncategorised]);
    }

    public function test_it_creates_a_post_with_body_and_faq(): void
    {
        $category = $this->makeCategory();

        Livewire::test(CreatePost::class)
            ->fillForm([
                'title' => ['tr' => 'Bel fıtığı belirtileri'],
                'slug' => ['tr' => 'bel-fitigi-belirtileri'],
                'body' => ['tr' => '<p>Yazı içeriği</p>'],
                'post_category_id' => $category->getKey(),
                'video_youtube_id' => 'dQw4w9WgXcQ',
                'reading_time' => 4,
                'faq' => [
                    [
                        'question' => ['tr' => 'Ameliyat şart mı?'],
                        'answer' => ['tr' => 'Çoğu hastada değil.'],
                    ],
                ],
                'status' => ContentStatus::Published->value,
                'locales_enabled' => ['tr'],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $post = Post::query()->latest('id')->firstOrFail();

        $this->assertSame('<p>Yazı içeriği</p>', $post->localized('body', 'tr'));
        $this->assertSame($category->getKey(), $post->post_category_id);
        $this->assertSame(4, $post->reading_time);
        $this->assertSame('Ameliyat şart mı?', data_get($post->faq, '0.question.tr'));
    }

    public function test_it_edits_a_post(): void
    {
        $post = $this->makePost();

        Livewire::test(EditPost::class, ['record' => $post->getKey()])
            ->assertOk()
            ->assertFormSet(['title.tr' => 'Ağrı ve uyku'])
            ->fillForm(['excerpt' => ['tr' => 'Yeni özet']])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Yeni özet', $post->refresh()->localized('excerpt', 'tr'));
    }

    private function makeCategory(): PostCategory
    {
        return PostCategory::create([
            'name' => ['tr' => 'Ağrı'],
            'slug' => ['tr' => 'agri'],
        ]);
    }

    private function makePost(): Post
    {
        return Post::create([
            'title' => ['tr' => 'Ağrı ve uyku'],
            'slug' => ['tr' => 'agri-ve-uyku'],
            'body' => ['tr' => '<p>İçerik</p>'],
            'status' => ContentStatus::Published,
            'locales_enabled' => ['tr'],
        ]);
    }
}

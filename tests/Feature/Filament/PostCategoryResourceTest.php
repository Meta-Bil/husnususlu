<?php

namespace Tests\Feature\Filament;

use App\Enums\ContentStatus;
use App\Filament\Resources\PostCategories\Pages\CreatePostCategory;
use App\Filament\Resources\PostCategories\Pages\EditPostCategory;
use App\Filament\Resources\PostCategories\Pages\ListPostCategories;
use App\Models\Post;
use App\Models\PostCategory;
use Livewire\Livewire;

class PostCategoryResourceTest extends AdminPanelTestCase
{
    public function test_it_lists_categories_with_their_post_count(): void
    {
        $category = $this->makeCategory();

        Post::create([
            'post_category_id' => $category->getKey(),
            'title' => ['tr' => 'Yazı'],
            'slug' => ['tr' => 'yazi'],
            'status' => ContentStatus::Published,
            'locales_enabled' => ['tr'],
        ]);

        Livewire::test(ListPostCategories::class)
            ->assertOk()
            ->assertCanSeeTableRecords([$category])
            ->assertTableColumnStateSet('posts_count', 1, $category);
    }

    public function test_it_creates_a_category(): void
    {
        Livewire::test(CreatePostCategory::class)
            ->fillForm([
                'name' => ['tr' => 'Girişimsel tedaviler'],
                'slug' => ['tr' => 'girisimsel-tedaviler'],
                'description' => ['tr' => 'Kategori açıklaması'],
                'sort_order' => 2,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $category = PostCategory::query()->latest('id')->firstOrFail();

        $this->assertSame('Girişimsel tedaviler', $category->localized('name', 'tr'));
        $this->assertSame(2, $category->sort_order);
    }

    public function test_it_edits_a_category(): void
    {
        $category = $this->makeCategory();

        Livewire::test(EditPostCategory::class, ['record' => $category->getKey()])
            ->assertOk()
            ->assertFormSet(['name.tr' => 'Ağrı'])
            ->fillForm(['description' => ['tr' => 'Yeni açıklama']])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Yeni açıklama', $category->refresh()->localized('description', 'tr'));
    }

    private function makeCategory(): PostCategory
    {
        return PostCategory::create([
            'name' => ['tr' => 'Ağrı'],
            'slug' => ['tr' => 'agri'],
        ]);
    }
}

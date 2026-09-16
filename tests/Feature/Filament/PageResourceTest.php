<?php

namespace Tests\Feature\Filament;

use App\Enums\ContentStatus;
use App\Enums\PageTemplate;
use App\Filament\Resources\Pages\Pages\CreatePage;
use App\Filament\Resources\Pages\Pages\EditPage;
use App\Filament\Resources\Pages\Pages\ListPages;
use App\Models\Page;
use Livewire\Livewire;

class PageResourceTest extends AdminPanelTestCase
{
    public function test_it_lists_pages(): void
    {
        $page = $this->makePage();

        Livewire::test(ListPages::class)
            ->assertOk()
            ->assertCanSeeTableRecords([$page]);
    }

    public function test_it_creates_a_page_with_translations(): void
    {
        Livewire::test(CreatePage::class)
            ->fillForm([
                'title' => ['tr' => 'Ağrı kliniği', 'en' => 'Pain clinic'],
                'slug' => ['tr' => 'agri-klinigi', 'en' => 'pain-clinic'],
                'template' => PageTemplate::Default->value,
                'schema_type' => 'WebPage',
                'status' => ContentStatus::Published->value,
                'locales_enabled' => ['tr', 'en'],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $page = Page::query()->latest('id')->firstOrFail();

        $this->assertSame('Ağrı kliniği', $page->localized('title', 'tr'));
        $this->assertSame('pain-clinic', $page->slugFor('en'));
        $this->assertSame(ContentStatus::Published, $page->status);
        $this->assertSame(['tr', 'en'], $page->locales_enabled);
    }

    public function test_it_requires_the_default_locale_only(): void
    {
        Livewire::test(CreatePage::class)
            ->fillForm([
                'title' => ['tr' => null],
                'slug' => ['tr' => null],
                'locales_enabled' => ['tr'],
            ])
            ->call('create')
            ->assertHasFormErrors(['title.tr', 'slug.tr'])
            ->assertHasNoFormErrors(['title.en', 'slug.en']);
    }

    public function test_it_edits_a_page(): void
    {
        $page = $this->makePage();

        Livewire::test(EditPage::class, ['record' => $page->getKey()])
            ->assertOk()
            ->assertFormSet(['title.tr' => 'Hakkımda', 'title.en' => 'About'])
            ->fillForm(['excerpt' => ['tr' => 'Kısa özet']])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Kısa özet', $page->refresh()->localized('excerpt', 'tr'));
    }

    private function makePage(): Page
    {
        return Page::create([
            'title' => ['tr' => 'Hakkımda', 'en' => 'About'],
            'slug' => ['tr' => 'hakkimda', 'en' => 'about'],
            'template' => PageTemplate::Default,
            'status' => ContentStatus::Published,
            'locales_enabled' => ['tr', 'en'],
        ]);
    }
}

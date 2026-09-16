<?php

namespace Tests\Feature\Filament;

use App\Enums\ContentStatus;
use App\Enums\MenuItemType;
use App\Enums\PageTemplate;
use App\Filament\Resources\Menus\Pages\CreateMenu;
use App\Filament\Resources\Menus\Pages\EditMenu;
use App\Filament\Resources\Menus\Pages\ListMenus;
use App\Filament\Resources\Menus\RelationManagers\ItemsRelationManager;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

class MenuResourceTest extends AdminPanelTestCase
{
    public function test_it_lists_menus(): void
    {
        $menu = $this->makeMenu();

        Livewire::test(ListMenus::class)
            ->assertOk()
            ->assertCanSeeTableRecords([$menu]);
    }

    public function test_it_creates_a_menu(): void
    {
        Livewire::test(CreateMenu::class)
            ->fillForm([
                'key' => 'footer',
                'name' => 'Alt menü',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('menus', ['key' => 'footer', 'name' => 'Alt menü']);
    }

    public function test_it_edits_a_menu(): void
    {
        $menu = $this->makeMenu();

        Livewire::test(EditMenu::class, ['record' => $menu->getKey()])
            ->assertOk()
            ->assertFormSet(['key' => 'main'])
            ->fillForm(['name' => 'Ana menü (yeni)'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Ana menü (yeni)', $menu->refresh()->name);
    }

    public function test_the_items_relation_manager_lists_items(): void
    {
        $menu = $this->makeMenu();
        $page = $this->makePage();

        $parent = MenuItem::create([
            'menu_id' => $menu->getKey(),
            'label' => ['tr' => 'Tedaviler'],
            'type' => MenuItemType::Page,
            'linkable_type' => Page::class,
            'linkable_id' => $page->getKey(),
        ]);

        $child = MenuItem::create([
            'menu_id' => $menu->getKey(),
            'parent_id' => $parent->getKey(),
            'label' => ['tr' => 'Bel ağrısı'],
            'type' => MenuItemType::Url,
            'url' => ['tr' => 'https://example.com'],
        ]);

        Livewire::test(ItemsRelationManager::class, [
            'ownerRecord' => $menu,
            'pageClass' => EditMenu::class,
        ])
            ->assertOk()
            ->assertCanSeeTableRecords([$parent, $child]);
    }

    public function test_the_items_relation_manager_creates_a_nested_page_item(): void
    {
        $menu = $this->makeMenu();
        $page = $this->makePage();

        $parent = MenuItem::create([
            'menu_id' => $menu->getKey(),
            'label' => ['tr' => 'Kurumsal'],
            'type' => MenuItemType::Url,
            'url' => ['tr' => 'https://example.com'],
        ]);

        Livewire::test(ItemsRelationManager::class, [
            'ownerRecord' => $menu,
            'pageClass' => EditMenu::class,
        ])
            ->callAction(TestAction::make('create')->table(), [
                'type' => MenuItemType::Page->value,
                'linkable_id' => $page->getKey(),
                'label' => ['tr' => 'Hakkımda'],
                'parent_id' => $parent->getKey(),
                'target' => '_self',
                'sort_order' => 1,
                'is_visible' => true,
            ])
            ->assertHasNoActionErrors();

        $item = MenuItem::query()->latest('id')->firstOrFail();

        $this->assertSame(MenuItemType::Page, $item->type);
        $this->assertSame(Page::class, $item->linkable_type);
        $this->assertSame($page->getKey(), $item->linkable_id);
        $this->assertSame($parent->getKey(), $item->parent_id);
        $this->assertSame('Hakkımda', $item->localized('label', 'tr'));
    }

    private function makeMenu(): Menu
    {
        return Menu::create(['key' => 'main', 'name' => 'Ana menü']);
    }

    private function makePage(): Page
    {
        return Page::create([
            'title' => ['tr' => 'Hakkımda'],
            'slug' => ['tr' => 'hakkimda'],
            'template' => PageTemplate::Default,
            'status' => ContentStatus::Published,
            'locales_enabled' => ['tr'],
        ]);
    }
}

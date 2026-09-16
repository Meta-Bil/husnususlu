<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\Redirects\Pages\CreateRedirect;
use App\Filament\Resources\Redirects\Pages\EditRedirect;
use App\Filament\Resources\Redirects\Pages\ListRedirects;
use App\Models\Redirect;
use App\Support\Http\RedirectResolver;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;

class RedirectResourceTest extends AdminPanelTestCase
{
    public function test_it_lists_redirects(): void
    {
        $redirect = $this->makeRedirect();

        Livewire::test(ListRedirects::class)
            ->assertOk()
            ->assertCanSeeTableRecords([$redirect]);
    }

    public function test_it_creates_a_redirect_and_flushes_the_resolver_cache(): void
    {
        $this->warmResolverCache();

        Livewire::test(CreateRedirect::class)
            ->fillForm([
                'from_path' => '/eski-adres',
                'to_path' => '/yeni-adres',
                'status_code' => 301,
                'is_active' => true,
                'note' => 'WordPress taşıması',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('redirects', [
            'from_path' => '/eski-adres',
            'to_path' => '/yeni-adres',
            'status_code' => 301,
        ]);

        $this->assertResolverCacheIsCold();
    }

    public function test_the_hit_counter_is_shown_but_never_written(): void
    {
        $redirect = $this->makeRedirect();
        $redirect->forceFill(['hits' => 7, 'last_hit_at' => now()])->save();

        Livewire::test(EditRedirect::class, ['record' => $redirect->getKey()])
            ->assertOk()
            ->assertFormSet(['from_path' => '/agri-tedavisi'])
            ->assertSee('Kullanım sayısı')
            ->fillForm(['note' => 'Not eklendi'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(7, $redirect->refresh()->hits);
    }

    public function test_it_edits_a_redirect_and_flushes_the_resolver_cache(): void
    {
        $redirect = $this->makeRedirect();

        $this->warmResolverCache();

        Livewire::test(EditRedirect::class, ['record' => $redirect->getKey()])
            ->fillForm(['is_active' => false])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertFalse($redirect->refresh()->is_active);
        $this->assertResolverCacheIsCold();
    }

    private function makeRedirect(): Redirect
    {
        return Redirect::create([
            'from_path' => '/agri-tedavisi',
            'to_path' => '/girisimsel-tedaviler',
            'status_code' => 301,
            'is_active' => true,
        ]);
    }

    private function warmResolverCache(): void
    {
        app(RedirectResolver::class)->map();

        $this->assertTrue(Cache::has('redirects.map'));
    }

    private function assertResolverCacheIsCold(): void
    {
        $this->assertFalse(Cache::has('redirects.map'), 'The redirect map should have been flushed.');
    }
}

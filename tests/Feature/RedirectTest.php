<?php

namespace Tests\Feature;

use App\Models\Redirect;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_old_url_is_redirected_permanently(): void
    {
        Redirect::create([
            'from_path' => '/ameliyatsiz-fitik-tedavisi',
            'to_path' => '/girisimsel-tedaviler/nukleoplasti',
        ]);

        $this->get('/ameliyatsiz-fitik-tedavisi')
            ->assertStatus(301)
            ->assertRedirect('/girisimsel-tedaviler/nukleoplasti');
    }

    public function test_a_trailing_slash_and_upper_case_still_match(): void
    {
        Redirect::create([
            'from_path' => '/eski-sayfa',
            'to_path' => '/yeni-sayfa',
        ]);

        $this->get('/Eski-Sayfa/')->assertRedirect('/yeni-sayfa');
    }

    public function test_the_query_string_is_kept(): void
    {
        Redirect::create([
            'from_path' => '/eski-sayfa',
            'to_path' => '/yeni-sayfa',
        ]);

        $this->get('/eski-sayfa?utm_source=google')
            ->assertRedirect('/yeni-sayfa?utm_source=google');
    }

    public function test_an_inactive_redirect_is_ignored(): void
    {
        Redirect::create([
            'from_path' => '/kapali',
            'to_path' => '/yeni-sayfa',
            'is_active' => false,
        ]);

        $this->get('/kapali')->assertNotFound();
    }

    public function test_a_hit_is_recorded(): void
    {
        $redirect = Redirect::create([
            'from_path' => '/sayilan',
            'to_path' => '/yeni-sayfa',
        ]);

        $this->get('/sayilan');

        $this->assertSame(1, $redirect->fresh()->hits);
        $this->assertNotNull($redirect->fresh()->last_hit_at);
    }
}

<?php

namespace Tests\Feature;

use App\Enums\ContentStatus;
use App\Enums\TreatmentKind;
use App\Models\Treatment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TreatmentPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_pain_type_page_renders_with_its_breadcrumb(): void
    {
        $this->createTreatment(TreatmentKind::PainType, 'Bel ve Bacak Ağrıları', 'bel-ve-bacak-agrilari');

        $this->get('/agri-turleri/bel-ve-bacak-agrilari')
            ->assertOk()
            ->assertSee('Bel ve Bacak Ağrıları', escape: false)
            ->assertSee('/agri-turleri', escape: false);
    }

    public function test_a_procedure_page_renders(): void
    {
        $this->createTreatment(TreatmentKind::Procedure, 'Nükleoplasti', 'nukleoplasti');

        $this->get('/girisimsel-tedaviler/nukleoplasti')
            ->assertOk()
            ->assertSee('Nükleoplasti', escape: false);
    }

    public function test_the_index_lists_the_treatments_of_its_kind_only(): void
    {
        $this->createTreatment(TreatmentKind::PainType, 'Fibromiyalji', 'fibromiyalji');
        $this->createTreatment(TreatmentKind::Procedure, 'Vertebroplasti', 'vertebroplasti');

        $this->get('/agri-turleri')
            ->assertOk()
            ->assertSee('Fibromiyalji', escape: false)
            ->assertDontSee('Vertebroplasti', escape: false);
    }

    public function test_a_treatment_is_not_reachable_under_the_other_kind(): void
    {
        $this->createTreatment(TreatmentKind::PainType, 'Fibromiyalji', 'fibromiyalji');

        $this->get('/girisimsel-tedaviler/fibromiyalji')->assertNotFound();
    }

    private function createTreatment(TreatmentKind $kind, string $title, string $slug): Treatment
    {
        return Treatment::create([
            'kind' => $kind,
            'title' => ['tr' => $title],
            'slug' => ['tr' => $slug],
            'summary' => ['tr' => 'Kısa açıklama.'],
            'blocks' => [],
            'locales_enabled' => ['tr'],
            'status' => ContentStatus::Published,
            'published_at' => now()->subDay(),
        ]);
    }
}

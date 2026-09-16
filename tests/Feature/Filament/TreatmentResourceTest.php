<?php

namespace Tests\Feature\Filament;

use App\Enums\ContentStatus;
use App\Enums\TreatmentKind;
use App\Filament\Resources\Treatments\Pages\CreateTreatment;
use App\Filament\Resources\Treatments\Pages\EditTreatment;
use App\Filament\Resources\Treatments\Pages\ListTreatments;
use App\Models\Treatment;
use Livewire\Livewire;

class TreatmentResourceTest extends AdminPanelTestCase
{
    public function test_it_lists_treatments(): void
    {
        $treatment = $this->makeTreatment();

        Livewire::test(ListTreatments::class)
            ->assertOk()
            ->assertCanSeeTableRecords([$treatment]);
    }

    public function test_it_filters_by_kind(): void
    {
        $painType = $this->makeTreatment();
        $procedure = $this->makeTreatment(TreatmentKind::Procedure, 'Radyofrekans', 'radyofrekans');

        Livewire::test(ListTreatments::class)
            ->filterTable('kind', TreatmentKind::Procedure->value)
            ->assertCanSeeTableRecords([$procedure])
            ->assertCanNotSeeTableRecords([$painType]);
    }

    public function test_it_creates_a_treatment(): void
    {
        Livewire::test(CreateTreatment::class)
            ->fillForm([
                'kind' => TreatmentKind::Procedure->value,
                'title' => ['tr' => 'Epidural enjeksiyon'],
                'slug' => ['tr' => 'epidural-enjeksiyon'],
                'summary' => ['tr' => 'Kısa açıklama'],
                'sort_order' => 5,
                'is_featured' => true,
                'status' => ContentStatus::Published->value,
                'locales_enabled' => ['tr'],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $treatment = Treatment::query()->latest('id')->firstOrFail();

        $this->assertSame(TreatmentKind::Procedure, $treatment->kind);
        $this->assertSame('Epidural enjeksiyon', $treatment->localized('title', 'tr'));
        $this->assertTrue($treatment->is_featured);
        $this->assertSame(5, $treatment->sort_order);
    }

    public function test_it_edits_a_treatment(): void
    {
        $treatment = $this->makeTreatment();

        Livewire::test(EditTreatment::class, ['record' => $treatment->getKey()])
            ->assertOk()
            ->assertFormSet(['title.tr' => 'Bel ağrısı'])
            ->fillForm(['summary' => ['tr' => 'Güncellendi']])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Güncellendi', $treatment->refresh()->localized('summary', 'tr'));
    }

    private function makeTreatment(
        TreatmentKind $kind = TreatmentKind::PainType,
        string $title = 'Bel ağrısı',
        string $slug = 'bel-agrisi',
    ): Treatment {
        return Treatment::create([
            'kind' => $kind,
            'title' => ['tr' => $title],
            'slug' => ['tr' => $slug],
            'status' => ContentStatus::Published,
            'locales_enabled' => ['tr'],
        ]);
    }
}

<?php

namespace App\Http\Controllers\Front;

use App\Enums\TreatmentKind;
use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Treatment;
use App\Support\Localization\Locales;
use App\Support\Seo\Meta;
use Illuminate\View\View;

class TreatmentController extends Controller
{
    public function painTypeIndex(): View
    {
        return $this->index(TreatmentKind::PainType, 'pain_types', 'pain-types.index');
    }

    public function procedureIndex(): View
    {
        return $this->index(TreatmentKind::Procedure, 'procedures', 'procedures.index');
    }

    public function painType(string $slug): View
    {
        return $this->show(TreatmentKind::PainType, $slug);
    }

    public function procedure(string $slug): View
    {
        return $this->show(TreatmentKind::Procedure, $slug);
    }

    private function index(TreatmentKind $kind, string $segment, string $routeName): View
    {
        $locale = app()->getLocale();

        /*
         * An optional editorial page with the same slug as the section supplies
         * the intro blocks above the list.
         */
        $page = Page::query()
            ->live()
            ->whereSlug(Locales::segment($segment, $locale))
            ->first();

        $treatments = Treatment::query()
            ->live()
            ->where('kind', $kind)
            ->orderBy('sort_order')
            ->get();

        $meta = $page
            ? Meta::forModel($page)
            : Meta::forRoute($routeName, $kind->getLabel());

        return view('front.treatment-index', $meta + [
            'page' => $page,
            'kind' => $kind,
            'treatments' => $treatments,
        ]);
    }

    private function show(TreatmentKind $kind, string $slug): View
    {
        $treatment = Treatment::query()
            ->live()
            ->where('kind', $kind)
            ->whereSlug($slug)
            ->firstOrFail();

        $related = Treatment::query()
            ->live()
            ->where('kind', $kind)
            ->whereKeyNot($treatment->getKey())
            ->orderBy('sort_order')
            ->limit(3)
            ->get();

        return view('front.treatment', Meta::forModel($treatment) + [
            'treatment' => $treatment,
            'related' => $related,
        ]);
    }
}

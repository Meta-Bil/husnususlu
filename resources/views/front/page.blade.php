@php
    use App\Blocks\BlockRegistry;
    use App\Support\Seo\Schema;

    $graph = array_merge(
        [Schema::forPage($page)],
        BlockRegistry::jsonLd($page->blocks),
    );
@endphp

<x-layouts.app
    :title="$title"
    :description="$description"
    :canonical="$canonical"
    :alternates="$alternates"
    :noindex="$noindex"
    :json-ld="$graph"
>
    {!! BlockRegistry::render($page->blocks) !!}
</x-layouts.app>

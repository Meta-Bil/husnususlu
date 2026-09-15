@php
    use App\Blocks\BlockRegistry;

    $locale = app()->getLocale();
@endphp

<x-layouts.app
    :title="$title"
    :description="$description"
    :canonical="$canonical"
    :alternates="$alternates"
    :noindex="$noindex"
>
    @if ($page)
        {!! BlockRegistry::render($page->blocks) !!}
    @else
        <section class="bg-navy-900 text-cream-bright">
            <div class="mx-auto max-w-[1200px] px-6 py-16 lg:px-0 lg:py-20">
                <h1 class="font-display text-5xl font-medium lg:text-7xl">{{ __('front.all_videos') }}</h1>
            </div>
        </section>
    @endif

    @if ($featured)
        <section class="bg-navy-900">
            <div class="mx-auto flex max-w-[1200px] flex-col gap-6 px-6 pb-16 lg:px-0">
                <x-ui.video-card :video="$featured" size="large" />
            </div>
        </section>
    @endif

    @foreach ([['label' => __('front.on_screens'), 'items' => $tvVideos], ['label' => __('front.all_videos'), 'items' => $infoVideos]] as $group)
        @if ($group['items']->isNotEmpty())
            <section class="bg-navy-900 text-cream-bright">
                <div class="mx-auto flex max-w-[1200px] flex-col gap-8 px-6 pb-16 lg:px-0 lg:pb-20">
                    <h2 class="text-xs font-semibold tracking-[0.3em] text-gold-500 uppercase">{{ $group['label'] }}</h2>
                    <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                        @foreach ($group['items'] as $video)
                            <x-ui.video-card :video="$video" />
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    @endforeach
</x-layouts.app>

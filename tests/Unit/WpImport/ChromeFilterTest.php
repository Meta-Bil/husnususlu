<?php

namespace Tests\Unit\WpImport;

use App\Services\WpImport\Elementor\ChromeFilter;
use Tests\TestCase;

class ChromeFilterTest extends TestCase
{
    use WpFixtures;

    public function test_it_drops_the_widgets_that_were_never_content(): void
    {
        $filter = $this->chromeFilter();
        $page = $this->fixture('page-50');

        foreach (['nav-menu', 'search-form', 'theme-site-logo', 'share-buttons', 'animated-headline', 'shortcode'] as $type) {
            $this->assertTrue(
                $filter->isChrome($this->widget($page, $type)),
                "{$type} should have been treated as site chrome.",
            );
        }
    }

    public function test_it_drops_the_contact_rail_but_keeps_a_list_of_real_items(): void
    {
        $filter = $this->chromeFilter();

        /* The first icon list of every page is the phone, mail and address rail. */
        $this->assertTrue($filter->isChrome($this->widget($this->fixture('page-374'), 'icon-list')));
        $this->assertTrue($filter->isChrome($this->widget($this->fixture('page-374'), 'icon-list', 1)));

        /* The third icon list on the procedure page is a list of selling points. */
        $this->assertFalse($filter->isChrome($this->widget($this->fixture('page-1701'), 'icon-list', 2)));
    }

    public function test_it_drops_an_html_widget_that_only_carries_structured_data(): void
    {
        $filter = $this->chromeFilter();
        $post = $this->fixture('post-2290');

        $this->assertFalse($filter->isChrome($this->widget($post, 'html')), 'The first html widget is the article.');
        $this->assertTrue($filter->isChrome($this->widget($post, 'html', 1)), 'The rest are JSON-LD scripts.');
    }

    public function test_it_counts_how_many_pages_a_text_appears_on(): void
    {
        $filter = $this->chromeFilter();
        $disclaimer = $this->widget($this->fixture('page-374'), 'heading');

        $this->assertStringStartsWith('Bu makaledeki yazılar', $disclaimer->text());
        $this->assertSame(2, $filter->occurrences($disclaimer), 'Two of the six fixtures carry the disclaimer.');
    }

    public function test_a_text_repeated_across_enough_pages_is_treated_as_furniture(): void
    {
        $lenient = new ChromeFilter([], ['heading'], [], threshold: 2);
        $lenient->learn($this->allFixtures());

        $disclaimer = $this->widget($this->fixture('page-374'), 'heading');
        $ownTitle = $this->widget($this->fixture('page-1701'), 'heading');

        $this->assertSame('aynı metin 2 sayfada tekrar ediyor', $lenient->reasonFor($disclaimer));
        $this->assertNull($lenient->reasonFor($ownTitle), 'A heading used once belongs to its page.');
    }

    public function test_cards_are_never_dropped_by_the_repetition_rule(): void
    {
        $lenient = new ChromeFilter([], ['text-editor', 'heading', 'html', 'icon-list'], [], threshold: 1);
        $lenient->learn($this->allFixtures());

        $card = $this->widget($this->fixture('page-50'), 'icon-box', 3);

        $this->assertStringContainsString('Bel Ağrıları', $card->text());
        $this->assertNull(
            $lenient->reasonFor($card),
            'Card grids are matched against treatment records, so repetition is expected.',
        );
    }
}

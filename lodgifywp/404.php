<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package House_Booking_System
 */

get_header();
?>

<main id="primary" class="site-main">
    <section class="error-404 not-found">
        <header class="page-header">
            <h1 class="page-title"><?php esc_html_e('Oops! That page can&rsquo;t be found.', 'house-booking-system'); ?></h1>
        </header>

        <div class="page-content">
            <p><?php esc_html_e('It looks like nothing was found at this location. Maybe try a search?', 'house-booking-system'); ?></p>

            <?php get_search_form(); ?>

            <div class="widget-area">
                <div class="widget">
                    <h2 class="widget-title"><?php esc_html_e('Most Used Categories', 'house-booking-system'); ?></h2>
                    <ul>
                        <?php
                        wp_list_categories(array(
                            'orderby' => 'count',
                            'order' => 'DESC',
                            'show_count' => 1,
                            'title_li' => '',
                            'number' => 10,
                        ));
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
get_footer(); 
<?php
/**
 * The footer for our theme
 *
 * @package House_Booking_System
 */
?>
    <footer id="colophon" class="site-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-info">
                    <?php if ($site_logo = get_field('site_logo', 'option')) : ?>
                        <div class="footer-logo">
                            <img src="<?php echo esc_url($site_logo['url']); ?>" alt="<?php echo esc_attr($site_logo['alt']); ?>">
                        </div>
                    <?php endif; ?>

                    <?php if ($site_email = get_field('site_email', 'option')) : ?>
                        <div class="footer-contact">
                            <p>Email: <a href="mailto:<?php echo esc_attr($site_email); ?>"><?php echo esc_html($site_email); ?></a></p>
                        </div>
                    <?php endif; ?>

                    <?php if ($site_phone = get_field('site_phone', 'option')) : ?>
                        <div class="footer-contact">
                            <p>Phone: <a href="tel:<?php echo esc_attr($site_phone); ?>"><?php echo esc_html($site_phone); ?></a></p>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($social_media = get_field('social_media', 'option')) : ?>
                    <div class="footer-social">
                        <h3>Follow Us</h3>
                        <ul class="social-links">
                            <?php foreach ($social_media as $social) : ?>
                                <li>
                                    <a href="<?php echo esc_url($social['url']); ?>" target="_blank" rel="noopener noreferrer">
                                        <?php echo esc_html($social['platform']); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>

            <div class="site-info">
                <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html> 
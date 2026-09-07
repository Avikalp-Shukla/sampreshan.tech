<?php
/**
 * Template Name: Contact Us
 *
 * Contact page with form, info cards, and ShivBodh Trust details.
 *
 * @package SampreShan_Child
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();

// Process contact form submission
$form_sent   = false;
$form_error  = '';
$contact_name    = '';
$contact_email   = '';
$contact_subject = '';
$contact_message = '';

if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['sp_contact_nonce'] ) ) {
    if ( wp_verify_nonce( $_POST['sp_contact_nonce'], 'sp_contact_submit' ) ) {
        $contact_name    = sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) );
        $contact_email   = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
        $contact_subject = sanitize_text_field( wp_unslash( $_POST['subject'] ?? '' ) );
        $contact_message = sanitize_textarea_field( wp_unslash( $_POST['message'] ?? '' ) );

        if ( empty( $contact_name ) || empty( $contact_email ) || empty( $contact_message ) ) {
            $form_error = __( 'Please fill in all required fields.', 'sampreshan-child' );
        } elseif ( ! is_email( $contact_email ) ) {
            $form_error = __( 'Please enter a valid email address.', 'sampreshan-child' );
        } else {
            $to      = get_option( 'admin_email' );
            $headers = array(
                'Content-Type: text/html; charset=UTF-8',
                'Reply-To: ' . $contact_name . ' <' . $contact_email . '>',
            );
            $body = sprintf(
                "<strong>Name:</strong> %s<br><strong>Email:</strong> %s<br><strong>Subject:</strong> %s<br><strong>Message:</strong><br>%s",
                esc_html( $contact_name ),
                esc_html( $contact_email ),
                esc_html( $contact_subject ?: 'No subject' ),
                nl2br( esc_html( $contact_message ) )
            );

            $sent = wp_mail( $to, '[SampreShan] ' . ( $contact_subject ?: 'Contact Form' ), $body, $headers );

            if ( $sent ) {
                $form_sent = true;
            } else {
                $form_error = __( 'Something went wrong. Please try again later.', 'sampreshan-child' );
            }
        }
    }
}
?>

<main class="site-main sp-page" role="main">

    <!-- HERO -->
    <header class="sp-page__hero">
        <div class="sp-page__hero-icon">
            <?php sp_icon_e( 'mail', 'sp-icon--3xl sp-icon--saffron sp-icon--float', __( 'Contact', 'sampreshan-child' ) ); ?>
        </div>
        <h1 class="sp-page__hero-title"><?php esc_html_e( 'Contact Us', 'sampreshan-child' ); ?></h1>
        <p class="sp-page__hero-sub">
            <?php esc_html_e( 'Have a question, suggestion, or want to collaborate? We would love to hear from you.', 'sampreshan-child' ); ?>
        </p>
    </header>

    <div class="sp-page__body">

        <!-- CONTACT INFO CARDS -->
        <div class="sp-contact-info">
            <div class="sp-contact-info__item">
                <div class="sp-contact-info__item-icon">
                    <?php sp_icon_e( 'mail', 'sp-icon--md sp-icon--saffron', __( 'Email', 'sampreshan-child' ) ); ?>
                </div>
                <div>
                    <div class="sp-contact-info__item-label"><?php esc_html_e( 'Email', 'sampreshan-child' ); ?></div>
                    <div class="sp-contact-info__item-value">
                        <a href="mailto:info@sampreshan.tech">info@sampreshan.tech</a>
                    </div>
                </div>
            </div>
            <div class="sp-contact-info__item">
                <div class="sp-contact-info__item-icon">
                    <?php sp_icon_e( 'verified', 'sp-icon--md sp-icon--saffron', __( 'Trust', 'sampreshan-child' ) ); ?>
                </div>
                <div>
                    <div class="sp-contact-info__item-label"><?php esc_html_e( 'Organization', 'sampreshan-child' ); ?></div>
                    <div class="sp-contact-info__item-value">
                        <a href="https://shivbodhtrust.org" target="_blank" rel="noopener">ShivBodh Trust</a>
                    </div>
                </div>
            </div>
            <div class="sp-contact-info__item">
                <div class="sp-contact-info__item-icon">
                    <?php sp_icon_e( 'location', 'sp-icon--md sp-icon--saffron', __( 'Location', 'sampreshan-child' ) ); ?>
                </div>
                <div>
                    <div class="sp-contact-info__item-label"><?php esc_html_e( 'Website', 'sampreshan-child' ); ?></div>
                    <div class="sp-contact-info__item-value">
                        <a href="https://shivbodhtrust.org/contact/" target="_blank" rel="noopener">shivbodhtrust.org/contact</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- CONTACT FORM -->
        <section class="sp-page__card">
            <h2 class="sp-page__section-title">
                <?php sp_icon_e( 'pen', 'sp-icon--md sp-icon--saffron', __( 'Message', 'sampreshan-child' ) ); ?>
                <?php esc_html_e( 'Send us a Message', 'sampreshan-child' ); ?>
            </h2>

            <?php if ( $form_sent ) : ?>
                <div class="sp-page__content" style="text-align: center; padding: 2rem 0;">
                    <?php sp_icon_e( 'check', 'sp-icon--3xl sp-icon--success', __( 'Sent', 'sampreshan-child' ) ); ?>
                    <h3 style="margin: 1rem 0 0.5rem; color: var(--success, #2D7A4D);"><?php esc_html_e( 'Message Sent!', 'sampreshan-child' ); ?></h3>
                    <p><?php esc_html_e( 'Thank you for reaching out. We will get back to you within 48 hours.', 'sampreshan-child' ); ?></p>
                </div>
            <?php else : ?>

                <?php if ( $form_error ) : ?>
                    <div class="sp-login__error" role="alert" style="margin-bottom: 1rem;">
                        <?php sp_icon_e( 'close', 'sp-icon--md sp-icon--danger', __( 'Error', 'sampreshan-child' ) ); ?>
                        <span><?php echo esc_html( $form_error ); ?></span>
                    </div>
                <?php endif; ?>

                <form class="sp-contact-form" method="post" action="<?php echo esc_url( wp_unslash( $_SERVER['REQUEST_URI'] ?? get_permalink() ) ); ?>" novalidate>
                    <?php wp_nonce_field( 'sp_contact_submit', 'sp_contact_nonce' ); ?>

                    <div class="sp-contact-form__row">
                        <div class="sp-contact-form__field">
                            <label class="sp-contact-form__label" for="sp-contact-name">
                                <?php sp_icon_e( 'profile', 'sp-icon--sm sp-icon--muted', __( 'Name', 'sampreshan-child' ) ); ?>
                                <?php esc_html_e( 'Name', 'sampreshan-child' ); ?> *
                            </label>
                            <input type="text" id="sp-contact-name" name="name" class="sp-contact-form__input" required autocomplete="name" value="<?php echo esc_attr( $contact_name ); ?>" />
                        </div>
                        <div class="sp-contact-form__field">
                            <label class="sp-contact-form__label" for="sp-contact-email">
                                <?php sp_icon_e( 'mail', 'sp-icon--sm sp-icon--muted', __( 'Email', 'sampreshan-child' ) ); ?>
                                <?php esc_html_e( 'Email', 'sampreshan-child' ); ?> *
                            </label>
                            <input type="email" id="sp-contact-email" name="email" class="sp-contact-form__input" required autocomplete="email" value="<?php echo esc_attr( $contact_email ); ?>" />
                        </div>
                    </div>

                    <div class="sp-contact-form__field">
                        <label class="sp-contact-form__label" for="sp-contact-subject">
                            <?php sp_icon_e( 'document', 'sp-icon--sm sp-icon--muted', __( 'Subject', 'sampreshan-child' ) ); ?>
                            <?php esc_html_e( 'Subject', 'sampreshan-child' ); ?>
                        </label>
                        <select id="sp-contact-subject" name="subject" class="sp-contact-form__select">
                            <option value=""><?php esc_html_e( 'Select a topic...', 'sampreshan-child' ); ?></option>
                            <option value="general" <?php selected( $contact_subject, 'general' ); ?>><?php esc_html_e( 'General Inquiry', 'sampreshan-child' ); ?></option>
                            <option value="petition" <?php selected( $contact_subject, 'petition' ); ?>><?php esc_html_e( 'Petition Support', 'sampreshan-child' ); ?></option>
                            <option value="technical" <?php selected( $contact_subject, 'technical' ); ?>><?php esc_html_e( 'Technical Issue', 'sampreshan-child' ); ?></option>
                            <option value="collaboration" <?php selected( $contact_subject, 'collaboration' ); ?>><?php esc_html_e( 'Collaboration', 'sampreshan-child' ); ?></option>
                            <option value="feedback" <?php selected( $contact_subject, 'feedback' ); ?>><?php esc_html_e( 'Feedback', 'sampreshan-child' ); ?></option>
                            <option value="other" <?php selected( $contact_subject, 'other' ); ?>><?php esc_html_e( 'Other', 'sampreshan-child' ); ?></option>
                        </select>
                    </div>

                    <div class="sp-contact-form__field">
                        <label class="sp-contact-form__label" for="sp-contact-message">
                            <?php sp_icon_e( 'pen', 'sp-icon--sm sp-icon--muted', __( 'Message', 'sampreshan-child' ) ); ?>
                            <?php esc_html_e( 'Message', 'sampreshan-child' ); ?> *
                        </label>
                        <textarea id="sp-contact-message" name="message" class="sp-contact-form__textarea" required placeholder="<?php esc_attr_e( 'Tell us how we can help...', 'sampreshan-child' ); ?>"><?php echo esc_textarea( $contact_message ); ?></textarea>
                    </div>

                    <button type="submit" class="btn-3d btn-3d--lg">
                        <?php sp_icon_e( 'mail', 'sp-icon--sm sp-icon--white', __( 'Send', 'sampreshan-child' ) ); ?>
                        <?php esc_html_e( 'Send Message', 'sampreshan-child' ); ?>
                    </button>
                </form>

            <?php endif; ?>
        </section>

        <!-- CTA -->
        <section class="sp-page__cta">
            <h2 class="sp-page__cta-title"><?php esc_html_e( 'Want to Start a Petition?', 'sampreshan-child' ); ?></h2>
            <p class="sp-page__cta-sub"><?php esc_html_e( 'If your inquiry is about a specific cause, consider starting a petition to gather community support.', 'sampreshan-child' ); ?></p>
            <div class="sp-page__cta-actions">
                <a class="btn-3d btn-3d--lg" href="<?php echo esc_url( home_url( '/start-a-petition/' ) ); ?>">
                    <?php sp_icon_e( 'plus', 'sp-icon--sm sp-icon--white', __( 'Start', 'sampreshan-child' ) ); ?>
                    <?php esc_html_e( 'Start a Petition', 'sampreshan-child' ); ?>
                </a>
            </div>
        </section>

    </div>
</main>

<?php
$footer_tpl = get_stylesheet_directory() . '/template-parts/footer/site-footer.php';
if ( file_exists( $footer_tpl ) ) {
    include $footer_tpl;
} else {
    get_footer();
}

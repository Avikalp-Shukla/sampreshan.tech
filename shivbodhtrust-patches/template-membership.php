<?php
/**
 * Template Name: Membership
 * Description: Membership tiers and signup
 *
 * @package ShivBodh_Child
 */

$default_tiers = array(
    array(
        'slug'        => 'sevak',
        'name_hi'     => 'सेवक',
        'price_inr'   => 1100,
        'cadence'     => 'yearly',
        'perks'       => array(
            'मासिक श्लोक ईमेल',
            'याचिकाओं पर प्राथमिक सूचना',
            'वार्षिक कैलेंडर',
        ),
        'featured'    => false,
    ),
    array(
        'slug'        => 'yajman',
        'name_hi'     => 'यजमान',
        'price_inr'   => 5100,
        'cadence'     => 'yearly',
        'perks'       => array(
            'सेवक स्तर की सभी सुविधाएँ',
            'विशेष कार्यक्रमों में निमंत्रण',
            'वार्षिक ग्रंथ/पुस्तक',
            'पीठ दर्शन प्राथमिकता',
        ),
        'featured'    => true,
    ),
    array(
        'slug'        => 'trustee',
        'name_hi'     => 'न्यासी',
        'price_inr'   => 21000,
        'cadence'     => 'yearly',
        'perks'       => array(
            'यजमान स्तर की सभी सुविधाएँ',
            'धर्मसभा में स्थान',
            'वार्षिक संगोष्ठी में आमंत्रण',
            'पीठों के प्रतिनिधि मंडल में नाम',
        ),
        'featured'    => false,
    ),
);

$tiers = apply_filters( 'shivbodh_membership_tiers', $default_tiers );

get_header();
?>

<section class="sb-page-header">
    <div class="sb-container">
        <div class="sb-page-header-content">
            <div class="sb-breadcrumb">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'shivbodh-child' ); ?></a>
                <span>/</span>
                <span><?php esc_html_e( 'Membership', 'shivbodh-child' ); ?></span>
            </div>
            <h1><?php esc_html_e( 'सदस्यता', 'shivbodh-child' ); ?></h1>
            <p><?php esc_html_e( 'Sanatan Dharma की निरंतर सेवा के लिए सदस्य बनें।', 'shivbodh-child' ); ?></p>
        </div>
    </div>
</section>

<section class="sb-section">
    <div class="sb-container">

        <div class="sb-membership-grid">
            <?php foreach ( $tiers as $tier ) : ?>
            <article class="sb-tier-card sb-fade-in <?php echo $tier['featured'] ? 'sb-tier-featured' : ''; ?>">
                <?php if ( $tier['featured'] ) : ?>
                    <span class="sb-tier-badge"><?php esc_html_e( 'अनुशंसित', 'shivbodh-child' ); ?></span>
                <?php endif; ?>
                <h3 class="sb-tier-name"><?php echo esc_html( $tier['name_hi'] ); ?></h3>
                <div class="sb-tier-price">
                    <span class="sb-tier-currency">₹</span>
                    <span class="sb-tier-amount"><?php echo esc_html( number_format_i18n( $tier['price_inr'] ) ); ?></span>
                    <span class="sb-tier-cadence">/ <?php echo esc_html( $tier['cadence'] === 'yearly' ? 'वर्ष' : 'माह' ); ?></span>
                </div>
                <ul class="sb-tier-perks">
                    <?php foreach ( $tier['perks'] as $perk ) : ?>
                        <li><?php echo esc_html( $perk ); ?></li>
                    <?php endforeach; ?>
                </ul>
                <a href="<?php echo esc_url( add_query_arg( array( 'tier' => $tier['slug'] ), home_url( '/donate/' ) ) ); ?>" class="sb-btn <?php echo $tier['featured'] ? 'sb-btn-primary' : 'sb-btn-outline'; ?> sb-btn-block">
                    <?php esc_html_e( 'सदस्य बनें', 'shivbodh-child' ); ?>
                </a>
            </article>
            <?php endforeach; ?>
        </div>

        <div class="sb-membership-faq">
            <h2><?php esc_html_e( 'अक्सर पूछे जाने वाले प्रश्न', 'shivbodh-child' ); ?></h2>
            <div class="sb-faq-list">
                <details class="sb-faq-item">
                    <summary class="sb-faq-question"><?php esc_html_e( 'क्या सदस्यता राशि 80G के अंतर्गत है?', 'shivbodh-child' ); ?></summary>
                    <div class="sb-faq-answer"><div class="sb-faq-answer-content">
                        <p><?php esc_html_e( 'हाँ, सदस्यता राशि पर 80G प्रमाणपत्र जारी किया जाता है।', 'shivbodh-child' ); ?></p>
                    </div></div>
                </details>
                <details class="sb-faq-item">
                    <summary class="sb-faq-question"><?php esc_html_e( 'क्या मैं अपना स्तर बदल सकता/सकती हूँ?', 'shivbodh-child' ); ?></summary>
                    <div class="sb-faq-answer"><div class="sb-faq-answer-content">
                        <p><?php esc_html_e( 'हाँ, आप किसी भी समय अपने सदस्यता स्तर को उन्नत या अवनत कर सकते हैं।', 'shivbodh-child' ); ?></p>
                    </div></div>
                </details>
                <details class="sb-faq-item">
                    <summary class="sb-faq-question"><?php esc_html_e( 'क्या मेरा योगदान नियमित है?', 'shivbodh-child' ); ?></summary>
                    <div class="sb-faq-answer"><div class="sb-faq-answer-content">
                        <p><?php esc_html_e( 'सदस्यता स्वतः नवीनीकरण पर है, लेकिन आप किसी भी समय रद्द कर सकते हैं।', 'shivbodh-child' ); ?></p>
                    </div></div>
                </details>
            </div>
        </div>

    </div>
</section>

<?php get_footer(); ?>

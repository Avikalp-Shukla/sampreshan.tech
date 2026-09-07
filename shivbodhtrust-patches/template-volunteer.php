<?php
/**
 * Template Name: Volunteer
 * Description: Volunteer signup landing
 *
 * @package ShivBodh_Child
 */

get_header();
?>

<section class="sb-page-header">
    <div class="sb-container">
        <div class="sb-page-header-content">
            <div class="sb-breadcrumb">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'shivbodh-child' ); ?></a>
                <span>/</span>
                <span><?php esc_html_e( 'Volunteer', 'shivbodh-child' ); ?></span>
            </div>
            <h1><?php esc_html_e( 'धर्मसेवक बनें', 'shivbodh-child' ); ?></h1>
            <p><?php esc_html_e( 'अपने समय, कौशल और सेवा को सनातन धर्म की रक्षा में समर्पित करें।', 'shivbodh-child' ); ?></p>
        </div>
    </div>
</section>

<section class="sb-section">
    <div class="sb-container sb-container-narrow">

        <div class="sb-volunteer-intro">
            <h2><?php esc_html_e( 'कैसे स्वयंसेवक बनें?', 'shivbodh-child' ); ?></h2>
            <p><?php esc_html_e( 'नीचे दिया गया फॉर्म भरें। हमारी टीम 7 कार्य दिवसों के भीतर आपसे संपर्क करेगी।', 'shivbodh-child' ); ?></p>
        </div>

        <form id="sb-volunteer-form" class="sb-form" autocomplete="on">
            <?php wp_nonce_field( 'shivbodh_nonce', 'nonce' ); ?>

            <div class="sb-form-row sb-form-grid-2">
                <div>
                    <label for="sb-v-name"><?php esc_html_e( 'पूरा नाम', 'shivbodh-child' ); ?> *</label>
                    <input id="sb-v-name" type="text" name="name" required autocomplete="name">
                </div>
                <div>
                    <label for="sb-v-email"><?php esc_html_e( 'ईमेल', 'shivbodh-child' ); ?> *</label>
                    <input id="sb-v-email" type="email" name="email" required autocomplete="email">
                </div>
            </div>

            <div class="sb-form-row sb-form-grid-2">
                <div>
                    <label for="sb-v-phone"><?php esc_html_e( 'फ़ोन', 'shivbodh-child' ); ?> *</label>
                    <input id="sb-v-phone" type="tel" name="phone" required autocomplete="tel">
                </div>
                <div>
                    <label for="sb-v-city"><?php esc_html_e( 'शहर', 'shivbodh-child' ); ?> *</label>
                    <input id="sb-v-city" type="text" name="city" required autocomplete="address-level2">
                </div>
            </div>

            <div class="sb-form-row">
                <label><?php esc_html_e( 'सेवा क्षेत्र चुनें', 'shivbodh-child' ); ?></label>
                <div class="sb-checkbox-grid">
                    <label class="sb-checkbox"><input type="checkbox" name="areas[]" value="petition"><span><?php esc_html_e( 'याचिका संचालन', 'shivbodh-child' ); ?></span></label>
                    <label class="sb-checkbox"><input type="checkbox" name="areas[]" value="save-cow"><span><?php esc_html_e( 'गौ रक्षा', 'shivbodh-child' ); ?></span></label>
                    <label class="sb-checkbox"><input type="checkbox" name="areas[]" value="events"><span><?php esc_html_e( 'कार्यक्रम प्रबंधन', 'shivbodh-child' ); ?></span></label>
                    <label class="sb-checkbox"><input type="checkbox" name="areas[]" value="content"><span><?php esc_html_e( 'सामग्री लेखन / अनुवाद', 'shivbodh-child' ); ?></span></label>
                    <label class="sb-checkbox"><input type="checkbox" name="areas[]" value="tech"><span><?php esc_html_e( 'तकनीकी सेवा', 'shivbodh-child' ); ?></span></label>
                    <label class="sb-checkbox"><input type="checkbox" name="areas[]" value="legal"><span><?php esc_html_e( 'विधिक सहायता', 'shivbodh-child' ); ?></span></label>
                    <label class="sb-checkbox"><input type="checkbox" name="areas[]" value="media"><span><?php esc_html_e( 'मीडिया / सोशल', 'shivbodh-child' ); ?></span></label>
                    <label class="sb-checkbox"><input type="checkbox" name="areas[]" value="translation"><span><?php esc_html_e( 'शास्त्र अनुवाद', 'shivbodh-child' ); ?></span></label>
                </div>
            </div>

            <div class="sb-form-row">
                <label for="sb-v-skills"><?php esc_html_e( 'कौशल और अनुभव', 'shivbodh-child' ); ?></label>
                <textarea id="sb-v-skills" name="skills" rows="4" placeholder="<?php esc_attr_e( 'अपने कौशल और अनुभव का संक्षिप्त विवरण लिखें।', 'shivbodh-child' ); ?>"></textarea>
            </div>

            <div class="sb-form-row">
                <label for="sb-v-availability"><?php esc_html_e( 'उपलब्धता', 'shivbodh-child' ); ?></label>
                <select id="sb-v-availability" name="availability">
                    <option value="few-hours"><?php esc_html_e( 'सप्ताह में कुछ घंटे', 'shivbodh-child' ); ?></option>
                    <option value="weekly"><?php esc_html_e( 'सप्ताह में एक दिन', 'shivbodh-child' ); ?></option>
                    <option value="daily"><?php esc_html_e( 'प्रतिदिन कुछ समय', 'shivbodh-child' ); ?></option>
                    <option value="fulltime"><?php esc_html_e( 'पूर्णकालिक', 'shivbodh-child' ); ?></option>
                </select>
            </div>

            <button type="submit" class="sb-btn sb-btn-primary sb-btn-lg sb-btn-block">
                <?php esc_html_e( 'स्वयंसेवक के रूप में आवेदन करें', 'shivbodh-child' ); ?>
            </button>
        </form>

    </div>
</section>

<?php get_footer(); ?>

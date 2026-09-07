<?php
/**
 * Template Name: Donate
 * Description: Donation flow landing
 *
 * @package ShivBodh_Child
 */

get_header();
?>

<section class="sb-page-header sb-page-header-cta">
    <div class="sb-container">
        <div class="sb-page-header-content">
            <div class="sb-breadcrumb">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'shivbodh-child' ); ?></a>
                <span>/</span>
                <span><?php esc_html_e( 'Donate', 'shivbodh-child' ); ?></span>
            </div>
            <h1><?php esc_html_e( 'सनातन धर्म की सेवा में अपना योगदान दें', 'shivbodh-child' ); ?></h1>
            <p><?php esc_html_e( 'आपका दान चारों अम्नाय पीठों, गौ रक्षा, और धार्मिक याचिकाओं के संचालन में सहायक है।', 'shivbodh-child' ); ?></p>
        </div>
    </div>
</section>

<section class="sb-section">
    <div class="sb-container">

        <div class="sb-donate-grid">

            <aside class="sb-donate-info">
                <h2><?php esc_html_e( 'कहाँ जाता है आपका दान?', 'shivbodh-child' ); ?></h2>
                <ul class="sb-donate-list">
                    <li>
                        <strong><?php esc_html_e( 'पीठ सेवा', 'shivbodh-child' ); ?></strong>
                        <span><?php esc_html_e( 'श्रृंगेरी, द्वारका, पुरी, ज्योतिर्मठ', 'shivbodh-child' ); ?></span>
                    </li>
                    <li>
                        <strong><?php esc_html_e( 'गौ रक्षा अभियान', 'shivbodh-child' ); ?></span>
                        <span><?php esc_html_e( 'गौशाला सहायता, कानूनी सहायता, जागरूकता', 'shivbodh-child' ); ?></span>
                    </li>
                    <li>
                        <strong><?php esc_html_e( 'धार्मिक याचिकाएँ', 'shivbodh-child' ); ?></strong>
                        <span><?php esc_html_e( 'विधिक और सांस्कृतिक अभियान', 'shivbodh-child' ); ?></span>
                    </li>
                    <li>
                        <strong><?php esc_html_e( 'शिक्षा और प्रचार', 'shivbodh-child' ); ?></strong>
                        <span><?php esc_html_e( 'श्लोक, ग्रंथ, वेबिनार', 'shivbodh-child' ); ?></span>
                    </li>
                </ul>

                <div class="sb-donate-trust">
                    <h3><?php esc_html_e( 'पारदर्शिता और विश्वास', 'shivbodh-child' ); ?></h3>
                    <p><?php esc_html_e( 'हम 80G प्रमाणपत्र जारी करते हैं। सभी दान ऑडिट योग्य हैं।', 'shivbodh-child' ); ?></p>
                </div>
            </aside>

            <div class="sb-donate-form-wrap">
                <form id="sb-donate-form" class="sb-form sb-donate-form" autocomplete="on">
                    <div class="sb-form-row">
                        <label for="sb-cause"><?php esc_html_e( 'कारण चुनें', 'shivbodh-child' ); ?></label>
                        <select id="sb-cause" name="cause" required>
                            <option value="general"><?php esc_html_e( 'सामान्य धर्म सेवा', 'shivbodh-child' ); ?></option>
                            <option value="peetham"><?php esc_html_e( 'पीठ सेवा', 'shivbodh-child' ); ?></option>
                            <option value="save-cow"><?php esc_html_e( 'गौ रक्षा', 'shivbodh-child' ); ?></option>
                            <option value="events"><?php esc_html_e( 'कार्यक्रम', 'shivbodh-child' ); ?></option>
                        </select>
                    </div>

                    <div class="sb-form-row">
                        <label><?php esc_html_e( 'राशि', 'shivbodh-child' ); ?></label>
                        <div class="sb-amount-chips" role="radiogroup" aria-label="<?php esc_attr_e( 'Amount', 'shivbodh-child' ); ?>">
                            <button type="button" class="sb-amount-chip" data-amount="101">₹101</button>
                            <button type="button" class="sb-amount-chip" data-amount="501">₹501</button>
                            <button type="button" class="sb-amount-chip" data-amount="1100">₹1,100</button>
                            <button type="button" class="sb-amount-chip" data-amount="5100">₹5,100</button>
                            <button type="button" class="sb-amount-chip" data-amount="11000">₹11,000</button>
                            <label class="sb-amount-custom">
                                <span class="screen-reader-text"><?php esc_html_e( 'Custom amount', 'shivbodh-child' ); ?></span>
                                <input type="number" name="amount_custom" min="1" placeholder="<?php esc_attr_e( 'अन्य राशि', 'shivbodh-child' ); ?>">
                            </label>
                        </div>
                        <input type="hidden" name="amount" id="sb-amount-final" required>
                    </div>

                    <div class="sb-form-row">
                        <label for="sb-frequency"><?php esc_html_e( 'आवृत्ति', 'shivbodh-child' ); ?></label>
                        <select id="sb-frequency" name="frequency">
                            <option value="once"><?php esc_html_e( 'एक बार', 'shivbodh-child' ); ?></option>
                            <option value="monthly"><?php esc_html_e( 'मासिक', 'shivbodh-child' ); ?></option>
                            <option value="yearly"><?php esc_html_e( 'वार्षिक', 'shivbodh-child' ); ?></option>
                        </select>
                    </div>

                    <div class="sb-form-row">
                        <label for="sb-name"><?php esc_html_e( 'पूरा नाम', 'shivbodh-child' ); ?></label>
                        <input id="sb-name" type="text" name="name" required autocomplete="name">
                    </div>

                    <div class="sb-form-row">
                        <label for="sb-email"><?php esc_html_e( 'ईमेल', 'shivbodh-child' ); ?></label>
                        <input id="sb-email" type="email" name="email" required autocomplete="email">
                    </div>

                    <div class="sb-form-row">
                        <label for="sb-phone"><?php esc_html_e( 'फ़ोन', 'shivbodh-child' ); ?></label>
                        <input id="sb-phone" type="tel" name="phone" autocomplete="tel">
                    </div>

                    <div class="sb-form-row sb-form-row-inline">
                        <label class="sb-checkbox">
                            <input type="checkbox" name="pan_provided" value="1">
                            <span><?php esc_html_e( 'मैं 80G प्रमाणपत्र चाहता/चाहती हूँ (PAN आवश्यक)', 'shivbodh-child' ); ?></span>
                        </label>
                    </div>

                    <div class="sb-form-row sb-form-row-hidden" data-show-if="pan_provided">
                        <label for="sb-pan"><?php esc_html_e( 'PAN', 'shivbodh-child' ); ?></label>
                        <input id="sb-pan" type="text" name="pan" pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}" maxlength="10" autocomplete="off">
                    </div>

                    <div class="sb-form-row sb-form-row-inline">
                        <label class="sb-checkbox">
                            <input type="checkbox" name="is_anonymous" value="1">
                            <span><?php esc_html_e( 'नाम गोपनीय रखें', 'shivbodh-child' ); ?></span>
                        </label>
                    </div>

                    <?php wp_nonce_field( 'shivbodh_nonce', 'nonce' ); ?>

                    <button type="submit" class="sb-btn sb-btn-primary sb-btn-lg sb-btn-block">
                        <?php esc_html_e( 'अभी दान करें', 'shivbodh-child' ); ?>
                    </button>

                    <p class="sb-form-trust">
                        <?php esc_html_e( 'सुरक्षित भुगतान। आपका डेटा कभी तीसरे पक्ष के साथ साझा नहीं किया जाता।', 'shivbodh-child' ); ?>
                    </p>
                </form>
            </div>

        </div>

    </div>
</section>

<?php get_footer(); ?>

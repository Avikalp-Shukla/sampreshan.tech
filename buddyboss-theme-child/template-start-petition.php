<?php
/**
 * Template: Start a Petition — Premium Edition
 * Multi-step petition creation form with live preview
 *
 * @package SampreShan_Child
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! is_user_logged_in() ) {
    wp_safe_redirect( wp_login_url( home_url( '/start-a-petition/' ) ) );
    exit;
}

get_header();

$cause_terms    = get_terms( array(
    'taxonomy'   => 'cause_category',
    'hide_empty' => false,
    'parent'     => 0,
) );
$cause_terms    = is_wp_error( $cause_terms ) ? array() : $cause_terms;
$current_user   = wp_get_current_user();
$nonce          = wp_create_nonce( 'sp_create_petition' );
$ajax_url       = admin_url( 'admin-ajax.php' );
$upload_url     = wp_upload_dir()['baseurl'];
?>

<main class="sp-page sp-petition-create" role="main">
    <div class="sp-petition-create__inner">

        <!-- HERO -->
        <section class="sp-petition-create__hero">
            <div class="sp-petition-create__hero-content">
                <span class="sp-petition-create__hero-icon" aria-hidden="true">
                    <?php sp_icon_e( 'megaphone', 'sp-icon--lg', '' ); ?>
                </span>
                <h1 class="sp-petition-create__hero-title">
                    <?php echo esc_html__( 'Start a Petition', 'sampreshan-child' ); ?>
                </h1>
                <p class="sp-petition-create__hero-sub">
                    <?php echo esc_html__( 'Raise your voice for Dharma. Gather community support for causes that matter.', 'sampreshan-child' ); ?>
                </p>
            </div>
        </section>

        <!-- MULTI-STEP FORM -->
        <div class="sp-petition-create__form-wrapper glass">
            <!-- Step Indicators -->
            <div class="sp-petition-create__steps" aria-label="Form steps">
                <button class="sp-step is-active" data-step="1" type="button">
                    <span class="sp-step__num">1</span>
                    <span class="sp-step__label"><?php echo esc_html__( 'Details', 'sampreshan-child' ); ?></span>
                </button>
                <span class="sp-step__connector"></span>
                <button class="sp-step" data-step="2" type="button">
                    <span class="sp-step__num">2</span>
                    <span class="sp-step__label"><?php echo esc_html__( 'Story', 'sampreshan-child' ); ?></span>
                </button>
                <span class="sp-step__connector"></span>
                <button class="sp-step" data-step="3" type="button">
                    <span class="sp-step__num">3</span>
                    <span class="sp-step__label"><?php echo esc_html__( 'Settings', 'sampreshan-child' ); ?></span>
                </button>
                <span class="sp-step__connector"></span>
                <button class="sp-step" data-step="4" type="button">
                    <span class="sp-step__num">4</span>
                    <span class="sp-step__label"><?php echo esc_html__( 'Publish', 'sampreshan-child' ); ?></span>
                </button>
            </div>

            <form id="sp-petition-form" class="sp-petition-create__form" novalidate>
                <input type="hidden" name="action" value="sp_create_petition">
                <input type="hidden" name="nonce" value="<?php echo esc_attr( $nonce ); ?>">
                <input type="hidden" name="current_step" id="sp-current-step" value="1">

                <!-- STEP 1: BASIC DETAILS -->
                <div class="sp-form-step is-active" data-step="1">
                    <h2 class="sp-form-step__title">
                        <?php echo esc_html__( 'What is your petition about?', 'sampreshan-child' ); ?>
                    </h2>
                    <p class="sp-form-step__desc">
                        <?php echo esc_html__( 'Choose a clear, compelling title that explains what you want to change.', 'sampreshan-child' ); ?>
                    </p>

                    <div class="sp-form-group">
                        <label class="sp-form-label" for="petition-title">
                            <?php echo esc_html__( 'Petition Title', 'sampreshan-child' ); ?>
                            <span class="sp-form-label__required">*</span>
                        </label>
                        <input
                            class="sp-form-input"
                            type="text"
                            id="petition-title"
                            name="petition_title"
                            required
                            maxlength="120"
                            placeholder="<?php esc_attr_e( 'e.g. Protect the ancient Kedarnath Temple from encroachment', 'sampreshan-child' ); ?>"
                            aria-describedby="petition-title-count"
                        >
                        <div class="sp-form-hint">
                            <span class="sp-form-hint__count" id="petition-title-count">0/120</span>
                            <span class="sp-form-hint__tip"><?php echo esc_html__( 'A strong title is clear and specific', 'sampreshan-child' ); ?></span>
                        </div>
                    </div>

                    <div class="sp-form-group">
                        <label class="sp-form-label" for="petition-cause">
                            <?php echo esc_html__( 'Cause Category', 'sampreshan-child' ); ?>
                            <span class="sp-form-label__required">*</span>
                        </label>
                        <div class="sp-form-select-wrapper">
                            <select class="sp-form-select" id="petition-cause" name="cause_category" required>
                                <option value=""><?php echo esc_html__( 'Select a cause...', 'sampreshan-child' ); ?></option>
                                <?php foreach ( $cause_terms as $term ) : ?>
                                    <option value="<?php echo esc_attr( $term->term_id ); ?>">
                                        <?php echo esc_html( $term->name ); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="sp-form-select__arrow" aria-hidden="true">&#9662;</span>
                        </div>
                    </div>

                    <div class="sp-form-group">
                        <label class="sp-form-label" for="petition-location">
                            <?php echo esc_html__( 'Location (optional)', 'sampreshan-child' ); ?>
                        </label>
                        <input
                            class="sp-form-input"
                            type="text"
                            id="petition-location"
                            name="petition_location"
                            maxlength="100"
                            placeholder="<?php esc_attr_e( 'e.g. Varanasi, Uttar Pradesh', 'sampreshan-child' ); ?>"
                        >
                    </div>

                    <div class="sp-form-nav">
                        <div></div>
                        <button class="btn btn--primary btn--lg sp-next-step" type="button" data-next="2">
                            <?php echo esc_html__( 'Continue', 'sampreshan-child' ); ?>
                            <span aria-hidden="true">&rarr;</span>
                        </button>
                    </div>
                </div>

                <!-- STEP 2: YOUR STORY -->
                <div class="sp-form-step" data-step="2">
                    <h2 class="sp-form-step__title">
                        <?php echo esc_html__( 'Tell your story', 'sampreshan-child' ); ?>
                    </h2>
                    <p class="sp-form-step__desc">
                        <?php echo esc_html__( 'Explain why this matters. People sign when they understand the impact.', 'sampreshan-child' ); ?>
                    </p>

                    <div class="sp-form-group">
                        <label class="sp-form-label" for="petition-summary">
                            <?php echo esc_html__( 'Short Summary', 'sampreshan-child' ); ?>
                            <span class="sp-form-label__required">*</span>
                        </label>
                        <textarea
                            class="sp-form-textarea"
                            id="petition-summary"
                            name="petition_summary"
                            required
                            rows="3"
                            maxlength="300"
                            placeholder="<?php esc_attr_e( 'A brief 2-3 sentence summary of your petition...', 'sampreshan-child' ); ?>"
                        ></textarea>
                        <span class="sp-form-hint__count" id="petition-summary-count">0/300</span>
                    </div>

                    <div class="sp-form-group">
                        <label class="sp-form-label" for="petition-description">
                            <?php echo esc_html__( 'Full Description', 'sampreshan-child' ); ?>
                            <span class="sp-form-label__required">*</span>
                        </label>
                        <textarea
                            class="sp-form-textarea sp-form-textarea--lg"
                            id="petition-description"
                            name="petition_description"
                            required
                            rows="10"
                            placeholder="<?php esc_attr_e( 'Describe the issue, why it matters, and what change you want to see...', 'sampreshan-child' ); ?>"
                        ></textarea>
                    </div>

                    <div class="sp-form-group">
                        <label class="sp-form-label" for="petition-target">
                            <?php echo esc_html__( 'Who is this directed at?', 'sampreshan-child' ); ?>
                        </label>
                        <input
                            class="sp-form-input"
                            type="text"
                            id="petition-target"
                            name="petition_target"
                            maxlength="120"
                            placeholder="<?php esc_attr_e( 'e.g. The Archaeological Survey of India, Local Government', 'sampreshan-child' ); ?>"
                        >
                    </div>

                    <div class="sp-form-nav">
                        <button class="btn btn--outline sp-prev-step" type="button" data-prev="1">
                            <span aria-hidden="true">&larr;</span>
                            <?php echo esc_html__( 'Back', 'sampreshan-child' ); ?>
                        </button>
                        <button class="btn btn--primary btn--lg sp-next-step" type="button" data-next="3">
                            <?php echo esc_html__( 'Continue', 'sampreshan-child' ); ?>
                            <span aria-hidden="true">&rarr;</span>
                        </button>
                    </div>
                </div>

                <!-- STEP 3: SETTINGS -->
                <div class="sp-form-step" data-step="3">
                    <h2 class="sp-form-step__title">
                        <?php echo esc_html__( 'Set your goal', 'sampreshan-child' ); ?>
                    </h2>
                    <p class="sp-form-step__desc">
                        <?php echo esc_html__( 'Set a signature goal and optional deadline to create urgency.', 'sampreshan-child' ); ?>
                    </p>

                    <div class="sp-form-row">
                        <div class="sp-form-group sp-form-group--half">
                            <label class="sp-form-label" for="petition-goal">
                                <?php sp_icon_auto( 'target', 'sp-icon--sm', '' ); ?>
                                <?php echo esc_html__( 'Signature Goal', 'sampreshan-child' ); ?>
                            </label>
                            <div class="sp-form-input-group">
                                <input
                                    class="sp-form-input"
                                    type="number"
                                    id="petition-goal"
                                    name="petition_goal"
                                    min="10"
                                    max="1000000"
                                    value="1000"
                                    step="100"
                                >
                                <span class="sp-form-input-group__suffix"><?php echo esc_html__( 'signatures', 'sampreshan-child' ); ?></span>
                            </div>
                        </div>
                        <div class="sp-form-group sp-form-group--half">
                            <label class="sp-form-label" for="petition-deadline">
                                <?php echo esc_html__( 'Deadline (optional)', 'sampreshan-child' ); ?>
                            </label>
                            <input
                                class="sp-form-input"
                                type="date"
                                id="petition-deadline"
                                name="petition_deadline"
                                min="<?php echo esc_attr( date( 'Y-m-d', strtotime( '+7 days' ) ) ); ?>"
                            >
                        </div>
                    </div>

                    <div class="sp-form-group">
                        <label class="sp-form-label" for="petition-cover">
                            <?php echo esc_html__( 'Cover Image (optional)', 'sampreshan-child' ); ?>
                        </label>
                        <div class="sp-form-upload" id="sp-cover-upload">
                            <input type="file" id="petition-cover-input" name="petition_cover" accept="image/*" class="sp-form-upload__input">
                            <div class="sp-form-upload__preview" id="sp-cover-preview" style="display:none;">
                                <img id="sp-cover-img" src="" alt="Cover preview">
                                <button type="button" class="sp-form-upload__remove" id="sp-cover-remove">&times;</button>
                            </div>
                            <div class="sp-form-upload__placeholder" id="sp-cover-placeholder">
                                <span class="sp-form-upload__icon" aria-hidden="true">
                                    <?php sp_icon_e( 'heart', 'sp-icon--lg', '' ); ?>
                                </span>
                                <p class="sp-form-upload__text"><?php echo esc_html__( 'Click to upload or drag & drop', 'sampreshan-child' ); ?></p>
                                <p class="sp-form-upload__hint"><?php echo esc_html__( 'PNG, JPG up to 5MB', 'sampreshan-child' ); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="sp-form-group">
                        <label class="sp-form-label sp-form-label--toggle">
                            <span>
                                <?php echo esc_html__( 'Sign as anonymous', 'sampreshan-child' ); ?>
                                <span class="sp-form-hint-inline"><?php echo esc_html__( '(your name won\'t be shown publicly)', 'sampreshan-child' ); ?></span>
                            </span>
                            <input type="checkbox" class="sp-form-toggle" name="petition_anonymous" value="1">
                            <span class="sp-form-toggle__track" aria-hidden="true"></span>
                        </label>
                    </div>

                    <div class="sp-form-nav">
                        <button class="btn btn--outline sp-prev-step" type="button" data-prev="2">
                            <span aria-hidden="true">&larr;</span>
                            <?php echo esc_html__( 'Back', 'sampreshan-child' ); ?>
                        </button>
                        <button class="btn btn--primary btn--lg sp-next-step" type="button" data-next="4">
                            <?php echo esc_html__( 'Preview & Publish', 'sampreshan-child' ); ?>
                            <span aria-hidden="true">&rarr;</span>
                        </button>
                    </div>
                </div>

                <!-- STEP 4: PREVIEW & PUBLISH -->
                <div class="sp-form-step" data-step="4">
                    <h2 class="sp-form-step__title">
                        <?php echo esc_html__( 'Preview your petition', 'sampreshan-child' ); ?>
                    </h2>
                    <p class="sp-form-step__desc">
                        <?php echo esc_html__( 'Review everything before publishing. You can edit later.', 'sampreshan-child' ); ?>
                    </p>

                    <div class="sp-petition-preview" id="sp-petition-preview">
                        <div class="sp-petition-preview__cover" id="sp-preview-cover"></div>
                        <div class="sp-petition-preview__body">
                            <span class="sp-petition-preview__cause" id="sp-preview-cause"></span>
                            <h3 class="sp-petition-preview__title" id="sp-preview-title"></h3>
                            <p class="sp-petition-preview__summary" id="sp-preview-summary"></p>
                            <div class="sp-petition-preview__meta">
                                <span class="sp-petition-preview__author">
                                    <?php
                                    $avatar_url = get_avatar_url( $current_user->ID, array( 'size' => 32 ) );
                                    ?>
                                    <img class="sp-petition-preview__avatar" src="<?php echo esc_url( $avatar_url ); ?>" alt="" width="28" height="28">
                                    <?php echo esc_html( $current_user->display_name ); ?>
                                </span>
                                <span class="sp-petition-preview__goal" id="sp-preview-goal"></span>
                            </div>
                        </div>
                    </div>

                    <div class="sp-form-notice sp-form-notice--info">
                        <span class="sp-form-notice__icon" aria-hidden="true">&#9432;</span>
                        <?php echo esc_html__( 'Your petition will be reviewed and published immediately. You can edit it anytime from your dashboard.', 'sampreshan-child' ); ?>
                    </div>

                    <div class="sp-form-nav">
                        <button class="btn btn--outline sp-prev-step" type="button" data-prev="3">
                            <span aria-hidden="true">&larr;</span>
                            <?php echo esc_html__( 'Back', 'sampreshan-child' ); ?>
                        </button>
                        <button class="btn btn--primary btn--xl sp-submit-petition" type="submit" id="sp-submit-petition">
                            <span class="sp-submit-petition__text">
                                <?php echo esc_html__( 'Publish Petition', 'sampreshan-child' ); ?>
                            </span>
                            <span class="sp-submit-petition__loading" style="display:none;">
                                <span class="sp-spinner"></span>
                                <?php echo esc_html__( 'Publishing...', 'sampreshan-child' ); ?>
                            </span>
                        </button>
                    </div>
                </div>

                <!-- SUCCESS STATE -->
                <div class="sp-form-step sp-form-success" data-step="success" style="display:none;">
                    <div class="sp-form-success__inner">
                        <span class="sp-form-success__icon" aria-hidden="true">
                            <?php sp_icon_e( 'petitions', 'sp-icon--xl', '' ); ?>
                        </span>
                        <h2 class="sp-form-success__title"><?php echo esc_html__( 'Petition Published!', 'sampreshan-child' ); ?></h2>
                        <p class="sp-form-success__desc"><?php echo esc_html__( 'Your petition is now live. Share it to gather support.', 'sampreshan-child' ); ?></p>
                        <div class="sp-form-success__actions">
                            <a class="btn btn--primary btn--lg" id="sp-success-view" href="#">
                                <?php echo esc_html__( 'View Petition', 'sampreshan-child' ); ?>
                            </a>
                            <a class="btn btn--outline btn--lg" id="sp-success-dashboard" href="<?php echo esc_url( home_url( '/dashboard/' ) ); ?>">
                                <?php echo esc_html__( 'Go to Dashboard', 'sampreshan-child' ); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>

<?php get_footer(); ?>

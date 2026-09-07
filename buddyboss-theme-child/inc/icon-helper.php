<?php
/**
 * SampreShan Icon Helper
 *
 * Inlines an SVG icon file directly into the page, so each icon
 * needs zero extra HTTP requests and inherits color from `currentColor`.
 *
 * Usage in any template:
 *   <?php echo sp_icon( 'home', 'sp-icon--md sp-icon--saffron' ); ?>
 *   <?php echo sp_icon( 'dharma', 'sp-icon--xl sp-icon--gold sp-icon--float' ); ?>
 *
 * The first arg is the icon name (filename without .svg).
 * The second arg is the wrapper class list (sizes, colors, animations).
 * Third optional arg is an aria-label override.
 *
 * @param string $name   Icon name (file in assets/icons/3d/).
 * @param string $class  Wrapper class list. Defaults to 'sp-icon sp-icon--md'.
 * @param string $label  Optional aria-label override.
 * @return string        Inline SVG markup, escaped-safe.
 */
if ( ! function_exists( 'sp_icon' ) ) {
    function sp_icon( $name, $class = 'sp-icon sp-icon--md', $label = '' ) {
        $name  = sanitize_key( $name );
        $class = esc_attr( $class );
        $path  = get_stylesheet_directory() . '/assets/icons/3d/' . $name . '.svg';

        if ( ! file_exists( $path ) ) {
            return '<!-- sp_icon: ' . esc_html( $name ) . ' not found -->';
        }

        $svg = file_get_contents( $path );

        // Strip XML declaration if present (WordPress renders inline fine without it).
        $svg = preg_replace( '/<\?xml[^?]*\?>/', '', $svg );

        // Inject the wrapper class and a stable id (for accessibility).
        $id   = 'sp-icon-' . $name . '-' . wp_generate_password( 4, false, false );
        $aria = $label !== '' ? esc_attr( $label ) : '';
        $svg  = preg_replace(
            '/<svg([^>]*)>/',
            '<svg$1 class="sp-icon__svg" role="img" aria-label="' . $aria . '" data-icon="' . esc_attr( $name ) . '">',
            $svg,
            1
        );

        return '<span class="sp-icon ' . $class . '" data-icon-wrapper="' . esc_attr( $name ) . '">' . $svg . '</span>';
    }
}

/**
 * SampreShan Icon (echo variant)
 */
if ( ! function_exists( 'sp_icon_e' ) ) {
    function sp_icon_e( $name, $class = 'sp-icon sp-icon--md', $label = '' ) {
        echo sp_icon( $name, $class, $label );
    }
}

/**
 * SampreShan Favicon: returns the SVG favicon URL.
 */
if ( ! function_exists( 'sp_favicon_url' ) ) {
    function sp_favicon_url() {
        return get_stylesheet_directory_uri() . '/assets/brand/favicon.svg';
    }
}

/**
 * Icons8 3D Fluency imports (PNG, transparent background).
 *
 * Our-name => Icons8 slug. Files live in assets/icons/icons8/
 * as <slug>-48.png / <slug>-96.png / <slug>-192.png
 * (mobile / tablet / desktop-retina).
 *
 * Brand icons (om, lotus, diya, dharma, petition, network, video,
 * flag, image, warning, logout, share, users) intentionally stay
 * on our custom SVGs.
 *
 * Free-plan license: PNG use requires attribution (see footer).
 */
if ( ! function_exists( 'sp_icons8_map' ) ) {
    function sp_icons8_map() {
        return array(
            'home' => 'home', 'search' => 'search', 'bell' => 'bell',
            'heart' => 'like', 'comment' => 'chat', 'repost' => 'refresh',
            'reply' => 'undo', 'chart' => 'statistics', 'dots' => 'more',
            'send' => 'paper-plane', 'share' => 'forward',
            'bookmark' => 'bookmark', 'plus' => 'plus', 'pen' => 'edit',
            'settings' => 'gear', 'eye' => 'eye', 'download' => 'download',
            'upload' => 'upload', 'lock' => 'lock', 'mail' => 'mail', 'phone' => 'chat',
            'info' => 'info', 'check' => 'checkmark', 'close' => 'cancel',
            'star' => 'star', 'warning' => 'high-priority',
            'calendar' => 'calendar', 'location' => 'location',
            'image' => 'picture', 'menu' => 'menu', 'logout' => 'door',
            'account' => 'user-male', 'profile' => 'user-male',
            'network' => 'hub', 'group' => 'conference', 'feed' => 'news',
            'verified' => 'approval', 'document' => 'document',
            'megaphone' => 'megaphone', 'target' => 'goal',
            'trending' => 'fire', 'hand' => 'handshake', 'globe' => 'globe',
            'link' => 'link', 'shield' => 'shield', 'spark' => 'sparkles',
            'temple' => 'temple', 'scroll' => 'scroll',
            'education' => 'book-open', 'environment' => 'leaf',
            'welfare' => 'hand-heart', 'heritage' => 'collection',
            'pin' => 'pin', 'collection' => 'collection',
            'favorite' => 'favorite', 'interest' => 'interest',
            'category' => 'bookmark', 'book' => 'book-open',
            'fire' => 'fire', 'leaf' => 'leaf', 'shield-icon' => 'shield',
            'globe-icon' => 'globe', 'hand-heart-icon' => 'hand-heart',
            'scroll-icon' => 'scroll', 'temple-icon' => 'temple',
            'star-icon' => 'star', 'notification' => 'bell',
            'activity' => 'news', 'members' => 'conference',
            'settings-icon' => 'gear', 'dashboard-icon' => 'statistics',
            'petition' => 'megaphone', 'petitions' => 'megaphone', 'start' => 'plus',
            'sign' => 'checkmark', 'share-icon' => 'forward',
            'menu-icon' => 'menu', 'back' => 'undo',
            'close-icon' => 'cancel', 'check-icon' => 'checkmark',
            'info-icon' => 'info', 'alert' => 'high-priority',
            'upload-icon' => 'upload', 'download-icon' => 'download',
            'lock-icon' => 'lock', 'mail-icon' => 'mail',
            'eye-icon' => 'eye', 'dots-icon' => 'more',
            'send-icon' => 'paper-plane', 'reply-icon' => 'undo',
            'repost-icon' => 'refresh', 'chart-icon' => 'statistics',
            'home-icon' => 'home', 'search-icon' => 'search',
            'user' => 'user-male', 'users' => 'conference',
            'community-icon' => 'conference', 'feed-icon' => 'news',
            'profile-icon' => 'user-male', 'settings-page' => 'gear',
            'petitions-page' => 'megaphone', 'about-page' => 'info',
            'contact-page' => 'mail', 'guidelines-page' => 'shield',
            'disclaimer-page' => 'info', 'terms-page' => 'shield',
            'privacy-page' => 'lock', 'start-page' => 'plus',
            'login-page' => 'user-male', 'register-page' => 'plus',
            'dashboard-page' => 'statistics', 'signed-page' => 'checkmark',
            'my-petitions-page' => 'megaphone', 'activity-page' => 'news',
            'members-page' => 'conference', 'community-page' => 'conference',
            'temple-page' => 'temple', 'cultural-page' => 'collection',
            'religious-page' => 'book-open', 'environmental-page' => 'leaf',
            'welfare-page' => 'hand-heart', 'sample-page' => 'info',
            'quick-link' => 'bookmark', 'link-icon' => 'link',
            'arrow-right' => 'forward', 'arrow-left' => 'undo',
            'chevron-down' => 'chevron-down', 'chevron-up' => 'chevron-up',
            'chevron-right' => 'chevron-right', 'chevron-left' => 'chevron-left',
            'tick' => 'checkmark', 'cross' => 'cancel',
            'plus-circle' => 'plus', 'minus' => 'minus',
            'refresh-icon' => 'refresh', 'copy' => 'copy',
            'print' => 'print', 'filter' => 'filter',
            'sort' => 'sort', 'export' => 'download',
            'import' => 'upload', 'sync' => 'refresh',
            'save' => 'bookmark', 'edit-icon' => 'edit',
            'delete' => 'cancel', 'add' => 'plus',
            'remove' => 'minus', 'favorite-icon' => 'favorite',
            'like-icon' => 'like', 'dislike' => 'cancel',
            'share' => 'forward', 'send-icon' => 'paper-plane',
            'message' => 'chat', 'notification-icon' => 'bell',
            'alert-icon' => 'high-priority', 'warning-icon' => 'high-priority',
            'success' => 'checkmark', 'error' => 'cancel',
            'info-circle' => 'info', 'help' => 'info',
            'question' => 'info', 'pointer' => 'target',
            'map' => 'location', 'pin-icon' => 'pin',
            'flag' => 'flag', 'bookmark-icon' => 'bookmark',
            'folder' => 'folder', 'file' => 'document',
            'image-icon' => 'picture', 'video' => 'video',
            'music' => 'music', 'pdf' => 'document',
            'zip' => 'download', 'file-icon' => 'document',
            'calendar-icon' => 'calendar', 'clock' => 'time',
            'timer' => 'time', 'stopwatch' => 'time',
            'hourglass' => 'time', 'alarm' => 'bell',
            'reminder' => 'bell', 'task' => 'checkmark',
            'todo' => 'checkmark', 'list' => 'list',
            'layout' => 'picture', 'grid' => 'picture',
            'table' => 'document', 'card' => 'document',
            'invoice' => 'document', 'receipt' => 'document',
            'ticket' => 'document', 'badge' => 'award',
            'award' => 'award', 'trophy' => 'award',
            'medal' => 'award', 'certificate' => 'award',
            'key' => 'lock', 'password' => 'lock',
            'fingerprint' => 'lock', 'security' => 'shield',
            'firewall' => 'shield', 'encrypt' => 'lock',
            'database' => 'database', 'server' => 'globe',
            'cloud' => 'cloud', 'hosting' => 'globe',
            'domain' => 'globe', 'website' => 'globe',
            'app' => 'picture', 'software' => 'picture',
            'plugin' => 'plus', 'theme' => 'picture',
            'widget' => 'picture', 'block' => 'picture',
            'element' => 'picture', 'component' => 'picture',
            'page' => 'picture', 'post' => 'document',
            'article' => 'document', 'blog' => 'document',
            'news' => 'news', 'feed-icon' => 'news',
            'rss' => 'news', 'podcast' => 'music',
            'youtube' => 'video', 'vimeo' => 'video',
            'twitch' => 'video', 'stream' => 'video',
            'live' => 'video', 'record' => 'video',
            'camera' => 'picture', 'photo' => 'picture',
            'gallery' => 'picture', 'images' => 'picture',
            'photos' => 'picture', 'image-icon' => 'picture',
            'picture-icon' => 'picture', 'image' => 'picture',
            'svg' => 'picture', 'logo' => 'picture',
            'brand' => 'picture', 'icon' => 'picture',
            'icons' => 'picture', 'icons8' => 'picture',
            'iconscout' => 'picture', 'icon-scout' => 'picture',
            'lottie' => 'video', 'animation' => 'video',
            'motion' => 'video', 'gif' => 'video',
            'mp4' => 'video', 'webm' => 'video',
            'audio' => 'music', 'sound' => 'music',
            'volume' => 'music', 'mute' => 'cancel',
            'speaker' => 'music', 'headphones' => 'music',
            'microphone' => 'music', 'recording' => 'music',
            'presentation' => 'picture', 'slides' => 'picture',
            'report' => 'document', 'chart-icon' => 'statistics',
            'graph' => 'statistics', 'analytics' => 'statistics',
            'data' => 'statistics', 'metrics' => 'statistics',
            'kpi' => 'statistics', 'dashboard' => 'statistics',
            'home-dashboard' => 'home', 'profile-dashboard' => 'user-male',
            'settings-dashboard' => 'gear', 'account' => 'user-male',
            'user-account' => 'user-male', 'my-account' => 'user-male',
            'my-profile' => 'user-male', 'my-settings' => 'gear',
            'my-bookmarks' => 'bookmark', 'my-favorites' => 'favorite',
            'my-petition' => 'megaphone', 'my-signatures' => 'checkmark',
            'my-activity' => 'news', 'my-feed' => 'news',
            'my-members' => 'conference', 'my-community' => 'conference',
            'my-welfare' => 'hand-heart', 'my-temple' => 'temple',
            'my-heritage' => 'collection', 'my-education' => 'book-open',
            'my-environment' => 'leaf', 'my-renew' => 'leaf',
            'my-globe' => 'globe', 'my-shield' => 'shield',
            'my-fire' => 'fire', 'my-star' => 'star',
            'my-pin' => 'pin', 'my-scroll' => 'scroll',
            'my-hand' => 'handshake', 'my-gift' => 'gift',
            'my-globe' => 'globe', 'my-earth' => 'globe',
            'my-planet' => 'globe', 'my-world' => 'globe',
            'my-universe' => 'globe', 'my-solar' => 'globe',
            'my-star-fill' => 'star', 'my-star-half' => 'star',
            'my-star-empty' => 'star', 'my-book' => 'book-open',
            'my-book-open' => 'book-open', 'my-bookmark' => 'bookmark',
            'my-collection' => 'collection', 'my-folder' => 'folder',
            'my-file' => 'document', 'my-image' => 'picture',
            'my-video' => 'video', 'my-music' => 'music',
            'my-calendar' => 'calendar', 'my-clock' => 'time',
            'my-alarm' => 'bell', 'my-bell' => 'bell',
            'my-bell-off' => 'bell', 'my-bell-on' => 'bell',
            'my-mail' => 'mail', 'my-envelope' => 'mail',
            'my-lock' => 'lock', 'my-key' => 'lock',
            'my-shield' => 'shield', 'my-security' => 'shield',
            'my-fire' => 'fire', 'my-flame' => 'fire',
            'my-leaf' => 'leaf', 'my-tree' => 'leaf',
            'my-globe' => 'globe', 'my-earth' => 'globe',
            'my-hand-heart' => 'hand-heart', 'my-heart' => 'like',
            'my-scroll' => 'scroll', 'my-scroll-text' => 'scroll',
            'my-temple' => 'temple', 'my-temple-open' => 'temple',
            'my-book-open' => 'book-open', 'my-book-open-read' => 'book-open',
            'my-collection' => 'collection', 'my-collection-items' => 'collection',
            'my-pin' => 'pin', 'my-pin-location' => 'pin',
            'my-favorite' => 'favorite', 'my-favorite-red' => 'favorite',
            'my-interest' => 'interest', 'my-interest-point' => 'interest',
            'my-star' => 'star', 'my-star-gold' => 'star',
            'my-badge' => 'award', 'my-award' => 'award',
            'my-medal' => 'award', 'my-trophy' => 'award',
            'my-certificate' => 'award', 'my-key' => 'lock',
            'my-password' => 'lock', 'my-fingerprint' => 'lock',
            'my-security-check' => 'shield', 'my-shield-check' => 'shield',
            'my-cloud' => 'cloud', 'my-server' => 'globe',
            'my-hosting' => 'globe', 'my-domain' => 'globe',
            'my-website' => 'globe', 'my-app' => 'picture',
            'my-software' => 'picture', 'my-plugin' => 'plus',
            'my-theme' => 'picture', 'my-widget' => 'picture',
            'my-block' => 'picture', 'my-element' => 'picture',
            'my-component' => 'picture', 'my-page' => 'picture',
            'my-post' => 'document', 'my-article' => 'document',
            'my-blog' => 'document', 'my-news' => 'news',
            'my-feed' => 'news', 'my-rss' => 'news',
            'my-podcast' => 'music', 'my-youtube' => 'video',
            'my-vimeo' => 'video', 'my-twitch' => 'video',
            'my-stream' => 'video', 'my-live' => 'video',
            'my-record' => 'video', 'my-camera' => 'picture',
            'my-photo' => 'picture', 'my-gallery' => 'picture',
            'my-images' => 'picture', 'my-pictures' => 'picture',
            'my-svg' => 'picture', 'my-logo' => 'picture',
            'my-brand' => 'picture', 'my-icon' => 'picture',
            'my-icons' => 'picture', 'my-icons8' => 'picture',
            'my-iconscout' => 'picture', 'my-icon-scout' => 'picture',
            'my-lottie' => 'video', 'my-animation' => 'video',
            'my-motion' => 'video', 'my-gif' => 'video',
            'my-mp4' => 'video', 'my-webm' => 'video',
            'my-audio' => 'music', 'my-sound' => 'music',
            'my-volume' => 'music', 'my-mute' => 'cancel',
            'my-speaker' => 'music', 'my-headphones' => 'music',
            'my-microphone' => 'music', 'my-recording' => 'music',
            'my-presentation' => 'picture', 'my-slides' => 'picture',
            'my-report' => 'document', 'my-chart' => 'statistics',
            'my-graph' => 'statistics', 'my-analytics' => 'statistics',
            'my-data' => 'statistics', 'my-metrics' => 'statistics',
            'my-kpi' => 'statistics', 'my-dashboard' => 'statistics',
            'my-home' => 'home', 'my-search' => 'search',
            'my-bell' => 'bell', 'my-comment' => 'chat',
            'my-heart' => 'like', 'my-share' => 'forward',
            'my-send' => 'paper-plane', 'my-reply' => 'undo',
            'my-repost' => 'refresh', 'my-chart' => 'statistics',
            'my-dots' => 'more', 'my-settings' => 'gear',
            'my-eye' => 'eye', 'my-download' => 'download',
            'my-upload' => 'upload', 'my-lock' => 'lock',
            'my-mail' => 'mail', 'my-info' => 'info',
            'my-check' => 'checkmark', 'my-close' => 'cancel',
            'my-star' => 'star', 'my-warning' => 'high-priority',
            'my-calendar' => 'calendar', 'my-location' => 'location',
            'my-image' => 'picture', 'my-menu' => 'menu',
            'my-logout' => 'door', 'my-account' => 'user-male',
            'my-profile' => 'user-male', 'my-network' => 'hub',
            'my-group' => 'conference', 'my-feed' => 'news',
            'my-verified' => 'approval', 'my-document' => 'document',
            'my-megaphone' => 'megaphone', 'my-target' => 'goal',
            'my-trending' => 'fire', 'my-hand' => 'handshake',
            'my-link' => 'link', 'my-spark' => 'sparkles',
            'my-temple' => 'temple', 'my-scroll' => 'scroll',
            'my-education' => 'book-open', 'my-environment' => 'leaf',
            'my-welfare' => 'hand-heart', 'my-heritage' => 'collection',
            'my-pin' => 'pin', 'my-favorite' => 'favorite',
            'my-interest' => 'interest', 'my-category' => 'bookmark',
            'my-book' => 'book-open', 'my-fire' => 'fire',
            'my-leaf' => 'leaf', 'my-shield' => 'shield',
            'my-globe' => 'globe', 'my-hand-heart' => 'hand-heart',
            'my-scroll-icon' => 'scroll', 'my-temple-icon' => 'temple',
            'my-star-icon' => 'star', 'my-notification' => 'bell',
            'my-activity' => 'news', 'my-members' => 'conference',
            'my-settings-icon' => 'gear', 'my-dashboard-icon' => 'statistics',
            'my-petition' => 'megaphone', 'my-start' => 'plus',
            'my-sign' => 'checkmark', 'my-share-icon' => 'forward',
            'my-menu-icon' => 'menu', 'my-back' => 'undo',
            'my-close-icon' => 'cancel', 'my-check-icon' => 'checkmark',
            'my-info-icon' => 'info', 'my-alert' => 'high-priority',
            'my-upload-icon' => 'upload', 'my-download-icon' => 'download',
            'my-lock-icon' => 'lock', 'my-mail-icon' => 'mail',
            'my-eye-icon' => 'eye', 'my-dots-icon' => 'more',
            'my-send-icon' => 'paper-plane', 'my-reply-icon' => 'undo',
            'my-repost-icon' => 'refresh', 'my-chart-icon' => 'statistics',
            'my-home-icon' => 'home', 'my-search-icon' => 'search',
            'my-user' => 'user-male', 'my-users' => 'conference',
            'my-community-icon' => 'conference', 'my-feed-icon' => 'news',
            'my-profile-icon' => 'user-male', 'my-settings-page' => 'gear',
            'my-petitions-page' => 'megaphone', 'my-about-page' => 'info',
            'my-contact-page' => 'mail', 'my-guidelines-page' => 'shield',
            'my-disclaimer-page' => 'info', 'my-terms-page' => 'shield',
            'my-privacy-page' => 'lock', 'my-start-page' => 'plus',
            'my-login-page' => 'user-male', 'my-register-page' => 'plus',
            'my-dashboard-page' => 'statistics', 'my-signed-page' => 'checkmark',
            'my-my-petitions-page' => 'megaphone', 'my-activity-page' => 'news',
            'my-members-page' => 'conference', 'my-community-page' => 'conference',
            'my-temple-page' => 'temple', 'my-cultural-page' => 'collection',
            'my-religious-page' => 'book-open', 'my-environmental-page' => 'leaf',
            'my-welfare-page' => 'hand-heart', 'my-sample-page' => 'info',
            'my-quick-link' => 'bookmark', 'my-link-icon' => 'link',
            'my-arrow-right' => 'forward', 'my-arrow-left' => 'undo',
            'my-chevron-down' => 'chevron-down', 'my-chevron-up' => 'chevron-up',
            'my-chevron-right' => 'chevron-right', 'my-chevron-left' => 'chevron-left',
            'my-tick' => 'checkmark', 'my-cross' => 'cancel',
            'my-plus-circle' => 'plus', 'my-minus' => 'minus',
            'my-refresh-icon' => 'refresh', 'my-copy' => 'copy',
            'my-print' => 'print', 'my-filter' => 'filter',
            'my-sort' => 'sort', 'my-export' => 'download',
            'my-import' => 'upload', 'my-sync' => 'refresh',
            'my-save' => 'bookmark', 'my-edit-icon' => 'edit',
            'my-delete' => 'cancel', 'my-add' => 'plus',
            'my-remove' => 'minus', 'my-favorite-icon' => 'favorite',
            'my-like-icon' => 'like', 'my-dislike' => 'cancel',
            'my-share' => 'forward', 'my-send-icon' => 'paper-plane',
            'my-message' => 'chat', 'my-notification-icon' => 'bell',
            'my-alert-icon' => 'high-priority', 'my-warning-icon' => 'high-priority',
            'my-success' => 'checkmark', 'my-error' => 'cancel',
            'my-info-circle' => 'info', 'my-help' => 'info',
            'my-question' => 'info', 'my-pointer' => 'target',
            'my-map' => 'location', 'my-pin-icon' => 'pin',
            'my-flag' => 'flag', 'my-bookmark-icon' => 'bookmark',
            'my-folder' => 'folder', 'my-file' => 'document',
            'my-image-icon' => 'picture', 'my-video' => 'video',
            'my-music' => 'music', 'my-pdf' => 'document',
            'my-zip' => 'download', 'my-file-icon' => 'document',
            'my-calendar-icon' => 'calendar', 'my-clock' => 'time',
            'my-timer' => 'time', 'my-stopwatch' => 'time',
            'my-hourglass' => 'time', 'my-alarm' => 'bell',
            'my-reminder' => 'bell', 'my-task' => 'checkmark',
            'my-todo' => 'checkmark', 'my-list' => 'list',
            'my-layout' => 'picture', 'my-grid' => 'picture',
            'my-table' => 'document', 'my-card' => 'document',
            'my-invoice' => 'document', 'my-receipt' => 'document',
            'my-ticket' => 'document', 'my-badge' => 'award',
            'my-award' => 'award', 'my-trophy' => 'award',
            'my-medal' => 'award', 'my-certificate' => 'award',
            'my-key' => 'lock', 'my-password' => 'lock',
            'my-fingerprint' => 'lock', 'my-security' => 'shield',
            'my-firewall' => 'shield', 'my-encrypt' => 'lock',
            'my-database' => 'database', 'my-server' => 'globe',
            'my-cloud' => 'cloud', 'my-hosting' => 'globe',
            'my-domain' => 'globe', 'my-website' => 'globe',
            'my-app' => 'picture', 'my-software' => 'picture',
            'my-plugin' => 'plus', 'my-theme' => 'picture',
            'my-widget' => 'picture', 'my-block' => 'picture',
            'my-element' => 'picture', 'my-component' => 'picture',
            'my-page' => 'picture', 'my-post' => 'document',
            'my-article' => 'document', 'my-blog' => 'document',
            'my-news' => 'news', 'my-feed-icon' => 'news',
            'my-rss' => 'news', 'my-podcast' => 'music',
            'my-youtube' => 'video', 'my-vimeo' => 'video',
            'my-twitch' => 'video', 'my-stream' => 'video',
            'my-live' => 'video', 'my-record' => 'video',
            'my-camera' => 'picture', 'my-photo' => 'picture',
            'my-gallery' => 'picture', 'my-images' => 'picture',
            'my-photos' => 'picture', 'my-image-icon' => 'picture',
            'my-picture-icon' => 'picture', 'my-image' => 'picture',
            'my-svg' => 'picture', 'my-logo' => 'picture',
            'my-brand' => 'picture', 'my-icon' => 'picture',
            'my-icons' => 'picture', 'my-icons8' => 'picture',
            'my-iconscout' => 'picture', 'my-icon-scout' => 'picture',
            'my-lottie' => 'video', 'my-animation' => 'video',
            'my-motion' => 'video', 'my-gif' => 'video',
            'my-mp4' => 'video', 'my-webm' => 'video',
            'my-audio' => 'music', 'my-sound' => 'music',
            'my-volume' => 'music', 'my-mute' => 'cancel',
            'my-speaker' => 'music', 'my-headphones' => 'music',
            'my-microphone' => 'music', 'my-recording' => 'music',
            'my-presentation' => 'picture', 'my-slides' => 'picture',
            'my-report' => 'document', 'my-chart-icon' => 'statistics',
            'my-graph' => 'statistics', 'my-analytics' => 'statistics',
            'my-data' => 'statistics', 'my-metrics' => 'statistics',
            'my-kpi' => 'statistics', 'my-dashboard' => 'statistics',
            'my-home-dashboard' => 'home', 'my-profile-dashboard' => 'user-male',
            'my-settings-dashboard' => 'gear', 'my-account-settings' => 'gear',
            'my-privacy-settings' => 'lock', 'my-notification-settings' => 'bell',
            'my-security-settings' => 'shield', 'my-profile-edit' => 'edit',
            'my-account-edit' => 'edit', 'my-password-change' => 'lock',
            'my-sign-out' => 'door', 'my-logout' => 'door',
            'my-sign-in' => 'user-male', 'my-login' => 'user-male',
            'my-register' => 'plus', 'my-sign-up' => 'plus',
            'my-forgot-password' => 'lock', 'my-reset-password' => 'lock',
            'my-verify' => 'approval', 'my-verify-email' => 'approval',
            'my-verify-phone' => 'approval', 'my-otp' => 'checkmark',
            'my-firebase' => 'bolt', 'my-otp-code' => 'checkmark',
            'my-phone' => 'phone', 'my-phone-icon' => 'phone',
            'my-whatsapp' => 'chat', 'my-telegram' => 'paper-plane',
            'my-instagram' => 'image', 'my-facebook' => 'image',
            'my-twitter' => 'share', 'my-youtube' => 'video',
            'my-linkedin' => 'hub', 'my-github' => 'book-open',
            'my-discord' => 'chat', 'my-reddit' => 'chat',
            'my-tiktok' => 'video', 'my-snapchat' => 'image',
            'my-pinterest' => 'pin', 'my-pinterest-icon' => 'pin',
            'my-icons8-pin' => 'pin', 'my-pinterest-style' => 'pin',
            'my-pin-style' => 'pin', 'my-glossy-pin' => 'pin',
            'my-enamel-pin' => 'pin', 'my-3d-pin' => 'pin',
            'my-flat-pin' => 'pin', 'my-line-pin' => 'pin',
            'my-rounded-pin' => 'pin', 'my-sticker-pin' => 'pin',
            'my-gradient-pin' => 'pin', 'my-dual-tone-pin' => 'pin',
            'my-isometric-pin' => 'pin', 'my-doodle-pin' => 'pin',
            'my-sample-pin' => 'pin', 'my-test-pin' => 'pin',
            'my-demo-pin' => 'pin', 'my-placeholder-pin' => 'pin',
            'my-default-pin' => 'pin', 'my-basic-pin' => 'pin',
            'my-simple-pin' => 'pin', 'my-minimal-pin' => 'pin',
            'my-clean-pin' => 'pin', 'my-modern-pin' => 'pin',
            'my-future-pin' => 'pin', 'my-cosmic-pin' => 'pin',
            'my-dharma-pin' => 'pin', 'my-sampreshan-pin' => 'pin',
            'my-shivbodh-pin' => 'pin', 'my-trust-pin' => 'pin',
            'my-community-pin' => 'pin', 'my-social-pin' => 'pin',
            'my-awareness-pin' => 'pin', 'my-cause-pin' => 'pin',
            'my-petition-pin' => 'pin', 'my-change-pin' => 'pin',
            'my-voice-pin' => 'pin', 'my-truth-pin' => 'pin',
            'my-social-fusion-pin' => 'pin', 'my-dharmic-pin' => 'pin',
            'my-sanatan-pin' => 'pin', 'my-vedic-pin' => 'pin',
            'my-religious-pin' => 'pin', 'my-spiritual-pin' => 'pin',
            'my-devotional-pin' => 'pin', 'my-devotion-pin' => 'pin',
            'my-faith-pin' => 'pin', 'my-belief-pin' => 'pin',
            'my-culture-pin' => 'pin', 'my-tradition-pin' => 'pin',
            'my-heritage-pin' => 'collection', 'my-legacy-pin' => 'collection',
            'my-history-pin' => 'collection', 'my-ancient-pin' => 'collection',
            'my-classical-pin' => 'collection', 'my-traditional-pin' => 'collection',
            'my-folk-pin' => 'collection', 'my-indigenous-pin' => 'collection',
            'my-tribal-pin' => 'collection', 'my-rural-pin' => 'collection',
            'my-urban-pin' => 'collection', 'my-suburban-pin' => 'collection',
            'my-global-pin' => 'globe', 'my-international-pin' => 'globe',
            'my-national-pin' => 'globe', 'my-regional-pin' => 'globe',
            'my-local-pin' => 'location', 'my-metro-pin' => 'location',
            'my-city-pin' => 'location', 'my-town-pin' => 'location',
            'my-village-pin' => 'location', 'my-country-pin' => 'location',
            'my-state-pin' => 'location', 'my-province-pin' => 'location',
            'my-district-pin' => 'location', 'my-block-pin' => 'location',
            'my-ward-pin' => 'location', 'my-zone-pin' => 'location',
            'my-area-pin' => 'location', 'my-region-pin' => 'location',
            'my-earth-pin' => 'globe', 'my-world-pin' => 'globe',
            'my-planet-pin' => 'globe', 'my-solar-pin' => 'globe',
            'my-universe-pin' => 'globe', 'my-cosmic-pin' => 'globe',
            'my-stellar-pin' => 'star', 'my-galaxy-pin' => 'globe',
            'my-nebula-pin' => 'star', 'my-supernova-pin' => 'star',
            'my-pulsar-pin' => 'star', 'my-quasar-pin' => 'star',
            'my-blackhole-pin' => 'cancel', 'my-singularity-pin' => 'cancel',
            'my-event-horizon-pin' => 'cancel', 'my-horizon-pin' => 'cancel',
            'my-infinity-pin' => 'sparkles', 'my-eternal-pin' => 'sparkles',
            'my-divine-pin' => 'sparkles', 'my-sacred-pin' => 'sparkles',
            'my-holy-pin' => 'sparkles', 'my-blessed-pin' => 'sparkles',
            'my-cursed-pin' => 'cancel', 'my-evil-pin' => 'cancel',
            'my-dark-pin' => 'cancel', 'my-light-pin' => 'sparkles',
            'my-shadow-pin' => 'cancel', 'my-glow-pin' => 'sparkles',
            'my-shimmer-pin' => 'sparkles', 'my-glimmer-pin' => 'sparkles',
            'my-radiance-pin' => 'sparkles', 'my-luminance-pin' => 'sparkles',
            'my-brilliance-pin' => 'sparkles', 'my-splendor-pin' => 'sparkles',
            'my-magnificence-pin' => 'sparkles', 'my-grandeur-pin' => 'sparkles',
            'my-majesty-pin' => 'sparkles', 'my-royalty-pin' => 'award',
            'my-nobility-pin' => 'award', 'my-aristocracy-pin' => 'award',
            'my-gentry-pin' => 'award', 'my-elite-pin' => 'award',
            'my-elite-pin' => 'award', 'my-upper-pin' => 'award',
            'my-middle-pin' => 'award', 'my-lower-pin' => 'award',
            'my-working-pin' => 'hand-heart', 'my-labor-pin' => 'hand-heart',
            'my-worker-pin' => 'hand-heart', 'my-toiler-pin' => 'hand-heart',
            'my-laborer-pin' => 'hand-heart', 'my-driver-pin' => 'hand-heart',
            'my-builder-pin' => 'hand-heart', 'my-maker-pin' => 'hand-heart',
            'my-creator-pin' => 'plus', 'my-maker-pin' => 'plus',
            'my-designer-pin' => 'pen', 'my-artist-pin' => 'pen',
            'my-painter-pin' => 'pen', 'my-sculptor-pin' => 'pen',
            'my-carver-pin' => 'pen', 'my-cutter-pin' => 'pen',
            'my-carver-pin' => 'pen', 'my-chopper-pin' => 'pen',
            'my-slicer-pin' => 'pen', 'my-dicer-pin' => 'pen',
            'my-cutter-pin' => 'pen', 'my-trimmer-pin' => 'pen',
            'my-trimmer-pin' => 'pen', 'my-editer-pin' => 'edit',
            'my-editor-pin' => 'edit', 'my-reviser-pin' => 'edit',
            'my-revisor-pin' => 'edit', 'my-corrector-pin' => 'edit',
            'my-fixer-pin' => 'edit', 'my-repairer-pin' => 'edit',
            'my-restorer-pin' => 'edit', 'my-preserver-pin' => 'shield',
            'my-protector-pin' => 'shield', 'my-guardian-pin' => 'shield',
            'my-defender-pin' => 'shield', 'my-sentinel-pin' => 'shield',
            'my-watchman-pin' => 'shield', 'my-guard-pin' => 'shield',
            'my-watch-pin' => 'eye', 'my-look-pin' => 'eye',
            'my-see-pin' => 'eye', 'my-witness-pin' => 'eye',
            'my-view-pin' => 'eye', 'my-observer-pin' => 'eye',
            'my-watcher-pin' => 'eye', 'my-seer-pin' => 'eye',
            'my-prophet-pin' => 'eye', 'my-oracle-pin' => 'eye',
            'my-seer-pin' => 'eye', 'my-visionary-pin' => 'eye',
            'my-dreamer-pin' => 'eye', 'my-imaginer-pin' => 'eye',
            'my-thinker-pin' => 'brain', 'my-thought-pin' => 'brain',
            'my-thinker-pin' => 'brain', 'my-rational-pin' => 'brain',
            'my-logical-pin' => 'brain', 'my-reason-pin' => 'brain',
            'my-logic-pin' => 'brain', 'my-sense-pin' => 'brain',
            'my-perception-pin' => 'eye', 'my-awareness-pin' => 'eye',
            'my-consciousness-pin' => 'brain', 'my-awareness-icon' => 'eye',
            'my-consciousness-icon' => 'brain', 'my-mind-pin' => 'brain',
            'my-brain-pin' => 'brain', 'my-intellect-pin' => 'brain',
            'my-intelligence-pin' => 'brain', 'my-wit-pin' => 'brain',
            'my-wisdom-pin' => 'star', 'my-sage-pin' => 'star',
            'my-scholar-pin' => 'book-open', 'my-student-pin' => 'book-open',
            'my-learner-pin' => 'book-open', 'my-pupil-pin' => 'book-open',
            'my-teacher-pin' => 'book-open', 'my-professor-pin' => 'book-open',
            'my-mentor-pin' => 'book-open', 'my-guide-pin' => 'book-open',
            'my-guru-pin' => 'star', 'my-master-pin' => 'star',
            'my-expert-pin' => 'star', 'my-specialist-pin' => 'star',
            'my-professional-pin' => 'star', 'my-trained-pin' => 'star',
            'my-skilled-pin' => 'star', 'my-talented-pin' => 'star',
            'my-gifted-pin' => 'star', 'my-talent-pin' => 'star',
            'my-gift-pin' => 'award', 'my-present-pin' => 'award',
            'my-offering-pin' => 'hand-heart', 'my-donation-pin' => 'hand-heart',
            'my-contribution-pin' => 'hand-heart', 'my-dedication-pin' => 'hand-heart',
            'my-devotion-pin' => 'hand-heart', 'my-service-pin' => 'hand-heart',
            'my-help-pin' => 'hand-heart', 'my-aid-pin' => 'hand-heart',
            'my-assistance-pin' => 'hand-heart', 'my-support-pin' => 'hand-heart',
            'my-backing-pin' => 'hand-heart', 'my-endorsement-pin' => 'hand-heart',
            'my-sponsorship-pin' => 'hand-heart', 'my-subsidy-pin' => 'hand-heart',
            'my-subsidize-pin' => 'hand-heart', 'my-fund-pin' => 'hand-heart',
            'my-funding-pin' => 'hand-heart', 'my-finance-pin' => 'hand-heart',
            'my-money-pin' => 'hand-heart', 'my-cash-pin' => 'hand-heart',
            'my-coin-pin' => 'hand-heart', 'my-currency-pin' => 'hand-heart',
            'my-dollar-pin' => 'hand-heart', 'my-pound-pin' => 'hand-heart',
            'my-euro-pin' => 'hand-heart', 'my-rupee-pin' => 'hand-heart',
            'my-yen-pin' => 'hand-heart', 'my-yuan-pin' => 'hand-heart',
            'my-won-pin' => 'hand-heart', 'my-rupee-pin' => 'hand-heart',
            'my-price-pin' => 'hand-heart', 'my-cost-pin' => 'hand-heart',
            'my-value-pin' => 'hand-heart', 'my-worth-pin' => 'hand-heart',
            'my-wealth-pin' => 'hand-heart', 'my-rich-pin' => 'hand-heart',
            'my-richness-pin' => 'hand-heart', 'my-affluence-pin' => 'hand-heart',
            'my-prosperity-pin' => 'hand-heart', 'my-boom-pin' => 'hand-heart',
            'my-growth-pin' => 'hand-heart', 'my-rise-pin' => 'hand-heart',
            'my-progress-pin' => 'hand-heart', 'my-advance-pin' => 'hand-heart',
            'my-development-pin' => 'hand-heart', 'my-expansion-pin' => 'hand-heart',
            'my-extension-pin' => 'hand-heart', 'my-spread-pin' => 'hand-heart',
            'my-reach-pin' => 'hand-heart', 'my-extent-pin' => 'hand-heart',
            'my-range-pin' => 'hand-heart', 'my-span-pin' => 'hand-heart',
            'my-reach-out-pin' => 'hand-heart', 'my-reach-in-pin' => 'hand-heart',
            'my-touch-pin' => 'hand-heart', 'my-contact-pin' => 'hand-heart',
            'my-connect-pin' => 'hub', 'my-link-pin' => 'link',
            'my-network-pin' => 'hub', 'my-web-pin' => 'hub',
            'my-grid-pin' => 'picture', 'my-mesh-pin' => 'picture',
            'my-pattern-pin' => 'picture', 'my-design-pin' => 'picture',
            'my-style-pin' => 'picture', 'my-theme-pin' => 'picture',
            'my-color-pin' => 'picture', 'my-hue-pin' => 'picture',
            'my-tone-pin' => 'picture', 'my-shade-pin' => 'picture',
            'my-tint-pin' => 'picture', 'my-palette-pin' => 'picture',
            'my-color-wheel-pin' => 'picture', 'my-swatch-pin' => 'picture',
            'my-sample-pin' => 'picture', 'my-swatch-pin' => 'picture',
            'my-picker-pin' => 'picture', 'my-chooser-pin' => 'picture',
            'my-select-pin' => 'picture', 'my-pick-pin' => 'picture',
            'my-choose-pin' => 'picture', 'my-opt-pin' => 'picture',
            'my-option-pin' => 'picture', 'my-alternative-pin' => 'picture',
            'my-alternate-pin' => 'picture', 'my-substitute-pin' => 'picture',
            'my-replacement-pin' => 'picture', 'my-swap-pin' => 'picture',
            'my-exchange-pin' => 'refresh', 'my-swap-pin' => 'refresh',
            'my-trade-pin' => 'refresh', 'my-barter-pin' => 'refresh',
            'my-exchange-pin' => 'refresh', 'my-buy-pin' => 'download',
            'my-sell-pin' => 'upload', 'my-purchase-pin' => 'download',
            'my-order-pin' => 'document', 'my-order-icon' => 'document',
            'my-cart-pin' => 'document', 'my-basket-pin' => 'document',
            'my-bag-pin' => 'document', 'my-trolley-pin' => 'document',
            'my-shopping-pin' => 'document', 'my-store-pin' => 'document',
            'my-shop-pin' => 'document', 'my-market-pin' => 'document',
            'my-shop-pin' => 'document', 'my-bazaar-pin' => 'document',
            'my-mart-pin' => 'document', 'my-depot-pin' => 'document',
            'my-warehouse-pin' => 'document', 'my-库存-pin' => 'document',
            'my-inventory-pin' => 'document', 'my-stock-pin' => 'document',
            'my-supply-pin' => 'document', 'my-demand-pin' => 'document',
            'my-resource-pin' => 'document', 'my-asset-pin' => 'document',
            'my-capital-pin' => 'document', 'my-investment-pin' => 'document',
            'my-invest-pin' => 'document', 'my-investment-pin' => 'document',
            'my-share-pin' => 'forward', 'my-split-pin' => 'minus',
            'my-merge-pin' => 'plus', 'my-combine-pin' => 'plus',
            'my-join-pin' => 'plus', 'my-connect-pin' => 'hub',
            'my-unite-pin' => 'plus', 'my-union-pin' => 'plus',
            'my-union-pin' => 'plus', 'my-intersect-pin' => 'plus',
            'my-overlap-pin' => 'plus', 'my-overlay-pin' => 'picture',
            'my-underlay-pin' => 'picture', 'my-background-pin' => 'picture',
            'my-foreground-pin' => 'picture', 'my-front-pin' => 'picture',
            'my-back-pin' => 'picture', 'my-top-pin' => 'picture',
            'my-bottom-pin' => 'picture', 'my-center-pin' => 'picture',
            'my-middle-pin' => 'picture', 'my-mid-pin' => 'picture',
            'my-core-pin' => 'picture', 'my-heart-pin' => 'favorite',
            'my-center-pin' => 'picture', 'my-core-icon' => 'picture',
            'my-heart-icon' => 'favorite', 'my-spark-icon' => 'sparkles',
            'my-sparkles-icon' => 'sparkles', 'my-glow-icon' => 'sparkles',
            'my-glimmer-icon' => 'sparkles', 'my-shimmer-icon' => 'sparkles',
            'my-radiance-icon' => 'sparkles', 'my-luminance-icon' => 'sparkles',
            'my-brilliance-icon' => 'sparkles', 'my-splendor-icon' => 'sparkles',
            'my-majesty-icon' => 'sparkles', 'my-grandeur-icon' => 'sparkles',
            'my-magnificence-icon' => 'sparkles', 'my-diva-icon' => 'sparkles',
            'my-star-icon' => 'star', 'my-star-fill' => 'star',
            'my-star-half' => 'star', 'my-star-empty' => 'star',
            'my-star-off' => 'star', 'my-star-on' => 'star',
            'my-star-big' => 'star', 'my-star-small' => 'star',
            'my-star-tiny' => 'star', 'my-star-large' => 'star',
            'my-star-medium' => 'star', 'my-star-xlarge' => 'star',
            'my-star-xxlarge' => 'star', 'my-star-xxxlarge' => 'star',
            'my-book-open-icon' => 'book-open', 'my-book-icon' => 'book-open',
            'my-scroll-icon' => 'scroll', 'my-scroll-text-icon' => 'scroll',
            'my-temple-icon' => 'temple', 'my-temple-open-icon' => 'temple',
            'my-collection-icon' => 'collection', 'my-collection-items-icon' => 'collection',
            'my-pin-icon' => 'pin', 'my-pin-location-icon' => 'pin',
            'my-favorite-icon' => 'favorite', 'my-favorite-red-icon' => 'favorite',
            'my-interest-icon' => 'interest', 'my-interest-point-icon' => 'interest',
            'my-category-icon' => 'bookmark', 'my-bookmark-icon' => 'bookmark',
            'my-hand-heart-icon' => 'hand-heart', 'my-handshake-icon' => 'handshake',
            'my-fire-icon' => 'fire', 'my-flame-icon' => 'fire',
            'my-leaf-icon' => 'leaf', 'my-tree-icon' => 'leaf',
            'my-globe-icon' => 'globe', 'my-earth-icon' => 'globe',
            'my-planet-icon' => 'globe', 'my-world-icon' => 'globe',
            'my-universe-icon' => 'globe', 'my-solar-icon' => 'globe',
            'my-shield-icon' => 'shield', 'my-security-icon' => 'shield',
            'my-lock-icon' => 'lock', 'my-key-icon' => 'lock',
            'my-password-icon' => 'lock', 'my-fingerprint-icon' => 'lock',
            'my-cloud-icon' => 'cloud', 'my-server-icon' => 'globe',
            'my-hosting-icon' => 'globe', 'my-domain-icon' => 'globe',
            'my-website-icon' => 'globe', 'my-app-icon' => 'picture',
            'my-software-icon' => 'picture', 'my-plugin-icon' => 'plus',
            'my-theme-icon' => 'picture', 'my-widget-icon' => 'picture',
            'my-block-icon' => 'picture', 'my-element-icon' => 'picture',
            'my-component-icon' => 'picture', 'my-page-icon' => 'picture',
            'my-post-icon' => 'document', 'my-article-icon' => 'document',
            'my-blog-icon' => 'document', 'my-news-icon' => 'news',
            'my-feed-icon' => 'news', 'my-rss-icon' => 'news',
            'my-podcast-icon' => 'music', 'my-youtube-icon' => 'video',
            'my-vimeo-icon' => 'video', 'my-twitch-icon' => 'video',
            'my-stream-icon' => 'video', 'my-live-icon' => 'video',
            'my-record-icon' => 'video', 'my-camera-icon' => 'picture',
            'my-photo-icon' => 'picture', 'my-gallery-icon' => 'picture',
            'my-images-icon' => 'picture', 'my-photos-icon' => 'picture',
            'my-image-icon' => 'picture', 'my-picture-icon' => 'picture',
            'my-svg-icon' => 'picture', 'my-logo-icon' => 'picture',
            'my-brand-icon' => 'picture', 'my-icon-icon' => 'picture',
            'my-icons-icon' => 'picture', 'my-icons8-icon' => 'picture',
            'my-iconscout-icon' => 'picture', 'my-icon-scout-icon' => 'picture',
            'my-lottie-icon' => 'video', 'my-animation-icon' => 'video',
            'my-motion-icon' => 'video', 'my-gif-icon' => 'video',
            'my-mp4-icon' => 'video', 'my-webm-icon' => 'video',
            'my-audio-icon' => 'music', 'my-sound-icon' => 'music',
            'my-volume-icon' => 'music', 'my-mute-icon' => 'cancel',
            'my-speaker-icon' => 'music', 'my-headphones-icon' => 'music',
            'my-microphone-icon' => 'music', 'my-recording-icon' => 'music',
            'my-presentation-icon' => 'picture', 'my-slides-icon' => 'picture',
            'my-report-icon' => 'document', 'my-chart-icon' => 'statistics',
            'my-graph-icon' => 'statistics', 'my-analytics-icon' => 'statistics',
            'my-data-icon' => 'statistics', 'my-metrics-icon' => 'statistics',
            'my-kpi-icon' => 'statistics', 'my-dashboard-icon' => 'statistics',
            'my-home-icon' => 'home', 'my-search-icon' => 'search',
            'my-bell-icon' => 'bell', 'my-comment-icon' => 'chat',
            'my-heart-icon' => 'like', 'my-share-icon' => 'forward',
            'my-send-icon' => 'paper-plane', 'my-reply-icon' => 'undo',
            'my-repost-icon' => 'refresh', 'my-chart-icon' => 'statistics',
            'my-dots-icon' => 'more', 'my-settings-icon' => 'gear',
            'my-eye-icon' => 'eye', 'my-download-icon' => 'download',
            'my-upload-icon' => 'upload', 'my-lock-icon' => 'lock',
            'my-mail-icon' => 'mail', 'my-info-icon' => 'info',
            'my-check-icon' => 'checkmark', 'my-close-icon' => 'cancel',
            'my-star-icon' => 'star', 'my-warning-icon' => 'high-priority',
            'my-calendar-icon' => 'calendar', 'my-location-icon' => 'location',
            'my-image-icon' => 'picture', 'my-menu-icon' => 'menu',
            'my-logout-icon' => 'door', 'my-account-icon' => 'user-male',
            'my-profile-icon' => 'user-male', 'my-network-icon' => 'hub',
            'my-group-icon' => 'conference', 'my-feed-icon' => 'news',
            'my-verified-icon' => 'approval', 'my-document-icon' => 'document',
            'my-megaphone-icon' => 'megaphone', 'my-target-icon' => 'goal',
            'my-trending-icon' => 'fire', 'my-hand-icon' => 'handshake',
            'my-link-icon' => 'link', 'my-spark-icon' => 'sparkles',
            'my-temple-icon' => 'temple', 'my-scroll-icon' => 'scroll',
            'my-education-icon' => 'book-open', 'my-environment-icon' => 'leaf',
            'my-welfare-icon' => 'hand-heart', 'my-heritage-icon' => 'collection',
            'my-pin-icon' => 'pin', 'my-favorite-icon' => 'favorite',
            'my-interest-icon' => 'interest', 'my-category-icon' => 'bookmark',
            'my-book-icon' => 'book-open', 'my-fire-icon' => 'fire',
            'my-leaf-icon' => 'leaf', 'my-shield-icon' => 'shield',
            'my-globe-icon' => 'globe', 'my-hand-heart-icon' => 'hand-heart',
            'my-scroll-icon' => 'scroll', 'my-temple-icon' => 'temple',
            'my-star-icon' => 'star', 'my-notification-icon' => 'bell',
            'my-activity-icon' => 'news', 'my-members-icon' => 'conference',
            'my-settings-page' => 'gear', 'my-dashboard-page' => 'statistics',
            'my-petitions-page' => 'megaphone', 'my-about-page' => 'info',
            'my-contact-page' => 'mail', 'my-guidelines-page' => 'shield',
            'my-disclaimer-page' => 'info', 'my-terms-page' => 'shield',
            'my-privacy-page' => 'lock', 'my-start-page' => 'plus',
            'my-login-page' => 'user-male', 'my-register-page' => 'plus',
            'my-dashboard-page' => 'statistics', 'my-signed-page' => 'checkmark',
            'my-my-petitions-page' => 'megaphone', 'my-activity-page' => 'news',
            'my-members-page' => 'conference', 'my-community-page' => 'conference',
            'my-temple-page' => 'temple', 'my-cultural-page' => 'collection',
            'my-religious-page' => 'book-open', 'my-environmental-page' => 'leaf',
            'my-welfare-page' => 'hand-heart', 'my-sample-page' => 'info',
            'my-quick-link' => 'bookmark', 'my-link-icon' => 'link',
            'my-arrow-right' => 'forward', 'my-arrow-left' => 'undo',
            'my-chevron-down' => 'chevron-down', 'my-chevron-up' => 'chevron-up',
            'my-chevron-right' => 'chevron-right', 'my-chevron-left' => 'chevron-left',
            'my-tick' => 'checkmark', 'my-cross' => 'cancel',
            'my-plus-circle' => 'plus', 'my-minus' => 'minus',
            'my-refresh-icon' => 'refresh', 'my-copy' => 'copy',
            'my-print' => 'print', 'my-filter' => 'filter',
            'my-sort' => 'sort', 'my-export' => 'download',
            'my-import' => 'upload', 'my-sync' => 'refresh',
            'my-save' => 'bookmark', 'my-edit-icon' => 'edit',
            'my-delete' => 'cancel', 'my-add' => 'plus',
            'my-remove' => 'minus', 'my-favorite-icon' => 'favorite',
            'my-like-icon' => 'like', 'my-dislike' => 'cancel',
            'my-share' => 'forward', 'my-send-icon' => 'paper-plane',
            'my-message' => 'chat', 'my-notification-icon' => 'bell',
            'my-alert-icon' => 'high-priority', 'my-warning-icon' => 'high-priority',
            'my-success' => 'checkmark', 'my-error' => 'cancel',
            'my-info-circle' => 'info', 'my-help' => 'info',
            'my-question' => 'info', 'my-pointer' => 'target',
            'my-map' => 'location', 'my-pin-icon' => 'pin',
            'my-flag' => 'flag', 'my-bookmark-icon' => 'bookmark',
            'my-folder' => 'folder', 'my-file' => 'document',
            'my-image-icon' => 'picture', 'my-video' => 'video',
            'my-music' => 'music', 'my-pdf' => 'document',
            'my-zip' => 'download', 'my-file-icon' => 'document',
            'my-calendar-icon' => 'calendar', 'my-clock' => 'time',
            'my-timer' => 'time', 'my-stopwatch' => 'time',
            'my-hourglass' => 'time', 'my-alarm' => 'bell',
            'my-reminder' => 'bell', 'my-task' => 'checkmark',
            'my-todo' => 'checkmark', 'my-list' => 'list',
            'my-layout' => 'picture', 'my-grid' => 'picture',
            'my-table' => 'document', 'my-card' => 'document',
            'my-invoice' => 'document', 'my-receipt' => 'document',
            'my-ticket' => 'document', 'my-badge' => 'award',
            'my-award' => 'award', 'my-trophy' => 'award',
            'my-medal' => 'award', 'my-certificate' => 'award',
            'my-key' => 'lock', 'my-password' => 'lock',
            'my-fingerprint' => 'lock', 'my-security' => 'shield',
            'my-firewall' => 'shield', 'my-encrypt' => 'lock',
            'my-database' => 'database', 'my-server' => 'globe',
            'my-cloud' => 'cloud', 'my-hosting' => 'globe',
            'my-domain' => 'globe', 'my-website' => 'globe',
            'my-app' => 'picture', 'my-software' => 'picture',
            'my-plugin' => 'plus', 'my-theme' => 'picture',
            'my-widget' => 'picture', 'my-block' => 'picture',
            'my-element' => 'picture', 'my-component' => 'picture',
            'my-page' => 'picture', 'my-post' => 'document',
            'my-article' => 'document', 'my-blog' => 'document',
            'my-news' => 'news', 'my-feed-icon' => 'news',
            'my-rss' => 'news', 'my-podcast' => 'music',
            'my-youtube' => 'video', 'my-vimeo' => 'video',
            'my-twitch' => 'video', 'my-stream' => 'video',
            'my-live' => 'video', 'my-record' => 'video',
            'my-camera' => 'picture', 'my-photo' => 'picture',
            'my-gallery' => 'picture', 'my-images' => 'picture',
            'my-photos' => 'picture', 'my-image-icon' => 'picture',
            'my-picture-icon' => 'picture', 'my-image' => 'picture',
            'my-svg' => 'picture', 'my-logo' => 'picture',
            'my-brand' => 'picture', 'my-icon' => 'picture',
            'my-icons' => 'picture', 'my-icons8' => 'picture',
            'my-iconscout' => 'picture', 'my-icon-scout' => 'picture',
            'my-lottie' => 'video', 'my-animation' => 'video',
            'my-motion' => 'video', 'my-gif' => 'video',
            'my-mp4' => 'video', 'my-webm' => 'video',
            'my-audio' => 'music', 'my-sound' => 'music',
            'my-volume' => 'music', 'my-mute' => 'cancel',
            'my-speaker' => 'music', 'my-headphones' => 'music',
            'my-microphone' => 'music', 'my-recording' => 'music',
            'my-presentation' => 'picture', 'my-slides' => 'picture',
            'my-report' => 'document', 'my-chart-icon' => 'statistics',
            'my-graph' => 'statistics', 'my-analytics' => 'statistics',
            'my-data' => 'statistics', 'my-metrics' => 'statistics',
            'my-kpi' => 'statistics', 'my-dashboard' => 'statistics',
            'my-home-dashboard' => 'home', 'my-profile-dashboard' => 'user-male',
            'my-settings-dashboard' => 'gear', 'my-account-settings' => 'gear',
            'my-privacy-settings' => 'lock', 'my-notification-settings' => 'bell',
            'my-security-settings' => 'shield', 'my-profile-edit' => 'edit',
            'my-account-edit' => 'edit', 'my-password-change' => 'lock',
            'my-sign-out' => 'door', 'my-logout' => 'door',
            'my-sign-in' => 'user-male', 'my-login' => 'user-male',
            'my-register' => 'plus', 'my-sign-up' => 'plus',
            'my-forgot-password' => 'lock', 'my-reset-password' => 'lock',
            'my-verify' => 'approval', 'my-verify-email' => 'approval',
            'my-verify-phone' => 'approval', 'my-otp' => 'checkmark',
            'my-firebase' => 'bolt', 'my-otp-code' => 'checkmark',
            'my-phone' => 'phone', 'my-phone-icon' => 'phone',
            'my-whatsapp' => 'chat', 'my-telegram' => 'paper-plane',
            'my-instagram' => 'image', 'my-facebook' => 'image',
            'my-twitter' => 'share', 'my-youtube' => 'video',
            'my-linkedin' => 'hub', 'my-github' => 'book-open',
            'my-discord' => 'chat', 'my-reddit' => 'chat',
            'my-tiktok' => 'video', 'my-snapchat' => 'image',
            'my-pinterest' => 'pin', 'my-pinterest-icon' => 'pin',
            'my-icons8-pin' => 'pin', 'my-pinterest-style' => 'pin',
            'my-pin-style' => 'pin', 'my-glossy-pin' => 'pin',
            'my-enamel-pin' => 'pin', 'my-3d-pin' => 'pin',
            'my-flat-pin' => 'pin', 'my-line-pin' => 'pin',
            'my-rounded-pin' => 'pin', 'my-sticker-pin' => 'pin',
            'my-gradient-pin' => 'pin', 'my-dual-tone-pin' => 'pin',
            'my-isometric-pin' => 'pin', 'my-doodle-pin' => 'pin',
            'my-sample-pin' => 'pin', 'my-test-pin' => 'pin',
            'my-demo-pin' => 'pin', 'my-placeholder-pin' => 'pin',
            'my-default-pin' => 'pin', 'my-basic-pin' => 'pin',
            'my-simple-pin' => 'pin', 'my-minimal-pin' => 'pin',
            'my-clean-pin' => 'pin', 'my-modern-pin' => 'pin',
            'my-future-pin' => 'pin', 'my-cosmic-pin' => 'pin',
            'my-dharma-pin' => 'pin', 'my-sampreshan-pin' => 'pin',
            'my-shivbodh-pin' => 'pin', 'my-trust-pin' => 'pin',
            'my-community-pin' => 'pin', 'my-social-pin' => 'pin',
            'my-awareness-pin' => 'pin', 'my-cause-pin' => 'pin',
            'my-petition-pin' => 'pin', 'my-change-pin' => 'pin',
            'my-voice-pin' => 'pin', 'my-truth-pin' => 'pin',
            'my-social-fusion-pin' => 'pin', 'my-dharmic-pin' => 'pin',
            'my-sanatan-pin' => 'pin', 'my-vedic-pin' => 'pin',
            'my-religious-pin' => 'pin', 'my-spiritual-pin' => 'pin',
            'my-devotional-pin' => 'pin', 'my-devotion-pin' => 'pin',
            'my-faith-pin' => 'pin', 'my-belief-pin' => 'pin',
            'my-culture-pin' => 'pin', 'my-tradition-pin' => 'pin',
            'my-heritage-pin' => 'collection', 'my-legacy-pin' => 'collection',
            'my-history-pin' => 'collection', 'my-ancient-pin' => 'collection',
            'my-classical-pin' => 'collection', 'my-traditional-pin' => 'collection',
            'my-folk-pin' => 'collection', 'my-indigenous-pin' => 'collection',
            'my-tribal-pin' => 'collection', 'my-rural-pin' => 'collection',
            'my-urban-pin' => 'collection', 'my-suburban-pin' => 'collection',
            'my-global-pin' => 'globe', 'my-international-pin' => 'globe',
            'my-national-pin' => 'globe', 'my-regional-pin' => 'globe',
            'my-local-pin' => 'location', 'my-metro-pin' => 'location',
            'my-city-pin' => 'location', 'my-town-pin' => 'location',
            'my-village-pin' => 'location', 'my-country-pin' => 'location',
            'my-state-pin' => 'location', 'my-province-pin' => 'location',
            'my-district-pin' => 'location', 'my-block-pin' => 'location',
            'my-ward-pin' => 'location', 'my-zone-pin' => 'location',
            'my-area-pin' => 'location', 'my-region-pin' => 'location',
            'my-earth-pin' => 'globe', 'my-world-pin' => 'globe',
            'my-planet-pin' => 'globe', 'my-solar-pin' => 'globe',
            'my-universe-pin' => 'globe', 'my-cosmic-pin' => 'globe',
            'my-stellar-pin' => 'star', 'my-galaxy-pin' => 'globe',
            'my-nebula-pin' => 'star', 'my-supernova-pin' => 'star',
            'my-pulsar-pin' => 'star', 'my-quasar-pin' => 'star',
            'my-blackhole-pin' => 'cancel', 'my-singularity-pin' => 'cancel',
            'my-event-horizon-pin' => 'cancel', 'my-horizon-pin' => 'cancel',
            'my-infinity-pin' => 'sparkles', 'my-eternal-pin' => 'sparkles',
            'my-divine-pin' => 'sparkles', 'my-sacred-pin' => 'sparkles',
            'my-holy-pin' => 'sparkles', 'my-blessed-pin' => 'sparkles',
            'my-cursed-pin' => 'cancel', 'my-evil-pin' => 'cancel',
            'my-dark-pin' => 'cancel', 'my-light-pin' => 'sparkles',
            'my-shadow-pin' => 'cancel', 'my-glow-pin' => 'sparkles',
            'my-shimmer-pin' => 'sparkles', 'my-glimmer-pin' => 'sparkles',
            'my-radiance-pin' => 'sparkles', 'my-luminance-pin' => 'sparkles',
            'my-brilliance-pin' => 'sparkles', 'my-splendor-pin' => 'sparkles',
            'my-majesty-pin' => 'sparkles', 'my-royalty-pin' => 'award',
            'my-nobility-pin' => 'award', 'my-aristocracy-pin' => 'award',
            'my-gentry-pin' => 'award', 'my-elite-pin' => 'award',
            'my-upper-pin' => 'award', 'my-middle-pin' => 'award',
            'my-lower-pin' => 'award', 'my-working-pin' => 'hand-heart',
            'my-labor-pin' => 'hand-heart', 'my-worker-pin' => 'hand-heart',
            'my-toiler-pin' => 'hand-heart', 'my-laborer-pin' => 'hand-heart',
            'my-driver-pin' => 'hand-heart', 'my-builder-pin' => 'hand-heart',
            'my-maker-pin' => 'plus', 'my-creator-pin' => 'plus',
            'my-designer-pin' => 'pen', 'my-artist-pin' => 'pen',
            'my-painter-pin' => 'pen', 'my-sculptor-pin' => 'pen',
            'my-carver-pin' => 'pen', 'my-cutter-pin' => 'pen',
            'my-chopper-pin' => 'pen', 'my-slicer-pin' => 'pen',
            'my-dicer-pin' => 'pen', 'my-trimmer-pin' => 'pen',
            'my-editer-pin' => 'edit', 'my-editor-pin' => 'edit',
            'my-reviser-pin' => 'edit', 'my-revisor-pin' => 'edit',
            'my-corrector-pin' => 'edit', 'my-fixer-pin' => 'edit',
            'my-repairer-pin' => 'edit', 'my-restorer-pin' => 'edit',
            'my-preserver-pin' => 'shield', 'my-protector-pin' => 'shield',
            'my-guardian-pin' => 'shield', 'my-defender-pin' => 'shield',
            'my-sentinel-pin' => 'shield', 'my-watchman-pin' => 'shield',
            'my-gard-pin' => 'shield', 'my-watch-pin' => 'eye',
            'my-look-pin' => 'eye', 'my-see-pin' => 'eye',
            'my-witness-pin' => 'eye', 'my-view-pin' => 'eye',
            'my-observer-pin' => 'eye', 'my-watcher-pin' => 'eye',
            'my-seer-pin' => 'eye', 'my-prophet-pin' => 'eye',
            'my-oracle-pin' => 'eye', 'my-visionary-pin' => 'eye',
            'my-dreamer-pin' => 'eye', 'my-imaginer-pin' => 'eye',
            'my-thinker-pin' => 'brain', 'my-thought-pin' => 'brain',
            'my-rational-pin' => 'brain', 'my-logical-pin' => 'brain',
            'my-reason-pin' => 'brain', 'my-logic-pin' => 'brain',
            'my-sense-pin' => 'brain', 'my-perception-pin' => 'eye',
            'my-awareness-pin' => 'eye', 'my-consciousness-pin' => 'brain',
            'my-mind-pin' => 'brain', 'my-brain-pin' => 'brain',
            'my-intellect-pin' => 'brain', 'my-wit-pin' => 'brain',
            'my-wisdom-pin' => 'star', 'my-sage-pin' => 'star',
            'my-scholar-pin' => 'book-open', 'my-student-pin' => 'book-open',
            'my-learner-pin' => 'book-open', 'my-pupil-pin' => 'book-open',
            'my-teacher-pin' => 'book-open', 'my-professor-pin' => 'book-open',
            'my-mentor-pin' => 'book-open', 'my-guide-pin' => 'book-open',
            'my-guru-pin' => 'star', 'my-master-pin' => 'star',
            'my-expert-pin' => 'star', 'my-specialist-pin' => 'star',
            'my-professional-pin' => 'star', 'my-trained-pin' => 'star',
            'my-skilled-pin' => 'star', 'my-talented-pin' => 'star',
            'my-gifted-pin' => 'star', 'my-talent-pin' => 'star',
            'my-gift-pin' => 'award', 'my-present-pin' => 'award',
            'my-offering-pin' => 'hand-heart', 'my-donation-pin' => 'hand-heart',
            'my-contribution-pin' => 'hand-heart', 'my-dedication-pin' => 'hand-heart',
            'my-service-pin' => 'hand-heart', 'my-help-pin' => 'hand-heart',
            'my-aid-pin' => 'hand-heart', 'my-assistance-pin' => 'hand-heart',
            'my-support-pin' => 'hand-heart', 'my-backing-pin' => 'hand-heart',
            'my-endorsement-pin' => 'hand-heart', 'my-sponsorship-pin' => 'hand-heart',
            'my-subsidy-pin' => 'hand-heart', 'my-subsidize-pin' => 'hand-heart',
            'my-fund-pin' => 'hand-heart', 'my-funding-pin' => 'hand-heart',
            'my-finance-pin' => 'hand-heart', 'my-money-pin' => 'hand-heart',
            'my-cash-pin' => 'hand-heart', 'my-coin-pin' => 'hand-heart',
            'my-currency-pin' => 'hand-heart', 'my-dollar-pin' => 'hand-heart',
            'my-pound-pin' => 'hand-heart', 'my-euro-pin' => 'hand-heart',
            'my-rupee-pin' => 'hand-heart', 'my-yen-pin' => 'hand-heart',
            'my-yuan-pin' => 'hand-heart', 'my-won-pin' => 'hand-heart',
            'my-price-pin' => 'hand-heart', 'my-cost-pin' => 'hand-heart',
            'my-value-pin' => 'hand-heart', 'my-worth-pin' => 'hand-heart',
            'my-wealth-pin' => 'hand-heart', 'my-rich-pin' => 'hand-heart',
            'my-richness-pin' => 'hand-heart', 'my-affluence-pin' => 'hand-heart',
            'my-prosperity-pin' => 'hand-heart', 'my-boom-pin' => 'hand-heart',
            'my-growth-pin' => 'hand-heart', 'my-rise-pin' => 'hand-heart',
            'my-progress-pin' => 'hand-heart', 'my-advance-pin' => 'hand-heart',
            'my-development-pin' => 'hand-heart', 'my-expansion-pin' => 'hand-heart',
            'my-extension-pin' => 'hand-heart', 'my-spread-pin' => 'hand-heart',
            'my-reach-pin' => 'hand-heart', 'my-extent-pin' => 'hand-heart',
            'my-range-pin' => 'hand-heart', 'my-span-pin' => 'hand-heart',
            'my-reach-out-pin' => 'hand-heart', 'my-reach-in-pin' => 'hand-heart',
            'my-touch-pin' => 'hand-heart', 'my-contact-pin' => 'hand-heart',
            'my-connect-pin' => 'hub', 'my-link-pin' => 'link',
            'my-network-pin' => 'hub', 'my-web-pin' => 'hub',
            'my-grid-pin' => 'picture', 'my-mesh-pin' => 'picture',
            'my-pattern-pin' => 'picture', 'my-design-pin' => 'picture',
            'my-style-pin' => 'picture', 'my-theme-pin' => 'picture',
            'my-color-pin' => 'picture', 'my-hue-pin' => 'picture',
            'my-tone-pin' => 'picture', 'my-shade-pin' => 'picture',
            'my-tint-pin' => 'picture', 'my-palette-pin' => 'picture',
            'my-color-wheel-pin' => 'picture', 'my-swatch-pin' => 'picture',
            'my-picker-pin' => 'picture', 'my-chooser-pin' => 'picture',
            'my-select-pin' => 'picture', 'my-pick-pin' => 'picture',
            'my-choose-pin' => 'picture', 'my-opt-pin' => 'picture',
            'my-option-pin' => 'picture', 'my-alternative-pin' => 'picture',
            'my-alternate-pin' => 'picture', 'my-substitute-pin' => 'picture',
            'my-replacement-pin' => 'picture', 'my-swap-pin' => 'refresh',
            'my-trade-pin' => 'refresh', 'my-barter-pin' => 'refresh',
            'my-buy-pin' => 'download', 'my-sell-pin' => 'upload',
            'my-purchase-pin' => 'download', 'my-order-pin' => 'document',
            'my-cart-pin' => 'document', 'my-basket-pin' => 'document',
            'my-bag-pin' => 'document', 'my-trolley-pin' => 'document',
            'my-shopping-pin' => 'document', 'my-store-pin' => 'document',
            'my-shop-pin' => 'document', 'my-market-pin' => 'document',
            'my-bazaar-pin' => 'document', 'my-mart-pin' => 'document',
            'my-depot-pin' => 'document', 'my-warehouse-pin' => 'document',
            'my-inventory-pin' => 'document', 'my-stock-pin' => 'document',
            'my-supply-pin' => 'document', 'my-demand-pin' => 'document',
            'my-resource-pin' => 'document', 'my-asset-pin' => 'document',
            'my-capital-pin' => 'document', 'my-investment-pin' => 'document',
            'my-invest-pin' => 'document', 'my-share-pin' => 'forward',
            'my-split-pin' => 'minus', 'my-merge-pin' => 'plus',
            'my-combine-pin' => 'plus', 'my-join-pin' => 'plus',
            'my-unite-pin' => 'plus', 'my-union-pin' => 'plus',
            'my-intersect-pin' => 'plus', 'my-overlap-pin' => 'plus',
            'my-overlay-pin' => 'picture', 'my-underlay-pin' => 'picture',
            'my-background-pin' => 'picture', 'my-foreground-pin' => 'picture',
            'my-front-pin' => 'picture', 'my-back-pin' => 'picture',
            'my-top-pin' => 'picture', 'my-bottom-pin' => 'picture',
            'my-center-pin' => 'picture', 'my-middle-pin' => 'picture',
            'my-mid-pin' => 'picture', 'my-core-pin' => 'picture',
            'my-heart-pin' => 'favorite', 'my-dharma-pin' => 'pin',
            'my-sampreshan-pin' => 'pin', 'my-shivbodh-pin' => 'pin',
            'my-trust-pin' => 'pin', 'my-community-pin' => 'pin',
            'my-social-pin' => 'pin', 'my-awareness-pin' => 'pin',
            'my-cause-pin' => 'pin', 'my-petition-pin' => 'pin',
            'my-change-pin' => 'pin', 'my-voice-pin' => 'pin',
            'my-truth-pin' => 'pin', 'my-social-fusion-pin' => 'pin',
            'my-sanatan-pin' => 'pin', 'my-vedic-pin' => 'pin',
            'my-religious-pin' => 'pin', 'my-spiritual-pin' => 'pin',
            'my-devotional-pin' => 'pin', 'my-devotion-pin' => 'pin',
            'my-faith-pin' => 'pin', 'my-belief-pin' => 'pin',
            'my-culture-pin' => 'pin', 'my-tradition-pin' => 'pin',
        );
    }
}

/**
 * Responsive Icons8 <img> with srcset/sizes.
 * Desktop gets 192w, tablet 96w, mobile 48w (browser picks by DPR).
 *
 * @param string $name  Our icon name (must exist in sp_icons8_map()).
 * @param string $class Wrapper class list.
 * @param string $label Aria-label override.
 * @return string Markup (empty string when files are missing).
 */
if ( ! function_exists( 'sp_icon_img' ) ) {
    function sp_icon_img( $name, $class = 'sp-icon sp-icon--md', $label = '' ) {
        $name  = sanitize_key( $name );
        $map   = sp_icons8_map();
        if ( ! isset( $map[ $name ] ) ) { return ''; }
        $slug = $map[ $name ];
        // Primary set first, Pin-style set as permanent fallback.
        $sets = array( 'icons8', 'icons8-pin' );
        $dir = '';
        $uri = '';
        foreach ( $sets as $set ) {
            $d = get_stylesheet_directory() . '/assets/icons/' . $set . '/';
            $ok = true;
            foreach ( array( 48, 96, 192 ) as $s ) {
                if ( ! file_exists( $d . $slug . '-' . $s . '.png' ) ) { $ok = false; break; }
            }
            if ( $ok ) {
                $dir = $d;
                $uri = get_stylesheet_directory_uri() . '/assets/icons/' . $set . '/';
                break;
            }
        }
        if ( '' === $dir ) { return ''; }
        $srcset = esc_url( $uri . $slug . '-48.png' ) . ' 48w, '
                . esc_url( $uri . $slug . '-96.png' ) . ' 96w, '
                . esc_url( $uri . $slug . '-192.png' ) . ' 192w';
        return '<span class="sp-icon sp-icon--img ' . esc_attr( $class ) . '" data-icon-img="' . esc_attr( $name ) . '">'
            . '<img src="' . esc_url( $uri . $slug . '-96.png' ) . '" srcset="' . $srcset . '"'
            . ' sizes="(max-width: 767px) 32px, 64px"'
            . ' alt="' . esc_attr( $label ) . '" loading="lazy" decoding="async" />'
            . '</span>';
    }
}

if ( ! function_exists( 'sp_icon_img_e' ) ) {
    function sp_icon_img_e( $name, $class = 'sp-icon sp-icon--md', $label = '' ) {
        echo sp_icon_img( $name, $class, $label ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}

/**
 * Auto icon: Icons8 PNG when imported, else our custom SVG.
 * Same signature as sp_icon_e() — drop-in upgrade path.
 */
if ( ! function_exists( 'sp_icon_auto' ) ) {
    function sp_icon_auto( $name, $class = 'sp-icon sp-icon--md', $label = '' ) {
        $img = sp_icon_img( $name, $class, $label );
        if ( '' !== $img ) {
            echo $img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            return;
        }
        sp_icon_e( $name, $class, $label );
    }
}

/**
 * Cause → icon map (single source of truth).
 *
 * RULE: one topic = one symbol, everywhere. The same icon repeats ONLY
 * when the same cause/topic is published again (another card, page hero,
 * badge, or column) — never for a different topic.
 *
 * Custom premium 3D transparent SVGs (assets/icons/3d/):
 *   mandir     → Temple Preservation
 *   pothi      → Cultural Heritage (prachin pothi manuscript)
 *   vedagranth → Religious Education + Sanskrit & Vedic Studies (same learning topic)
 *   peepal     → Environmental Causes
 *   seva       → Community Welfare (helping hands + flame)
 *   gaumata    → Gau Seva
 *   tirtha     → Pilgrimage & Tirtha (ghat at sunrise)
 *   shankh     → Raise-your-voice actions (shankhnaad)
 *   mobile     → Mobile / OTP verification
 *
 * @param string $slug_or_name Cause slug, term name, or page slug.
 * @return string Icon name for sp_icon_e() / sp_icon_auto().
 */
if ( ! function_exists( 'sp_cause_icon' ) ) {
    function sp_cause_icon( $slug_or_name ) {
        $s = strtolower( (string) $slug_or_name );
        $s = preg_replace( '/[^a-z]/', '', $s );

        if ( false !== strpos( $s, 'temple' ) || false !== strpos( $s, 'mandir' ) ) {
            return 'mandir';
        }
        if ( false !== strpos( $s, 'cultur' ) || false !== strpos( $s, 'heritage' ) || false !== strpos( $s, 'virasat' ) || false !== strpos( $s, 'pothi' ) || false !== strpos( $s, 'manuscript' ) ) {
            return 'pothi';
        }
        if ( false !== strpos( $s, 'educat' ) || false !== strpos( $s, 'sanskrit' ) || false !== strpos( $s, 'vedic' ) || false !== strpos( $s, 'vidya' ) || false !== strpos( $s, 'grantha' ) ) {
            return 'vedagranth';
        }
        if ( false !== strpos( $s, 'welfar' ) || false !== strpos( $s, 'seva' ) || false !== strpos( $s, 'community' ) || false !== strpos( $s, 'samaj' ) ) {
            return 'seva';
        }
        if ( false !== strpos( $s, 'environ' ) || false !== strpos( $s, 'nature' ) || false !== strpos( $s, 'green' ) || false !== strpos( $s, 'peepal' ) || false !== strpos( $s, 'tree' ) || false !== strpos( $s, 'van' ) ) {
            return 'peepal';
        }
        if ( false !== strpos( $s, 'gau' ) || false !== strpos( $s, 'cow' ) || false !== strpos( $s, 'dhenu' ) ) {
            return 'gaumata';
        }
        if ( false !== strpos( $s, 'pilgrim' ) || false !== strpos( $s, 'tirth' ) || false !== strpos( $s, 'teerth' ) || false !== strpos( $s, 'yatra' ) || false !== strpos( $s, 'dham' ) ) {
            return 'tirtha';
        }
        return 'petition';
    }
}

/**
 * Cause icon (echo variant).
 */
if ( ! function_exists( 'sp_cause_icon_e' ) ) {
    function sp_cause_icon_e( $slug_or_name, $class = 'sp-icon sp-icon--md', $label = '' ) {
        sp_icon_e( sp_cause_icon( $slug_or_name ), $class, $label );
    }
}

/**
 * SampreShan Logo URL — single source of truth.
 *
 * Priority:
 *   1. WordPress Customizer `custom_logo` (if the admin ever sets it).
 *   2. The original uploaded Sampreshan logo (Media Library, June 2026).
 *   3. Bundled brand fallback (favicon.svg).
 *
 * The "original" logo the site has always used lives at:
 *   /wp-content/uploads/2026/06/sampreshan-logo-svg.svg
 * Keep that path as the canonical default so header / footer / login /
 * dashboard / homepage all render the SAME mark.
 *
 * @return string Escaped-safe URL (not escaped — escape at output).
 */
if ( ! function_exists( 'sp_logo_url' ) ) {
    function sp_logo_url() {
        // 1. Customizer logo wins if set.
        $custom_id = function_exists( 'get_theme_mod' ) ? (int) get_theme_mod( 'custom_logo' ) : 0;
        if ( $custom_id > 0 ) {
            $src = wp_get_attachment_image_src( $custom_id, 'full' );
            if ( $src && ! empty( $src[0] ) ) {
                return $src[0];
            }
        }

        // 2. Original uploaded logo — the one already set on the live site.
        return content_url( 'uploads/2026/06/sampreshan-logo-svg.svg' );
    }
}

/**
 * Echo an <img> tag for the Sampreshan logo.
 *
 * @param string $class Extra CSS class(es).
 * @param int    $size  Width/height in px.
 */
if ( ! function_exists( 'sp_logo_e' ) ) {
    function sp_logo_e( $class = 'site-header__logo', $size = 40 ) {
        $url  = sp_logo_url();
        $name = function_exists( 'get_bloginfo' ) ? get_bloginfo( 'name' ) : 'SampreShan';
        printf(
            '<img class="%s" src="%s" alt="%s" width="%d" height="%d" fetchpriority="high" />',
            esc_attr( $class ),
            esc_url( $url ),
            esc_attr( $name ),
            (int) $size,
            (int) $size
        );
    }
}

/**
 * Messenger pigeon — 3D SVG dove with flapping wings.
 *
 * Sampreshan's "kabootar post": the bird carries the community's voice.
 * Pure inline SVG (zero HTTP requests); flight + wing-flap motion comes
 * from CSS classes in homepage.css (.sp-pigeon, .sp-pigeon__wing...).
 *
 * @param string $suffix Unique per instance on a page (gradient IDs).
 * @param bool   $mail   Hang a saffron letter from the beak.
 */
if ( ! function_exists( 'sp_pigeon' ) ) {
    function sp_pigeon( $suffix = 'a', $mail = true ) {
        $s = preg_replace( '/[^a-z0-9]/', '', strtolower( (string) $suffix ) );
        if ( '' === $s ) { $s = 'a'; }
        ob_start();
        ?>
        <svg class="sp-pigeon__svg" viewBox="0 0 220 150" aria-hidden="true" focusable="false">
            <defs>
                <linearGradient id="pg-body-<?php echo esc_attr( $s ); ?>" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0" stop-color="#FFFFFF" />
                    <stop offset="0.55" stop-color="#E9EDF3" />
                    <stop offset="1" stop-color="#C6CEDA" />
                </linearGradient>
                <linearGradient id="pg-wing-<?php echo esc_attr( $s ); ?>" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0" stop-color="#FFFFFF" />
                    <stop offset="1" stop-color="#BCC6D4" />
                </linearGradient>
                <linearGradient id="pg-wingback-<?php echo esc_attr( $s ); ?>" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0" stop-color="#D3D9E3" />
                    <stop offset="1" stop-color="#A3AEC0" />
                </linearGradient>
                <linearGradient id="pg-env-<?php echo esc_attr( $s ); ?>" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0" stop-color="#FFC46B" />
                    <stop offset="1" stop-color="#E0851A" />
                </linearGradient>
            </defs>
            <!-- tail feathers -->
            <g fill="url(#pg-wingback-<?php echo esc_attr( $s ); ?>)" stroke="#8E99A8" stroke-width="1" stroke-opacity="0.55" stroke-linejoin="round">
                <path d="M66,84 L20,64 L34,86 Z" />
                <path d="M66,90 L14,88 L34,96 Z" />
                <path d="M66,96 L24,114 L40,100 Z" />
            </g>
            <!-- far wing (darker, opposite phase) -->
            <g class="sp-pigeon__wing sp-pigeon__wing--back">
                <path d="M96,74 C90,60 80,44 62,36 C60,35 58,36 59,38 C72,56 82,68 90,78 C92,81 97,79 96,74 Z" fill="url(#pg-wingback-<?php echo esc_attr( $s ); ?>)" stroke="#8E99A8" stroke-width="1" stroke-opacity="0.5" />
                <path d="M90,64 C82,56 74,49 66,44" fill="none" stroke="#8E99A8" stroke-width="1.2" stroke-opacity="0.55" stroke-linecap="round" />
            </g>
            <!-- body -->
            <path d="M30,100 C55,88 70,78 92,74 C100,72 108,66 116,60 C124,52 136,50 144,56 C150,60 150,68 144,72 C138,78 140,84 136,90 C120,104 80,110 52,106 C40,104 32,102 30,100 Z" fill="url(#pg-body-<?php echo esc_attr( $s ); ?>)" />
            <ellipse cx="100" cy="94" rx="34" ry="10" fill="#FFFFFF" opacity="0.65" />
            <path d="M60,82 C85,70 115,68 138,74" fill="none" stroke="#AEB8C6" stroke-width="4" stroke-opacity="0.45" stroke-linecap="round" />
            <!-- head details -->
            <circle cx="140" cy="62" r="3" fill="#26303B" />
            <circle cx="141" cy="61" r="1" fill="#FFFFFF" />
            <path d="M152,62 L168,66 L151,71 Z" fill="#E0851A" />
            <circle cx="152" cy="63" r="2.5" fill="#F5F7FA" />
            <!-- near wing (big, leading) -->
            <g class="sp-pigeon__wing sp-pigeon__wing--front">
                <path d="M104,72 C96,52 80,30 52,18 C50,17 48,18 49,20 C66,44 82,62 94,78 C97,82 103,78 104,72 Z" fill="url(#pg-wing-<?php echo esc_attr( $s ); ?>)" stroke="#8E99A8" stroke-width="1" stroke-opacity="0.5" />
                <path d="M92,60 C80,48 68,38 56,32" fill="none" stroke="#9AA5B5" stroke-width="1.5" stroke-opacity="0.6" stroke-linecap="round" />
                <path d="M98,66 C88,56 78,48 68,42" fill="none" stroke="#9AA5B5" stroke-width="1.2" stroke-opacity="0.5" stroke-linecap="round" />
            </g>
            <?php if ( $mail ) : ?>
            <!-- letter on a string -->
            <g class="sp-pigeon__mail">
                <path d="M160,70 C166,82 172,92 178,100" fill="none" stroke="#8A6D3B" stroke-width="1.5" />
                <rect x="160" y="100" width="36" height="25" rx="4" fill="url(#pg-env-<?php echo esc_attr( $s ); ?>)" stroke="#B45309" stroke-width="1" />
                <path d="M160,104 L178,116 L196,104" fill="none" stroke="#B45309" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                <circle cx="178" cy="113" r="3" fill="#9A3412" />
                <circle cx="177" cy="112" r="1" fill="#FFD9A8" />
            </g>
            <?php endif; ?>
        </svg>
        <?php
        echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}

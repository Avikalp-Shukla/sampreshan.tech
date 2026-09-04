<?php
/**
 * Petitions module bootstrap.
 *
 * Loads the CPT, admin UI, signature model, REST API, and notification
 * pieces. All public APIs are documented on each file; this loader just
 * requires the parts in the right order.
 *
 * @package SampreShan_Child
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

require_once __DIR__ . '/cpt.php';            // CPT + taxonomy + role
require_once __DIR__ . '/admin.php';          // meta boxes + admin columns
require_once __DIR__ . '/signature.php';      // signature store + AJAX
require_once __DIR__ . '/moderation.php';     // victory declarations + reports
require_once __DIR__ . '/notifications.php';  // email + WP-Cron
require_once __DIR__ . '/rest.php';           // REST endpoints

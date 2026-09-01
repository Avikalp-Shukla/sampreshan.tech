<?php
/**
 * SSO fields.
 *
 * @since   2.6.30
 * @package BuddyBossPro/SSO
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$google_fields = array(
	'label'  => __( 'Google', 'buddyboss-pro' ),
	'fields' => array(
		'web' => array(
			'label'      => __( 'Web', 'buddyboss-pro' ),
			'sub_fields' => array(
				array(
					'id'          => 'client_id',
					'name'        => __( 'Client ID', 'buddyboss-pro' ),
					'type'        => 'text',
					'value'       => BB_SSO::get_provider_setting( 'google', 'client_id' ),
					'placeholder' => __( 'Enter client ID', 'buddyboss-pro' ),
				),
				array(
					'id'          => 'client_secret',
					'name'        => __( 'Client Secret', 'buddyboss-pro' ),
					'type'        => 'text',
					'value'       => BB_SSO::get_provider_setting( 'google', 'client_secret' ),
					'placeholder' => __( 'Enter client secret', 'buddyboss-pro' ),
				),
				array(
					'id'       => '',
					'name'     => __( 'Callback URI / Redirect URL', 'buddyboss-pro' ),
					'type'     => 'text',
					'value'    => BB_SSO::$allowed_providers['google']->get_redirect_uri_for_auth_flow(),
					'disabled' => true,
				),
				array(
					'id'          => 'information',
					'name'        => '',
					'type'        => 'information',
					'description' => sprintf(
						__( '<a href="%1$s" target="_blank">%2$s</a> to create an app, or explore <a href="%3$s" target="_blank">%4$s</a> to learn the process.', 'buddyboss-pro' ),
						'https://console.developers.google.com/apis',
						__( 'Click here', 'buddyboss-pro' ),
						esc_url(
							bp_get_admin_url(
								add_query_arg(
									array(
										'page'    => 'bp-help',
										'article' => 127918,
									),
									'admin.php'
								)
							)
						),
						__( 'the tutorial', 'buddyboss-pro' )
					),
				),
			),
		),
	),
);

if ( ! function_exists( 'bbapp' ) ) {
	unset( $google_fields['fields']['web']['label'] );
}

// Conditionally add Android and iOS fields if bbapp function exists.
if ( function_exists( 'bbapp' ) ) {
	$google_fields['fields']['android'] = array(
		'label'      => __( 'Android App', 'buddyboss-pro' ),
		'sub_fields' => array(
			array(
				'id'    => 'app_android_client_id',
				'name'  => sprintf(
					__( 'Client ID', 'buddyboss-pro' ) . ' %s',
					sprintf(
						'<span style="display:inline;" data-bp-tooltip-pos="up" data-bp-tooltip="%s"><i style="opacity: 0.9" class="bb-icon-rf bb-icon-info"></i></span>',
						__( 'For Release App', 'buddyboss-pro' )
					)
				),
				'type'        => 'text',
				'value'       => BB_SSO::get_provider_setting( 'google', 'app_android_client_id' ),
				'placeholder' => __( 'Enter client ID', 'buddyboss-pro' ),
			),
			array(
				'id'    => 'app_android_test_client_id',
				'name'  => sprintf(
					__( 'Test Build Client ID', 'buddyboss-pro' ) . ' %s',
					sprintf(
						'<span style="display:inline;" data-bp-tooltip-pos="up" data-bp-tooltip="%s"><i style="opacity: 0.9" class="bb-icon-rf bb-icon-info"></i></span>',
						__( 'For Test App', 'buddyboss-pro' )
					)
				),
				'type'        => 'text',
				'value'       => BB_SSO::get_provider_setting( 'google', 'app_android_test_client_id' ),
				'placeholder' => __( 'Enter test build client ID', 'buddyboss-pro' ),
			),
			array(
				'id'          => 'information',
				'name'        => '',
				'type'        => 'information',
				'description' => sprintf(
					__( '<a href="%1$s" target="_blank">%2$s</a> to create an app, or explore <a href="%3$s" target="_blank">%4$s</a> to learn the process.', 'buddyboss-pro' ),
					'https://console.developers.google.com/apis',
					__( 'Click here', 'buddyboss-pro' ),
					esc_url(
						bp_get_admin_url(
							add_query_arg(
								array(
									'page'    => 'bp-help',
									'article' => 127918,
								),
								'admin.php'
							)
						)
					),
					__( 'the tutorial', 'buddyboss-pro' )
				),
			),
		),
	);

	$google_fields['fields']['ios'] = array(
		'label'      => __( 'iOS App', 'buddyboss-pro' ),
		'sub_fields' => array(
			array(
				'id'    => 'app_ios_client_id',
				'name'  => sprintf(
					__( 'Client ID', 'buddyboss-pro' ) . ' %s',
					sprintf(
						'<span style="display:inline;" data-bp-tooltip-pos="up" data-bp-tooltip="%s"><i style="opacity: 0.9" class="bb-icon-rf bb-icon-info"></i></span>',
						__( 'For Release App', 'buddyboss-pro' )
					)
				),
				'type'        => 'text',
				'value'       => BB_SSO::get_provider_setting( 'google', 'app_ios_client_id' ),
				'placeholder' => __( 'Enter client ID', 'buddyboss-pro' ),
			),
			array(
				'id'    => 'app_ios_test_client_id',
				'name'  => sprintf(
					__( 'Test Build Client ID', 'buddyboss-pro' ) . ' %s',
					sprintf(
						'<span style="display:inline;" data-bp-tooltip-pos="up" data-bp-tooltip="%s"><i style="opacity: 0.9" class="bb-icon-rf bb-icon-info"></i></span>',
						__( 'For Test App', 'buddyboss-pro' )
					)
				),
				'type'        => 'text',
				'value'       => BB_SSO::get_provider_setting( 'google', 'app_ios_test_client_id' ),
				'placeholder' => __( 'Enter test build client ID', 'buddyboss-pro' ),
			),
			array(
				'id'          => 'information',
				'name'        => '',
				'type'        => 'information',
				'description' => sprintf(
					__( '<a href="%1$s" target="_blank">%2$s</a> to create an app, or explore <a href="%3$s" target="_blank">%4$s</a> to learn the process.', 'buddyboss-pro' ),
					'https://console.developers.google.com/apis',
					__( 'Click here', 'buddyboss-pro' ),
					esc_url(
						bp_get_admin_url(
							add_query_arg(
								array(
									'page'    => 'bp-help',
									'article' => 127918,
								),
								'admin.php'
							)
						)
					),
					__( 'the tutorial', 'buddyboss-pro' )
				),
			),
		),
	);
}

$microsoft_fields = array(
	'label'  => __( 'Microsoft', 'buddyboss-pro' ),
	'fields' => array(
		'web' => array(
			'sub_fields' => array(
				array(
					'id'          => 'client_id',
					'name'        => __( 'Application Client ID', 'buddyboss-pro' ),
					'type'        => 'text',
					'value'       => BB_SSO::get_provider_setting( 'microsoft', 'client_id' ),
					'placeholder' => __( 'Enter application client ID', 'buddyboss-pro' ),
				),
				array(
					'id'          => 'client_secret',
					'name'        => __( 'Client Secret', 'buddyboss-pro' ),
					'type'        => 'text',
					'value'       => BB_SSO::get_provider_setting( 'microsoft', 'client_secret' ),
					'placeholder' => __( 'Enter client secret', 'buddyboss-pro' ),
				),
				array(
					'id'      => 'tenant',
					'name'    => __( 'Audience', 'buddyboss-pro' ),
					'type'    => 'radio',
					'value'   => BB_SSO::get_provider_setting( 'microsoft', 'tenant' ),
					'options' => array(
						'organizations' => __( 'Accounts in any organizational directory (Any Azure AD directory - Multitenant)', 'buddyboss-pro' ),
						'common'        => __( 'Accounts in any organizational directory (Any Azure AD directory - Multitenant) and personal Microsoft accounts (e.g. Skype, Xbox)', 'buddyboss-pro' ),
						'consumers'     => __( 'Personal Microsoft accounts only', 'buddyboss-pro' ),
						'custom_tenant' => __( 'Only users in an organizational directory from a particular Azure AD tenant:', 'buddyboss-pro' ) . '<input type="text" id="custom_tenant_value" name="custom_tenant_value" placeholder="' . esc_attr__( 'Enter a value', 'buddyboss-pro' ) . '" value="' . BB_SSO::get_provider_setting( 'microsoft', 'custom_tenant_value' ) . '" data-old-value="' . BB_SSO::get_provider_setting( 'microsoft', 'custom_tenant_value' ) . '" />',
					),
				),
				array(
					'id'      => 'prompt',
					'name'    => __( 'Authorization Prompt', 'buddyboss-pro' ),
					'type'    => 'radio',
					'value'   => BB_SSO::get_provider_setting( 'microsoft', 'prompt' ),
					'options' => array(
						'select_account' => __( 'Display account select modal', 'buddyboss-pro' ),
						'login'          => __( 'Force user to enter login credentials on each login', 'buddyboss-pro' ),
						''               => __( 'Display authorization and authentication dialog only when necessary', 'buddyboss-pro' ),
					),
				),
				array(
					'id'       => '',
					'name'     => __( 'Callback URI / Redirect URL', 'buddyboss-pro' ),
					'type'     => 'text',
					'value'    => BB_SSO::$allowed_providers['microsoft']->get_redirect_uri_for_auth_flow(),
					'disabled' => true,
				),
				array(
					'id'          => 'information',
					'name'        => '',
					'type'        => 'information',
					'description' => sprintf(
						__( '<a href="%1$s" target="_blank">%2$s</a> to create an app, or explore <a href="%3$s" target="_blank">%4$s</a> to learn the process.', 'buddyboss-pro' ),
						'https://portal.azure.com/#blade/Microsoft_AAD_RegisteredApps/ApplicationsListBlade',
						__( 'Click here', 'buddyboss-pro' ),
						esc_url(
							bp_get_admin_url(
								add_query_arg(
									array(
										'page'    => 'bp-help',
										'article' => 127919,
									),
									'admin.php'
								)
							)
						),
						__( 'the tutorial', 'buddyboss-pro' )
					),
				),
			),
		),
	),
);

return array(
	'facebook'  => array(
		'label'  => __( 'Facebook', 'buddyboss-pro' ),
		'fields' => array(
			'web' => array(
				'label'      => '',
				'sub_fields' => array(
					array(
						'id'          => 'appid',
						'name'        => __( 'App ID', 'buddyboss-pro' ),
						'type'        => 'text',
						'value'       => BB_SSO::get_provider_setting( 'facebook', 'appid' ),
						'placeholder' => __( 'Enter app ID', 'buddyboss-pro' ),
					),
					array(
						'id'          => 'secret',
						'name'        => __( 'App Secret', 'buddyboss-pro' ),
						'type'        => 'text',
						'value'       => BB_SSO::get_provider_setting( 'facebook', 'secret' ),
						'placeholder' => __( 'Enter app secret', 'buddyboss-pro' ),
					),
					array(
						'id'       => '',
						'name'     => __( 'Callback URI / Redirect URL', 'buddyboss-pro' ),
						'type'     => 'text',
						'value'    => BB_SSO::$allowed_providers['facebook']->get_redirect_uri_for_auth_flow(),
						'disabled' => true,
					),
					array(
						'id'          => 'information',
						'name'        => '',
						'type'        => 'information',
						'description' => sprintf(
						/* translators: %1$s: URL, %2$s: Click here, %3$s: URL, %4$s: the tutorial */
							__( '<a href="%1$s" target="_blank">%2$s</a> to create an app, or explore <a href="%3$s" target="_blank">%4$s</a> to learn the process.', 'buddyboss-pro' ),
							'https://developers.facebook.com/apps',
							__( 'Click here', 'buddyboss-pro' ),
							esc_url(
								bp_get_admin_url(
									add_query_arg(
										array(
											'page'    => 'bp-help',
											'article' => 127920,
										),
										'admin.php'
									)
								)
							),
							__( 'the tutorial', 'buddyboss-pro' )
						),
					),
				),
			),
		),
	),
	'google'    => $google_fields,
	'microsoft' => $microsoft_fields,
	'twitter'   => array(
		'label'  => __( 'X', 'buddyboss-pro' ),
		'fields' => array(
			'web' => array(
				'label'      => '',
				'sub_fields' => array(
					array(
						'id'          => 'client_id',
						'name'        => __( 'Client ID (V2)', 'buddyboss-pro' ),
						'type'        => 'text',
						'value'       => BB_SSO::get_provider_setting( 'twitter', 'client_id' ),
						'placeholder' => __( 'Enter client ID', 'buddyboss-pro' ),
						'version'     => '2',
						'class'       => 'twitter-v2-specific-field',
					),
					array(
						'id'          => 'client_secret',
						'name'        => __( 'Client Secret (V2)', 'buddyboss-pro' ),
						'type'        => 'text',
						'value'       => BB_SSO::get_provider_setting( 'twitter', 'client_secret' ),
						'placeholder' => __( 'Enter client secret', 'buddyboss-pro' ),
						'version'     => '2',
						'class'       => 'twitter-v2-specific-field',
					),
					array(
						'id'       => '',
						'name'     => __( 'Callback URI / Redirect URL', 'buddyboss-pro' ),
						'type'     => 'text',
						'value'    => BB_SSO::$allowed_providers['twitter']->get_redirect_uri_for_auth_flow(),
						'disabled' => true,
					),
					array(
						'id'          => 'information',
						'name'        => '',
						'type'        => 'information',
						'description' => sprintf(
						/* translators: %1$s: URL, %2$s: Click here, %3$s: URL, %4$s: the tutorial */
							__( '<a href="%1$s" target="_blank">%2$s</a> to create an app, or explore <a href="%3$s" target="_blank">%4$s</a> to learn the process.', 'buddyboss-pro' ),
							'https://developer.twitter.com/en/portal/projects-and-apps',
							__( 'Click here', 'buddyboss-pro' ),
							esc_url(
								bp_get_admin_url(
									add_query_arg(
										array(
											'page'    => 'bp-help',
											'article' => 127921,
										),
										'admin.php'
									)
								)
							),
							__( 'the tutorial', 'buddyboss-pro' )
						),
					),
				),
			),
		),
	),
	'linkedin'  => array(
		'label'  => __( 'LinkedIn', 'buddyboss-pro' ),
		'fields' => array(
			'web' => array(
				'label'      => '',
				'sub_fields' => array(
					array(
						'id'          => 'client_id',
						'name'        => __( 'Client ID', 'buddyboss-pro' ),
						'type'        => 'text',
						'value'       => BB_SSO::get_provider_setting( 'linkedin', 'client_id' ),
						'placeholder' => __( 'Enter client ID', 'buddyboss-pro' ),
					),
					array(
						'id'          => 'client_secret',
						'name'        => __( 'Client Secret', 'buddyboss-pro' ),
						'type'        => 'text',
						'value'       => BB_SSO::get_provider_setting( 'linkedin', 'client_secret' ),
						'placeholder' => __( 'Enter client secret', 'buddyboss-pro' ),
					),
					array(
						'id'       => '',
						'name'     => __( 'Callback URI / Redirect URL', 'buddyboss-pro' ),
						'type'     => 'text',
						'value'    => BB_SSO::$allowed_providers['linkedin']->get_redirect_uri_for_auth_flow(),
						'disabled' => true,
					),
					array(
						'id'          => 'information',
						'name'        => '',
						'type'        => 'information',
						'description' => sprintf(
							/* translators: %1$s: URL, %2$s: Click here, %3$s: URL, %4$s: the tutorial. */
							__( '<a href="%1$s" target="_blank">%2$s</a> to create an app, or explore <a href="%3$s" target="_blank">%4$s</a> to learn the process.', 'buddyboss-pro' ),
							'https://www.linkedin.com/developer/apps',
							__( 'Click here', 'buddyboss-pro' ),
							esc_url(
								bp_get_admin_url(
									add_query_arg(
										array(
											'page'    => 'bp-help',
											'article' => 127925,
										),
										'admin.php'
									)
								)
							),
							__( 'the tutorial', 'buddyboss-pro' )
						),
					),
				),
			),
		),
	),
	'apple'     => array(
		'label'  => __( 'Apple', 'buddyboss-pro' ),
		'fields' => array(
			'web' => array(
				'label'      => '',
				'sub_fields' => array(
					array(
						'id'          => 'private_key_id',
						'name'        => __( 'Private Key ID', 'buddyboss-pro' ),
						'type'        => 'text',
						'value'       => BB_SSO::get_provider_setting( 'apple', 'private_key_id' ),
						'placeholder' => __( 'Enter private key ID', 'buddyboss-pro' ),
					),
					array(
						'id'          => 'private_key',
						'name'        => __( 'Private Key', 'buddyboss-pro' ),
						'type'        => 'textarea',
						'value'       => BB_SSO::get_provider_setting( 'apple', 'private_key' ),
						'placeholder' => __( 'Enter private key', 'buddyboss-pro' ),
					),
					array(
						'id'          => 'team_identifier',
						'name'        => __( 'Team Identifier', 'buddyboss-pro' ),
						'type'        => 'text',
						'value'       => BB_SSO::get_provider_setting( 'apple', 'team_identifier' ),
						'placeholder' => __( 'Enter team identifier', 'buddyboss-pro' ),
					),
					array(
						'id'          => 'service_identifier',
						'name'        => __( 'Service Identifier', 'buddyboss-pro' ),
						'type'        => 'text',
						'value'       => BB_SSO::get_provider_setting( 'apple', 'service_identifier' ),
						'placeholder' => __( 'Enter service identifier', 'buddyboss-pro' ),
					),
					array(
						'id'       => '',
						'name'     => __( 'Callback URI / Redirect URL', 'buddyboss-pro' ),
						'type'     => 'text',
						'value'    => BB_SSO::$allowed_providers['apple']->get_redirect_uri_for_auth_flow(),
						'disabled' => true,
					),
					array(
						'id'          => 'information',
						'name'        => '',
						'type'        => 'information',
						'description' => sprintf(
							/* translators: %1$s: URL, %2$s: Click here, %3$s: URL, %4$s: the tutorial. */
							__( '<a href="%1$s" target="_blank">%2$s</a> to create an app, or explore <a href="%3$s" target="_blank">%4$s</a> to learn the process.', 'buddyboss-pro' ),
							'https://developer.apple.com/account/resources/identifiers/list',
							__( 'Click here', 'buddyboss-pro' ),
							esc_url(
								bp_get_admin_url(
									add_query_arg(
										array(
											'page'    => 'bp-help',
											'article' => 127919,
										),
										'admin.php'
									)
								)
							),
							__( 'the tutorial', 'buddyboss-pro' )
						),
					),
				),
			),
		),
	),
);

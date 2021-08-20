<?php
/**
 * The Popup Settings Meta-box configurations.
 *
 * @package Smart_Post_Show
 * @subpackage Smart_Post_Show/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access pages directly.

/**
 * The Popup settings class.
 */
class SPS_DetailSettings {

	/**
	 * Popup settings section metabox.
	 *
	 * @param string $prefix The metabox key.
	 * @return void
	 */
	public static function section( $prefix ) {
		SP_PC::createSection(
			$prefix,
			array(
				'title'  => __( 'Detail page Settings', 'smart-post-show' ),
				'icon'   => 'fa fa-external-link-square',
				'fields' => array(
					array(
						'id'       => 'pcp_page_link_type',
						'class'    => 'pcp_page_link_type',
						'type'     => 'radio',
						'title'    => __( 'Detail Page Link Type', 'smart-post-show' ),
						'subtitle' => __( 'Choose a link type for the (item) detail page.', 'smart-post-show' ),
						'options'  => array(
							'popup'       => __( 'Popup (Pro)', 'smart-post-show' ),
							'single_page' => __( 'Single Page', 'smart-post-show' ),
							'none'        => __( 'None (no link action)', 'smart-post-show' ),
						),
						'default'  => 'single_page',
					),
					array(
						'id'         => 'pcp_link_target',
						'type'       => 'radio',
						'title'      => __( 'Target', 'smart-post-show' ),
						'subtitle'   => __( 'Set a target for the item link.', 'smart-post-show' ),
						'options'    => array(
							'_self'   => __( 'Current Tab', 'smart-post-show' ),
							'_blank'  => __( 'New Tab', 'smart-post-show' ),
							'_parent' => __( 'Parent', 'smart-post-show' ),
							'_top'    => __( 'Top', 'smart-post-show' ),
						),
						'default'    => '_self',
						'dependency' => array( 'pcp_page_link_type', '==', 'single_page' ),
					),
					array(
						'id'      => 'pcp_link_rel',
						'type'    => 'checkbox',
						'title'   => __( 'Add rel="nofollow" to item links', 'smart-post-show' ),
						'default' => false,
					),
					array(
						'type'    => 'notice',
						'content' => __( 'To unlock the following amazing Popup Settings</b>, <a href="//smartpostshow.com" target="_blank"><b>Upgrade To Pro!</b></a>', 'smart-post-show' ),
					),
					array(
						'id'     => 'pcp_popup_settings',
						'class'  => 'pcp_popup_settings_field',
						'type'   => 'fieldset',
						'fields' => array(
							array(
								'id'       => 'pcp_popup_type',
								'type'     => 'radio',
								'title'    => __( 'Popup Type', 'smart-post-show-pro' ),
								'subtitle' => __( 'Choose a popup type.', 'smart-post-show-pro' ),
								'options'  => array(
									'single_popup' => __( 'Single Popup', 'smart-post-show-pro' ),
									'multi_popup'  => __( 'Multi Popup (Preview with navigation)', 'smart-post-show-pro' ),
								),
								'default'  => 'single_popup',
							),
							array(
								'id'       => 'popup_width',
								'type'     => 'spacing',
								'title'    => __( 'Maximum Width', 'smart-post-show-pro' ),
								'subtitle' => __( 'Set maximum popup width.', 'smart-post-show-pro' ),
								'all'      => true,
								'min'      => 0,
								'max'      => 3000,
								'all_icon' => '<i class="fa fa-arrows-h"></i>',
								'units'    => array( 'px', '%' ),
								'default'  => array(
									'all' => '1050',
								),
							),
							array(
								'id'       => 'popup_height',
								'type'     => 'spacing',
								'title'    => __( 'Maximum Height', 'smart-post-show-pro' ),
								'subtitle' => __( 'Set maximum popup height.', 'smart-post-show-pro' ),
								'all'      => true,
								'min'      => 0,
								'max'      => 3000,
								'all_icon' => '<i class="fa fa-arrows-v"></i>',
								'units'    => array( 'vh', 'px', '%' ),
								'default'  => array(
									'all' => '700',
								),
							),
							array(
								'id'       => 'popup_content_color',
								'type'     => 'color_group',
								'title'    => __( 'Content Color', 'smart-post-show-pro' ),
								'subtitle' => __( 'Set the popup content color.', 'smart-post-show-pro' ),
								'options'  => array(
									'post-title'    => __( 'Post Title', 'smart-post-show-pro' ),
									'post-meta'     => __( 'Post Meta', 'smart-post-show-pro' ),
									'post-content'  => __( 'Post Content', 'smart-post-show-pro' ),
									'custom-fields' => __( 'Custom Fields', 'smart-post-show-pro' ),
								),
								'default'  => array(
									'post-title'    => '#111',
									'post-meta'     => '#888',
									'post-content'  => '#444',
									'custom-fields' => '#888',
								),
							),
							array(
								'id'       => 'popup_bg_color',
								'type'     => 'color',
								'title'    => __( 'Background Color', 'smart-post-show-pro' ),
								'subtitle' => __( 'Change the popup background color.', 'smart-post-show-pro' ),
								'default'  => '#fff',
							),
							array(
								'id'       => 'popup_overlay_color',
								'type'     => 'color',
								'title'    => __( 'Overlay Color', 'smart-post-show-pro' ),
								'subtitle' => __( 'Change the popup overlay color.', 'smart-post-show-pro' ),
								'default'  => 'rgba(11,11,11,0.8)',
							),
							array(
								'id'         => 'popup_close_button',
								'type'       => 'switcher',
								'title'      => __( 'Close Button', 'smart-post-show-pro' ),
								'subtitle'   => __( 'Show/Hide popup close button.', 'smart-post-show-pro' ),
								'text_on'    => __( 'Show', 'smart-post-show-pro' ),
								'text_off'   => __( 'Hide', 'smart-post-show-pro' ),
								'text_width' => 80,
								'default'    => true,
							),
							array(
								'id'       => 'popup_close_button_color',
								'type'     => 'color_group',
								'title'    => __( 'Close Button Color', 'smart-post-show-pro' ),
								'subtitle' => __( 'Change the popup close button color.', 'smart-post-show-pro' ),
								'options'  => array(
									'color'       => __( 'Color', 'smart-post-show-pro' ),
									'hover-color' => __( 'Hover Color', 'smart-post-show-pro' ),
								),
								'default'  => array(
									'color'       => '#fc0c0c',
									'hover-color' => '#e1624b',
								),
							),
							array(
								'id'       => 'popup_nav_color',
								'type'     => 'color_group',
								'title'    => __( 'Navigation Color', 'smart-post-show-pro' ),
								'subtitle' => __( 'Change the popup navigation color.', 'smart-post-show-pro' ),
								'options'  => array(
									'color'       => __( 'Color', 'smart-post-show-pro' ),
									'hover-color' => __( 'Hover Color', 'smart-post-show-pro' ),
									'bg'          => __( 'Background', 'smart-post-show-pro' ),
									'hover-bg'    => __( 'Hover Background', 'smart-post-show-pro' ),
								),
								'default'  => array(
									'color'       => '#aaa',
									'hover-color' => '#fff',
									'bg'          => 'rgba(0,0,0,0.5)',
									'hover-bg'    => '#e1624b',
								),
							),
							array(
								'id'       => 'popup_post_content_sorter',
								'type'     => 'sortable',
								'title'    => __( 'Popup Fields', 'smart-post-show-pro' ),
								'subtitle' => __( 'Show/Hide content fields in the popup. These fields are also sortable.', 'smart-post-show-pro' ),
								'class'    => 'post_content_sorter_sortable',
								'default'  => array(
									'popup_show_post_thumb' => true,
									'popup_show_post_title' => true,
									'popup_show_post_meta' => true,
									'popup_show_post_content' => true,
									'popup_show_social_share' => true,
									'popup_show_custom_fields' => false,
								),
								'fields'   => array(
									array(
										'id'         => 'popup_show_post_thumb',
										'type'       => 'switcher',
										'title'      => __( 'Thumbnail', 'smart-post-show-pro' ),
										'text_on'    => __( 'Show', 'smart-post-show-pro' ),
										'text_off'   => __( 'Hide', 'smart-post-show-pro' ),
										'text_width' => 80,
									),
									array(
										'id'         => 'popup_show_post_title',
										'type'       => 'switcher',
										'title'      => __( 'Title', 'smart-post-show-pro' ),
										'text_on'    => __( 'Show', 'smart-post-show-pro' ),
										'text_off'   => __( 'Hide', 'smart-post-show-pro' ),
										'text_width' => 80,
									),
									array(
										'id'         => 'popup_show_post_meta',
										'type'       => 'switcher',
										'title'      => __( 'Meta Fields', 'smart-post-show-pro' ),
										'text_on'    => __( 'Show', 'smart-post-show-pro' ),
										'text_off'   => __( 'Hide', 'smart-post-show-pro' ),
										'text_width' => 80,
									),
									array(
										'id'         => 'popup_show_post_content',
										'type'       => 'switcher',
										'title'      => __( 'Content', 'smart-post-show-pro' ),
										'text_on'    => __( 'Show', 'smart-post-show-pro' ),
										'text_off'   => __( 'Hide', 'smart-post-show-pro' ),
										'text_width' => 80,
									),
									array(
										'id'         => 'popup_show_social_share',
										'type'       => 'switcher',
										'title'      => __( 'Social Share', 'smart-post-show-pro' ),
										'text_on'    => __( 'Show', 'smart-post-show-pro' ),
										'text_off'   => __( 'Hide', 'smart-post-show-pro' ),
										'text_width' => 80,
									),
									array(
										'id'         => 'popup_show_custom_fields',
										'type'       => 'switcher',
										'title'      => __( 'Custom Fields', 'smart-post-show-pro' ),
										'text_on'    => __( 'Show', 'smart-post-show-pro' ),
										'text_off'   => __( 'Hide', 'smart-post-show-pro' ),
										'text_width' => 80,
									),

								),
							),
						),
					),

				), // End of fields array.
			)
		); // Display settings section end.
	}
}

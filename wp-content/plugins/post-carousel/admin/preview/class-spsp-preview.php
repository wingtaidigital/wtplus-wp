<?php
/**
 * The admin preview.
 *
 * @link        http://smartpostshow.com/
 * @since      2.1.4
 *
 * @package    Smart_Post_Show
 * @subpackage Smart_Post_Show/admin
 */

/**
 * The admin preview.
 *
 * @package    Smart_Post_Show
 * @subpackage Smart_Post_Show/admin
 * @author     ShapedPlugin <support@shapedplugin.com>
 */
class Class_SPSPS_Preview {

	/**
	 * Script and style suffix
	 *
	 * @since 2.1.4
	 * @access protected
	 * @var string
	 */
	protected $suffix;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.1.4
	 */
	public function __construct() {
		$this->pcp_preview_action();
	}

	/**
	 * Public Action
	 *
	 * @return void
	 */
	private function pcp_preview_action() {
		// admin Preview.
		add_action( 'wp_ajax_spf_preview_meta_box', array( $this, 'pcp_backend_preview' ) );
	}

	/**
	 * Function Backed preview.
	 *
	 * @since 2.2.5
	 */
	public function pcp_backend_preview() {
		$nonce = ! empty( $_POST['nonce'] ) ? sanitize_text_field(
			wp_unslash( $_POST['nonce'] )
		) : '';
		if ( ! wp_verify_nonce( $nonce, 'spf_pcp_metabox_nonce' ) ) {
			return false;
		}
		$capability = apply_filters( 'spf_chosen_ajax_capability', 'manage_options' );
		if ( ! current_user_can( $capability ) ) {
			return esc_html__( 'You do not have required permissions to access.', 'smart-post-show' );
		}
		$settings = array();
		$data     = ! empty( $_POST['data'] ) ?
			wp_unslash( $_POST['data'] ) // phpcs:ignore
		: '';
		parse_str( $data, $settings );
		$settings = array_map( 'wp_kses_post_deep', $settings );
		// Shortcode id.
		$pcp_gl_id = $settings['post_ID'];
		// Preset Layouts.
		$layout  = $settings['sp_pcp_layouts'];
		$layouts = $layout;
		// All the visible options for the Shortcode like – Global, Filter, Display, Popup, Typography etc.
		$view_options  = $settings['sp_pcp_view_options'];
		$section_title = $settings['post_title'];
		?><style>
		<?php
		$pcp_id     = $pcp_gl_id;
		$custom_css = '';
		include SP_PC_PATH . 'public/dynamic-css/dynamic-css.php';
		echo esc_html( $custom_css );
		?>
		</style>
		<?php
		SP_PC_Output::pc_html_show( $view_options, $layout, $pcp_gl_id, $section_title );
		?>
		<script src="<?php echo esc_url( SP_PC_URL ); ?>/admin/assets/js/preview.js"></script>
		<?php
		die();
	}

}
new Class_SPSPS_Preview();

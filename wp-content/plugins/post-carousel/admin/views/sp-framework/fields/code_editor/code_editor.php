<?php if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access directly.
/**
 *
 * Field: code_editor
 *
 * @since 1.0.0
 * @version 1.0.0
 */
if ( ! class_exists( 'SP_PC_Field_code_editor' ) ) {
	class SP_PC_Field_code_editor extends SP_PC_Fields {

		public $version = '5.60.0';
		public $cdn_url = 'https://cdn.jsdelivr.net/npm/codemirror@';

		/**
		 * Constructor.
		 *
		 * @param [type] $field
		 * @param string $value
		 * @param string $unique
		 * @param string $where
		 * @param string $parent
		 */
		public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
			parent::__construct( $field, $value, $unique, $where, $parent );
		}

		/**
		 * Render function
		 *
		 * @return void
		 */
		public function render() {

			$default_settings = array(
				'tabSize'     => 2,
				'lineNumbers' => true,
				'theme'       => 'default',
				'mode'        => 'htmlmixed',
				'cdnURL'      => $this->cdn_url . $this->version,
			);

			$settings = ( ! empty( $this->field['settings'] ) ) ? $this->field['settings'] : array();
			// $view_options = wp_parse_args( $view_options, $default_settings );
			$settings = wp_parse_args( $settings, $default_settings );
			// $encoded      = htmlspecialchars( json_encode( $view_options ) );

			echo wp_kses_post( $this->field_before() );
			echo '<textarea name="' . esc_attr( $this->field_name() ) . '"' . wp_kses_post( $this->field_attributes() ) . ' data-editor="' . esc_attr( json_encode( $settings ) ) . '">' . wp_kses_post( $this->value ) . '</textarea>';
			echo wp_kses_post( $this->field_after() );

		}

		/**
		 * Enqueue code-mirror .
		 *
		 * @return void
		 */
		public function enqueue() {

			// Do not loads CodeMirror in revslider page.
			// if ( in_array( spf_get_var( 'page' ), array( 'revslider' ) ) ) {
			// return; }
			$page = ( ! empty( $_GET['page'] ) ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
			// Do not loads CodeMirror in revslider page.
			if ( in_array( $page, array( 'revslider' ) ) ) {
				return; }

			if ( ! wp_script_is( 'spf-codemirror' ) ) {
				wp_enqueue_script( 'spf-codemirror', esc_url( $this->cdn_url . $this->version . '/lib/codemirror.min.js' ), array( 'spf' ), $this->version, true );
				wp_enqueue_script( 'spf-codemirror-loadmode', esc_url( $this->cdn_url . $this->version . '/addon/mode/loadmode.min.js' ), array( 'spf-codemirror' ), $this->version, true );
			}

			if ( ! wp_style_is( 'spf-codemirror' ) ) {
				wp_enqueue_style( 'spf-codemirror', esc_url( $this->cdn_url . $this->version . '/lib/codemirror.min.css' ), array(), $this->version );
			}

		}

	}
}

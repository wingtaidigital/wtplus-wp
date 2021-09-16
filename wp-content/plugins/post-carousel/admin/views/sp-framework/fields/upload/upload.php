<?php if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access directly.
/**
 *
 * Field: upload
 *
 * @since 1.0.0
 * @version 1.0.0
 */
if ( ! class_exists( 'SP_PC_Fields_upload' ) ) {
	class SP_PC_Fields_upload extends SP_PC_Fields {
		/**
		 * Class constructor.
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
		 * The render method.
		 *
		 * @return void
		 */
		public function render() {

			$args = wp_parse_args(
				$this->field,
				array(
					'library'      => array(),
					'button_title' => esc_html__( 'Upload', 'smart-post-show' ),
					'remove_title' => esc_html__( 'Remove', 'smart-post-show' ),
				)
			);

			echo wp_kses_post( $this->field_before() );

			$library = ( is_array( $args['library'] ) ) ? $args['library'] : array_filter( (array) $args['library'] );
			$library = ( ! empty( $library ) ) ? implode( ',', $library ) : '';
			$hidden  = ( empty( $this->value ) ) ? ' hidden' : '';

			echo '<div class="spf--wrap">';
			echo '<input type="text" name="' . esc_attr( $this->field_name() ) . '" value="' . esc_attr( $this->value ) . '"' . wp_kses_post( $this->field_attributes() ) . '/>';
			echo '<a href="#" class="button button-primary spf--button" data-library="' . esc_attr( $library ) . '">' . esc_html( $args['button_title'] ) . '</a>';
			echo '<a href="#" class="button button-secondary spf-warning-primary spf--remove' . esc_attr( $hidden ) . '">' . esc_html( $args['remove_title'] ) . '</a>';
			echo '</div>';

			echo wp_kses_post( $this->field_after() );

		}
	}
}

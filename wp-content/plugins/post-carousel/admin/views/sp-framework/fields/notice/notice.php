<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.
/**
 *
 * Field: notice
 *
 * @since 2.0
 * @version 2.0
 */
if ( ! class_exists( 'SP_PC_Field_notice' ) ) {
	class SP_PC_Field_notice extends SP_PC_Fields {

		/**
		 * Constructor method.
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
		 * Render method.
		 *
		 * @return void
		 */
		public function render() {
			echo wp_kses_post( $this->field_before() );
			$style = ( ! empty( $this->field['style'] ) ) ? $this->field['style'] : 'normal';

			echo ( ! empty( $this->field['content'] ) ) ? '<div class="spf-notice spf-notice-' . $style . '">' . wp_kses_post( $this->field['content'] ) . '</div>' : '';
			echo wp_kses_post( $this->field_after() );
		}

	}
}

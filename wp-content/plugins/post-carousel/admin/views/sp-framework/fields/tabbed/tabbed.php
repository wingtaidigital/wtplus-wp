<?php if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access directly.
/**
 *
 * Field: tabbed
 *
 * @since 1.0.0
 * @version 1.0.0
 */
if ( ! class_exists( 'SP_PC_Field_tabbed' ) ) {
	class SP_PC_Field_tabbed extends SP_PC_Fields {

		/**
		 * The class constructor.
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

			$unallows = array( 'tabbed' );

			echo wp_kses_post( $this->field_before() );

			echo '<div class="spf-tabbed-nav">';
			foreach ( $this->field['tabs'] as $key => $tab ) {

				$tabbed_icon   = ( ! empty( $tab['icon'] ) ) ? '<i class="spf--icon ' . $tab['icon'] . '"></i>' : '';
				$tabbed_active = ( empty( $key ) ) ? ' class="spf-tabbed-active"' : '';

				echo '<a href="#"' . wp_kses_post( $tabbed_active ) . '>' . wp_kses_post( $tabbed_icon . $tab['title'] ) . '</a>';

			}
			echo '</div>';

			echo '<div class="spf-tabbed-sections">';
			foreach ( $this->field['tabs'] as $key => $tab ) {

				$tabbed_hidden = ( ! empty( $key ) ) ? ' hidden' : '';

				echo '<div class="spf-tabbed-section' . esc_attr( $tabbed_hidden ) . '">';

				foreach ( $tab['fields'] as $field ) {

					if ( in_array( $field['type'], $unallows ) ) {
						$field['_notice'] = true; }

					$field_id      = ( isset( $field['id'] ) ) ? $field['id'] : '';
					$field_default = ( isset( $field['default'] ) ) ? $field['default'] : '';
					$field_value   = ( isset( $this->value[ $field_id ] ) ) ? $this->value[ $field_id ] : $field_default;
					$unique_id     = ( ! empty( $this->unique ) ) ? $this->unique . '[' . $this->field['id'] . ']' : $this->field['id'];

					SP_PC::field( $field, $field_value, $unique_id, 'field/tabbed' );

				}

				echo '</div>';

			}
			echo '</div>';

			echo wp_kses_post( $this->field_after() );

		}

	}
}

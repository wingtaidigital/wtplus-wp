<?php if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access directly.
/**
 *
 * Field: gallery
 *
 * @since 1.0.0
 * @version 1.0.0
 */
if ( ! class_exists( 'SP_PC_Field_gallery' ) ) {
	class SP_PC_Field_gallery extends SP_PC_Fields {

		/**
		 * The field constructor.
		 *
		 * @param string $field The field type.
		 * @param string $value The field value.
		 * @param string $unique The unique ID.
		 * @param string $where The place to show the field.
		 * @param string $parent If it has any parent.
		 */
		public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
			parent::__construct( $field, $value, $unique, $where, $parent );
		}

		/**
		 * The render function.
		 *
		 * @return void
		 */
		public function render() {

			$args = wp_parse_args(
				$this->field,
				array(
					'add_title'   => esc_html__( 'Add Gallery', 'smart-post-show' ),
					'edit_title'  => esc_html__( 'Edit Gallery', 'smart-post-show' ),
					'clear_title' => esc_html__( 'Clear', 'smart-post-show' ),
				)
			);

			$hidden = ( empty( $this->value ) ) ? ' hidden' : '';

			echo wp_kses_post( $this->field_before() );
			echo '<a href="#" class="button button-primary spf-button"><i class="fa fa-plus-circle"></i>' . esc_html( $args['add_title'] ) . '</a>';
			echo '<ul class="sp-gallery-images">';
			if ( ! empty( $this->value ) ) {

				$values = explode( ',', $this->value );

				foreach ( $values as $id ) {
					$attachment = wp_get_attachment_image_src( $id, 'thumbnail' );
					echo '<li><img src="' . esc_url( $attachment[0] ) . '"/></li>';
				}
			}

			echo '</ul>';

			echo '<ul><li><a href="#" class="button spf-edit-gallery' . esc_attr( $hidden ) . '"><i class="fa fa-pencil-square-o"></i>' . esc_html( $args['edit_title'] ) . '</a></li></ul>';
			echo '<ul><li><a href="#" class="button spf-warning-primary spf-clear-gallery' . esc_attr( $hidden ) . '"><i class="fa fa-trash"></i>' . esc_html( $args['clear_title'] ) . '</a></li></ul>';
			echo '<input type="text" name="' . esc_attr( $this->field_name() ) . '" value="' . esc_attr( $this->value ) . '"' . wp_kses_post( $this->field_attributes() ) . '/></span>';

			echo wp_kses_post( $this->field_after() );

		}

	}
}

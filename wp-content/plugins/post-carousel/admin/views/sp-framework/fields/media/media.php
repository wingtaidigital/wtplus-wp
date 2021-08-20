<?php if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access directly.
/**
 *
 * Field: media
 *
 * @since 1.0.0
 * @version 1.0.0
 */
if ( ! class_exists( 'SP_PC_Field_media' ) ) {
	class SP_PC_Field_media extends SP_PC_Fields {

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
					'url'          => true,
					'preview'      => true,
					'library'      => array(),
					'button_title' => esc_html__( 'Upload', 'smart-post-show' ),
					'remove_title' => esc_html__( 'Remove', 'smart-post-show' ),
					'preview_size' => 'thumbnail',
				)
			);

			$default_values = array(
				'url'         => '',
				'id'          => '',
				'width'       => '',
				'height'      => '',
				'thumbnail'   => '',
				'alt'         => '',
				'title'       => '',
				'description' => '',
			);

			// fallback.
			if ( is_numeric( $this->value ) ) {
				$this->value = array(
					'id'        => $this->value,
					'url'       => wp_get_attachment_url( $this->value ),
					'thumbnail' => wp_get_attachment_image_src( $this->value, 'thumbnail', true )[0],
				);
			}
			$this->value = wp_parse_args( $this->value, $default_values );

			$library     = ( is_array( $args['library'] ) ) ? $args['library'] : array_filter( (array) $args['library'] );
			$library     = ( ! empty( $library ) ) ? implode( ',', $library ) : '';
			$preview_src = ( 'thumbnail' !== $args['preview_size'] ) ? $this->value['url'] : $this->value['thumbnail'];
			$hidden_url  = ( empty( $args['url'] ) ) ? ' hidden' : '';
			$hidden_auto = ( empty( $this->value['url'] ) ) ? ' hidden' : '';
			$placeholder = ( empty( $this->field['placeholder'] ) ) ? ' placeholder="' . esc_html__( 'No media selected', 'smart-post-show' ) . '"' : '';

			echo wp_kses_post( $this->field_before() );

			if ( ! empty( $args['preview'] ) ) {
				echo '<div class="spf--preview' . esc_attr( $hidden_auto ) . '">';
				echo '<div class="spf-image-preview"><a href="#" class="spf--remove fa fa-times"></a><img src="' . esc_url( $preview_src ) . '" class="spf--src"/></div>';
				echo '</div>';
			}

			echo '<div class="spf--placeholder">';
			echo '<input type="text" name="' . esc_url( $this->field_name( '[url]' ) ) . '" value="' . esc_url( $this->value['url'] ) . '" class="spf--url' . esc_attr( $hidden_url ) . '" readonly="readonly"' . wp_kses_post( $this->field_attributes() . $placeholder ) . ' />';
			echo '<a href="#" class="button button-primary spf--button" data-library="' . esc_attr( $library ) . '" data-preview-size="' . esc_attr( $args['preview_size'] ) . '">' . esc_html( $args['button_title'] ) . '</a>';
			echo ( empty( $args['preview'] ) ) ? '<a href="#" class="button button-secondary spf-warning-primary spf--remove' . esc_attr( $hidden_auto ) . '">' . esc_html( $args['remove_title'] ) . '</a>' : '';
			echo '</div>';

			echo '<input type="hidden" name="' . esc_attr( $this->field_name( '[id]' ) ) . '" value="' . esc_attr( $this->value['id'] ) . '" class="spf--id"/>';
			echo '<input type="hidden" name="' . esc_attr( $this->field_name( '[width]' ) ) . '" value="' . esc_attr( $this->value['width'] ) . '" class="spf--width"/>';
			echo '<input type="hidden" name="' . esc_attr( $this->field_name( '[height]' ) ) . '" value="' . esc_attr( $this->value['height'] ) . '" class="spf--height"/>';
			echo '<input type="hidden" name="' . esc_attr( $this->field_name( '[thumbnail]' ) ) . '" value="' . esc_attr( $this->value['thumbnail'] ) . '" class="spf--thumbnail"/>';
			echo '<input type="hidden" name="' . esc_attr( $this->field_name( '[alt]' ) ) . '" value="' . esc_attr( $this->value['alt'] ) . '" class="spf--alt"/>';
			echo '<input type="hidden" name="' . esc_attr( $this->field_name( '[title]' ) ) . '" value="' . esc_attr( $this->value['title'] ) . '" class="spf--title"/>';
			echo '<input type="hidden" name="' . esc_attr( $this->field_name( '[description]' ) ) . '" value="' . esc_attr( $this->value['description'] ) . '" class="spf--description"/>';

			echo wp_kses_post( $this->field_after() );

		}

	}
}

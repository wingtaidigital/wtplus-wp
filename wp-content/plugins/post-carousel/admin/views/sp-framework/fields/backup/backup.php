<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.
/**
 *
 * Field: backup
 *
 * @since 1.0.0
 * @version 1.0.0
 */
if ( ! class_exists( 'SP_PC_Field_backup' ) ) {
	class SP_PC_Field_backup extends SP_PC_Fields {

		/**
		 * Constructor function.
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

			$unique = $this->unique;
			$nonce  = wp_create_nonce( 'spf_backup_nonce' );
			$export = add_query_arg(
				array(
					'action' => 'spf-export',
					'export' => $unique,
					'nonce'  => $nonce,
				),
				admin_url( 'admin-ajax.php' )
			);

			echo wp_kses_post( $this->field_before() );

			echo '<textarea name="spf_transient[spf_import_data]" class="spf-import-data"></textarea>';
			echo '<button type="submit" class="button button-primary spf-confirm spf-import" data-unique="' . $unique . '" data-nonce="' . $nonce . '">' . esc_html__( 'Import', 'smart-post-show' ) . '</button>';
			echo '<small>( ' . esc_html__( 'copy-paste your backup string here', 'smart-post-show' ) . ' )</small>';

			echo '<hr />';
			echo '<textarea readonly="readonly" class="spf-export-data">' . json_encode( get_option( $unique ) ) . '</textarea>';

			echo '<a href="' . esc_url( $export ) . '" class="button button-primary spf-export" target="_blank">' . esc_html__( 'Export and Download Backup', 'smart-post-show' ) . '</a>';
			echo '<hr />';
			echo '<button type="submit" name="spf_transient[spf_reset_all]" value="spf_reset_all" class="button spf-warning-primary spf-confirm spf-reset" data-unique="' . $unique . '" data-nonce="' . $nonce . '">' . esc_html__( 'Reset All', 'smart-post-show' ) . '</button>';
			echo '<small class="spf-text-error">' . esc_html__( 'Please be sure for reset all of options.', 'smart-post-show' ) . '</small>';

			echo wp_kses_post( $this->field_after() );

		}

	}
}

<?php
/*
Plugin Name: WP Jive Importer
Plugin URI:
Description: Import posts, comments, and tags from a Jive Blog and migrate authors to Wordpress users.
Author: markparolisi, voceconnect
Author URI:
Version: 0.1.0
License: GPLv2
License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

if ( !defined( 'WP_LOAD_IMPORTERS' ) ) {
	return;
}
require_once 'lib/connection.php';
require_once 'lib/post.php';
require_once 'lib/media.php';
require_once 'lib/taxonomy.php';
require_once 'lib/author.php';

if ( !class_exists( 'WP_Importer' ) ) {
	$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
	if ( file_exists( $class_wp_importer ) ) {
		require_once $class_wp_importer;
	}
}

if ( class_exists( 'WP_Importer' ) ) {
	class Jive_Import extends WP_Importer {

		protected $error = null;
		protected $jive_settings = array(
			'domain'   => '',
			'blog_id'  => 0,
			'username' => '',
			'password' => '',
		);

		public static function init() {
			$jive_import = new Jive_Import();
			$jive_import->jive_settings = get_option('jive_import_settings');
			register_importer( 'jive', 'Jive', 'Import from a Jive blog.', array( $jive_import, 'run' ) );
		}

		/**
		 * Kick off the import
		 */
		public function run() {
			if ( isset( $_POST[ 'jive_settings' ] ) ) {
				$this->jive_settings = array_merge( $this->jive_settings, array_map( 'esc_attr', $_POST[ 'jive_settings' ] ) );
				update_option( 'jive_import_settings', $this->jive_settings );
			}
			$this->greet( $this->error );
		}

		/**
		 * The admin page for user credentials entry
		 *
		 * @param null $error
		 */
		public function greet( $error = null ) {

			if ( !empty( $error ) ) {
				echo "<div class='error'><p>{$error}</p></div>";
			}
			?>

			<div class='wrap'><?php echo screen_icon(); ?>
				<h2>Import Jive Blog</h2>

				<p>Get your auth data from Jive (add more instructions here)</p>

				<form action='?import=jive' method='post'>
					<table class="form-table">
						<tr>
							<th scope="row"><label for='domain'>Domain</label></label>
							</th>
							<td><input type='text' class="regular-text" name='jive_settings[domain]'
							           value='<?php echo esc_attr( $this->jive_settings[ 'domain' ] ); ?>'/>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for='blog_id'>Blog ID</label></label>
							</th>
							<td><input type='text' class="regular-text" name='jive_settings[blog_id]'
							           value='<?php echo esc_attr( $this->jive_settings[ 'blog_id' ] ); ?>'/>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for='username'>Username</label></label>
							</th>
							<td><input type='text' class="regular-text" name='jive_settings[username]'
							           value='<?php echo esc_attr( $this->jive_settings[ 'username' ] ); ?>'/>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for='password'>Password</label></label>
							</th>
							<td><input type='text' class="regular-text" name='jive_settings[password]'
							           value='<?php echo esc_attr( $this->jive_settings[ 'password' ] ); ?>'/>
							</td>
						</tr>
					</table>
					<p class='submit'>
						<input type='submit' class='button button-primary' value="Save Settings"/>
						<input type='button' class='button' value="Import"/>
					</p>
				</form>
			</div>
		<?php
		}
	}
}

add_action( 'admin_init', array( 'Jive_Import', 'init' ) );
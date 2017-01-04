<?php
/**
 * Plugin Name:     WP SMTP Config
 * Plugin URI:      http://wordpress.org/extend/plugins/wp-smtp-config/
 * Description:     Configure an external SMTP server in your config file.
 * Author:          Daniel Schröder
 * Author URI:      https://github.com/schroedan
 * License:         GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/gpl-2.0.html
 * Version:         1.1.1
 *
 * @package         WP_SMTP_Config
 */

if ( defined( 'WP_SMTP_HOST' ) && is_string( WP_SMTP_HOST ) ) {

	/**
	 * PHPMailer init action.
	 *
	 * @param PHPMailer $phpmailer The PHPMailer instance.
	 *
	 * @return void
	 */
	function wp_smtp_config_phpmailer_init( $phpmailer ) {
		$phpmailer->isSMTP();
		$phpmailer->Host = WP_SMTP_HOST;

		if ( defined( 'WP_SMTP_PORT' ) && is_int( WP_SMTP_PORT ) ) {
			$phpmailer->Port = WP_SMTP_PORT;
		}

		if ( defined( 'WP_SMTP_ENCRYPTION' ) && in_array( WP_SMTP_ENCRYPTION, array( 'ssl', 'tls' ), true ) ) {
			$phpmailer->SMTPSecure = WP_SMTP_ENCRYPTION;
		}

		if ( defined( 'WP_SMTP_USER' ) && is_string( WP_SMTP_USER ) ) {
			$phpmailer->SMTPAuth = true;
			$phpmailer->Username = WP_SMTP_USER;

			if ( defined( 'WP_SMTP_PASSWORD' ) && is_string( WP_SMTP_PASSWORD ) ) {
				$phpmailer->Password = WP_SMTP_PASSWORD;
			}
		}

		if ( defined( 'WP_SMTP_FROM' ) && preg_match( '/^(?P<name>.+?)(<(?P<from>.*)>)?$/', WP_SMTP_FROM, $matches ) > 0 ) {
			if ( isset( $matches['from'] ) ) {
				$phpmailer->FromName = $matches['name'];
			} else {
				$matches['from'] = $matches['name'];
			}

			$phpmailer->From = $matches['from'];
		}
	}

	add_action( 'phpmailer_init', 'wp_smtp_config_phpmailer_init' );

	function wp_smtp_config_network_admin_menu() {
		add_submenu_page( 'settings.php', 'SMTP Settings', 'SMTP', 'manage_network_options', 'wp-smtp-config', 'wp_smtp_config_options_page' );
	}

	function wp_smtp_config_admin_menu() {
		add_options_page( 'SMTP Settings', 'SMTP', 'manage_options', 'wp-smtp-config', 'wp_smtp_config_options_page' );
	}

	function wp_smtp_config_options_page_save() {
		global $phpmailer;

		if ( isset( $_POST['smtp_submit'] ) && $_POST['smtp_submit'] == 'Send' && isset( $_POST['smtp_recipient'] ) ) {
			$message          = new stdClass();
			$message->error   = true;
			$message->title   = 'Test Email Failure';
			$message->content = array( 'There was an error while trying to send the test email.' );

			if ( is_email( $_POST['smtp_recipient'] ) ) {
				if ( wp_mail( $_POST['smtp_recipient'], 'SMTP Test', 'If you received this email it means you have configured SMTP correctly on your WordPress website.' ) ) {
					$message->error   = false;
					$message->title   = 'Test Email Success';
					$message->content = array( 'The test email was sent successfully.' );

					return $message;
				}
				$error = ( is_object( $phpmailer ) && is_a( $phpmailer, 'PHPMailer' ) ) ? $phpmailer->ErrorInfo : '';
				if ( ! empty( $error ) ) {
					array_push( $message->content, $error );
				}
			} elseif ( empty( $_POST['smtp_recipient'] ) ) {
				array_push( $message->content, 'Please enter a valid email address.' );
			} else {
				array_push( $message->content, $_POST['smtp_recipient'] . ' is no valid email address.' );
			}

			return $message;
		}

		return null;
	}

	function wp_smtp_config_options_page() {
		$message = wp_smtp_config_options_page_save();
		?>
		<?php if ( is_object( $message ) ) : ?>
			<div id="smtp-message" class="<?php echo esc_attr( $message->error ? 'error' : 'updated fade' ); ?>">
				<p><strong><?php echo esc_html( $message->title ); ?></strong></p>
				<?php foreach ( $message->content as $content ) : ?>
					<p><?php echo esc_html( $content ); ?></p>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<div class="wrap">
			<div class="icon32" id="icon-options-general"><br/></div>
			<h2>SMTP Settings</h2>
			<h3>Send a Test Email</h3>
			<p>Enter a valid email address below to send a test message.</p>
			<form method="post">
				<table class="optiontable form-table">
					<tr valign="top">
						<th scope="row"><label for="smtp_recipient">Recipient:</label></th>
						<td><input id="smtp_recipient" name="smtp_recipient" type="text" value="" class="regular-text"/>
						</td>
					</tr>
				</table>
				<p class="submit">
					<input type="submit" name="smtp_submit" class="button-primary" value="Send"/>
				</p>
			</form>
		</div>
		<?php
	}

	if ( defined( 'MULTISITE' ) && MULTISITE ) {
		add_action( 'network_admin_menu', 'wp_smtp_config_network_admin_menu' );
	} else {
		add_action( 'admin_menu', 'wp_smtp_config_admin_menu' );
	}
}

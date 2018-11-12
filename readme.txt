=== Plugin Name ===
Contributors: pCoLaSD, holzhannes
Tags: email, mail, phpmailer, smtp, ssl, tls, wp_mail, wpmu, multisite, network
Requires at least: 3.0
Tested up to: 4.9.8
Stable tag: 1.2.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Configure an external SMTP server in your config file.

== Description ==

This plugin configures WordPress and WordPress MU to use a SMTP server when sending emails instead of the default PHP `mail()` function.

You will configure your SMTP settings in your `wp-config.php` file instead of the settings page.
The advantage is that no admin of your blog can read the settings.
And you only have to place your settings once in cases of a WordPress MU installation.

A sample configuration:
`
/**
 * WordPress SMTP server
 */
define('WP_SMTP_HOST',       'mail.example.com');
define('WP_SMTP_PORT',       25);                                // obligatory - default: 25
define('WP_SMTP_ENCRYPTION', 'tls');                             // obligatory ('tls' or 'ssl') - default: no encryption
define('WP_SMTP_USER',       'username');                        // obligatory - default: no user
define('WP_SMTP_PASSWORD',   'password');                        // obligatory - default: no password
define('WP_SMTP_FROM',       'John Doe <john.doe@example.com>'); // obligatory - default: no custom from address
define('WP_SMTP_REPLYTO',    'Jane Doe <jane.doe@example.com>'); // obligatory - default: no custom reply to address
`

== Installation ==

1. Upload `wp-smtp-config.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Place the SMTP details (see [Description](http://wordpress.org/extend/plugins/wp-smtp-config/)) in your `wp-config.php` file above the line `/* That’s all, stop editing! Happy blogging. */`
4. Test your settings (`Settings -> SMTP`)

== Frequently Asked Questions ==

= Where is my SMTP settings page? =

The configuration of the SMTP server credentials will be placed in your `wp-config.php` file only.
You can test your configuration in `Settings -> SMTP Test`.
If you are running a MU installation you will find this settings page for SMTP Test in your network settings.

== Changelog ==

= 1.2.0 =
* Fixed bug settings page not showing for network admin
* Added config for custom reply to address
* Added some security validations

= 1.1.1 =
* Fixed bug with port configuration

= 1.1.0 =
* Added config for custom from address

= 1.0 =
* Initial release

<?php
/* Plugin Name: EngineMailer for Wordpress 
 * Plugin URI: http://www.connesis.com`/
 * Description: Let Wordpress to send email via EngineMailer service
 * Version: 1.0
 * Author: Francis Hor
 * Author URI: http://www.connesis.com/
 * License: GPLv2 or later
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

global $em_plugin_network_activate;
global $em_plugin_name;
global $em_api;

$em_plugin_network_activate = FALSE;
$em_plugin_name = plugin_basename(__FILE__);

require_once 'vendor/autoload.php';
require_once 'constants.php';

// Include our libraries
require_once 'lib/helpers.php';
require_once 'lib/twig.php';


if ( !function_exists( 'hmspa' ) ) {
    require_once 'lib/class-hms-plugin-activation.php';
}

if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
    require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
}

require_once 'includes/class-em-admin-scripts.php';
require_once 'includes/class-em-network-admin.php';
require_once 'includes/class-em-admin.php';

class EngineMailer {
    private static $_instance = null;

    private $twig;
    private $network_admin;
    private $admin;
    private $admin_scripts;
    private $api;
    private $ajax;

	public function __construct() {
        global $em_plugin_network_activate, $em_api;
                
        $this->twig = get_twig();

        // Plugin Activation and Deactivation
		register_activation_hook(__FILE__, array($this, 'pluginActivate'));
		register_deactivation_hook(__FILE__, array($this, 'pluginDeactivate'));
   
        // Action Hooks for checking other plugins requirements
        add_action( 'hmspa_register', array($this, 'registerRequiredPlugins'));

        // Action Hooks for plugin
        add_action('plugins_loaded', array($this, 'pluginsLoaded'));
        add_action('init', array($this, 'pluginInit'));
                
        $em_plugin_network_activate = is_plugin_active_for_network('enginemailer/enginemailer.php');
        if ($em_plugin_network_activate) {
            log_message('debug', '[enginemailer][__construct()] plugin is network activated.');
        } else {
            log_message('debug', '[enginemailer][__construct()]: plugin is site activated.');
        }

        $this->admin_scripts = new EmAdminScripts();
        $this->network_admin = new EmNetworkAdmin();
        $this->admin = new EmAdmin();
        
	}
    
	public static function getInstance() {
		if ( ! ( self::$_instance instanceof self ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}
    
	public function pluginActivate($network_wide) {
        global $wpdb;

        log_message('debug', '[enginemailer][plguinActivate()] start.');

        if (function_exists('is_multisite') && is_multisite()) {
            if ($network_wide) {
                log_message("debug", "[enginemailer][pluginActivate()] multisite enable, plugin is network activated, current blog id is {$wpdb->blogid}");

                $old_blog = $wpdb->blogid;
                $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
                foreach ($blogids as $blog_id) {
                    switch_to_blog($blog_id);
                    $this->_pluginActivate();
                }
                switch_to_blog($old_blog);                

            } else {
                log_message("debug", "[enginemailer][pluginActivate()] multisite enable, plugin is site activated, current blog id is {$wpdb->blogid}");

                $this->_pluginActivate();
            }
        } else {
            log_message("debug", "[enginemailer][pluginActivate()] single site activated, current blog id is {$wpdb->blogid}");

            $this->_pluginActivate();
        }

	}
    
    private function _pluginActivate() {
        $blog_id = get_current_blog_id();
        log_message('debug', "[enginemailer][_pluginActivate()] activate for blog $blog_id.");

    }
    
    public function pluginDeactivate($network_wide) {
        global $wpdb;

        log_message('debug', '[enginemailer][pluginDeactivate()] start.');

        if (function_exists('is_multisite') && is_multisite()) {
            if ($network_wide) {
                $old_blog = $wpdb->blogid;
                $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
                foreach ($blogids as $blog_id) {
                    switch_to_blog($blog_id);
                    $this->_pluginDeactivate();
                }
                switch_to_blog($old_blog);                

            } else {
                $this->_pluginDeactivate();
            }
        } else {
            $this->_pluginDeactivate();
        }

    }

    private function _pluginDeactivate() {
        $blog_id = get_current_blog_id();
        log_message('debug', "[enginemailer][_pluginDeactivate()] deactivate for blog $blog_id.");

    }	

    public function pluginsLoaded() {
        log_message("debug", "[enginemailer][pluginLoaded()] start");
    }

    public function pluginInit() {
        log_message("debug", "[enginemailer][pluginInit()] network site url:" . network_site_url());
        log_message("debug", "[enginemailer][pluginInit()] network site host:" . parse_url(network_site_url(), PHP_URL_HOST));
        
    }

    public function registerRequiredPlugins() {
        log_message("debug", "[enginemailer][registerRequiredPlugins()] start");
    
    }

}

// Start PHP session first
if( !session_id() ) {
    log_message("debug", "[enginemailer] start session.");
    session_start();
}

EngineMailer::getInstance();



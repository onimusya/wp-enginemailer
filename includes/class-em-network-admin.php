<?php

class EmNetworkAdmin {
    private $twig;

    public function __construct() {
        global $em_twig, $em_plugin_name, $em_plugin_network_activate;;

        $this->twig = $em_twig;

        // Network Admin Settings only for network activate
        if ($em_plugin_network_activate) {
            // Setup Actions
            add_action('network_admin_menu', array($this, "networkAdminMenu"));        

            add_filter('network_admin_plugin_action_links_' . $em_plugin_name , array($this, 'addActionLinks'));
        }

    }

    public function networkAdminMenu() {

        // Setup Network Admin Main Menu
        add_menu_page(
            __(EM_PAGE_TITLE, EM_TEXT_DOMAIN), 
            __(EM_MENU_TITLE, EM_TEXT_DOMAIN), 
            'manage_network_options', 
            EM_MENU_SLUG, 
            array($this, 'networkMainMenuPage'), 
            EM_URL . 'assets/img/123funnel-menu-icon.png', 
            '23.56'
        );
    }

    public function networkMainMenuPage() {
        global $wpdb, $current_user;
        log_message('debug', '[enginemailer][EmNetworkAdmin->addActionLinks()] admin url:' . self_admin_url());
        log_message('debug', '[enginemailer][EmNetworkAdmin->addActionLinks()] menu page url:' . menu_page_url(EM_MENU_SLUG, false));

		if ( !is_super_admin() ) {
			echo "<p>" . __( 'Access Denied...', EM_TEXT_DOMAIN) . "</p>"; //If accessed properly, this message doesn't appear.
			return;
		}        
        
        $tab = '';
        if (isset($_GET['t'])) {
            $tab = $_GET['t'];
        }
        

        $params = [            
            'error_message' => '',
            'current_tab' => $tab,
            'tab_general_url' => self_admin_url() . '/admin.php?page=' . EM_MENU_SLUG,
            'tab_others_url' => self_admin_url() . '/admin.php?page=' . EM_MENU_SLUG . '&t=testmail'
        ];
        
        $action = '';
        if (isset($_POST['action'])) {
            // Admin Page Form Submit
            check_admin_referer( 'em_network_settings', 'em_network_settings_nonce' );
            $action = $_POST['action'];

            if ($action == 'general') {

                $em_user_id = '';
                if (isset($_POST['em_user_id']))
                    $em_user_id = sanitize_text_field($_POST['em_user_id']);

                $em_user_key = '';
                if (isset($_POST['em_user_key']))
                    $em_user_key = sanitize_text_field($_POST['em_user_key']);

                update_site_option(EM_OPTION_EM_USER_ID, $fb_app_id);
                update_site_option(EM_OPTION_EM_USER_KEY, $fb_app_secret);

            } else if ($action == 'testmail') {
                log_message('debug', "[enginemailer][EmNetworkAdmin->networkMainMenuPage()]action:$action");

            } else {
                log_message('debug', "[enginemailer][EmNetworkAdmin->networkMainMenuPage()] unknown form action:$action");

            }
        }

        // Render Admin Main Menu Page

        $nonce = wp_nonce_field('em_network_settings', 'em_network_settings_nonce', true, false);
        $params['nonce'] = $nonce;
        
        switch ($tab) {
            case 'testemail':

                echo $this->twig->render('network_settings_other.tpl.html', $params);
                break;

            default:

                $params[EM_OPTION_EM_USER_ID] = get_site_option(EM_OPTION_EM_USER_ID);
                $params[EM_OPTION_EM_USER_KEY] = get_site_option(EM_OPTION_EM_USER_KEY);

                echo $this->twig->render('network_settings.tpl.html', $params);
                break;
        }

    }

    public function addActionLinks($links) {
        log_message('debug', '[enginemailer][EmNetworkAdmin->addActionLinks()] start');

        $mylinks = array(
            '<a href="' . self_admin_url() . '/admin.php?page=' . EM_MENU_SLUG . '">Settings</a>',
        );

        return array_merge( $links, $mylinks );
    }


}
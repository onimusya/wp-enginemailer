<?php

class EmAdmin {
    private $twig;
    private $em_user_id;
    private $em_user_key;

    public function __construct() {
        global $em_twig, $em_plugin_name;

        $this->twig = $em_twig;

        // Setup Actions
        add_action('init', array($this, 'pluginInit'));
        add_action('admin_menu', array($this, "adminMenu"), 999);

        add_filter('plugin_action_links_' . $em_plugin_name , array($this, 'addActionLinks'));

    }

    public function pluginInit() {
        global $em_plugin_network_activate;

        $blog_id = get_current_blog_id();

        if ($em_plugin_network_activate) {
            $this->em_user_id = get_site_option(EM_OPTION_EM_USER_ID);
            $this->em_user_key = get_site_option(EM_OPTION_EM_USER_KEY);
            
        } else {
            $this->em_user_id = get_option(EM_OPTION_EM_USER_ID);
            $this->em_user_key = get_option(EM_OPTION_EM_USER_KEY);
            
        }        
    }

    public function adminMenu() {

        // Setup Network Admin Menu
        add_menu_page(
            __(EM_PAGE_TITLE, EM_TEXT_DOMAIN), 
            __(EM_MENU_TITLE, EM_TEXT_DOMAIN), 
            'manage_options', 
            EM_MENU_SLUG, 
            array($this, 'mainMenuPage'), 
            EM_URL . 'assets/img/123funnel-menu-icon.png', 
            '23.56'
        );
    }

    public function mainMenuPage() {
        global $wpdb, $current_user, $em_plugin_network_activate;

        log_message('debug', '[enginemailer][EmAdmin->mainMenuPage()] admin url:' . self_admin_url());
        log_message('debug', '[enginemailer][EmAdmin->mainMenuPage()] menu page url:' . menu_page_url(EM_MENU_SLUG, false));        

        $tab = '';
        if (isset($_GET['t'])) {
            $tab = $_GET['t'];
        }

        $params = [            
            'error_message' => '',
            'plugin_network_activate' => $em_plugin_network_activate,
            'current_tab' => $tab,
            'tab_general_url' => menu_page_url(EM_MENU_SLUG, false),
            'tab_testmail_url' => menu_page_url(EM_MENU_SLUG, false) . '&t=testmail'

        ];

        $action = '';
        if (isset($_POST['action'])) {
            // Admin Page Form Submit
            check_admin_referer( 'em_settings', 'em_settings_nonce' );
            $action = $_POST['action'];

            log_message('debug', "[enginemailer][EmAdmin->mainMenuPage()] form action:$action");

            if ($action == 'general') {
                $em_user_id = '';
                if (isset($_POST['em_user_id'])) {
                    $em_user_id = sanitize_text_field($_POST['em_user_id']);
                }

                $em_user_key = '';
                if (isset($_POST['em_user_key'])) {
                    $em_user_key = sanitize_text_field($_POST['em_user_key']);
                }

                if (!$em_plugin_network_activate) {
                    log_message('debug', "[enginemailer][EmAdmin->mainMenuPage()] Update setting, user_id:$em_user_id user_key:$em_user_key");
                    update_option(EM_OPTION_EM_USER_ID, $em_user_id);
                    update_option(EM_OPTION_EM_USER_KEY, $em_user_key);

                } else {
                    log_message('debug', "[enginemailer][EmAdmin->mainMenuPage()] Ignore update setting in network activate mode, user_id:$em_user_id user_key:$em_user_key");

                }

            } else if ($action == 'testmail') {
                // Send test email

            } else {

            }
            
        }

        $nonce = wp_nonce_field('em_settings', 'em_settings_nonce', true, false);
        $blog_id = get_current_blog_id();

        $params['nonce'] = $nonce;

        switch ($tab) {
            case 'testmail':
                echo $this->twig->render('settings_testmail.tpl.html', $params);
                break;

            default:
                if ($em_plugin_network_activate) {
                    // When plugin is network activate, get the settings from network option
                        
                    $params[EM_OPTION_EM_USER_ID] = get_site_option(EM_OPTION_EM_USER_ID);
                    $params[EM_OPTION_EM_USER_KEY] = get_site_option(EM_OPTION_EM_USER_KEY);  
                    
                } else {
                    $params[EM_OPTION_EM_USER_ID] = get_option(EM_OPTION_EM_USER_ID);
                    $params[EM_OPTION_EM_USER_KEY] = get_option(EM_OPTION_EM_USER_KEY);   
    
                }
    
                echo $this->twig->render('settings.tpl.html', $params);
                break;
        }

    }

    public function addActionLinks($links) {
        
        $mylinks = array(
            '<a href="' . menu_page_url(EM_MENU_SLUG, false) . '">Settings</a>',
        );

        return array_merge( $links, $mylinks );
    }

}
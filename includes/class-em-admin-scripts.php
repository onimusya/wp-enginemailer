<?php

class EmAdminScripts {
    public function __construct() {

        add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
    }

    public function enqueueScripts($hook) {
        global $post;

        log_message("debug", "[enginemailer][enqueueScripts()] hook:$hook");

        $enable = false;

        if (($hook == 'toplevel_page_' . EM_MENU_SLUG)) {
            $enable = true;
        } 

        if ($enable) {
            log_message("debug", "[enginemailer][enqueueScripts()] enqueue EngineMailer Plugin styles & scripts");

            // Our Settings Page
            wp_enqueue_style('em_boostrap', EM_URL . 'assets/vendor/bootstrap/css/hm-bootstrap.css', false, '3.3.7', false);
            wp_enqueue_style('em_datepicker', EM_URL . 'assets/vendor/bootstrap-datepicker/css/bootstrap-datepicker3.standalone.css', false, '1.0.0', false);
            wp_enqueue_style('em_select2', EM_URL . 'assets/vendor/select2/css/select2.css', false, '1.0.0', false);
            wp_enqueue_style('em_select2_bootstrap', EM_URL . 'assets/vendor/select2/css/hm-select2-bootstrap.css', false, '1.0.0', false);
            wp_enqueue_style('em_query_builder', EM_URL . 'assets/vendor/jquery.query-builder/css/query-builder.default.css', false, '2.4.5', false);

            // Style for JQuery Menu Builder
            wp_enqueue_style('em_font_awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css', false, '4.2.0', false);
            wp_enqueue_style('em_iconpicker', EM_URL . 'assets/vendor/jquery.menu-editor/bs-iconpicker/css/bootstrap-iconpicker.min.css', false, '1.7.0', false);
            wp_enqueue_style('em_nestable', EM_URL . 'assets/vendor/jquery.nestable/jquery.nestable.min.css', false, '2.0.0', false);            

            
            wp_enqueue_style('em_app', EM_URL . 'assets/css/em.css', false, time(), false);
            wp_enqueue_style('em_admin', EM_URL . 'assets/css/em_admin.css', false, time(), false);

            wp_enqueue_media();
            wp_enqueue_script('jquery');
	        wp_enqueue_script('em_boostrap_js', EM_URL . 'assets/vendor/bootstrap/js/bootstrap.min.js', array('jquery'), '3.3.7', false);
            wp_enqueue_script('em_moment_js', EM_URL . 'assets/vendor/moment/moment.min.js', array('jquery'), '2.18.1', false);
            wp_enqueue_script('em_datepicker_js', EM_URL . 'assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js', array('jquery'), '1.0.0', false);
            //wp_enqueue_script('lb_select2_js', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js', array('jquery'), '1.0.0', false);
            wp_enqueue_script('em_select2_js', EM_URL .'assets/vendor/select2/js/select2.full.js', array('jquery'), '1.0.0', false);
            wp_enqueue_script('em_query_builder_js', EM_URL . 'assets/vendor/jquery.query-builder/js/query-builder.standalone.js', '2.4.5', false);
            
            wp_enqueue_script('em_menu_builder_js', EM_URL . 'assets/vendor/jquery.menu-editor/jquery-menu-editor.js', '1.0.0', false);
            wp_enqueue_script('em_iconpicker_iconset_js', EM_URL . 'assets/vendor/jquery.menu-editor/bs-iconpicker/js/iconset/iconset-fontawesome-4.2.0.min.js', '4.2.0', false);
            wp_enqueue_script('em_iconpicker_js', EM_URL . 'assets/vendor/jquery.menu-editor/bs-iconpicker/js/bootstrap-iconpicker.min.js', '1.7.0', false);            
            wp_enqueue_script('em_nestable_js', EM_URL . 'assets/vendor/jquery.nestable/jquery.nestable.min.js', '2.0.0', false);

            wp_enqueue_script('em_admin_js', EM_URL .'assets/js/em_admin.js', array('jquery'), time(), false);

            $nonce = wp_create_nonce(EM_NONCE);
            wp_localize_script( 'em_admin_js', 'em_ajax_obj', array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => $nonce,
            ));
            
            return;            
        }
    }    

}
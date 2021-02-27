<?php
/*
Author: Luis del Cid
Author URI: https://github.com/luisdelcid
Description: A collection of useful Contact Form 7 functions for your WordPress theme's functions.php.
Domain Path:
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Network: true
Plugin Name: LDC CF7 Functions
Plugin URI: https://github.com/luisdelcid/ldc-cf7-functions
Text Domain: ldc-cf7-functions
Version: 0.2.27
*/

if(defined('ABSPATH')){
    add_action('plugins_loaded', function(){
        if(did_action('ldc_functions_loaded')){
            ldc_build_update_checker('https://github.com/luisdelcid/ldc-cf7-functions', __FILE__, 'ldc-cf7-functions');
        }
        require_once(plugin_dir_path(__FILE__) . 'class-ifcf7.php');
        require_once(plugin_dir_path(__FILE__) . 'class-ifcf7-login.php');
        require_once(plugin_dir_path(__FILE__) . 'class-ifcf7.signup');
        IFCF7::load();
    }, 11);
}

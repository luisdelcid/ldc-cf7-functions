<?php

final class IFCF7 {

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //
    // public
    //
    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function add_actions(){
        add_action('wpcf7_before_send_mail', [__CLASS__, 'wpcf7_before_send_mail'], 10, 3);
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function add_filters(){
        add_filter('wpcf7_validate_email', [__CLASS__, 'wpcf7_validate_email'], 10, 2);
        add_filter('wpcf7_validate_text', [__CLASS__, 'wpcf7_validate_text'], 10, 2);
        add_filter('wpcf7_validate_password', [__CLASS__, 'wpcf7_validate_password'], 10, 2);
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function load(){
    	self::add_actions();
        self::add_filters();
        self::replace_shortcodes();
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function replace_shortcodes(){
        remove_shortcode('contact-form');
        remove_shortcode('contact-form-7');
        add_shortcode('contact-form', [__CLASS__, 'wpcf7_contact_form_tag_func']);
        add_shortcode('contact-form-7', [__CLASS__, 'wpcf7_contact_form_tag_func']);
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function wpcf7_before_send_mail($contact_form, $abort, $submission){
        if($contact_form->is_true('ldc_signup')){
            IFCF7_Signup::before_send_mail($contact_form, $abort, $submission);
        }
        if($contact_form->is_true('ldc_login')){
            IFCF7_Login::before_send_mail($contact_form, $abort, $submission);
        }
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function wpcf7_contact_form_tag_func($atts, $content = null, $shortcode_tag = ''){
    	$html = wpcf7_contact_form_tag_func($atts, $content, $shortcode_tag);
        $contact_form = wpcf7_get_current_contact_form();
        if(!$contact_form){
            return $html;
        }
        $submission = WPCF7_Submission::get_instance();
        if(!$submission){
            return $result;
        }
        if($contact_form->is_true('ldc_signup')){
            return IFCF7_Signup::filter_html($html, $contact_form, $submission);
        }
        if($contact_form->is_true('ldc_login')){
            return IFCF7_Login::filter_html($html, $contact_form, $submission);
        }
    	return $html;
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function wpcf7_validate_email($result, $tag){
        $contact_form = wpcf7_get_current_contact_form();
        if(!$contact_form){
            return $result;
        }
        $submission = WPCF7_Submission::get_instance();
        if(!$submission){
            return $result;
        }
        if($contact_form->is_true('ldc_signup')){
            return IFCF7_Signup::validate_email($result, $tag, $contact_form, $submission);
        }
        if($contact_form->is_true('ldc_login')){
            return IFCF7_Login::validate_email($result, $tag, $contact_form, $submission);
        }
        return $result;
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function wpcf7_validate_text($result, $tag){
        $contact_form = wpcf7_get_current_contact_form();
        if(!$contact_form){
            return $result;
        }
        $submission = WPCF7_Submission::get_instance();
        if(!$submission){
            return $result;
        }
        if($contact_form->is_true('ldc_signup')){
            return IFCF7_Signup::validate_text($result, $tag, $contact_form, $submission);
        }
        if($contact_form->is_true('ldc_login')){
            return IFCF7_Login::validate_text($result, $tag, $contact_form, $submission);
        }
        return $result;
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function wpcf7_validate_password($result, $tag){
        $contact_form = wpcf7_get_current_contact_form();
        if(!$contact_form){
            return $result;
        }
        $submission = WPCF7_Submission::get_instance();
        if(!$submission){
            return $result;
        }
        if($contact_form->is_true('ldc_signup')){
            return IFCF7_Signup::validate_password($result, $tag, $contact_form, $submission);
        }
        if($contact_form->is_true('ldc_login')){
            return IFCF7_Login::validate_password($result, $tag, $contact_form, $submission);
        }
        return $result;
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

}

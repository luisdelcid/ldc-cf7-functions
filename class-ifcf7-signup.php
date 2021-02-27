<?php

final class IFCF7_Signup {

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //
    // public
    //
    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function before_send_mail($contact_form, $abort, $submission){
        $user_email = (string) $submission->get_posted_data('ldc_user_email');
        if(!$user_email){
            return;
        }
        $user = get_user_by('email', $user_email);
        if($user){
            return;
        }
        if($contact_form->is_true('ldc_email_as_username')){
            $user_login = (string) $submission->get_posted_data('ldc_user_email');
        } else {
            $user_login = (string) $submission->get_posted_data('ldc_user_login');
        }
        if(!$user_login){
            return;
        }
        $user = get_user_by('login', $user_login);
        if($user){
            return;
        }
        $user_password = (string) $submission->get_posted_data('ldc_user_password');
        if(!$user_password){
            return;
        }
        $user_password_confirm = (string) $submission->get_posted_data('ldc_user_password_confirm');
        if($user_password_confirm and $user_password_confirm != $user_password){
            return;
        }
        wp_create_user($user_login, $user_password, $user_email);
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function filter_html($html, $contact_form){
        $invalid = [];
        $missing = [];
        $tags = wp_list_pluck($contact_form->scan_form_tags(), 'type', 'name');
        if(!array_key_exists('ldc_user_email', $tags)){
            $missing[] = 'ldc_user_email';
        }
        if(!$contact_form->is_true('ldc_email_as_username')){
            if(!array_key_exists('ldc_user_login', $tags)){
                $missing[] = 'ldc_user_login';
            }
        }
        if(!array_key_exists('ldc_user_password', $tags)){
            $missing[] = 'ldc_user_password';
        }
        if($missing){
            return current_user_can('manage_options') ? sprintf(__('Missing parameter(s): %s'), implode(', ', $missing)) : __('Something went wrong.');
        }
        if($tags['ldc_user_email'] != 'email*'){
            $invalid[] = 'ldc_user_email';
        }
        if(!$contact_form->is_true('ldc_email_as_username')){
            if($tags['ldc_user_login'] != 'text*'){
                $invalid[] = 'ldc_user_login';
            }
        }
        if($tags['ldc_user_password'] != 'password*'){
            $invalid[] = 'ldc_user_password';
        }
        if($invalid){
            return current_user_can('manage_options') ? sprintf(__('Invalid parameter(s): %s'), implode(', ', $invalid)) : __('Something went wrong.');
        }
        if(is_user_logged_in() and !current_user_can('create_users')){
            return __('Sorry, you are not allowed to create new users.') . ' ' . __('You need a higher level of permission.');
        }
        return $html;
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function validate_email($result, $tag, $contact_form, $submission){
        if($tag->name != 'ldc_user_email'){
            return $result;
        }
        $user_email = $submission->get_posted_data('ldc_user_email');
        if(!$user_email){
            return $result;
        }
        $message = __('<strong>Error</strong>: This email is already registered. Please choose another one.');
        $user = get_user_by('email', $user_email);
        if($user){
            $result->invalidate($tag, wp_strip_all_tags($message));
        }
        if($contact_form->is_true('ldc_email_as_username')){
            $message = __('<strong>Error</strong>: This username is already registered. Please choose another one.');
            $user = get_user_by('login', $user_email);
            if($user){
                $result->invalidate($tag, wp_strip_all_tags($message));
            }
        }
        return $result;
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function validate_text($result, $tag, $contact_form, $submission){
        if($tag->name != 'ldc_user_login'){
            return $result;
        }
        $user_login = $submission->get_posted_data('ldc_user_login');
        if(!$user_login){
            return $result;
        }
        $message = __('<strong>Error</strong>: This username is already registered. Please choose another one.');
        $user = get_user_by('login', $user_login);
        if($user){
            $result->invalidate($tag, wp_strip_all_tags($message));
        }
        return $result;
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function validate_password($result, $tag, $contact_form, $submission){
        if($tag->name != 'ldc_user_password_confirm'){
            return $result;
        }
        $user_password_confirm = $submission->get_posted_data('ldc_user_password_confirm');
        if(!$user_password_confirm){
            return $result;
        }
        $message = __('The passwords do not match.');
        $user_password = $submission->get_posted_data('ldc_user_password');
        if($user_password_confirm != $user_password){
            $result->invalidate($tag, wp_strip_all_tags($message));
        }
        return $result;
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

}

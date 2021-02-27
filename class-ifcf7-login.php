<?php

final class IFCF7_Login {

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //
    // public
    //
    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function before_send_mail($contact_form, $abort, $submission){
        if(is_user_logged_in()){
            return;
        }
        if($contact_form->is_true('ldc_email_as_username')){
            $user_login = (string) $submission->get_posted_data('ldc_user_email');
            if(!$user_login){
                return;
            }
            $user = get_user_by('email', $user_login);
            if(!$user){
                return;
            }
        } else {
            $user_login = (string) $submission->get_posted_data('ldc_user_login');
            if(!$user_login){
                return;
            }
            if(is_email($user_login)){
                $user = get_user_by('email', $user_login);
            } else {
                $user = get_user_by('login', $user_login);
            }
            if(!$user){
                return;
            }
        }
        $user_password = (string) $submission->get_posted_data('ldc_user_password');
        if(!$user_password){
            return;
        }
        if(!wp_check_password($user_password, $user->data->user_pass, $user->ID)){
            return;
        }
        $remember = (bool) $submission->get_posted_data('ldc_remember');
        wp_signon([
            'remember' => $remember,
            'user_login' => $user_login,
            'user_password' => $user_password,
        ]);
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function filter_html($html, $contact_form){
        $invalid = [];
        $missing = [];
        $tags = wp_list_pluck($contact_form->scan_form_tags(), 'type', 'name');
        if($contact_form->is_true('ldc_email_as_username')){
            if(!array_key_exists('ldc_user_email', $tags)){
                $missing[] = 'ldc_user_email';
            }
        } else {
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
        if($contact_form->is_true('ldc_email_as_username')){
            if($tags['ldc_user_email'] != 'email*'){
                $invalid[] = 'ldc_user_email';
            }
        } else {
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
        if(is_user_logged_in()){
            return __('You are logged in already. No need to register again!');
        }
        return $html;
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function validate_email($result, $tag, $contact_form, $submission){
        if(!$contact_form->is_true('ldc_email_as_username')){
            return $result;
        }
        if($tag->name != 'ldc_user_email'){
            return $result;
        }
        $user_login = $submission->get_posted_data('ldc_user_email');
        if(!$user_login){
            return $result;
        }
        $message = __('Unknown email address. Check again or try your username.');
        $user = get_user_by('email', $user_login);
        if(!$user){
            $result->invalidate($tag, wp_strip_all_tags($message));
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
        if(is_email($user_login)){
            $message = __('Unknown email address. Check again or try your username.');
            $user = get_user_by('email', $user_login);
        } else {
            $message = __('Unknown username. Check again or try your email address.');
            $user = get_user_by('login', $user_login);
        }
        if(!$user){
            $result->invalidate($tag, wp_strip_all_tags($message));
        }
        return $result;
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function validate_password($result, $tag, $contact_form, $submission){
        if($tag->name != 'ldc_user_password'){
            return $result;
        }
        if($contact_form->is_true('ldc_email_as_username')){
            $user_login = $submission->get_posted_data('ldc_user_email');
            if(!$user_login){
                return $result;
            }
            $message = sprintf(__('<strong>Error</strong>: The password you entered for the email address %s is incorrect.'), '<strong>' . $user_login . '</strong>');
            $user = get_user_by('email', $user_login);
            if(!$user){
                return $result;
            }
        } else {
            $user_login = $submission->get_posted_data('ldc_user_login');
            if(!$user_login){
                return $result;
            }
            if(is_email($user_login)){
                $message = sprintf(__('<strong>Error</strong>: The password you entered for the email address %s is incorrect.'), '<strong>' . $user_login . '</strong>');
                $user = get_user_by('email', $user_login);
            } else {
                $message = sprintf(__('<strong>Error</strong>: The password you entered for the username %s is incorrect.'), '<strong>' . $user_login . '</strong>');
                $user = get_user_by('login', $user_login);
            }
            if(!$user){
                return $result;
            }
        }
        $user_password = $submission->get_posted_data('ldc_user_password');
        if(!$user_password){
            return $result;
        }
        if(!wp_check_password($user_password, $user->data->user_pass, $user->ID)){
            $result->invalidate($tag, wp_strip_all_tags($message));
        }
        return $result;
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

}

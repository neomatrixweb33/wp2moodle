<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    
}

require_once($CFG->libdir.'/authlib.php');


class auth_plugin_wp2moodle extends auth_plugin_base {

   
    public function auth_plugin_wp2moodle() {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct();
    }
    function __construct() {
        $this->authtype = 'wp2moodle';
        $this->config = get_config('auth_wp2moodle');
    }

   
    function user_login ($username, $password = null) {
        global $CFG, $DB;
        if ($password == null || $password == '') { return false; }
        if ($user = $DB->get_record('user', array('username'=>$username, 'password'=>$password, 'mnethostid'=>$CFG->mnet_localhost_id))) {
                return true;
            }
        return false;
    }

    function prevent_local_passwords() {
        return true;
    }

   
    function is_internal() {
        return false;
    }

   
    function can_change_password() {
        return false;
    }

   
    function change_password_url() {
        return null;
    }

   
    function can_reset_password() {
        return false;
    }

    function logoutpage_hook() {
        global $SESSION;
        set_moodle_cookie('nobody');
        require_logout();
        if (isset($this->config->logoffurl)) {
            if (ob_get_level() !== 0) ob_end_clean(); 
            
            header("Location: " . $this->config->logoffurl, true, 301);
            exit; 
        }
    }


   
    function config_form($config, $err, $user_fields) {
        include "config.html";
    }

   
   function process_config($config) {
        
        if (!isset($config->sharedsecret)) {
            $config->sharedsecret = 'this is not a secure key, change it';
        }
        if (!isset($config->timeout)) {
            $config->timeout = '5';
        }
        if (!isset($config->logoffurl)) {
            $config->logoffurl = '';
        }
        if (!isset($config->autoopen)) {
            $config->autoopen = 'no';
        }
        if (!isset($config->updateuser)) {
            $config->updateuser = 'yes';
        }
        if (!isset($config->redirectnoenrol)) {
            $config->redirectnoenrol = 'no';
        }
        if (!isset($config->idprefix)) {
            $config->idprefix = '';
        }

        
        set_config('sharedsecret', $config->sharedsecret, 'auth_wp2moodle');
        set_config('logoffurl', $config->logoffurl, 'auth_wp2moodle');
        set_config('timeout', $config->timeout, 'auth_wp2moodle');
        set_config('autoopen', $config->autoopen, 'auth_wp2moodle');
        set_config('updateuser', $config->updateuser, 'auth_wp2moodle');
        set_config('redirectnoenrol', $config->redirectnoenrol, 'auth_wp2moodle');
        set_config('idprefix', $config->idprefix, 'auth_wp2moodle');

        return true;
    }

}



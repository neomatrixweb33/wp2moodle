<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    $wp2myesno = array(
        new lang_string('no'),
        new lang_string('yes'),
    );

    $wp2m_fields = ['idnumber','email','username'];

    $settings->add(new admin_setting_heading('auth_wp2moodle/pluginname',
            '',
            new lang_string('settings_description', 'auth_wp2moodle')));

    $settings->add(new admin_setting_configtext('auth_wp2moodle/sharedsecret',
        new lang_string('settings_sharedsecret','auth_wp2moodle'),
    	new lang_string('settings_sharedsecret_desc', 'auth_wp2moodle'),
    	'this is not a secure key, change it'
    ));

    $settings->add(new admin_setting_configtext_with_maxlength('auth_wp2moodle/timeout',
        new lang_string('settings_timeout','auth_wp2moodle'),
    	new lang_string('settings_timeout_desc', 'auth_wp2moodle'),
    	'5',
    	PARAM_RAW, 4, 3
    ));

    $settings->add(new admin_setting_configtext('auth_wp2moodle/logoffurl',
        new lang_string('settings_logoffurl','auth_wp2moodle'),
    	new lang_string('settings_logoffurl_desc', 'auth_wp2moodle'),
    	''
    ));

    $settings->add(new admin_setting_configselect('auth_wp2moodle/matchfield',
        new lang_string('settings_matchfield', 'auth_wp2moodle'),
        new lang_string('settings_matchfield_desc', 'auth_wp2moodle'),
        'idnumber',
        $wp2m_fields
    ));

    $settings->add(new admin_setting_configselect('auth_wp2moodle/autoopen',
        new lang_string('settings_autoopen', 'auth_wp2moodle'),
        new lang_string('settings_autoopen_desc', 'auth_wp2moodle'),
        1,
        $wp2myesno
    ));

    $settings->add(new admin_setting_configselect('auth_wp2moodle/updateuser',
        new lang_string('settings_updateuser', 'auth_wp2moodle'),
        new lang_string('settings_updateuser_desc', 'auth_wp2moodle'),
        1,
        $wp2myesno
    ));

    $settings->add(new admin_setting_configselect('auth_wp2moodle/redirectnoenrol',
        new lang_string('settings_redirectnoenrol', 'auth_wp2moodle'),
        new lang_string('settings_redirectnoenrol_desc', 'auth_wp2moodle'),
        0,
        $wp2myesno
    ));

    $settings->add(new admin_setting_configtext('auth_wp2moodle/firstname',
        new lang_string('settings_firstname','auth_wp2moodle'),
    	new lang_string('settings_firstname_desc', 'auth_wp2moodle'),
    	'empty-firstname'
    ));

    $settings->add(new admin_setting_configtext('auth_wp2moodle/lastname',
        new lang_string('settings_lastname','auth_wp2moodle'),
    	new lang_string('settings_lastname_desc', 'auth_wp2moodle'),
    	'empty-lastname'
    ));

    $settings->add(new admin_setting_configtext('auth_wp2moodle/idprefix',
        new lang_string('settings_idprefix','auth_wp2moodle'),
    	new lang_string('settings_idprefix_desc', 'auth_wp2moodle'),
    	'wp2m'
    ));

   

}
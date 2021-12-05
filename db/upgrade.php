<?php

defined('MOODLE_INTERNAL') || die();

function xmldb_auth_wp2moodle_upgrade($oldversion) {
    global $CFG;

    if ($oldversion < 2020111500) {
        set_config('settings_matchfield', 'idnumber', 'auth_wp2moodle');
        upgrade_plugin_savepoint(true, 2020111500, 'auth', 'wp2moodle');
    }

    return true;
}

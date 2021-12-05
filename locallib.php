<?php

function wp2m_base64_decode($b64) {
	return base64_decode(str_replace(array('-','_'),array('+','/'),$b64));
}
function wp2m_is_base64($string) {
    $decoded = base64_decode($string, true);
    
    if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $string)) return false;
    
    if (!base64_decode($string, true)) return false;
    
    if (base64_encode($decoded) != $string) return false;
    return true;
}

function decrypt_string($data, $key) {
	if ( wp2m_is_base64($key)) {
		$encryption_key = base64_decode($key);
	} else {
		$encryption_key = $key;
	}
	list($encrypted_data, $iv) = explode('::', wp2m_base64_decode($data), 2);
	return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
}


function get_key_value($string, $key) {
	$list = explode( '&', str_replace( '&amp;', '&', $string));
	foreach ($list as $pair) {
		$item = explode( '=', $pair);
		if (strtolower($key) == strtolower($item[0])) {
			return urldecode($item[1]); 
		}
	}
	return "";
}


function truncate_user($userobj) {
	$user_array = truncate_userinfo((array) $userobj);
	$obj = new stdClass();
	foreach($user_array as $key=>$value) {
		$obj->{$key} = $value;
	}
	return $obj;
}



function enrol_into_course($courseid, $userid, $roleid = 5) {
	global $DB;
	$manualenrol = enrol_get_plugin('manual'); 
	$enrolinstance = $DB->get_record('enrol',
		array('courseid'=>$courseid,
			'status'=>ENROL_INSTANCE_ENABLED,
			'enrol'=>'manual'
		),
		'*',
		MUST_EXIST
	);
	
	return $manualenrol->enrol_user($enrolinstance, $userid, $roleid); 
}

function check_user_email($email) {
	global $SESSION;
    if (email_is_not_allowed($email)) {

        $failurereason = AUTH_LOGIN_FAILED;
        $event = \core\event\user_login_failed::create(['other' => ['username' => $username,
                                                                    'reason' => $failurereason]]);
        $event->trigger();
        
        $reason = get_string('loginerror_invaliddomain', 'auth_wp2moodle');
        $errormsg = get_string('notloggedindebug', 'auth_wp2moodle', $reason);
        $SESSION->loginerrormsg = $errormsg;
        redirect(new moodle_url('/login/index.php'));
	}
}

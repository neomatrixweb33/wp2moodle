<?php

global $CFG, $USER, $SESSION, $DB;

require('../../config.php');
require_once('locallib.php');
require_once($CFG->libdir.'/moodlelib.php');
require_once($CFG->dirroot.'/cohort/lib.php');
require_once($CFG->dirroot.'/group/lib.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot."/lib/enrollib.php");

$SESSION->wantsurl = $CFG->wwwroot.'/';

$PASSTHROUGH_KEY = get_config('auth_wp2moodle', 'sharedsecret');
if (!isset($PASSTHROUGH_KEY)) {
	die("Sorry, this plugin has not yet been configured. Please contact the Moodle administrator for details.");
}
if (!is_enabled_auth('wp2moodle')) {
	die('Sorry this plugin is not enabled. Please contact the Moodle administrator for details.');
}

$rawdata = $_GET['data'];
if (!empty($_GET)) {

	$auth 				= 'wp2moodle';

	
	$userdata 			= decrypt_string($rawdata, $PASSTHROUGH_KEY);

	
	$timeout 			= (integer) get_config('auth_wp2moodle', 'timeout');

	
	$mf 				= intval(get_config('auth_wp2moodle', 'matchfield') ?: 0);
	$matchvalue 			= "";
	$courseId 			= 0;

	switch ($mf) {
		case 1: $matchfield = "email"; break;
		case 2: $matchfield = "username"; break;
		default: $matchfield = "idnumber";
	}

	
	$default_firstname 	= get_config('auth_wp2moodle', 'firstname') ?: "no-firstname";
	$default_lastname 	= get_config('auth_wp2moodle', 'lastname') ?: "no-lastname";

	
	$idnumber_prefix 	= get_config('auth_wp2moodle', 'idprefix') ?: "wp2m";
	$redirectnoenrol	= get_config('auth_wp2moodle', 'redirectnoenrol');

	
	$timestamp 			= (integer) get_key_value($userdata, "stamp"); 
	$theirs				= new DateTime("@$timestamp"); 
	$diff 				= floatval(date_diff(date_create("now"), $theirs)->format("%i")); 

	
	if ($timestamp > 0 && ($timeout == 0 || $diff <= $timeout)) { 

		$username 			= trim(strtolower(get_key_value($userdata, "username"))); 
		$hashedpassword 	= get_key_value($userdata, "passwordhash");
		$firstname 			= get_key_value($userdata, "firstname") ?: $default_firstname;
		$lastname 			= get_key_value($userdata, "lastname") ?: $default_lastname;
		$email 				= get_key_value($userdata, "email");
		$idnumber 			= $idnumber_prefix . get_key_value($userdata, "idnumber"); 
		$cohort_idnumbers 	= get_key_value($userdata, "cohort"); 
		$group_idnumbers 	= get_key_value($userdata, "group");
		$course_idnumbers 	= get_key_value($userdata, "course");
		$activity 			= (integer) get_key_value($userdata, "activity"); 
		$cmid 				= (integer) get_key_value($userdata, "cmid"); 

		
		$updatefields 		= (get_config('auth_wp2moodle', 'updateuser') === '1');
		$wantsurl 			= get_key_value($userdata, "url");

		
		switch ($matchfield) {
			case "username": $matchvalue = $username; break;
			case "email":    $matchvalue = $email;    break;
			default:		 $matchvalue = $idnumber; $matchfield = "idnumber";
		}

		
		if ($DB->record_exists('user', [$matchfield => $matchvalue])) {
			$updateuser = get_complete_user_data($matchfield, $matchvalue);
			if ($updatefields) {
				check_user_email($email);
				$updateuser->username 		= $username;
				$updateuser->email 			= $email;
				$updateuser->idnumber 		= $idnumber;
				$updateuser->firstname 		= $firstname;
				$updateuser->lastname 		= $lastname;
				$updateuser 				= truncate_user($updateuser); 
				$updateuser->timemodified 	= time(); 

				$DB->update_record('user', $updateuser);
				\core\event\user_updated::create_from_userid($updateuser->id)->trigger();
			}
			$user = get_complete_user_data('id', $updateuser->id);
		} else {
			$authplugin = get_auth_plugin($auth);
			check_user_email($email);
			$updateuser = new stdClass();
			if ($newinfo = $authplugin->get_userinfo($username)) {
				$newinfo = truncate_user($newinfo);
				foreach ($newinfo as $key => $value){
					$updateuser->$key = $value;
				}
			}
			$updateuser->city 			= '';
			$updateuser->auth 			= $auth;
			$updateuser->policyagreed 	= 1;
			$updateuser->idnumber 		= $idnumber;
			$updateuser->username 		= $username;
			$updateuser->password 		= md5($hashedpassword); 
			$updateuser->firstname 		= $firstname;
			$updateuser->lastname 		= $lastname;
			$updateuser->email 			= $email;
			$updateuser->lang 			= $CFG->lang;
			$updateuser->confirmed 		= 1; 
			$updateuser->lastip 		= getremoteaddr();
			$updateuser->timecreated 	= time();
			$updateuser->timemodified 	= $updateuser->timecreated;
			$updateuser->mnethostid 	= $CFG->mnet_localhost_id;
			$updateuser = truncate_user($updateuser);
			$updateuser->id = $DB->insert_record('user', $updateuser);
			\core\event\user_created::create_from_userid($updateuser->id)->trigger();
			$user = get_complete_user_data('id', $updateuser->id);
		}

		
		if (!empty($cohort_idnumbers)) {
			$ids = array_map('trim', explode(',', $cohort_idnumbers));
			foreach ($ids as $cohort) {
				if ($DB->record_exists('cohort', array('idnumber'=>$cohort))) {
					$cohortrow = $DB->get_record('cohort', array('idnumber'=>$cohort));
					if (!$DB->record_exists('cohort_members', array('cohortid'=>$cohortrow->id, 'userid'=>$user->id))) {
						
						cohort_add_member($cohortrow->id, $user->id);
					}

					
					if (get_config('auth_wp2moodle', 'autoopen') === '1')  {
						$courseId = $DB->get_field('enrol','courseid',['enrol'=>'cohort','customint1'=>$cohortrow->id,'status'=>0], IGNORE_MULTIPLE);
					}
				}
			}
		}

		
		if (!empty($group_idnumbers) && $redirectnoenrol === '0') {
			$ids = array_map('trim', explode(',', $group_idnumbers));
			foreach ($ids as $group) {
				if ($DB->record_exists('groups', array('idnumber'=>$group))) {
					$grouprow = $DB->get_record('groups', array('idnumber'=>$group));
					$courseId = $grouprow->courseid;
					enrol_into_course($courseId, $user->id);
					if (!$DB->record_exists('groups_members', array('groupid'=>$grouprow->id, 'userid'=>$user->id))) {
						
						groups_add_member($grouprow->id, $user->id);
					}
				}
			}
		}

		
		if (!empty($course_idnumbers)) {
			$studentrow = $DB->get_record('role', array('shortname'=>'student'));
			$ids = array_map('trim', explode(',', $course_idnumbers));
			foreach ($ids as $course) {
				if ($DB->record_exists('course', array('idnumber'=>$course))) {
					$courserow = $DB->get_record('course', array('idnumber'=>$course));
					if ($redirectnoenrol === '0') { 
						if (!enrol_try_internal_enrol($courserow->id, $user->id, $studentrow->id)) {
							continue;
						}
					}
					$courseId = $courserow->id;
				}
			}
		}

		$courseId = intval($courseId);

		
		if (get_config('auth_wp2moodle', 'autoopen') === '1')  {
			if ($courseId > 0) {
				$SESSION->wantsurl = new moodle_url('/course/view.php', array('id'=>$courseId));
			}
			
			if ($courseId == 0 && $cmid > 0) $courseId = $DB->get_field('course_modules','course', array('id' => $cmid));

			
			if ($courseId != 0 && ($activity > 0 || $cmid > 0)) {
				$course = get_course($courseId);
				$modinfo = get_fast_modinfo($course);
				$index = 0;
				foreach ($modinfo->get_cms() as $cmindex => $cm) {
					if ($cm->uservisible && $cm->available) {
						
						if (($index === $activity && $cmid === 0) || ($activity === 0 && $cmid === $cmindex)) {
							$SESSION->wantsurl = new moodle_url("/mod/" . $cm->modname . "/view.php", array("id" => $cmindex));
							break;
						}
						$index += 1;
					}
				}
			}
		}

		
		if (!empty($wantsurl)) {
			$SESSION->wantsurl = new moodle_url(rawurldecode($wantsurl));
		}

		
		$authplugin = get_auth_plugin($auth);
		if ($authplugin->user_login($user->username, $user->password)) {
			$user->loggedin = true;
			$user->site     = $CFG->wwwroot;
			complete_user_login($user);
		}
	}
}


redirect($SESSION->wantsurl);

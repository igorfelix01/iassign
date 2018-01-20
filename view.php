<?php

/**
 * This php script contains all the stuff to display iAssign.
 * 
 * @author Patricia Alves Rodrigues
 * @author Leo^nidas O. Branda~o
 * @version v 1.0 2012/10/16
 * @package mod_iassign
 * @since 2010/09/27
 * @copyright iMatica (<a href="http://www.matematica.br">iMath</a>) - Computer Science Dep. of IME-USP (Brazil)
 * 
 * <b>License</b> 
 *  - http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once("../../config.php");
require_once("lib.php");
require_once($CFG->libdir . '/completionlib.php');
require_once($CFG->libdir . '/plagiarismlib.php');

//Parameters GET e POST (parâmetros GET e POST)
$id = optional_param('id', 0, PARAM_INT); // Course Module ID
$a = optional_param('a', 0, PARAM_INT); //  iAssign instance id (from table 'iassign')

$url = new moodle_url('/mod/iassign/view.php'); // novo

if ($id) {
    // ./lib/datalib.php : function get_coursemodule_from_id($modulename, $cmid, $courseid=0, $sectionnum=false, $strictness=IGNORE_MISSING): returns 'course_modules.*' and 'modules.name'
    if (!$cm = get_coursemodule_from_id('iassign', $id)) { // Moodle function 'get_coursemodule_from_id(...)' returns the object from table '
        print_error('invalidcoursemodule');
    }

    if (!$iassign = $DB->get_record("iassign", array("id" => $cm->instance))) { // 'course_modules.instance = iassign.id'
        print_error('invalidid', 'iassign');
    }

    if (!$course = $DB->get_record("course", array("id" => $iassign->course))) {
        print_error('coursemisconf', 'iassign');
    }
    $url->param('id', $id);
} else {
    if (!$iassign = $DB->get_record("iassign", array("id" => $a))) {
        print_error('invalidid', 'iassign');
    }
    if (!$course = $DB->get_record("course", array("id" => $iassign->course))) {
        print_error('coursemisconf', 'iassign');
    }
    if (!$cm = get_coursemodule_from_instance("iassign", $iassign->id, $course->id)) {
        print_error('invalidcoursemodule');
    }
    $url->param('a', $a);
}

//print_r($url);

$PAGE->set_url($url);

require_login($course, true, $cm);

$PAGE->set_title(format_string($iassign->name));
$PAGE->set_heading($course->fullname);

// Mark viewed by user (if required)
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

$write_solution = 1;
$iassigninstance = new iassign($iassign, $cm, $course, array('write_solution' => 1));
$iassigninstance->view(); // ./mod/iassign/locallib.php : actually who display the iAssign whose id is '$id'! (this function ignores parameters)
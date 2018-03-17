<?php

/**
 * This class provides all the functionality for an ia (interactive activities).
 * 
 * Release Notes:
 * - v 4.9.1 2017/11/02
 *   + Fixed a bug with iGeom script - the view was not loading the CEO with activity (resulting in an error "Erro ao gravar na base de dados")
 *   + function view_iassign_current(): it were inserted 2 'if(substr($ilm_name,0,5)=="igeom") if($iassign_statement_activity_item->special_param1==1) $lTA=true;' to call '$ilm->view_iLM(...lTA)'
 * 
 * - v 4.9 2017/03/13
 *   + Great number of changes to allow the use o iVProgH5 (e.g. function changed: from 'applet_ilm' to 'build_ilm_tags')
 * 
 * - v 4.8 2016/05/12
 *   + Function add_to_log() is deprecated it was then  rewritten  to the new events API
 *   + Resolved: the field "description" table "iassign_ilm" was being filled with the codes in HTML when importing magnet package and 
 *     due to this the language field was not being displayed because in place of the quotes was recorded in the & quot field. 
 *     ("$description_str = htmlentities(str_replace(array('<description>','</description>'), array('',''), $application_xml->description->asXML()));"
 *     replaced by
 *     "description_str = str_replace(array('<description>','</description>'), array('',''), $application_xml->description->asXML());")
 * - v 4.7 2016/02/17
 *   + Moodle 3.X: now iAssign is working fine under version 3.X (iassign/version.php: '$module->' changed to '$plugin->'; iassign/locallib.php: 'format_text(...)' replaced 'filter_text(...)')
 *   + Improved: now is possible to see the iGeom menus in preview (from iAssign Repository) - it depends on the version 1.3 of iAssign filter!
 *   + Improved: new names for 'form.input.MA_POST_Archive' and 'form.input.MA_POST_Value', now: 'iLM_PARAM_ArchiveContent' and 'iLM_PARAM_ActivityEvaluation'
 *   + BUG fixed: now it is fine the "online" edition of activities (in iAssign Repository) - inserted 'iLM_PARAM_Authoring' (iLM 2) and 'MA_POST_ArchiveTeacher' (iLM 1)
 *   + BUG fixed: it is possible to change the name of any file in iAssign Repository - problems was in 'optional_param(...)', 'PARAM_TEXT' replaced 'PARAM_ALPHANUMEXT'
 *   + BUG fixed: it is possible to duplicate any file in iAssign Repository - problems also in 'optional_param(...)', 'PARAM_TEXT' replaced 'PARAM_ALPHANUMEXT'
 *   + BUG fixed: now is possible to edit an iAssign activity with no new object been created (in iGeom: turn an example in exercise)
 * 
 * --------------- (abaixo estava no MOOC e NAO incorporei aqui!)
 * - v 4.6 2014/02/25
 *   + Fix bugs in filter function for open applets.
 * - v 4.5 2014/02/24
 *   + Fix bugs in params.
 *   + Insert new param type.
 * - v 4.4 2014/01/24
 *   + Allow select type of params.
 *   + Insert the use of applet params specific for activities.
 * - v 4.3 2014/01/23
 *   + Insert function for move activities for other iLM (ilm_settings::confirm_move_iassign, ilm_settings::move_iassign).
 * - v 4.2 2016/02/13
 *   + Fixed API usage to work fine under Moodle 3.X: ilm_editor_new()
 * --------------- (acima estava no MOOC)
 * 
 * - v 4.1 2013/12/13
 *   + Insert log in iAssign actions.
 *   + Allow use the language in iLM description (ilm_settings::new_file_ilm, ilm_settings::new_ilm, ilm_settings::edit_ilm, ilm_settings::copy_new_version_ilm, ilm_settings::add_edit_copy_ilm, iassign_language::get_description_lang, iassign_language::get_all_lang).
 *   + Insert class for Log actions in system.
 * - v 4.0 2013/10/31
 *   + Insert support of export iLM in zip packages (ilm_settings::export_ilm).
 *   + Insert support of import iLM from zip packages (ilm_settings::import_ilm).
 *   + Fix bugs in message alert in iassign title and remove message alert of the description by cache error.
 * - v 3.9 2013/10/25
 *   + Insert support of upgrade iLM.
 *   + Insert support for more than one extension in iLM.
 *   + Fix bugs in verion control.
 * - v 3.8 2013/09/19
 *   + Get data of general fields in iassign statement table (iassign::add_edit_iassign).
 * - v 3.7 2013/09/12
 *   + Change tag APPLET in all functions of module (ilm::view_iLM, ilm_manager::ilm_editor_new, ilm_manager::ilm_editor_update).
 *   + Insert tool for manage aditional params for iLM (ilm_settings::add_edit_copy_param, ilm_settings::visible_param, ilm_settings::add_param, ilm_settings::edit_param, ilm_settings::copy_param, ilm_settings::delete_param).
 * - v 3.6 2013/09/05
 *   + Insert function ilm_settings::applet_ilm for create APPLET html tag.
 *   + Insert function ilm_settings::applet_filetime for get modified date of iLM file.
 *   + Change tag APPLET in function ilm_settings::view_ilm.
 * - v 3.5 2013/08/26
 *   + Fix bug in download package iassign without answers (iassign::report).
 * - v 3.4 2013/08/23
 *   + Fix bug in export package iassign.
 * - v 3.3 2013/08/22
 *   + Insert functions for export users answer in iassign (iassign::export_file_answer, iassign::export_package_answer, iassign::view_iassign_current, iassign::report).
 *   + Insert function for rename iassign file (ilm_manager::rename_file_ilm, ilm_manager::view_files_ilm).
 * - v 3.2 2013/08/21
 *   + Change title link with message for get file for donwload file (ilm_manager::view_files_ilm).
 *   + Change functions for import files for ilm_manager.php.
 *   + Create static utils class for functions system utils (iassign_utils::format_filename, iassign_utils::version_filename).
 * - v 3.1 2013/08/15
 *   + Change return file selected (ilm_manager::add_ilm).
 *   + Insert functions for import files, export files and remove selected files (ilm_manager::view_files_ilm, ilm_manager::import_files_ilm, ilm_manager::export_files_ilm, ilm_manager::delete_selected_ilm).
 * - v 3.0 2013/08/02
 *   + Insert link for view informations of iLMs in teacher view, same screen of admin view but wiht some features hide (ilm_settings::list_ilm, ilm_settings::view_ilm, iassign::view_iassigns).
 * - v 2.9 2013/08/01
 *   + Fix bugs in functions ilm_settings::new_file_ilm, ilm_settings::copy_new_version_ilm, ilm_settings::add_edit_copy_ilm.
 * - v 2.8 2013/07/25
 *   + Insert the activity name in header of view (activity::view_dates).
 *   + Set function default iLM in view iLMs versions (ilm_settings::default_ilm and ilm_settings::confirm_default_ilm).
 * - v 2.7 2013/07/24
 *   + Create link previous and next for student view in one activity (activity::view_dates).
 *   + Fix bugs for view error in iLM not on DB in function iassign::view_iassign_current.
 * - v 2.6 2013/07/23
 *   + Fix bugs for view files in function ilm_manager::view_files_ilm.
 *   + Fix bugs for comment on teacher view in function iassign::view_iassign_current.
 * - v 2.5 2013/07/12
 *   + Change iLM settings for accept versions (ilm_settings::new_file_ilm, ilm_settings::new_ilm, ilm_settings::edit_ilm, ilm_settings::copy_new_version_ilm).
 *   + Insert new informations in iLMs table: created date, modified date, author, version, modified date of JAR (ilm_settings::view_ilm).
 *   + Added support for PHP 7.0 Constructors and fallback for previous Moodle Versions (<3.1) - Márcio
 *
 * @author Patricia Alves Rodrigues
 * @author Leônidas O. Brandão
 * @contributor Luciano Oliveira Borges
 * @contributor Márcio de Lima Passos
 * @version v 4.8 2016/05/12
 * @package mod_iassign_lib
 * @since 2010/09/27
 * @copyright iMatica (<a href="http://www.matematica.br">iMath</a>) - Computer Science Dep. of IME-USP (Brazil)
 *
 * <b>License</b> 
 *  - http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


//TODO Review: eliminate iLM JAR under MoodleData? It is necessary to HTML5 packages (like iVProgH5), anyway
//TODO Whenever under HTTPS, verify if MoodleData is working, if it is not, please use iLM JAR under WWW setting $CONF_WWW = 1
//TODO (under HTTPS could fail 'pluginfile', like 'https://saw.atp.usp.br/pluginfile.php/1/mod_iassign/ilm/182563135/iassign/ilm/igeom/5920/iGeom.jar"'
$CONF_WWW = 1; //TODO get iLM (JAR) from WWW, avoiding MoodleData


/**
 * Standard base class for all iAssign
 */

class iassign {

  var $cm;
  var $course;
  var $iassign;
  var $striassign;
  var $striassigns;
  var $context;
  var $activity;
  var $iassign_up;
  var $iassign_down;
  var $action;
  var $iassign_submission_current;
  var $userid_iassign;
  var $bottonPost;
  var $write_solution;
  var $view_iassign;

  // 3.1 update PHP 7.0 compatibility for all moodle versions
  //D public function iassign($iassign, $cm, $course) { self::__construct($iassign, $cm, $course); }
  /// Constructor for the base iassign class    
  //  @calledby ./mod/iassign/view.php : $iassigninstance = new iassign($iassign, $cm, $course)
  //  @calledby ./mod/iassign/grade.php : $iassigninstance = new iassign($iassign, $cm, $course);
  //  @calledby ./mod/iassign/renderer.php : return $this->render(new iassign_files($context, $itemid, $filearea));
  function __construct ($iassign, $cm, $course) {
    global $COURSE, $CFG, $USER, $DB;
    $botton = optional_param('botton', NULL, PARAM_TEXT);
    $this->userid_iassign = optional_param('userid_iassign', 0, PARAM_INT);
    if (!is_null($botton))
      $USER->iassignEdit = $botton;

    //D The Moodle function 'optional_param(...)' allow to filter GET parameters over a click that launches, e.g., './mod/iassign/view.php&userid_iassign=6'
    //D If "$var1 = optional_param('userid_iassign', 'NOT', PARAM_TEXT);", $var1 will be set to 6 (if 'userid_iassign' is not present, $var1 will be set to 'NOT'
    $this->iassign_up = optional_param('iassign_up', 0, PARAM_INT); // if parameter 'iassign_up' does not exists or it is not integer => use 0 as "default"
    $this->iassign_down = optional_param('iassign_down', 0, PARAM_INT);
    $this->iassign_submission_current = optional_param('iassign_submission_current', 0, PARAM_INT);
    $this->write_solution = optional_param('write_solution', 0, PARAM_INT);
    $this->action = optional_param('action', NULL, PARAM_TEXT);
    $this->cm = $cm;
    $this->context = context_module::instance($this->cm->id);
    //D echo "locallib.php: new iassign(): action=" . $this->action . ", write_solution=" . $this->write_solution . ", this->cm->id=" . $this->cm->id . "<br/>";

    if (!has_capability('mod/iassign:evaluateiassign', $this->context, $USER->id))
      $this->userid_iassign = $USER->id;

    if ($course) {
      $this->course = $course;
    } else if ($this->cm->course == $COURSE->id) {
      $this->course = $COURSE;
    } else if (!$this->course = $DB->get_record('course', array('id' => $this->cm->course))) {
      print_error('invalidid', 'iassign');
      }
    $this->coursecontext = context_course::instance($this->course->id);

    if ($iassign) {
      $this->iassign = $iassign;
    } else if (!$this->iassign = $DB->get_record('iassign', array('id' => $this->cm->instance))) {
      print_error('invalidid', 'iassign');
      }
    $USER->context = context_module::instance($this->cm->id);
    $USER->cm = $this->cm->id;
    $this->iassign->cmidnumber = $this->cm->idnumber; // compatibility with modedit ia obj
    $this->iassign->courseid = $this->course->id; // compatibility with modedit ia obj
    $this->context = context_module::instance($this->cm->id);
    $this->striassign = get_string('modulename', 'iassign');
    $this->striassigns = get_string('modulenameplural', 'iassign');
    $this->return = $CFG->wwwroot . "/mod/iassign/view.php?id=" . $this->cm->id;
    $this->bottonPost = 0;
    $this->view_iassign = optional_param('action', false, PARAM_BOOL);
    $this->activity = new activity(optional_param('iassign_current', NULL, PARAM_TEXT));
    $this->view();
    } // function __construct ($iassign, $cm, $course)


  /// Show iAssign by using the security filter (temporary data on '*_iassign_security')
  //  This method provides the page to view each iLM with any interactive activity
  //  @calledby view.php : $iassigninstance->view($id); that instantiate 'iassign' (constructor above)
  function view () { // This is an standard function for each iAssign instance (to not use parameters, it is ignored)
    global $USER, $DB, $OUTPUT;

    //$iassign_statementid = $this->iassign->iassign_statementid;

    $iassign_statementid = $this->activity->get_activity()->id;

    // '$this' has : iassign Object = cm ; course ; iassign ; striassign ; striassigns ; context ; activity ; iassign_up ; iassign_down ; action ; ... ; coursecontext ; return
    //D echo "1: locallib.php: view(): action=" . $this->action . ", iassign_statementid=" . $iassign_statementid . "<br/>\n";
    // If this user has no capability to View 'iassign': stop here
    require_capability('mod/iassign:view', $this->context);

    // Trigger module viewed event.
    $event = \mod_iassign\event\course_module_viewed::create(array(
      'objectid' => $this->iassign->id,
      'context' => $this->context
      ));

    $event->add_record_snapshot('course', $this->course);
    $event->trigger();

    if ($this->action) { // when student do/redo activity or teacher see student solution
      $this->action(); // calls '$this->view_iLM();'
    } else {
      print $OUTPUT->header();
      $this->view_iassigns(); // show the iLM with the content
      print $OUTPUT->footer();
      }

    // Security: delete all records with an error loading iLM - 'iassign_security : id iassign_statementid userid file timecreated view'
    //D $DB->delete_records("iassign_security", array("userid" => $USER->id, "view" => 1));
    //D $iassign_iLM_security = $DB->get_record("iassign_security", array("iassign_statementid" => $iassign_statementid));
    //D foreach ($iassign_iLM_security as $item) { echo $iassign_iLM_security->id . " ; " . $iassign_iLM_security->iassign_statementid . " ; " . $iassign_iLM_security->userid . " ; "  . $iassign_iLM_security->timecreated . " ; " .  $iassign_iLM_security->view . " ; " . $iassign_iLM_security->file . "<br/>\n"   }
    $DB->delete_records("iassign_security", array("userid" => $USER->id, "iassign_statementid" => $iassign_statementid));
    //D $iassign_iLM_security = $DB->get_record("iassign_security", array("iassign_statementid" => $iassign_statementid));
    //D if ($iassign_iLM_security) foreach ($iassign_iLM_security as $item) { echo $iassign_iLM_security->id . " ; " . $iassign_iLM_security->iassign_statementid . " ; " . $iassign_iLM_security->userid . " ; "  . $iassign_iLM_security->timecreated . " ; " .  $iassign_iLM_security->view . " ; " . $iassign_iLM_security->file . "<br/>\n"   }
    //D else echo "Apagou!<br/>";

    die();
    } // function view()


  /// Execute the action from Moodle (move, make visible, register the exercise (teacher) or its answer (learner)...)
  function action () {
    global $USER;
    // action:
    // up - move up activity (mover atividade para cima)
    // down - move down activity (mover atividade para baixo)
    // visible - view/hide activity (exibir/ocultar atividade)
    // delete - delete activity (excluir atividade)
    // deleteyes - confirms exclusion of activity (confirma exclusão de atividade)
    // deleteno - does not erase activity (não apaga atividade)
    // add - add activity (adicionar atividade)
    // edit - edit activity (modificar atividade)

    $action_iassign = array(
      'newcomment' => '$this->get_answer();',
      'view' => '$this->view_iassign_current();',
      'get_answer' => '$this->get_answer();',
      'repeat' => '$this->view_iassign_current();',
      'overwrite' => '$this->get_answer();',
      'stats_student' => '$this->stats_students();',
      'download_answer' => '$this->export_file_answer();',
      'download_all_answer' => '$this->export_package_answer();');

    $action_iassign_limit = array(
      'view' => '$this->view_iassign_current();',
      'newcomment' => '$this->get_answer();',
      'viewsubmission' => '$this->view_iassign_current();',
      'edit_status' => '$this->edit_status();',
      'edit_grade' => '$this->edit_grade();',
      'report' => '$this->report();',
      'print' => '$this->report();',
      'stats' => '$this->stats();',
      'printstats' => '$this->stats();');

    $restricted = array('up' => '$this->activity->move_iassign($this->iassign_up,$this->return);',
      'down' => '$this->activity->move_iassign($this->iassign_down,$this->return);',
      'visible' => '$this->activity->visible_iassign($this->return);',
      'delete' => '$this->activity->delete($this->return);',
      'deleteno' => '$this->return_home_course("confirm_not_delete_iassign");',
      'deleteyes' => '$this->activity->deleteyes($this->return, $this);',
      'add' => '$this->add_edit_iassign();',
      'edit' => '$this->add_edit_iassign();',
      'get_answer' => '$this->get_answer();',
      'duplicate_activity' => '$this->duplicate_activity();');

    $action_iassign_restricted = array_merge($restricted, $action_iassign_limit, $action_iassign);
    
    // On depends the user's capability: precedence to edit power ('editiassign'); after to analyse ('evaluateiassign'); otherwise only view (any other).
    if (has_capability('mod/iassign:editiassign', $this->context, $USER->id)) {
      // When teacher is seeing student solution. By 'viewsubmission' goes to the function 'view_iassign_current()'
      eval($action_iassign_restricted[$this->action]); // Now load 'view_iassign_current()' with 'viewsubmission'
      }
    elseif (has_capability('mod/iassign:evaluateiassign', $this->context, $USER->id)) {
      eval($action_iassign_limit[$this->action]);
      }
    else {
      // When student do/redo activity: do => action = "view"; redo => action = "repeat" 
      eval($action_iassign[$this->action]); // Now load 'view_iassign_current()' with 'view'
      }
    } // function action()

  
  /// This method duplicates an iAssign activity
  function duplicate_activity() {
    global $USER, $CFG, $COURSE, $DB, $OUTPUT;

    $id = $this->cm->id;
    $iassignid = optional_param('iassign_current', NULL, PARAM_TEXT);;

    $context = context_module::instance($this->cm->id);

    $contextuser = context_user::instance($USER->id);

    // Get the the iAssign acitivity to be duplicated
    $iassign_statement = $DB->get_record("iassign_statement", array("id" => $iassignid));

    // Remove the current id of activity
    $iassign_statement->id=0;

    // Get the information about current author, and add this information in author_modified field
    $author = $DB->get_record("user", array('id' => $USER->id));
    $iassign_statement->author_modified_name = $author->firstname . '&nbsp;' . $author->lastname;
    $iassign_statement->author_modified = $iassign_statement->author_modified_name;

    // Store the activity in the table
    if ($id_ = $DB->insert_record("iassign_statement", $iassign_statement)) {

      // Duplicate activity file
      $fs = get_file_storage();
      $files = $fs->get_area_files($context->id, 'mod_iassign', 'exercise', $iassign_statement->file);

      foreach ($files as $value) {
        if ($value->get_filename() != ".") {
          echo  $value->get_itemid() . " : " . $value->get_filename()."<br>";

          $newfile = $fs->create_file_from_storedfile(array('contextid' => $context->id, 'component' => 'mod_iassign', 'filearea' => 'exercise', 'itemid' => $value->get_itemid() + $id_), $value);

          $updateentry = new stdClass();
          $updateentry->id = $id_;
          $updateentry->file = $newfile->get_itemid();

          // Update the duplicated iLM iAssign with new file id
          $DB->update_record("iassign_statement", $updateentry);
        }
      }
    }

    // log event --------------------------------------------------------------------------------------
    iassign_log::add_log('duplicate_iassign_exercise', 'name: ' . $author->firstname, $id_, $this->cm->id);

    $this->return_home_course('duplicated_activity');
    exit;
  }

  /// This method gets the content from the iLM and register it
  //  It could be the exercise (teacher) or an answer (student)
  function get_answer () {
    
    global $USER, $CFG, $DB, $OUTPUT;
    $id = $this->cm->id;
    
    $submission_comment = optional_param('submission_comment', NULL, PARAM_TEXT);

    $comment = false;
    if ($submission_comment)
      $comment = $this->write_comment_submission();

    // receives data of iLM using the current activity
    $iassign_ilm = $DB->get_record("iassign_ilm", array("id" => $this->activity->get_activity()->iassign_ilmid)); // has automatic evaluation?
    $iassign = $DB->get_record("iassign", array("id" => $this->activity->get_activity()->iassignid)); // activity
    // receives data of submission of current activity
    $iassign_submission = $DB->get_record("iassign_submission", array("iassign_statementid" => $this->activity->get_activity()->id, "userid" => $this->userid_iassign)); // data about student solution

    //D echo "DEBUG: "; $x = array("iassign_statementid" => $this->activity->get_activity()->id, "userid" => $this->userid_iassign);
    //D print_r($x); exit;

    // receives post and get
    $iLM_PARAM_ActivityEvaluation = optional_param('iLM_PARAM_ActivityEvaluation', 0, PARAM_INT); // 1 - activity evaluated as correct / 0 - activity evaluated as incorrect
    //2016/02/16: IMPORTANTE trocar formatador para "nao formatado", pois esta destruindo o conteudo do arquivo
    $iLM_PARAM_ArchiveContent = optional_param('iLM_PARAM_ArchiveContent', NULL, PARAM_RAW); // answer file (ATTENTION: do not change format, use RAW in order to ensure the correct content)
    $MA_POST_Info = optional_param('MA_POST_Info', NULL, PARAM_FORMAT);
    $MA_POST_SystemData = optional_param('MA_POST_SystemData', NULL, PARAM_FORMAT);
    $return_get_answer = optional_param('return_get_answer', 0, PARAM_INT);
    $msg = '';

    // Feedback
    // Activity status: 0 => not post 1; => post; 2 => evaluated as incorrect; 3 => evaluated as correct
    $str_action = "view"; // repeat
    if (strtolower($iassign_ilm->name) == "igeom") {      
      }

    $title = get_string('evaluate_iassign', 'iassign');
    print $OUTPUT->header();
    print $OUTPUT->box_start();

    // Action = { view ; repeat ; viewsubmission }
    // * 'view' => it is impossible to re-send answer (this is correct with iGeom because its model to ensure security - do not allow the learner's access to the "answer model")
    // * 'repeat' => student explicitly requested to redo the activiy or enter in the last submission under iLM other then iGeom
    // * 'viewsubmission' => teacher/non editing teacher seeing the learner's activity

    $return = $CFG->wwwroot . "/mod/iassign/view.php?action=view&id=" . $id . "&iassign_submission_current=" . $this->iassign_submission_current . "&userid_iassign=" . $this->userid_iassign . "&iassign_current=" . $this->activity->get_activity()->id;

    $return_last = "&nbsp;<a href='" . $return . "'>" . iassign_icons::insert('return_home') . '&nbsp;' . get_string('return_iassign', 'iassign') . "</a>";

    $link_return = "&nbsp;<a href='" . $this->return . "'>" . iassign_icons::insert('home') . '&nbsp;' . get_string('activities_page', 'iassign') . "</a>";
    print '<table  width=100% >';

    if ($iLM_PARAM_ArchiveContent == - 1 || empty($iLM_PARAM_ArchiveContent)) { // if ($iLM_PARAM_ActivityEvaluation == -1)
      //TODO alterar aqui???
      $this->write_solution = 0; // necessary in order to take note in Moodle 'grade' system
      // empty_answer_post = No solution was posted.
      if ($comment)
        print '<tr><td colspan=2><br>' . get_string('empty_answer_post', 'iassign') . '</br>' . get_string('confirm_add_comment', 'iassign') . '</td>';
      else
        print '<tr><td colspan=2><br>' . get_string('empty_answer_post', 'iassign') . '</td>';
      print '<tr><td width=40% align=right>' . $return_last . '&nbsp;' . $link_return . '</td></tr>';
    } else {
      if ($iassign_ilm->evaluate == 1 && $this->activity->get_activity()->automatic_evaluate == 1) { // iLM with automatic evaluator
        if (intval($iLM_PARAM_ActivityEvaluation) == 1) {
          // Correct answer!!
          //TODO apelei - o melhor e' alterar acima o 'write_solution=0'?
          $this->write_solution = 1; // 

          $status = 3;
          $grade_student = $this->activity->get_activity()->grade; // evaluated as correct solution submitted is assigned the note pattern of activity
          $msg = '<tr><td colspan=2>' . iassign_icons::insert('feedback_correct') . '<br>' . get_string('get_answer_correct', 'iassign') . '</td>';

          // log record
          $info = $iassign->name . "&nbsp;-&nbsp;" . $this->activity->get_activity()->name . "&nbsp;-&nbsp;" . get_string('feedback_correct', 'iassign') . "&nbsp;-&nbsp;" . get_string('grade_iassign', 'iassign') . ":" . $grade_student;
          // Trigger module viewed event.
          $event = \mod_iassign\event\submission_created::create(array(
            'objectid' => $this->iassign->id,
            'context' => $this->context,
            'other' => $info
            ));
          $event->add_record_snapshot('course', $this->course);
          $event->trigger();
        } else { // else if (intval($iLM_PARAM_ActivityEvaluation) == 1)
          // Wrong answer...: get_answer_incorrect
          $status = 2;
          $grade_student = 0; // evaluated as incorrect solution
          $msg = '<tr><td colspan=2>' . iassign_icons::insert('feedback_incorrect') . '<br>' . get_string('get_answer_incorrect', 'iassign') . '</td>';

          // log record
          $info = $iassign->name . "&nbsp;-&nbsp;" . $this->activity->get_activity()->name . "&nbsp;-&nbsp;" . get_string('feedback_incorrect', 'iassign') .
            '&nbsp;-&nbsp;' . get_string('grade_iassign', 'iassign') . $grade_student;
          // Trigger module viewed event.
          $event = \mod_iassign\event\submission_created::create(array(
            'objectid' => $this->iassign->id,
            'context' => $this->context,
            'other' => $info
            ));
          $event->add_record_snapshot('course', $this->course);
          $event->trigger();
        } // else if (intval($iLM_PARAM_ActivityEvaluation) == 1)
        // Presents to the learner the result of the automatic evaluate?
        if ($this->activity->get_activity()->show_answer == 0) { // no...
          print '<tr><td width=60% ><strong>' . iassign_icons::insert('post') . get_string('get_answer', 'iassign') . '</strong></td>';
          print '<tr><td width=40% align=right>' . $return_last . '&nbsp;' . $link_return . '</td></tr>';
          print '<tr>';
          // log record
          $info = $iassign->name . "&nbsp;-&nbsp;" . $this->activity->get_activity()->name . "&nbsp;-&nbsp;" . get_string('get_answer', 'iassign');
          // Trigger module viewed event.
          $event = \mod_iassign\event\submission_created::create(array(
            'objectid' => $this->iassign->id,
            'context' => $this->context,
            'other' => $info
            ));
          $event->add_record_snapshot('course', $this->course);
          $event->trigger();
        } else { // yes!!!
          print '<tr><td width=60% ><strong>' . get_string('auto_result', 'iassign') . '</strong></td>';
          print '<td width=40% align=right>' . $return_last . '&nbsp;' . $link_return . '</td></tr>';
          print '<tr>';
          print $msg;
          }
      } else { // if ($iassign_ilm->evaluate == 1 && $this->activity->get_activity()->automatic_evaluate == 1)
        $status = 1;
        $grade_student = 0; // iLM not have automatic evaluator

        print '<tr><td colspan=2>' . iassign_icons::insert('post') . get_string('get_answer_post', 'iassign') . '</td>';
        print '<tr><td width=40% align=right>' . $return_last . '&nbsp;' . $link_return . '</td></tr>';
        print '<tr>';

        // log record
        $info = $iassign->name . "&nbsp;-&nbsp;" . $this->activity->get_activity()->name . "&nbsp;-&nbsp;" . get_string('get_answer_post', 'iassign');
        $event = \mod_iassign\event\submission_created::create(array(
          'objectid' => $this->iassign->id,
          'context' => $this->context,
          'other' => $info
          ));
        $event->add_record_snapshot('course', $this->course);
        $event->trigger();
        } // if ($iassign_ilm->evaluate == 1)
      } // if ($iLM_PARAM_ActivityEvaluation == -1)
    print '</tr></table>';
    print $OUTPUT->box_end();

    // add or update evaluate
    if ($this->write_solution == 1) {
      $timenow = time();

      // new record
      if (!$iassign_submission) {
        $newentry = new stdClass();
        $newentry->userid = $this->userid_iassign;
        // $newentry->userid = $USER->id;
        $newentry->iassign_statementid = $this->activity->get_activity()->id;
        $newentry->timecreated = $timenow;
        $newentry->timemodified = $timenow;
        $newentry->answer = $iLM_PARAM_ArchiveContent;
        $newentry->grade = $grade_student;
        $newentry->status = $status;
        $newentry->experiment = 1;
        if (!$newentry->id = $DB->insert_record("iassign_submission", $newentry)) {
          print_error('error_insert', 'iassign');
        } else {
          // Trigger module viewed event.
          $event = \mod_iassign\event\submission_created::create(array(
            'objectid' => $this->iassign->id,
            'context' => $this->context
            ));
          $event->add_record_snapshot('course', $this->course);
          $event->trigger();
          $this->update_grade_student($newentry->userid, $newentry->iassign_statementid, $this->iassign->id);
          }
      } elseif ($iassign_submission->status != 3) {
        $newentry = new stdClass();
        $newentry->id = $iassign_submission->id;
        $newentry->iassign_statementid = $iassign_submission->iassign_statementid;
        $newentry->userid = $iassign_submission->userid;
        $newentry->timecreated = $iassign_submission->timecreated;
        $newentry->timemodified = $timenow;
        $newentry->answer = $iLM_PARAM_ArchiveContent;
        $newentry->grade = $grade_student;
        $newentry->status = $status;
        $newentry->experiment = $iassign_submission->experiment + 1;
        if (!$DB->update_record("iassign_submission", $newentry)) {
          print_error('error_update', 'iassign');
          //D depurar...
          //D $stringAux = "ia.class.php: ".$iLM_PARAM_ArchiveContent."<br/> ".utf8_encode($iLM_PARAM_ArchiveContent)."<br/>".utf8_encode(utf8_encode($iLM_PARAM_ArchiveContent))."<br/>";
          //D $fp = fopen("teste1.txt","w");
          //D fwrite($fp,$stringAux);
        } else {
          // Trigger module viewed event.
          $event = \mod_iassign\event\submission_updated::create(array(
            'objectid' => $this->iassign->id,
            'context' => $this->context
            ));
          $event->add_record_snapshot('course', $this->course);
          $this->update_grade_student($newentry->userid, $newentry->iassign_statementid, $this->iassign->id);
          }
      } else {
        if ($return_get_answer == 1) {
          $newentry = new stdClass();
          $newentry->id = $iassign_submission->id;
          $newentry->iassign_statementid = $iassign_submission->iassign_statementid;
          $newentry->userid = $iassign_submission->userid;
          $newentry->timecreated = $iassign_submission->timecreated;
          $newentry->timemodified = $timenow;
          $newentry->answer = $iLM_PARAM_ArchiveContent;
          $newentry->grade = $grade_student;
          $newentry->status = $status;
          $newentry->experiment = $iassign_submission->experiment + 1;
          if (!$DB->update_record("iassign_submission", $newentry))
            print_error('error_update', 'iassign');
          else {
            $event = \mod_iassign\event\submission_updated::create(array(
              'objectid' => $this->iassign->id,
              'context' => $this->context
              ));
            $event->add_record_snapshot('course', $this->course);
            $event->trigger();
            $this->update_grade_student($newentry->userid, $newentry->iassign_statementid, $this->iassign->id);
            print $OUTPUT->box_start();
            print "<p>" . get_string('iassign_update', 'iassign') . "</p>";
            print $OUTPUT->box_end();
            }
        } elseif ($return_get_answer == 2) {
          print $OUTPUT->box_start();
          print "<p>" . get_string('iassign_cancel', 'iassign') . "</p>";
          print $OUTPUT->box_end();
        } else {
          print $OUTPUT->box_start();
          print "
     <script type='text/javascript'>
       //<![CDATA[
       function overwrite () {
         document.formEnvio.return_get_answer.value = 1;
         document.formEnvio.submit();
         }

       function nooverwrite () {
         document.formEnvio.return_get_answer.value = 2;
         document.formEnvio.submit();
         }
      //]]>
     </script>";
          $param_aux = "action=overwrite&iassign_submission_current=" . $iassign_submission->id . "&id=" . $id . "&iassign_current=" . $this->activity->get_activity()->id . "&write_solution=" . $this->write_solution . "&userid_iassign=" . $USER->id;
          $get_answer_overwrite = $CFG->wwwroot . "/mod/iassign/view.php?" . $param_aux;
          print "<form name='formEnvio' method='post' action='$get_answer_overwrite' enctype='multipart/form-data'>";
          print "<p>" . get_string('last_iassign_correct', 'iassign') . "</p>";
          print "<p>" . get_string('update_iassign', 'iassign') . "</p>";
          print "<input type='hidden' name='iLM_PARAM_ArchiveContent' value='$iLM_PARAM_ArchiveContent'/>
       <input type='hidden' name='iLM_PARAM_ActivityEvaluation' value='$iLM_PARAM_ActivityEvaluation'/>
       <input type='hidden' name='MA_POST_Info' value='$MA_POST_Info'/>
       <input type='hidden' name='MA_POST_SystemData' value='$MA_POST_SystemData'/>
       <input type='hidden' name='return_get_answer'/> ";
          print "<input type=button value='" . get_string('yes', 'iassign') . "' onClick = 'overwrite()'
      title='" . get_string('message_update_iassign', 'iassign') . "'/>&nbsp;&nbsp;";
          print "<input type=button value='" . get_string('no', 'iassign') . "' onClick = 'nooverwrite()'
      title='" . get_string('message_no_update_iassign', 'iassign') . "'/>";
          print " </form>";
          print $OUTPUT->box_end();
          }
        }
      } // if ($this->write_solution == 1)

    print $OUTPUT->footer();
    die();
    } // function get_answer()


  /// Export in file the answer of student. 
  function export_file_answer () {
    global $DB;

    $iassign_submission_id = optional_param('iassign_submission_id', NULL, PARAM_INT);

    $iassign_submission = $DB->get_record("iassign_submission", array("id" => $iassign_submission_id));
    $iassign_statement = $DB->get_record("iassign_statement", array("id" => $iassign_submission->iassign_statementid));
    $name = iassign_utils::format_filename(strip_tags($iassign_statement->name));

    $iassign_ilm = $DB->get_record("iassign_ilm", array("id" => $iassign_statement->iassign_ilmid));
    $extensions = explode(",", $iassign_ilm->extension);

    $iassign_user = $DB->get_record("user", array("id" => $iassign_submission->userid));
    $username = iassign_utils::format_filename($iassign_user->firstname . ' ' . $iassign_user->lastname);

    $name_answer = $username . '-' . $name . '-' . userdate($iassign_submission->timemodified, '%Y%m%d-%H%M') . '.' . $extensions[0];

    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private", false);
    header("Content-Type: document/unknown");
    header("Content-Disposition: attachment; filename=\"" . $name_answer . "\";");
    set_time_limit(0);
    print($iassign_submission->answer);
    exit;
    } // function export_file_answer()


  /// Export an package (zip) with all answer of students
  function export_package_answer() {
    global $DB, $CFG;

    $iassign_id = optional_param('iassign_id', NULL, PARAM_INT);
    $iassign = $DB->get_record("iassign", array("id" => $iassign_id));
    $iassign_name = iassign_utils::format_filename($iassign->name);

    $userid = optional_param('userid', NULL, PARAM_INT);
    $iassign_user = $DB->get_record("user", array("id" => $userid));
    $username = iassign_utils::format_filename($iassign_user->firstname . ' ' . $iassign_user->lastname);

    $zip_filename = $CFG->dataroot . '/temp/package-iassign-' . $username . '-' . $iassign_name . '-' . date('Ymd-Hi') . '.zip'; // Moodle.org 2016/02/17 - antes
    //MOOC 2014: $zip_filename = $CFG->dataroot . '/temp/ilm-' . iassign_utils::format_pathname($iassign_ilm->name . '-v' . $iassign_ilm->version) . '.ipz';

    $zip = new zip_archive();
    $zip->open($zip_filename);

    $iassign_statements = $DB->get_records("iassign_statement", array("iassignid" => $iassign_id));
    foreach ($iassign_statements as $iassign_statement) {
      $name = iassign_utils::format_filename(strip_tags($iassign_statement->name));

      $iassign_ilm = $DB->get_record("iassign_ilm", array("id" => $iassign_statement->iassign_ilmid));
      $extensions = explode(",", $iassign_ilm->extension);

      $timemodified = "";
      $answer = "";
      $iassign_submission = $DB->get_record("iassign_submission", array("iassign_statementid" => $iassign_statement->id, "userid" => $userid));
      if ($iassign_submission) {
        $timemodified = '-' . userdate($iassign_submission->timemodified, '%Y%m%d-%H%M');
        $answer = $iassign_submission->answer;
        $name_answer = $name . $timemodified . '.' . $extensions[0];
        $zip->add_file_from_string($name_answer, $answer);
        }
      }

    $zip->close();

    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private", false);
    header("Content-Type: application/zip");
    header("Content-Disposition: attachment; filename=\"" . basename($zip_filename) . "\";");
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: " . @filesize($zip_filename));
    set_time_limit(0);
    @readfile("$zip_filename") || die("File not found.");
    unlink($zip_filename);
    exit;
    } // function export_package_answer()


  /// Editing status of interactive activities
  function edit_status () {
    global $USER, $DB, $OUTPUT;
    $newentry = new stdClass();
    $newentry->id = $this->iassign_submission_current;
    $newentry->status = optional_param('return_status', 0, PARAM_INT);

    $iassign_submission = $DB->get_record('iassign_submission', array('id' => $this->iassign_submission_current));
    if ($iassign_submission->status != 0 && $newentry->status == 0)
      $newentry->status = $iassign_submission->status;

    $newentry->teacher = $USER->id;
    if (!$DB->update_record('iassign_submission', $newentry))
      print_error('error_update', 'iassign');
    else {
      // Trigger module viewed event.
      $event = \mod_iassign\event\submission_updated::create(array(
        'objectid' => $this->iassign->id,
        'context' => $this->context
        ));
      $event->add_record_snapshot('course', $this->course);
      $event->trigger();
      $this->action = 'viewsubmission';
      $this->view_iassign_current();
      } // if (!$DB->update_record('iassign_submission', $newentry))
    }

// function edit_status()
  /// Editing grade of interactive activities
  function edit_grade() {
    global $USER, $DB, $OUTPUT;

    $newgrade = optional_param('return_grade', 0, PARAM_INT);
    if ($newgrade && $newgrade >= 0) {
      $newentry = new stdClass();
      $newentry->id = $this->iassign_submission_current;
      $newentry->grade = optional_param('return_grade', 0, PARAM_INT);
      $newentry->teacher = $USER->id;
      if (!$DB->update_record('iassign_submission', $newentry))
        print_error('error_update', 'iassign');
      else {
        // Trigger module viewed event.
        $event = \mod_iassign\event\submission_updated::create(array(
          'objectid' => $this->iassign->id,
          'context' => $this->context
          ));
        $event->add_record_snapshot('course', $this->course);
        $event->trigger();
        }
      } // if ($newgrade >= 0)

    $this->action = 'viewsubmission';
    $this->view_iassign_current();
    }


  /// Add or Edit interactive activities
  function add_edit_iassign () {
    global $USER, $CFG, $COURSE, $DB, $OUTPUT;
    require_once('iassign_form.php');

    $id = $this->cm->id;
    $iassignid = $this->iassign->id;

    $param = new stdClass();

    $param->action = $this->action; // oculto
    $param->id = $id; // oculto
    $COURSE->cm = $id;
    $COURSE->iassignid = $iassignid;
    $COURSE->iassign_file_id = NULL;

    $context = context_module::instance($this->cm->id);

    $contextuser = context_user::instance($USER->id);

    $component = 'mod_iassign';
    $filearea = 'exercise';

    if (!empty($this->iassign_current))
      $COURSE->iassign_id = $this->iassign_current;
    else
      $COURSE->iassign_id = 0;

    if ($this->action == 'add') {

      $iassign_data = $DB->get_record("iassign", array('id' => $iassignid));

      $params = array('iassignid' => $iassignid);
      $iassign_statement = $DB->get_records_sql("SELECT s.id, s.name, s.dependency
        FROM {iassign_statement} s
        WHERE s.iassignid = :iassignid
        ORDER BY s.position ASC", $params); // " - jed/emacs

      $param->iassignid = $iassignid;
      $param->name = "";
      $param->oldname = "";
      $param->type_iassign = 3;
      $param->proposition = "";
      $author = $DB->get_record("user", array("id" => $USER->id));
      $param->author_name = $author->firstname . '&nbsp;' . $author->lastname;
      $param->author_modified_name = $author->firstname . '&nbsp;' . $author->lastname;
      $param->author = $param->author_name;
      $param->author_modified = $param->author_modified_name;
      $COURSE->iassign_list = array();
      $param->iassign_list = array();
      if ($iassign_statement) {
        foreach ($iassign_statement as $iassign) {
          $param->iassign_list[$iassign->id] = 0;
          $COURSE->iassign_list[$iassign->id] = new stdClass();
          $COURSE->iassign_list[$iassign->id]->id = $iassign->id;
          $COURSE->iassign_list[$iassign->id]->name = $iassign->name;
          $COURSE->iassign_list[$iassign->id]->enable = 1;
          } // foreach ($iassign_statement as $iassign)
        }
      $param->iassign_ilmid = 0;
      $param->file = 0;
      $param->fileold = 0;
      $param->filename = "";
      $param->grade = $iassign_data->grade;
      $param->timemodified = time();
      $param->timecreated = time();
      $param->timeavailable = $iassign_data->timeavailable;
      $param->timedue = $iassign_data->timedue;
      $param->preventlate = $iassign_data->preventlate;
      $param->test = $iassign_data->test;
      $param->special_param1 = 0;
      $param->visible = 1;
      $param->max_experiment = $iassign_data->max_experiment;
      $param->dependency = 0;
      $param->automatic_evaluate = 1;
      $param->show_answer = 1;
      } // if ($this->action == 'add')
    elseif ($this->action == 'edit') {

      $COURSE->iassign_list = array();

      if ($this->activity->get_activity() != null) {
        $iassign_statement_current = $DB->get_record("iassign_statement", array("id" => $this->activity->get_activity()->id));

        $str_query = "SELECT * FROM {iassign_statement} s WHERE s.iassignid = '$iassignid' AND s.id!='$iassign_statement_current->id' ORDER BY s.position ASC";
        $iassign_statement = $DB->get_records_sql($str_query);

        $param->iassign_id = $iassign_statement_current->id; // oculto
        $param->iassignid = $iassign_statement_current->iassignid; // oculto
        $param->name = $iassign_statement_current->name;
        $param->oldname = $iassign_statement_current->name;
        $param->type_iassign = $iassign_statement_current->type_iassign;
        $param->proposition = $iassign_statement_current->proposition;
        $param->author_name = $iassign_statement_current->author_name; // oculto
        $param->author = $iassign_statement_current->author_name;
        $author = $DB->get_record("user", array('id' => $USER->id));
        $param->author_modified_name = $author->firstname . '&nbsp;' . $author->lastname;
        $param->author_modified = $param->author_modified_name;
        $dependency = explode(';', $iassign_statement_current->dependency);
        $param->iassign_list = array();
        $inter = array();
        if ($iassign_statement)
          foreach ($iassign_statement as $iassign)
            if (in_array($iassign->id, $dependency))
              $inter[] = $iassign->id;

        $str_query = "SELECT * FROM {iassign_statement} s WHERE s.iassignid = '$iassignid' AND s.id!='$iassign_statement_current->id' AND s.dependency!=0";
        $iassign_statement_dependency = $DB->get_records_sql($str_query);

        $array_dependency = array();
        $subdependency = "";
        $sub_subdependency = "";
        // dependent on this exercise
        if ($iassign_statement_dependency) {
          $subdependency .= $this->search_dependency($iassign_statement_current->id, $iassign_statement_dependency);

          // to whom this exercise depends
          foreach ($inter as $tmp)
            $sub_subdependency .= $this->search_sub_dependency($tmp);

          $list_dependency = $subdependency . $sub_subdependency;
          $array_dependency = explode(";", $list_dependency);
          }

        if ($iassign_statement) {
          foreach ($iassign_statement as $iassign) {
            $COURSE->iassign_list[$iassign->id] = new stdClass();
            $COURSE->iassign_list[$iassign->id]->name = $iassign->name;
            $COURSE->iassign_list[$iassign->id]->id = $iassign->id;

            if (in_array($iassign->id, $dependency))
              $param->iassign_list[$iassign->id] = 1;
            else
              $param->iassign_list[$iassign->id] = 0;

            if (in_array($iassign->id, $array_dependency))
              $COURSE->iassign_list[$iassign->id]->enable = 0;
            else
              $COURSE->iassign_list[$iassign->id]->enable = 1;
            } // foreach ($iassign_statement as $iassign)
          }
        $param->iassign_ilmid = $iassign_statement_current->iassign_ilmid;
        $param->fileold = 0;
        $param->file = 0;
        $param->filename = '';
        $param->grade = $iassign_statement_current->grade;
        $param->timecreated = $iassign_statement_current->timecreated; // oculto
        $param->timeavailable = $iassign_statement_current->timeavailable;
        $param->timedue = $iassign_statement_current->timedue;
        $param->preventlate = $iassign_statement_current->preventlate;
        $param->test = $iassign_statement_current->test;
        $param->special_param1 = $iassign_statement_current->special_param1;
        $param->position = $iassign_statement_current->position; // oculto
        $param->visible = $iassign_statement_current->visible;
        $param->max_experiment = $iassign_statement_current->max_experiment;
        $param->automatic_evaluate = $iassign_statement_current->automatic_evaluate;
        $param->show_answer = $iassign_statement_current->show_answer;
        $fs = get_file_storage();

        $files = $fs->get_area_files($context->id, $component, $filearea, $iassign_statement_current->file);
        if ($files) {
          foreach ($files as $file) {
            if ($file->get_filename() != '.') {
              $param->filename = $file->get_filename();
              $param->file = $file->get_id();
              $param->fileold = $file->get_id();
              $COURSE->iassign_file_id = $file->get_id();
              }
            }
          }

        //TODO //MOOC2014 -- inicio
        //D  $iassign_ilm_configs = $DB->get_records('iassign_statement_config', array('iassign_statementid' => $iassign_statement_current->id));
        //D  if ($iassign_ilm_configs) {
        //D  foreach ($iassign_ilm_configs as $iassign_ilm_config)
        //D  $param->{'param_'.$iassign_ilm_config->iassign_ilm_configid} = $iassign_ilm_config->param_value;
        //D  } //MOOC2014 -- fim
        } // if ($this->activity->get_activity () != null)
      } // elseif ($this->action == 'edit')

    // search position
    $iassign_list = $DB->get_records_list('iassign_statement', 'iassignid', array($this->iassign->id), 'position ASC');

    if ($iassign_list) {
      $end_list = array_pop($iassign_list);
      $param->position = $end_list->position + 1;
      } // if ($iassign_list)
    else
      $param->position = 1;

    $mform = new mod_iassign_form ();
    $mform->set_data($param);

    if ($mform->is_cancelled()) {
      $this->return_home_course('iassign_cancel');
      exit;
      } else if ($result = $mform->get_data()) {
      $result->context = $context;

      if ($result->type_iassign == 1 || $result->type_iassign == 2)
        $result->grade = 0;
      if ($result->type_iassign == 1) {
        $result->automatic_evaluate = 0;
        $result->show_answer = 0;
      } elseif ($result->automatic_evaluate == 0)
        $result->show_answer = 0;

      // $_POST['iassign_list']
      $result->iassign_list = optional_param_array('iassign_list', array(), PARAM_RAW);
      if ($result->iassign_list) {
        foreach ($result->iassign_list as $key => $value)
          $result->dependency .= $key . ';';
      } else
        $result->dependency = 0;

      $iassign_ilm = $DB->get_record("iassign_ilm", array("id" => $result->iassign_ilmid));

      if ($this->action == 'add') {
        $iassign_statement_name = $DB->get_records('iassign_statement', array('iassignid' => $result->iassignid, 'name' => $result->name));

        if ($iassign_statement_name) {
          $this->return_home_course('error_iassign_name');
          die();
          }

        $iassignid = $this->activity->new_iassign($result);

        $this->activity->add_calendar($iassignid);
        // Trigger module viewed event.
        $event = \mod_iassign\event\iassign_created::create(array(
          'objectid' => $iassignid,
          'context' => $context
          ));
        $event->add_record_snapshot('course', $this->course);
        $event->trigger();
        $this->return_home_course('iassign_add');
        } // if ($this->action == 'add')
      elseif ($this->action == 'edit') {

        $iassignid = $this->activity->update_iassign($result);
        $this->activity->update_calendar($iassignid, $result->oldname);
        // Trigger module viewed event.
        $event = \mod_iassign\event\iassign_updated::create(array(
          'objectid' => $iassignid,
          'context' => $context
          ));
        $event->add_record_snapshot('course', $this->course);
        $event->trigger();
        $this->return_home_course('iassign_update');
        } // elseif ($this->action == 'edit')

      die();
      } // elseif ($mform->get_data())

    print $OUTPUT->header();

    $mform->display();

    print $OUTPUT->footer();

    die();
    } // function add_edit_iassign()


  /// Search for dependencies
  function search_dependency($search_iassing_id, $iassign_statement) {
    global $DB, $OUTPUT;
    $dependency = "";
    if ($iassign_statement)
      foreach ($iassign_statement as $iassign) {
        $inter_dependency = explode(';', $iassign->dependency);
        if (in_array($search_iassing_id, $inter_dependency)) {
          $dependency .= $iassign->id . ";";
          $dependency .= $this->search_dependency($iassign->id, $iassign_statement);
          } // if (in_array($search_iassing_id, $inter_dependency))
        } // foreach ($iassign_statement as $iassign)
    return $dependency;
    }

  /// Search for "sub"dependency
  function search_sub_dependency($search_iassing_id) {
    global $DB, $OUTPUT;

    $iassign_statement = $DB->get_record("iassign_statement", array("id" => $search_iassing_id));

    $dependency = "";
    if ($iassign_statement) {
      $inter_dependency = explode(';', $iassign_statement->dependency);

      foreach ($inter_dependency as $tmp) {
        if ($tmp != 0)
          $dependency .= $tmp . ";";
        $dependency .= $this->search_sub_dependency($tmp);
        } // foreach ($inter_dependency as $tmp)
      } // if ($iassign_statement)
    return $dependency;
    }

  // Warning message
  static function warning_message_iassign ($strcode) {
    return "<div class='warning' style='display:inline; font-weight: bold; color:#a00'>" . get_string($strcode, 'iassign') . "</div>\n";
    }


  /// Update grade of iAssign
  //  Called always any iAssign activity is created
  //  @see /mod/iassign/view.php: call to iassign->iassign(): starting point
  static function update_grade_iassign ($iassignid) {
    global $USER, $CFG, $COURSE, $DB, $OUTPUT;
    require_once($CFG->libdir . '/gradelib.php');
    //D $sum_grade = $DB->get_records_sql ( "SELECT SUM(grade) as total
    //D FROM {$CFG->prefix}iassign_statement s WHERE s.iassignid = '$iassignid' AND s.type_iassign=3" );
    //TODO: REVIEW: wich one is more efficienty, '$DB->get_records' geting objects or '$DB->get_records' with 'foreach'?
    // Each iAssign item is associated with one item on the "gradebook"
    // Sum all '*_iassign_statement' associated with one item in '*_grade_items': iassignid AND type_iassign=3
    //$sum_grade = 0;
    //$grade = $DB->get_records('iassign_statement', array('iassignid' => $iassignid, 'type_iassign' => 3));
    //foreach($grade as $tmp) {
    //    $sum_grade += $tmp->grade;
    // }
    //1 Solution 1
    //1 $grade = $DB->get_records('iassign_statement', array('iassignid' => $iassignid, 'type_iassign' => 3)); //1
    //1 $sum_grade = 0; //1
    //1 foreach ($grade as $tmp) { $sum_grade += $tmp->grade; } //1
    //2 Solution 2
    $array_sum_grade = $DB->get_records_sql("SELECT SUM(grade) as total FROM {iassign_statement} s WHERE s.iassignid = '$iassignid' AND s.type_iassign=3"); //2
    //2 foreach ($array_sum_grade as $array_item) { $sum_grade = $array_item->total; break; } // nao necessario, basta 'key(...)' abaixo
    if (key($array_sum_grade))
      $sum_grade = key($array_sum_grade); //2
    else
      $sum_grade = 0; //2

    $grade_iassign = $DB->get_record("iassign", array("id" => $iassignid));
    $grades = NULL;
    $params = array('itemname' => $grade_iassign->name);
    $params['iteminstance'] = $iassignid;
    $params['gradetype'] = GRADE_TYPE_VALUE;
    //2016 if ($sum_grade != 0) {
    $params['grademax'] = $sum_grade;
    $params['rawgrademax'] = $sum_grade;
    //2016 } else { $params['grademax'] = 0; $params['rawgrademax'] = 0; }
    $params['grademin'] = 0;
    // @calls /lib/gradelib.php: call to grade_item->insert()
    //TODO: is there any error here in Moodle version 3.0?
    //TODO: Incorrect property 'grademax' found when inserting grade object
    //TODO: line 899 of /mod/iassign/locallib.php: call to grade_update()
    grade_update('mod/iassign', $grade_iassign->course, 'mod', 'iassign', $iassignid, 0, $grades, $params);
    }


  /// Update grade of student
  function update_grade_student ($userid, $iassign_statementid, $iassignid) {
    global $CFG, $DB, $OUTPUT;
    require_once($CFG->libdir . '/gradelib.php');
    $grade_iassign = $DB->get_record('iassign', array('id' => $iassignid));

    // Review all the student submission for this iAssign activity
    $grade_iassign_statements = $DB->get_records('iassign_statement', array('iassignid' => $iassignid));
    $total_grade = 0;
    foreach ($grade_iassign_statements as $grade_iassign_statement) {
      $iassign_submission = $DB->get_record('iassign_submission', array('iassign_statementid' => $grade_iassign_statement->id, 'userid' => $userid));
      if ($iassign_submission)
        $total_grade += $iassign_submission->grade;
      } // foreach ($grade_iassign_statements as $grade_iassign_statement)
    //D $sum_grade = $DB->get_records_sql ( "SELECT SUM(grade) as total
    //D  FROM {$CFG->prefix}iassign_statement s WHERE s.iassignid = '$iassignid' AND s.type_iassign=3" );
    //TODO: REVIEW: wich one is more efficienty, '$DB->get_records' geting objects or '$DB->get_records' with 'foreach'?
    //1 $sum_grade = 0; $grade = $DB->get_records('iassign_statement', array('iassignid' => $iassignid, 'type_iassign' => 3));
    //1 foreach ($grade as $tmp) { $sum_grade += $tmp->grade; }
    $array_sum_grade = $DB->get_records_sql("SELECT SUM(grade) as total FROM {iassign_statement} s WHERE s.iassignid = '$iassignid' AND s.type_iassign=3"); //2
    if (key($array_sum_grade))
      $sum_grade = key($array_sum_grade); //2
    else
      $sum_grade = 0; //2

    $grades['userid'] = $userid;
    $grades['rawgrade'] = $total_grade; // sum of all submissions for this iAssign activity
    $params = array('itemname' => $grade_iassign->name);
    $params['iteminstance'] = $iassignid;
    $params['gradetype'] = GRADE_TYPE_VALUE;

    //2016 if ($sum_grade != 0) { // depois eliminar comentario
    $params['grademax'] = $sum_grade;
    $params['rawgrademax'] = $sum_grade;
    //2016 } else { $params['grademax'] = 0; $params['rawgrademax'] = 0; }
    grade_update('mod/iassign', $grade_iassign->course, 'mod', 'iassign', $iassignid, 0, $grades, $params);
    }

  /// Display caption of icons
  function view_legend_icons () {
    global $USER, $CFG, $DB, $OUTPUT;
    $id = $this->cm->id;

    if ($this->action == 'print')
      print '<table border=1 width=100%><tr>';
    else
      print '<table width=100%><tr>';
    print '<td >';
    if ($this->action != 'print')
      print $OUTPUT->help_icon('legend', 'iassign');

    // helpbutton('legend', get_string('legend', 'iassign'), 'iassign', $image = true, $linktext = false, $text = '', $return = false,
    // $imagetext = '');
    print '<strong>' . get_string('legend', 'iassign') . '</strong>';
    print '&nbsp;' . iassign_icons::insert('correct') . '&nbsp;' . get_string('correct', 'iassign');
    print '&nbsp;' . iassign_icons::insert('incorrect') . '&nbsp;' . get_string('incorrect', 'iassign');
    print '&nbsp;' . iassign_icons::insert('post') . '&nbsp;' . get_string('post', 'iassign');
    print '&nbsp;' . iassign_icons::insert('not_post') . '&nbsp;' . get_string('not_post', 'iassign');
    print '&nbsp;' . iassign_icons::insert('comment_unread') . '&nbsp;' . get_string('comment_unread', 'iassign');

    if (has_capability('mod/iassign:viewreport', $this->context, $USER->id) && $this->action == 'report') {
      print '&nbsp;' . iassign_icons::insert('comment_read') . '&nbsp;' . get_string('comment_read', 'iassign');
      print '</td>' . "\n";
      if ($this->action != 'print') {
        $link_print = "<a href='" . $CFG->wwwroot . "/mod/iassign/view.php?id=" . $id . "&action=print&iassignid=" . $this->iassign->id . "'>" . iassign_icons::insert('print') . '&nbsp;' . get_string('print', 'iassign') . "</a>";
        $link_stats = "<a href='" . $CFG->wwwroot . "/mod/iassign/view.php?id=" . $id . "&action=stats&iassignid=" . $this->iassign->id . "'>" . iassign_icons::insert('results') . '&nbsp;' . get_string('graphic', 'iassign') . "</a>";
        print '<td width=15% align="right">' . $link_stats . '</td>' . "\n";
        print '<td width=15% align="right">' . $link_print . '</td>' . "\n";
        } // if ($this->action != 'print')
      print '</tr></table>' . "\n";
      } // if (has_capability('mod/iassign:viewreport', $this->context, $USER->id) && $this->action == 'report')
    elseif (has_capability('mod/iassign:submitiassign', $this->context, $USER->id)) {
      $link_stats = "<a href='" . $CFG->wwwroot . "/mod/iassign/view.php?id=" . $id . "&action=stats_student&iassignid=" . $this->iassign->id . "'>" . iassign_icons::insert('results') . '&nbsp;' . get_string('results', 'iassign') . "</a>";
      print '<td width=15% align="right">' . $link_stats . '</td>' . "\n";
      print '</tr></table>' . "\n";
      } // elseif (has_capability('mod/iassign:submitiassign', $this->context, $USER->id))
    else
      print '</td></tr></table>' . "\n";
    } // function view_legend_icons()


  /// Display activity current
  //  @calledby view() -> action() : when student do/redo activity and teacher see student answer
  function view_iassign_current () {
    global $USER, $CFG, $COURSE, $DB, $OUTPUT;
    $id = $this->cm->id;
    $iassignid = $this->iassign->id;

    // Get data of current activity
    $iassign_statement_activity_item = $this->activity->get_activity(); // search data of current activity
    $ilm = new ilm($iassign_statement_activity_item->iassign_ilmid);
    $iassign = $DB->get_record("iassign", array("id" => $iassignid));

    $ilm_name = strtolower($ilm->ilm->name); // class ilm has a unique property ('ilm'), get the iLM name
    // Do not allow the learner resent his solution only is iGeom iLM ("igeom") - why? iGeom acitivity "model answer" is not sent with the learner solution
    $allow_resubmission = (substr($ilm_name, 0, 5) != "igeom" ? 1 : 0);

    // log record
    $info = $iassign->name . ":" . $iassign_statement_activity_item->name;
    //Trigger module viewed event.
    $event = \mod_iassign\event\submission_viewed::create(array(
      'objectid' => $iassign->id,
      'context' => $this->context,
      'other' => $info
      ));
    $event->add_record_snapshot('course', $this->course);
    $event->trigger();

    print $OUTPUT->header();

    // Search of iLM data used in the current activity
    $iassign_ilm = $DB->get_record("iassign_ilm", array("id" => $iassign_statement_activity_item->iassign_ilmid));

    if ($this->action == 'viewsubmission') {
      if (!empty($this->iassign_submission_current) || $this->iassign_submission_current != 0)
        $iassign_submission = $DB->get_record("iassign_submission", array("id" => $this->iassign_submission_current)); // data about activity current
      else
        $iassign_submission = $DB->get_record("iassign_submission", array("iassign_statementid" => $this->activity->get_activity()->id, "userid" => $this->userid_iassign)); // data about student solution
      } else {
      $iassign_submission = $DB->get_record("iassign_submission", array("iassign_statementid" => $this->activity->get_activity()->id, "userid" => $this->userid_iassign)); // data about student solution
      }

    if ($iassign_submission)
      $this->update_comment($iassign_submission->id);

    $file = $iassign_statement_activity_item->file;

    // 1 when open previous file; 2 when the activity is redone!; 3 when the teacher enter in the activity
    // 1 => locallib.php: view_iassign_current(): action=view will set write_solution=0!!!!!
    // 2 => locallib.php: view_iassign_current(): action=repeat will set write_solution=0!!!!!
    // 3 => locallib.php: view_iassign_current(): action=viewsubmission will set write_solution=0!!!!!

    $this->bottonPost = 0; // hide submit button
    //xxx $this->write_solution = 0; // disable recording solution (however, iVProg allow the learner to edit previou solution)
    $this->view_iassign = false; // disable visualization of activity
    $repeat = "";
    $last_iassign = "";
    $student_answer = "";
    $comment = "";

    // *** Teacher access (view learner's submission)    
    if (($this->action != 'viewsubmission') && has_capability('mod/iassign:evaluateiassign', $USER->context, $USER->id)) {
      //TODO leo Verificar se o correto eh '$this->context' ou '$USER->context' como deixei
      // It is not 'viewsubmission' and it is teacher or 'non editing teacher'?
      // ---> access teacher for test

      if ($iassign_statement_activity_item->type_iassign != 1) // type_iassign=1 => activity of type "example" - not submit button for submission
        $this->bottonPost = 1;

      print $OUTPUT->box('<p><strong>' . get_string('area_specific_teacher', 'iassign') . '</strong></p>');
      $this->activity->view_dates();
      $USER->iassignEdit = $this->bottonPost;
      $this->activity->show_info_iassign();

      if ($iassign_submission) {
        //xxx $param_aux = "action=get_answer&iassign_submission_current=" . $iassign_submission->id . "&id=" . $id . "&iassign_current=" . $this->activity->get_activity()->id . "&write_solution=" . $this->write_solution . "&userid_iassign=" . $USER->id;
        $param_aux = "action=get_answer&iassign_submission_current=" . $iassign_submission->id . "&id=" . $id . "&iassign_current=" . $this->activity->get_activity()->id . "&userid_iassign=" . $USER->id;
      } else {
        //xxx $param_aux = "action=get_answer&id=" . $id . "&iassign_current=" . $this->activity->get_activity()->id . "&write_solution=" . $this->write_solution . "&userid_iassign=" . $USER->id;
        $param_aux = "action=get_answer&id=" . $id . "&iassign_current=" . $this->activity->get_activity()->id . "&userid_iassign=" . $USER->id;
        }

      $enderecoPOST = "" . $CFG->wwwroot . "/mod/iassign/view.php?" . $param_aux;
      //if ($ilm->confirms_jar($iassign_statement_activity_item->file, $iassign_ilm->file_jar, $this->cm->id))
      // Prepare tags to present the iLM
      print $OUTPUT->box($ilm->view_iLM($iassign_statement_activity_item, $student_answer, $enderecoPOST, true));
      } // if (($this->action != 'viewsubmission') && has_capability('mod/iassign:evaluateiassign', $USER->context, $USER->id))
    // *** (end) Teacher access (view the activity)

    // *** Teacher access (view learner's submission to the activity)
    elseif (($this->action == 'viewsubmission') && has_capability('mod/iassign:evaluateiassign', $USER->context, $USER->id)) {

      // It is teacher or 'nonediting teacher' that can evaluate
      // ----> area teacher evaluate

      $row = optional_param('row', 0, PARAM_INT);
      $column = optional_param('column', 0, PARAM_INT);

      $link_next = iassign_icons::insert('right_disable');
      $link_previous = iassign_icons::insert('left_disable');
      $link_up = iassign_icons::insert('up_disable');
      $link_down = iassign_icons::insert('down_disable');

      // next_activity
      if ($USER->matrix_iassign[$row][$column]->iassign_next != - 1) {
        $url_next = "view.php?action=viewsubmission&id=$id&iassign_submission_current=" . $USER->matrix_iassign[$row][$column + 1]->iassign_submission_current . "&userid_iassign=$this->userid_iassign&iassign_current=" . $USER->matrix_iassign[$row][$column]->iassign_next . "&view_iassign=report&row=" . ($row) . "&column=" . ($column + 1);
        $link_next = "<a href='" . $url_next . "'>" . (iassign_icons::insert('next_activity')) . "</a>";
        }
      // previous_activity
      if ($USER->matrix_iassign[$row][$column]->iassign_previous != - 1) {
        $url_previous = "view.php?action=viewsubmission&id=$id&iassign_submission_current=" . $USER->matrix_iassign[$row][$column - 1]->iassign_submission_current . "&userid_iassign=$this->userid_iassign&iassign_current=" . $USER->matrix_iassign[$row][$column]->iassign_previous . "&view_iassign=report&row=" . ($row) . "&column=" . ($column - 1);
        $link_previous = "<a href='" . $url_previous . "'>" . (iassign_icons::insert('previous_activity')) . "</a>";
        }
      // previous_student
      if ($USER->matrix_iassign[$row][$column]->user_next != - 1) {
        $url_down = "view.php?action=viewsubmission&id=$id&iassign_submission_current=" . $USER->matrix_iassign[$row + 1][$column]->iassign_submission_current . "&userid_iassign=" . $USER->matrix_iassign[$row][$column]->user_next . "&iassign_current=" . $this->activity->get_activity()->id . "&view_iassign=report&row=" . ($row + 1) . "&column=" . ($column);
        $link_down = "<a href='" . $url_down . "'>" . (iassign_icons::insert('previous_student')) . "</a>";
        }
      // next_student
      if ($USER->matrix_iassign[$row][$column]->user_previous != - 1) {
        $url_up = "view.php?action=viewsubmission&id=$id&iassign_submission_current=" . $USER->matrix_iassign[$row - 1][$column]->iassign_submission_current . "&userid_iassign=" . $USER->matrix_iassign[$row][$column]->user_previous . "&iassign_current=" . $this->activity->get_activity()->id . "&view_iassign=report&row=" . ($row - 1) . "&column=" . ($column);
        $link_up = "<a href='" . $url_up . "'>" . (iassign_icons::insert('next_student')) . "</a>";
        }

      if ($iassign_submission) {
        $student_answer = $iassign_submission->answer;
        }

      $last_iassign = get_string('last_iassign', 'iassign');

      $user_data = $DB->get_record("user", array('id' => $this->userid_iassign));

      // Messages related to due date (and user role)
      $this->activity->view_dates();
      print $OUTPUT->box_start();
      print '<table width=100% border=0 valign="top"><tr>' . "\n";
      print '<td width=80%><font color="blue"><strong>' . get_string('area_available', 'iassign') . '</strong></font><br>' . "\n";
      print $OUTPUT->user_picture($user_data);
      print '&nbsp;' . $user_data->firstname . '&nbsp;' . $user_data->lastname;
      print '</td>' . "\n";
      print '<td width=20% align=right>' . "\n";
      print '<table width=50 cellpadding="0">';
      print '<tr><td colspan=2 align=center>' . $link_up . '</td></tr>' . "\n";
      print '<tr><td align=center>' . $link_previous . '</td>' . "\n";
      print '<td align=center>' . $link_next . '</td></tr>' . "\n";
      print '<td colspan=2 align=center>' . $link_down . '</td></tr>' . "\n";
      print '</table>' . "\n";
      print '</td></tr></table>' . "\n";
      print $OUTPUT->box_end();

      print $OUTPUT->box_start();
      print '<table width=100% border=0 valign="top"><tr>' . "\n";
      print '<td width=60% valign="top">' . "\n";
      print '<strong>' . get_string('proposition', 'iassign') . '</strong>' . "\n";
      print '<p>' . $iassign_statement_activity_item->proposition . '</p>' . "\n";

      if ($iassign_statement_activity_item->automatic_evaluate == 1)
        $resp = get_string('yes');
      else
        $resp = get_string('no');
      print '<p>' . get_string('automatic_evaluate', 'iassign') . '&nbsp;' . $resp . '</p>' . "\n";
      if ($iassign_statement_activity_item->show_answer == 1)
        $resp = get_string('yes');
      else
        $resp = get_string('no');
      print '<p>' . get_string('show_answer', 'iassign') . '&nbsp;' . $resp . '</p>' . "\n";
      print '</td>';

      if ($iassign_statement_activity_item->type_iassign == 3) { // type_iassign=3 => activity of type "exercise" - submit button and automatic evaluation
        print '<td width=40% valign="top" align="left">';
        print '<strong>' . get_string('status', 'iassign') . '</strong>' . "\n";

        // check status of solution sent by the student
        if ($iassign_submission) {
          switch ($iassign_submission->status) {
            case 3 :
              print iassign_icons::insert('correct') . '&nbsp;' . get_string('correct', 'iassign') . '&nbsp;' . $comment;
              break;
            case 2 :
              print iassign_icons::insert('incorrect') . '&nbsp;' . get_string('incorrect', 'iassign') . '&nbsp;' . $comment;
              break;
            case 1 :
              print iassign_icons::insert('post') . '&nbsp;' . get_string('post', 'iassign') . '&nbsp;' . $comment;
              break;
            default :
              print iassign_icons::insert('not_post') . '&nbsp;' . get_string('not_post', 'iassign') . '&nbsp;' . $comment;
              $last_iassign = get_string('no_iLM_PARAM_ArchiveContent', 'iassign');
            } // switch ($iassign_submission->status)
        } else { // if ($iassign_submission)
          print iassign_icons::insert('not_post') . '&nbsp;' . get_string('not_post', 'iassign') . '&nbsp;' . $comment;
          $last_iassign = get_string('no_iLM_PARAM_ArchiveContent', 'iassign');
          }

        // update_status
        if ($iassign_submission && $iassign_submission->experiment > 0) {
            $edit_status = $CFG->wwwroot . "/mod/iassign/view.php?action=edit_status&id=" . $id . "&userid_iassign=" . $this->userid_iassign . "&iassign_current=" . $this->activity->get_activity()->id . "&iassign_submission_current=" . $this->iassign_submission_current . "&row=" . ($row) . "&column=" . ($column);

            print " <script type='text/javascript'>
  //<![CDATA[
  function overwriteStatus (newstatus) {
    if (confirm('" . get_string('confirm_change_situation', 'iassign') . "')) {
      document.formEditStatus.return_status.value=newstatus;
      document.formEditStatus.submit();
      }
    else
      document.formEditStatus.return_status.value=-1;
    }
  //]]>
  </script>";

            print "<form name='formEditStatus' method='post' action='$edit_status' enctype='multipart/form-data'>\n";
            print ' <font color="blue"><strong>' . get_string('changeto', 'iassign') . "</strong></font>\n";
            print " <select name='status' onchange= 'overwriteStatus(this.value)'>\n" . " <option value=\"3\">" . get_string('correct', 'iassign') . "</option>\n" . " <option value=\"2\">" . get_string('incorrect', 'iassign') . "</option>\n" . " <option value=\"1\">" . get_string('post', 'iassign') . "</option>\n" . " <option value=\"0\">" . get_string('not_post', 'iassign') . "</option>\n" . " <option value=\"-1\" selected>" . get_string('newsituation', 'iassign') . "</option>\n" . " </select>\n";
            print " <input type='hidden' name='return_status'>\n";
            print "</form>\n";

            print '<p><strong>' . get_string('grade_student', 'iassign') . '</strong>&nbsp;' . $iassign_submission->grade . "</p>\n";
            print '<p><strong>' . get_string('grade_iassign', 'iassign') . '</strong>&nbsp;' . $iassign_statement_activity_item->grade . "</p>\n";
            $edit_grade = $CFG->wwwroot . "/mod/iassign/view.php?action=edit_grade&id=" . $id . "&userid_iassign=" . $this->userid_iassign . "&iassign_current=" . $this->activity->get_activity()->id . "&iassign_submission_current=" . $this->iassign_submission_current . "&row=" . ($row) . "&column=" . ($column);
            print "
  <script type='text/javascript'>
  //<![CDATA[
  function overwriteGrade (newgrade,maxgrade) {
   if (newgrade<0 || newgrade>maxgrade) {
     alert('" . get_string('erro_grade', 'iassign') . " '+maxgrade)
     document.formEditGrade.return_grade.value=-1;
     document.formEditGrade.submit();
     }
   else {
     document.formEditGrade.return_grade.value=newgrade;
     document.formEditGrade.submit();
     }
    }
  //]]>
  </script>";
            print "<form name='formEditGrade' method='post' action='$edit_grade' enctype='multipart/form-data'>\n";
            print ' <font color="blue"><strong>' . get_string('changeto', 'iassign') . "</strong></font>" . "\n";
            print " <input type='text' name='grade' size='6'>";
            print " <input type='hidden' name='return_grade'> ";
            print " <input type=button value='" . get_string('confirm', 'iassign') . "' onClick = 'overwriteGrade(grade.value," . $iassign_statement_activity_item->grade . ")' " . "  title='" . get_string('confirm_new_grade', 'iassign') . "'>\n";
            print "</form>";

            $url_answer = "" . $CFG->wwwroot . "/mod/iassign/view.php?" . "action=download_answer&iassign_submission_id=" . $iassign_submission->id . "&id=" . $id;
            print '<p><strong>' . get_string('experiment', 'iassign') . '</strong>&nbsp;' . $iassign_submission->experiment . ' <a href="' . $url_answer . '">' . iassign_icons::insert('download_assign') . '</a></p>';

            print '<p><strong>' . get_string('timemodified', 'iassign') . '</strong>&nbsp;' . userdate($iassign_submission->timemodified) . '</p>';
            $teacher = $DB->get_record("user", array('id' => $iassign_submission->teacher));
            if ($teacher)
                print '<p><strong>' . get_string('last_modification', 'iassign') . '</strong>&nbsp;' . $teacher->firstname . '</p>' . "\n";
          } // if ($iassign_submission->experiment > 0)
        print '</td>';
        } // if ($iassign_statement_activity_item->type_iassign == 3)

      print '</tr></table>';
      print $OUTPUT->box_end();
      $USER->iassignEdit = $this->bottonPost;

      if ($iassign_submission && $allow_resubmission) {

        // Put the iLM to 
        print $OUTPUT->box_start();

        print '<p><strong>' . $last_iassign . '</strong></p>';
        //d if ($ilm->confirms_jar ( $iassign_statement_activity_item->file, $iassign_ilm->file_jar, $this->cm->id )) {
        $enderecoPOST = "";

        // Prepare tags to present the iLM
        print $ilm->view_iLM($iassign_statement_activity_item, $student_answer, $enderecoPOST, false);

        //d } // if ($this->confirms_jar($iassign_statement_activity_item->file, $iassign_ilm->file_jar))

        print $OUTPUT->box_end();

      } else { // if ($iassign_submission && $allow_resubmission) - techer view student answer

        // ATTENTION: exception used by iGeom (exercise with "script")
        $loadTeacherActivity = false; // use 'true' whenever 'special_param1 == 1'
        if (substr($ilm_name, 0, 5)=="igeom") {
          if ($iassign_statement_activity_item->special_param1 == 1) // if 1 => use the teacher activity with some complement from the student (in iGeom = GEO + SCR)
            $loadTeacherActivity = true;
          }

        print $OUTPUT->box('<p><strong>' . $last_iassign . '</strong></p>' . "\n");
        // Prepare tags to present the iLM
        print $OUTPUT->box($ilm->view_iLM($iassign_statement_activity_item, $student_answer, $enderecoPOST, $loadTeacherActivity));

        }

      if ($iassign_statement_activity_item->type_iassign == 3) { // type_iassign=3 => activity of type "exercise" - submit button and automatic evaluation
        $output = '';
        $history_comment = '';
        if ($iassign_submission) {
          $enderecoPOSTcomment = "" . $CFG->wwwroot . "/mod/iassign/view.php?id=" . $id . "&action=newcomment&iassign_current=" . $this->activity->get_activity()->id .
	    "&iassign_submission_current=" . $iassign_submission->id . "&userid_iassign=" . $this->userid_iassign . "&row=" . ($row) . "&column=" . ($column);
          $history_comment = $this->search_comment_submission($iassign_submission->id);
        } else {
          $enderecoPOSTcomment = "" . $CFG->wwwroot . "/mod/iassign/view.php?id=" . $id . "&action=newcomment&iassign_current=" . $this->activity->get_activity()->id .
	    "&userid_iassign=" . $this->userid_iassign . "&row=" . ($row) . "&column=" . ($column);
          }

        $output .= $OUTPUT->box_start();

        $output .= "<center><form name='formEnvioComment' id='formEnvioComment1' method='post' action='$enderecoPOSTcomment' enctype='multipart/form-data'>\n";
        $output .= "<p><textarea rows='2' cols='60' name='submission_comment'></textarea></p>\n";
        $output .= "<p><input type=submit value='" . get_string('submit_comment', 'iassign') . "'\></p>\n";
        $output .= "</form></center>\n";
        if (!empty($history_comment)) {
          $output .= "  <table id='outlinetable' class='generaltable boxaligncenter' cellpadding='5' width='100%'> \n";
          $output .= "     <tr><th>" . get_string('history_comments', 'iassign') . "</th></tr>";
          $output .= $history_comment;
          $output .= "</table>";
          }
        $output .= $OUTPUT->box_end();

        print $output;
        } // if ($iassign_statement_activity_item->type_iassign == 3)
      }    // elseif (($this->action == 'viewsubmission')
    // *** (end) Teacher access (view learner's submission to the activity)

    // *** Student access (view the activity)
    elseif (has_capability('mod/iassign:submitiassign', $USER->context, $USER->id)) {

      // It could be the learner (he could send or resend the activity)
      // ---> access student

      $time_now = time();
      if ($iassign_statement_activity_item->type_iassign == 1) { // type_iassign=1 => activity of type "exemple" - no submit button
        // activity of type example - not submit button for submission
        $this->view_iassign = true;
        //TODO rever esta condicao para iMA que nao fazem autoavaliacao
      } elseif ($iassign_statement_activity_item->type_iassign == 2 && $iassign_ilm->evaluate == 1) {
        // activity of type test - iLM automatic evaluator - submit button for submission
        if ($iassign_statement_activity_item->timeavailable < $time_now && $iassign_statement_activity_item->timedue > $time_now) { // activity within of deadline
          $this->bottonPost = 1;
          $this->view_iassign = true;
        } else
          $this->view_iassign = false;
      } elseif ($iassign_statement_activity_item->type_iassign == 3) { // type_iassign=3 => activity of type "exercise" - submit button and automatic evaluation
        // activity of type exercise (learner can send his answer, if yet open...)
        $this->view_iassign = true;
        if ($iassign_statement_activity_item->timeavailable > $time_now) // due date expired
          $this->view_iassign = false;
        elseif ($iassign_statement_activity_item->timedue > $time_now || $iassign_statement_activity_item->preventlate == 1) { // activity within due date
          $this->bottonPost = 1; // allow the submit button
          // Look at table 'iassign_submission' ('iassign_submission.experiment' is the number of submissions)

          $repeat_title = ' title="' . get_string('repeat_alt', 'iassign') . '" '; // 'Use this button to redo the activity'

          if (!$iassign_submission || $this->action == 'repeat' || ($iassign_submission && $allow_resubmission)) {
            if ($this->action != 'repeat') {
              $repeat = "<a href='view.php?action=repeat&id="
                 . $id . "&userid_iassign=" . $USER->id . "&iassign_current="
                 . $this->activity->get_activity()->id . "&iassign_submission_current="
                 . $iassign_submission->id . "'" . $repeat_title . ">"
                 . iassign_icons::insert('repeat') . '&nbsp;' . get_string('repeat', 'iassign') . "</a>\n";
              }
            $this->bottonPost = 1;
            $this->write_solution = 1; // can register his submission
            $student_answer = $iassign_submission->answer;
          } else {
            // In 'class ilm : view_iLM($iassign_statement_activity_item, $student_answer, $enderecoPOST, $view)'
            $last_iassign = get_string('last_iassign', 'iassign');
            if ($iassign_submission) {
              $repeat = "<a href='view.php?action=repeat&id=" . $id . "&userid_iassign=$USER->id&iassign_current=" . $this->activity->get_activity()->id .
	        "&iassign_submission_current=" . $iassign_submission->id . "'" . $repeat_title . ">" . iassign_icons::insert('repeat') . '&nbsp;' . get_string('repeat', 'iassign') . "</a>\n";
              $student_answer = $iassign_submission->answer;
            } else {
              $repeat = "<a href='view.php?action=repeat&id=" . $id . "&userid_iassign=$USER->id&iassign_current=" . $this->activity->get_activity()->id .
	        "'" . $repeat_title . ">" . iassign_icons::insert('repeat') . '&nbsp;' . get_string('repeat', 'iassign') . "</a>\n";
              }
            }
        } elseif ($iassign_statement_activity_item->test == 1) { // allowed to test after expired due date
          if ($this->action == 'repeat' || ($iassign_submission && $iassign_submission->experiment < 1)) {
            $this->bottonPost = 1;
            $this->write_solution = 0; // if iVProg it is valid to the learner to edit previous solution
          } else {
            $last_iassign = get_string('last_iassign', 'iassign');
            if ($iassign_submission) {
              $repeat = "<a href='view.php?action=repeat&id=" . $id . "&userid_iassign=$USER->id&iassign_current=" . $this->activity->get_activity()->id .
	        "&iassign_submission_current=" . $iassign_submission->id . "'" . $repeat_title . ">" . iassign_icons::insert('repeat') . '&nbsp;' . get_string('repeat', 'iassign') . "</a>\n";
              $student_answer = $iassign_submission->answer;
            } else {
              // Symbol of "redo activity"
              $repeat = "<a href='view.php?action=repeat&id=" . $id . "&userid_iassign=$USER->id&iassign_current=" . $this->activity->get_activity()->id .
	        "'" . $repeat_title . ">" . iassign_icons::insert('repeat') . '&nbsp;' . get_string('repeat', 'iassign') . "</a>\n";
              }
            }
          } // elseif ($iassign_statement_activity_item->test == 1)
        elseif ($iassign_statement_activity_item->test == 0)
          $this->view_iassign = false;
        } // elseif ($iassign_statement_activity_item->type_iassign == 3)

      if ($iassign_submission)
        $param_aux = "action=get_answer&iassign_submission_current=" . $iassign_submission->id . "&id=" . $id . "&iassign_current=" . $this->activity->get_activity()->id .
	  "&write_solution=" . $this->write_solution . "&userid_iassign=" . $USER->id;
      else
        $param_aux = "action=get_answer&id=" . $id . "&iassign_current=" . $this->activity->get_activity()->id . "&write_solution=" . $this->write_solution . "&userid_iassign=" . $USER->id;

      $enderecoPOST = "" . $CFG->wwwroot . "/mod/iassign/view.php?" . $param_aux;

      $this->view_legend_icons();
      $this->activity->view_dates();

      if ($this->view_iassign) {

        print $OUTPUT->box_start();

        print '<table width=100% border=0 valign="top">' . "\n";
        print '<tr><td width=60% valign="top">' . "\n";
        print '<strong>' . get_string('proposition', 'iassign') . '</strong>' . "\n";
        print '<p>' . $iassign_statement_activity_item->proposition . '</p>' . "\n";
        $flag_dependency = true;

        if ($iassign_statement_activity_item->type_iassign == 3) {
          if ($iassign_statement_activity_item->dependency == 0) {
            print '<strong>' . get_string('independent_activity', 'iassign') . '</strong>' . "\n";
          } else {
            $dependencys = explode(';', $iassign_statement_activity_item->dependency);
            print '<p><strong>' . get_string('dependency', 'iassign') . '</strong></p>' . "\n";

            foreach ($dependencys as $dependency) {
              if ($dependency) {
                $dependencyiassign = $DB->get_record("iassign_statement", array("id" => $dependency));
                $dependencysubmissions = $DB->get_record("iassign_submission", array("iassign_statementid" => $dependencyiassign->id, 'userid' => $USER->id));
                if ($dependencysubmissions) {
                  if ($dependencysubmissions->status == 3)
                    $icon = iassign_icons::insert('correct');
                  elseif ($dependencysubmissions->status == 2) {
                    $icon = iassign_icons::insert('incorrect');
                    $flag_dependency = false;
                  } elseif ($dependencysubmissions->status == 1) {
                    $icon = iassign_icons::insert('post');
                    $flag_dependency = false;
                  } elseif ($dependencysubmissions->status == 0) {
                    $icon = iassign_icons::insert('not_post');
                    $flag_dependency = false;
                    }
                } else {
                  $icon = iassign_icons::insert('not_post');
                  $flag_dependency = false;
                  } // if ($dependencysubmissions)

                print '<p>&nbsp;' . $icon . $dependencyiassign->name . '</p>' . "\n";
                } // if ($dependency)
              } // foreach ($dependencys as $dependency)
            } // if ($iassign_statement_activity_item->dependency == 0)
          } // if ($iassign_statement_activity_item->type_iassign == 3)

        if ($flag_dependency == false) {
          print '<strong>' . get_string('message_dependency', 'iassign') . '</strong>' . "\n";
          $this->view_iassign = false;
          print '</tr></table>' . "\n";
        } else {
          $this->view_iassign = true;
          print '</td>' . "\n";
          } // if ($flag_dependency == false)

        if ($this->view_iassign) { //TODO it is already inside 'if ($this->view_iassign)'!!!!
          if ($iassign_statement_activity_item->type_iassign == 3) { // activity is present only if exercise
            // receiver=1 - message to teacher
            // receiver=2 - message to student
            if ($iassign_submission) {
              $verify_message = $DB->get_record('iassign_submission_comment', array('iassign_submissionid' => $iassign_submission->id, 'return_status' => 0, 'receiver' => 2));
              if ($verify_message)
                $comment = iassign_icons::insert('comment_unread');
              }
            print '<td width=40% valign="top" align="left">';
            print '<strong>' . get_string('status', 'iassign') . '</strong>' . "\n";

            if ($iassign_statement_activity_item->show_answer == 1) {

              // check status of solution sent by the student
              if ($iassign_submission) {
                switch ($iassign_submission->status) {
                  case 3 :
                    print iassign_icons::insert('correct') . '&nbsp;' . get_string('correct', 'iassign') . '&nbsp;' . $comment;
                    break;
                  case 2 :
                    print iassign_icons::insert('incorrect') . '&nbsp;' . get_string('incorrect', 'iassign') . '&nbsp;' . $comment;
                    break;
                  case 1 :
                    print iassign_icons::insert('post') . '&nbsp;' . get_string('post', 'iassign') . '&nbsp;' . $comment;
                    break;
                  default :
                    print iassign_icons::insert('not_post') . '&nbsp;' . get_string('not_post', 'iassign') . '&nbsp;' . $comment;
                    $repeat = "";
                    $last_iassign = "";
                  } // switch ($iassign_submission->status)
              } else {
                  print iassign_icons::insert('not_post') . '&nbsp;' . get_string('not_post', 'iassign') . '&nbsp;' . $comment;
                  $repeat = "";
                  $last_iassign = "";
                }

              if ($iassign_submission && $iassign_submission->experiment > 0) {
                print '<p><strong>' . get_string('grade_student', 'iassign') . ':</strong>&nbsp;' . $iassign_submission->grade;
                print '&nbsp;&nbsp;(' . get_string('grade_iassign', 'iassign') . ':&nbsp;' . $iassign_statement_activity_item->grade . ')</p>' . "\n";

                print '<p><strong>' . get_string('experiment_student', 'iassign') . '</strong>&nbsp;' . $iassign_submission->experiment;

                if ($iassign_statement_activity_item->max_experiment == 0)
                  print '&nbsp;&nbsp(' . get_string('experiment_iassign', 'iassign') . '&nbsp;' . get_string('ilimit', 'iassign') . ')</p>' . "\n";
                else {
                  print '&nbsp;&nbsp(' . get_string('experiment_iassign', 'iassign') . '&nbsp;' . $iassign_statement_activity_item->max_experiment . ')</p>' . "\n";
                  if ($iassign_submission->experiment >= $iassign_statement_activity_item->max_experiment) {
                    $repeat = "";
                    $last_iassign .= "&nbsp;<font color=red>" . get_string('attempts_exhausted', 'iassign') . '</font>' . "\n";
                    $this->bottonPost = 0;
                    $this->write_solution = 0;
                    }
                  } // else if ($iassign_statement_activity_item->max_experiment == 0)

                print '<p><strong>' . get_string('timemodified', 'iassign') . '</strong>&nbsp;' . userdate($iassign_submission->timemodified) . '</p>' . "\n";
                $teacher = $DB->get_record("user", array('id' => $iassign_submission->teacher));
                if ($teacher)
                  print '<p><strong>' . get_string('last_modification', 'iassign') . '</strong>&nbsp;' . $teacher->firstname . '</p>' . "\n"; // "
                } // if ($iassign_submission && $iassign_submission->experiment > 0)

              } // if ($iassign_statement_activity_item->show_answer==1)
            else {
              if (!isset($iassign_submission) || $iassign_submission->status == 0) {
                print iassign_icons::insert('not_post') . '&nbsp;' . get_string('not_post', 'iassign') . '&nbsp;' . $comment;
                $repeat = "";
                $last_iassign = "";
              } elseif ($iassign_submission->status == 1) {
                print iassign_icons::insert('post') . '&nbsp;' . get_string('post', 'iassign') . '&nbsp;' . $comment;
                }
              }

            print '</td>';
            } // if ($iassign_statement_activity_item->type_iassign == 3)

          print '</tr></table>' . "\n";

          // Presents the iLM
          print '<table width=100% border=0 valign="top">' . "\n";
          print '<td width=80% align="left">';
          print '<strong>' . $last_iassign . ' ' . get_string('repeat_msg', 'iassign') . '</strong></td>' . "\n"; // If you want to do this activity from the beginning again, use the \"Redo button\".
          print '<td width=20% align="rigth">';
          //D $ilm_name = strtolower($this->ilm->name); //if (substr($ilm_name, 0, 5) == "igeom") ; // is iGeom exercise
          print $repeat; // symbol of "redo activity"
          print '</td></tr></table>' . "\n";
          print $OUTPUT->box_end();

          $output = '';

          if (!$iassign_ilm) {
            $iassign_ilm = new stdClass();
            $iassign_ilm->file_jar = "";
            }

          $output .= $OUTPUT->box_start();

          $USER->iassignEdit = $this->bottonPost;

          // ---
          // Presents the iLM
          // Prepare tags to present the iLM
          if (!$iassign_submission || $this->action == 'repeat') { // or $iassign_submission->answer==0
            $student_answer = ""; //?
            $output .= $ilm->view_iLM($iassign_statement_activity_item, $student_answer, $enderecoPOST, true); // presents iLM and true => see the teacher file
          } elseif ($iassign_submission && $iassign_submission->answer == '0') {
            $student_answer = "";
            $output .= $ilm->view_iLM($iassign_statement_activity_item, $student_answer, $enderecoPOST, true); // presents iLM
          } else {
            // When student is redoing his activity

            // ATTENTION: exception used by iGeom (exercise with "script")
            $loadTeacherActivity = false; // trocar para 'true' se 'special_param1 == 1'
            if (substr($ilm_name, 0, 5)=="igeom") {
              if ($iassign_statement_activity_item->special_param1 == 1) // if 1 => use the teacher activity with some complement from the student (in iGeom = GEO + SCR)
                $loadTeacherActivity = true;
              }

            $output .= $ilm->view_iLM($iassign_statement_activity_item, $student_answer, $enderecoPOST, $loadTeacherActivity); // presents iLM
            }

          if ($iassign_statement_activity_item->type_iassign == 3) {
            $history_comment = '';
            if ($iassign_submission) {
              $history_comment = $this->search_comment_submission($iassign_submission->id);
              }

            if (!empty($history_comment)) {
              $output .= "\n  <table id='outlinetable' class='generaltable boxaligncenter' cellpadding='5' width='100%'>\n" .
                "   <tr><th>" . get_string('history_comments', 'iassign') . "</th></tr>\n"; //
              $output .= $history_comment;
              $output .= "</table>\n";
              }
            $output .= "</form></center>\n";
            $output .= $OUTPUT->box_end();
            print $output;
          } else { // if ($iassign_statement_activity_item->type_iassign == 3)
            $output .= $OUTPUT->box_end();
            print $output;
            }
          } // if ($this->view_iassign)
        } // if ($this->view_iassign)
      } // elseif (has_capability('mod/iassign:submitiassign', $USER->context, $USER->id))
    else
    if (isguestuser()) { // else of elseif (has_capability('mod/iassign:submitiassign', $USER->context, $USER->id))
      print($OUTPUT->notification(get_string('no_permission_iassign', 'iassign'), 'notifyproblem'));
      print '<table width=100% border=0 valign="top">' . "\n";
      print '<tr><td width=60% valign="top">' . "\n";
      print '<strong>' . get_string('proposition', 'iassign') . '</strong>' . "\n";
      print '<p>' . $iassign_statement_activity_item->proposition . '</p>' . "\n";
      print '</tr></table>' . "\n";
      $student_answer = "";
      $enderecoPOST = "";

      // Prepare tags to present the iLM
      $output = $ilm->view_iLM($iassign_statement_activity_item, $student_answer, $enderecoPOST, true);

      print $output;
      }

    // final block 'studant'
    print $OUTPUT->footer();
    die();
    } // function view_iassign_current()


  /// Display report of performance
  function report () {
    global $USER, $CFG, $DB, $OUTPUT;
    $id = $this->cm->id;
    $iassign_list = $DB->get_records_list('iassign_statement', 'iassignid', array('iassignid' => $this->iassign->id), "position ASC");

    if ($this->action != 'print') {
      $title = get_string('report', 'iassign');
      print $OUTPUT->header();
      } // if ($this->action != 'print')
    print $OUTPUT->box_start();

    $this->view_legend_icons();
    print '<p>' . get_string('ps_experiment', 'iassign') . '</p>';
    print '<p>' . get_string('ps_comment', 'iassign') . '</p>';
    print $OUTPUT->box_end();

    if ($this->action == 'print')
      print '<table border=1 width="100%">' . "\n";
    else
      print '<table id="outlinetable" class="generaltable boxaligncenter" cellpadding="5" width="100%">' . "\n";
    print '<tr><th colspan=2 class="header c1">' . iassign_utils::remove_code_message($this->iassign->name) . '</th></tr>' . "\n";
    // $num = array();
    $i = 1;
    $num = array();

    foreach ($iassign_list as $iassign) {
      $test_exercise = "";
      $iassign_submission = $DB->get_records("iassign_submission", array("iassign_statementid" => $iassign->id));
      if (($iassign_submission) && $iassign->type_iassign < 3) {
        $test_exercise = "&nbsp;<b>(" . get_string('iassign_exercise', 'iassign') . ")</b>";
        } // if (($iassign_submission) && $iassign->type_iassign < 3)
      if ($iassign->type_iassign == 3 || ($iassign_submission)) {
        $num[$i] = new stdClass();
        $num[$i]->name = $iassign->name;
        $num[$i]->id = $i;
        $num[$i]->iassignid = $iassign->id;
        print ' <tr >' . "\n";
        print "  <td class=\"cell c1 numviews\" width=5% align='center'><strong>" . $num[$i]->id . "</strong></td>\n";
        print "<td class=\"cell c0 actvity\">";
        print '&nbsp;' . $num[$i]->name . '&nbsp;' . $test_exercise . "</td>";
        print ' </tr>' . "\n";
        $i ++;
        } // if ($iassign->type_iassign == 3 || ($iassign_submission))
      } // foreach ($iassign_list as $iassign)
    print "</table>";
    print "<p></p>";
    if ($this->action == 'print')
      print '<table border=1 width="100%">' . "\n";
    else
      print '<table id="outlinetable" class="generaltable boxaligncenter" cellpadding="5" width="100%">' . "\n";
    $context = context_course::instance($this->course->id);
    if ($i > 1) {

      // $role = $DB->get_record_sql("SELECT s.id, s.shortname FROM {$CFG->prefix}role s WHERE s.shortname = 'student'");
      $params = array('shortname' => 'student');
      $role = $DB->get_record_sql(
        "SELECT s.id, s.shortname FROM {role} s " .
        " WHERE s.shortname = :shortname", $params);

      // $students = $DB->get_records_sql("SELECT s.userid, a.firstname, a.lastname FROM {$CFG->prefix}role_assignments s, {$CFG->prefix}user a WHERE s.contextid = '$context->id' AND s.userid = a.id AND s.roleid = '$role->id' ORDER BY a.firstname ASC,a.lastname ASC");
      $params = array('contextid' => $context->id, 'roleid' => $role->id);
      $students = $DB->get_records_sql(
        "SELECT s.userid, a.firstname, a.lastname FROM {role_assignments} s, {user} a " .
        " WHERE s.contextid = :contextid AND s.userid = a.id AND s.roleid = :roleid " .
        " ORDER BY a.firstname ASC,a.lastname ASC", $params);

      print '<tr><th class="header c1">' . get_string('students', 'iassign') . '</th>' . "\n";
      for ($j = 1; $j < $i; $j ++) {
        $sum_iassign_correct[$j] = 0;
        print '<th class="header c1" scope="col">' . $num[$j]->id . '</th>' . "\n"; // <th class="header c1" scope="col">
        }
      print '<th class="header c1" width=5%> ' . get_string('functions', 'iassign') . '</th>';
      $sum_iassign = $j - 1;
      print '</tr>' . "\n";
      $total = 0;
      $sum_student = 0;
      $comment = iassign_icons::insert('comment_read');
      $sum_comment = 0;
      $sum_correct_iassign = array();
      $sum_correct_student = array();

      $USER->matrix_iassign = array();
      if ($students) {
        $w = 0;
        foreach ($students as $tmp) {
          $users_array[$w] = $tmp;
          $w ++;
          }

        for ($x = 0; $x < $w; $x ++) {
          print '<tr>' . "\n";
          $sum_student ++;
          $name = $users_array[$x]->firstname . '&nbsp;' . $users_array[$x]->lastname;
          print '  <td >' . $name . '</td>' . "\n";
          $total_student = 0;
          $tentativas = 0;

          for ($j = 1; $j < $i; $j ++) {
            $sum_comment = 0;

            $student_submissions = $DB->get_record("iassign_submission", array('iassign_statementid' => $num[$j]->iassignid, 'userid' => $users_array[$x]->userid)); // data about student solution

            print '  <td valign="bottom" align="center">' . "\n";
            if ($student_submissions) {
              $last_solution_submission = " title=\"" . userdate($student_submissions->timemodified) . "\" "; // timemodified: time of the last student solution
              $tentativas = $student_submissions->experiment;

              // $student_submissions_comment = $DB->get_record_sql("SELECT COUNT(iassign_submissionid) FROM {$CFG->prefix}ia_assign_submissions_comment WHERE iassign_submissionid = '$student_submissions->id'");
              $params = array('iassign_submissionid' => $student_submissions->id);
              $student_submissions_comment = $DB->get_record_sql(
                "SELECT COUNT(iassign_submissionid) FROM {iassign_submission_comment} " .
                "WHERE iassign_submissionid = :iassign_submissionid", $params);

              if ($student_submissions_comment)
                foreach ($student_submissions_comment as $tmp)
                  $sum_comment = $tmp;

              // informations to previous activities
              if ($j - 1 < 1 || $j == $i)
                $iassign_previous = "-1";
              else
                $iassign_previous = $num[$j - 1]->iassignid;

              if ($x - 1 < 0 || $x == $w)
                $user_previous = "-1";
              else
                $user_previous = $users_array[$x - 1]->userid;

              // next
              if ($i - 1 > $j)
                $iassign_next = $num[$j + 1]->iassignid;
              else
                $iassign_next = "-1";

              if ($w - 1 > $x)
                $user_next = $users_array[$x + 1]->userid;
              else
                $user_next = "-1";

              $position = "&row= $x&column=$j";

              $url = "" . $CFG->wwwroot . "/mod/iassign/view.php?action=viewsubmission&id=" . $id . "&iassign_submission_current=" . $student_submissions->id .
                "&userid_iassign=" . $users_array[$x]->userid . "&iassign_current=" . $num[$j]->iassignid . "&view_iassign=" . $this->view_iassign;
              $url .= $position;

              // receiver=1 - message to teacher
              // receiver=2 - message to student
              // $verify_message = $DB->get_record_sql("SELECT COUNT(iassign_submissionid) FROM {$CFG->prefix}ia_assign_submissions_comment " .
              // "WHERE iassign_submissionid = '$student_submissions->id' AND return_status='0' AND receiver='1'");

              $params = array('iassign_submissionid' => $student_submissions->id, 'return_status' => '0', 'receiver' => '1');
              $verify_message = $DB->get_record_sql(
                "SELECT COUNT(iassign_submissionid) FROM {iassign_submission_comment} " .
                "WHERE iassign_submissionid = :iassign_submissionid " .
                "  AND return_status= :return_status " .
                "  AND receiver= :receiver", $params);

              if ($verify_message)
                  foreach ($verify_message as $tmp)
                    $sum_verify_message = $tmp;

              if ($sum_verify_message > 0)
                  $comment = iassign_icons::insert('comment_unread');
              else
                  $comment = iassign_icons::insert('comment_read');

              if ($student_submissions->status == 3) {
                  $sum_iassign_correct[$j] ++;
                  $total_student ++;
                  $feedback = iassign_icons::insert('correct');
                }
              elseif ($student_submissions->status == 2) {
                  $feedback = iassign_icons::insert('incorrect');
                }
              elseif ($student_submissions->status == 1) {
                  $feedback = iassign_icons::insert('post');
                }
              elseif ($student_submissions->status == 0) {
                  $feedback = iassign_icons::insert('not_post');
                }

              if ($this->action != 'print') {
                print '<table><tr>';
                if ($tentativas > 0)
                  print '<td> <a href="' . $url . '" ' . $last_solution_submission . '>' . $feedback . '</a> &nbsp;(' . $tentativas . ')</td>' . "\n";
                else
                  print '<td> <a href="' . $url . '" ' . $last_solution_submission . '>' . $feedback . '</a> </td>' . "\n";
                print '<td> &nbsp; </td>';
                print '</tr><tr>';
                print '<td> &nbsp;</td>';
                if ($sum_comment > 0 && $sum_verify_message > 0)
                  print '<td>  <a href="' . $url . '"> ' . $comment . '</a> &nbsp;(' . $sum_verify_message . '/' . $sum_comment . ') </td>' . "\n";
                else if ($sum_comment > 0)
                  print '<td>  <a href="' . $url . '"> ' . $comment . '</a> &nbsp;(' . $sum_comment . ') </td>' . "\n";
                else
                  print '<td> &nbsp;</td>' . "\n";
                print '</tr></table>' . "\n";
                }

              if ($this->action == 'print')
                  print $feedback . '&nbsp;(' . $tentativas . ')<br>' . $comment . '&nbsp;(' . $sum_comment . ')&nbsp;' . "\n";

              } // if ($student_submissions)
            else { // if ($student_submissions)

              // informations to browse previous activities
              if ($j - 1 < 1 || $j == $i)
                $iassign_previous = "-1";
              else
                $iassign_previous = $num[$j - 1]->iassignid;

              if ($x - 1 < 0 || $x == $w)
                $user_previous = "-1";
              else
                $user_previous = $users_array[$x - 1]->userid;

              // next
              if ($i - 1 > $j)
                $iassign_next = $num[$j + 1]->iassignid;
              else
                $iassign_next = "-1";

              if ($w - 1 > $x)
                $user_next = $users_array[$x + 1]->userid;
              else
                $user_next = "-1";

              $position = "&row= $x&column=$j";

              $url = $CFG->wwwroot . "/mod/iassign/view.php?action=viewsubmission&id=" . $id . "&userid_iassign=" . $users_array[$x]->userid .
                "&iassign_current=" . $num[$j]->iassignid . "&view_iassign=" . $this->view_iassign;
              $url .= $position;
              $feedback = iassign_icons::insert('not_post');
              if ($this->action == 'print')
                print $feedback . '&nbsp;(0)<br>' . $comment . '&nbsp;(' . $sum_comment . ')&nbsp;' . "\n";
              else {
                print '<table><tr>';
                print '<td> <a href="' . $url . '">' . $feedback . '</a> </td>' . "\n";
                print '<td> &nbsp; </td>';
                print '</tr><tr>' . "\n";
                print '<td> &nbsp;</td>';
                if ($sum_comment > 0)
                  print '<td> <a href="' . $url . '">' . $comment . '</a> &nbsp;(' . $sum_comment . ') </td>' . "\n";
                else
                  print '<td> &nbsp;</td>';
                print '</tr></table>';
                }
              } // else if ($student_submissions)
            $USER->matrix_iassign[$x][$j] = new stdClass();
            $USER->matrix_iassign[$x][$j]->iassign_previous = $iassign_previous;
            $USER->matrix_iassign[$x][$j]->user_previous = $user_previous;
            $USER->matrix_iassign[$x][$j]->iassign_next = $iassign_next;
            $USER->matrix_iassign[$x][$j]->user_next = $user_next;

            if ($student_submissions)
                $USER->matrix_iassign[$x][$j]->iassign_submission_current = $student_submissions->id;
            else
                $USER->matrix_iassign[$x][$j]->iassign_submission_current = 0;
            print '</td>' . "\n";
            } // for ($j=1; $j<$i; $j++)

          $total = $total + $total_student;
          $porcentagem = ($total_student / ($j - 1)) * 100;

          if ($tentativas != 0 && $tentativas != null) {
            $url_answer = "" . $CFG->wwwroot . "/mod/iassign/view.php?" . "action=download_all_answer&iassign_id=" . $this->iassign->id . "&userid=" . $users_array[$x]->userid . "&id=" . $id;
            print '  <td  align="center"><a href="' . $url_answer . '">' . iassign_icons::insert('download_all_assign') . '</a></td>' . "\n";
          } else {
            print '  <td  align="center">' . iassign_icons::insert('download_all_assign_disabled') . '</td>' . "\n";
            }

          print '</tr>' . "\n";
          $sum_correct_student[$sum_student] = new stdClass();
          $sum_correct_student[$sum_student]->name = $name;
          $sum_correct_student[$sum_student]->sum = $total_student;
          } // for ($x = 0; $x < $w; $x ++)

        for ($i = 1; $i < $j; $i ++) {
          if (is_null($sum_iassign_correct[$i]))
            $sum_iassign_correct[$i] = 0;

          $sum_correct_iassign[$i] = new stdClass();
          $sum_correct_iassign[$i]->sum = $sum_iassign_correct[$i];
          $sum_correct_iassign[$i]->name = $num[$i]->name;
          }
        // print '</tr></table>' . "\n";
        }
      } // if ($i>1)
    else {
      print_string('no_activity', 'iassign');
      }
    print "</table>\n";

    if ($this->action != 'print')
      print $OUTPUT->footer();
    die();
    } // function report()


  /// Display graphics of performance
  function stats () {
    global $USER, $CFG, $DB, $OUTPUT;
    $id = $this->cm->id;
    $iassign_list = $DB->get_records_list('iassign_statement', 'iassignid', array('iassignid' => $this->iassign->id), "position ASC");

    if ($this->action != 'printstats')
      $title = get_string('graphic', 'iassign');

    $num = array();
    $sum_correct_iassign = array();
    $sum_correct_student = array();
    $sum_student = 0;
    $i = 1;
    foreach ($iassign_list as $iassign) {
      $iassign_submission = $DB->get_records("iassign_submission", array("iassign_statementid" => $iassign->id));
      if ($iassign->type_iassign == 3) { // || ($iassign_submission)
        $sum_iassign_correct[$i] = 0;

        $num[$i] = new stdClass();
        $num[$i]->name = $iassign->name;
        $num[$i]->id = $i;
        $num[$i]->iassignid = $iassign->id;
        $i ++;
        } // if ($iassign->type_iassign == 3)
      } // foreach ($iassign_list as $iassign)
    $sum_iassign = $i - 1;

    $context = context_course::instance($this->course->id);

    if ($i > 1) {
      // $role = $DB->get_record_sql("SELECT s.id, s.shortname FROM {$CFG->prefix}role s WHERE s.shortname = 'student'");
      $params = array('shortname' => 'student');
      $role = $DB->get_record_sql(
        "SELECT s.id, s.shortname FROM {role} s " .
        " WHERE s.shortname = :shortname", $params);

      // $students = $DB->get_records_sql("SELECT s.userid, a.firstname, a.lastname FROM {$CFG->prefix}role_assignments s, {$CFG->prefix}user a WHERE s.contextid = '$context->id' AND s.userid = a.id AND s.roleid = '$role->id' ORDER BY a.firstname ASC,a.lastname ASC");

      $params = array('contextid' => $context->id, 'roleid' => $role->id);
      $students = $DB->get_records_sql(
        "SELECT s.userid, a.firstname, a.lastname FROM {role_assignments} s, {user} a " .
        " WHERE s.contextid = :contextid AND s.userid = a.id AND s.roleid = :roleid " .
        " ORDER BY a.firstname ASC,a.lastname ASC", $params);

      $total = 0;
      $sum_student = 0;
      $j = 0;
      $sum_correct_iassign = array();
      $sum_correct_student = array();
      $sum_experiment = array();
      if ($students) {
        foreach ($students as $users) {
          $sum_student ++;
          $name = $users->firstname . '&nbsp;' . $users->lastname;
          // rows
          $total_student = 0;

          for ($j = 1; $j < $i; $j ++) {
            $total_experiment = 0;
            $student_submissions = $DB->get_record("iassign_submission", array('iassign_statementid' => $num[$j]->iassignid, 'userid' => $users->userid)); // data about student solution

            if ($student_submissions) {
              if ($student_submissions->status == 3) {
                $sum_iassign_correct[$j] ++;
                $total_student ++;
                } // if ($student_submissions->status == 3)
              $total_experiment += $student_submissions->experiment;
              }
            $sum_experiment[$j] = $total_experiment;
            } // for ($j=1; $j<$i; $j++)

          $total = $total + $total_student;
          $sum_correct_student[$sum_student] = new stdClass();
          $sum_correct_student[$sum_student]->name = $name;
          $sum_correct_student[$sum_student]->sum = $total_student;
          } // foreach ($students as $users)
        }
      for ($i = 1; $i < $j; $i ++) {
        if (is_null($sum_iassign_correct[$i]))
            $sum_iassign_correct[$i] = 0;
        $sum_correct_iassign[$i] = new stdClass();
        $sum_correct_iassign[$i]->sum = $sum_iassign_correct[$i];
        $sum_correct_iassign[$i]->name = $num[$i]->name;
        $sum_correct_iassign[$i]->experiment = $sum_experiment[$i];
        } // for ($i = 1; $i < $j; $i++)
      } // if ($i > 1)

    if ($this->action != 'printstats') {
      $title = get_string('graphic', 'iassign');
      print $OUTPUT->header();
      $link_report = "<a href='" . $CFG->wwwroot . "/mod/iassign/view.php?id=" . $id . "&action=report&iassignid=" . $this->iassign->id . "'>" . iassign_icons::insert('view_report') . '&nbsp;' . get_string('report', 'iassign') . "</a>";
      $link_print_stats = "<a href='" . $CFG->wwwroot . "/mod/iassign/view.php?id=" . $id . "&action=printstats&&iassignid=" . $this->iassign->id . "'>" . iassign_icons::insert('print') . '&nbsp;' . get_string('print', 'iassign') . "</a>";
      print '<table width=100%><tr>';
      print '<td align="right">' . $link_print_stats . '</td>' . "\n";
      print '<td width=15% align="right">' . $link_report . '</td>';
      print '</td></tr></table>' . "\n";

      print "<br><br>";
      print '<table id="outlinetable" class="generaltable boxaligncenter" cellpadding="5" width="100%">' . "\n";
      print '<tr><th colspan=5 class="header c1">' . get_string('distribution_activity', 'iassign') . '</th></tr>' . "\n";
      print '<tr><td class=\'cell c0 actvity\' width=35%><strong>' . iassign_utils::remove_code_message($this->iassign->name) . '</strong></td>' . "\n";
      print '<td class=\'cell c0 actvity\' width=35%><strong>' . get_string('percentage_correct', 'iassign') . '</strong></td>' . "\n";
      print '<td class=\'cell c0 actvity\' width=10% align="right"><strong>' . get_string('proportion_correct', 'iassign') . '</strong>' . "\n";
      print '<td class=\'cell c0 actvity\' width=10% align="right"><strong>' . get_string('sum_experiment', 'iassign') . '</strong></td>' . "\n";
      print '<td class=\'cell c0 actvity\' width=10% align="right"><strong>' . get_string('avg_experiment', 'iassign') . '</strong></td>' . "\n";
      print '</tr>' . "\n";
      $sum_correct = 0;
      if ($sum_correct_iassign) {
        foreach ($sum_correct_iassign as $iassign) {
          if (is_null($iassign->experiment))
            $iassign->experiment = 0;
          $bar = "";
          $sum = $iassign->sum;
          $percent = ($sum / $sum_student) * 100;
          $text = number_format($percent, 1) . '%';
          $sum_correct += $sum;
          if ($sum > 0) {
            for ($i = 1; $i < $percent * 2; $i ++)
              $bar .= iassign_icons::insert('hbar_blue');
            $bar .= iassign_icons::insert('hbar_blue_r');
            } // if ($sum > 0)
          print '<tr ><td class=\'cell c0 actvity\'width=35%>' . $iassign->name . '</td>' . "\n";
          print ' <td class=\'cell c0 actvity\' width=35%>' . $bar . '&nbsp;' . $text . '</td>' . "\n";
          print ' <td class=\'cell c0 actvity\' width=10% align="right">' . $sum . '/' . $sum_student . '</td>' . "\n";
          print ' <td class=\'cell c0 actvity\' width=10% align="right">' . $iassign->experiment . '</td>' . "\n";
          print ' <td class=\'cell c0 actvity\' width=10% align="right">' . number_format($iassign->experiment / $sum_iassign, 1) . '</td>' . "\n";
          print '</tr>' . "\n";
          } // foreach ($sum_correct_iassign as $iassign)
        }
      print "</table>";
      print "<br><br>";
      print '<table id="outlinetable" class="generaltable boxaligncenter" cellpadding="5" width="100%">' . "\n";
      print '<tr><th colspan=3 class="header c1">' . get_string('distribution_student', 'iassign') . '</th></tr>' . "\n";
      print '<tr><td class=\'cell c0 actvity\' width=50%><strong>' . iassign_utils::remove_code_message($this->iassign->name) . '</strong></td>' . "\n";
      print ' <td class=\'cell c0 actvity\' width=40%><strong>' . get_string('percentage_correct', 'iassign') . '</strong></td>';
      print ' <td class=\'cell c0 actvity\' width=10% align="right"><strong>' . get_string('sum_correct', 'iassign') . '</strong></td>';
      print '</tr>' . "\n";
      $sum_correct = 0;
      foreach ($sum_correct_student as $student) {
        $bar = "";
        $sum = $student->sum;
        $percent = ($sum / $sum_iassign) * 100;
        $text = number_format($percent, 1) . '%';
        $sum_correct += $sum;
        if ($sum > 0) {
          for ($i = 1; $i < $percent * 2; $i ++)
            $bar .= iassign_icons::insert('hbar_blue');
          $bar .= iassign_icons::insert('hbar_blue_r');
          } // if ($sum > 0)
        print '<tr ><td class=\'cell c0 actvity\'width=50%>' . $student->name . '</td>' . "\n";
        print '<td class=\'cell c0 actvity\' width=40%>' . $bar . '&nbsp;' . $text . '</td>' . "\n";
        print '<td class=\'cell c0 actvity\' width=10% align="right">' . $sum . '/' . $sum_iassign . '</td>' . "\n";
        print '</tr>' . "\n";
        } // foreach ($sum_correct_student as $student)
      print "</table>\n";
      print "<br><br>\n";

      $var = 0;
      $cv = 0;
      $dv = 0;
      $avg = 0;
      if ($sum_correct_student) {
        $avg = $sum_correct / $sum_student;
        if ($avg > 0) {
          foreach ($sum_correct_student as $student)
            $var += pow($student->sum - $avg, 2);

          $var = $var / $sum_student;
          $dv = sqrt($var);
          $cv = ($dv / $avg) * 100;
          }
        }

      print '<table id="outlinetable" class="generaltable boxaligncenter" cellpadding="5" width="100%">' . "\n";
      print '<tr><th colspan=5 class="header c1">' . get_string('statistics', 'iassign') . '</th></tr>' . "\n";
      print '<tr><td class=\'cell c0 actvity\' width=20% align="center"><strong>' . get_string('sum_activity', 'iassign') . '</strong></td>' . "\n";
      print ' <td class=\'cell c0 actvity\' width=20% align="center"><strong>' . get_string('sum_student', 'iassign') . '</strong></td>' . "\n";
      print ' <td class=\'cell c0 actvity\' width=20% align="center"><strong>' . get_string('mean_score', 'iassign') . '</strong></td>' . "\n";
      print ' <td class=\'cell c0 actvity\' width=20% align="center"><strong>' . get_string('standard_deviation', 'iassign') . '</strong></td>' . "\n";
      print ' <td class=\'cell c0 actvity\' width=20% align="center"><strong>' . get_string('coefficient_variation', 'iassign') . '</strong></td></tr>' . "\n";
      print '<tr><td class=\'cell c0 actvity\' width=20% align="center">' . $sum_iassign . '</td>' . "\n";
      print ' <td class=\'cell c0 actvity\' width=20% align="center">' . $sum_student . '</td>' . "\n";
      print ' <td class=\'cell c0 actvity\' width=20% align="center">' . number_format($avg, 1) . '</td>' . "\n";
      print ' <td class=\'cell c0 actvity\' width=20% align="center">' . number_format($dv, 1) . '</td>' . "\n";
      print ' <td class=\'cell c0 actvity\' width=20% align="center">' . number_format($cv, 1) . '%</td></tr>' . "\n";
      print "</table>\n";

      print $OUTPUT->footer();
    } else {
      print "<STYLE TYPE='text/css'>
  <!--
  .boldtable {
    font-family:sans-serif;
    font-size:10pt;
    }
  -->
</STYLE>\n";

      print '<table border=1 width=100%>' . "\n";
      print '<tr><td colspan=3 align="center"><strong>' . get_string('distribution_activity', 'iassign') . '</strong></td></tr>' . "\n";
      print '<tr><td width=50%><strong>' . iassign_utils::remove_code_message($this->iassign->name) . '</strong></td>' . "\n";
      print '<td width=40%><strong>' . get_string('percentage_correct', 'iassign') . '</strong></td>';
      print '<td width=10% align="right"><strong>' . get_string('sum_correct', 'iassign') . '</strong></td>';
      print '</tr>' . "\n";
      $sum_correct = 0;
      foreach ($sum_correct_iassign as $iassign) {
        $bar = "";
        $sum = $iassign->sum;
        $percent = ($sum / $sum_student) * 100;
        $text = number_format($percent, 1) . '%';
        $sum_correct += $sum;
        if ($sum > 0) {
            for ($i = 1; $i < $percent * 2; $i ++)
                $bar .= iassign_icons::insert('hbar_blue');
            $bar .= iassign_icons::insert('hbar_blue_r');
          } // if ($sum > 0)
        print '<tr><td width=50%>' . $iassign->name . '</td>' . "\n";
        print '<td width=40%>' . $bar . '&nbsp;' . $text . '</td>';
        print '<td width=10% align="right">' . $sum . '/' . $sum_student . '</td>';
        print '</tr>' . "\n";
        } // foreach ($sum_correct_iassign as $iassign)
      print "</table>";
      print "<br><br>";
      print '<table border=1 class="boldtable" width=100%>' . "\n";
      print '<tr><td colspan=3 align="center" ><strong>' . get_string('distribution_student', 'iassign') . '</strong></td></tr>' . "\n";
      print '<tr><td width=50%><strong>' . iassign_utils::remove_code_message($this->iassign->name) . '</strong></td>' . "\n";
      print '<td  width=40%><strong>' . get_string('percentage_correct', 'iassign') . '</strong></td>';
      print '<td  width=10% align="right"><strong>' . get_string('sum_correct', 'iassign') . '</strong></td>';
      print '</tr>' . "\n";
      $sum_correct = 0;
      foreach ($sum_correct_student as $student) {
        $bar = "";
        $sum = $student->sum;
        $percent = ($sum / $sum_iassign) * 100;
        $text = number_format($percent, 1) . '%';
        $sum_correct += $sum;
        if ($sum > 0) {
          for ($i = 1; $i < $percent * 2; $i ++)
            $bar .= iassign_icons::insert('hbar_blue');
          $bar .= iassign_icons::insert('hbar_blue_r');
          } // if ($sum > 0)
        print "<tr><td width=50%>" . $student->name . "</td>\n";
        print ' <td width=40%>' . $bar . '&nbsp;' . $text . '</td>' . "\n";
        print ' <td width=10% align="right">' . $sum . '/' . $sum_iassign . '</td>' . "\n";
        print '</tr>' . "\n";
        } // foreach ($sum_correct_student as $student)
      print "</table>\n";
      print "<br/><br/>\n";

      $var = 0;
      $cv = 0;
      $dv = 0;
      $avg = 0;
      if ($sum_correct_student) {
        $avg = $sum_correct / $sum_student;
        if ($avg > 0) {
          foreach ($sum_correct_student as $student)
            $var += pow($student->sum - $avg, 2);
          $var = $var / $sum_student;
          $dv = sqrt($var);
          $cv = ($dv / $avg) * 100;
          }
        }

      print '<table border=1 class="boldtable" width=100%>' . "\n";
      print '<tr><td colspan=5 align="center"><strong>' . get_string('statistics', 'iassign') . '</strong></th></tr>' . "\n";
      print '<tr><td width=20% align="center"><strong>' . get_string('sum_activity', 'iassign') . '</strong></td>' . "\n";
      print '<td  width=20% align="center"><strong>' . get_string('sum_student', 'iassign') . '</strong></td>' . "\n";
      print '<td  width=20% align="center"><strong>' . get_string('mean_score', 'iassign') . '</strong></td>' . "\n";
      print '<td  width=20% align="center"><strong>' . get_string('standard_deviation', 'iassign') . '</strong></td>' . "\n";
      print '<td  width=20% align="center"><strong>' . get_string('coefficient_variation', 'iassign') . '</strong></td></tr>' . "\n";
      print '<tr><td  width=20% align="center">' . $sum_iassign . '</td>' . "\n";
      print '<td  width=20% align="center">' . $sum_student . '</td>' . "\n";
      print '<td  width=20% align="center">' . number_format($avg, 1) . '</td>' . "\n";
      print '<td  width=20% align="center">' . number_format($dv, 1) . '</td>' . "\n";
      print '<td  width=20% align="center">' . number_format($cv, 1) . '%</td></tr>' . "\n";
      print "</table>\n";
      } // if ($this->action != 'printstats')
    die();
    }


  /// Display graphics of performance for students
  function stats_students () {
    global $USER, $CFG, $DB, $OUTPUT;
    $id = $this->cm->id;
    $iassign_statement_list = $DB->get_records_sql("SELECT * FROM {iassign_statement} s " .
      " WHERE s.iassignid = '{$this->iassign->id}' AND s.type_iassign=3 ORDER BY s.position");

    $title = get_string('results', 'iassign');
    // echo $OUTPUT->header();

    $sum_correct = 0;
    $sum_incorrect = 0;
    $sum_post = 0;
    $sum_nopost = 0;
    $sum_iassign = count($iassign_statement_list);
    $bar_nopost = "";
    $bar_correct = "";
    $bar_incorrect = "";
    $bar_post = "";
    $text_nopost = "";
    $text_correct = "";
    $text_incorrect = "";

    foreach ($iassign_statement_list as $iassign_statement_activity_item) {
      $iassign_submission = $DB->get_record("iassign_submission", array('iassign_statementid' => $iassign_statement_activity_item->id, 'userid' => $USER->id)); // data about student solution
      if ($iassign_submission) {
        if ($iassign_submission->status == 3)
          $sum_correct ++;
        elseif ($iassign_submission->status == 2)
          $sum_incorrect ++;
        elseif ($iassign_submission->status == 1)
          $sum_post ++;
        elseif ($iassign_submission->status == 0 || !$iassign_submission)
          $sum_nopost ++;
        } // if ($iassign_submission)
      } // foreach ($iassign_statement_list as $iassign_statement_activity_item)

    if ($sum_iassign > 0) {
      $percent_correct = ($sum_correct / $sum_iassign) * 100;
      $text_correct = number_format($percent_correct, 1) . '%';
      }

    if ($sum_correct > 0) {
      for ($i = 1; $i < $percent_correct * 2; $i ++)
        $bar_correct .= iassign_icons::insert('hbar_green');
      $bar_correct .= iassign_icons::insert('hbar_green_r');
      } // if ($sum_correct > 0)

    if ($sum_iassign > 0) {
      $percent_incorrect = ($sum_incorrect / $sum_iassign) * 100;
      $text_incorrect = number_format($percent_incorrect, 1) . '%';
      }

    if ($sum_incorrect > 0) {
      for ($i = 1; $i < $percent_incorrect * 2; $i ++)
        $bar_incorrect .= iassign_icons::insert('hbar_red');
      $bar_incorrect .= iassign_icons::insert('hbar_red_r');
      } // if ($sum_incorrect > 0)

    if ($sum_iassign > 0) {
      $percent_post = ($sum_post / $sum_iassign) * 100;
      $text_post = number_format($percent_post, 1) . '%';
      }
    if ($sum_post > 0) {
      for ($i = 1; $i < $percent_post * 2; $i ++)
        $bar_post .= iassign_icons::insert('hbar_blue');
      $bar_post .= iassign_icons::insert('hbar_blue_r');
      } // if ($sum_post > 0)

    if ($sum_iassign > 0) {
      $percent_nopost = ($sum_nopost / $sum_iassign) * 100;
      $text_nopost = number_format($percent_nopost, 1) . '%';
      }
    if ($sum_nopost > 0) {
      for ($i = 1; $i < $percent_nopost * 2; $i ++)
        $bar_nopost .= iassign_icons::insert('hbar_orange');
      $bar_nopost .= iassign_icons::insert('hbar_orange_r');
      } // if ($sum_nopost > 0)

    print $OUTPUT->header();
    $link_return = "&nbsp;<a href='" . $this->return . "'>" . iassign_icons::insert('home') . get_string('activities_page', 'iassign') . "</a>";
    print '<table width=100%><tr>';
    print '<td align="right">' . $link_return . '</td>' . "\n";
    print '</td></tr></table>' . "\n";

    print "<br/><br/>\n";
    print '<table id="outlinetable" class="generaltable box aligncenter" cellpadding="5" width="100%">' . "\n";

    print '<tr><th colspan=3 class="header c1">' . "\n";
    // helpbutton('legend', get_string('legend', 'iassign'), 'iassign', $image = true, $linktext = false, $text = '', $return = false,
    // $imagetext = '');
    print iassign_utils::remove_code_message($this->iassign->name) . '</th></tr>' . "\n";

    print '<tr ><td class=\'cell c0 actvity\'width=50%>' . get_string('correct', 'iassign') . '</td>' . "\n";
    print '<td class=\'cell c0 actvity\' width=40%>' . $bar_correct . '&nbsp;' . $text_correct . '</td>';
    print '<td class=\'cell c0 actvity\' width=10% align="right">' . $sum_correct . '/' . $sum_iassign . '</td>';
    print '</tr>' . "\n";
    print '<tr ><td class=\'cell c0 actvity\'width=50%>' . get_string('incorrect', 'iassign') . '</td>' . "\n";
    print '<td class=\'cell c0 actvity\' width=40%>' . $bar_incorrect . '&nbsp;' . $text_incorrect . '</td>';
    print '<td class=\'cell c0 actvity\' width=10% align="right">' . $sum_incorrect . '/' . $sum_iassign . '</td>';
    print '</tr>' . "\n";
    if ($sum_post) {
      print '<tr ><td class=\'cell c0 actvity\'width=50%>' . get_string('post', 'iassign') . '</td>' . "\n";
      print '<td class=\'cell c0 actvity\' width=40%>' . $bar_post . '&nbsp;' . $text_post . '</td>';
      print '<td class=\'cell c0 actvity\' width=10% align="right">' . $sum_post . '/' . $sum_iassign . '</td>';
      print '</tr>' . "\n";
      } // if ($sum_post)
    print '<tr ><td class=\'cell c0 actvity\'width=50%>' . get_string('not_post', 'iassign') . '</td>' . "\n";
    print '<td class=\'cell c0 actvity\' width=40%>' . $bar_nopost . '&nbsp;' . $text_nopost . '</td>';
    print '<td class=\'cell c0 actvity\' width=10% align="right">' . $sum_nopost . '/' . $sum_iassign . '</td>';
    print '</tr>' . "\n";
    print "</table>";
    print "<br><br>";
    print '<table id="outlinetable" class="generaltable boxaligncenter" cellpadding="5" width="100%">' . "\n";
    print '<tr><th colspan=3 class="header c1">' . get_string('grades', 'iassign') . '</th></tr>' . "\n";
    print '<tr><td class=\'cell c0 actvity\' width=50%><strong>' . iassign_utils::remove_code_message($this->iassign->name) . '</strong></td>' . "\n";
    print '<td class=\'cell c0 actvity\' width=25% align=right><strong>' . get_string('grade_student', 'iassign') . '</strong></td>' . "\n";
    print '<td class=\'cell c0 actvity\' width=25% align=right><strong>' . get_string('grade_iassign', 'iassign') . '</strong></tr>' . "\n";

    $sum_grade = 0;
    $sum_grade_student = 0;
    $avg = 0;
    foreach ($iassign_statement_list as $iassign_statement_activity_item) {
      $iassign_submission = $DB->get_record("iassign_submission", array('iassign_statementid' => $iassign_statement_activity_item->id, 'userid' => $USER->id));
      if (!$iassign_submission) {
        $iassign_submission = new stdClass();
        $iassign_submission->grade = 0;
        }
      print '<tr ><td class=\'cell c0 actvity\'width=50%>' . $iassign_statement_activity_item->name . '</td>' . "\n";
      print '<td class=\'cell c0 actvity\' width=25% align=right>' . $iassign_submission->grade . '</td>';
      print '<td class=\'cell c0 actvity\' width=25% align=right>' . $iassign_statement_activity_item->grade . '</td>';
      print '</tr>' . "\n";

      $sum_grade += $iassign_statement_activity_item->grade;
      $sum_grade_student += $iassign_submission->grade;
      } // foreach ($iassign_statement_list as $iassign_statement_activity_item)
    if ($sum_grade > 0)
      $avg = $sum_grade_student / $sum_grade * 100;

    print '<tr><td class=\'cell c0 actvity\' width=50%><strong>' . get_string('total', 'iassign') . '</strong></td>' . "\n";
    print '<td class=\'cell c0 actvity\' width=25% align=right><strong>' . $sum_grade_student . '</strong></td>' . "\n";
    print '<td class=\'cell c0 actvity\' width=25% align=right><strong>' . $sum_grade . '</strong></tr>' . "\n";
    print '<tr><td class=\'cell c0 actvity\' width=25% align=left><strong>' . get_string('percentage_correct', 'iassign') . '</strong></td>' . "\n";
    print '<td colspan=2 class=\'cell c0 actvity\' align=right><strong>' . number_format($avg, 1) . '%</strong></tr>' . "\n";
    print "</table>";

    print $OUTPUT->footer();
    die();
    } // function stats_students()


  /// Display page of iAssign's activities
  function view_iassigns () {
    global $USER, $CFG, $COURSE, $DB, $OUTPUT;
    $id = $this->cm->id;

    $iassign_list = $DB->get_records_list('iassign_statement', 'iassignid', array('iassignid' => $this->iassign->id), 'position ASC');

    $notice = optional_param('notice', '', PARAM_TEXT);
    if (strpos($notice, 'error'))
      print($OUTPUT->notification(get_string($notice, 'iassign'), 'notifyproblem'));
    else if ($notice != '')
      print($OUTPUT->notification(get_string($notice, 'iassign'), 'notifysuccess'));

    print $OUTPUT->box_start();

    print '<table width=100% border=0><tr>' . "\n";
    $url_help = new moodle_url('/mod/iassign/settings_ilm.php', array('action' => 'list', 'ilm_id' => 1));
    $action_help = new popup_action('click', $url_help, 'iplookup', array('title' => get_string('help_ilm', 'iassign'), 'width' => 1200, 'height' => 700));

    $link_help = $OUTPUT->action_link($url_help, iassign_icons::insert('help_ilm') . get_string('help_ilm', 'iassign'), $action_help);
    $link_add = "<a href='" . $CFG->wwwroot . "/mod/iassign/view.php?id=" . $id . "&action=add&iassignid=" . $this->iassign->id . "'>" . iassign_icons::insert('add_iassign') . get_string('add_iassign', 'iassign') . "</a>";
    $link_report = "<a href='" . $CFG->wwwroot . "/mod/iassign/view.php?id=" . $id . "&action=report&iassignid=" . $this->iassign->id . "'>" . iassign_icons::insert('view_report') . get_string('report', 'iassign') . "</a>";

    // TODO: esta consulta esta sendo feita novamente na linha +/- 2258
    if (has_capability('mod/iassign:viewiassignall', $this->context, $USER->id)) {
      // could be "has_capability('mod/iassign:viewiassignall', $this->context, $USER->id)"
      // Has capability to see "report": teacher or up
      print '<td width=10% align="left">' . "\n";
      print $link_help;
      print '</td>' . "\n";
      print '<td width=10% align="left">' . "\n";
      print $link_report;
      print '</td>' . "\n";
      } // if (has_capability('mod/iassign:viewiassignall', $this->context, $USER->id))
    if (has_capability('mod/iassign:editiassign', $this->context, $USER->id)) {
      print '<td width=15% align="left">' . "\n";
      print $link_add;
      print "</td>\n";
      } // if (has_capability('mod/iassign:editiassign', $this->context, $USER->id))
    if (has_capability('mod/iassign:editiassign', $this->context, $USER->id)) {
      if ($iassign_list) {
        print '<td align="right">' . "\n";

        // $USER->iassignEdit == 0 view 'Turn editing off'
        // $USER->iassignEdit == 1 view 'Turn editing on'
        if (!isset($USER->iassignEdit))
          $USER->iassignEdit = 0;

        if ($USER->iassignEdit == 0) {
          $bottonEdit_message = get_string('turneditingon', 'iassign');
          $botton = 1;
          }     // if ($USER->iassignEdit == 0)
        elseif ($USER->iassignEdit == 1) {
          $bottonEdit_message = get_string('turneditingoff', 'iassign');
          $botton = 0;
          } // elseif ($USER->iassignEdit == 1)
        $editPost = "" . $CFG->wwwroot . "/mod/iassign/view.php?id=" . $id . "&botton=" . $botton;
        print "\n<form name='formEditPost' id='formEditPost' method='post' action='$editPost' enctype='multipart/form-data'>\n";
        print " <input type=submit value='$bottonEdit_message'/>\n";
        print "</form>\n";
        print "</td>\n";
        } // if ($iassign_list)
      }
    print '</tr></table>' . "\n";

    if (has_capability('mod/iassign:submitiassign', $this->context, $USER->id))
      $this->view_legend_icons();

    print $OUTPUT->box_end();

    $iassign_array_exercise = array();
    $i_exercise = 0;
    $iassign_array_test = array();
    $i_test = 0;
    $iassign_array_example = array();
    $i_example = 0;
    $iassign_array_general = array();
    $i_general = 0;

    if ($iassign_list) {

      if ($this->iassign->activity_group == 0) {
        foreach ($iassign_list as $iassign) {
          $iassign_array_general[$i_general] = $iassign;
          $i_general ++;
          }
        } // if ($this->iassign->activity_group == 0)
      else {
        foreach ($iassign_list as $iassign) {
          if ($iassign->type_iassign == 3) {
            $iassign_array_exercise[$i_exercise] = $iassign;
            $i_exercise ++;
            } // if ($iassign->type_iassign == 3)
          if ($iassign->type_iassign == 2) {
            $iassign_array_test[$i_test] = $iassign;
            $i_test ++;
            } // if ($iassign->type_iassign == 2)
          if ($iassign->type_iassign == 1) {
            $iassign_array_example[$i_example] = $iassign;
            $i_example ++;
            } // if ($iassign->type_iassign == 1)
          }
        }

      if ($iassign_array_exercise) {
        $title = get_string('exercise', 'iassign');
        $this->show_iassign($title, $iassign_array_exercise, $i_exercise);
        }
      if ($iassign_array_test) {
        $title = get_string('test', 'iassign');
        $this->show_iassign($title, $iassign_array_test, $i_test);
        }
      if ($iassign_array_example) {
        $title = get_string('example', 'iassign');
        $this->show_iassign($title, $iassign_array_example, $i_example);
        }
      if ($iassign_array_general) {
        $title = "";
        $this->show_iassign($title, $iassign_array_general, $i_general);
        }
      } else { // if ($iassign_list)
        print $OUTPUT->notification(get_string('no_activity', 'iassign'), 'notifysuccess');
        }

    if (count($iassign_list) > 5 && !(has_capability('mod/iassign:submitiassign', $this->context, $USER->id))) {
      if (has_capability('mod/iassign:viewiassignall', $this->context, $USER->id)) {
        print $OUTPUT->box_start();
        print '<table width=100% border=0><tr>' . "\n";
        print '<td width=10% align="left">' . "\n";
        print $link_report;
        print '</td>' . "\n";
        print '</tr></table>' . "\n";
        print $OUTPUT->box_end();
        } // if (has_capability('mod/iassign:viewiassignall', $this->context, $USER->id))
      if (has_capability('mod/iassign:editiassign', $this->context, $USER->id)) {
        print $OUTPUT->box_start();
        print '<table width=100% border=0><tr>' . "\n";
        print '<td align="left">' . "\n";
        print $link_add;
        print '</td>' . "\n";
        print '</tr></table>' . "\n";
        print $OUTPUT->box_end();
        } // if (has_capability('mod/iassign:editiassign', $this->context, $USER->id))
      } // if (count($iassign_list) > 5 && !(has_capability('mod/iassign:submitiassign', $this->context, $USER->id)))
    } // function view_iassigns()


  /// Display all iAssigns
  function show_iassign ($title, $iassign_array, $i) {
    global $USER, $CFG, $DB, $OUTPUT;

    $id = $this->cm->id;
    print $OUTPUT->box_start();
    if (has_capability('mod/iassign:viewiassignall', $this->context, $USER->id)) {
      print "<p><font color='#0000aa'><strong>" . $title . "</strong></font></p>";
      for ($j = 0; $j < $i; $j ++) {
        $iassign_current = $iassign_array[$j]->id;

        // receiver=1 - message to teacher
        // receiver=2 - message to student
        $sum_comment = 0;
        $iassign_submissions = $DB->get_records('iassign_submission', array('iassign_statementid' => $iassign_current));
        foreach ($iassign_submissions as $iassign_submission) {
          $params = array('iassign_submissionid' => $iassign_submission->id, 'return_status' => '0', 'receiver' => '1');
          $verify_message = $DB->get_record_sql("SELECT COUNT(iassign_submissionid) FROM {iassign_submission_comment} " .
            " WHERE iassign_submissionid = :iassign_submissionid AND return_status= :return_status AND receiver= :receiver", $params);
          if ($verify_message)
            foreach ($verify_message as $tmp)
              $sum_comment += $tmp;
          } // foreach ($iassign_submissions as $iassign_submission)

        if ($sum_comment == 0)
          $comment_unread = "";
        else {
          $comment_unread_message = get_string('comment_unread', 'iassign');
          if ($sum_comment == 1)
            $comment_unread_message = get_string('comment_unread_one', 'iassign');
          $comment_unread = "&nbsp;<a href='" . $CFG->wwwroot . "/mod/iassign/view.php?id=" . $id . "&action=report&iassignid=" . $this->iassign->id . "'><font color='red'>" .
            iassign_icons::insert('comment_unread') . "&nbsp;($sum_comment&nbsp;" . $comment_unread_message . ")</font></a>";
          }

        if ($j == $i - 1)
          $iassign_down = $iassign_array[$j]->id;
        else
          $iassign_down = $iassign_array[$j + 1]->id;
        if ($j > 0)
          $iassign_up = $iassign_array[$j - 1]->id;
        else
          $iassign_up = $iassign_array[$j]->id;
        if ($iassign_array[$j]->visible == 0)
          $links = "&nbsp;<a href='view.php?id=$id&userid_iassign=$USER->id&action=view&iassign_current=$iassign_current'><font color='#bbbbbb'>" . $iassign_array[$j]->name . "</font></a>";
        else
          $links = "&nbsp;<a href='view.php?id=$id&userid_iassign=$USER->id&action=view&iassign_current=$iassign_current'>" . $iassign_array[$j]->name . "</a>";
        $links .= $comment_unread;
        if (has_capability('mod/iassign:editiassign', $this->context, $USER->id) && $USER->iassignEdit == 1) {
          $aux = "&id=$id&iassign_current=$iassign_current&iassign_up=$iassign_up&iassign_down=$iassign_down";
          $link_up = "&nbsp;<a href='view.php?action=up$aux'>" . iassign_icons::insert('move_up_iassign') . "</a>";
          $link_down = "&nbsp;<a href='view.php?action=down$aux'>" . iassign_icons::insert('move_down_iassign') . "</a>";
          $link_delete = "&nbsp;<a href='view.php?action=delete$aux'>" . iassign_icons::insert('delete_iassign') . "</a>";
          $link_visible_hide = "&nbsp;<a href='view.php?action=visible$aux'>" . iassign_icons::insert('hide_iassign') . "</a>";
          $link_visible_show = "&nbsp;<a href='view.php?action=visible$aux'>" . iassign_icons::insert('show_iassign') . "</a>";
          $link_edit = "&nbsp;<a href='view.php?action=edit$aux'>" . iassign_icons::insert('edit_iassign') . "</a>";
          $link_duplicate_activity = "&nbsp;<a href='view.php?action=duplicate_activity$aux' >" . iassign_icons::insert('duplicate_iassign') . "</a>\n";
          if (count($iassign_array) > 1) {
            if ($j == 0)
              $links .= $link_down;
            elseif ($j == $i - 1)
              $links .= $link_up;
            else
              $links .= $link_up . $link_down;
            } // if (count($iassign_array) > 1)
          $links .= $link_edit . $link_delete;

          if ($iassign_array[$j]->visible == 0)
            $links .= $link_visible_show;
          else
            $links .= $link_visible_hide;

          $links .= $link_duplicate_activity;
          } // if ($USER->iassignEdit == 1 && has_capability('mod/iassign:editiassign', $this->context, $USER->id))

        print '<p>' . $links . '</p>' . "\n";
        }
      } // if (has_capability('mod/iassign:viewiassignall', $this->context, $USER->id))
    elseif (has_capability('mod/iassign:submitiassign', $this->context, $USER->id)) { // student
      print '<table width=100% ><tr>' . "\n";
      print "<td width=70% align='left'><font color='#0000aa'><strong>" . $title . "</strong></font></td>" . "\n";
      print '</tr></table>' . "\n";

      for ($j = 0; $j < $i; $j ++) {
        $icon_status = "";
        $icon_comment = "";
        if ($iassign_array[$j]->visible == 1) {
         $iassign_current = $iassign_array[$j]->id;
         $iassign_submission = $DB->get_record('iassign_submission', array('iassign_statementid' => $iassign_current, 'userid' => $USER->id));
         $links = "&nbsp;<a href='view.php?id=$id&userid_iassign=$USER->id&action=view&iassign_current=$iassign_current'>" . $iassign_array[$j]->name . "</a>";
         $icon_status = "";
         $icon_comment = "";
         if ($iassign_submission) {
           // receiver=1 - message to teacher
           // receiver=2 - message to student
           // $verify_message = $DB->get_record_sql("SELECT COUNT(iassign_submissionid) FROM {$CFG->prefix}ia_assign_submissions_comment WHERE iassign_submissionid = '$iassign_submission->id' and return_status= 0 and receiver=2");

           $params = array('iassign_submissionid' => $iassign_submission->id, 'return_status' => '0', 'receiver' => '2');
           $verify_message = $DB->get_record_sql(
             "SELECT COUNT(iassign_submissionid) FROM {iassign_submission_comment} " .
             " WHERE iassign_submissionid = :iassign_submissionid AND return_status= :return_status AND receiver= :receiver", $params);

           if ($verify_message)
             foreach ($verify_message as $tmp)
               $sum_comment = $tmp;

           if ($sum_comment > 0) {
             $comment_unread_message = get_string('comment_unread', 'iassign');
             if ($sum_comment == 1)
               $comment_unread_message = get_string('comment_unread_one', 'iassign');
             $icon_comment = "&nbsp;<font color='red'>" . iassign_icons::insert('comment_unread') . "&nbsp;($sum_comment&nbsp;" . $comment_unread_message . ")</font>";
             }
           // $icon_comment = iassign_icons::insert('comment_unread');

           if ($iassign_array[$j]->type_iassign == 3) {
             if ($iassign_array[$j]->show_answer == 1) {
               if ($iassign_submission->status == 3)
                 $icon_status = iassign_icons::insert('correct');
               elseif ($iassign_submission->status == 2)
                 $icon_status = iassign_icons::insert('incorrect');
               elseif ($iassign_submission->status == 1)
                 $icon_status = iassign_icons::insert('post');
               elseif ($iassign_submission->status == 0)
                 $icon_status = iassign_icons::insert('not_post');
               } // if ($iassign_array[$j]->show_answer==1)
             else {
               if ($iassign_submission->status == 0)
                 $icon_status = iassign_icons::insert('not_post');
               else
                 $icon_status = iassign_icons::insert('post');
               }
             } // if ($iassign_array[$j]->type_iassign == 3)
           } // if ($iassign_submission)
         elseif ($iassign_array[$j]->type_iassign == 3) {
             $icon_status = iassign_icons::insert('not_post');
           } // if ($iassign_array[$j]->type_iassign == 3)
         print '<p>' . $icon_status . '&nbsp;' . $links . '&nbsp;' . $icon_comment . '</p>' . "\n";
          } // if ($iassign_array[$j]->visible == 1)
        } // for ($j = 0; $j < $i; $j++)
      } else if (isguestuser()) {
      print($OUTPUT->notification(get_string('no_permission_iassign', 'iassign'), 'notifyproblem'));
      print '<table width=100% ><tr>' . "\n";
      print "<td width=70% align='left'><font color='#0000aa'><strong>" . $title . "</strong></font></td>" . "\n";
      print '</tr></table>' . "\n";

      for ($j = 0; $j < $i; $j ++) {
        $icon_status = "";
        $icon_comment = "";
        if ($iassign_array[$j]->visible == 1) {
          $iassign_current = $iassign_array[$j]->id;
          $links = "&nbsp;<a href='view.php?id=$id&userid_iassign=$USER->id&action=view&iassign_current=$iassign_current'>" . $iassign_array[$j]->name . "</a>";
          print '<p>' . $links . '</p>' . "\n";
          } // if ($iassign_array[$j]->visible == 1)
        }
      }
    print $OUTPUT->box_end();
    } // function show_iassign($title, $iassign_array, $i)


  /// Show message of return
  function return_home_course ($message) {
    //D global $DB, $OUTPUT;
    //D $link_return = "&nbsp;<a href='" . $this->return . "'>" . iassign_icons::insert('home') . get_string('activities_page', 'iassign') . "</a>";
    //D echo $OUTPUT->box_start();
    //D echo '<table width=100% border=0 valign="top">' . "\n";
    //D echo '<tr><td align="left"><strong>' . "\n";
    //D print_string($message, 'iassign');
    //D echo '</strong></td>' . "\n";
    //D echo '<td width=20% align="right">' . "\n";
    //D echo $link_return;
    //D echo '</td></tr></table>' . "\n";
    //D echo $OUTPUT->box_end();
    //D // echo $OUTPUT->footer();
    redirect(new moodle_url($this->return . '&notice=' . $message));
    exit;
    }

  /// Search comment of activity
  function search_comment_submission ($iassign_submissionid) {
    global $USER, $DB, $OUTPUT, $COURSE;
    $context = context_course::instance($COURSE->id);

    $comments = $DB->get_records_list('iassign_submission_comment', 'iassign_submissionid', array('iassign_submissionid' => $iassign_submissionid), 'timecreated DESC'); // 'ORDER BY "timecreated" ASC'
    $text = "";
    if ($comments) {

      foreach ($comments as $tmp) {
        $user_data = $DB->get_record("user", array('id' => $tmp->comment_authorid));
        if (has_capability('mod/iassign:editiassign', $context, $tmp->comment_authorid)) {
            $text .= "<tr><td bgcolor='#fee7ae'><b> $user_data->firstname</b>&nbsp;(" . userdate($tmp->timecreated) . "</br>\n";
            $text .= "$tmp->comment</td></tr>";
          } else {
            $text .= "<tr><td bgcolor='#dce7ec'>&raquo;<b>" . $user_data->firstname . "</b>&nbsp;(" . userdate($tmp->timecreated) . "</br>\n";
            $text .= $tmp->comment . "</td></tr>\n";
          }
        } // foreach ($comments as $tmp)
      }
    return $text;
    }


  /// Update comment of activity
  function update_comment ($iassign_submissionid) {
    global $USER, $DB, $OUTPUT;
    if (!has_capability('mod/iassign:submitiassign', $this->context, $USER->id) || is_siteadmin())
      $receiver = 1; // student message to teacher
    else
      $receiver = 2; // teacher message to student

    $verify_message = $DB->get_records('iassign_submission_comment', array('iassign_submissionid' => $iassign_submissionid)); //

    if ($verify_message) {
      foreach ($verify_message as $message) {
        if ($message->receiver == $receiver) {
            $newentry = new stdClass();
            $newentry->id = $message->id;
            $newentry->return_status = 1;
            if (!$DB->update_record('iassign_submission_comment', $newentry)) {
                print_error('error_update', 'iassign');
              } // if (!$DB->update_record('iassign_submission_comment', $newentry))
          } // if ($message->receiver == $receiver)
        // Trigger module viewed event.
        $event = \mod_iassign\event\submission_comment_updated::create(array(
          'objectid' => $this->iassign->id,
          'context' => $this->context
          ));
        $event->add_record_snapshot('course', $this->course);
        $event->trigger();
        } // foreach ($verify_message as $message)
      }
    }


  /// Record comment of activity
  function write_comment_submission () {
    global $USER, $CFG, $DB;
    $id = $this->cm->id;
    $submission_comment = optional_param('submission_comment', NULL, PARAM_TEXT);
    $row = optional_param('row', 0, PARAM_INT);
    $column = optional_param('column', 0, PARAM_INT);

    $sum_comment = 0;

    $return = "" . $CFG->wwwroot . "/mod/iassign/view.php?action=viewsubmission&id=" . $id . "&iassign_submission_current=" . $this->iassign_submission_current . "&userid_iassign=" . $this->userid_iassign . "&iassign_current=" . $this->activity->get_activity()->id . "&row=" . ($row) . "&column=" . ($column);

    $link_return = "&nbsp;<a href='" . $return . "'>" . iassign_icons::insert('return_home') . get_string('return', 'iassign') . "</a>";

    $str1 = trim($submission_comment);
    $str2 = trim(get_string('box_comment_message', 'iassign'));

    if (!empty($submission_comment) && (strcmp($str1, $str2) != 0)) { // there is comment and it is different from "previous"
      //D $iassign_submission = $DB->get_record("iassign_submission", array("id" => $this->iassign_submission_current)); //MOOC 2016
      if (has_capability('mod/iassign:submitiassign', $this->context, $USER->id) && !is_siteadmin()) { //MOOC '&& !is_siteadmin()'
        $receiver = 1; // student message to teacher
        $this->action = 'view';
        //D $iassign_statement_activity_item = $DB->get_record("iassign_statement", array("id" => $iassign_submission->iassign_statementid)); //MOOC 2016
        //D $tousers = get_users_by_capability($this->context, 'mod/iassign:evaluateiassign'); //MOOC 2016
        } else {
        $receiver = 2; // teacher message to student
        $this->action = 'viewsubmission';
        //D $tousers = array(); //MOOC 2016
        //MOOC 2016 //T $tousers[] = $DB->get_record("user", array("id" => $iassign_submission->userid)); //TODO Para registrar mensagem na area do Moodle - tem que ativar abaixo
        }

      //MOOC 2016: foi p/ 15 linhas acima: 
      $iassign_submission = $DB->get_record("iassign_submission", array("id" => $this->iassign_submission_current));

      if (!$iassign_submission) {
        $iassign_statement_activity_item = $DB->get_record("iassign_statement", array("id" => $this->activity->get_activity()->id));

        $id_submission = $this->new_submission($iassign_statement_activity_item->id, $this->userid_iassign, $receiver);
        $this->iassign_submission_current = $id_submission;
        } else {
        $id_submission = $iassign_submission->id;
        } // if (!$iassign_submission)
      // $comments = $DB->get_record_sql("SELECT COUNT(iassign_submissionid) FROM {$CFG->prefix}ia_assign_submissions_comment
      // WHERE iassign_submissionid = '$id_submission' and comment='$submission_comment' and comment_authorid='$USER->id'"); //
      // Attention: this Moodle function 'get_record_sql' makes a replace in ':comment'
      $params = array("iassign_submissionid" => $id_submission, "comment" => $submission_comment, "comment_authorid" => $USER->id);
      $comments = $DB->get_record_sql(
            "SELECT COUNT(iassign_submissionid) FROM {iassign_submission_comment} " .
            " WHERE iassign_submissionid = :iassign_submissionid AND comment= :comment AND comment_authorid= :comment_authorid", $params);

      if ($comments)
        foreach ($comments as $tmp)
          $sum_comment = $tmp;

      if ($sum_comment == 0) {
        $newentry = new stdClass();
        $newentry->iassign_submissionid = $id_submission;
        $newentry->comment_authorid = $USER->id;
        $newentry->timecreated = time();
        $newentry->comment = $submission_comment;
        $newentry->receiver = $receiver;
        $ia_assign_submissions_comment_id = $DB->insert_record('iassign_submission_comment', $newentry);
        //T foreach ($tousers as $touser) { //TODO Para registrar mensagem na area do Moodle - tem que ativar '$tousers[] = $DB->get_record(...);' acima
        //T $eventdata = new stdClass();
        //T $eventdata->component         = 'mod_iassign'; //your component name
        //T $eventdata->name              = 'comment'; //this is the message name from messages.php
        //T $eventdata->userfrom          = $USER;
        //T $eventdata->userto            = $touser;
        //T $eventdata->subject           = "Teste de Subject";
        //T $eventdata->fullmessage       = "Teste de Mensagem...";
        //T $eventdata->fullmessageformat = FORMAT_PLAIN;
        //T $eventdata->fullmessagehtml   = "<b>Teste de Mensagem...</b>";
        //T $eventdata->smallmessage      = "Teste de Mensagem";
        //T $eventdata->notification      = 1; //this is only set to 0 for personal messages between users
        //T // alteracao tulio faria
        //T //message_send($eventdata);
        //T } //MOOC 2016 - TODO NAO finalizado, iniciado pelo Tulio
        // Trigger module viewed event.
        $event = \mod_iassign\event\submission_comment_created::create(array(
          'objectid' => $this->iassign->id,
          'context' => $this->context
          ));
        $event->add_record_snapshot('course', $this->course);
        $event->trigger();
        }
      } // if (!empty($submission_comment) && (strcmp($str1, $str2) != 0))
    // if ($this->action=='viewsubmission') {
    // echo $OUTPUT->header();
    // $this->return_last('confirm_add_comment', $link_return);
    // die;
    // }

    return true;
    } // function write_comment_submission()


  /// Writes a new submission
  function new_submission ($iassignid, $id_user, $receiver) {
    global $USER, $DB, $OUTPUT;
    $newentry = new stdClass();
    $newentry->iassign_statementid = $iassignid;
    $newentry->userid = $id_user;
    $newentry->timecreated = time();
    $newentry->timemodified = time();
    $newentry->answer = 0; // student only submit message
    if ($receiver == 2) // teacher message to student (write id teacher)
      $newentry->teacher = $USER->id;

    if (!$newentry->id = $DB->insert_record("iassign_submission", $newentry))
      return_home_course('error_insert_submissions');
    else {
      // Trigger module viewed event.
      $event = \mod_iassign\event\submission_created::create(array(
        'objectid' => $this->iassign->id,
        'context' => $this->context
        ));
      $event->add_record_snapshot('course', $this->course);
      $event->trigger();
      }
    return $newentry->id;
    }

  /// Return to a specific address of page
  function return_last($message, $link_return) {
    global $DB, $OUTPUT;
    print $OUTPUT->box_start();
    print '<table width=100% border=0 valign="top">' . "\n";
    print '<tr><td align="left"><strong>' . "\n";
    print_string($message, 'iassign');
    print '</strong></td>' . "\n";
    print '<td width=20% align="right">' . "\n";
    print $link_return;
    print '</td></tr></table>' . "\n";
    print $OUTPUT->box_end();
    print $OUTPUT->footer();
    die();
    } // function return_last($message, $link_return)

  } // class iassign


/// Class for manage activities
class activity {

  var $activity;

  /// Constructor of class.
  //  @param int $id Id of activity
  //  3.1 update PHP 7.0 compatibility for all moodle versions
  //D public function activity($id) { self::__construct($id); }

  function __construct ($id) {
    global $DB;
    $this->activity = $DB->get_record("iassign_statement", array("id" => $id));
    if (empty($this->activity))
      $this->activity = null;
    }

  /// Get an activity
  //  @return NULL
  function get_activity () {
    if ($this->activity != null)
      return $this->activity;
    else
      return null;
    }

  /// Delete interactive activities
  function delete ($return) {
    global $USER, $CFG, $DB, $OUTPUT;

    $iassign_submission_currents = $DB->get_records("iassign_submission", array("iassign_statementid" => $this->activity->id));

    $output = $OUTPUT->header();
    $output .= $OUTPUT->box_start();
    $output .= "<p>" . get_string('delete_activity', 'iassign') . "&nbsp;<strong>" . $this->activity->name . "</strong></p>";
    if ($iassign_submission_currents) {
      $output .= "<p>" . get_string('number_submissions', 'iassign') . "&nbsp;<strong>" . count($iassign_submission_currents) . "</strong></p>";
      if (!has_capability('mod/iassign:deleteiassignnotnull', $USER->context, $USER->id)) {
        $output .= $OUTPUT->heading(get_string('delete_activity_permission_adm', 'iassign'));
        $output .= $OUTPUT->single_button($return, get_string('return', 'iassign'), 'get');
        $output .= $OUTPUT->box_end();
        $output .= $OUTPUT->footer();
        print $output;
        die();
        } // if (!has_capability('mod/iassign:deleteiassignnotnull', $this->context, $USER->id))
      }   // if ($iassign_submission_currents)
    else
      $output .= "<p>" . get_string('not_submissions_activity', 'iassign') . "</p>";
    $output .= '<table width=50% border=0>';
    $output .= '<tr valign="top"><td>';
    $output .= "<p>" . get_string('what_do', 'iassign') . "</p>";
    $output .= '</td><td>';

    $bottonDelete_yes = get_string('delete_iassign', 'iassign');
    $deleteiassignyes = $CFG->wwwroot . "/mod/iassign/view.php?id=" . $USER->cm . "&action=deleteyes&iassign_current=" . $this->activity->id;
    $output .= "<form name='formDelete' id='formDelete' method='post' action='$deleteiassignyes' enctype='multipart/form-data'>";
    $output .= " <input type=submit value='$bottonDelete_yes'/>\n";
    $output .= "</form>\n";
    $output .= '</td><td>';
    $bottonDelete_no = get_string('delete_cancel', 'iassign');
    $deleteiassignno = $CFG->wwwroot . "/mod/iassign/view.php?id=" . $USER->cm . "&action=deleteno&iassign_current=" . $this->activity->id;
    $output .= "<form name='formDelete' id='formDelete' method='post' action='$deleteiassignno' enctype='multipart/form-data'>\n";
    $output .= " <p><input type=submit value='$bottonDelete_no'/></p>\n";
    $output .= "</form>\n";
    $output .= '</td></tr></table>' . "\n";
    $output .= $OUTPUT->box_end();
    $output .= $OUTPUT->footer();
    print $output;
    } //  function delete($return)


  /// Function for confirm the delete of activity
  //  @param String $return Url of return
  //  @param Object $iassign Object content an activity
  function deleteyes ($return, $iassign) {
    global $USER, $CFG, $DB, $OUTPUT;
    $msg = '';

    if (!empty($this->activity->id)) {
      $iassign_submission_currents = $DB->get_records("iassign_submission", array("iassign_statementid" => $this->activity->id));
      if ($iassign_submission_currents) {
        if (has_capability('mod/iassign:deleteassignnull', $USER->context, $USER->id)) {
            foreach ($iassign_submission_currents as $iassign_submission)
                $DB->delete_records('iassign_submission_comment', array('iassign_submissionid' => $iassign_submission->id));
            $delete_iassign_submission_currents = $DB->delete_records("iassign_submission ", array("iassign_statementid" => $this->activity->id));
          } // if ($iassign_submission_currents)
        }

      //$delete_iassign_statement_config = $DB->delete_records('iassign_statement_config', array('iassign_statementid' => $this->activity->id)); //MOOC 2016

      $this->delete_calendar($this->activity->id);
      $delete_iassign_current = $DB->delete_records('iassign_statement', array('id' => $this->activity->id));
      iassign::update_grade_iassign($this->activity->iassignid);

      if ($delete_iassign_current) {
        $iassign->return_home_course('confirm_delete_iassign');
        //$msg = get_string ( 'confirm_delete_iassign', 'iassign' );
        } else {
        $iassign->return_home_course('error_confirm_delete_iassign');
        //$msg = get_string ( 'error_confirm_delete_iassign', 'iassign' );
        }
      // if (($this->action == 'deleteyes') && (has_capability('mod/iassign:deleteassignnull', $this->context, $USER->id)))
      }
    }

  /// Changes position within of interactive group activity
  function move_iassign($target, $return) {
    global $DB, $OUTPUT;
    $iassign_target = $DB->get_record("iassign_statement", array("id" => $target));
    $aux = $this->activity->position;
    $newentry = new stdClass();
    $newentry->id = $this->activity->id;
    $newentry->position = $iassign_target->position;

    if (!$DB->update_record('iassign_statement', $newentry)) {
      print_error('error_update_move_iassign', 'iassign');
      }

    $newentry->id = $iassign_target->id;
    $newentry->position = $aux;
    if (!$DB->update_record('iassign_statement', $newentry))
      print_error('error_update_move_iassign', 'iassign');
    redirect($return);
    }


  /// Enable or disable the display of interactive activities
  function visible_iassign ($return) {
    global $DB;
    $newentry = new stdClass();
    $newentry->id = $this->activity->id;
    $newentry->visible = $this->activity->visible == 0 ? 1 : 0;
    if (!$DB->update_record('iassign_statement', $newentry))
      print_error('error_update_visible_iassign', 'iassign');
    redirect($return);
    }


  /// Add news interactive activities
  function new_iassign ($param) {
    global $DB;

    $newentry = new stdClass();
    $newentry->iassignid = $param->iassignid;
    $newentry->name = $param->name;
    $newentry->type_iassign = $param->type_iassign;
    $newentry->proposition = $param->proposition;
    $newentry->author_name = $param->author_name;
    $newentry->author_modified_name = $param->author_modified_name;
    $newentry->iassign_ilmid = $param->iassign_ilmid;
    $newentry->file = $param->file;
    $newentry->grade = $param->grade;
    $newentry->timemodified = time();
    $newentry->timecreated = time();
    if ($param->type_iassign == 1) {
      $newentry->timedue = 0;
      $newentry->timeavailable = 0;
      }   // if ($param->type_iassign == 1)
    else {
      $newentry->timedue = $param->timedue;
      $newentry->timeavailable = $param->timeavailable;
      }
    $newentry->preventlate = $param->preventlate;
    $newentry->test = $param->test;
    $newentry->special_param1 = $param->special_param1;
    $newentry->visible = $param->visible;
    $newentry->position = $param->position;
    $newentry->max_experiment = $param->max_experiment;
    $newentry->dependency = $param->dependency;
    $newentry->automatic_evaluate = $param->automatic_evaluate;
    $newentry->show_answer = $param->show_answer;

    if ($id = $DB->insert_record("iassign_statement", $newentry)) {
      $component = 'mod_iassign';
      $filearea = 'exercise';
      $fs = get_file_storage();
      $file = $fs->get_file_by_id($param->file);
      $itemid = $file->get_itemid() + $id;
      $newfile = $fs->create_file_from_storedfile(array('contextid' => $param->context->id, 'component' => $component, 'filearea' => $filearea, 'itemid' => $itemid), $file);
      $updateentry = new stdClass();
      $updateentry->id = $id;
      $updateentry->file = $newfile->get_itemid();
      if (!$DB->update_record("iassign_statement", $updateentry))
        print_error('error_add', 'iassign');

      if ($param->type_iassign == 3)
        iassign::update_grade_iassign($param->iassignid);

      //TODO iLM_HTML5 :: //MOOC2014
      //D  $iassign_ilm_configs = $DB->get_records('iassign_ilm_config', array('iassign_ilmid' => $param->iassign_ilmid, 'visible' => '1'));
      //D  if ($iassign_ilm_configs) {
      //D  foreach ($iassign_ilm_configs as $iassign_ilm_config) {
      //D  if ($iassign_ilm_config->param_type != 'static') {
      //D  $newentry = new stdClass();
      //D  $newentry->iassign_statementid = $id;
      //D  $newentry->iassign_ilm_configid = $iassign_ilm_config->id;
      //D  $newentry->param_name = $iassign_ilm_config->param_name;
      //D  $newentry->param_value =(is_array($param->{'param_'.$iassign_ilm_config->id}) ? implode(",", $param->{'param_'.$iassign_ilm_config->id}) : $param->{'param_'.$iassign_ilm_config->id});
      //D  if (!$DB->insert_record("iassign_statement_config", $newentry))
      //D  print_error('error_add_param', 'iassign');
      //D  }
      //D  }
      //D  }
      // log event --------------------------------------------------------------------------------------
      iassign_log::add_log('add_iassign_exercise', 'name: ' . $param->name, $id, $param->iassign_ilmid);
      // log event --------------------------------------------------------------------------------------

      return $id;
      }
    else
      print_error('error_add', 'iassign');
    } // function new_iassign($param)


  /// Add the calendar entries for this iassign
  //  @param int $coursemoduleid - Required to pass this in because it might not exist in the database yet
  //  @return bool
  static function add_calendar ($iassignid) {
    global $DB, $CFG;
    require_once($CFG->dirroot . '/calendar/lib.php');

    $iassign_statement_activity_item = $DB->get_record("iassign_statement", array("id" => $iassignid));
    $iassign = $DB->get_record("iassign", array("id" => $iassign_statement_activity_item->iassignid));

    $event = new stdClass();
    $event->name = $iassign->name . '&nbsp;-&nbsp;' . $iassign_statement_activity_item->name;
    $event->description = $iassign_statement_activity_item->name;
    $event->courseid = $iassign->course;
    $event->groupid = 0;
    $event->userid = 0;
    $event->modulename = 'iassign';
    $event->instance = $iassign->id;
    $event->eventtype = 'due';
    $event->timestart = $iassign_statement_activity_item->timeavailable;
    $event->timeduration = ($iassign_statement_activity_item->timedue - $iassign_statement_activity_item->timeavailable);
    calendar_event::create($event);
    }


  /// Update the calendar entries for this iassign
  //  @param int $coursemoduleid - Required to pass this in because it might not exist in the database yet
  //  @return bool
  function update_calendar ($iassignid, $olddescription) {
    global $DB, $CFG;
    require_once($CFG->dirroot . '/calendar/lib.php');

    $iassign_statement_activity_item = $DB->get_record("iassign_statement", array("id" => $iassignid));
    $iassign = $DB->get_record("iassign", array("id" => $iassign_statement_activity_item->iassignid));

    $event = new stdClass();
    $event->id = 0;
    $events = $DB->get_records('event', array('modulename' => 'iassign', 'instance' => $iassign->id));
    if ($events) {
      foreach ($events as $value) {
        if ($value->description == $olddescription) {
          $event->id = $value->id;
          }
        }
      }
    if ($event->id != 0) {
      $event->name = $iassign->name . '&nbsp;-&nbsp;' . $iassign_statement_activity_item->name;
      $event->description = $iassign_statement_activity_item->name;
      $event->timestart = $iassign_statement_activity_item->timeavailable;
      $event->timeduration = ($iassign_statement_activity_item->timedue - $iassign_statement_activity_item->timeavailable);

      $calendarevent = calendar_event::load($event->id);
      $calendarevent->update($event);
    } else
      $this->add_calendar($iassignid);
    }


  /// Update the calendar entries for this iassign
  //  @param int $coursemoduleid - Required to pass this in because it might not exist in the database yet
  //  @return bool
  function delete_calendar ($iassignid) {
    global $DB, $CFG;
    require_once($CFG->dirroot . '/calendar/lib.php');

    $iassign_statement_activity_item = $DB->get_record("iassign_statement", array("id" => $iassignid));
    $iassign = $DB->get_record("iassign", array("id" => $iassign_statement_activity_item->iassignid));
    $events = $DB->get_records('event', array('modulename' => 'iassign', 'instance' => $iassign->id));
    if ($events) {
      foreach ($events as $value) {
        if ($value->description == $iassign_statement_activity_item->name) {
            $DB->delete_records('event', array('id' => $value->id));
          }
        }
      }
    }


  /// Update interactive activities
  function update_iassign ($param) {
    global $DB;

    $component = 'mod_iassign';
    $filearea = 'exercise';
    $fs = get_file_storage();
    $file = $fs->get_file_by_id($param->file);
    $fileold = $fs->get_file_by_id($param->fileold);

    if ($param->file != $param->fileold) {

      if ($fileold) {
        $fileoldarea = $fs->get_area_files($fileold->get_contextid(), $fileold->get_component(), $fileold->get_filearea(), $fileold->get_itemid());
        foreach ($fileoldarea as $value) {
          $value->delete();
          }
        }

      if (!$fs->file_exists($param->context->id, $component, $filearea, $file->get_itemid(), $file->get_filepath(), $file->get_filename())) {
        $itemid = $file->get_itemid() + $param->iassign_id;
        $newfile = $fs->create_file_from_storedfile(array('contextid' => $param->context->id, 'component' => $component, 'filearea' => $filearea, 'itemid' => $itemid), $file);
        $param->file = $newfile->get_itemid();
      } else
        $param->file = $file->get_itemid();

    } else {
      $param->file = $file->get_itemid();
    }

    $newentry = new stdClass();
    $newentry->id = $param->iassign_id;
    $newentry->name = $param->name;
    $newentry->type_iassign = $param->type_iassign;
    $newentry->proposition = $param->proposition;
    $newentry->iassign_ilmid = $param->iassign_ilmid;

    $newentry->file = $param->file;
    $newentry->grade = $param->grade;
    $newentry->author_modified_name = $param->author_modified_name;

    $newentry->timemodified = time();
    if ($param->type_iassign == 1) {
      $newentry->timedue = 0;
      $newentry->timeavailable = 0;
      } // if ($param->type_iassign == 1)
    else {
      $newentry->timedue = $param->timedue;
      $newentry->timeavailable = $param->timeavailable;
      }
    $newentry->preventlate = $param->preventlate;
    $newentry->test = $param->test;
    $newentry->special_param1 = $param->special_param1;
    $newentry->visible = $param->visible;
    $newentry->max_experiment = $param->max_experiment;
    $newentry->dependency = $param->dependency;
    $newentry->automatic_evaluate = $param->automatic_evaluate;
    $newentry->show_answer = $param->show_answer;

    if (!$DB->update_record("iassign_statement", $newentry))
      print_error('error_update', 'iassign');

    if ($param->type_iassign == 3) {
      iassign::update_grade_iassign($param->iassignid);
      }

    //$id = $newentry->id; // MOOC 2016 --- inicio
    //$iassign_activity_item_configs = $DB->get_records('iassign_statement_config', array('iassign_statementid' => $newentry->id));
    //if ($iassign_activity_item_configs) {
    //foreach ($iassign_activity_item_configs as $iassign_activity_item_config) {
    //  $newentry = new stdClass();
    //   $newentry->id = $iassign_activity_item_config->id;
    //   $newentry->param_value =(is_array($param->{'param_'.$iassign_activity_item_config->iassign_ilm_configid}) ? implode(",",
    //   $newentry->param_value =(is_array($param->{'param_' . $iassign_activity_item_config->iassign_ilm_configid}) ? implode(",", 
    //             $param->{'param_' . $iassign_activity_item_config->iassign_ilm_configid}) : $param->{'param_' . $iassign_activity_item_config->iassign_ilm_configid});
    //   if (!$DB->update_record("iassign_statement_config", $newentry))
    //     print_error('error_edit_param', 'iassign');
    //  }
    // } // MOOC 2016 --- final
    // log event --------------------------------------------------------------------------------------
    iassign_log::add_log('update_iassign_exercise', 'name: ' . $param->name, $param->iassign_id, $param->iassign_ilmid);
    // log event --------------------------------------------------------------------------------------

    return $newentry->id;
    // if ($param->type_iassign==3)
    // $this->update_grade_iassign($param->iassignid);
    } // function update_iassign($param)


  /// Show information of activity
  function show_info_iassign () {
    global $DB, $OUTPUT;

    $output = '<p><strong>' . get_string('proposition', 'iassign') . ':</strong>&nbsp;' . $this->activity->proposition . '</p>' . "\n";
    if ($this->activity->type_iassign == 3) {
      if ($this->activity->dependency == 0) {
        $output .= '<p><strong>' . get_string('independent_activity', 'iassign') . '</strong></p>' . "\n";
        } else {
        $dependencys = explode(';', $this->activity->dependency);
        $output .= '<p><strong>' . get_string('dependency', 'iassign') . '</strong></p>';
        foreach ($dependencys as $dependency) {
            $dependencyiassign = $DB->get_record("iassign_statement", array("id" => $dependency));
            if ($dependencyiassign)
                $output .= '<p>' . $dependencyiassign->name . '</p>' . "\n";
          } // foreach ($dependencys as $dependency)
        } // if ($iassign_statement_activity_item->dependency == 0)
      if ($this->activity->max_experiment == 0)
        $output .= '<p><strong>' . get_string('experiment', 'iassign') . '</strong>&nbsp;' . get_string('ilimit', 'iassign');
      else
        $output .= '<p><strong>' . get_string('experiment_iassign', 'iassign') . '</strong>&nbsp;' . $this->activity->max_experiment . "\n";
      $output .= '&nbsp;&nbsp;&nbsp;<strong>' . get_string('grade_iassign', 'iassign') . '</strong>&nbsp;' . $this->activity->grade . '</p>' . "\n";
      } // if ($iassign_statement_activity_item->type_iassign == 3)

    print $OUTPUT->box($output);
    } // function show_info_iassign()


  /// Shows date of opening and closing activities
  function view_dates () {
    global $USER, $CFG, $DB, $OUTPUT;

    $return = $CFG->wwwroot . "/mod/iassign/view.php?id=" . $USER->cm;
    $link_return = "&nbsp;<a href='" . $return . "'>" . iassign_icons::insert('home') . get_string('activities_page', 'iassign') . "</a>";
    $status_iassign = "";
    $status_iassign1 = "";
    $status_iassign2 = "";
    if ($this->activity->type_iassign == 1) // activity of type example
      $type_iassign = get_string('example_iassign', 'iassign');
    elseif ($this->activity->type_iassign == 2) { // activity of type test
      $type_iassign = get_string('test_iassign', 'iassign');
      if ($this->activity->timeavailable > time()) {
        $status_iassign = get_string('previous_timeavailable', 'iassign');
        } elseif ($this->activity->timedue < time()) {
        $status_iassign = get_string('last_timedue', 'iassign');
        }
    } elseif ($this->activity->type_iassign == 3) { // activity of type exercise
      $type_iassign = get_string('exercise_iassign', 'iassign');
      if ($this->activity->timeavailable > time()) {
        $status_iassign = get_string('previous_timeavailable', 'iassign'); // before of deadline
      } elseif ($this->activity->timedue < time()) { // after delivery
        $status_iassign = get_string('last_timedue', 'iassign');
        if ($this->activity->preventlate == 1) // permitted to submit after the deadline
          $status_iassign1 = get_string('duedate_preventlate_enable', 'iassign');
        elseif ($this->activity->preventlate == 0) { // not permitted to submit after the deadline
          $status_iassign1 = get_string('duedate_preventlate_desable', 'iassign');
          if ($this->activity->test == 1) // allowed to test after of deadline
            $status_iassign2 = get_string('test_preventlate', 'iassign');
          elseif ($this->activity->test == 0) { // not allowed to test after of deadline
            $status_iassign2 = get_string('test_preventlate_no', 'iassign');
            } // elseif ($iassign_statement_activity_item->test == 0)
          } // elseif ($iassign_statement_activity_item->preventlate == 0)
        } // elseif ($iassign_statement_activity_item->timedue < time())
      } // elseif ($iassign_statement_activity_item->type_iassign == 3)

    $output = '<table  width=100% >' . "\n";
    $output .= '<tr><td colspan=2><h4>' . $this->activity->name . '</h4></td></tr>' . "\n";
    $output .= '<tr>' . "\n";

    // TODO duvida: como permitir ao admin,professor,monitor ver a atividade mesmo apos prazo???
    // Leo testes para passar por cima com 'has_capability('mod/iassign:...', $this->context, $USER->id)
    $output .= '<td width=60%>' . $type_iassign . '</td>' . "\n";
    // leo $output .= '<td width=80%>' . $type_iassign;
    // $output .= $auxStr . " - status_assign=$status_iassign - this->activity->type_iassign=" . $this->activity->type_iassign. "<br/>"; // Period ended.
    // $output .= '</td>' . "\n";

    if (has_capability('mod/iassign:viewiassignall', $USER->context, $USER->id) && ($this->activity->type_iassign == 3)) {
      // Link (with icon) to report survey of this batch of these insteractivy exercises
      $link_report = "<a href='" . $CFG->wwwroot . "/mod/iassign/view.php?id=" . $USER->cm . "&action=report&iassignid=" . $this->activity->iassign_ilmid . "'>" . iassign_icons::insert('view_report') . '&nbsp;' . get_string('report', 'iassign') . "</a>";
      $output .= '<td width=40% align="right">' . '&nbsp;' . $link_report . '</td>' . "\n";
      } else {

      $link_next = "";
      $link_previous = "";

      $iassign_previous = $DB->get_record('iassign_statement', array('iassignid' => $this->activity->iassignid, 'position' => $this->activity->position - 1));
      $iassign_next = $DB->get_record('iassign_statement', array('iassignid' => $this->activity->iassignid, 'position' => $this->activity->position + 1));

      // previous_activity
      if ($iassign_previous) {
        $url_previous = "view.php?id=$USER->cm&userid_iassign=$USER->id&action=view&iassign_current=$iassign_previous->id";
        $link_previous = "<a href='" . $url_previous . "'>" . (iassign_icons::insert('previous_student_activity')) . "</a>";
        } // next_activity
      if ($iassign_next) {
        $url_next = "view.php?id=$USER->cm&userid_iassign=$USER->id&action=view&iassign_current=$iassign_next->id";
        $link_next = "<a href='" . $url_next . "'>" . (iassign_icons::insert('next_student_activity')) . "</a>";
        }

      $output .= '<td width=40% align="right">' . $link_previous . '&nbsp;&nbsp;&nbsp;' . $link_return . '&nbsp;&nbsp;&nbsp;' . $link_next . '</td>' . "\n";
      } // if (has_capability('mod/iassign:viewiassignall', $this->context, $USER->id) && ($iassign_statement_activity_item->type_iassign == 3))
    $output .= '</tr></table>' . "\n";
    $output .= '<table  width=100% >' . "\n";
    if ($this->activity->type_iassign > 1) {
      if ($this->activity->timeavailable)
        $output .= '<tr><td width=50% align="left"> <strong>' . get_string('availabledate', 'iassign') . ':</strong>&nbsp;' . userdate($this->activity->timeavailable) . '</td>' . "\n";
      if ($this->activity->timedue)
        $output .= '<td width=50% align="left"><strong>' . get_string('duedate', 'iassign') . ':</strong>&nbsp;' . userdate($this->activity->timedue) . '</td>' . "\n";
      } // if ($iassign_statement_activity_item->type_iassign > 1)
    if ($status_iassign != "" && $status_iassign1 != "" && $status_iassign2 != "")
      $output .= '<tr><td><font color="red">' . $status_iassign . '&nbsp;' . $status_iassign1 . '&nbsp;' . $status_iassign2 . '</font></td></tr>' . "\n";

    $output .= '</table>' . "\n";

    print $OUTPUT->box($output);
    } // function view_dates()

  } // class activity


/// Class to manage Interactive Learning Module (iLM)
class ilm {

  var $ilm;

  /// Constructor of class
  //  @param int $id Id of iLM
  //D  3.1 update PHP 7.0 compatibility for all moodle versions
  //D public function ilm($id) { self::__construct($iassign, $cm, $course); }
  function __construct ($id) {
    global $DB;
    $this->ilm = $DB->get_record("iassign_ilm", array("id" => $id));
    if (empty($this->ilm))
      $this->ilm = null;
    }


  /// Shows activity in iLM
  //  @calledby view_iassign_current()
  function view_iLM ($iassign_statement_activity_item, $student_answer, $enderecoPOST, $view_teacherfileversion) {
    global $USER, $CFG, $DB;

    $iassign_ilm = $DB->get_record('iassign_ilm', array('id' => $iassign_statement_activity_item->iassign_ilmid));

    // Faz a leitura do tipo de iLM e realiza a chamada à classe adequada
    $typec = strtolower($iassign_ilm->type);
    require_once 'ilm_handlers/' . $typec . '.php';
    $retorno = $typec::show_activity_in_ilm($iassign_statement_activity_item, $student_answer, $enderecoPOST, $view_teacherfileversion);
    return $retorno;
    } // function view_iLM($iassign_statement_activity_item, $student_answer, $enderecoPOST, $view_teacherfileversion)


  /// Function to give a single access to an iLM content avoi (after used, 'view()', after 'view_iLM(...)', will erase the entry)
  //  @calledby view_iLM($iassign_statement_activity_item, $student_answer, $enderecoPOST, $view_teacherfileversion) : $id_iLM_security=$this->write_iLM_security($iassign_statement_activity_item->id,$content_or_id_from_ilm_security);
  //  @param int $iassign_statement_activity_itemid Id of iassign statement
  //  @param Object $file File in use in activity
  //  @return int Return the id of log
  function write_iLM_security ($iassign_activity_itemid, $content_or_id_from_ilm_security) {
    global $CFG, $USER, $COURSE, $DB, $OUTPUT;
    $newentry = new stdClass();
    $newentry->iassign_statementid = $iassign_activity_itemid;
    $newentry->userid = $USER->id;
    $newentry->file = $content_or_id_from_ilm_security;
    $newentry->timecreated = time();
    $newentry->view = 1;
    $id_iLM_security = $DB->insert_record("iassign_security", $newentry);
    if (!$id_iLM_security) {
      print_error('error_security', 'iassign'); // ./lib/setuplib.php: moodle_exception thrown
      } // from (!$DB->insert_record("iassign_security", $newentry))

    return $id_iLM_security;
    }

  /// Function to avoid that erros in remotion of entries in table 'iassign_security' allow future access to this contents
  //  @calledby view_iLM($iassign_statement_activity_item, $student_answer, $enderecoPOST, $view_teacherfileversion) : $this->remove_old_iLM_security_entries($USER->id, $iassign_statement_activity_item->id);
  //  @param int $userid
  //  @param int $iassign_activity_itemid Id of iassign statement
  function remove_old_iLM_security_entries ($userid, $iassign_activity_itemid) {
    global $DB;
    // This is an additional security: erase eventually old entries in 'iassign_security' table (do not remove '$iassign_activity_itemid' since it is going to be used "now")
    $result = $DB->delete_records_select("iassign_security", "userid=" . $userid . " AND iassign_statementid<>" . $iassign_activity_itemid, null);
    }

  } // class ilm


/// Class to manage settings of iLM.
class ilm_settings {

  /// Function to prepare tag to load iLM (that is stored in Moodle filesystem - usually /var/moodledata/filedir/).
  //  In case of JAR it will prepare the tag "applet". In case of HTML5 will prepare an "iframe".
  //  @param int $ilm_id Id of iLM
  //  @param array $options An array with options for create dynamic tag html APPLET
  //  @return string Return with a tag html APPLET created
  static function build_ilm_tags ($ilm_id, $options = array()) {
    global $DB;

    $iassign_ilm = $DB->get_record('iassign_ilm', array('id' => $ilm_id));

    // Faz a leitura do tipo de iLM e realiza a chamada à classe adequada
    $typec = strtolower($iassign_ilm->type);
    require_once 'ilm_handlers/' . $typec . '.php';
    $retorno = $typec::build_ilm_tags($ilm_id, $options);
    return $retorno;
    } // static function build_ilm_tags($ilm_id, $options = array())


  /// Function for get modified date of iLM file
  //  @param string $file_jar String with Ids of iLM files
  //  @return string Return with the filenames and modified date
  static function applet_filetime ($file_jar) {
    $filetime = "";
    $fs = get_file_storage();
    $files_jar = explode(",", $file_jar);
    foreach ($files_jar as $one_file) {
      $file = $fs->get_file_by_id($one_file);
      if ($file)
        $filetime .= "\n" . $file->get_filename() . ' (' . userdate($file->get_timemodified()) . ')' . '</br>';
      }
    return $filetime;
    }


  /// Function for verify an default applet
  //  @param String $file_jar String containing an list de ids of applet files
  //  @return boolean Return true or fale if applet is default
  static function applet_default ($file_jar) {
    $is_default = true;
    $fs = get_file_storage();
    $files_jar = explode(",", $file_jar);
    foreach ($files_jar as $one_file) {
      $file = $fs->get_file_by_id($one_file);
      if ($file)
        $is_default &= ($file->get_itemid() == 0);
      }
    return $is_default;
    }


  /// Function for get form variables for add, edit, or copy iLM
  //  @calledby settings_ilm.php
  //  @see      settings_form.php
  //  @param int $ilm_id Id of iLM
  //  @param string $action String with the action
  //  @return object Return an object with forms variables
  static function add_edit_copy_ilm ($ilm_id, $action) {
    global $USER, $DB, $CFG;

    require_once('settings_form.php');
    $iassign_ilm = $DB->get_record('iassign_ilm', array('id' => $ilm_id));
    $param = new stdClass();
    $param->action = $action;
    $param->ilm_id = $ilm_id;
    $CFG->action_ilm = $action;
    $CFG->ilm_id = $ilm_id;

    if ($action == 'add') {
      $param->title = get_string('add_ilm', 'iassign');
      $param->name = "";
      $param->version = "";
      $param->url = "";
      $param->description = "";
      $param->extension = "";
      $param->author = $USER->id;
      $param->file_jar = "";
      $param->file_jar_static = "";
      $param->file_class = "";
      $param->width = 800;
      $param->height = 600;
      $param->enable = 0; // 0 - hide / 1 - show
      $param->timecreated = time();
      $param->timemodified = time();
      $param->evaluate = 0;
      $param->parent = 0;
     }
    elseif ($action == 'edit') {
      if ($iassign_ilm) {
        $description = json_decode($iassign_ilm->description);
        $param->title = get_string('edit_ilm', 'iassign');
        $param->id = $iassign_ilm->id;
        $param->name_ilm = $iassign_ilm->name;
        $param->name = $iassign_ilm->name;
        $param->version = $iassign_ilm->version;
        $param->ilm_type = $iassign_ilm->type;
        $param->url = $iassign_ilm->url;
        $param->description = $description->{current_language()};
        $param->description_lang = $iassign_ilm->description;
        $param->extension = $iassign_ilm->extension;
        $param->author = $iassign_ilm->author;
        $param->file_jar = $iassign_ilm->file_jar;
        $param->file_jar_static = ilm_settings::applet_filetime($iassign_ilm->file_jar);
        $param->file_class = $iassign_ilm->file_class;
        $param->width = $iassign_ilm->width;
        $param->height = $iassign_ilm->height;
        $param->enable = $iassign_ilm->enable;
        $param->timecreated = $iassign_ilm->timecreated;
        $param->timemodified = time();
        $param->evaluate = $iassign_ilm->evaluate;
        $param->parent = $iassign_ilm->parent;
        }
      }
    elseif ($action == 'new_version') {
      $description = json_decode($iassign_ilm->description);
      if ($iassign_ilm) {
        if ($iassign_ilm->parent == 0)
          $iassign_ilm->parent = $ilm_id;
        $param->title = get_string('new_version_ilm', 'iassign');
        $param->name_ilm = $iassign_ilm->name;
        $param->name = $iassign_ilm->name;
        $param->version = "";
        $param->ilm_type = $iassign_ilm->type;
        $param->url = $iassign_ilm->url;
        $param->description = $description->{current_language()};
        $param->description_lang = $iassign_ilm->description;
        $param->extension = $iassign_ilm->extension;
        $param->author = $USER->id;
        $param->file_jar = '';
        $param->file_jar_static = '';
        $param->file_class = $iassign_ilm->file_class;
        $param->width = $iassign_ilm->width;
        $param->height = $iassign_ilm->height;
        $param->enable = 0;
        $param->timecreated = time();
        $param->timemodified = time();
        $param->evaluate = $iassign_ilm->evaluate;
        $param->parent = $iassign_ilm->parent;
        }
      } elseif ($action == 'copy') {
      $description = json_decode($iassign_ilm->description);
      if ($iassign_ilm) {
        if ($iassign_ilm->parent == 0)
          $iassign_ilm->parent = $ilm_id;
        $param->title = get_string('copy_ilm', 'iassign');
        $param->id = $iassign_ilm->id;
        $param->name_ilm = $iassign_ilm->name;
        $param->name = $iassign_ilm->name;
        $param->version = "";
        $param->ilm_type = $iassign_ilm->type;
        $param->url = $iassign_ilm->url;
        $param->description = $description->{current_language()};
        $param->description_lang = $iassign_ilm->description;
        $param->extension = $iassign_ilm->extension;
        $param->author = $USER->id;
        $param->file_jar = '';
        $param->file_jar_static = '';
        $param->file_class = $iassign_ilm->file_class;
        $param->width = $iassign_ilm->width;
        $param->height = $iassign_ilm->height;
        $param->enable = 0;
        $param->timecreated = time();
        $param->timemodified = time();
        $param->evaluate = $iassign_ilm->evaluate;
        $param->parent = $iassign_ilm->parent;
        }
      }
    return $param;
    } // static function add_edit_copy_ilm($ilm_id, $action)


  /// Function for save iLM file in moodledata
  //  @param int $itemid Itemid of file save in draft (upload file)
  //  @param int $ilm_id Id of iLM
  //  @return string Return an string with ids of iLM files
  static function new_file_ilm ($itemid, $iassign_ilm) {
    global $CFG, $USER, $DB;

    $return = null;
    $file_jar = array();
    $fs = get_file_storage();
    $contextuser = context_user::instance($USER->id);
    $contextsystem = context_system::instance();
    $files_ilm = $fs->get_area_files($contextuser->id, 'user', 'draft', $itemid);

    if ($files_ilm) {

      foreach ($files_ilm as $value) {
        // VERIFICA SE e' DO TIPO HTML5
        if ($iassign_ilm->type == 1) {
          // Verifica a extensão do arquivo iLM para verificar se é ZIP
          $ext = pathinfo($value->get_filename(), PATHINFO_EXTENSION);
          // CASO SEJA ZIP: COPIA O ARQUIVO, PARA O ilm_debug E DESCOMPACTA no diretorio 'ilm'
          if ((strtolower($ext) == 'zip')) {
            // COPIA:
            $destination = 'ilm_debug/' . $value->get_filename();
            $value->copy_content_to($destination);

            // EXTRAIR CONTEUDO:
            $zip = new ZipArchive();
            $extracted = './ilm';
            $dir = "";
            if ($zip->open($destination) === TRUE) {
              $dir = './ilm/' . trim($zip->getNameIndex(0));
              if (is_dir($dir)) {
                $i = 1;
                $previous = str_replace("/", "", $zip->getNameIndex(0));
                while (file_exists('./ilm/' . $previous . "_" . $i)) {
                  $i ++;
                  }
                $name = $previous . "_" . $i;
                $dir = './ilm/' . $name . "/";
                $j = 0;
                while ($item_name = $zip->getNameIndex($j)) {
                  $zip->renameIndex($j, str_replace($previous, $name, $item_name));
                  $j++;
                  }
                $zip->close();
                }

              $zip->open($destination);
              $zip->extractTo($extracted);
              $zip->close();

              // Apos extrair, remover do DEBUG:
              unlink($destination);
            } else {
              // APÓS NAO CONSEGUIR EXTRAIR, apagar O ZIP:
              unlink($destination);
              print_error('error_add_ilm_zip', 'iassign');
              }

            return $dir;
            }
          }

        if ($value->get_filename() != '.') {
          $file_ilm = array(
            'userid' => $USER->id,
            'contextid' => $contextsystem->id,
            'component' => 'mod_iassign',
            'filearea' => 'ilm',
            'itemid' => rand(1, 999999999),
            'filepath' => '/iassign/ilm/' . iassign_utils::format_pathname($iassign_ilm->name) . '/' . iassign_utils::format_pathname($iassign_ilm->version) . '/',
            'filename' => $value->get_filename());
          $file_ilm = $fs->create_file_from_storedfile($file_ilm, $value);
          array_push($file_jar, $file_ilm->get_id());
          }
        }

      if (!empty($file_jar)) {
        $return = implode(",", $file_jar);
        $old_file_jar = explode(",", $iassign_ilm->file_jar);
        foreach ($old_file_jar as $value) {
          $file = $fs->get_file_by_id($value);
          if ($file)
            $fs->delete_area_files($contextsystem->id, 'mod_iassign', 'ilm', $file->get_itemid());
          }
        }
      } else
      $return = $iassign_ilm->file_jar;

    $delete_file = $fs->delete_area_files($contextuser->id, 'user', 'draft', $itemid);

    return $return;
    }

  /// Function for save in database an new iLM
  //  @param object $param An object with iLM params
  static function new_ilm ($itemid) {
    global $CFG, $USER, $OUTPUT;

    // Verifica se existe algum XML anterior na pasta temp e o exclui
    if (file_exists($CFG->dataroot . '/temp/' . 'ilm-application.xml')) {
      unlink($CFG->dataroot . '/temp/' . 'ilm-application.xml');
      }

    $pathtemp = $CFG->dataroot . '/temp/';

    $contextuser = context_user::instance($USER->id);

    $fs = get_file_storage();
    $zip = new zip_packer();
    $files = $fs->get_directory_files($contextuser->id, 'user', 'draft', $itemid, '/');
    foreach ($files as $file) {
      if (!$file->is_directory())
        $files_extract = $zip->extract_to_pathname($file, $pathtemp);
      }

    $application_xml = @simplexml_load_file($CFG->dataroot . '/temp/' . 'ilm-application.xml', null, LIBXML_NOCDATA);

    // Verifica se o pacote possui o XML
    if (!$application_xml) {
      print($OUTPUT->notification(get_string('error_xml_ilm', 'iassign'), 'notifyproblem'));
      return;
      } else {
      $missing = "";
      if (!isset($application_xml->name)) {
        $missing .= "name";
        }
      if (!isset($application_xml->version)) {
        $missing .= ", version";
        }
      if (!isset($application_xml->type)) {
        $missing .= ", type";
        }
      if (!isset($application_xml->extension)) {
        $missing .= ", extension";
        }
      if (!isset($application_xml->file_jar)) {
        $missing .= ", file_jar";
        }
      if (!isset($application_xml->file_class)) {
        $missing .= ", file_class";
        }
      if (!isset($application_xml->width)) {
        $missing .= ", width";
        }
      if (!isset($application_xml->height)) {
        $missing .= ", height";
        }
      if (!isset($application_xml->evaluate)) {
        $missing .= ", evaluate";
        }
      if (strlen($missing) > 2) {
        print($OUTPUT->notification(get_string('error_xml_missing', 'iassign') . $missing . ".", 'notifyproblem'));
        return;
        }
      }

    // Faz a leitura do tipo de iLM e realiza a chamada à classe adequada
    $typec = strtolower($application_xml->type);
    require_once 'ilm_handlers/' . $typec . '.php';
    $retorno = $typec::new_ilm($itemid, $files_extract, $application_xml, $contextuser, $fs);
    return $retorno;
    }


  /// Function for save in database an iLM edit
  //  @param object $param An object with iLM params
  static function edit_ilm ($param, $itemid) {
    // Descobrir o tipo de iLM:
    global $DB, $USER, $CFG;

    $iassign_t = $DB->get_record('iassign_ilm', array('id' => $param->id));

    $pathtemp = $CFG->dataroot . '/temp/';

    $contextuser = context_user::instance($USER->id);

    $fs = get_file_storage();
    $zip = new zip_packer();
    $files = $fs->get_directory_files($contextuser->id, 'user', 'draft', $itemid, '/');
    $files_extract = null;
    foreach ($files as $file) {
      if (!$file->is_directory())
        $files_extract = $zip->extract_to_pathname($file, $pathtemp);
      }

    // Faz a leitura do tipo de iLM e realiza a chamada à classe adequada
    $typec = strtolower($iassign_t->type);
    require_once 'ilm_handlers/' . $typec . '.php';
    $typec::edit_ilm($param, $itemid, $files_extract, $contextuser);
    }


  /// Function for save in database an iLM copy
  //  @param object $param An object with iLM params
  static function copy_new_version_ilm ($param) {
    global $DB, $CFG, $USER;
    $itemid = $param->file;

    $pathtemp = $CFG->dataroot . '/temp/';

    $contextuser = context_user::instance($USER->id);

    $fs = get_file_storage();
    $zip = new zip_packer();
    $files = $fs->get_directory_files($contextuser->id, 'user', 'draft', $itemid, '/');
    $files_extract = null;
    foreach ($files as $file) {
      if (!$file->is_directory())
        $files_extract = $zip->extract_to_pathname($file, $pathtemp);
      }

    // Faz a leitura do tipo de iLM e realiza a chamada à classe adequada
    $iassign_t = $DB->get_record('iassign_ilm', array('id' => $param->parent));
    $typec = strtolower($iassign_t->type);
    require_once 'ilm_handlers/' . $typec . '.php';
    $typec::copy_new_version_ilm($param, $files_extract);
    }


  //VER:: 
  //Notice: Undefined variable: output_ilm in C:\Users\Igor\OneDrive\htdocs\moodleteste\moodle\mod\iassign\locallib.php on line 5054
  // Notice: Trying to get property of non-object in C:\Users\Igor\OneDrive\htdocs\moodleteste\moodle\mod\iassign\locallib.php on line 192
  //Notice: Trying to get property of non-object in C:\Users\Igor\OneDrive\htdocs\moodleteste\moodle\mod\iassign\locallib.php on line 194
  /// Function for change visibility of iLM
  //  @param int $ilm_id Id of iLM
  //  @param int $status Indicator of change vibility (0 = hide, 1 = show)
  static function visible_ilm ($ilm_id, $status) {
    global $DB;
    if ($status == 0)
      $visible = 1;
    else
      $visible = 0;
    $newentry = new stdClass();
    $newentry->id = $ilm_id;
    $newentry->enable = $visible;

    if (!$DB->update_record("iassign_ilm", $newentry))
      error(get_string('error_edit_ilm', 'iassign'));
    }


  /// Function for confirm change default iLM
  //  @param int $ilm_id Id of iLM
  //  @param int $ilm_parent Id of parent iLM
  //  @return string Return with an string for create default page confirmation
  static function confirm_default_ilm ($ilm_id, $ilm_parent) {
    global $OUTPUT, $DB;

    $iassign_ilm = $DB->get_record('iassign_ilm', array('id' => $ilm_id));

    $optionsno = new moodle_url('/admin/settings.php', array('section' => 'modsettingiassign', 'action' => 'config', 'ilm_id' => $ilm_parent));
    $optionsyes = new moodle_url('/mod/iassign/settings_ilm.php', array('action' => 'default', 'ilm_id' => $ilm_id, 'ilm_parent' => $ilm_parent));

    $return = $OUTPUT->heading(get_string('confirm_default', 'iassign') . ': ' . $iassign_ilm->name);
    $return .= $OUTPUT->confirm(get_string('confirm_default_ilm', 'iassign') . $OUTPUT->help_icon('confirm_default_ilm', 'iassign'), $optionsyes, $optionsno);
    return $return;
    }


  /// Function for change default iLM
  //  @param int $ilm_id Id of iLM
  //  @return int Return Id of default iLM
  static function default_ilm ($ilm_id) {
    global $DB;

    $iassign_ilm_default = $DB->get_record("iassign_ilm", array('id' => $ilm_id));

    $iassign_ilm = $DB->get_record("iassign_ilm", array('id' => $iassign_ilm_default->parent));

    $DB->delete_records("iassign_ilm", array('id' => $iassign_ilm_default->id));

    $iassign_ilm_default->id = $iassign_ilm->id;
    $iassign_ilm_default->parent = 0;
    $iassign_ilm->parent = $iassign_ilm_default->id;
    $iassign_ilm->id = 0;
    $iassign_ilm_default->enable = 1;

    if (!$DB->update_record("iassign_ilm", $iassign_ilm_default)) {
      print_error('error_edit_ilm', 'iassign');
      }

    if (!$DB->insert_record("iassign_ilm", $iassign_ilm)) {
      $msg_error = get_string('error_add_ilm', 'iassign') . "<br/>In default_ilm(" . $ilm_id . ")<br/>\n";
      print_error($msg_error);
      //xx print_error('error_add_ilm', 'iassign');
      }

    return $iassign_ilm_default->id;
    }


  /// Function for confirm delete iLM
  //  @param int $ilm_id Id of iLM
  //  @param int $ilm_parent Id of parent iLM
  //  @return string Return with an string for create delete page confirmation
  static function confirm_delete_ilm ($ilm_id, $ilm_parent) {
    global $OUTPUT, $DB;

    $iassign_ilm = $DB->get_record('iassign_ilm', array('id' => $ilm_id));

    $optionsno = new moodle_url('/admin/settings.php', array('section' => 'modsettingiassign', 'action' => 'config', 'ilm_id' => $ilm_parent));
    $optionsyes = new moodle_url('/mod/iassign/settings_ilm.php', array('action' => 'delete', 'ilm_id' => $ilm_id, 'ilm_parent' => $ilm_parent));

    return $OUTPUT->confirm(get_string('confirm_delete_ilm', 'iassign', $iassign_ilm->name . ' ' . $iassign_ilm->version), $optionsyes, $optionsno);
    }


  /// Function for delete directory where the iLM is allocated.
  //  @param string $dirPath
  //  @throws InvalidArgumentException
  public static function delete_dir ($dirPath) {
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
      $dirPath .= '/';
      }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
      if (is_dir($file)) {
        ilm_settings::delete_dir($file);
        } else {
        unlink($file);
        }
      }
    rmdir($dirPath);
    }

  /// Function for delete iLM
  //  @param int $ilm_id Id of iLM
  //  @return int Return Id of parent iLM
  static function delete_ilm ($ilm_id) {
    global $DB;

    $iassign_ilm = $DB->get_record('iassign_ilm', array('id' => $ilm_id));

    // Faz a leitura do tipo de iLM e realiza a chamada à classe adequada
    $typec = strtolower($iassign_ilm->type);
    require_once 'ilm_handlers/' . $typec . '.php';
    $retorno = $typec::delete_ilm($ilm_id);
    return $retorno;
    }


  /// Function to export iLM package (ZIP file), eventually to install in other Moodle
  //  @param int $ilm_id Id of iLM
  static function export_ilm ($ilm_id) {

    global $DB;

    $iassign_ilm = $DB->get_record('iassign_ilm', array('id' => $ilm_id));

    // Faz a leitura do tipo de iLM e realiza a chamada à classe adequada
    $typec = strtolower($iassign_ilm->type);
    require_once 'ilm_handlers/' . $typec . '.php';
    $typec::export_ilm($ilm_id);
    } // static function export_ilm($ilm_id)


  //TODO iLM_HTML5 :: //MOOC 2016
  //  Function to export iLM package descriptor for allow online update. //TODO a ser usado onde? como?
  //  @param int $ilm_id Id of iLM
  static function export_update_ilm ($ilm_id) {
    global $DB, $CFG;

    $iassign_ilm = $DB->get_record('iassign_ilm', array('id' => $ilm_id));

    $xml_filename = $CFG->dataroot . '/temp/ilm-upgrade_' . iassign_utils::format_pathname($iassign_ilm->name) . '.xml';
    $zip_filename = 'ilm-' . iassign_utils::format_pathname($iassign_ilm->name . '-v' . $iassign_ilm->version) . '.ipz';

    $upgrade_descriptor = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
    $upgrade_descriptor .= '<upgrade xmlns="http://line.ime.usp.br/application/1.5">' . "\n";
    $upgrade_descriptor .= '   <version>' . $iassign_ilm->version . '</version>' . "\n";
    $upgrade_descriptor .= '   <file>' . $zip_filename . '</file>' . "\n";
    $upgrade_descriptor .= '   <description>' . iassign_language::json_to_xml($iassign_ilm->description) . "\n  " . '</description>' . "\n";
    $upgrade_descriptor .= '</upgrade>' . "\n";

    file_put_contents($xml_filename, $upgrade_descriptor);

    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private", false);
    header('Content-Type: application/xml; charset=utf-8');
    header("Content-Disposition: attachment; filename=\"" . basename($xml_filename) . "\";");
    header("Content-Length: " . @filesize($xml_filename));
    set_time_limit(0);
    @readfile("$xml_filename") || die("File not found.");
    unlink($xml_filename);
    exit;
    } // static function export_update_ilm($ilm_id) //MOOC 2016


  /// Function for save iLM from XML descriptor
  //  @param array $application_xml Data of XML descriptor
  //  @param array $files_extract Filenames of extract files
  //  @return array Return an array content id of JAR files
  static function save_ilm_by_xml ($application_xml, $files_extract) {
    global $CFG, $USER;

    // Tratamento diferenciado se for do tipo HTML5:
    $source = "";
    $diretorio = "";
    if (strtolower($application_xml->type) == 'html5') {
      $i = 0;
      foreach ($files_extract as $key => $value) {
        $file = $CFG->dataroot . '/temp/' . $key;
        // Verifica se já existe a pasta no diretório dos iLM:
        if ($i == 0) {
          $source = $file;
          if (file_exists("ilm/" . basename($file))) {
            $j = 1;
            while (file_exists('ilm/' . basename($file) . "_" . $j)) {
                $j ++;
              }
            $diretorio = 'ilm/' . basename($file) . "_" . $j;
            mkdir($diretorio, 0777, true);
          } else {
            $diretorio = 'ilm/' . basename($file);
            mkdir($diretorio, 0777, true);
            }
          break;
          }
        $i ++;
        }

      foreach ($iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST) as $item) {
        if ($item->isDir()) {
          mkdir($diretorio . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
        } else {
          copy($item, $diretorio . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
          }
        }

      ilm_settings::delete_dir($source);
      return "./" . $diretorio;
      }

    $fs = get_file_storage();
    $file_jar = array();
    $files_ilm = explode(",", $application_xml->file_jar);
    $contextsystem = context_system::instance();

    foreach ($files_ilm as $value) {
      $file_ilm = array(
        'userid' => $USER->id,
        'contextid' => $contextsystem->id,
        'component' => 'mod_iassign',
        'filearea' => 'ilm',
        'itemid' => rand(1, 999999999),
        'filepath' => '/iassign/ilm/' . iassign_utils::format_pathname($application_xml->name) . '/' . iassign_utils::format_pathname($application_xml->version) . '/',
        'filename' => $value);

      $file_ilm = $fs->create_file_from_pathname($file_ilm, $CFG->dataroot . '/temp/' . $value);

      array_push($file_jar, $file_ilm->get_id());
      }

    foreach ($files_extract as $key => $value) {
      $file = $CFG->dataroot . '/temp/' . $key;
      if (file_exists($file))
        unlink($file);
      }
    return $file_jar;
    }


  /// Function for import the iLM from an package
  //  @param int $itemid Itemid of zip file
  static function import_ilm ($itemid) {
    global $CFG, $USER, $OUTPUT;

    // Verifica se existe algum XML anterior na pasta temp e o exclui
    if (file_exists($CFG->dataroot . '/temp/' . 'ilm-application.xml')) {
      unlink($CFG->dataroot . '/temp/' . 'ilm-application.xml');
      }

    $pathtemp = $CFG->dataroot . '/temp/';

    $contextuser = context_user::instance($USER->id);

    $fs = get_file_storage();
    $zip = new zip_packer();
    $files = $fs->get_directory_files($contextuser->id, 'user', 'draft', $itemid, '/');
    foreach ($files as $file) {
      if (!$file->is_directory())
        $files_extract = $zip->extract_to_pathname($file, $pathtemp);
      }

    $application_xml = @simplexml_load_file($CFG->dataroot . '/temp/' . 'ilm-application.xml', null, LIBXML_NOCDATA);

    // Verifica se o pacote possui o XML
    if (!$application_xml) {
      print($OUTPUT->notification(get_string('error_xml_ilm', 'iassign'), 'notifyproblem'));
      return;
    } else {
      $missing = "";
      if (!isset($application_xml->name)) {
        $missing .= "name";
        }
      if (!isset($application_xml->version)) {
        $missing .= ", version";
        }
      if (!isset($application_xml->type)) {
        $missing .= ", type";
        }
      if (!isset($application_xml->extension)) {
        $missing .= ", extension";
        }
      if (!isset($application_xml->file_jar)) {
        $missing .= ", file_jar";
        }
      if (!isset($application_xml->file_class)) {
        $missing .= ", file_class";
        }
      if (!isset($application_xml->width)) {
        $missing .= ", width";
        }
      if (!isset($application_xml->height)) {
        $missing .= ", height";
        }
      if (!isset($application_xml->evaluate)) {
        $missing .= ", evaluate";
        }
      if (strlen($missing) > 2) {
        print($OUTPUT->notification(get_string('error_xml_missing', 'iassign') . $missing . ".", 'notifyproblem'));
        return;
        }
      }

    // Faz a leitura do tipo de iLM e realiza a chamada à classe adequada
    $typec = strtolower($application_xml->type);
    require_once 'ilm_handlers/' . $typec . '.php';
    $typec::import_ilm($itemid, $files_extract, $application_xml, $contextuser, $fs);
    } // static function import_ilm($itemid)


  /// Function for list iLM defaults
  //  @return string Return an string with a table of iLM
  static function list_ilm () {
    global $DB, $OUTPUT;

    $iassign_ilm = $DB->get_records('iassign_ilm', array("enable" => 1));

    $str = '';
    $str .= '<table id="outlinetable" cellpadding="5" width="100%" >' . "\n";
    $str .= '<tr><td align=right><input type=button value="' . get_string('close', 'iassign') . '"  onclick="javascript:window.close ();"></td></tr>';

    if ($iassign_ilm) {
      foreach ($iassign_ilm as $ilm) {

        $url_view = new moodle_url('/mod/iassign/settings_ilm.php', array('action' => 'view', 'ilm_id' => $ilm->id));
        $link_view = $OUTPUT->action_link($url_view, iassign_icons::insert('view_ilm') . ' ' . get_string('read_more', 'iassign'));

        $str .= '<tr><td>';
        $str .= '<table class="generaltable boxaligncenter" width="100%">';

        $str .= '<tr>';
        $str .= '<td class=\'cell c0 actvity\' width=40%><strong>' . get_string('name_ilm', 'iassign') . ':</strong>&nbsp;' . $ilm->name . '</td>' . "\n";
        $str .= '<td><strong>' . get_string('version_ilm', 'iassign') . ':</strong>&nbsp;' . $ilm->version . '</td>' . "\n";
        $str .= '<td align=right>' . $link_view . '</td>' . "\n";
        $str .= '</tr>';
        $str .= '<tr><td colspan=3>' . iassign_language::get_description_lang(current_language(), $ilm->description) . '</td></tr>';
        $str .= '<tr><td colspan=3><a href="' . $ilm->url . '">' . $ilm->url . '</a></td></tr>';

        $str .= '</table>';
        $str .= '</td></tr>';
        }
      }
    $str .= '</table>';

    return $str;
    }

  /// Function for download and install an upgrade of an iLM
  //  @param int $ilm_id Id of iLM
  static function upgrade_ilm ($ilm_id) {
    global $DB, $CFG, $USER;

    $iassign_ilm = $DB->get_record('iassign_ilm', array('id' => $ilm_id));

    $upgrade_file = $iassign_ilm->url . 'ilm-upgrade_' . strtolower($iassign_ilm->name) . '.xml';

    $update_xml = @simplexml_load_file($upgrade_file, null, LIBXML_NOCDATA);

    $result = file_put_contents($CFG->dataroot . '/temp/' . $update_xml->file, fopen($iassign_ilm->url . $update_xml->file, 'r'));

    if (!$result)
      print_error('error_upgrade_ilm', 'iassign');
    else {
      $zip_filename = $CFG->dataroot . '/temp/' . $update_xml->file;
      $extension = explode(".", $zip_filename);
      if ($extension[count($extension) - 1] != 'ipz') {
        print($OUTPUT->notification(get_string('error_upload_ilm', 'iassign'), 'notifyproblem'));
        die;
        }
      $zip = new zip_packer();
      $fs = get_file_storage();
      $contextuser = context_user::instance($USER->id);
      $files_extract = $zip->extract_to_pathname($zip_filename, $CFG->dataroot . '/temp/');

      $application_xml = @simplexml_load_file($CFG->dataroot . '/temp/' . 'ilm-application.xml', null, LIBXML_NOCDATA);
      $description_str = htmlentities(str_replace(array('<description>', '</description>'), array('', ''), $application_xml->description->asXML()));

      $file_jar = self::save_ilm_by_xml($application_xml, $files_extract);

      if (file_exists($zip_filename))
        unlink($zip_filename);

      if (empty($file_jar)) {
        $msg_error = get_string('error_add_ilm', 'iassign') . "<br/>In upgrade_ilm(" . $ilm_id . ")<br/>\n";
        print_error($msg_error);
        //xx print_error('error_add_ilm', 'iassign');
        }
      else {
        $newentry = new stdClass();
        $newentry->name = (String) $application_xml->name;
        $newentry->version = (String) $application_xml->version;
        $newentry->url = (String) $application_xml->url;
        $newentry->description = $description_str;
        $newentry->extension = strtolower((String) $application_xml->extension);
        $newentry->file_jar = implode(",", $file_jar);
        $newentry->file_class = (String) $application_xml->file_class;
        $newentry->width = (String) $application_xml->width;
        $newentry->height = (String) $application_xml->height;
        $newentry->enable = 0;
        $newentry->timemodified = time();
        $newentry->author = $USER->id;
        $newentry->timecreated = time();
        $newentry->evaluate = (String) $application_xml->evaluate;
        $newentry->parent = $ilm_id;

        $newentry->id = $DB->insert_record("iassign_ilm", $newentry);
        }
      }

    return $iassign_ilm->id;
    }

//MOOC2014 -- inicio
//TODO: REVIEW
// static function confirm_move_iassign($ilmid, $ilm_parent)
// static function move_iassign($ilm_id)
//MOOC2014 -- final
  /// Function for list iLM versions with all informations
  //  @return string Return an string with a table of iLM
  static function view_ilm ($ilmid, $from) {
    global $DB;

    $iassign_ilm = $DB->get_record('iassign_ilm', array('id' => $ilmid));

    // Faz a leitura do tipo de iLM e realiza a chamada à classe adequada
    $typec = strtolower($iassign_ilm->type);
    require_once 'ilm_handlers/' . $typec . '.php';
    $retorno = $typec::view_ilm($ilmid, $from);
    return $retorno;
    }


  /// Function for get form variables for add, edit, or copy iLM params
  //  @param int $ilm_param_id Id of iLM param
  //  @param string $action String with the action
  //  @return object Return an object with forms variables
  static function add_edit_copy_param ($ilm_param_id, $action) {
    global $DB;

    require_once('params_form.php');
    $iassign_ilm_config = $DB->get_record('iassign_ilm_config', array('id' => $ilm_param_id));
    $param = new stdClass();
    $param->action = $action;
    $param->ilm_param_id = $ilm_param_id;

    $type = optional_param('type', NULL, PARAM_TEXT); //MOOC2014
    if ($type == NULL && $iassign_ilm_config) //MOOC2014
      $type = $iassign_ilm_config->param_type;

    if ($action == 'add') {
      $param->title = get_string('add_ilm', 'iassign');
      $param->iassign_ilmid = $ilm_param_id;
      $param->param_name = "";
      $param->param_value = "";
      $param->description = "";
      $param->visible = 1;
      } elseif ($action == 'edit') {
      if ($iassign_ilm_config) {
        $param->title = get_string('edit_ilm', 'iassign');
        $param->id = $iassign_ilm_config->id;
        $param->iassign_ilmid = $iassign_ilm_config->iassign_ilmid;

        $param->param_type = $type; //MOOC2014
        $param->param_name = $iassign_ilm_config->param_name;
        if ($type != 'choice' && $type != 'multiple') //MOOC2014
            $param->param_value = $iassign_ilm_config->param_value;
        else //MOOC2014
            $param->param_value = str_replace(", ", "\n", $iassign_ilm_config->param_value); //MOOC2014

        $param->param_value = $iassign_ilm_config->param_value;
        $param->description = $iassign_ilm_config->description;
        $param->visible = $iassign_ilm_config->visible;
        }
      } elseif ($action == 'copy') {
      if ($iassign_ilm_config) {
        $param->title = get_string('copy_ilm', 'iassign');
        $param->iassign_ilmid = $iassign_ilm_config->iassign_ilmid;
        $param->param_type = $type; //MOOC2014
        $param->param_name = $iassign_ilm_config->param_name;

        if ($type != 'choice' && $type != 'multiple') //MOOC2014
            $param->param_value = $iassign_ilm_config->param_value;
        else //MOOC2014
            $param->param_value = str_replace(", ", "\n", $iassign_ilm_config->param_value); //MOOC2014

        $param->description = $iassign_ilm_config->description;
        $param->visible = $iassign_ilm_config->visible;
        }
      }
    return $param;
    } // static function add_edit_copy_param($ilm_param_id, $action)


  /// Function for change visibility of iLM param
  //  @param int $ilm_param_id Id of iLM param
  //  @param int $status Indicator of change vibility (0 = hide, 1 = show)
  static function visible_param ($ilm_param_id, $status) {
    global $DB, $CFG;
    if ($status == 0)
      $visible = 1;
    else
      $visible = 0;
    $newentry = new stdClass();
    $newentry->id = $ilm_param_id;
    $newentry->visible = $visible;

    if (!$DB->update_record("iassign_ilm_config", $newentry))
      error(get_string('error_edit_param', 'iassign'));
    }


  /// Function for save in database an new iLM param
  //  @param object $param An object with iLM params
  static function add_param ($param) {
    global $DB;

    $newentry = new stdClass();
    $newentry->iassign_ilmid = $param->iassign_ilmid;
    //MOOC2014 $newentry->param_name = $param->param_name;
    $newentry->param_type = $param->param_type; //MOOC2014
    $newentry->param_name = iassign_utils::format_filename($param->param_name); //MOOC2014
    if ($newentry->param_type != 'choice' && $newentry->param_type != 'multiple') //MOOC2014
      $newentry->param_value = $param->param_value;
    else //MOOC2014
      $newentry->param_value = str_replace("\r\n", ", ", $param->param_value); //MOOC2014

    $newentry->description = $param->description;
    $newentry->visible = $param->visible;

    $newentry->id = $DB->insert_record("iassign_ilm_config", $newentry);
    if (!$newentry->id) {
      print_error('error_add_param', 'iassign');
      }
    }

  /// Function for save in database a iLM param edit
  //  @param object $param An object with iLM params
  static function edit_param($param) {

    global $DB;

    $updentry = new stdClass();
    $updentry->id = $param->id;
    $updentry->iassign_ilmid = $param->iassign_ilmid;
    $updentry->param_type = $param->param_type; //MOOC2014
    // $updentry->param_name = $param->param_name;
    $newentry->param_name = iassign_utils::format_filename($param->param_name); //MOOC2014

    if ($updentry->param_type != 'choice' && $updentry->param_type != 'multiple') //MOOC2014
      $updentry->param_value = $param->param_value;
    else //MOOC2014
      $updentry->param_value = str_replace("\r\n", ", ", $param->param_value); //MOOC2014

    $updentry->description = $param->description;
    $updentry->visible = $param->visible;

    if (!$DB->update_record("iassign_ilm_config", $updentry)) {
      error(get_string('error_edit_param', 'iassign'));
      }
    }

  /// Function for save in database a iLM param copy
  //  @param object $param An object with iLM params
  static function copy_param ($param) {
    global $DB;

    $newentry = new stdClass();
    $newentry->iassign_ilmid = $param->iassign_ilmid;
    //$newentry->param_name = $param->param_name;
    $newentry->param_name = iassign_utils::format_filename($param->param_name); //MOOC2014

    if ($newentry->param_type != 'choice' && $newentry->param_type != 'multiple') //MOOC2014
      $newentry->param_value = $param->param_value;
    else //MOOC2014
      $newentry->param_value = str_replace("\r\n", ", ", $param->param_value); //MOOC2014

    $newentry->description = $param->description;
    $newentry->visible = $param->visible;

    $newentry->id = $DB->insert_record("iassign_ilm_config", $newentry);
    if (!$newentry->id) {
      print_error('error_add_param', 'iassign');
      }
    }

  /// Function for delete iLM param of database
  //  @param int $param_id Id of iLM param
  static function delete_param ($param_id) {
    global $DB;

    if (!$DB->delete_records("iassign_ilm_config", array('id' => $param_id))) {
      print_error('error_delete_param', 'iassign');
      }
    }

  } // class ilm_settings


/// Class for manage iLM files (editor). 
class ilm_manager {

  var $id; // course id
  var $url;
  var $from;

  /// Constructor for the base ilm_manager class
  //  3.1 update PHP 7.0 compatibility for all moodle versions
  //D public function ilm_manager($id, $url, $from) { self::__construct($iassign, $cm, $course); }
  function __construct ($id, $url, $from) {
    $this->id = $id; // course id
    $this->url = $url;
    $this->from = $from;
    }

  /// Function to get iAssign content file in Moodle data (exercise)
  //  @calledby x function preview_ilm($iassign_ilm) : 
  //  @calledby ilm_manager.php : with 'action=get' in '$ilm_manager_instance->get_file_ilm($ilmid, $fileid)'
  function get_file_ilm () {
    $ilmid = optional_param('ilmid', NULL, PARAM_INT);
    $fileid = optional_param('fileid', NULL, PARAM_INT);
    //$filename = optional_param('filename', NULL, PARAM_TEXT);
    $fs = get_file_storage();
    $md_file = $fs->get_file_by_id($fileid);
    $ilm_content_file = $md_file->get_content();
    return $ilm_content_file;
    }

  /// Function for creating an new file in online editor
  function ilm_editor_new () {
    global $CFG, $DB, $OUTPUT, $PAGE;

    $ilmid = optional_param('ilmid', NULL, PARAM_INT); // iAssign ID
    $dirid = optional_param('dirid', NULL, PARAM_INT);
    $iassign = $DB->get_record("iassign_ilm", array("id" => $ilmid));
    $context = context_course::instance($this->id);
    $returnurl = $CFG->wwwroot . "/mod/iassign/ilm_manager.php?from=$this->from&id=$this->id&dirid=$dirid&ilmid=$ilmid";

    // verify if the JAR file $PAGE->set_course($course); is registered in DB (table '*_iassign_ilm')
    if (!$iassign) {
      print $OUTPUT->notification(get_string('error_confirms_ilm', 'iassign'), 'notifysuccess');
      die();
      }

    $temp = explode(",", $iassign->extension);
    $extension = $temp[0]; // default extension for this iLM
    //2016/02/16: IMPORTANTE trocar formatador para "nao formatado", pois esta destruindo o nome do arquivo (e.g. limpa brancos)
    //2016/02/16: $stringArchiveContent = optional_param('iLM_PARAM_ArchiveContent', NULL, PARAM_ALPHANUMEXT);
    $stringArchiveContent = optional_param('iLM_PARAM_ArchiveContent', NULL, PARAM_RAW); // iLM 2
    // $stringArchiveContent = optional_param('iLM_PARAM_ArchiveContent', NULL, PARAM_RAW); // iLM 1

    if ($stringArchiveContent != NULL) {
      // $stringArchiveContent = $_POST['iLM_PARAM_ArchiveContent'];
      //2016/02/16: IMPORTANTE trocar formatador para "nao formatado", pois esta destruindo o conteudo do arquivo
      //2016/02/16: $filename = optional_param('filename', NULL, PARAM_ALPHANUMEXT);
      $filename = optional_param('filename', NULL, PARAM_RAW);

      $filename = iassign_utils::format_filename($filename);
      $arrayfilename = explode(".", $filename);
      // if (count ( $arrayfilename ) == 1) $filename = $arrayfilename[0] . '.' . $extension;
      $count_dots = count($arrayfilename);
      if ($count_dots > 0) { // at least one dot mark
        $last_name = $arrayfilename[$count_dots - 1];
        if ($last_name != $extension)
          $filename = $filename . '.' . $extension;
        }
      else { // no extension...
        $filename = $filename . '.' . $extension;
        }

      $_SESSION['file_name'] = $filename;

      //$this->write_file_iassign($string, $filename);
      $this->write_file_iassign($stringArchiveContent, $filename);

      die();

      }
    else { // if ($stringArchiveContent != NULL)
      // iLM On-line editor 
      if ($extension == "html" || $extension == "ivph" || strtolower($iassign->type) == 'html5') { // if iLM is HTML5 is inside a frame
        $str_get_iLM = "window.frames.iLM";
        $str_submitbutton_name = "javascript:window.submit_iLM_Answer()";
        }
      else { // otherwise it is JAR named 'iLM'
        $str_get_iLM = "document.iLM";
        $str_submitbutton_name = "submit_iLM_Answer()"; // to call 'submit_iLM_Answer()'
        }

      $fs = get_file_storage();
      $files = $fs->get_area_files($context->id, 'mod_iassign', 'activity');
      $files_array = '';
      foreach ($files as $value) {
        if ($value->get_filename() != ".")
            $files_array .= "'" . $value->get_filename() . "',";
        }
      $files_array .= "''";
      $file = null;
      $ia_content = "";
      $filename = "";
      $error_files_exists = get_string('error_file_exists', 'iassign');

      $output = "<script type='text/javascript'>
   //<![CDATA[
   //D alert('locallib.php: ilm_editor_new');

   function submit_iLM_Answer () {
     var docFormOnLineEditor = document.formEnvio;
     var resposta_exerc = new Array(3);
     var valor_resposta = new Array(3);
     var sessao = new Array(3);
     var doc_iLM = " . $str_get_iLM . "; // 'window.frames.iLM' or 'document.iLM'
     resposta_exerc[0] = doc_iLM.getAnswer();
     valor_resposta[0] = doc_iLM.getEvaluation();
     docFormOnLineEditor.iLM_PARAM_ActivityEvaluation.value = valor_resposta[0];
     docFormOnLineEditor.iLM_PARAM_ArchiveContent.value = resposta_exerc[0];
     var files = new Array(" . $files_array . ");
     var filename=docFormOnLineEditor.filename.value+'.'+'$extension';
" .
     //D alert('#files = ' + files.length);
     // 2016/02/16: NOT necessary, since it is teacher editing (perhaps he only make an example as exercise)
     // if (resposta_exerc[0] == -1) {
     //  alert('" . get_string('error_null_iassign', 'iassign') . "'); // ERRO: O exercício esta vazio ou não foi alterado
     //  return false;
     //  } else {
     "
     if (docFormOnLineEditor.filename.value=='') {
      // ERRO: O nome do arquivo está vazio.
      alert('" . get_string('error_file_null_iassign', 'iassign') . "');
      return false; 
      }
   // }

    for (i=0; i<files.length; i++) {
      if (files[i]==docFormOnLineEditor.filename.value || files[i]==filename) {
        alert('" . $error_files_exists . "');
        return false;
        }
      }
    docFormOnLineEditor.submit();
    return true;
    }
   //]]>
</script>\n";

      $output .= "
   <form name='formEnvio' id='formEnvio' method='post' enctype='multipart/form-data'>\n";
      $output .= $OUTPUT->box_start();

      // Put text "File name" and the corresponding "input" to enter the file nama: "File name: [   ]"
      $output .= "
   <table width='100%' cellpadding='20'>
   <tr><td>" . get_string('label_file_iassign', 'iassign') . " <input type='text' name='filename' size=50/>

     <input type=button value='" . get_string('label_write_iassign', 'iassign') . "' title='' onclick='" . $str_submitbutton_name . ";'/></td>
     <td><input type=button value='" . get_string('close', 'iassign') . "' title='' onclick='javascript:window.location = \"$returnurl\";'/></td>
   </tr>
   </table>\n";

      $output .= $OUTPUT->box_end();
      $output .= "<center>\n";

      // Prepare tag to load the iLM. In case of JAR it will prepare the tag "applet". In case of HTML5 will prepare an "iframe".
      // Since it is the activity file, it is not necessary to use 'ilm_security'
      $output_ilm .= ilm_settings::build_ilm_tags($ilmid, array("type" => "editor_new", "notSEND" => "true"));
      $output .= $output_ilm;

      $output .= "   <input type='hidden' name='iLM_PARAM_ArchiveContent' value='" . $ia_content . "'>
  <input type='hidden' name='iLM_PARAM_ActivityEvaluation'>
  </center>
  </form>\n";

      $title = get_string('title_editor_iassign', 'iassign') . " - " . $iassign->name . " " . $iassign->version; //MOOC2014
      $PAGE->navbar->add($title);
      $PAGE->set_title($title);
      $PAGE->set_heading($title); // insert title above the navigation bar
      print $OUTPUT->header();
      //print $OUTPUT->heading("&nbsp;&nbsp;" . $title); // insert title below the navigation bar

      print $output;

      print $OUTPUT->footer();
      }
    die();
    } // function ilm_editor_new()


  /// Function for editing an file in online editor
  //  @calledby ilm_manager.php: case 'update': $ilm_manager_instance->ilm_editor_update($security['content'], $security['token'], $security['secure_id']);
  function ilm_editor_update ($filename, $content_file, $token, $secure_id) {
    global $CFG, $DB, $OUTPUT, $PAGE;

    $ilmid = optional_param('ilmid', NULL, PARAM_INT); // iAssign ID
    $dirid = optional_param('dirid', NULL, PARAM_INT);
    $fileid = optional_param('fileid', NULL, PARAM_TEXT);
    $iassign = $DB->get_record("iassign_ilm", array("id" => $ilmid));
    $returnurl = $CFG->wwwroot . "/mod/iassign/ilm_manager.php?from=$this->from&id=$this->id&dirid=$dirid&ilmid=$ilmid";

    // If iLM is not registered, error!
    if (!$iassign) {
      print $OUTPUT->notification(get_string('error_confirms_ilm', 'iassign'), 'notifyproblem');
      die;
      }

    //2017/03/12 //QUARANTINE agora vindo de 'ilm_manager.php' via os 3 parametros
    //2017/03/12 $fs = get_file_storage(); // from Moodle data
    //2017/03/12 $filename = ''; $end_file = ''; $file = $fs->get_file_by_id($fileid); if ($file) $filename = iassign_utils::format_filename($file->get_filename());
    //D echo "locallib.php: ilm_editor_update(): filename=$filename<br/>\n";
    //2016/02/16: IMPORTANTE trocar formatador para "nao formatado", pois esta destruindo o conteudo do arquivo
    //2016/02/16: $stringArchiveContent = optional_param('iLM_PARAM_ArchiveContent', NULL, PARAM_ALPHANUMEXT);
    $stringArchiveContent = optional_param('iLM_PARAM_ArchiveContent', NULL, PARAM_RAW);

    //D echo "stringArchiveContent:$stringArchiveContent<br/>\n";

    if ($stringArchiveContent != NULL) {
      $this->update_file_iassign($stringArchiveContent, $filename, $fileid);
      die();
      } else { // if ($stringArchiveContent != NULL)
      $end_file = '';
      if ($content_file) {
        // 2017/03/12 $token=''; $view=-1; $end_file = $CFG->wwwroot . '/mod/iassign/ilm_security.php?id=' . $fileid . '&token=' . $token . '&view=' . $view; // need full path...
        $end_file = $CFG->wwwroot . '/mod/iassign/ilm_security.php?id=' . $secure_id . '&action=update&token=' . $token . '&view=0'; // need full path...
        }

      $temp = explode(",", $iassign->extension);
      $extension = $temp[0]; // default extension for this iLM

      if ($extension == "html" || $extension == "ivph") { // if iLM is HTML5 is inside a frame
        $str_get_iLM = "window.frames.iLM";
        $str_submitbutton_name = "javascript:window.submit_iLM_Answer()";
        } else { // otherwise it is JAR named 'iLM'
        $str_get_iLM = "document.iLM";
        $str_submitbutton_name = "submit_iLM_Answer()"; // to call 'submit_iLM_Answer()'
        }

      $output = "<script type='text/javascript'>
   //<![CDATA[
   function submit_iLM_Answer () {
     var docFormEditor = document.formEnvio;
     var activityAnswer; // to get activity answer (text)
     var activityValue; // to get activity answer value (float)
     var doc_iLM = " . $str_get_iLM . "; // 'window.frames.iLM' or 'document.iLM'
//alert('locallib.php: ilm_editor_update: submit_iLM_Answer(): ' + doc_iLM);
     //activityAnswer = doc_iLM.getAnswer();
     //activityValue = doc_iLM.getEvaluation();
     activityAnswer = " . $str_get_iLM . ".getAnswer();
     activityValue = " . $str_get_iLM . ".getEvaluation();
//alert('locallib.php: ilm_editor_update: submit_iLM_Answer(): ' + activityAnswer);
     docFormEditor.iLM_PARAM_ActivityEvaluation.value = activityValue;
     docFormEditor.iLM_PARAM_ArchiveContent.value = activityAnswer;
alert('locallib.php: ilm_editor_update: submit_iLM_Answer(): ' + activityAnswer);
     docFormEditor.submit();
     return true;
     }
  //]]>
</script>\n";
      // 2016/02/16: NOT necessary, since it is teacher editing (perhaps he only make an example as exercise)
      // if (activityAnswer == -1) { // E.g. in iGeom it is possible to turn an example in exercise
      //   alert('" . get_string('error_null_iassign', 'iassign') . "'); // ERRO: O exercício esta vazio ou não foi alterado
      //   return true;
      //   }

      $output .= "
 <form name='formEnvio' id='formEnvio' method='post' enctype='multipart/form-data'>\n";
      $output .= $OUTPUT->box_start();
      $output .= "             
  <table width='100%' cellpadding='20'>
  <tr><td width='75%'>" . get_string('label_file_iassign', 'iassign') . "<b>$filename</b></td>
  <td align='right' width='25%'><input type=button value='" . get_string('label_write_iassign', 'iassign') . "' title='' onclick='submit_iLM_Answer();'/>
  <input type='hidden' name='filename' value='$filename'/></td>
  <td>
    <input type=button value='" . get_string('close', 'iassign') . "' title='' onclick='javascript:window.location = \"$returnurl\";'/></td>
  </tr>              
  </table>\n";

      $output .= $OUTPUT->box_end();
      $output .= " <center>\n";

      // Since it is the activity file, it is not necessary to use 'ilm_security' (id_iLM_security)
      $output .= ilm_settings::build_ilm_tags($ilmid, array("type" => "editor_update", "notSEND" => "false", "Proposition" => $end_file));

      $output .= " <input type='hidden' name='iLM_PARAM_ArchiveContent' value=''>
 <input type='hidden' name='iLM_PARAM_ActivityEvaluation' value=''>
 </center>
 </form>\n";

      $title = get_string('title_editor_iassign', 'iassign') . " - " . $iassign->name . " " . $iassign->version; //MOOC2014
      $PAGE->navbar->add($title);
      $PAGE->set_title($title);
      $PAGE->set_heading($title); // insert title above the navigation bar
      print $OUTPUT->header();
      //print $OUTPUT->heading("&nbsp;&nbsp;" . $title); // insert title below the navigation bar
      print $output;
      print $OUTPUT->footer();
      } // else if ($stringArchiveContent != NULL)
    die();
    } // function ilm_editor_update()


  /// Function for write iAssign file in Moodle data (exercise)
  //  @param string $stringArchiveContent Content of iassign file
  //  @param string $filename Filename of iassign file
  function write_file_iassign ($stringArchiveContent, $filename) {
    global $USER;

    $ilmid = optional_param('ilmid', NULL, PARAM_INT);
    $context = context_course::instance($this->id);
    $fs = get_file_storage();
    $dirid = $this->get_dir_ilm('dirid');
    $dir = $fs->get_file_by_id($dirid);

    $fileinfo = array('contextid' => $context->id, // ID of course
      'component' => 'mod_iassign', // usually = table name
      'filearea' => 'activity', // usually = table name
      'itemid' => 0, // usually = ID of row in table
      'filepath' => $dir->get_filepath(), // any path beginning and ending whith '/'
      'userid' => $USER->id,
      'author' => $USER->firstname . ' ' . $USER->lastname, 'license' => 'allrightsreserved', // allrightsreserved
      'filename' => $filename); // any filename
    // Create file containing text. '$stringArchiveContent'
    $file_course = $fs->create_file_from_string($fileinfo, $stringArchiveContent);
    $output = "
    <script type='text/javascript'>
     //<![CDATA[
     alert('" . get_string('sucess_write', 'iassign') . "');
     window.location='" . $this->url . "&dirid=$dirid&ilmid=$ilmid';
     //]]>
     </script>";
    print $output;
    die();
    }


  /// Function for write iAssign file in Moodle data (exercise)  
  //  @param string $stringArchiveContent Content of iassign file
  //  @param string $filename Filename of iassign file
  //  @param int $itemid Itemid of iassign file
  function update_file_iassign ($stringArchiveContent, $filename, $fileid) {
    global $OUTPUT, $USER;

    $ilmid = optional_param('ilmid', NULL, PARAM_INT);
    $context = context_course::instance($this->id);
    $dirid = $this->get_dir_ilm('dirid');

    if ($stringArchiveContent != (-1)) {
      $fs = get_file_storage();
      $file = $fs->get_file_by_id($fileid);
      if (!$file) {
        print $OUTPUT->notification(get_string('error_view_ilm', 'iassign'), 'notifyproblem');
        die;
        }

      $fileinfo = array('contextid' => $context->id, // ID of context
        'component' => 'mod_iassign', // usually = table name
        'filearea' => 'activity', // usually = table name
        'itemid' => 0, // usually = ID of row in table
        'filepath' => $file->get_filepath(), // any path beginning and ending with '/'
        'userid' => $USER->id,
        'author' => $USER->firstname . ' ' . $USER->lastname,
        'license' => 'allrightsreserved', // allrightsreserved
        'timecreated' => $file->get_timecreated(),
        'filename' => $file->get_filename()); // any filename
      $file->delete();
      $file_course = $fs->create_file_from_string($fileinfo, $stringArchiveContent); //$string
      }
    $output = "<script type='text/javascript'>
     //<![CDATA[
     alert('" . get_string('sucess_update', 'iassign') . "');\n
     window.location='" . $this->url . "&dirid=$dirid&ilmid=$ilmid';
     //]]>
     </script>";
    print $output;
    die();
    }

  /// Function for create an tag for iAssign filter
  //  @calledby function preview_ilm($iassign_ilm) : $tag_filter = $this->tag_ilm($fileid);
  //  @calledby function tinymce_ilm($fileid) : $tag_filter = $this->tag_ilm($fileid);
  //  @calledby function editor_ilm($fileid, $editor) : $tag_filter = $this->tag_ilm($fileid);
  //  @calledby function atto_ilm ($fileid) : $tag_filter = $this->tag_ilm($fileid);
  //  @param int $fileid Id of file
  //  @return string Return an string with an tag of iassign filter
  function tag_ilm ($fileid) {
    global $DB;

    $fs = get_file_storage();
    $width = '600';
    $height = '400';
    $file = $fs->get_file_by_id($fileid);
    $filetype = explode(".", $file->get_filename());
    $iassign_ilm = $DB->get_records('iassign_ilm', array("enable" => 1, "parent" => 0));
    foreach ($iassign_ilm as $value) {
      $extensions = explode(",", $value->extension);
      if (in_array($filetype[1], $extensions)) {
        $width = $value->width;
        $height = $value->height;
        }
      }
    return("<p>&lt;ia toolbar=disable width=$width height=$height &gt;$fileid&lt;/ia&gt;</p>");
    }

  /// Function for delete iAssign file in Moodle data (exercise)
  function delete_file_ilm () {
    $ilmid = optional_param('ilmid', NULL, PARAM_INT);

    $fs = get_file_storage();
    $fileid = optional_param('fileid', NULL, PARAM_RAW);
    $file = $fs->get_file_by_id($fileid);
    if ($file)
      $file->delete();
    redirect(new moodle_url($this->url . '&dirid=' . $this->get_dir_ilm('dirid') . '&ilmid=' . $ilmid));
    die();
    }

  /// Function for delete selected iAssign file in Moodle data (exercise)
  function delete_selected_ilm () {
    $ilmid = optional_param('ilmid', NULL, PARAM_INT);
    $fs = get_file_storage();
    $context = context_course::instance($this->id);
    $files_id = explode(",", optional_param('files_id', '', PARAM_TEXT));
    $dirid = $this->get_dir_ilm('dirid');
    foreach ($files_id as $file_id) {
      $file = $fs->get_file_by_id($file_id);
      if ($file) {
        if (!$file->is_directory())
            $file->delete();
        else {
            $files_delete = $fs->get_directory_files($context->id, 'mod_iassign', 'activity', 0, $file->get_filepath(), true, true);
            foreach ($files_delete as $value)
                $value->delete();
            $file->delete();
          }
        }
      }
    redirect(new moodle_url($this->url . '&dirid=' . $dirid . '&ilmid=' . $ilmid));
    die();
    }

  /// Function for duplicate iAssign file from "online" edition
  //  @callby JavaScript function 'duplicate_ilm(ilmid, filename, fileid)' bellow
  function duplicate_file_ilm() {
    global $USER, $COURSE;

    $ilmid = optional_param('ilmid', NULL, PARAM_INT);
    $fs = get_file_storage();
    $fileid = optional_param('fileid', NULL, PARAM_INT);
    $filename = optional_param('filename', NULL, PARAM_RAW);

    $file = $fs->get_file_by_id($fileid);
    $context = context_course::instance($this->id);

    $fileinfo = array(
      'contextid' => $context->id, // ID of context
      'component' => 'mod_iassign', // usually = table name
      'filearea' => 'activity', // usually = table name
      'itemid' => 0, // usually = ID of row in table
      'filepath' => $this->get_dir_ilm('dir_base'), // any path beginning and ending in /
      'userid' => $USER->id,
      'author' => $USER->firstname . ' ' . $USER->lastname, 'license' => 'allrightsreserved', // allrightsreserved
      'timecreated' => $file->get_timecreated(), 'filename' => $filename); // any filename

    $newfile = $fs->create_file_from_string($fileinfo, $file->get_content());

    redirect(new moodle_url($this->url . "&dirid=" . $this->get_dir_ilm('dirid') . "&ilmid=" . $ilmid));
    die();
    }

  /// Function for rename iAssign file
  function rename_file_ilm () {
    $ilmid = optional_param('ilmid', NULL, PARAM_INT);
    $fs = get_file_storage();
    $fileid = optional_param('fileid', NULL, PARAM_INT);
    $filename = optional_param('filename', NULL, PARAM_TEXT);

    $file = $fs->get_file_by_id($fileid);

    $file->rename($this->get_dir_ilm('dir_base'), $filename);

    //MOOC2014 redirect(new moodle_url($this->url . "&dirid=" . $dir_parent . "&ilmid=$ilmid"));
    redirect(new moodle_url($this->url . '&dirid=' . $this->get_dir_ilm('dirid') . '&ilmid=' . $ilmid));
    //D echo "locallib.php: rename_file_ilm; ilmid=$ilmid, dirid=" . $this->get_dir_ilm('dirid') . "<br/>\n"; //
    die();
    }

  /// Function for get iassign file for iassign form
  function add_ilm () {
    $fileid = optional_param('fileid', NULL, PARAM_INT);
    $filename = optional_param('filename', NULL, PARAM_TEXT);

    $output = "
  <script type='text/javascript'>
    //<![CDATA[
    var iassign_file_link = window.opener.document.getElementById('iassign_file_link');
    iassign_file_link.innerHTML = '$filename';
    window.opener.document.forms['mform1'].file.value='$fileid';
    window.opener.document.forms['mform1'].filename.value='$filename';
    window.close();
    //]]>
  </script>";
    print $output;
    die();
    }

  /// Function for preview iAssign file from iAssign Repository (it uses iAssign filter)
  //  @see /iassign_filter/filter.php : function 'filter($text, array $options = array())' and exit (do not continue bellow)
  //  @see ilm_manager.php : $ilm_manager_instance->preview_ilm();
  function preview_ilm ($courseid, $iassign_ilm) {
    global $OUTPUT, $CFG, $USER;

    $fileid = optional_param('fileid', NULL, PARAM_TEXT);
    $title = get_string('modulename', 'iassign'); // iAssign
    print $OUTPUT->header();
    print $OUTPUT->box_start();

    $javascript = "
   <!-- iAssign preview iLM content / LInE - http://line.ime.usp.br -->
   <script type='text/javascript'>
    //<![CDATA[
    function submit_close () { 
     window.opener.location.reload();
    window.close();
    }
    //]]>
   </script>\n";

    // Use iAssin filter to change "&lt;ia toolbar=disable width=800 height=600 &gt;45&lt;/ia&gt;"
    // to the complete "applet" tag
    // Version previous to: 2016/02/16: do not present menus' options - similar to the filter
    //TODO: E' melhor apresentar o iMA completo, sem usar o filtro com opcao "<param name='SOH_ADD' value='ADD'>"
    //TODO: Talvez seja melhor colocar opcao no filtro para evitar opcao 'ADD'!
    //TODO: Here reaches '/iassign_filter/filter.php : function 'filter($text, array $options = array())' with parameter 'originalformat=0' (in glossary is 1)
    //TODO: then, I changed '/iassign_filter/filter.php' (functions 'filter(...) to avoid 'SOH_ADD' if 'originalformat=0'
    $ilm_name = substr(strtolower($iassign_ilm->name), 0, 6);

    if ($ilm_name != "ivprog") {
      //MOOC2014 $tag_filter = format_text($this->tag_ilm($fileid));
      $tag_filter = $this->tag_ilm($fileid); // build the iAssign filter tag, something like: <ia toolbar=disable width=800 height=600 >58</ia>
      $tag_filter_filtered = format_text($tag_filter, FORMAT_MOODLE, array('overflowdiv' => true, 'allowid' => true)); // Moodle 3.X
    } else {
      require_once($CFG->dirroot . '/mod/iassign/ilm_security.php');
      $content_or_id_from_ilm_security = $this->get_file_ilm();
      $timecreated = time();
      $token = md5($timecreated);
      $id_iLM_security = ilm_security::write_iLM_security($USER->id, $timecreated, -1, $content_or_id_from_ilm_security); // insert in 'iassign_security'

      $param = 'id=' . $id_iLM_security . '&ilmid=' . $iassign_ilm->id . '&token=' . $token . '&view=0';
      $url_file = $CFG->wwwroot . '/mod/iassign/ilm_security.php?action=preview&' . $param;
      $tag_filter_filtered = ilm_settings::build_ilm_tags($iassign_ilm->id, array("type" => "view", "notSEND" => "true", "Proposition" => $url_file)); // buil iLM tag (JAR or HTML5)
      }

    $html = "
  <form name='formEnvio' id='formEnvio' method='post' enctype='multipart/form-data'>
  <table border='1'>
   <tr><td>
     <!-- iLM calls : begin -->\n" . $tag_filter_filtered . "\n
     <!-- iLM calls : end -->
     </td></tr>
  </table>
  <table>
   <tr>
     <td align='center'>
       <input type=button value='" . get_string('close', 'iassign') . "' title='' onclick='submit_close();' />
     </td></tr>
  </table>
  </form>\n";

    print $javascript . $html;
    print $OUTPUT->box_end();

    //NAO echo format_string($html); // Moodle 3.X
    die();
    }

// function preview_ilm($iassign_ilm)
  //r_ /// Function for preview iassign file from iassign filter.
  //r_ function preview_ilm() {
  //r_   $fileid = optional_param('fileid', NULL, PARAM_TEXT);
  //r_   $tag_filter = $this->tag_ilm($fileid);
  //r_   $javascript = "<script type='text/javascript'>
  //r_   //<![CDATA[
  //r_   function submit_close() { window.opener.location.reload(); window.close(); }
  //r_   //]]>
  //r_   </script>";
  //r_   $html = "<html><head></head><body><form name='formEnvio' id='formEnvio' method='post' enctype='multipart/form-data'>
  //r_   <table border='1'><tr><td>$tag_filter</td></tr></table><table><tr><td align='center'><input type=button value='" . get_string('close', 'iassign') . "' title='' onclick='submit_close();'/></td></tr>
  //r_   </table></form></body></html>";
  //r_   echo $javascript . format_text($html);
  //r_   die;
  //r_   }
  /// Function for export an package (zip) of iassign files
  function export_files_ilm () {
    global $CFG;
    $context = context_course::instance($this->id);

    $files_id = explode(",", optional_param('files_id', '', PARAM_TEXT));

    $zip_filename = $CFG->dataroot . '/temp/backup-iassign-files-' . date("Ymd-Hi") . '.zip';
    $zip = new zip_archive();
    $zip->open($zip_filename);
    $fs = get_file_storage();
    foreach ($files_id as $file_id) {
      $file = $fs->get_file_by_id($file_id);
      if (!$file->is_directory())
        $zip->add_file_from_string($file->get_filename(), $file->get_content());
      else {
        $zip->add_directory($file->get_filepath());
        $files_zip = $fs->get_directory_files($context->id, 'mod_iassign', 'activity', 0, $file->get_filepath(), true, true);
        foreach ($files_zip as $value) {
          if (!$value->is_directory())
            $zip->add_file_from_string($value->get_filepath() . $value->get_filename(), $value->get_content());
          else
            $zip->add_directory($value->get_filepath());
          }
        }
      }
    $zip->close();

    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private", false);
    header("Content-Type: application/zip");
    header("Content-Disposition: attachment; filename=\"" . basename($zip_filename) . "\";");
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: " . @filesize($zip_filename));
    set_time_limit(0);
    @readfile("$zip_filename") || die("File not found.");
    unlink($zip_filename);
    exit;
    }

  /// Function of execute a command in button editor tinymce
  //  @param int $fileid Id of file
  function tinymce_ilm ($fileid) {

    $tag_filter = $this->tag_ilm($fileid);

    $output = "<script type='text/javascript'>
  //<![CDATA[
     var tag_filter = '$tag_filter';
     if (window.opener.tinyMCE.execCommand('mceiAssignReturn', tag_filter)) {
       // all right, insert it
     } else {
       alert('Error trying to insert iLM content (tinymce_ilm " + $fileid + ")');
       }
     // window.close();
  //]]>
</script>\n";
    print $output;
    die();
    }

  //D sugestao para uso de apenas uma funcao para chamar os editores, cortando 17 linhas de codigo
  /// Function to add content to the Editor Window
  //  Updated: Marcio Passos - marciopassosbel[at]gmail[dot]com :: 07 / Jul / 2016
  //  @param int $fileid Id of file
  function editor_ilm ($fileid, $editor) {
    $tag_filter = $this->tag_ilm($fileid); // Prepare tag like: <ia toolbar=disable width=800 height=600>ID</ia>
    $output1 = "<script type='text/javascript'>
  //<![CDATA[
  var tag_filter = '$tag_filter';\n";

    if ($editor == 'atto') {
      $output2 = "  if (window.opener.document.execCommand('insertHTML', false, tag_filter)) { } // all right, insert it";
      } elseif ($editor == 'tinyMCE') {
      $output2 = "  if (window.opener.tinyMCE.execCommand('mceiAssignReturn', tag_filter)) { } // all right, insert it";
      } else
      $output2 = "";

    $output3 = "  else { console.log('Error trying to insert iLM content (atto_ilm " . $fileid . ")'); }
  window.close();
  //]]>
</script>\n";

    //D var_dump($output);
    print $output1 . $output2 . $output3;

    die();
    }

  /// Function to add content to the Atto Editor Window
  //  Updated: Marcio Passos - marciopassosbel[at]gmail[dot]com :: 07 / Jul / 2016
  //  @param int $fileid Id of file
  function atto_ilm($fileid) {
    global $CFG, $DB;

    $tag_filter = $this->tag_ilm($fileid);

    $output = "<script type='text/javascript'>
     //<![CDATA[
     var tag_filter = '$tag_filter';
     if (window.opener.document.execCommand('insertHTML', false, tag_filter)) {
      // all right, insert iLM tag to this editor
     } else {
     // error trying to insert
     alert('Error trying to insert iLM content (atto_ilm " + $fileid + ")');
       }
     //window.close();
     //]]>
</script>\n";
    print $output;
    die();
    }

  /// Function for get path and infos of dirs: dirid,  dir_base, dir_parent, dir_home
  //  @param string $key Key for return information
  //  @return Ambigous <unknown, number, string, NULL> Return an information requested
  function get_dir_ilm ($key) {
    $fs = get_file_storage();
    $context = context_course::instance($this->id);
    $dirid = optional_param('dirid', 0, PARAM_INT);
    $dir_home = $fs->get_file($context->id, 'mod_iassign', 'activity', 0, $dir_base = '/', '.');
    if ($dirid == 0) {
      $dir = ($dir_home = $fs->create_directory($context->id, 'mod_iassign', 'activity', 0, $dir_base));
      $dirid = $dir->get_id();
      } else {
      $dir = $fs->get_file_by_id($dirid);
      $dir_base = $dir->get_filepath();
      }
    $dir_parent = $dir->get_parent_directory();
    $data = array('dirid' => $dirid, 'dir_base' => $dir_base, 'dir_parent' => ($dir_parent == NULL ? 0 : $dir_parent->get_id()), 'dir_home' => $dir_home->get_id());
    return $data[$key];
    }

  /// Function for create an new dir
  function new_dir_ilm () {
    global $USER;

    $ilmid = optional_param('ilmid', NULL, PARAM_INT);

    $dirname = optional_param('dirname', NULL, PARAM_TEXT);
    $dir_base = $this->get_dir_ilm('dir_base');

    $context = context_course::instance($this->id);
    $fs = get_file_storage();

    $fs->create_directory($context->id, 'mod_iassign', 'activity', 0, $dir_base . $dirname . "/", $USER->id);
    $dir_base = $fs->get_file($context->id, 'mod_iassign', 'activity', 0, $dir_base . $dirname . "/", '.');
    $dir_base->set_author($USER->firstname . ' ' . $USER->lastname);

    redirect(new moodle_url($this->url . '&dirid=' . $this->get_dir_ilm('dirid') . '&ilmid=' . $ilmid));
    }

  /// Function for delete an dir
  function delete_dir_ilm () { //
    $fs = get_file_storage();
    $ilmid = optional_param('ilmid', NULL, PARAM_INT);
    $context = context_course::instance($this->id);
    $dir = $fs->get_file_by_id($this->get_dir_ilm('dirid'));
    $dir_parent = $this->get_dir_ilm('dir_parent');
    if ($dir) {
      if ($dir->is_directory()) {
        $files_delete = $fs->get_directory_files($context->id, 'mod_iassign', 'activity', 0, $dir->get_filepath(), true, true);
        foreach ($files_delete as $value)
            $value->delete();
        $dir->delete();
        }
      }
    redirect(new moodle_url($this->url . '&dirid=' . $dir_parent . '&ilmid=' . $ilmid));
    die();
    }

  /// Function for rename an dir
  function rename_dir_ilm () {
    $fs = get_file_storage();
    $ilmid = optional_param('ilmid', NULL, PARAM_INT);
    $context = context_course::instance($this->id);
    $dir = $fs->get_file_by_id($this->get_dir_ilm('dirid'));
    $dir_parent = $this->get_dir_ilm('dir_parent');
    $dirname = optional_param('dirname', NULL, PARAM_TEXT);

    $pathname = explode("/", substr($dir->get_filepath(), 0, strlen($dir->get_filepath()) - 1));
    if ($dir->is_directory()) {
      $files_rename_path = $fs->get_directory_files($context->id, 'mod_iassign', 'activity', 0, $dir->get_filepath(), true, true);
      foreach ($files_rename_path as $value)
        $value->rename(str_replace($pathname[count($pathname) - 1], $dirname, $value->get_filepath()), $value->get_filename());
      $dir->rename(str_replace($pathname[count($pathname) - 1], $dirname, $dir->get_filepath()), $dir->get_filename());
      }

    redirect(new moodle_url($this->url . '&dirid=' . $dir_parent . '&ilmid=' . $ilmid));
    die();
    }

  /// Function for move an dir and your content for other dir
  function selected_move_ilm() {
    global $PAGE, $OUTPUT, $CFG;
    $fs = get_file_storage();
    $context = context_course::instance($this->id);

    $ilmid = optional_param('ilmid', NULL, PARAM_INT);
    $dirid = $this->get_dir_ilm('dirid');
    $dir_base = $this->get_dir_ilm('dir_base');
    $files_id = explode(",", optional_param('files_id', '', PARAM_TEXT));

    $code_javascript_ilm = "
<script type='text/javascript'>
 //<![CDATA[
  function getRadiobutton () {
  var radioButtons = document.getElementsByTagName('input');
  var param = '';
  for (var counter=0; counter < radioButtons.length; counter++) {
    if (radioButtons[counter].type.toUpperCase()=='RADIO' && radioButtons[counter].checked == true && radioButtons[counter].name == 'selected_dir')
    param = radioButtons[counter].value;
    }
  return param;
    } 

  function move_selected_ilm () {
  var msgAnswer;
  if (getRadiobutton() != '') {
    msgAnswer = confirm('" . get_string('question_move_dir', 'iassign') . "');
    if (msgAnswer)
      window.location='" . $CFG->wwwroot . "/mod/iassign/ilm_manager.php?from=" . $this->from . "&id=" . $this->id . "&action=move&ilmid=" . $ilmid . "&dirid=" . $dirid . "&files_id=" .
        optional_param('files_id', '', PARAM_TEXT) . "&dir_move='+getRadiobutton();
    }
  else
    alert('" . get_string('error_dir_not_selected_to_move', 'iassign') . "');
    }

  function cancel_selected_ilm () {
    window.location='" . $CFG->wwwroot . "/mod/iassign/ilm_manager.php?from=" . $this->from . "&id=" . $this->id . "&ilmid=" . $ilmid . "&dirid=" . $dirid . "';
    }
   //]]>
</script>\n";

    $title = get_string('move_files', 'iassign');
    $PAGE->set_title($title);
    $PAGE->set_pagelayout('base');
    print $OUTPUT->header();
    print $OUTPUT->heading($title);
    $dir_paths = array();
    print $OUTPUT->box_start();
    print "<center>";
    foreach ($files_id as $file_id) {
      $file = $fs->get_file_by_id($file_id);
      if ($file) {
        if (!$file->is_directory())
          print "<p>" . iassign_icons::insert('file') . "&nbsp;" . $file->get_filepath() . $file->get_filename() . "</p>";
        else {
          print "<p>" . iassign_icons::insert('dir') . "&nbsp;" . $file->get_filepath() . "</p>";
          array_push($dir_paths, $file->get_filepath());
          }
        }
      }
    print $OUTPUT->heading(get_string('select_move_ilm', 'iassign'), 3, 'move', 'move_files');
    if ($dir_base != '/') {
      $check_select = "<input name='selected_dir' type='radio' value='" . $this->get_dir_ilm('dir_home') . "'/>";
      print $check_select . "&nbsp;" . iassign_icons::insert('dir') . "&nbsp;/<br>";
      }
    $files_tree = $fs->get_directory_files($context->id, 'mod_iassign', 'activity', 0, '/', true, true, 'filepath');
    foreach ($files_tree as $file) {
      if ($file->is_directory() && $file->get_filepath() != $dir_base) {
        $is_parent = false;
        foreach ($dir_paths as $dir) {
          $path = explode("/", $dir);
          array_pop($path);
          $path[count($path) - 1] = '';
          $path = implode("/", $path);
          $is_parent |= (strpos($file->get_filepath(), $dir) === false ? false : true);
          $is_parent |= ($file->get_filepath() != $path ? false : true);
          }
        if ($is_parent == false) {
          $check_select = "<input name='selected_dir' type='radio' value='" . $file->get_id() . "'/>";
          print "<p>" . $check_select . "&nbsp;" . iassign_icons::insert('dir') . "&nbsp;" . $file->get_filepath() . "</p>";
          }
        }
      }
    print "<p><input type='button' value='" . get_string('ok') . "' onclick='move_selected_ilm();'/>&nbsp;&nbsp;&nbsp;&nbsp;";
    print "<input type='button' value='" . get_string('cancel') . "' onclick='cancel_selected_ilm();'/></p>";
    print "</center>";
    print $OUTPUT->box_end();
    print $OUTPUT->footer();
    print $code_javascript_ilm;
    die;
    }

  /// Function for move files for an dir
  function move_files_ilm () {
    $fs = get_file_storage();
    $context = context_course::instance($this->id);

    $ilmid = optional_param('ilmid', NULL, PARAM_INT);
    $dirid = $this->get_dir_ilm('dirid');
    $dir_move = $fs->get_file_by_id(optional_param('dir_move', 0, PARAM_INT));
    $files_id = explode(",", optional_param('files_id', '', PARAM_TEXT));

    foreach ($files_id as $file_id) {
      $file = $fs->get_file_by_id($file_id);
      if ($file) {
        if ($file->is_directory()) {
          $pathname = explode("/", $file->get_filepath());
          $files_move_path = $fs->get_directory_files($context->id, 'mod_iassign', 'activity', 0, $file->get_filepath(), true, true);
          foreach ($files_move_path as $value) {
            $path_move = $dir_move->get_filepath() . $pathname[count($pathname) - 2] . '/' . str_replace($file->get_filepath(), '', $value->get_filepath());
            $value->rename($path_move, $value->get_filename());
            //echo($value->get_filepath().$value->get_filename()." - $path_move".$value->get_filename()."<br>");
            }
          $path_move = $dir_move->get_filepath() . $pathname[count($pathname) - 2] . '/';
          //echo($file->get_filepath().$file->get_filename()." - $path_move".$file->get_filename()."<br>");
          $file->rename($path_move, $file->get_filename());
        } else {
          //echo($file->get_filepath().$file->get_filename()." -> ".$dir_move->get_filepath().$file->get_filename()."<br>");
          $file->rename($dir_move->get_filepath(), $file->get_filename());
          }
        }
      }
    //die;

    redirect(new moodle_url($this->url . '&ilmid=' . $ilmid . '&dirid=' . $dirid));
    die();
    } // function move_files_ilm()


  /// Function for recover files in use on all activities of a course
  function recover_files_ilm() {
    global $DB, $USER;

    $fs = get_file_storage();
    $courseid = optional_param('id', NULL, PARAM_INT);
    $dirid = $this->get_dir_ilm('dirid');
    $ilmid = optional_param('ilmid', NULL, PARAM_INT);
    $contextfile = context_course::instance($this->id);

    $iassigns = $DB->get_records("iassign", array("course" => $courseid));
    foreach ($iassigns as $iassign) {
      $iassign_statement_activity_list = $DB->get_records("iassign_statement", array("iassignid" => $iassign->id));
      foreach ($iassign_statement_activity_list as $iassign_statement_activity_item) {
        $cm = get_coursemodule_from_instance("iassign", $iassign->id, $courseid);
        $context = context_module::instance($cm->id);
        $files = $fs->get_area_files($context->id, 'mod_iassign', 'exercise', $iassign_statement_activity_item->file);
        if ($files) {
          foreach ($files as $value) {
            $extension = explode(".", $value->get_filename());
            if (!$value->is_directory()) {
              $fileinfo = array('contextid' => $contextfile->id,
                 'component' => 'mod_iassign',
                 'filearea' => 'activity',
                 'itemid' => 0,
                 'filepath' => $this->get_dir_ilm('dir_base'),
                 'userid' => $USER->id,
                 'author' => $USER->firstname . ' ' . $USER->lastname,
                 'license' => 'allrightsreserved',
                 'timecreated' => time(),
                 'filename' => $iassign_statement_activity_item->name . "." . $extension[1]); // any filename
              $newfile = $fs->create_file_from_string($fileinfo, $value->get_content());
              }
            }
          } // if ($files)
        } // foreach ($iassign_statement_activity_list as $iassign_statement_activity_item)
      } // foreach ($iassigns as $iassign)

    redirect(new moodle_url($this->url . '&dirid=' . $dirid . '&ilmid=' . $ilmid));
    die();
    } // function recover_files_ilm()


  /// List iassign files from course directory
  //  @calledby ilm_manager.php : $ilm_manager_instance->view_files_ilm($iassign_ilm->extension);
  function view_files_ilm($iassign_ilm_class, $extension) {
    global $CFG, $DB, $USER, $OUTPUT;
    $fs = get_file_storage();
    $context = context_course::instance($this->id);
    $ilmid = optional_param('ilmid', NULL, PARAM_INT);
    $dirid = $this->get_dir_ilm('dirid');
    $dir_base = $this->get_dir_ilm('dir_base');

    $files_course = $fs->get_directory_files($context->id, 'mod_iassign', 'activity', 0, $dir_base, false, true, 'filename');
    $files_array = '';
    foreach ($files_course as $value) {
      if (!$value->is_directory())
        $files_array .= "'" . $value->get_filename() . "',";
      }
    $files_array .= "''";
    $error_files_exists = get_string('error_file_exists', 'iassign');

    $dirs_array = '';
    foreach ($files_course as $value) {
      if ($value->is_directory()) {
        $pathname = explode("/", substr($value->get_filepath(), 0, strlen($value->get_filepath()) - 1));
        $dirs_array .= "'" . $pathname[count($pathname) - 1] . "',";
        }
      }
    $dirs_array .= "''";
    $error_dir_exists = get_string('error_dir_exists', 'iassign');

    // TODO Rever o preview pois só deixar ver uma vez.
    $code_javascript_ilm = "
<script type='text/javascript'>
 //<![CDATA[
   function preview_ilm (fileid, ilmid) {
   var param = '" . $CFG->wwwroot . "/mod/iassign/ilm_manager.php?from=" . $this->from . "&id=" . $this->id . "&action=preview&fileid='+fileid+'&ilmid='+ilmid;
   var preview_ilm=window.open(param,'','menubar=0,location=0,scrollbars,status,resizable,width=900 height=700');
     }

   function update_ilm (ilmid, fileid) {
     window.location='" . $CFG->wwwroot . "/mod/iassign/ilm_manager.php?from=" . $this->from . "&id=" . $this->id . "&action=update&ilmid='+ilmid+'&dirid=" . $dirid . "&fileid='+fileid;
     }

   function delete_ilm (ilmid, fileid) {
     var msgAnswer;
     msgAnswer = confirm('" . get_string('delete_file', 'iassign') . "');
     if (msgAnswer) {
       window.location='" . $CFG->wwwroot . "/mod/iassign/ilm_manager.php?from=" . $this->from . "&id=" . $this->id . "&action=delete&ilmid='+ilmid+'&dirid=" . $dirid . "&fileid='+fileid;
       }
     }

   function delete_selected_ilm () {
     var msgAnswer;
     var param = getCheckbox();
     if (param.join() != '') {
       msgAnswer = confirm('" . get_string('delete_files', 'iassign') . "');
       if (msgAnswer)
         window.location='" . $CFG->wwwroot . "/mod/iassign/ilm_manager.php?from=" . $this->from . "&id=" . $this->id . "&action=selected_delete&dirid=" . $dirid . "&files_id='+param.join();
       }
     else
       alert('" . get_string('error_file_not_selected_to_delete', 'iassign') . "');
     }

   function add_ilm_iassign (ilmid, filename, fileid) {
     window.location='" . $CFG->wwwroot . "/mod/iassign/ilm_manager.php?from=" . $this->from . "&id=" . $this->id . "&action=addilm&ilmid='+ilmid+'&fileid='+fileid+'&filename='+filename;
     }

   function duplicate_ilm (ilmid, filename, fileid) {
     var filenamecopy;
     var i;
     var files = new Array($files_array);
     do {
      filenamecopy = prompt ('" . get_string('duplicate_file', 'iassign') . "',filename);
       } while (filenamecopy == '');
     if (filenamecopy == null)
       return false;\n
     else {
       for (i=0;i<files.length;i++) {
         if (files[i]==filenamecopy) {
           alert('$error_files_exists');
           return false;\n
           }
         }\n" .
       // @see: function 'duplicate_file_ilm()' above
       "
       window.location='" . $CFG->wwwroot . "/mod/iassign/ilm_manager.php?" .
         "from=" . $this->from . "&id=" . $this->id . "&action=duplicate&ilmid=' + ilmid + '&dirid=" . $dirid . "&fileid=' + fileid + '&filename=' + filenamecopy;
       } // else
     }

   function rename_ilm (ilmid, filename, fileid) {
     var filenamecopy;
     var i;
     var files = new Array($files_array);
     do {
      filenamecopy = prompt('" . get_string('rename_file', 'iassign') . "',filename);
     } while (filenamecopy == '');
     if (filenamecopy == null)
       return false;
     else {
       for (i=0;i<files.length;i++) {
         if (files[i]==filenamecopy) {
           alert('$error_files_exists');
           return false;
           }
         }
       window.location='" . $CFG->wwwroot . "/mod/iassign/ilm_manager.php?from=" . $this->from . "&id=" . $this->id . "&action=rename&ilmid='+ilmid+'&dirid=" . $dirid . "&fileid='+fileid+'&filename='+filenamecopy;
       }
     }
   
   function export_files_ilm () {
     var param = getCheckbox();
     if (param.join() != '')
       window.location='" . $CFG->wwwroot . "/mod/iassign/ilm_manager.php?from=" . $this->from . "&id=" . $this->id . "&action=export&dirid=" . $dirid . "&files_id='+param.join();
     else
       alert('" . get_string('error_file_not_selected_to_export', 'iassign') . "');
     }

  function select_all_ilm () {
    var checkBoxes = document.getElementsByTagName('input');
    var selectAll = document.getElementById('select_all');
    for (var counter=0; counter < checkBoxes.length; counter++) {
      if (checkBoxes[counter].type.toUpperCase()=='CHECKBOX' && checkBoxes[counter].name == 'selected_file')
        checkBoxes[counter].checked = selectAll.checked;
      }
    }

  function getCheckbox () {
    var checkBoxes = document.getElementsByTagName('input');
    var param = new Array();
    for (var counter=0; counter < checkBoxes.length; counter++) {
    if (checkBoxes[counter].type.toUpperCase()=='CHECKBOX' && checkBoxes[counter].checked == true && checkBoxes[counter].name == 'selected_file')
      param.push(checkBoxes[counter].value);
      }
    return param;
    }

  function new_dir_ilm () {
    var dirname = '';
    var i;
    var dirs = new Array($dirs_array);
    do {
     var dirname = prompt ('" . get_string('question_new_dir', 'iassign') . "', '');
    }  while (dirname == '');
    if (dirname == null)
      return false;\n
    else {
      for (i=0;i<dirs.length;i++) {
        if (dirs[i]==dirname) {
          alert('$error_dir_exists');
          return false;\n
          }
        }
      window.location='" . $CFG->wwwroot . "/mod/iassign/ilm_manager.php?from=" . $this->from . "&id=" . $this->id . "&action=new_dir&ilmid=" . $ilmid . "&dirid=" . $dirid . "&dirname='+dirname;
      }
    }

   function delete_dir_ilm (ilmid, dirid) {
     var msgAnswer;
     msgAnswer = confirm('" . get_string('question_delete_dir', 'iassign') . "');
     if (msgAnswer) {
       window.location='" . $CFG->wwwroot . "/mod/iassign/ilm_manager.php?from=" . $this->from . "&id=" . $this->id . "&action=delete_dir&ilmid='+ilmid+'&dirid='+dirid;
       }
     }

   function rename_dir_ilm (ilmid, dirname, dirid) {
     var dirnamecopy;
     var i;
     var dirs = new Array($dirs_array);
     do {
       dirnamecopy = prompt ('" . get_string('question_rename_dir', 'iassign') . "',dirname);
       } while (dirnamecopy == '');
     if (dirnamecopy == null)
       return false;\n
     else {
       for (i=0;i<dirs.length;i++) {
         if (dirs[i]==dirnamecopy) {
           alert('$error_dir_exists');
           return false;\n
           }
         }
       window.location='" . $CFG->wwwroot . "/mod/iassign/ilm_manager.php?from=" . $this->from . "&id=" . $this->id . "&action=rename_dir&ilmid='+ilmid+'&dirid='+dirid+'&dirname='+dirnamecopy;
       }
     }

   function move_selected_ilm (ilmid) {
   var param = getCheckbox();
   if (param.join() != '')
     window.location='" . $CFG->wwwroot . "/mod/iassign/ilm_manager.php?from=" . $this->from . "&id=" . $this->id . "&action=selected_move&ilmid='+ilmid+'&dirid=" . $dirid . "&files_id='+param.join();
   else
     alert('" . get_string('error_file_not_selected_to_move', 'iassign') . "');
     }

   function recover_files_ilm () {
     var msgAnswer;
     msgAnswer = confirm('" . get_string('question_recover_files_ilm', 'iassign') . "');
     if (msgAnswer) {
       window.location='" . $CFG->wwwroot . "/mod/iassign/ilm_manager.php?from=" . $this->from . "&id=" . $this->id . "&action=recover&ilmid=" . $ilmid . "&dirid=" . $dirid . "';
       }
    }

    window.onload = function() {
      
      var xPosition = 0;
      var yPosition = 0;

      element=document.getElementById('new_file');

      while(element) {
          xPosition += (element.offsetLeft - element.scrollLeft + element.clientLeft);
          yPosition += (element.offsetTop - element.scrollTop + element.clientTop);
          element = element.offsetParent;
      }

      console.log(xPosition, yPosition);

      document.getElementById('new_file').style.top = '' + (yPosition - 400) + 'px';
      document.getElementById('new_file').style.right = '' + 0 + 'px';

      console.log(yPosition - 400);

      location.hash = '#new_file';
    };

   //]]>
</script>\n";

    $output = "";
    $select_all = "";
    $count_files = 0;

    $extensions_allow = array();
    $iassign_ilm = $DB->get_records('iassign_ilm', array("enable" => 1)); // extensions for all iLM...
    foreach ($iassign_ilm as $item_iassign_ilm)
      $extensions_allow = array_merge($extensions_allow, explode(",", $item_iassign_ilm->extension));

    foreach ($files_course as $value) {

      $filename = $value->get_filename();
      $filepath = $value->get_filepath();
      $pathname = explode("/", substr($filepath, 0, strlen($filepath) - 1));
      $pathname = $pathname[count($pathname) - 1];
      $fileid = $value->get_id();
      $tmp = explode(".", $filename);
      $filetype = $tmp[1];
      $author = $value->get_author();
      $timemodified = date("d/m/Y H:i:s", $value->get_timemodified());
      $timecreated = date("d/m/Y H:i:s", $value->get_timecreated());
      $extensions = explode(",", $extension);

      if (in_array(strtolower($filetype), $extensions) || $value->is_directory() || $this->from == 'block' || $this->from == 'tinymce' || $this->from == 'atto') {

        $count_files ++;

        // buscar fileid nas tabelas do iassign
        $list_filein_use = "";
        $iassign_statement_activity_list = $DB->get_records("iassign_statement", array("file" => $fileid));
        if ($iassign_statement_activity_list) {
            foreach ($iassign_statement_activity_list as $iassign_statement_activity_item) {
                $list_filein_use .= $iassign_statement_activity_item->name . "</br>\n";
              }
          }

        $iassign_ilm = $DB->get_record("iassign_ilm", array('extension' => $filetype, 'parent' => '0', 'enable' => '1'));
        if (!$iassign_ilm) {
            $iassign_ilm = new stdClass();
            $iassign_ilm->id = $ilmid;
          }

        $url = "{$CFG->wwwroot}/pluginfile.php/{$value->get_contextid()}/mod_iassign/activity";
        $fileurl = $url . '/' . $value->get_itemid() . $filepath . $filename;
        $dirurl = new moodle_url($this->url) . '&ilmid=' . $iassign_ilm->id . '&dirid=' . $fileid;

        $straux = $CFG->wwwroot . "/mod/iassign/ilm_manager.php?from=" . $this->from . "&id=" . $this->id . "&fileid=" . $fileid . "&";
        $link_add_ilm_iassign = "&nbsp;&nbsp;<a href='" . $straux . "action=addilm&filename=$filename&nbsp;&nbsp;&nbsp;'>" . iassign_icons::insert('add_ilm_iassign') . "</a>\n";
        $link_add_ilm_tinymce = "&nbsp;&nbsp;<a href='" . $straux . "action=tinymceilm'>" . iassign_icons::insert('add_ilm_iassign') . "</a>\n";
        $link_add_ilm_atto = "&nbsp;&nbsp;<a href='" . $straux . "action=attoilm'>" . iassign_icons::insert('add_ilm_iassign') . "</a>\n";

        $check_select = "";
        $link_rename = "";
        $link_delete = "";
        $link_duplicate = "&nbsp;&nbsp;<a href='#' onclick='duplicate_ilm(\"$iassign_ilm->id\", \"$filename\"," . $fileid . ");'>" . iassign_icons::insert('duplicate_iassign') . "</a>\n";
        $link_edit = "&nbsp;&nbsp;" . iassign_icons::insert('no_edit_iassign');
        $link_filter = "&nbsp;&nbsp;<a href='#' onclick='preview_ilm(" . $fileid . "," . $ilmid . ");'>" . iassign_icons::insert('preview_iassign') . "</a>\n";

        $link_duplicate = "&nbsp;&nbsp;<a href='#' onclick='duplicate_ilm(\"$iassign_ilm->id\", \"$filename\"," . $fileid . ");'>" .
                iassign_icons::insert('duplicate_iassign') . "</a>\n";

        $link_edit = "&nbsp;&nbsp;" . iassign_icons::insert('no_edit_iassign');
        $link_filter = "&nbsp;&nbsp;<a href='#' onclick='preview_ilm(" . $fileid . "," . $ilmid . ");'>" . iassign_icons::insert('preview_iassign') . "</a>\n";

        if ($value->get_userid() == $USER->id) {
            if ($iassign_statement_activity_list) {
                $check_select = "";
                $link_edit = iassign_icons::insert('edit_iassign_disable');
                $link_delete = "&nbsp;&nbsp;" . iassign_icons::insert('delete_iassign_disable');
                $link_rename = "";
              } else {
                $check_select = "<input name='selected_file' type='checkbox' value='$fileid'/>\n";
                $link_edit = "&nbsp;&nbsp;<a href='#' onclick='update_ilm(\"$iassign_ilm->id\", $fileid)'>" . iassign_icons::insert('edit_iassign') . "</a>\n";
                $link_delete = "&nbsp;&nbsp;<a href='#' onclick='delete_ilm(\"$iassign_ilm->id\", $fileid);'>" . iassign_icons::insert('delete_iassign') . "</a>\n";
                $link_rename = "&nbsp;&nbsp;<a href='#' onclick='rename_ilm(\"$iassign_ilm->id\", \"$filename\"," . $fileid . ");'>" . iassign_icons::insert('rename_iassign') . "</a>\n";
              }
          }
        if (!in_array($filetype, $extensions_allow)) {
            $link_edit = "";
            $link_add_ilm_iassign = "";
            $link_add_ilm_tinymce = "";
            $link_add_ilm_atto = "";
            $link_filter = "";
          }

        if ($value->is_directory()) {
            $link_delete = "&nbsp;&nbsp;<a href='#' onclick='delete_dir_ilm(\"$iassign_ilm->id\", $fileid);'>" . iassign_icons::insert('delete_dir') . "</a>\n";
            $link_rename = "&nbsp;&nbsp;<a href='#' onclick='rename_dir_ilm(\"$iassign_ilm->id\", \"" . $pathname . "\"," . $fileid . ");'>" . iassign_icons::insert('rename_dir') . "</a>\n";
            $output .= "<tr><td>$check_select$link_rename$link_delete</td>
   <td><a href='$dirurl' title='" . get_string('dir', 'iassign') . $pathname . "'>" . iassign_icons::insert('dir') . '&nbsp;' . $pathname . "</a></td>
   <td><center>$author</center></td>
   <td><center>$timecreated</center></td>
   <td><center>$timemodified</center></td></tr>\n";
        } else if ($this->from == 'iassign') {

          $new_id = "";
          $new_class = "";
          if ($filename == $_SESSION['file_name']) { $new_class = "<div id='new_file' style='position: absolute;'></div>"; unset($_SESSION['file_name']);
          $new_id = "id='id_new_blink' style='background-color: hsl(244,61%,90%);'"; }

            $output .= "<tr $new_id><td>$new_class $check_select$link_rename$link_delete$link_duplicate$link_edit$link_filter$link_add_ilm_iassign</td>
   <td><a href='$fileurl' title='" . get_string('download_file', 'iassign') . "$filename'>$filename</a></td>
   <td><center>$author</center></td>
   <td><center>$timecreated</center></td>
   <td><center>$timemodified</center></td></tr>\n";
        } else if ($this->from == 'block') {
            $output .= "<tr><td>$check_select$link_rename$link_delete$link_duplicate$link_edit$link_filter</td>
   <td><a href='$fileurl' title='" . get_string('download_file', 'iassign') . "$filename'>$filename</a></td>
   <td><center>$author</center></td>
   <td><center>$timecreated</center></td>
   <td><center>$timemodified</center></td></tr>\n";
        } else if ($this->from == 'tinymce') {
            $output .= "<tr><td>$check_select$link_rename$link_delete$link_duplicate$link_edit$link_filter$link_add_ilm_tinymce</td>
   <td><a href='$fileurl' title='" . get_string('download_file', 'iassign') . "$filename'>$filename</a></td>
   <td><center>$author</center></td>
   <td><center>$timecreated</center></td>
   <td><center>$timemodified</center></td></tr>\n";
        } else if ($this->from == 'atto') {
            $output .= "<tr><td>$check_select$link_rename$link_delete$link_duplicate$link_edit$link_filter$link_add_ilm_atto</td>
   <td><a href='$fileurl' title='" . get_string('download_file', 'iassign') . "$filename'>$filename</a></td>
   <td><center>$author</center></td>
   <td><center>$timecreated</center></td>
   <td><center>$timemodified</center></td></tr>\n";
        }
        }
      }
    $basename = explode("/", substr($dir_base, 0, strlen($dir_base) - 1));
    $dir_base = "";
    $header = "";
    foreach ($basename as $value) {
      $dir_base .= "$value/";
      $dir_id = $fs->get_file($context->id, 'mod_iassign', 'activity', 0, $dir_base, '.');
      if ($dir_id) {
        if ($value == "") {
            $fileurl = new moodle_url($this->url) . '&dirid=' . $dir_id->get_id() . '&ilmid=' . $ilmid;
            $header .= "&nbsp;<a href='$fileurl' title='" . get_string('dir', 'iassign') . "Home'>Home</a>\n";
          } else {
            $fileurl = new moodle_url($this->url) . '&dirid=' . $dir_id->get_id() . '&ilmid=' . $ilmid;
            $header .= "&nbsp;" . $OUTPUT->rarrow() . "&nbsp;<a href='$fileurl' title='" . get_string('dir', 'iassign') . "$dir_base'>$value</a>\n";
          }
        }
      }

    $html = $OUTPUT->heading(iassign_icons::insert('open_dir') . $header, 2, 'dirtitle', 'iassign');
    $select_all = "<input id='select_all' type='checkbox' onclick='select_all_ilm();'/>\n";

    $html .= "
  <table id='outlinetable' class='generaltable boxaligncenter' cellpadding='5' width='100%'>
  <tr><th class='header c1' width='20%'>$select_all " . get_string('functions', 'iassign') . "</th>
    <th class='header c1' width='*'>" . get_string('file', 'iassign') . "</th>
    <th class='header c1' width='10%'>" . get_string('author', 'iassign') . "</th>
    <th class='header c1' width='10%'>" . get_string('file_created', 'iassign') . "</th>
    <th class='header c1' width='10%'>" . get_string('file_modified', 'iassign') . "</th>\n " . $output . "
  </table>\n";
    $html .= "<form id='form_buttons' method='post' enctype='multipart/form-data'>\n";
    $html .= "<table width='100%'><tr>\n";
    $html .= "<td width='80%'><input type='button' value='" . get_string('new_dir', 'iassign') . "' onClick='new_dir_ilm();'>\n";
    if ($count_files != 0) {
      $html .= "&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<input type='button' value='" . get_string('file_ilm_move', 'iassign') . "' onClick='move_selected_ilm(\"$iassign_ilm->id\");'/>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;\n";
      $html .= "<input type='button' value='" . get_string('file_ilm_delete', 'iassign') . "' onClick='delete_selected_ilm(\"$iassign_ilm->id\");'>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;\n";
      $html .= "<input type='button' value='" . get_string('file_ilm_export', 'iassign') . "' onClick='export_files_ilm();'>\n";
      }
    $html .= "</td><td  width='100%' align='right'><input type='button' value='" . get_string('file_ilm_recover', 'iassign') . "' onClick='recover_files_ilm();'>\n";
    $html .= $OUTPUT->help_icon('file_ilm_recover', 'iassign') . "</td></tr></table>\n";
    $html .= "</form>\n";

    print $code_javascript_ilm;

    print $html;
    } // function view_files_ilm($extension)

  } // class ilm_manager


/// Class to insert of icons
class iassign_icons {

  static function insert ($icon) {
    global $CFG;
    $string = '<img src="' . $CFG->wwwroot . '/mod/iassign/icon/' . $icon . '.gif" title="' . get_string($icon, 'iassign') . '" alt="' . get_string($icon, 'iassign') . '"/>'; // "\n"
    return $string;
    }

  }

/// Class with util functions for plugin manage
class iassign_utils {

  /// Function to return the filename extension
  //  @param  string The $filename as an string
  //  @return string Return an string (last group after '.')
  static function filename_extension ($filename) {
    if ($filename == null || $filename == '') {
      return null;
      }
    $itens = explode('.', $filename);
    $num = count($itens);
    if ($num < 2) {
      return null;
      }
    return $itens[$num - 1];
    }

  /// Function for format filename remove special caracters
  //  @param string $text String for clean
  //  @param boolean $is_lowercase Boolean for apply lowercase in filename
  //  @return string Return an string in clean format
  static function format_filename ($text) {
    if ($text != '.') {
      $text = htmlspecialchars(urldecode($text));
      $array1 = array("á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ç", "Á", "À", "Â", "Ã", "Ä", "É", "È", "Ê",
        "Ë", "Í", "Ì", "Î", "Ï", "Ó", "Ò", "Ô", "Õ", "Ö", "Ú", "Ù", "Û", "Ü", "Ç", "@", " ", "!", "?");
      $array2 = array("a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "c", "A", "A", "A", "A", "A", "E", "E", "E",
        "E", "I", "I", "I", "I", "O", "O", "O", "O", "O", "U", "U", "U", "U", "C", "-", "_", "", "");

      // E.g. text = "exerc_midpoint.course.geo"
      //              0123456789012345678901234
      $index_lastposition = strrpos($text, "."); // Find the position of the last occurrence of a substring in a string (21)
      $text2 = substr($text, 0, $index_lastposition); // Erase the final point and its extension ("exerc_midpoint.course")
      $ext = substr($text, $index_lastposition); // Point with extention (".geo")
      $text = str_replace(".", "", $text2) . $ext; // Clear any other point and redefine the name ("exerc_midpoint.course.geo")
      $text = str_replace($array1, $array2, $text); // Replace any letter with accent mark under the UTF8 format
      //$ext = strrpos($text, ".");
      //$text = str_replace(".", "", substr($text, 0, $ext)).substr($text, $ext);
      //$text = str_replace($array1, $array2, $text);
      }
    return $text;
    }

  /// Function for format pathname remove special caracters
  //  @param string $text String for clean
  //  @param boolean $is_lowercase Boolean for apply lowercase in pathname
  //  @return string Return an string in clean format
  static function format_pathname ($text, $is_lowercase = true) {
    $array1 = array("á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ç", "Á", "À", "Â", "Ã", "Ä", "É", "È", "Ê", "Ë", "Í", "Ì", "Î", "Ï", "Ó", "Ò", "Ô", "Õ", "Ö", "Ú", "Ù", "Û", "Ü", "Ç", "@", " ", "!", "?", ".");
    $array2 = array("a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "c", "A", "A", "A", "A", "A", "E", "E", "E", "E", "I", "I", "I", "I", "O", "O", "O", "O", "O", "U", "U", "U", "U", "C", "-", "_", "", "", "");
    $text = str_replace($array1, $array2, $text);
    if ($is_lowercase)
      $text = strtolower($text);
    return $text;
    }

  /// Function for insert version in the filename
  //  @param string $filename Name of file
  //  @return string Return the filename with version
  static function version_filename ($filename) {
    $array_filename = explode('.', $filename);
    if (count($array_filename) > 1)
      $filename = $array_filename[0] . '-' . date("Ymd-His") . '.' . $array_filename[1];
    else
      $filename = $array_filename[0] . '-' . date("Ymd-His");
    return $filename;
    }

  //TODO Retirar quando atualizar todo os iassign que estão com a tag &lt;ia_uc&gt;
  static function remove_code_message ($string) {
    $array = explode("&lt;ia_uc&gt;", $string);
    return $array[0];
    }

  }

// class iassign_utils
/// Class with language functions for plugin manage

class iassign_language {

  /// Function for return text in language or get default language (en)
  //  @param string $lang Code of language
  //  @param string $description JSON text content all languages
  //  @return string Return an string in the language selected
  static function get_description_lang ($lang, $descriptions) {
    $description_lang = "";
    $description = json_decode($descriptions);
    if ($description == null) {
      $description_lang = $descriptions;
      } else {
      if (isset($description->{$lang}))
        $description_lang = $description->{$lang};
      else
      if (isset($description->en)) //MOOC2014
        $description_lang = $description->en;
      else //MOOC2014
        $description_lang = "en"; //MOOC2014
      }
    return $description_lang;
    }

  /// Function for return all language supported by iLM
  //  @param string $descriptions JSON text content all languages
  //  @return string Return as string with all languages
  static function get_all_lang ($descriptions) {
    $langs = "";
    $description = json_decode($descriptions);
    if ($description) {
      foreach ($description as $key => $value) {
        $langs .= $key . " ; ";
        }
      $langs = substr($langs, 0, strlen($langs) - 3);
      }
    return $langs;
    }

  /// Function for convert json in xml //MOOC2014
  //  @param string $json JSON text
  //  @return string Return as string with xml tags
  static function json_to_xml ($json) {
    $xml = "";
    $json = json_decode($json);
    foreach ($json as $key => $value) {
      $xml .= "\n    <" . $key . ">" . $value . "</" . $key . ">";
      }
    return $xml;
    }

  }

// class iassign_language
/// Class with log functions for plugin manage.

class iassign_log {

  /// Function for insert log event
  //  @param string $action Code action of event
  //  @param string $information Text for describe action of event
  //  @param int $cmid Id of context module
  //  @param int $ilmid Id of iLM
  static function add_log ($action, $information = "", $cmid = 0, $ilmid = 0) {
    global $COURSE, $CFG, $USER, $DB;

    $newentry = new stdClass();
    $newentry->time = time();
    $newentry->userid = $USER->id;
    $newentry->ip = $_SERVER['REMOTE_ADDR'];
    $newentry->course = $COURSE->id;
    $newentry->cmid = $cmid;
    $newentry->ilmid = $ilmid;
    $newentry->action = $action;
    $newentry->info = $information;
    $newentry->language = current_language();
    $newentry->user_agent = $_SERVER['HTTP_USER_AGENT'];
    if (ini_get("browscap") && function_exists('get_browse')) {
      $browser = get_browse(null, true);
      $newentry->javascript = $browser['javascript'];
      $newentry->java = $browser['javaapplets'];
      }
    if (!$newentry->id = $DB->insert_record("iassign_log", $newentry))
      print_error('error_add_log', 'iassign');
    }

  }

// class iassign_log

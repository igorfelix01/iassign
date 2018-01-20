<?php

/**
 * Form to add and edit interactive activities
 * 
 * 
 * Release Notes:
 * - v 1.5 2013/09/19
 *   + Insert function for validation form (mod_iassign_form::validation).
 *   + Fix bugs in download exercise file.
 * - v 1.4 2013/08/21
 *   + Change title link with message for get file for donwload file.
 * - v 1.3 2013/08/15
 *   + Change view file for allow download file.
 * - v 1.2 2013/08/01
 *   + Fix error in sql query for var $igeom.
 * - v 1.1 2013/07/12
 *   + Fix error messages of 'setType' in debug mode for hidden fields.
 * 
 * @author Patricia Alves Rodrigues
 * @author Leônidas O. Brandão
 * @version v 1.5 2013/09/19
 * @package mod_iassign
 * @since 2010/09/27
 * @copyright iMatica (<a href="http://www.matematica.br">iMath</a>) - Computer Science Dep. of IME-USP (Brazil)
 * 
 * <b>License</b> 
 *  - http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


// Moodle core defines constant MOODLE_INTERNAL which shall be used to make sure that the script is included and not called directly.
if (!defined('MOODLE_INTERNAL')) {
  die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
  }

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/course/moodleform_mod.php');
require_once($CFG->dirroot . '/mod/iassign/lib.php');


/// This class create form based moodleform.
//  @see moodleform
class mod_iassign_form extends moodleform {

  /// Add elements to form
  function definition () {
    global $CFG, $COURSE, $USER, $DB;
    $mform = & $this->_form;
    $instance = $this->_customdata;

    // iGeom has special parameter 'script'
    $params = array('name' => '%iGeom%', 'parent' => 0);
    $igeom = $DB->get_records_sql(
      "SELECT s.id, s.name, s.parent FROM {iassign_ilm} s
       WHERE s.name LIKE :name AND s.parent = :parent", $params);
    $id = $COURSE->cm;
    foreach ($igeom as $item)
      $idigeom = $item->id; // get the last id of iGeom

    $all_ilm = $DB->get_records('iassign_ilm', array('enable' => 1)); // or use ./lib.php function: $all_ilm = search_iLM(1);
    $iassigns = $DB->get_records('iassign_statement', array('iassignid' => $COURSE->iassignid));

    if ($all_ilm) {
      $ids = "";
      $names = "";
      $evaluates = "";
      foreach ($all_ilm as $one_ilm) {
        $ids .= "'" . $one_ilm->id . "',";
        $names .= "'" . $one_ilm->name . "',";
        $evaluates .= "'" . $one_ilm->evaluate . "',";
        }
      $ids .= "'0'";
      $evaluates .= "'0'";
      }
    $name_iassigns = "";
    if ($iassigns) {
      foreach ($iassigns as $iassign) {
        $name_iassigns .= "'" . $iassign->name . "',";
        }
      }
    $name_iassigns .= "''";

    $error_name = get_string('error_iassign_name', 'iassign');

    // @todo Código Javascript, verificar alternativa.
    $code_javascript = "
  <script type='text/javascript'>
  //<![CDATA[

   var i;
   var ids = new Array($ids);
   var evaluates = new Array($evaluates);

   document.forms['mform1'].filename.disabled=1;  

   if (document.forms['mform1'].type_iassign.value==1) {
     document.forms['mform1'].grade.style.display='none';
     document.forms['mform1'].max_experiment.style.display='none';
   } else {
     document.forms['mform1'].grade.style.display='block';
     document.forms['mform1'].max_experiment.style.display='block';
     }

  for (i=0;i<ids.length;i++) {
    if (ids[i]==document.forms['mform1'].iassign_ilmid.value && evaluates[i]==0) {
      document.forms['mform1'].automatic_evaluate.style.display='none';
      document.forms['mform1'].show_answer.style.display='none';
      //document.forms['mform1'].automatic_evaluate.disabled=1;
      // document.forms['mform1'].show_answer.disabled=1;
      }
    }

  if (document.forms['mform1'].iassign_ilmid.value==" . $idigeom .") { // iGeom has special parameter 'script'
    document.forms['mform1'].special_param1.style.display='block';
    document.forms['mform1'].special_param1.disabled=0;
    }
  else {
    document.forms['mform1'].special_param1.style.display='none';
    document.forms['mform1'].special_param1.value=0;
    document.forms['mform1'].special_param1.disabled=1;
    }

  function confirm_name (name) {
    var i;
    var names = new Array($name_iassigns);
    for (i=0;i<names.length;i++) {
       if (names[i]==name)
         alert('" . $error_name . "');
       }
    }

  function config_ilm (id) {
    if (id==$idigeom) {
      document.forms['mform1'].special_param1.style.display='block';
      document.forms['mform1'].special_param1.disabled=0;
      }
    else {
      document.forms['mform1'].special_param1.style.display='none';
      document.forms['mform1'].special_param1.value=0;
      document.forms['mform1'].special_param1.disabled=1;
      }
    var i;
    var ids = new Array($ids);
    var evaluates = new Array($evaluates);
    if (document.forms['mform1'].type_iassign.value==1) {
      document.forms['mform1'].automatic_evaluate.disabled=1;
      document.forms['mform1'].show_answer.disabled=1;
      document.forms['mform1'].automatic_evaluate.value=0;
      document.forms['mform1'].show_answer.value=0;
      }
    else { // if (document.forms['mform1'].type_iassign.value==1)
      for (i=0;i<ids.length;i++) {
        if (ids[i]==id) {
          if (document.forms['mform1'].action.value=='edit') {
            if (evaluates[i]==0){
              document.forms['mform1'].automatic_evaluate.style.display='none';
              document.forms['mform1'].show_answer.style.display='none';
              document.forms['mform1'].automatic_evaluate.disabled=1;
              document.forms['mform1'].show_answer.disabled=1;
              document.forms['mform1'].automatic_evaluate.value=0;
              document.forms['mform1'].show_answer.value=0;
              }
            else {
              document.forms['mform1'].automatic_evaluate.style.display='block';
              document.forms['mform1'].show_answer.style.display='block';
              document.forms['mform1'].automatic_evaluate.disabled=0;
              document.forms['mform1'].show_answer.disabled=0;
              document.forms['mform1'].automatic_evaluate.value=1;
              document.forms['mform1'].show_answer.value=1;
              }
            }

          if (document.forms['mform1'].action.value=='add') {
            if (evaluates[i]==0) {
              document.forms['mform1'].automatic_evaluate.style.display='none';
              document.forms['mform1'].show_answer.style.display='none';
              document.forms['mform1'].automatic_evaluate.disabled=1;
              document.forms['mform1'].show_answer.disabled=1;
              document.forms['mform1'].automatic_evaluate.value=0;
              document.forms['mform1'].show_answer.value=0;
              }
            else {
              document.forms['mform1'].automatic_evaluate.style.display='block';
              document.forms['mform1'].show_answer.style.display='block';
              document.forms['mform1'].automatic_evaluate.disabled=0;
              document.forms['mform1'].show_answer.disabled=0;
              document.forms['mform1'].automatic_evaluate.value=1;
              document.forms['mform1'].show_answer.value=1;
              }
            }
          } // if (ids[i]==id)
        } // for (i=0;i<ids.length;i++)
      } // else if (document.forms['mform1'].type_iassign.value==1)
    } // function config_ilm(id)

  function disable_answer (resp) {
    if (resp==0) {
      document.forms['mform1'].show_answer.value=0;
      document.forms['mform1'].show_answer.disabled=1;
      }
    else {
      document.forms['mform1'].show_answer.disabled=0;
      }
    }

  function view_ilm_manager () {
    document.forms['mform1'].filename.disabled=1;
    open_ilm_manager=window.open('$CFG->wwwroot/mod/iassign/ilm_manager.php?id=$COURSE->id&from=iassign&ilmid='+document.forms['mform1'].iassign_ilmid.value,'','width=1000,height=880,menubar=0,location=0,scrollbars,status,fullscreen,resizable');
    }
  //]]>
  </script>";

    //-------------------------------------------------------------------------------
    // Adding the "title_type_iassign" fieldset, where all the common settings are showed

    $mform->addElement('header', 'title_type_iassign', get_string('type_iassign', 'iassign'));
    $type_iassign = array();
    $type_iassign[1] = get_string('example', 'iassign');
    $type_iassign[2] = get_string('test', 'iassign');
    $type_iassign[3] = get_string('exercise', 'iassign');
      
    $mform->addElement('select', 'type_iassign', get_string('choose_type_activity', 'iassign'), $type_iassign, array('onChange' => 'config_type(this.value);'));
    $mform->setDefault('type_iassign', 3); // default type_iassign = 3
    $mform->addHelpButton('type_iassign', 'helptypeiassign', 'iassign');
    //-------------------------------------------------------------------------------
    // Adding the "data_activity" fieldset, where all the common settings are showed

    $mform->addElement('header', 'data_activity', get_string('data_activity', 'iassign'));
    $mform->addElement('static', 'author', get_string('author_id', 'iassign'));
    $mform->addElement('static', 'author_modified', get_string('author_id_modified', 'iassign'));

    // Adding the standard "name" field
    $mform->addElement('text', 'name', get_string('iassigntitle', 'iassign'), array('size' => '55', 'onChange' => 'confirm_name(this.value);'));
    $mform->setType('name', PARAM_TEXT);
    $mform->addRule('name', get_string('required', 'iassign'), 'required');

    // Adding the standard "proposition" field
    $mform->addElement('htmleditor', 'proposition', get_string('proposition', 'iassign'));
    $mform->setType('proposition', PARAM_RAW);
    $mform->addRule('proposition', get_string('required', 'iassign'), 'required');

    //-----------------------------------------------------------------------------
    // Adding the "interactivy_learning_module" fieldset, where all the common settings are showed
    $mform->addElement('header', 'interactivy_learning_module', get_string('interactivy_learning_module', 'iassign'));
    //$mform->setExpanded('interactivy_learning_module');

    // Search iLM registered in the database
    // $ilms = search_iLM(1);
    $ilms = $all_ilm;

    $applets = array();
    foreach ($ilms as $ilm)
      $applets[$ilm->id] = $ilm->name . ' ' . $ilm->version;
    $mform->addElement('select', 'iassign_ilmid', get_string('choose_iLM', 'iassign'), $applets, array('onChange' => 'config_ilm(this.value);'));
    $mform->addHelpButton('iassign_ilmid', 'helpchoose_ilm', 'iassign');

    $fileurl = "";
    $filename = "";
    if (!is_null($COURSE->iassign_file_id)) {
      $fs = get_file_storage();
      $file = $fs->get_file_by_id($COURSE->iassign_file_id);
      $fileurl = $CFG->wwwroot . "/pluginfile.php/" . $file->get_contextid() . "/mod_iassign/exercise" . '/' . $file->get_itemid() . $file->get_filepath() . $file->get_filename();
      $filename = $file->get_filename();
      }
    //D echo "123";
    $html_div = '<div id="fitem_id_iassign_file_id" class="fitem required fitem_fgroup">';
    $html_div .= '<div class="fitemtitle"><label for="id_iassign_file_id">' . get_string('choose_file', 'iassign');
    $html_div .= '<img class="req" title="' . get_string('requiredelement', 'form') . '" alt="' . get_string('requiredelement', 'form') . '" src="' . $CFG->wwwroot . '/theme/image.php/standard/core/1379534589/req"></label></div>';
    $html_div .= '<div class="felement fselect">';
    $html_div .= '<span id="iassign_file_link" style="color:#000000;"><a href="' . $fileurl . '" target="_blank" title="' . get_string('download_file', 'iassign') . $filename . '">' . $filename . '</a></span>';
    if ($fileurl != "")
      $html_div .= '&nbsp;&nbsp;&nbsp;';
    $html_div .= '<input onclick="view_ilm_manager()" name="add_ilm" value="' . get_string('add_ilm', 'iassign') . '" type="button" id="id_add_ilm"/>';
    $html_div .= '</div>';
    $html_div .= '</div>';
    $mform->addElement('html', $html_div);

    //Applies only iLM iGeom
    $mform->addElement('selectyesno', 'special_param1', get_string('special_param', 'iassign')); //$ynoptions
    $mform->setDefault('special_param1', 0);
    $mform->addHelpButton('special_param1', 'helpspecial_param', 'iassign');

    //-----------------------------------------------------------------------------
    //Applies only when the iLM is automatic evaluate.
    $mform->addElement('header', 'id_automatic_evaluate', get_string('only_automatic_evaluate', 'iassign'));

    // Using automatic evaluation activity? 0 - no / 1 – yes
    $mform->addElement('selectyesno', 'automatic_evaluate', get_string('automatic_evaluate', 'iassign'), array('onChange' => 'disable_answer(this.value);'));
    $mform->disabledIf('automatic_evaluate', 'type_iassign', 'eq', 1); //activity does not display if the type example
    $mform->setDefault('automatic_evaluate', 0);
    // @todo Ver código comentado
    //$mform->addHelpButton('automatic_evaluate', 'helpautomatic_evaluate', 'iassign');
    //Show automatic evaluation results to students? 0 - no / 1 - yes
    $mform->addElement('selectyesno', 'show_answer', get_string('show_answer', 'iassign'));
    $mform->disabledIf('show_answer', 'type_iassign', 'eq', 1); //activity does not display if the type example
    // $mform->disabledIf('show_answer', 'automatic_evaluate', 'neq', 0);
    $mform->setDefault('show_answer', 0);
    //$mform->addHelpButton('show_answer', 'helpshow_answer', 'iassign');

    //-----------------------------------------------------------------------------
    // Adding the "duration_activity" fieldset, where all the common settings are showed
    $mform->addElement('header', 'duration_activity', get_string('duration_activity', 'iassign'));

    $mform->addElement('date_time_selector', 'timeavailable', get_string('availabledate', 'iassign'));
    $mform->setDefault('timeavailable', time());
    $mform->disabledIf('timeavailable', 'type_iassign', 'eq', 1); // activity does not display if the type example
    $mform->addElement('date_time_selector', 'timedue', get_string('duedate', 'iassign'));
    $mform->setDefault('timedue', time() + 7 * 24 * 3600);
    $mform->disabledIf('timedue', 'type_iassign', 'eq', 1); //activity does not display if the type example

    //Allow sending late? 0 - no or unlocked / 1 - yes or locked
    $mform->addElement('selectyesno', 'preventlate', get_string('preventlate', 'iassign'));
    $mform->setDefault('preventlate', 0);
    $mform->addHelpButton('preventlate', 'helppreventlate', 'iassign');

    $mform->disabledIf('preventlate', 'type_iassign', 'eq', 1); //activity does not display if the type example
    $mform->disabledIf('preventlate', 'type_iassign', 'eq', 2); //activity does not display if the type test
    //Allow test after delivery? 0 - no or unlocked / 1 - yes or locked
    $mform->addElement('selectyesno', 'test', get_string('permission_test', 'iassign'));
    $mform->setDefault('test', 0);
    $mform->addHelpButton('test', 'helptest', 'iassign');

    $mform->disabledIf('test', 'type_iassign', 'eq', 1); //activity does not display if the type example
    $mform->disabledIf('test', 'type_iassign', 'eq', 2); //activity does not display if the type test
    //--------------
    $mform->addElement('header', 'op_val', get_string('op_val', 'iassign'));

    $mform->addElement('modgrade', 'grade', get_string('grade', 'iassign'));
    $mform->setDefault('grade', 100);
    $mform->disabledIf('grade', 'type_iassign', 'eq', 1); //activity does not display if the type example
    $mform->disabledIf('grade', 'type_iassign', 'eq', 2); //activity does not display if the type test

    $max_experiment_options = array(0 => get_string('ilimit', 'iassign'));
    for ($i = 1; $i <= 20; $i++)
      $max_experiment_options[$i] = $i;

    $mform->addElement('select', 'max_experiment', get_string('experiment', 'iassign'), $max_experiment_options);
    $mform->setDefault('max_experiment', 0);
    $mform->addHelpButton('max_experiment', 'helpexperiment', 'iassign');
    $mform->disabledIf('max_experiment', 'type_iassign', 'eq', 1); //activity does not display if the type example
    $mform->disabledIf('max_experiment', 'type_iassign', 'eq', 2); //activity does not display if the type test

    if ($COURSE->iassign_list) {
      //-------------- dependency
      $mform->addElement('header', 'headerdependency', get_string('dependency', 'iassign'));
      $mform->addHelpButton('headerdependency', 'helpdependency', 'iassign');

      foreach ($COURSE->iassign_list as $iassign) {
        $tmp = 'iassign_list[' . $iassign->id . ']';
        if ($iassign->enable == 1)
          $mform->addElement('checkbox', $tmp, $iassign->name);
        } //foreach ($COURSE->iassign_list as $iassign)
      } //if ($COURSE->iassign_list)

    $mform->addElement('hidden', 'dependency');
    $mform->setType('dependency', PARAM_RAW);

    //-------------- config
    $mform->addElement('header', 'config', get_string('general', 'iassign'));
    $visibleoptions = array(1 => get_string('show'), 0 => get_string('hide'));

    $mform->addElement('select', 'visible', get_string('visible', 'iassign'), $visibleoptions);
    $mform->setDefault('visible', 0);

    //-------------------------------------------------------------------------------
    // Hidden fields
    $mform->addElement('hidden', 'action');
    $mform->setType('action', PARAM_TEXT);
    $mform->addElement('hidden', 'oldname');
    $mform->setType('oldname', PARAM_TEXT);
    $mform->addElement('hidden', 'id');
    $mform->setType('id', PARAM_TEXT);
    $mform->addElement('hidden', 'iassign_id');
    $mform->setType('iassign_id', PARAM_TEXT);
    $mform->addElement('hidden', 'file', '0');
    $mform->setType('file', PARAM_INT);
    $mform->addElement('hidden', 'filename');
    $mform->setType('filename', PARAM_TEXT);
    $mform->addElement('hidden', 'fileold');
    $mform->setType('fileold', PARAM_TEXT);
    $mform->addElement('hidden', 'iassignid');
    $mform->setType('iassignid', PARAM_TEXT);
    $mform->addElement('hidden', 'author_name');
    $mform->setType('author_name', PARAM_TEXT);
    $mform->addElement('hidden', 'author_modified_name');
    $mform->setType('author_modified_name', PARAM_TEXT);
    $mform->addElement('hidden', 'timecreated');
    $mform->setType('timecreated', PARAM_TEXT);
    $mform->addElement('hidden', 'position');
    $mform->setType('position', PARAM_TEXT);

    $mform->addElement('html', $code_javascript);
    // add standard elements, common to all modules
    $this->add_action_buttons();

    } // function definition()


  function validation ($data, $files) {
    global $COURSE, $DB;
    $errors = parent::validation($data, $files);
    $mform = & $this->_form;
    $errors = array();

    if ($mform->elementExists('name')) {
      $value = trim($data['name']);
      if ($value == '') {
        $errors['name'] = get_string('required', 'iassign');
        }
      }

    if ($mform->elementExists('proposition')) {
      $value = trim($data['proposition']);
      if ($value == '') {
        $errors['proposition'] = get_string('required', 'iassign');
        }
      }

    if ($mform->elementExists('file')) {
      $value = trim($data['file']);
      if ($value == 0) {
        $errors['iassign_ilmid'] = get_string('required_iassign_file', 'iassign');
        }
      }
    return $errors;
    } // function validation($data, $files)

  } // class mod_iassign_form extends moodleform

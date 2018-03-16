<?php

/**
 * This file keeps track of upgrades to the lams module.
 * 
 * Sometimes, changes between versions involve
 * alterations to database structures and other
 * major things that may break installations.
 * The upgrade function in this file will attempt
 * to perform all the necessary actions to upgrade
 * your older installtion to the current version.
 * If there's something it cannot do itself, it
 * will tell you what you need to do.
 * The commands in here will all be database-neutral,
 * using the functions defined in lib/ddllib.php
 * 
 * - v 1.4 2013/09/19
 *     + Insert general fields for iassign statement (grade, timeavaliable, timedue, preventlate, test, max_experiment).
 *     + Change index field 'name' in 'iassign_ilm' table to index field 'name,version'.
 * - v 1.2 2013/08/30
 * + Change 'filearea' for new concept for files.
 * + Change path file for ilm, consider version in pathname.
 * 
 * @author Patricia Alves Rodrigues
 * @author Leônidas O. Brandão
 * @author Luciano Oliveira Borges
 * @version v 1.4 2013/09/19
 * @package mod_iassign_db
 * @since 2010/12/21
 * @copyright iMatica (<a href="http://www.matematica.br">iMath</a>) - Computer Science Dep. of IME-USP (Brazil)
 * 
 * <b>License</b> 
 *  - http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *  
 * @param $oldversion Number of the old version. 
 */

require_once ($CFG->dirroot . '/mod/iassign/locallib.php');

function xmldb_iassign_upgrade ($oldversion) {

  global $CFG, $DB, $USER;

  $dbman = $DB->get_manager();

  if ($oldversion < 2014012100) {
    // Define field and index in iLM table to be added
    $table = new xmldb_table('iassign_ilm');
    $index_name = new xmldb_index('name');
    $index_name->set_attributes(XMLDB_INDEX_UNIQUE, array('name'));
    if ($dbman->index_exists($table, $index_name)) {
      $dbman->drop_index($table, $index_name, $continue = true, $feedback = true);
      }
    $index_name_version = new xmldb_index('name_version');
    $index_name_version->set_attributes(XMLDB_INDEX_UNIQUE, array('name', 'version'));
    if (!$dbman->index_exists($table, $index_name_version)) {
      $dbman->add_index($table, $index_name_version, $continue = true, $feedback = true);
      }

    // Fix field name in ilm table to be added.
    $table = new xmldb_table('iassign_ilm');
    $field_height = new xmldb_field('heigth', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '600', 'width');
    if ($dbman->field_exists($table, $field_height))
      $dbman->rename_field($table, $field_height, 'height');

    // Define fields in iassign table to be added.
    $table = new xmldb_table('iassign');
    $field_intro = new xmldb_field('intro', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, 'name');
    if (!$dbman->field_exists($table, $field_intro))
      $dbman->add_field($table, $field_intro);
    $field_introformat = new xmldb_field('introformat', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0', 'intro');
    if (!$dbman->field_exists($table, $field_introformat))
      $dbman->add_field($table, $field_introformat);
    $field_grade = new xmldb_field('grade', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, '0', 'activity_group');
    if (!$dbman->field_exists($table, $field_grade))
      $dbman->add_field($table, $field_grade);
    $field_timeavailable = new xmldb_field('timeavailable', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'grade');
    if (!$dbman->field_exists($table, $field_timeavailable))
      $dbman->add_field($table, $field_timeavailable);
    $field_timedue = new xmldb_field('timedue', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'timeavailable');
    if (!$dbman->field_exists($table, $field_timedue))
      $dbman->add_field($table, $field_timedue);
    $field_preventlate = new xmldb_field('preventlate', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, null, null, '1', 'timedue');
    if (!$dbman->field_exists($table, $field_preventlate))
      $dbman->add_field($table, $field_preventlate);
    $field_test = new xmldb_field('test', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, '0', 'preventlate');
    if (!$dbman->field_exists($table, $field_test))
      $dbman->add_field($table, $field_test);
    $field_max_experiment = new xmldb_field('max_experiment', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'test');
    if (!$dbman->field_exists($table, $field_max_experiment))
      $dbman->add_field($table, $field_max_experiment);

    if (!$dbman->table_exists($table)) {
      $field_id = new xmldb_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE);
      $field_time = new xmldb_field('time', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'id');
      $field_userid = new xmldb_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'time');
      $field_ip = new xmldb_field('ip', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'userid');
      $field_course = new xmldb_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'ip');
      $field_cmid = new xmldb_field('cmid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'course');
      $field_ilmid = new xmldb_field('ilmid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'cmid');
      $field_action = new xmldb_field('action', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'ilmid');
      $field_info = new xmldb_field('info', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, 'action');
      $field_language = new xmldb_field('language', XMLDB_TYPE_CHAR, '10', null, null, null, null, 'info');
      $field_user_agent = new xmldb_field('user_agent', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, 'language');
      $field_javascript = new xmldb_field('javascript', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'user_agent');
      $field_java = new xmldb_field('java', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'javascript');

      $key = new xmldb_key('primary');
      $key->set_attributes(XMLDB_KEY_PRIMARY, array('id'), null, null);

      $index = new xmldb_index('course');
      $index->set_attributes(XMLDB_INDEX_NOTUNIQUE, array('course'));

      $table->addField($field_id);
      $table->addField($field_time);
      $table->addField($field_userid);
      $table->addField($field_ip);
      $table->addField($field_course);
      $table->addField($field_cmid);
      $table->addField($field_ilmid);
      $table->addField($field_action);
      $table->addField($field_info);
      $table->addField($field_language);
      $table->addField($field_user_agent);
      $table->addField($field_javascript);
      $table->addField($field_java);

      $table->addKey($key);

      $table->addIndex($index);

      $status = $dbman->create_table($table);
      } // if (!$dbman->table_exists($table))

    // Now update Moodle table '*_files' related to the iAssign: insert new field 'filearea'
    $fs = get_file_storage();
    $is_delete = true;
    $ilm_path = $CFG->dirroot . "/mod/iassign/ilm/";

    // All iAssign 'exercises' must get the label 'exercise'
    $exercise_files = $DB->get_records('files', array("component" => "mod_iassign", "filearea" => "activity"));
    foreach ($exercise_files as $exercise_file) {
      $exercise_file->filearea = "exercise";
      $DB->update_record('files', $exercise_file);
      }

    // All iAssign iLM (usually 'JAR package') must get the label 'activity'
    $activity_files = $DB->get_records('files', array("component" => "mod_iassign", "filearea" => "ilm"));
    foreach ($activity_files as $activity_file) {
      $activity_file->filearea = "activity";
      $DB->update_record('files', $activity_file);
      }

    // iAssign savepoint reached
    upgrade_mod_savepoint(true, 2014012100, 'iassign');
    } // if ($oldversion < 2014012100)

  if ($oldversion < 2017021900) { // update to migrate from 2016021300 to 2017021900 (2.2.00)
    // iassign_ilm : id; name; version; description; url; extension; parent; file_jar; file_class; width; height; enable; timemodified; author; timecreated; evaluate
    $count_iassign_ilm = $DB->count_records_sql('SELECT COUNT(id) FROM {iassign_ilm}'); // count the number of existing iLM in table '*_iassign_ilm'
    if ($count_iassign_ilm>0) { // Insert new iLM: iVProgH5 (package in HTML5 technology)

      // Insert an entry in the Moodle table '*_files'
      $context = context_system::instance();
      $file_ilm = array(
        'userid' => $USER->id, // ID of context
        'contextid' => $context->id, // ID of context
        'component' => 'mod_iassign', // usually = table name
        'filearea' => 'ilm', // in table '*_files' all iAssign exercises will have 'filearea="exercise"' and all iLM must be 'ilm'
        'itemid' => 0,
        'filepath' => '/iassign/ilm/', // any path beginning and ending in /
        'filename' => "ivprog-html/main.html"); // any filename: use the starting point of iVProgH5

      // Now update Moodle table '*_files' related to the iAssign: insert new field 'filearea'
      $fs = get_file_storage();
      $is_delete = true;
      $ilm_path = $CFG->dirroot . "/mod/iassign/ilm/";

      //TODO iLM_HTML5 : What model to implement to HTML5?
      //TODO 1. The first version of iLM HTML5 in '/var/www/html/moodle/mod/iassign/ilm/ilm-nome' and others in '/var/moodledata/filedir/'?
      //TODO 2. Inserting new iLM HTML5 by ZIP file => explode it and register in '/var/moodledata/filedir/'?

      // Create file using the content: the file information is inserted in the Moodle table '*_files'
      // The real file will be registered in Moodle data (default '/var/moodledata/filedir/xx/yy' - 'xxyy' is the first 4 char in '*_files.contenthash')
      //TODO Keep this even change the model to not insert iLM HTML5 (need this object to connect with '*_iassign_ilm' bellow)
      $file_ilm = $fs->create_file_from_string($file_ilm, file_get_contents($ilm_path . "ivprog-html/main.html"));

      if ($file_ilm)
        $is_delete &= @unlink($ilm_path . "ivprog-html");

      // Insert an entry in iAssign table '*_iassign_ilm'
      $newentry = new stdClass();
      $newentry->name = 'iVProgH5';
      $newentry->url = 'http://www.matematica.br/ivprogh5';
      $newentry->version = '0.1.0';
      $newentry->description = '{"en":"Visual Interactive Programming on the Internet HTML5","pt_br":"Programação visual interativa na Internet"}';
      $newentry->extension = 'ivph';
      $newentry->file_jar = 'iVProgH5'; // to JAR this is the '*_files.id' correponding to the iLM storaged in '/var/moodledata/filedir/'
      $newentry->file_class = 'ivprog-html/main.html';
      $newentry->width = 800;
      $newentry->height = 700;
      $newentry->enable = 1;
      $newentry->timemodified = time();
      $newentry->author = $USER->id;
      $newentry->timecreated = time();
      $newentry->evaluate = 1;

      // $DB->insert_records('iassign_ilm', $newentry);
      $newentry->id = $DB->insert_record("iassign_ilm", $newentry);

      // iAssign savepoint reached
      upgrade_mod_savepoint(true, 2017021900, 'iassign');
      } // if ($count_iassign_ilm>0)

    } // if ($oldversion < 2017021900)

  if ($oldversion < 2017120100) { // last one: 2017042800 from 2017/02/19
    // Create new field 'type', putting 'Java' if JAR and 'HTML5' if HTML5
    // Update fields 'file_jar, file_class' of iLM iVProgH5 (from 'iVProgH5, ivprog-html/main.html' to 'ilm/ivprog-html/, main.html'

    //---
    // Define new field in iassign table to be added: type varchar(20) in { 'Java' 'HTML5' }
    $table = new xmldb_table('iassign_ilm');
    $new_field_type = new xmldb_field('type', XMLDB_TYPE_CHAR, '20', null, null, null, null, 'version'); // after field 'version'
    if (!$dbman->field_exists($table, $new_field_type))
      $dbman->add_field($table, $new_field_type);

    //---      
    // All iAssign iLM (usually 'JAR package') must get the type 'Java'
    $list_of_ilm_installed = $DB->get_records('iassign_ilm');
    foreach ($list_of_ilm_installed as $ilm_installed) {
      $ilm_name = strtolower($ilm_installed->name);
      //D echo $ilm_name . " : " . $ilm_installed->file_jar . " : " . $ilm_installed->file_class . " : " . $ilm_installed->extension . "<br/>\n";

      if ($ilm_name=="ivprogh5" || $ilm_name=="ifractions" || $ilm_name=="fractions") { // if it is 'iVProgH5' (perhaps 'iFractions' already installed?)
        $ilm_installed->type = "HTML5"; // it is HTML5
        $file_jar = strtolower($ilm_installed->file_jar);
        if ($ilm_name=="ivprogh5" && $file_jar=="ivprogh5") { // fields 'file_jar' and 'file_class' must have: 'ilm/ivprog-html/' and 'main.html'
          $ilm_installed->file_jar = "ilm/ivprog-html/"; //  iVProgH5 ivprog-html/main.html
          $ilm_installed->file_class = "main.html";
          }
        }
      else
        $ilm_installed->type = "Java"; // otherwise it is Java

      $DB->update_record('iassign_ilm', $ilm_installed);
      } // foreach

    //---
    // Insert in talbe '{iassign_ilm}' the new iLM iFractions version 0.1.2017.11.22
    // Table 'iassign_ilm' : id name version type description url extension parent file_jar file_class width height enable timemodified author timecreated evaluate
    /*$new_ilm_ifractions['name'] = 'iFractions';
    $new_ilm_ifractions['version'] = '0.1.2017.11.22';
    $new_ilm_ifractions['type'] = 'HTML5';
    $new_ilm_ifractions['description'] = '{"en":"Visual Interactive Fractions Learning","pt_br":"Aprendizagem visual interativa de frações"}';
    $new_ilm_ifractions['url'] = 'http://www.matematica.br/ifractions';
    $new_ilm_ifractions['extension'] = 'frc';
    $new_ilm_ifractions['parent'] = '0';
    $new_ilm_ifractions['file_jar'] = 'ilm/ifractions/';
    $new_ilm_ifractions['file_class'] = 'index.html';
    $new_ilm_ifractions['width'] = '900';
    $new_ilm_ifractions['height'] = '600';
    $new_ilm_ifractions['enable'] = '1';
    $new_ilm_ifractions['timemodified'] = time();
    $new_ilm_ifractions['author'] = $USER->id;
    $new_ilm_ifractions['timecreated'] = time();
    $new_ilm_ifractions['evaluate'] = 1;
    $DB->insert_record('iassign_ilm', $new_ilm_ifractions, false); // insert new iLM in the table '{iassign_ilm}';*/

    } // if ($oldversion < 2017120101)

    if ($oldversion < 2018031000) {
      // Verify if exist iLM with the same name
      // then, update the version and file_jar

      $records = array(
        // iGeom 5.9.22
        array_combine(
                array('name', 'url', 'version', 'type', 'description', 'extension', 'file_jar', 'file_class', 'width', 'height', 'enable', 'timemodified', 'author', 'timecreated', 'evaluate'), array('iGeom', 'http://www.matematica.br/igeom', '5.9.22', 'Java', '{"en":"Interactive Geometry on the Internet","pt_br":"Geometria Interativa na Internet"}', 'geo', 'ilm/iGeom/5.9.22/iGeom.jar', 'IGeomApplet.class', 800, 600, 1, time(), $USER->id, time(), 1)),
        // iGraf 4.4.0.10
        array_combine(
                array('name', 'url', 'version', 'type', 'description', 'extension', 'file_jar', 'file_class', 'width', 'height', 'enable', 'timemodified', 'author', 'timecreated', 'evaluate'), array('iGraf', 'http://www.matematica.br/igraf', '4.4.0.10', 'Java', '{"en":"Interactive Graphic on the Internet","pt_br":"Gráficos Interativos na Internet"}', 'grf', 'ilm/iGraf/4.4.0.10/iGraf.jar', 'igraf.IGraf.class', 840, 600, 1, time(), $USER->id, time(), 1)),
        // iComb 0.9.5
        array_combine(
                array('name', 'url', 'version', 'type', 'description', 'extension', 'file_jar', 'file_class', 'width', 'height', 'enable', 'timemodified', 'author', 'timecreated', 'evaluate'), array('iComb', 'http://www.matematica.br/icomb', '0.9.5', 'Java', '{"en":"Combinatorics Interactive on the Internet","pt_br":"Combinatória Interativa na Internet"}', 'icb,cmb', 'ilm/iComb/0.9.5/iComb.jar', 'icomb.IComb.class', 750, 685, 1, time(), $USER->id, time(), 1)),
        // iVProg2 2.1.0
        array_combine(
                array('name', 'url', 'version', 'type', 'description', 'extension', 'file_jar', 'file_class', 'width', 'height', 'enable', 'timemodified', 'author', 'timecreated', 'evaluate'), array('iVProg2', 'http://www.matematica.br/ivprog2', '2.1.0', 'Java', '{"en":"Visual Interactive Programming on the Internet","pt_br":"Programação visual interativa na Internet"}', 'ivp2', 'ilm/iVProg2/2.1.0/iVProg2.jar', 'usp.ime.line.ivprog.Ilm.class', 800, 700, 1, time(), $USER->id, time(), 1)),
        // iTangram2 0.4.6
        array_combine(
                array('name', 'url', 'version', 'type', 'description', 'extension', 'file_jar', 'file_class', 'width', 'height', 'enable', 'timemodified', 'author', 'timecreated', 'evaluate'),
                array('iTangram2', 'http://www.matematica.br/itangram', '0.4.6', 'Java', '{"en":"The Objective of the game is to reproduce the form of the model using all 7 pieces of iTangram","pt_br":"O Objetivo do jogo é reproduzir a forma do modelo usando todas as 7 peças do iTangram"}', 'itg2', 'ilm/iTangram2/0.4.6/iTangram2.jar', 'ilm.line.itangram2.Tangram', 800, 600, 1, time(), $USER->id, time(), 1)),
        // iHanoi 3.1.0
        array_combine(
                array('name', 'url', 'version', 'type', 'description', 'extension', 'file_jar', 'file_class', 'width', 'height', 'enable', 'timemodified', 'author', 'timecreated', 'evaluate'),
                array('iHanoi', 'http://www.matematica.br/ihanoi', '3.1.0', 'Java', '{"en":"The Objective to move N discs from stick A to C, following some rule (from the game Towers of Hanoi)","pt_br":"O objetivo é mover N discos da haste A para C, seguindo algumas regras (implementa o jogo Torres de Hanói)"}', 'ihn', 'ilm/iHanoi/3.1.0/iHanoi.jar', 'ihanoi.iHanoi', 730, 450, 1, time(), $USER->id, time(), 1)),
        // Risco 2
        array_combine(
                array('name', 'url', 'version', 'type', 'description', 'extension', 'file_jar', 'file_class', 'width', 'height', 'enable', 'timemodified', 'author', 'timecreated', 'evaluate'),
                array('Risko', 'http://risko.pcc.usp.br/', '2.2.23', 'Java', '{"en":"Interactive computational tool for teaching geometry","pt_br":"Ferramenta computacional interativa para o ensino de geometria"}', 'rsk', 'ilm/Risko/2.2.23/Risko.jar', 'RiskoApplet.class', 800, 600, 1, time(), $USER->id, time(), 0)),
        // iVProgH5 0.1 - HTML5
        array_combine(
                 array('name', 'url', 'version', 'type', 'description', 'extension', 'file_jar', 'file_class', 'width', 'height', 'enable', 'timemodified', 'author', 'timecreated', 'evaluate'), 
                 array('iVProgH5', 'http://www.matematica.br/ivprogh5', '0.1.0', 'HTML5', '{"en":"Visual Interactive Programming on the Internet HTML5","pt_br":"Programação visual interativa na Internet"}', 'ivph', 'ilm/iVProgH5/0.1.0/ivprog-html/', 'main.html', 800, 600, 1, time(), $USER->id, time(), 1)),
        // fractions 0.1.2017.11.22 - HTML5
        array_combine(
                 array('name', 'url', 'version', 'type', 'description', 'extension', 'file_jar', 'file_class', 'width', 'height', 'enable', 'timemodified', 'author', 'timecreated', 'evaluate'), 
                 array('iFractions', 'http://www.matematica.br/ifractions', '0.1.2017.11.22', 'HTML5', '{"en":"Visual Interactive Fractions Learning","pt_br":"Aprendizagem visual interativa de frações"}', 'frc', 'ilm/iFractions/0.1.2017.11.22/ifractions/', 'index.html', 1000, 600, 1, time(), $USER->id, time(), 1))
          );

      foreach ($records as $record) {

        // Verify if there is a iLM register to update it
        $iassign_ilm = $DB->get_record('iassign_ilm', array('name' => $record['name'], 'version' => $record['version']));

        if ($iassign_ilm) {
          // Update file_jar and file_class
          $newentry = new stdClass();
          $newentry->id = $iassign_ilm->id;
          $newentry->file_jar = $record['file_jar'];
          $newentry->file_class = $record['file_class'];

          $DB->update_record("iassign_ilm", $newentry);
        } else {
          // If not found a iLM with the same name and version, search a
          // different version, to use as parent of new version

          $iassign_ilm_parent = $DB->get_record('iassign_ilm', array('name' => $record['name'], 'parent' => 0));

          if ($iassign_ilm_parent) {
            $record['parent'] = $iassign_ilm_parent->id;
            $DB->insert_record('iassign_ilm', $record, false); // insert with parent
          } else {
            $DB->insert_record('iassign_ilm', $record, false); // insert new iLM in the table '*_iassign_ilm' without a parent
          }
        }
        
      } // foreach ($records as $record) 
      
    } // if ($oldversion < 2018031000)

    

  // log event -----------------------------------------------------
  if (class_exists('plugin_manager'))
    $pluginman = plugin_manager::instance();
  else
    $pluginman = core_plugin_manager::instance();
  $plugins = $pluginman->get_plugins();
  iassign_log::add_log('upgrade', 'version: ' . $plugins['mod']['iassign']->versiondisk);
  // log event -----------------------------------------------------

  return true;
  }
<?php

/**
 * Script for install all ilm in iassign table.
 *
 * This file replaces:
 * STATEMENTS section in db/install.xml
 * lib.php/modulename_install() post installation hook partially defaults.php.
 *
 * Release Notes:
 * - v 1.4 2017/02/16
 *   + Insert iVProgH5, updated iGeom, iGraf, iComb, iHanoi
 * - v 1.3 2013/12/12
 *   + Language support in iLM
 * - v 1.2 2013/09/19
 *   + Change path file for ilm, consider version in pathname
 *
 * @author Patricia Alves Rodrigues
 * @author Leônidas O. Brandão
 * @author Luciano Oliveira Borges
 * @version v 1.2 2013/09/19
 * @package mod_iassign_db
 * @since 2010/09/27
 * @copyright iMatica (<a href="http://www.matematica.br">iMath</a>) - Computer Science Dep. of IME-USP (Brazil)
 *
 * <b>License</b>
 *  - http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once ($CFG->dirroot . '/mod/iassign/locallib.php');

function xmldb_iassign_install() {
  global $DB, $USER, $CFG;

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

    // Verify if there is a previous iLM (an old version that must be kept)
    $iassign_ilm_parent = $DB->get_record('iassign_ilm', array('name' => $record['name'], 'parent' => 0));

    $ilm_exists = false;
    if ($iassign_ilm_parent) {
      if ($iassign_ilm_parent->version != $record['version'])
        $record['parent'] = $iassign_ilm_parent->id;
      else
        $ilm_exists = true;
      }

    if (!empty($file_jar)) {
      $DB->insert_record('iassign_ilm', $record, false); // insert new iLM in the table '*_iassign_ilm'
    }
  } // foreach ($records as $record)
  // Add iAssign button to the Atto Editor
  $toolbar = get_config('editor_atto', 'toolbar');
  if (strpos($toolbar, 'iassign') === false && $toolbar && $toolbar != '') {
    $groups = explode("\n", $toolbar);
    // Try to put iassign in the html group.
    $found = false;
    foreach ($groups as $i => $group) {
      $parts = explode('=', $group);
      if (trim($parts[0]) == 'other') {
        $groups[$i] = 'other = ' . trim($parts[1]) . ', iassign';
        $found = true;
        }
      }

    // if the group is not found, create the other group and insert it there
    // Maybe unecessary as the other group is a standard, but if the user has changed it?
    if (!$found) {
      do {
        $last = array_pop($groups);
      } while (empty($last) && !empty($groups));
      $groups[] = 'other = iassign';
      $groups[] = $last;
      }

    // Update $toolbar and add to the config
    $toolbar = implode("\n", $groups);
    set_config('toolbar', $toolbar, 'editor_atto');
    } // if (strpos($toolbar, 'iassign') === false && $toolbar && $toolbar != '')
  // end Add

  // log event -----------------------------------------------------
  if (class_exists('plugin_manager'))
    $pluginman = plugin_manager::instance();
  else
    $pluginman = core_plugin_manager::instance();
  $plugins = $pluginman->get_plugins();
  iassign_log::add_log('install', 'version: ' . $plugins['mod']['iassign']->versiondb);
  // log event -----------------------------------------------------
  }

// function xmldb_iassign_install()

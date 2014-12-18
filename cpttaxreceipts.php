<?php

require_once 'cpttaxreceipts.civix.php';
require_once 'cpttaxreceipts.functions.inc';
require_once 'cpttaxreceipts.db.inc';


/**
 * Implementation of hook_civicrm_searchTasks().
 *
 * For users with permission to issue tax receipts, give them the ability to do it
 * as a batch of search results.
 */

function cpttaxreceipts_civicrm_searchTasks($objectType, &$tasks ) {
  if ( $objectType == 'contact' && CRM_Core_Permission::check( 'issue CPT Tax Receipts' ) ) {
    $annual_in_list = FALSE;
    foreach ($tasks as $key => $task) {
      if($task['class'] == 'CRM_CPTTaxReceipts_Task_IssueAnnualTaxReceipts') {
        $annual_in_list = TRUE;
      }
    }
    if (!$annual_in_list) {
      $tasks[] = array (
        'title' => ts('Issue Annual Tax Receipts'),
        'class' => 'CRM_CPTTaxReceipts_Task_IssueAnnualTaxReceipts',
        'result' => TRUE);
    }
  }
}

/**
 * Implementation of hook_civicrm_permission().
 */
function cpttaxreceipts_civicrm_permission( &$permissions ) {
  $prefix = ts('CiviCRM CPT Tax Receipts') . ': ';
  $permissions = array(
    'issue CPT Tax Receipts' => $prefix . ts('Issue Tax Receipts', array('domain' => 'org.cpt.cpttaxreceipts')),
  );
}


/**
 * Implementation of hook_civicrm_config
 */
function cpttaxreceipts_civicrm_config(&$config) {
  _cpttaxreceipts_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 */
function cpttaxreceipts_civicrm_xmlMenu(&$files) {
  _cpttaxreceipts_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 */
function cpttaxreceipts_civicrm_install() {
  // copy tables civicrm_cpttaxreceipts_log and civicrm_cpttaxreceipts_log_contributions IF they already exist
  // Issue: #1
  return _cpttaxreceipts_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 */
function cpttaxreceipts_civicrm_uninstall() {
  return _cpttaxreceipts_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 */
function cpttaxreceipts_civicrm_enable() {
  CRM_Core_Session::setStatus(ts('Configure the Tax Receipts extension at Administer >> CiviContribute >> CPT Tax Receipts.', array('domain' => 'org.cpt.cpttaxreceipts')));
  return _cpttaxreceipts_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 */
function cpttaxreceipts_civicrm_disable() {
  return _cpttaxreceipts_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 */
function cpttaxreceipts_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _cpttaxreceipts_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 */
function cpttaxreceipts_civicrm_managed(&$entities) {
  return _cpttaxreceipts_civix_civicrm_managed($entities);
}
/**
 * Implementation of hook_civicrm_managed
 *
 * Add entries to the navigation menu, automatically removed on uninstall
 */

function cpttaxreceipts_civicrm_navigationMenu(&$params) {

  // Check that our item doesn't already exist
  $cpttax_search = array('url' => 'civicrm/cpttaxreceipts/settings?reset=1');
  $cpttax_item = array();
  CRM_Core_BAO_Navigation::retrieve($cpttax_search, $cpttax_item);

  if ( ! empty($cpttax_item) ) {
    return;
  }

  // Get the maximum key of $params using method mentioned in discussion
  // https://issues.civicrm.org/jira/browse/CRM-13803
  $navId = CRM_Core_DAO::singleValueQuery("SELECT max(id) FROM civicrm_navigation");
  if (is_integer($navId)) {
    $navId++;
  }
  // Find the Memberships menu
  foreach($params as $key => $value) {
    if ('Administer' == $value['attributes']['name']) {
      $parent_key = $key;
      foreach($value['child'] as $child_key => $child_value) {
        if ('CiviContribute' == $child_value['attributes']['name']) {
          $params[$parent_key]['child'][$child_key]['child'][$navId] = array (
            'attributes' => array (
              'label' => ts('CPT Tax Receipts',array('domain' => 'org.cpt.cpttaxreceipts')),
              'name' => 'CPT Tax Receipts',
              'url' => 'civicrm/cpttaxreceipts/settings?reset=1',
              'permission' => 'access CiviContribute,administer CiviCRM',
              'operator' => 'AND',
              'separator' => 2,
              'parentID' => $child_key,
              'navID' => $navId,
              'active' => 1
            )
          );
        }
      }
    }
  }
}

function cpttaxreceipts_civicrm_validate( $formName, &$fields, &$files, &$form ) {
  if ($formName == 'CRM_CPTTaxReceipts_Form_Settings') {
    $errors = array();
    $allowed = array('gif', 'png', 'jpg', 'pdf');
    foreach ($files as $key => $value) {
      if (CRM_Utils_Array::value('name', $value)) {
        $ext = pathinfo($value['name'], PATHINFO_EXTENSION);
        if (!in_array($ext, $allowed)) {
          $errors[$key] = ts('Please upload a valid file. Allowed extensions are (.gif, .png, .jpg, .pdf)');
        }
      }
    }
    return $errors;
  }
}


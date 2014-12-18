<?php

require_once 'cpttaxreceipts.civix.php';
require_once 'cpttaxreceipts.functions.inc';
require_once 'cpttaxreceipts.db.inc';

function cpttaxreceipts_civicrm_buildForm( $formName, &$form ) {

  if ( is_a( $form, 'CRM_Contribute_Form_ContributionView' ) ) {

    // add "Issue Tax Receipt" button to the "View Contribution" page
    // if the Tax Receipt has NOT yet been issued -> display a white maple leaf icon
    // if the Tax Receipt has already been issued -> display a red maple leaf icon

    CRM_Core_Resources::singleton()->addStyleFile('org.cpt.cpttaxreceipts', 'css/civicrm_cpttaxreceipts.css');

    $contributionId = $form->get( 'id' );

    if ( isset($contributionId) && cpttaxreceipts_eligibleForReceipt($contributionId) ) {

      list($issued_on, $receipt_id) = cpttaxreceipts_issued_on($contributionId);
      $is_original_receipt = empty($issued_on);

      if ($is_original_receipt) {
        $buttons = array(array('type'      => 'submit',
                               'subName'   => 'issue_tax_receipt',
                               'name'      => ts('Tax Receipt', array('domain' => 'org.cpt.cpttaxreceipts')),
                               'isDefault' => FALSE ), );
      }
      else {
        // this is essentially the same button - but it has a different
        // subName -> which is used (css) to display the red maple leaf instead.
        $buttons = array(array('type'      => 'submit',
                               'subName'   => 'view_tax_receipt',
                               'name'      => ts('Tax Receipt', array('domain' => 'org.cpt.cpttaxreceipts')),
                               'isDefault' => FALSE ), );
      }
      $form->addButtons( $buttons );

    }
  }
}

/**
 * Implementation of hook_civicrm_postProcess().
 *
 * Called when a form comes back for processing. Basically, we want to process
 * the button we added in cpttaxreceipts_civicrm_buildForm().
 */

function cpttaxreceipts_civicrm_postProcess( $formName, &$form ) {

  // first check whether I really need to process this form
  if ( ! is_a( $form, 'CRM_Contribute_Form_ContributionView' ) ) {
    return;
  }
  $types = array('issue_tax_receipt','view_tax_receipt');
  $action = '';
  foreach($types as $type) {
    $post = '_qf_ContributionView_submit_'.$type;
    if (isset($_POST[$post])) {
      if ($_POST[$post] == ts('Tax Receipt', array('domain' => 'org.cpt.cpttaxreceipts'))) {
        $action = $post;
      }
    }
  }
  if (empty($action)) {
    return;
  }

  // the tax receipt button has been pressed.  redirect to the tax receipt 'view' screen, preserving context.
  $contributionId = $form->get( 'id' );
  $contactId = $form->get( 'cid' );

  $session = CRM_Core_Session::singleton();
  $session->pushUserContext(CRM_Utils_System::url('civicrm/contact/view/contribution',
    "reset=1&id=$contributionId&cid=$contactId&action=view&context=contribution&selectedChild=contribute"
  ));

  $urlParams = array('reset=1', 'id='.$contributionId, 'cid='.$contactId);
  CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/cpttaxreceipts/view', implode('&',$urlParams)));
}

/**
 * Implementation of hook_civicrm_searchTasks().
 *
 * For users with permission to issue tax receipts, give them the ability to do it
 * as a batch of search results.
 */

function cpttaxreceipts_civicrm_searchTasks($objectType, &$tasks ) {
  if ( $objectType == 'contribution' && CRM_Core_Permission::check( 'issue CPT Tax Receipts' ) ) {
    $single_in_list = FALSE;
    $aggregate_in_list = FALSE;
    foreach ($tasks as $key => $task) {
      if($task['class'] == 'CRM_cpttaxreceipts_Task_IssueSingleTaxReceipts') {
        $single_in_list = TRUE;
      }
    }
    foreach ($tasks as $key => $task) {
      if($task['class'] == 'CRM_cpttaxreceipts_Task_IssueAggregateTaxReceipts') {
        $aggregate_in_list = TRUE;
      }
    }
    if (!$single_in_list) {
      $tasks[] = array (
        'title' => ts('Issue Tax Receipts', array('domain' => 'org.cpt.cpttaxreceipts')),
        'class' => 'CRM_cpttaxreceipts_Task_IssueSingleTaxReceipts',
        'result' => TRUE);
    }
    if (!$aggregate_in_list) {
      $tasks[] = array (
        'title' => ts('Issue Aggregate Tax Receipts'),
        'class' => 'CRM_cpttaxreceipts_Task_IssueAggregateTaxReceipts',
        'result' => TRUE);
    }
  }
  elseif ( $objectType == 'contact' && CRM_Core_Permission::check( 'issue CPT Tax Receipts' ) ) {
    $annual_in_list = FALSE;
    foreach ($tasks as $key => $task) {
      if($task['class'] == 'CRM_cpttaxreceipts_Task_IssueAnnualTaxReceipts') {
        $annual_in_list = TRUE;
      }
    }
    if (!$annual_in_list) {
      $tasks[] = array (
        'title' => ts('Issue Annual Tax Receipts'),
        'class' => 'CRM_cpttaxreceipts_Task_IssueAnnualTaxReceipts',
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
  if ($formName == 'CRM_cpttaxreceipts_Form_Settings') {
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


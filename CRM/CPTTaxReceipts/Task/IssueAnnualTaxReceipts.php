<?php

/**
 * This class provides the common functionality for issuing Annual Tax Receipts for
 * one or a group of contact ids.
 */
class CRM_CPTTaxReceipts_Task_IssueAnnualTaxReceipts extends CRM_Contact_Form_Task {

  private $_receipts;
  private $_years;

  /**
   * build all the data structures needed to build the form
   *
   * @return void
   * @access public
   */
  function preProcess() {

    //check for permission to edit contributions
    if ( ! CRM_Core_Permission::check('issue CPT Tax Receipts') ) {
      CRM_Core_Error::fatal(ts('You do not have permission to access this page', array('domain' => 'org.cpt.cpttaxreceipts')));
    }

    parent::preProcess();

    $thisYear = date("Y");
    $this->_years = array($thisYear, $thisYear - 1, $thisYear - 2);

    $receipts = array();
    foreach ( $this->_years as $year ) {
      $receipts[$year] = array('email' => 0, 'print' => 0, 'total' => 0, 'contrib' => 0);
    }

    // count and categorize contributions
    foreach ( $this->_contactIds as $id ) {
      foreach ( $this->_years as $year ) {
        $eligible = count(cpttaxreceipts_contributions($id, $year));
        if ( $eligible > 0 ) {
          list( $method, $email ) = cpttaxreceipts_sendMethodForContact($id);
          $receipts[$year][$method]++;
          $receipts[$year]['total']++;
          $receipts[$year]['contrib'] += $eligible;
        }
      }
    }

    $this->_receipts = $receipts;

  }

  /**
   * Build the form
   *
   * @access public
   *
   * @return void
   */
  function buildQuickForm() {

    CRM_Utils_System::setTitle(ts('Issue Annual Tax Receipts', array('domain' => 'org.cpt.cpttaxreceipts')));

    CRM_Core_Resources::singleton()->addStyleFile('org.cpt.cpttaxreceipts', 'css/civicrm_cpttaxreceipts.css');

    // assign the counts
    $receipts = $this->_receipts;
    $receiptTotal = 0;
    foreach ( $this->_years as $year ) {
      $receiptTotal += $receipts[$year]['total'];
    }

    $this->assign('receiptCount', $receipts);
    $this->assign('receiptTotal', $receiptTotal);
    $this->assign('receiptYears', $this->_years);
    $this->assign('enable_email', CRM_Core_BAO_Setting::getItem('cpttaxreceipts', 'enable_email'));

    // add radio buttons
    foreach ( $this->_years as $year ) {
      $this->addElement('radio', 'receipt_year', NULL, $year, 'issue_' . $year);
    }
    $this->addRule('receipt_year', ts('Selection required', array('domain' => 'org.cpt.cpttaxreceipts')), 'required');

    $this->add('checkbox', 'is_preview', ts('Run in preview mode?', array('domain' => 'org.cpt.cpttaxreceipts')));

    $buttons = array(
      array(
        'type' => 'cancel',
        'name' => ts('Back', array('domain' => 'org.cpt.cpttaxreceipts')),
      ),
      array(
        'type' => 'next',
        'name' => 'Issue Tax Receipts',
        'isDefault' => TRUE,
        'js' => array('onclick' => "return submitOnce(this,'{$this->_name}','" . ts('Processing', array('domain' => 'org.cpt.cpttaxreceipts')) . "');"),
      ),
    );
    $this->addButtons($buttons);

  }

  function setDefaultValues() {
    return array('receipt_year' => 'issue_' . (date("Y") - 1),);
  }

  /**
   * process the form after the input has been submitted and validated
   *
   * @access public
   *
   * @return None
   */

  function postProcess() {

    // lets get around the time limit issue if possible
    if ( ! ini_get( 'safe_mode' ) ) {
      set_time_limit( 0 );
    }

    // Issue 1895204: Turn off geocoding to avoid hitting Google API limits
    $oldGeocode = cdntaxreceipts_getCiviSetting('geoProvider');
    cdntaxreceipts_setCiviSetting('geoProvider', NULL);

    $params = $this->controller->exportValues($this->_name);
    $year = $params['receipt_year'];
    if ( $year ) {
      $year = substr($year, strlen('issue_')); // e.g. issue_2012
    }

    $previewMode = FALSE;
    if (isset($params['is_preview']) && $params['is_preview'] == 1 ) {
      $previewMode = TRUE;
    }

    /**
     * Drupal module include
     */
    //module_load_include('.inc','civicrm_cpttaxreceipts','civicrm_cpttaxreceipts');f
    //module_load_include('.module','civicrm_cpttaxreceipts','civicrm_cpttaxreceipts');

    // start a PDF to collect receipts that cannot be emailed
    $receiptsForPrinting = cpttaxreceipts_openCollectedPDF();

    $emailCount = 0;
    $printCount = 0;
    $failCount = 0;

    foreach ($this->_contactIds as $contactId ) {

      $contributions = cpttaxreceipts_contributions($contactId, $year);

      if ( count($contributions) > 1 ) {

        list( $ret, $method ) = cpttaxreceipts_issueAnnualTaxReceipt($contactId, $year, $receiptsForPrinting, $previewMode);

        if ( $ret == 0 ) {
          $failCount++;
        }
        elseif ( $method == 'email' ) {
          $emailCount++;
        }
        else {
          $printCount++;
        }
      }
    }

    // 3. Set session status
    if ( $previewMode ) {
      $status = ts('%1 tax receipt(s) have been previewed.  No receipts have been issued.', array(1=>$printCount, 'domain' => 'org.cpt.cpttaxreceipts'));
      CRM_Core_Session::setStatus($status, '', 'success');
    }
    else {
      $status = ts('%1 tax receipt(s) were sent by email.', array(1=>$emailCount, 'domain' => 'org.cpt.cpttaxreceipts'));
      CRM_Core_Session::setStatus($status, '', 'success');
      $status = ts('%1 tax receipt(s) need to be printed.', array(1=>$printCount, 'domain' => 'org.cpt.cpttaxreceipts'));
      CRM_Core_Session::setStatus($status, '', 'success');
    }

    if ( $failCount > 0 ) {
      $status = ts('%1 tax receipt(s) failed to process.', array(1=>$failCount, 'domain' => 'org.cpt.cpttaxreceipts'));
      CRM_Core_Session::setStatus($status, '', 'error');
    }


    // Issue 1895204: Reset geocoding
    cdntaxreceipts_setCiviSetting('geoProvider', $oldGeocode);

    // 4. send the collected PDF for download
    // NB: This exits if a file is sent.
    cpttaxreceipts_sendCollectedPDF($receiptsForPrinting, 'Receipts-To-Print-' . (int) $_SERVER['REQUEST_TIME'] . '.pdf');  // EXITS.
  }
}


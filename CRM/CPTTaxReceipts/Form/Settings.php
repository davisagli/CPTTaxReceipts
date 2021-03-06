<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_CPTTaxReceipts_Form_Settings extends CRM_Core_Form {

  CONST SETTINGS = 'cpttaxreceipts';

  function buildQuickForm() {

    CRM_Utils_System::setTitle(ts('Configure CPT Contribution Summaries', array('domain' => 'org.cpt.cpttaxreceipts')));

    $this->processOrgOptions('build');
    $this->processReceiptOptions('build');
    $this->processSystemOptions('build');
    $this->processEmailOptions('build');

    $arr1 = $this->processOrgOptions('defaults');
    $arr2 = $this->processReceiptOptions('defaults');
    $arr3 = $this->processSystemOptions('defaults');
    $arr4 = $this->processEmailOptions('defaults');
    $defaults = array_merge($arr1, $arr2, $arr3, $arr4);
    $this->setDefaults($defaults);

    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Submit', array('domain' => 'org.cpt.cpttaxreceipts')),
        'isDefault' => TRUE,
      ),
    ));
    // Set image defaults
    $images = array('receipt_logo', 'receipt_watermark', 'receipt_pdftemplate', 'receipt_pdftemplate_canada');
    foreach ($images as $image) {
      if (CRM_Utils_Array::value($image, $defaults)) {
        $this->assign($image, $defaults[$image]);
        if (!file_exists($defaults[$image])) {
          $this->assign($image.'_class', TRUE);
        }
      }
    }

    parent::buildQuickForm();
  }

  function processOrgOptions($mode) {
    if ( $mode == 'build' ) {
      $this->add('text', 'org_name', ts('Organization Name', array('domain' => 'org.cpt.cpttaxreceipts')));
      $this->add('text', 'org_address_line1', ts('Address Line 1', array('domain' => 'org.cpt.cpttaxreceipts')));
      $this->add('text', 'org_address_line2', ts('Address Line 2', array('domain' => 'org.cpt.cpttaxreceipts')));
      $this->add('text', 'org_tel', ts('Telephone', array('domain' => 'org.cpt.cpttaxreceipts')));
      $this->add('text', 'org_fax', ts('Fax', array('domain' => 'org.cpt.cpttaxreceipts')));
      $this->add('text', 'org_email', ts('Email', array('domain' => 'org.cpt.cpttaxreceipts')));
      $this->add('text', 'org_web', ts('Website', array('domain' => 'org.cpt.cpttaxreceipts')));
      $this->add('text', 'org_charitable_no', ts('Charitable Registration Number', array('domain' => 'org.cpt.cpttaxreceipts')));

      $this->addRule('org_name', 'Enter Organization Name', 'required');
      $this->addRule('org_address_line1', 'Enter Address Line 1', 'required');
      $this->addRule('org_address_line2', 'Enter Address Line 2', 'required');
      $this->addRule('org_tel', 'Enter Telephone', 'required');
      $this->addRule('org_email', 'Enter Email', 'required');
      $this->addRule('org_web', 'Enter Website', 'required');
      $this->addRule('org_charitable_no', 'Enter Charitable Number', 'required');
    }
    else if ( $mode == 'defaults' ) {
      $defaults = array(
        'org_name' => CRM_Core_BAO_Setting::getItem(self::SETTINGS, 'org_name'),
        'org_address_line1' => CRM_Core_BAO_Setting::getItem(self::SETTINGS, 'org_address_line1'),
        'org_address_line2' => CRM_Core_BAO_Setting::getItem(self::SETTINGS, 'org_address_line2'),
        'org_tel' => CRM_Core_BAO_Setting::getItem(self::SETTINGS, 'org_tel'),
        'org_fax' => CRM_Core_BAO_Setting::getItem(self::SETTINGS, 'org_fax'),
        'org_email' => CRM_Core_BAO_Setting::getItem(self::SETTINGS, 'org_email'),
        'org_web' => CRM_Core_BAO_Setting::getItem(self::SETTINGS, 'org_web'),
        'receipt_logo' => CRM_Core_BAO_Setting::getItem(self::SETTINGS, 'receipt_logo'),
        'receipt_watermark' => CRM_Core_BAO_Setting::getItem(self::SETTINGS, 'receipt_watermark'),
        'receipt_pdftemplate' => CRM_Core_BAO_Setting::getItem(self::SETTINGS, 'receipt_pdftemplate'),
        'receipt_pdftemplate_canada' => CRM_Core_BAO_Setting::getItem(self::SETTINGS, 'receipt_pdftemplate_canada'),
        'org_charitable_no' => CRM_Core_BAO_Setting::getItem(self::SETTINGS, 'org_charitable_no'),
      );
      return $defaults;
    }
    else if ( $mode == 'post' ) {
      $values = $this->exportValues();
      CRM_Core_BAO_Setting::setItem($values['org_name'], self::SETTINGS, 'org_name');
      CRM_Core_BAO_Setting::setItem($values['org_address_line1'], self::SETTINGS, 'org_address_line1');
      CRM_Core_BAO_Setting::setItem($values['org_address_line2'], self::SETTINGS, 'org_address_line2');
      CRM_Core_BAO_Setting::setItem($values['org_tel'], self::SETTINGS, 'org_tel');
      CRM_Core_BAO_Setting::setItem($values['org_fax'], self::SETTINGS, 'org_fax');
      CRM_Core_BAO_Setting::setItem($values['org_email'], self::SETTINGS, 'org_email');
      CRM_Core_BAO_Setting::setItem($values['org_web'], self::SETTINGS, 'org_web');
      CRM_Core_BAO_Setting::setItem($values['org_charitable_no'], self::SETTINGS, 'org_charitable_no');
    }

  }

  function processReceiptOptions($mode) {
    if ( $mode == 'build' ) {
      $this->add('text', 'receipt_prefix', ts('Receipt Prefix', array('domain' => 'org.cpt.cpttaxreceipts')));

      $uploadSize = cdntaxreceipts_getCiviSetting('maxFileSize');
      if ($uploadSize >= 8 ) {
        $uploadSize = 8;
      }
      $uploadFileSize = $uploadSize * 1024 * 1024;

      $this->assign('uploadSize', $uploadSize );
      $this->setMaxFileSize( $uploadFileSize );

      $this->addElement('file', 'receipt_logo', ts('Organization Logo', array('domain' => 'org.cpt.cpttaxreceipts')), 'size=30 maxlength=60');
      $this->addUploadElement('receipt_logo');
      $this->addRule( 'receipt_logo', ts('File size should be less than %1 MBytes (%2 bytes)', array(1 => $uploadSize, 2 => $uploadFileSize)), 'maxfilesize', $uploadFileSize, array('domain' => 'org.cpt.cpttaxreceipts') );

      $this->addElement('file', 'receipt_watermark', ts('Watermark Image', array('domain' => 'org.cpt.cpttaxreceipts')), 'size=30 maxlength=60');
      $this->addUploadElement('receipt_watermark');
      $this->addRule( 'receipt_watermark', ts('File size should be less than %1 MBytes (%2 bytes)', array(1 => $uploadSize, 2 => $uploadFileSize)), 'maxfilesize', $uploadFileSize, array('domain' => 'org.cpt.cpttaxreceipts') );

      $this->addElement('file', 'receipt_pdftemplate', ts('PDF Template', array('domain' => 'org.cpt.cpttaxreceipts')), 'size=30 maxlength=60');
      $this->addUploadElement('receipt_pdftemplate');
      $this->addRule( 'receipt_pdftemplate', ts('File size should be less than %1 MBytes (%2 bytes)', array(1 => $uploadSize, 2 => $uploadFileSize)), 'maxfilesize', $uploadFileSize, array('domain' => 'org.cpt.cpttaxreceipts') );

      $this->addElement('file', 'receipt_pdftemplate_canada', ts('PDF Template (Canada)', array('domain' => 'org.cpt.cpttaxreceipts')), 'size=30 maxlength=60');
      $this->addUploadElement('receipt_pdftemplate_canada');
      $this->addRule( 'receipt_pdftemplate_canada', ts('File size should be less than %1 MBytes (%2 bytes)', array(1 => $uploadSize, 2 => $uploadFileSize)), 'maxfilesize', $uploadFileSize, array('domain' => 'org.cpt.cpttaxreceipts') );
    }
    else if ( $mode == 'defaults' ) {
      $defaults = array(
        'receipt_prefix' => CRM_Core_BAO_Setting::getItem(self::SETTINGS, 'receipt_prefix'),
      );
      return $defaults;
    }
    else if ( $mode == 'post' ) {
      $values = $this->exportValues();
      CRM_Core_BAO_Setting::setItem($values['receipt_prefix'], self::SETTINGS, 'receipt_prefix');

      $receipt_logo = $this->getSubmitValue('receipt_logo');
      $receipt_watermark = $this->getSubmitValue('receipt_watermark');
      $receipt_pdftemplate = $this->getSubmitValue('receipt_pdftemplate');
      $receipt_pdftemplate_canada = $this->getSubmitValue('receipt_pdftemplate_canada');

      $config = CRM_Core_Config::singleton( );
      foreach ( array('receipt_logo', 'receipt_watermark', 'receipt_pdftemplate', 'receipt_pdftemplate_canada') as $key ) {
        $upload_file = $this->getSubmitValue($key);
        if (is_array($upload_file)) {
          if ( $upload_file['error'] == 0 ) {
            $filename = $config->customFileUploadDir . CRM_Utils_File::makeFileName($upload_file['name']);
            if (!move_uploaded_file($upload_file['tmp_name'], $filename)) {
              CRM_Core_Error::fatal(ts('Could not upload the file'));
            }
            CRM_Core_BAO_Setting::setItem($filename, self::SETTINGS, $key);
          }
        }
      }
    }
  }

  function processSystemOptions($mode) {
    if ( $mode == 'build' ) {
      $yesno_options = array();
      $yesno_options[] = $this->createElement('radio', NULL, NULL, 'Yes', 1);
      $yesno_options[] = $this->createElement('radio', NULL, NULL, 'No', 0);
      $this->addGroup($yesno_options, 'enable_email', ts('Send receipts by email?', array('domain' => 'org.cpt.cpttaxreceipts')));
      $this->addRule('enable_email', 'Enable or disable email receipts', 'required');
    }
    else if ( $mode == 'defaults' ) {
      $defaults = array(
        'enable_email' => CRM_Core_BAO_Setting::getItem(self::SETTINGS, 'enable_email', NULL, 0),
      );
      return $defaults;
    }
    else if ( $mode == 'post' ) {
      $values = $this->exportValues();
      CRM_Core_BAO_Setting::setItem($values['enable_email'], self::SETTINGS, 'enable_email');
    }
  }

  function processEmailOptions($mode) {
    if ( $mode == 'build' ) {
      $this->add('text', 'email_subject', ts('Email Subject', array('domain' => 'org.cpt.cpttaxreceipts')));
      $this->add('text', 'email_from', ts('Email From', array('domain' => 'org.cpt.cpttaxreceipts')));
      $this->add('text', 'email_archive', ts('Archive Email', array('domain' => 'org.cpt.cpttaxreceipts')));
      $this->addElement('textarea', 'email_message', ts('Email Message', array('domain' => 'org.cpt.cpttaxreceipts')));

      $this->addRule('email_subject', 'Enter email subject', 'required');
      $this->addRule('email_from', 'Enter email from address', 'required');
      $this->addRule('email_message', 'Enter email message', 'required');
    }
    else if ( $mode == 'defaults' ) {
      $subject = ts('Your Tax Receipt', array('domain' => 'org.cpt.cpttaxreceipts'));
      $message = ts('Attached please find your official tax receipt for income tax purposes.', array('domain' => 'org.cpt.cpttaxreceipts'));
      $defaults = array(
        'email_subject' => CRM_Core_BAO_Setting::getItem(self::SETTINGS, 'email_subject', NULL, $subject),
        'email_from' => CRM_Core_BAO_Setting::getItem(self::SETTINGS, 'email_from'),
        'email_archive' => CRM_Core_BAO_Setting::getItem(self::SETTINGS, 'email_archive'),
        'email_message' => CRM_Core_BAO_Setting::getItem(self::SETTINGS, 'email_message', NULL, $message),
      );
      return $defaults;
    }
    else if ( $mode == 'post' ) {
      $values = $this->exportValues();
      CRM_Core_BAO_Setting::setItem($values['email_subject'], self::SETTINGS, 'email_subject');
      CRM_Core_BAO_Setting::setItem($values['email_from'], self::SETTINGS, 'email_from');
      CRM_Core_BAO_Setting::setItem($values['email_archive'], self::SETTINGS, 'email_archive');
      CRM_Core_BAO_Setting::setItem($values['email_message'], self::SETTINGS, 'email_message');
    }
  }

  function postProcess() {
    parent::postProcess();
    $this->processOrgOptions('post');
    $this->processReceiptOptions('post');
    $this->processSystemOptions('post');
    $this->processEmailOptions('post');

    $statusMsg = ts('Your settings have been saved.', array('domain' => 'org.cpt.cpttaxreceipts'));
    CRM_Core_Session::setStatus( $statusMsg, '', 'success' );
  }
}

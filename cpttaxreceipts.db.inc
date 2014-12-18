<?php

/*
 * Functions to save and read data from my table
 */


function cpttaxreceipts_contributions($contactId, $year) {

  $sql = "SELECT contrib.id as contribution_id, contrib.total_amount,
        ifnull(contrib.non_deductible_amount, 0) as non_deductible_amount, contrib.receive_date
    FROM civicrm_contribution contrib
    WHERE contrib.contact_id = $contactId AND YEAR(contrib.receive_date) = $year
    ORDER BY contrib.id";

  $dao = CRM_Core_DAO::executeQuery($sql);
  $contributions = array();

  // Rather than loading each CRM_Contribute_DAO_Contribution object, we'll just
  // transfer the information returned from the query.  This should save a whole
  // lot of database calls versus loading each contribution object individually.

  while ( $dao->fetch() ) {
    if ( cpttaxreceipts_eligibleForReceipt($dao->contribution_id) ) {
      $contributions[] = array(
        'contribution_id' => $dao->contribution_id,
        'total_amount' => $dao->total_amount,
        'non_deductible_amount' => $dao->non_deductible_amount,
        'receive_date' => $dao->receive_date,
      );
    }
  }

  return $contributions;
}

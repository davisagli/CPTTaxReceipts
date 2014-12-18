{* Confirmation of tax receipts  *}
<div class="crm-block crm-form-block crm-contact-task-delete-form-block">
<div class="messages status no-popup">
  <div class="icon inform-icon"></div>
  {ts domain='org.cpt.cpttaxreceipts'}You have selected <strong>{$totalSelectedContributions}</strong> contributions. Of these, <strong>{$receiptTotal}</strong> are eligible to receive tax receipts.{/ts}
</div>
  <table>
    <thead>
      <th>{ts domain='org.cpt.cpttaxreceipts'}{$receiptList.totals.original}Tax Receipt Status{/ts}</th>
      <th>{ts domain='org.cpt.cpttaxreceipts'}{$receiptList.totals.original}Total{/ts}</th>
      <th>{ts domain='org.cpt.cpttaxreceipts'}{$receiptList.totals.original}Email{/ts}</th>
      <th>{ts domain='org.cpt.cpttaxreceipts'}{$receiptList.totals.original}Print{/ts}</th>
    </thead>
    <tr>
      <td>{ts domain='org.cpt.cpttaxreceipts'}Not yet receipted{/ts}</td>
      <td>{$originalTotal}</td>c
      <td>{$receiptCount.original.email}</td>
      <td>{$receiptCount.original.print}</td>
    </tr>
    <tr>
      <td>{ts domain='org.cpt.cpttaxreceipts'}Already receipted<{/ts}/td>
      <td>{$duplicateTotal}</td>
      <td>{$receiptCount.duplicate.email}</td>
      <td>{$receiptCount.duplicate.print}</td>
    </tr>
  </table>
  <p>{$form.receipt_option.original_only.html}<br />
     {$form.receipt_option.include_duplicates.html}</p>
  <p>{ts domain='org.cpt.cpttaxreceipts'}Clicking 'Issue Tax Receipts' will issue the selected tax receipts.
    <strong>This action cannot be undone.</strong> Tax receipts will be logged for auditing purposes,
    and a copy of each receipt will be submitted to the tax receipt archive.{/ts}</p>
  <ul>
  <li>{ts domain='org.cpt.cpttaxreceipts'}Email receipts will be emailed directly to the contributor.{/ts}</li>
  <li>{ts domain='org.cpt.cpttaxreceipts'}Print receipts will be compiled into a file for download.  Please print and mail any receipts in this file.{/ts}</li>
  </ul></p>
  <p>{$form.is_preview.html} {$form.is_preview.label} {ts domain='org.cpt.cpttaxreceipts'}(Generates receipts marked 'preview', but does not issue the receipts.  No logging or emails sent.){/ts}</p>
  <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl"}</div>
</div>

{* Confirmation of tax receipts  *}
<div class="crm-block crm-form-block crm-contact-task-delete-form-block">
<div class="messages status no-popup">
  <div class="icon inform-icon"></div>
  {ts 1=$totalSelectedContacts domain='org.cpt.cpttaxreceipts'}You have selected <strong>%1</strong> contacts. The summary below includes these contacts only.{/ts}
</div>
  <table class="cpttax_summary">
    <thead>
      <th width=30%>{ts domain='org.cpt.cpttaxreceipts'}Select Tax Year{/ts}</th>
      <th width=30%>{ts domain='org.cpt.cpttaxreceipts'}Receipts Outstanding{/ts}</th>
      {if $enable_email}
        <th width=20%>{ts domain='org.cpt.cpttaxreceipts'}Email{/ts}</th>
        <th>Print</th>
      {/if}
    </thead>
    {foreach from=$receiptYears item=year}
      {assign var="key" value="issue_$year"}
      <tr class="{cycle values="odd-row,even-row"}">
        <td>{$form.receipt_year.$key.html}</td>
        <td>{if $receiptCount.$year.total}{$receiptCount.$year.total} ({$receiptCount.$year.contrib} contributions){else}0{/if}</td>
        {if $enable_email}
          <td>{$receiptCount.$year.email}</td>
          <td>{$receiptCount.$year.print}</td>
        {/if}
      </tr>
    {/foreach}
  </table>
  <p>{ts domain='org.cpt.cpttaxreceipts'}Clicking 'Issue Tax Receipts' will issue annual tax receipts for the selected year. Annual tax receipts are a sum
    total of all eligible contributions received from the donor during the selected year.{/ts}</p>
  <p>
  <ul>
  {if $enable_email}
    <li>{ts domain='org.cpt.cpttaxreceipts'}Email receipts will be emailed directly to the contributor.{/ts}</li>
  {/if}
  <li>{ts domain='org.cpt.cpttaxreceipts'}Print receipts will be compiled into a file for download.  Please print and mail any receipts in this file.{/ts}</li>
  </ul>
  </p>
  {if $enable_email}
    <p>{$form.is_preview.html} {$form.is_preview.label} {ts domain='org.cpt.cpttaxreceipts'}(Generates receipts marked 'preview', but does not issue the receipts.  No emails sent.){/ts}</p>
  {/if}
  <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl"}</div>
</div>

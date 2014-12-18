
{if $pdf_file}
{capture assign="file_url"}{crmURL p='civicrm/cpttaxreceipts/view' q="id=$contribution_id&cid=$contact_id&download=1"}{/capture}
<script type="text/javascript">
cj(document).ready(
  function() {ldelim}
    window.location = "{$file_url|replace:'&amp;':'&'}";
  {rdelim}
);
</script>
{/if}

<div class="crm-block crm-content-block crm-contribution-view-form-block">
{if $reissue eq 1 and $receipt}
<h3>{$receipt.receipt_no}</h3>
<table class="crm-info-panel">
    <tr>
        <td class="label">{ts}Receipt No.{/ts}</td>
        <td class="bold">{$receipt.receipt_no}</td>
        <td class="label">{ts}Issued By{/ts}</td>
        <td>{$receipt.uname}</td>
    </tr>
    <tr>
        <td class="label">{ts}Issue Date{/ts}</td>
        <td>{$receipt.issued_on|crmDate}</td>
        <td class="label">{ts}Method{/ts}</td>
        <td>{if $receipt.issue_method eq 'email'}{ts}Email{/ts}{elseif $receipt.issue_method eq 'print'}{ts}Print{/ts}{/if}</td>
    </tr>
    <tr>
        <td class="label">{ts}Type{/ts}</td>
        <td>{ts}{$receipt.display_type}{/ts}</td>
        <td class="label">{ts}IP{/ts}</td>
        <td>{$receipt.ip}</td>
    </tr>
    <tr>
        <td class="label">{ts}Amount{/ts}</td>
        <td class="bold">{$receipt.receipt_amount|crmMoney}</td>
        <td class="label">&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td class="label">{ts}Contribution(s){/ts}</td>
        <td>{foreach from=$receipt_contributions item=id}
              <a href="{crmURL p='civicrm/contact/view/contribution' q="action=view&reset=1&id=$id&cid=$contact_id&context=home"}">{$id}</a>
            {/foreach}
        </td>
        <td class="label">&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
</table>
{/if}

{if $reissue eq 0}
  <h3>{ts domain='org.cpt.cpttaxreceipts'}A tax receipt has not been issued for this contribution.{/ts}</h3>
  {if call_user_func(array('CRM_Core_Permission','check'), 'issue CPT Tax Receipts')}
    <p>{ts domain='org.cpt.cpttaxreceipts'}Click '{$buttonLabel}' to issue a tax receipt for this contribution.
    This action cannot be undone. The tax receipt will be logged for auditing purposes,
    and a copy of the receipt will be submitted to the tax receipt archive.{/ts}</p>
    {if $method eq 'email'}
      <p>{ts domain='org.cpt.cpttaxreceipts'}The receipt will be sent <strong>by email</strong>
      to the contributor ({$receiptEmail}).{/ts}</p>
    {else}
      <p class='status-warning'>{ts domain='org.cpt.cpttaxreceipts'}Please <strong>download and print</strong> the receipt that
      is generated. You will need to send a printed copy to the contributor.{/ts}</p>
    {/if}
  {else}
    <p>{ts domain='org.cpt.cpttaxreceipts'}You do not have sufficient authorization to issue tax receipts.{/ts}</p>
  {/if}
{elseif $reissue eq 1}
  <h3>{ts domain='org.cpt.cpttaxreceipts'}Re-Issue Tax Receipt{/ts}</h3>
  {if call_user_func(array('CRM_Core_Permission','check'), 'issue CPT Tax Receipts')}
    <p>{ts domain='org.cpt.cpttaxreceipts'}Click '{$buttonLabel}' to re-issue a tax receipt for this contribution. The
    tax receipt will be marked 'duplicate' with the same receipt number and amount as
    the original copy.{/ts}</p>
    {if $method eq 'email'}
      <p>{ts domain='org.cpt.cpttaxreceipts'}The receipt will be sent automatically <strong>by email</strong> to the contributor
      ({$receiptEmail}).{/ts}</p>
    {else}
      <p class='status-warning'>{ts domain='org.cpt.cpttaxreceipts'}Please <strong>download and print</strong> the receipt that
      is generated. You will need to send a printed copy to the contributor.{/ts}</p>
    {/if}
  {else}
    <p>{ts domain='org.cpt.cpttaxreceipts'}You do not have sufficient authorization to re-issue tax receipts.{/ts}</p>
  {/if}
{/if}

<div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
</div>

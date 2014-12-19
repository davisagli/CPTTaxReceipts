<div class="crm-block crm-form-block crm-miscellaneous-form-block">

<h3>{ts domain='org.cpt.cpttaxreceipts'}Organization Details{/ts}</h3>

  <table class="form-layout">
    <tbody>
      <tr>
        <td class="label">{$form.org_name.label}</td>
        <td class="content">{$form.org_name.html}
          <p class="description">{ts domain='org.cpt.cpttaxreceipts'}My Charitable Organization{/ts}</p></td>
      </tr>
      <tr>
        <td class="label">{$form.org_address_line1.label}</td>
        <td class="content">{$form.org_address_line1.html}
          <p class="description">{ts domain='org.cpt.cpttaxreceipts'}101 Anywhere Drive{/ts}</p></td>
      </tr>
      <tr>
        <td class="label">{$form.org_address_line2.label}</td>
        <td class="content">{$form.org_address_line2.html}
          <p class="description">{ts domain='org.cpt.cpttaxreceipts'}Toronto ON A1B 2C3{/ts}</p></td>
      </tr>
      <tr>
        <td class="label">{$form.org_tel.label}</td>
        <td class="content">{$form.org_tel.html}
          <p class="description">(555) 555-5555</p></td>
      </tr>
      <tr>
        <td class="label">{$form.org_fax.label}</td>
        <td class="content">{$form.org_fax.html}
          <p class="description">(555) 555-5555</p></td>
      </tr>
      <tr>
        <td class="label">{$form.org_email.label}</td>
        <td class="content">{$form.org_email.html}
          <p class="description">info@my.org</p></td>
      </tr>
      <tr>
        <td class="label">{$form.org_web.label}</td>
        <td class="content">{$form.org_web.html}
          <p class="description">www.my.org</p></td>
      </tr>
      <tr>
        <td class="label">{$form.org_charitable_no.label}</td>
        <td class="content">{$form.org_charitable_no.html}
          <p class="description">10000-000-RR0000</p></td>
      </tr>
    </tbody>
  </table>

<h3>{ts domain='org.cpt.cpttaxreceipts'}Receipt Configuration{/ts}</h3>

  <table class="form-layout">
    <tbody>
      <tr>
        <td class="label">{$form.receipt_prefix.label}</td>
        <td class="content">{$form.receipt_prefix.html}
          <p class="description">{ts domain='org.cpt.cpttaxreceipts'}Receipt numbers are formed by appending the CiviCRM Contribution ID to this prefix. Receipt numbers must be unique within your organization. If you also issue tax receipts using another system, you can use the prefix to ensure uniqueness (e.g. enter 'WEB-' here so all receipts issued through CiviCRM are WEB-00000001, WEB-00000002, etc.){/ts}</p></td>
      </tr>
      <tr>
        <td class="label">{$form.receipt_logo.label}</td>
        <td class="content">{$form.receipt_logo.html}
          <p class="description">{ts domain='org.cpt.cpttaxreceipts'}Logo size: 280x120 pixels; File types allowed: .jpg .png.{/ts}</p>
	  {if $receipt_logo}
	      {if $receipt_logo_class}<span class="crm-error">The file {$receipt_logo} was not found</span>
	      {else}<p class="label">Current {$form.receipt_logo.label}: {$receipt_logo}</p>{/if}
	  {/if}</td>
      </tr>
      <tr>
        <td class="label">{$form.receipt_watermark.label}</td>
        <td class="content">{$form.receipt_watermark.html}
          <p class="description">{ts domain='org.cpt.cpttaxreceipts'}Watermark Image size: 250x250 pixels; File types allowed: .jpg .png.{/ts}</p>
	  {if $receipt_watermark}
	      {if $receipt_watermark_class}<span class="crm-error">The file {$receipt_watermark} was not found</span>
	      {else}<p class="label">Current {$form.receipt_watermark.label}: {$receipt_watermark}</p>{/if}
	  {/if}</td>
      </tr>
      <tr>
        <td class="label">{$form.receipt_pdftemplate.label}</td>
        <td class="content">{$form.receipt_pdftemplate.html}
          <p class="description">{ts domain='org.cpt.cpttaxreceipts'}Upload your own PDF template: .pdf{/ts}</p>
	  {if $receipt_pdftemplate}
	      {if $receipt_pdftemplate_class}<span class="crm-error">The file {$receipt_pdftemplate} was not found</span>
	      {else}<p class="label">Current {$form.receipt_pdftemplate.label}: {$receipt_pdftemplate}</p>{/if}
	  {/if}</td>
      </tr>
    </tbody>
  </table>

<h3>{ts domain='org.cpt.cpttaxreceipts'}System Options{/ts}</h3>

  <table class="form-layout">
    <tbody>
      <tr>
        <td class="label">{$form.enable_email.label}</td>
        <td class="content">{$form.enable_email.html}
          <p class="description">{ts domain='org.cpt.cpttaxreceipts'}If enabled, tax receipts will be sent via email to donors who have an email address on file.{/ts}</p></td>
      </tr>
    </tbody>
  </table>

<h3>{ts domain='org.cpt.cpttaxreceipts'}Email Message{/ts}</h3>

  <table class="form-layout">
    <tbody>
      <tr>
        <td class="label">{$form.email_subject.label}</td>
        <td class="content">{$form.email_subject.html}
          <p class="description">{ts domain='org.cpt.cpttaxreceipts'}Subject of the Email to accompany your Tax Receipt. The receipt number will be appended.{/ts}</p></td>
      </tr>
      <tr>
        <td class="label">{$form.email_from.label}</td>
        <td class="content">{$form.email_from.html}
          <p class="description">{ts domain='org.cpt.cpttaxreceipts'}Address you would like to Email the Tax Receipt from.{/ts}</p></td>
      </tr>
      <tr>
        <td class="label">{$form.email_archive.label}</td>
        <td class="content">{$form.email_archive.html}
          <p class="description">{ts domain='org.cpt.cpttaxreceipts'}Address you would like to Send a copy of the Email containing the Tax Receipt to. This is useful to create an archive.{/ts}</p></td>
      </tr>
      <tr>
        <td class="label">{$form.email_message.label}</td>
        <td class="content">{$form.email_message.html}
          <p class="description">{ts domain='org.cpt.cpttaxreceipts'}Text in the Email to accompany your Tax Receipt.{/ts}</p></td>
      </tr>
    </tbody>
  </table>

{* FOOTER *}
<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>

</div>

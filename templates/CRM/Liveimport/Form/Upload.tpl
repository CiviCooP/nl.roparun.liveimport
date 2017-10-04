{* HEADER *}

{* FIELD EXAMPLE: OPTION 1 (AUTOMATIC LAYOUT) *}

{foreach from=$elementNames item=elementName}
  <div class="crm-section">
    <div class="label">{$form.$elementName.label}</div>
    <div class="content">{$form.$elementName.html}</div>
    <div class="clear"></div>
  </div>
{/foreach}

<h3>{ts}Upload CSV File{/ts}</h3>
<table class="form-layout">
  <tr>
    <td class="label">{$form.uploadFile.label}</td>
    <td>{$form.uploadFile.html}<br />
      <div class="description">{ts}File format must be comma-separated-values (CSV). File must be UTF8 encoded if it contains special characters (e.g. accented letters, etc.).{/ts}</div>
        {ts 1=$uploadSize}Maximum Upload File Size: %1 MB{/ts}
    </td>
  </tr>
</table>
<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>

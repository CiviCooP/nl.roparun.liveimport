<h3>This new page is generated by CRM/Liveimport/Page/UploadResult.php</h3>

{* Example: Display a variable directly *}
<p>The current time is {$currentTime}</p>

<ul>
    {foreach from=$failed item=row}
    <li><b>{$row.roparunid} </b>{$row.message}</li>
    {/foreach}
</ul>

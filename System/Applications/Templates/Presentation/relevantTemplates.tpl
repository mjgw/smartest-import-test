{if count($stylesheets)}  <li><strong>Stylesheets for this template</strong></li>{/if}
{foreach from=$stylesheets item="stylesheet"}
  <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}assets/editAsset?asset_id={$stylesheet.id}'"><i class="fa fa-file-o"></i> {$stylesheet.label}</a></li>
{/foreach}
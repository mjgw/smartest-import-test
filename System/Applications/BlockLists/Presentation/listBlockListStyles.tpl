<script type="text/javascript">
  var stylesList = new Smartest.UI.OptionSet('pageViewForm', 'item_id_input', 'item', 'styles-list');
</script>

<div id="work-area">
  <h3>Blocklist styles</h3>
  <div class="instruction">Blocklists are created in a 'style' - a coordinated series of templates and styling to make the creation of new blocks easy</div>
  
  <form id="pageViewForm" method="get" action="">
    <input type="hidden" name="style_id" value="" id="item_id_input" />
  </form>

  <ul class="options-list" id="styles-list">
    {foreach from=$blocklist_styles item="style"}
    <li>
       <a id="item_{$style.id}" class="option" href="javascript:nothing()" onclick="return stylesList.setSelectedItem('{$style.id}', 'item');" ondblclick="window.location='{$domain}{$section}/editBlockListStyle?style_id={$style.id}'">		 
         <i class="fa fa-tag"></i>{$style.label}</a>
     </li>
    {/foreach}
  </ul>

</div>

<div id="actions-area">
  <ul class="actions-list" id="item-specific-actions" style="display:none">
    <li><strong>Selected style</strong></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="stylesList.workWithItem('editBlockListStyle');"><i class="fa fa-pencil"></i> Edit this style</a></li>
  </ul>
</div>
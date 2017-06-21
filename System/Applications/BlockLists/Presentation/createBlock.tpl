<div id="work-area">
  <form action="{$domain}ajax:blocklists/insertBlock" method="post" id="insert-block-form">
    
    <input type="hidden" name="blocklist_id" value="{$blocklist.id}" />
    <input type="hidden" name="style_id" value="{$style.id}" />
    
    <div class="edit-form-row">
      <div class="form-section-label">Blocklist style</div>
      {$style.label}
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Block type</div>
      <select name="template_id">
  {foreach from=$style_templates item="template"}
        <option value="{$template.id}">{$template.label}</option>
  {/foreach}    
      </select>
    </div>
    
    <div class="edit-form-row" id="block-title-trigger-row">
      <div class="form-section-label">   </div>
      <input type="button" value="Add title" id="add-title-button" />
    </div>
    
    <div class="edit-form-row" id="block-title-row" style="display:none">
      <div class="form-section-label">Block title</div>
      <input type="text" name="block_title" id="block-title-input" />
    </div>
    
    <div class="buttons-bar"><img src="{$domain}Resources/System/Images/ajax-loader.gif" alt="" id="saving-gif" style="display:none" /><input type="button" value="Save new block" id="insert-block-button" /></div>
  </form>
  
    <script type="text/javascript">
    var blocklist_id = {$blocklist.id};
    var style_id = {$style.id};
  {literal}
    $('insert-block-button').observe('click', function(){
      $('saving-gif').show();
      $('insert-block-form').request({
        onComplete: function(){
          new Ajax.Updater('blocks-container', sm_domain+'ajax:blocklists/listOrderableBlocksForEditor', {
            parameters: {'blocklist_id': blocklist_id},
          });
          MODALS.hideViewer();
        }
      });
    });
    
    $('add-title-button').observe('click', function(){
      $('block-title-trigger-row').hide();
      $('block-title-row').show();
      $('block-title-input').focus();
    });
    
  {/literal}
    </script>
  
</div>
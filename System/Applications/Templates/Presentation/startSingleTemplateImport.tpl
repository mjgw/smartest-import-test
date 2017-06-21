<div id="work-area">
  
  <ul class="tabset">
    <li{if $method == "startSingleTemplateImport"} class="current"{/if}><a href="{$domain}templates/startSingleTemplateImport?{if $type_specified}type={$template_type.id}&amp;{/if}{if $add_to_group}add_to_group_id={$add_to_group.id}&amp;{/if}{if $blocklist_style}style_id={$blocklist_style.id}&amp;{/if}">Import template</a></li>
    <li{if $method == "addTemplate"} class="current"{/if}><a href="{$domain}templates/addTemplate?{if $type_specified}type={$template_type.id}&amp;{/if}{if $add_to_group}add_to_group_id={$add_to_group.id}&amp;{/if}{if $blocklist_style}style_id={$blocklist_style.id}&amp;{/if}">Create template</a></li>
  </ul>
  
  <h3>Import template</h3>
  
  {if $type_specified}
  
  <form action="{$domain}templates/finishSingleTemplateImport" method="post" id="template-import-form">
    
  <input type="hidden" name="type" value="{$template_type.id}" />
  {if $add_to_group}<input type="hidden" name="add_to_group_id" value="{$add_to_group.id}" />{/if}
  {if $blocklist_style}<input type="hidden" name="blocklist_style_id" value="{$blocklist_style.id}" />{/if}
  
  <div class="edit-form-row">
    <div class="form-section-label">Template name</div>
    <input type="text" name="new_template_label" id="new-template-label" value="New template" class="unfilled" />
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Which file would you like to import?</div>
    <div style="padding:5px 0 10px 0">Unimported template files in <code>Presentation/Layouts/</code></div>
    <div style="height:250px;overflow:scroll;border:1px solid #ccc;padding:5px">
  {foreach from=$potential_templates item="potential_template"}
  <input type="radio" name="chosen_file" value="{$potential_template}" class="select-template-radio" id="{$potential_template|slug}" />&nbsp;<label for="{$potential_template|slug}"><code>{$potential_template}</code></label><br />
  {/foreach}
    </div>
  </div>
  
  <div class="edit-form-row">
    <div class="buttons-bar">
      <input type="button" value="Cancel" onclick="cancelForm();">
      <input type="submit" value="Import" disabled="disabled" id="submit-button" />
    </div>
  </div>
  
  </form>
  
    <script type="text/javascript">
  
    var itemNameFieldDefaultValue = 'New template';
    var preventDefaultValue = true;
  {literal}
    document.observe('dom:loaded', function(){

        $('new-template-label').observe('focus', function(){
            if(($('new-template-label').getValue() == itemNameFieldDefaultValue)|| $('new-template-label').getValue() == ''){
                $('new-template-label').removeClassName('unfilled');
                $('new-template-label').setValue('');
            }
        });

        $('new-template-label').observe('blur', function(){
            if(($('new-template-label').getValue() == itemNameFieldDefaultValue) || $('new-template-label').getValue() == ''){
                $('new-template-label').addClassName('unfilled');
                $('new-template-label').setValue(itemNameFieldDefaultValue);
            }else{
                $('new-template-label').removeClassName('error');
            }
        });

        $('template-import-form').observe('submit', function(e){

            if(($('new-template-label').getValue() == itemNameFieldDefaultValue) || $('new-template-label').getValue() == ''){
                $('new-template-label').addClassName('error');
                e.stop();
            }

        });
      
        $$('input.select-template-radio').each(function(radio){
          radio.observe('click', function(){
            $('submit-button').disabled = false;
          });
        });

    });
  {/literal}
    </script>
  
  {else}
  
  Require type choice
  
  {/if}
  
</div>
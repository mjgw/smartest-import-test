<script type="text/javascript">
var customVarName = false;

{literal}
function setVarName(){
	if(document.getElementById('itemproperty_varname').value.length < 1){customVarName = false}
	
	var propertyName = document.getElementById('itemproperty_name').value;
	
	if(!customVarName){
		document.getElementById('itemproperty_varname').value = smartest.toVarName(propertyName);
	}
}
{/literal}
</script>

<div id="work-area">

<h3>Add a property to model</h3>

	{if !$auto_class_file_writable || !$auto_class_dir_writable}
	<div class="warning">
		{if !$auto_class_file_writable && !$auto_class_dir_writable}
		In order for changes made here to fully take effect, the following file locations need to be made writable:
		<ul class="file-list">
			<li><code>{$auto_class_file}</code></li>
			<li><code>{$auto_class_dir}</code></li>
		</ul>
		{elseif !$auto_class_file_writable}
		In order for changes made here to fully take effect, the file <code>{$auto_class_file}</code> needs to be writable by the web server.
		{elseif !$auto_class_dir_writable}
		In order for changes made here to fully take effect, the directory <code>{$auto_class_dir}</code> needs to be writable by the web server.
		{/if}
	</div>
	{/if}

<div class="instruction">You will be adding a new property to the model "{$model.plural_name}".</div>
  
  <form id="type_chooser" action="{$domain}{$section}/addPropertyToClass" method="get">
    
    <input type="hidden" name="class_id" value="{$model.id}" />
    <input type="hidden" name="continue" value="{$continue}" />
    
    <div class="edit-form-row">
      <div class="form-section-label">Choose which type of property you want to add</div>
      <select name="itemproperty_datatype" id='itemproperty_datatype' onchange="$('type_chooser').submit()">
        <option value="">Choose a type...</option>
{foreach from=$data_types item="data_type"}
  	    <option value="{$data_type.id}"{if $data_type.id==$property.datatype} selected="selected"{/if}>{$data_type.label}</option>
{/foreach}
      </select>
      {if $show_full_form}<p style="margin:10px;font-size:11px;margin:3px 0 0 0">{$type_description}</p>{/if}
    </div>
    
  </form>
  
  {if $show_full_form}
  
  <form action="{$domain}{$section}/insertItemClassProperty" method="post" id="new-property-form">
    
    <input type="hidden" name="class_id" value="{$model.id}" />
    <input type="hidden" name="itemproperty_datatype" value="{$property.datatype}" />

    <div class="edit-form-row">
      <div class="form-section-label">Property name</div>
      <input type="text" value="" name="itemproperty_name" id="itemproperty_name" placeholder="Enter property name..." />
      <div class="form-hint">Property names must be three chars or longer and start with a letter.</div>
      <div class="edit-form-sub-row">
        <span id="name-ok" style="display:none" class="feedback-ok"><i class="fa fa-check-circle"> </i> <span id="name-ok-label"></span></span>
        <span id="name-invalid" style="display:none" class="feedback-bad"><i class="fa fa-times"> </i> <span id="name-error-label"></span></span>
      </div>
    </div>
    
    <script type="text/javascript">
    {literal}
    var checkingTimer;
    
    var checkPropertyName = function(){
      console.log('checking property name '+$F('itemproperty_name'));
      new Ajax.Request(sm_domain+'ajax:datamanager/checkNewItemPropertyName', {
        parameters: 'name='+$F('itemproperty_name'),
        onSuccess: function(response){
          $('primary-ajax-loader').hide();
          if(response.responseJSON.permitted){
            $('save-button').disabled = false;
            $('name-ok').show();
            $('name-invalid').hide();
            $('name-ok-label').update(response.responseJSON.reason);
          }else{
            $('save-button').disabled = true;
            $('name-error-label').update(response.responseJSON.reason);
            $('name-invalid').show();
            $('name-ok').hide();
          }
        }
      });
    }
    
    var deactivateChecker = function(){
      $('save-button').disabled = true;
      $('name-invalid').hide();
      $('name-ok').hide();
    }
    
    $('itemproperty_name').observe('keyup', function(kevt){
      
      if (kevt.keyCode == Event.KEY_RETURN){
          kevt.stop();
      }
      
      if($F('itemproperty_name').charAt(0)){ // If the user has entered a value
        clearTimeout(checkingTimer);
        $('primary-ajax-loader').show();
        checkingTimer = setTimeout(checkPropertyName, 200);
      }else{
        deactivateChecker();
      }
      
    });
    
    $('itemproperty_name').observe('blur', function(evt){
      if($F('itemproperty_name').charAt(0)){ // If the user has entered a value
        checkPropertyName();
      }else{
        deactivateChecker();
      }
    });
    
    {/literal}
    </script>
    
{if $foreign_key_filter_select}
      {include file=$filter_select_template}
{/if}
    
    <div class="edit-form-row">
      <div class="form-section-label">Requirement</div>
      <input type="checkbox" name="itemproperty_required" id="is-required" value="TRUE" /> <label for="is-required">Check here if values for this property will be required</label>
    </div>
    
    <div class="edit-form-row">
      <div class="buttons-bar">
        Continue to: <select name="continue"><option value="PROPERTIES"{if $continue == "PROPERTIES"} selected="selected"{/if}>View other properties of model {$model.name}</option><option value="NEW_PROPERTY"{if $continue == "NEW_PROPERTY"} selected="selected"{/if}>Add another property to model {$model.name}</option></select>
        <input type="button" value="Cancel" onclick="window.location='{$domain}{$section}/getItemClassProperties?class_id={$model.id}';" />
        <input type="submit" value="Add property" disabled="disabled" id="save-button" />
      </div>
    </div>

  </form>
  
  <script type="text/javascript">
{literal}
    
    
    
{/literal}
  </script>
  
  {else}
  
  <div class="edit-form-row">
    <div class="buttons-bar">
      <input type="button" value="Cancel" onclick="window.location='{$domain}{$section}/getItemClassProperties?class_id={$model.id}';" />
    </div>
  </div>
  
  {/if}

</div>
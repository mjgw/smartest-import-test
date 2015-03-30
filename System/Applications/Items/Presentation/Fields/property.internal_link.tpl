{capture name="name" assign="name"}item[{$property.id}]{/capture}
{capture name="property_id" assign="property_id"}item_property_{$property.id}{/capture}

<ul class="round-buttons-list" id="{$property_id}-type-options">
  <li><a data-option="page" href="#page"{if !$value.empty && $value.namespace == 'page'} class="selected"{/if}><i class="fa fa-file-text"></i></a></li>
  <li><a data-option="item" href="#item"{if !$value.empty && $value.namespace == 'item'} class="selected"{/if}><i class="fa fa-cube"></i></a></li>
  <li><a data-option="download" href="#download"{if !$value.empty && $value.namespace == 'download'} class="selected"{/if}><i class="fa fa-download"></i></a></li>
  <li><a data-option="tag" href="#tag"{if !$value.empty && $value.namespace == 'tag'} class="selected"{/if}><i class="fa fa-tag"></i></a></li>
  <li><a data-option="user" href="#user"{if !$value.empty && $value.namespace == 'user'} class="selected"{/if}><i class="flaticon solid user-3"></i></a></li>
  <li><a data-option="none" href="#clear"><i class="fa fa-times"></i></a></li>
</ul>

<input type="hidden" name="{$name}" id="{$property_id}" value="{if !$value.empty}{$value.storable_format}{/if}" data-linktype="{if $value && $value.namespace}{$value.namespace}{/if}" data-target-id="{if $value && $value.target_id}{if $value.namespace == 'user'}{$value.target.username}{else}{$value.target_id}{/if}{/if}" />

<div id="{$property_id}-page-chooser" class="{$property_id}-chooser edit-form-sub-row"{if $value.empty || $value.namespace != 'page'} style="display:none"{/if}>
  <div class="form-section-label-full">Choose a page</div>
  <select name="" id="{$property_id}-page-select" class="id-select">
{foreach from=$_site._admin_normal_pages_list item="page"}
    {if $page.info.type == "NORMAL"}<option value="{$page.info.id}"{if !$value.empty && $value.namespace == 'page' && $value.target_id == $page.info.id} selected="selected"{/if}>{$page.info.title}</option>{/if}
{/foreach}
  </select>
</div>

<div id="{$property_id}-item-chooser" class="{$property_id}-chooser edit-form-sub-row"{if $value.empty || $value.namespace != 'item'} style="display:none"{/if}>
  <div class="form-section-label-full">Choose an item</div>
  <i class="fa fa-cube" style="font-size:1.5em;color:#aaa"></i> <input type="text" name="item_selector" value="{if !$value.empty && $value.namespace == 'item'}{$value.target.name}{/if}" id="{$property_id}-item-input" />
  <input type="hidden" name="{$property_id}-item-hidden-id-input" id="{$property_id}-item-hidden-id-input" value="3" />
  <div id="{$property_id}-item_autocomplete_choices" class="autocomplete"></div>
</div>

<div id="{$property_id}-download-chooser" class="{$property_id}-chooser edit-form-sub-row"{if $value.empty || $value.namespace != 'download'} style="display:none"{/if}>
  <div class="form-section-label-full">Choose a file to be downloaded</div>
  <i class="fa fa-file-image-o" style="font-size:1.5em;color:#aaa"></i> <input type="text" name="file_selector" value="{if !$value.empty && $value.namespace == 'download'}{$value.target.label}{/if}" id="{$property_id}-file-input" />
  <input type="hidden" name="{$property_id}-file-hidden-id-input" id="{$property_id}-file-hidden-id-input" value="3" />
  <div id="{$property_id}-download_autocomplete_choices" class="autocomplete"></div>
</div>

<div id="{$property_id}-tag-chooser" class="{$property_id}-chooser edit-form-sub-row"{if $value.empty || $value.namespace != 'tag'} style="display:none"{/if}>
  <div class="form-section-label-full">Choose a tag</div>
  <select name="" id="{$property_id}-tag-select" class="id-select">
{foreach from=$system_data_info.tags item="tag"}
    <option value="{$tag.id}"{if !$value.empty && $value.namespace == 'tag' && $value.target_id == $tag.id} selected="selected"{/if}>{$tag.label}</option>
{/foreach}
  </select>
</div>

<div id="{$property_id}-user-chooser" class="{$property_id}-chooser edit-form-sub-row"{if $value.empty || $value.namespace != 'user'} style="display:none"{/if}>
  <div class="form-section-label-full">Choose a user</div>
  <select name="" id="{$property_id}-user-select" class="id-select">
{foreach from=$system_data_info.system_users item="user"}
    <option value="{$user.username}"{if !$value.empty && $value.namespace == 'user' && $value.target_id == $user.id} selected="selected"{/if}>{$user.full_name}</option>
{/foreach}
  </select>
</div>

<script type="text/javascript">
(function(propertyId){ldelim}
{literal}

  var currentLinkType = $(propertyId).readAttribute('data-linktype');

  $$('#'+propertyId+'-type-options li a').each(function(el){
    el.observe('click', function(evt){
      
      var selectedlinkType = el.readAttribute('data-option');
      evt.stop();
      
      if(selectedlinkType == 'none'){
        
        $$('#'+propertyId+'-type-options li a').each(function(btn){
          btn.removeClassName('selected');
        });
        
        $$('div.'+propertyId+'-chooser').each(function(chooser){
          chooser.blindUp({duration: 0.3});
        });
        
        $(propertyId).value = '';
        
      }else{
      
        // Update buttons
        $$('#'+propertyId+'-type-options li a').each(function(btn){
          btn.removeClassName('selected');
        });
        el.addClassName('selected');
      
        // Show the right chooser
        $$('div.'+propertyId+'-chooser').each(function(chooser){
          chooser.hide();
        });
        
        if($(propertyId+'-'+selectedlinkType+'-chooser')){
          $(propertyId+'-'+selectedlinkType+'-chooser').show();
        }
        
        if(selectedlinkType == 'download'){
          $(propertyId+'-file-input').focus();
          $(propertyId).writeAttribute({'data-target-id':$(propertyId+'-file-hidden-id-input').value});
        }else if(selectedlinkType == 'item'){
          $(propertyId+'-item-input').focus();
          $(propertyId).writeAttribute({'data-target-id':$(propertyId+'-item-hidden-id-input').value});
        }else{
          $(propertyId).writeAttribute({'data-target-id':$(propertyId+'-'+selectedlinkType+'-select').value});
        }
        
        $(propertyId).writeAttribute({'data-linktype':selectedlinkType});
        currentLinkType = selectedlinkType;
        
        // fire an event to allow the input's value to be changed
        $(propertyId).fire('needs:update');
      
      }
      
    });
    
  });
  
  // set up change events on select menus
  $$('div.'+propertyId+'-chooser select.id-select').each(function(sel){
    sel.observe('change', function(e){
      $(propertyId).writeAttribute({'data-target-id':sel.value});
      $(propertyId).fire('needs:update');
    });
  });
  
  // Set up events on autocompleters
  new Ajax.Autocompleter(propertyId+'-item-input', propertyId+"-item_autocomplete_choices", sm_domain+"ajax:datamanager/linkableItemTextSearch", {
      paramName: "query", 
      minChars: 2,
      delay: 50,
      width: 300,
      afterUpdateElement : function(text, li) {
        var bits = li.id.split('-');
        $(propertyId+'-item-hidden-id-input').value = bits[1];
        $(propertyId).writeAttribute({'data-target-id':bits[1]});
        $(propertyId).fire('needs:update');
      }
  });
  
  new Ajax.Autocompleter(propertyId+'-file-input', propertyId+"-download_autocomplete_choices", sm_domain+"ajax:assets/assetSearch", {
      paramName: "query", 
      minChars: 3,
      delay: 50,
      width: 300,
      parameters: 'limit=other,embedded',
      afterUpdateElement : function(text, li) {
        var bits = li.id.split('-');
        $(propertyId+'-file-hidden-id-input').value = bits[1];
        $(propertyId).writeAttribute({'data-target-id':bits[1]});
        $(propertyId).fire('needs:update');
      }
  });
  
  
  // Code to update the actual value of the <select>
  $(propertyId).observe('needs:update', function(){
    var newValue = $(propertyId).readAttribute('data-linktype')+':'+$(propertyId).readAttribute('data-target-id');
    $(propertyId).value = newValue;
  });
  
{/literal}
{rdelim})('{$property_id}');
</script>
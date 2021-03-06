<ul class="round-buttons-list" id="{$_input_data.id}-type-options">
  <li><a data-option="page" href="#page"{if !$_input_data.value.empty && $_input_data.value.namespace == 'page'} class="selected"{/if}><i class="fa fa-file-text"></i></a></li>
  <li><a data-option="item" href="#item"{if !$_input_data.value.empty && $_input_data.value.namespace == 'item'} class="selected"{/if}><i class="fa fa-cube"></i></a></li>
  <li><a data-option="download" href="#download"{if !$_input_data.value.empty && $_input_data.value.namespace == 'download'} class="selected"{/if}><i class="fa fa-download"></i></a></li>
  <li><a data-option="tag" href="#tag"{if !$_input_data.value.empty && $_input_data.value.namespace == 'tag'} class="selected"{/if}><i class="fa fa-tag"></i></a></li>
  <li><a data-option="user" href="#user"{if !$_input_data.value.empty && $_input_data.value.namespace == 'user'} class="selected"{/if}><i class="flaticon solid user-3"></i></a></li>
  <li><a data-option="none" href="#clear"><i class="fa fa-times"></i></a></li>
</ul>

<input type="hidden" name="{$_input_data.name}" id="{$_input_data.id}" value="{if !$_input_data.value.empty}{$_input_data.value.storable_format}{/if}" data-linktype="{if $_input_data.value && $_input_data.value.namespace}{$_input_data.value.namespace}{/if}" data-target-id="{if $_input_data.value && $_input_data.value.target_id}{if $_input_data.value.namespace == 'user'}{$_input_data.value.target.username}{else}{$_input_data.value.target_id}{/if}{/if}" />

<div id="{$_input_data.id}-page-chooser" class="{$_input_data.id}-chooser edit-form-sub-row"{if $_input_data.value.empty || $_input_data.value.namespace != 'page'} style="display:none"{/if}>
  <div class="form-section-label-full">Choose a page</div>
  <select name="" id="{$_input_data.id}-page-select" class="id-select">
{foreach from=$_site._admin_normal_pages_list item="page"}
    {if $page.info.type == "NORMAL"}<option value="{$page.info.id}"{if !$_input_data.value.empty && $_input_data.value.namespace == 'page' && $_input_data.value.target_id == $page.info.id} selected="selected"{/if}>
      {* for $foo=1 to 3}
          <li>{$foo}</li>
      {/for *}
      +{section name="dashes" loop=$page.treeLevel}-{/section} {$page.info.title}</option>{/if}
{/foreach}
  </select>
</div>

<div id="{$_input_data.id}-item-chooser" class="{$_input_data.id}-chooser edit-form-sub-row"{if $_input_data.value.empty || $_input_data.value.namespace != 'item'} style="display:none"{/if}>
  <div class="item-chooser-value" id="{$_input_data.id}-item-chooser-value" style="display:{if !$_input_data.value.empty && $_input_data.value.namespace == 'item'}block{else}none{/if}">
    <p><i class="fa fa-cube" style="font-size:1.5em;color:#aaa"></i> <span id="{$_input_data.id}-item-chooser-value-label">{if !$_input_data.value.empty && $_input_data.value.namespace == 'item'}{$_input_data.value.target.name}{else}<em>No item selected.</em>{/if}</span> <a href="#change-value" id="{$_input_data.id}-item-chooser-value-activate" class="button small">Change</a></p>
  </div>
  <div class="item-chooser-input" id="{$_input_data.id}-item-chooser-input" style="display:{if $_input_data.value.empty || $_input_data.value.namespace != 'item'}block{else}none{/if}">
    <div class="form-section-label-full">Choose an item</div>
    <i class="fa fa-cube" style="font-size:1.5em;color:#aaa"></i> <input type="text" name="item_selector" value="{if !$_input_data.value.empty && $_input_data.value.namespace == 'item'}{$_input_data.value.target.name}{/if}" id="{$_input_data.id}-item-input" />
    <br /><span class="form-hint">To choose an item, simply begin entering its name in the box above</span>
    <input type="hidden" name="{$_input_data.id}-item-hidden-id-input" id="{$_input_data.id}-item-hidden-id-input" value="3" />
    <div id="{$_input_data.id}-item_autocomplete_choices" class="autocomplete"></div>
  </div>
</div>

<div id="{$_input_data.id}-download-chooser" class="{$_input_data.id}-chooser edit-form-sub-row"{if $_input_data.value.empty || $_input_data.value.namespace != 'download'} style="display:none"{/if}>
  <div class="item-chooser-value" id="{$_input_data.id}-file-chooser-value" style="display:{if !$_input_data.value.empty && $_input_data.value.namespace == 'download'}block{else}none{/if}">
    <p><i class="fa fa-download" style="font-size:1.5em;color:#aaa"></i> <span id="{$_input_data.id}-file-chooser-value-label">{if !$_input_data.value.empty && $_input_data.value.namespace == 'download'}{$_input_data.value.target.label|summary:"80"}{else}<em>No file selected.</em>{/if}</span> <a href="#change-value" id="{$_input_data.id}-file-chooser-value-activate" class="button small">Change</a></p>
  </div>
  <div class="item-chooser-input" id="{$_input_data.id}-file-chooser-input" style="display:{if $_input_data.value.empty || $_input_data.value.namespace != 'download'}block{else}none{/if}">
    <div class="form-section-label-full">Choose a file to be downloaded</div>
    <i class="fa fa-file-image-o" style="font-size:1.5em;color:#aaa"></i> <input type="text" name="file_selector" value="{if !$_input_data.value.empty && $_input_data.value.namespace == 'download'}{$_input_data.value.target.label}{/if}" id="{$_input_data.id}-file-input" />
    <input type="hidden" name="{$_input_data.id}-file-hidden-id-input" id="{$_input_data.id}-file-hidden-id-input" value="3" />
    <div id="{$_input_data.id}-download_autocomplete_choices" class="autocomplete"></div>
  </div>
</div>

<div id="{$_input_data.id}-tag-chooser" class="{$_input_data.id}-chooser edit-form-sub-row"{if $_input_data.value.empty || $_input_data.value.namespace != 'tag'} style="display:none"{/if}>
  <div class="form-section-label-full">Choose a tag</div>
  <select name="" id="{$_input_data.id}-tag-select" class="id-select">
{foreach from=$system_data_info.tags item="tag"}
    <option value="{$tag.id}"{if !$_input_data.value.empty && $_input_data.value.namespace == 'tag' && $_input_data.value.target_id == $tag.id} selected="selected"{/if}>{$tag.label}</option>
{/foreach}
  </select>
</div>

<div id="{$_input_data.id}-user-chooser" class="{$_input_data.id}-chooser edit-form-sub-row"{if $_input_data.value.empty || $_input_data.value.namespace != 'user'} style="display:none"{/if}>
  <div class="form-section-label-full">Choose a user</div>
  <select name="" id="{$_input_data.id}-user-select" class="id-select">
{foreach from=$system_data_info.system_users item="user"}
    <option value="{$user.username}"{if !$_input_data.value.empty && $_input_data.value.namespace == 'user' && $_input_data.value.target_id == $user.id} selected="selected"{/if}>{$user.full_name}</option>
{/foreach}
  </select>
</div>

<script type="text/javascript">
(function(propertyId){ldelim}
{literal}

  var currentLinkType = $(propertyId).readAttribute('data-linktype');
  var itemAutocompleteActive = false;
  var assetAutocompleteActive = false;

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
          if(!$(propertyId+'-file-chooser-input').visible()){
            $(propertyId+'-file-chooser-value').hide();
            $(propertyId+'-file-chooser-input').show();
            $(propertyId+'-file-input').activate();
            $(propertyId).writeAttribute({'data-target-id':$(propertyId+'-file-hidden-id-input').value});
          }
        }else if(selectedlinkType == 'item'){
          if(!$(propertyId+'-item-chooser-input').visible()){
            $(propertyId+'-item-chooser-value').hide();
            $(propertyId+'-item-chooser-input').show();
            $(propertyId+'-item-input').activate();
            $(propertyId).writeAttribute({'data-target-id':$(propertyId+'-item-hidden-id-input').value});
          }
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
        // console.log(li);
        if(li.id == 'itemOption-nothing'){
          
        }else{
          var bits = li.id.split('-');
          itemAutocompleteActive = false;
          $(propertyId+'-item-hidden-id-input').value = bits[1];
          $(propertyId).writeAttribute({'data-target-id':bits[1]});
          $(propertyId).fire('needs:update');
          $(propertyId+'-item-input').value = li.readAttribute('data-fullname');
          $(propertyId+'-item-chooser-value').show();
          $(propertyId+'-item-chooser-input').hide();
          $(propertyId+'-item-chooser-value-label').update(li.readAttribute('data-fullname'));
        }
      }
  });
  
  new Ajax.Autocompleter(propertyId+'-file-input', propertyId+"-download_autocomplete_choices", sm_domain+"ajax:assets/assetSearch", {
      paramName: "query", 
      minChars: 3,
      delay: 50,
      width: 300,
      parameters: 'limit=other,embedded',
      afterUpdateElement : function(text, li) {
        if(li.id == 'assetOption-nothing'){
          
        }else{
          var bits = li.id.split('-');
          assetAutocompleteActive = false;
          $(propertyId+'-file-hidden-id-input').value = bits[1];
          $(propertyId).writeAttribute({'data-target-id':bits[1]});
          $(propertyId).fire('needs:update');
          // $(propertyId+'-file-input').value = li.readAttribute('data-fullname');
          $(propertyId+'-file-chooser-value').show();
          $(propertyId+'-file-chooser-input').hide();
          $(propertyId+'-file-chooser-value-label').update(li.readAttribute('data-fullname'));
        }
      }
  });
  
  $(propertyId+'-item-chooser-value-activate').observe('click', function(aclick){
    aclick.stop();
    $(propertyId+'-item-chooser-value').hide();
    $(propertyId+'-item-chooser-input').show();
    $(propertyId+'-item-input').activate();
  });
  
  $(propertyId+'-file-chooser-value-activate').observe('click', function(aclick){
    aclick.stop();
    $(propertyId+'-file-chooser-value').hide();
    $(propertyId+'-file-chooser-input').show();
    $(propertyId+'-file-input').activate();
  });
  
  $(propertyId+'-item-input').observe('keydown', function(kevt){
    if(kevt.keyCode == 13){
      kevt.stop();
      $(propertyId+'-item-chooser-value').show();
      $(propertyId+'-item-chooser-input').hide();
      itemAutocompleteActive = false;
    }else{
      itemAutocompleteActive = true;
    }
  });
  
  $(propertyId+'-file-input').observe('keydown', function(kevt){
    if(kevt.keyCode == 13){
      kevt.stop();
      $(propertyId+'-file-chooser-value').show();
      $(propertyId+'-file-chooser-input').hide();
      assetAutocompleteActive = false;
    }else{
      assetAutocompleteActive = true;
    }
  });
  
  $(propertyId+'-item-input').observe('blur', function(kevt){
    setTimeout(function(){
      if(!itemAutocompleteActive){
        kevt.stop();
        $(propertyId+'-item-chooser-value').show();
        $(propertyId+'-item-chooser-input').hide();
        itemAutocompleteActive = false;
      }
    }, 50);
  });
  
  $(propertyId+'-file-input').observe('blur', function(kevt){
    setTimeout(function(){
      if(!assetAutocompleteActive){
        kevt.stop();
        $(propertyId+'-file-chooser-value').show();
        $(propertyId+'-file-chooser-input').hide();
        assetAutocompleteActive = false;
      }
    }, 50);
  });
  
  // Code to update the actual value of the <select>
  $(propertyId).observe('needs:update', function(){
    var newValue = $(propertyId).readAttribute('data-linktype')+':'+$(propertyId).readAttribute('data-target-id');
    $(propertyId).value = newValue;
  });
  
{/literal}
{rdelim})('{$_input_data.id}');
</script>
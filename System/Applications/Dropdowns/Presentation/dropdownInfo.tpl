<div id="work-area">

  {load_interface file="edit_dropdown_tabs.tpl"}
  <h3>Dropdown menu: <span class="light">{$dropdown.label}</span></h3>

  <form id="pageViewForm" method="post" action="{$domain}{$section}/updateDropDown">
  
    <input type="hidden" name="dropdown_id" value="{$dropdown.id}" />
  
    <div id="edit-form-layout">
    
      <div class="edit-form-row">
        <div class="form-section-label">Label </div>
        <input type="text" name="dropdown_label" value="{$dropdown.label}" />
      </div>
      
      <div class="edit-form-row">
        <div class="form-section-label">Name </div>
        <input type="text" name="dropdown_name" value="{$dropdown.name}" />
      </div>
      
      <div class="edit-form-row">
        <div class="form-section-label">Type</div>
{if $allow_datatype_change}
        <select name="dropdown_datatype">
{foreach from=$types item="datatype"}
          <option value="{$datatype.id}"{if $datatype.id == $dropdown.datatype} selected="selected"{/if}>{$datatype.label}</option>
{/foreach}
        </select>
{else}
        {$dropdown.datatype}
{/if}
      </div>
      
      <div class="edit-form-row">
        <div class="form-section-label">Language </div>
          <select name="dropdown_language">
{foreach from=$_languages item="lang" key="langcode"}
            <option value="{$langcode}"{if $dropdown.language == $langcode} selected="selected"{/if}>{$lang.label}</option>
{/foreach}
          </select>
      </div>
      
      <div class="edit-form-row">
        <div class="buttons-bar">
          <input type="submit"  value="Save changes">
        </div>
      </div>

    </div>

  </form>

</div>

<div id="actions-area">
  <ul class="actions-list" id="value-specific-actions" style="display:none">
    <li><b>Selected value</b></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="workWithItem('editDropDownValue');"><img border="0" src="{$domain}Resources/Icons/pencil.png"> Edit value</a></li>
{if $dropdown.num_options > 1}
    <li class="permanent-action"><a href="{dud_link}" onclick="workWithItem('moveDropDownValueUp');"><img border="0" src="{$domain}Resources/Icons/arrow_up.png"> Move value up</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="workWithItem('moveDropDownValueDown');"><img border="0" src="{$domain}Resources/Icons/arrow_down.png"> Move value down</a></li>
{/if}
    <li class="permanent-action"><a href="{dud_link}" onclick="{literal}if(selectedPage && confirm('Are you sure you want to delete this page?')){workWithItem('deleteDropDownValue');}{/literal}"><img border="0" src="{$domain}Resources/Icons/package_delete.png"> Delete value</a></li>
  </ul>
  
  {load_interface file="edit_dropdown_actions_area.tpl"}
  
</div>
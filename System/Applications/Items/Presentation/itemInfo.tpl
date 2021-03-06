<div id="work-area">
  <h3>{$item._model.name} Info</h3>
  
  {if $item._model.item_name_field_visible}
  <div class="edit-form-row">
    <div class="form-section-label">{$item._model.name} {$item._model.item_name_field_name}</div>
    {if $user_can_modify_items}
    <p class="editable" id="item-name" style="max-width:320px">{$item.name|escape_double_quotes}</p>
    <script type="text/javascript">
    // <!--
    new Ajax.InPlaceEditor('item-name', sm_domain+'ajax:datamanager/setItemNameFromInPlaceEditField', {ldelim}
      callback: function(form, value) {ldelim}
        return 'item_id={$item.id}&new_name='+encodeURIComponent(value);
      {rdelim},
      highlightColor: '#ffffff',
      hoverClassName: 'editable-hover',
      savingClassName: 'editable-saving'
    {rdelim});
    // -->
    </script>
    {else}
    {$item.name}
    {/if}
  </div>
  {/if}
  
  <div class="edit-form-row">
    <div class="form-section-label">{$item._model.name} short name (Used in links and URLS)</div>
    {if $user_can_modify_items}
    <p class="editable" id="item-slug" style="max-width:320px">{$item.slug|escape_double_quotes}</p>
    <script type="text/javascript">
    // <!--
    new Ajax.InPlaceEditor('item-slug', sm_domain+'ajax:datamanager/setItemSlugFromInPlaceEditField', {ldelim}
      callback: function(form, value) {ldelim}
        return 'item_id={$item.id}&new_slug='+encodeURIComponent(value);
      {rdelim},
      highlightColor: '#ffffff',
      hoverClassName: 'editable-hover',
      savingClassName: 'editable-saving'
    {rdelim});
    // -->
    </script>
    {else}
    <code>{$item.slug}</code>
    {/if}
  </div>
  
  <div class="edit-form-row">
      <div class="form-section-label">Numeric ID</div>
      {$item.id}
    </div>
  
    <div class="edit-form-row">
        <div class="form-section-label">Long ID</div>
        <code>{$item.webid}</code>
      </div>
  
  {if $has_page}
    <div class="edit-form-row">
        <div class="form-section-label">Default URL</div>
        {$item.absolute_uri}
      </div>
  {/if}
  {if $item.created.unix > 0}
    <div class="edit-form-row">
      <div class="form-section-label">Created:</div>
      {$item.created}
    </div>
    {/if}
    {if $item.modified.unix > 0}
    <div class="edit-form-row">
      <div class="form-section-label">Modified:</div>
      {$item.modified}
    </div>
    {/if}
    
    <div class="edit-form-row">
      <div class="form-section-label">Last published:</div>
      {$item.last_published}
    </div>

  {* <div id="sets" class="special-box">
       Sets: {if count($sets)}{foreach from=$sets item="set"}<a href="{$domain}sets/previewSet?set_id={$set.id}">{$set.label}</a> (<a href="{$domain}sets/transferSingleItem?item_id={$item.id}&amp;set_id={$set.id}&amp;transferAction=remove">remove</a>), {/foreach}{else}<em style="color:#666">None</em>{/if}
   {if count($possible_sets)}
           <div>
             <form action="{$domain}sets/transferSingleItem" method="post">
               <input type="hidden" name="item_id" value="{$item.id}" />
               <input type="hidden" name="transferAction" value="add" />
               Add this item to set:
               <select name="set_id">
   {foreach from=$possible_sets item="possible_set"}
                 <option value="{$possible_set.id}">{$possible_set.label}</option>
   {/foreach}
               </select>
               <input type="submit" value="Go" />
             </form>
           </div>
   {/if}
     </div> *}
<div class="edit-form-row">
   <div class="form-section-label">Author(s)</div>
   {if $item.authors._count > 0}{$item.authors}{else}No authors{/if}
 </div>
 <div class="edit-form-row">
     <div class="form-section-label">Tags</div>
     {if $item.tags._empty}<em>No tags selected</em>{else}{$item.tags}{/if}
   </div>
 <div class="edit-form-row">
   <div class="form-section-label">Workflow Status</div>
   {$item._workflow_status}
 </div>
  {if $has_page}
  <div class="edit-form-row">
    <div class="form-section-label">Link code (Default URL)</div>
    <code>[[{$item._model.name|varname}:{$item.slug}]]</code>
  </div>
  {* <div class="edit-form-row">
      <div class="form-section-label">QR code (Default URL)</div>
      {$item.absolute_uri.qr_code_image.width_100}
    </div> *}
  {/if}
  <div class="edit-form-row">
     <div class="form-section-label">Public JSON</div>
     <div class="edit-form-sub-row">
       <pre>{$item._json_pretty|xmlentities}</pre>
     </div>
   </div>
  </div>

<div id="actions-area">

  <ul class="actions-list" id="non-specific-actions">
    <li><b>This {$item._model.name}</b></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/openItem?item_id={$item.id}';" class="right-nav-link"><img src="{$domain}Resources/Icons/pencil.png" border="0" /> Try to edit it</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/addTodoItem?item_id={$item.id}';" class="right-nav-link"><img src="{$domain}Resources/Icons/tick.png" border="0" /> Assign To-do</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/itemComments?item_id={$item.id}';" class="right-nav-link"><img src="{$domain}Resources/Icons/comment.png" border="0" /> View public comments</a></li>
  </ul>
  
  <ul class="actions-list" id="non-specific-actions">
    <li><b>{$item._model.name} Options</b></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/getItemClassMembers?class_id={$item._model.id}';" class="right-nav-link">Back to {$item._model.plural_name}</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/addItem?class_id={$item._model.id}';" class="right-nav-link">New {$item._model.name}</a></li>
  </ul>
  
  <ul class="actions-list" id="non-specific-actions">
    <li><span style="color:#999">Recently edited {$item._model.plural_name|strtolower}</span></li>
    {* foreach from=$recent_items item="recent_item"}
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$recent_item.action_url}'"><img border="0" src="{$recent_item.small_icon}" /> {$recent_item.label|summary:"28"}</a></li>
    {/foreach *}
  </ul>
  
</div>

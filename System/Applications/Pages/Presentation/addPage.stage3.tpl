  <h3>Create a New Page</h3>
  
  <ol class="stages-indicator">
    <li class="label">Stage: </li>
    <li><span class="stage-number">1</span> Enter basic page details</li>
    <li><span class="stage-number">2</span> Add metadata and content</li>
    <li class="current"><span class="stage-number">3</span> Check &amp; confirm</li>
  </ol>
  
  <form action="{$domain}{$section}/insertPage" method="post">
    
    <table cellspacing="1" cellpadding="2" border="0" style="width:100%;background-color:#ccc;margin-top:10px">
      <tr>
        <td style="width:150px;background-color:#fff" valign="top">Title:</td>
        <td style="background-color:#fff" valign="top">{$newPage.title}</td>
      </tr>
      <tr>
        <td style="background-color:#fff" valign="top">URL:</td>
        <td style="background-color:#fff" valign="top"><code>{$domain}{$new_page_url}</code> {if !$chosen_url_available}<span class="ui-info-inactive">(Chosen URL not available)</span>{/if}</td>
      </tr>
      {if _b($newPage.parent)}
      <tr>
        <td style="background-color:#fff" valign="top">Parent page:</td>
        <td style="background-color:#fff" valign="top">{$newPage.parent.title}</td>
      </tr>
      {/if}
      {if is_numeric($newPage.dataset_id) && $newPage.type == "ITEMCLASS"}
      <tr>
        <td style="background-color:#fff" valign="top">Represents model:</td>
        <td style="background-color:#fff" valign="top">{$new_page_model.plural_name}</td>
      </tr>
      {/if}
      {if is_numeric($newPage.dataset_id) && $newPage.type == "TAG"}
      <tr>
        <td style="background-color:#fff" valign="top">Retrieves tag:</td>
        <td style="background-color:#fff" valign="top">{$newPage.tag_label}</td>
      </tr>
      {/if}
      <tr>
        <td style="background-color:#fff" valign="top">Cache this page:</td>
        <td style="background-color:#fff" valign="top">{$newPage.cache_as_html|yesno}</td>
      </tr>
      {if _b($newPage.cache_as_html)}
      <tr>
        <td style="background-color:#fff" valign="top">Cache interval:</td>
        <td style="background-color:#fff" valign="top">{$newPage.cache_interval|titlecase}</td>
      </tr>
      {/if}
      <tr>
        <td style="background-color:#fff" valign="top">Use a page preset:</td>
        <td style="background-color:#fff" valign="top">{if $use_preset}{$page_preset.label}{else}<span class="ui-info-inactive">None</span>{/if}</td>
      </tr>
      <tr>
        <td style="background-color:#fff" valign="top">Main template:</td>
        <td style="background-color:#fff" valign="top">{if strlen($newPage.draft_template)}<code>Presentation/Masters/{$newPage.draft_template}</code> {if $use_preset} <span class="ui-info-inactive">(from preset)</span>{/if}{else}<span class="ui-info-inactive">None</span>{/if}</td>
      </tr>
    {if $show_layout_template}
      <tr>
        <td style="background-color:#fff" valign="top">Layout template:</td>
        <td style="background-color:#fff" valign="top">{if $layout_template.id}<code>Presentation/Layouts/{$layout_template.url}</code> {if $use_preset_for_layout} <span class="ui-info-inactive">(from preset)</span>{/if}{else}<span class="ui-info-inactive">None</span>{/if}</td>
      </tr>
    {/if}
    {if $newPage.type == 'NORMAL'}
      <tr>
        <td style="background-color:#fff" valign="top">Description:</td>
        <td style="background-color:#fff" valign="top">{if strlen($newPage.description)}{$newPage.description}{else}<span class="ui-info-inactive">None</span>{/if}</td>
      </tr>
      <tr>
        <td style="background-color:#fff" valign="top">Search keywords:</td>
        <td style="background-color:#fff" valign="top">{if strlen($newPage.search_field)}{$newPage.search_field}{else}<span class="ui-info-inactive">None</span>{/if}</td>
      </tr>
      <tr>
        <td style="background-color:#fff" valign="top">Meta description:</td>
        <td style="background-color:#fff" valign="top">{if strlen($newPage.meta_description)}{$newPage.meta_description}{else}<span class="ui-info-inactive">None</span>{/if}</td>
      </tr>
      <tr>
        <td style="background-color:#fff" valign="top">Meta keywords:</td>
        <td style="background-color:#fff" valign="top">{if strlen($newPage.keywords)}{$newPage.keywords}{else}<span class="ui-info-inactive">None</span>{/if}</td>
      </tr>
    {/if}
    </table>
    
    <p>After the page has been built, take me:
      
      <select name="destination">
        <option value="PREVIEW">To preview this page</option>
        <option value="ELEMENTS">To the elements tree for this page</option>
        <option value="SITEMAP">Back to the site map</option>
        <option value="EDIT">To edit this page</option>
      </select>
      
    </p>
  
    <div class="edit-form-row">
      <div class="buttons-bar">
        <input type="button" value="&lt;&lt; Back a step" onclick="window.location=sm_domain+sm_section+'/addPage?stage=2'" />
        <input type="submit" value="Create new page now" />
      </div>
    </div>
    
  </form>
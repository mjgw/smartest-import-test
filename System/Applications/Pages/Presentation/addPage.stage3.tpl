  <h3>Create a New Page</h3>
  
  <div class="instruction-text">Step 3 of 3: Confirm the details of your new page</div>
  
  <form action="{$domain}{$section}/insertPage" method="post">
    
    <table cellspacing="1" cellpadding="2" border="0" style="width:100%;background-color:#ccc;margin-top:10px">
      <tr>
        <td style="width:150px;background-color:#fff" valign="top">Title:</td>
        <td style="background-color:#fff" valign="top">{$newPage.title}</td>
      </tr>
      <tr>
        <td style="background-color:#fff" valign="top">URL:</td>
        <td style="background-color:#fff" valign="top"><code>{$domain}{$new_page_url}</code></td>
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
        <td style="background-color:#fff" valign="top">Cache as HTML:</td>
        <td style="background-color:#fff" valign="top">{$newPage.cache_as_html|yesno}</td>
      </tr>
      {if _b($newPage.cache_as_html)}
      <tr>
        <td style="background-color:#fff" valign="top">Cache interval:</td>
        <td style="background-color:#fff" valign="top">{$newPage.cache_interval|titlecase}</td>
      </tr>
      {/if}
      <tr>
        <td style="background-color:#fff" valign="top">Layout preset:</td>
        <td style="background-color:#fff" valign="top">{if $newPage.preset}{$newPage.preset_label}{else}<em style="color:#999">NONE</em>{/if}</td>
      </tr>
      <tr>
        <td style="background-color:#fff" valign="top">Main template:</td>
        <td style="background-color:#fff" valign="top">{if strlen($newPage.draft_template)}<code>Presentation/Masters/{$newPage.draft_template}</code>{else}<em style="color:#999">NONE</em>{/if}</td>
      </tr>
  	{if $newPage.type == 'NORMAL'}
      <tr>
        <td style="background-color:#fff" valign="top">Description:</td>
        <td style="background-color:#fff" valign="top">{if strlen($newPage.description)}{$newPage.description}{else}<em style="color:#999">NONE</em>{/if}</td>
      </tr>
      <tr>
        <td style="background-color:#fff" valign="top">Search keywords:</td>
        <td style="background-color:#fff" valign="top">{if strlen($newPage.search_field)}{$newPage.search_field}{else}<em style="color:#999">NONE</em>{/if}</td>
      </tr>
      <tr>
        <td style="background-color:#fff" valign="top">Meta description:</td>
        <td style="background-color:#fff" valign="top">{if strlen($newPage.meta_description)}{$newPage.meta_description}{else}<em style="color:#999">NONE</em>{/if}</td>
      </tr>
      <tr>
        <td style="background-color:#fff" valign="top">Meta keywords:</td>
        <td style="background-color:#fff" valign="top">{if strlen($newPage.keywords)}{$newPage.keywords}{else}<em style="color:#999">NONE</em>{/if}</td>
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
        <input type="button" value="&lt;&lt; Back" onclick="window.location=sm_domain+sm_section+'/addPage?stage=2'" />
        <input type="submit" value="Finish" />
      </div>
    </div>
    
  </form>
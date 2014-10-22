<div id="work-area-full">

{load_interface file="edit_tabs.tpl"}

<h3>Preview of {if $item}{$item._model.name|lower}: <span class="light">{$item.name} <a href="{dud_link}" onclick="MODALS.load('datamanager/itemInfo?item_id={$item.id}', '{$item._model.name} info');" title="Get info"><img src="{$domain}Resources/Icons/information.png" alt="Get info" /></a> (via meta-page '{$page.title}')</span>{else}page: <span class="light">{$page.title}</span>{/if}</h3>

{if $show_iframe}


<script language="javascript">
{literal}  
    var t1, t2;
    
    function showPreview(){
        $('preview-iframe').style.height = '480px';
        $('preview-iframe').removeClassName('building');
        $('preview-iframe').addClassName('built');
        $('preview').appear({duration: 0.4});
        $('preview-loading').fade({duration: 0.4});
        clearTimeout(t1);
        clearTimeout(t2);
    }
    
    function cancelWait(){
      $('preview-slow').fade({duration: 0.4});
      showPreview();
    }
    
    function hidePreview(){
        
        // $('preview').fade({duration: 0.4, afterFinish: function(){
            $('preview-iframe').style.height = '0px';
            $('preview-iframe').removeClassName('built');
            $('preview-iframe').addClassName('building');
            $('preview').hide();
            $('preview-loading').appear({duration: 0.4});
        // }});
        
    }
    
    function previewSlow(){
        /* $('preview-loading').style.display = 'none';
        $('preview-slow').style.display = 'block'; */
        $('preview-loading').fade({duration: 0.4});
        $('preview-slow').appear({duration: 0.4});
    }
    
    function previewTimedOut(){
        $('preview-slow').style.display = 'none';
        $('preview-failed').style.display = 'block';
    }
    
    t1 = setTimeout(function(){previewSlow();}, 8000);
    t2 = setTimeout(function(){previewTimedOut();}, 20000);

{/literal}    
</script>

<div id="preview-container">
  
  <div id="preview-menu" class="preview-component">
    
    <div class="menubar">
      <a href="{dud_link}" class="js-menu-activator" id="actions-menu-activator">Actions</a> {*<a href="javascript:showPreview()">Show</a>*}
      {if $stylesheets._count > 0}<a href="{dud_link}" class="js-menu-activator" id="files-menu-activator">Stylesheets</a>{/if}
    </div>

    <div id="preview-actions-menu" class="js-menu" style="display:none">
      <ul></ul>
      <ul><li><a href="#reload-preview" id="reload-preview"><i class="fa fa-repeat"></i> Reload preview</a></li><li>{if $show_approve_button}<a href="{dud_link}" onclick="window.location='{$domain}{$section}/approvePageChanges?page_id={$page.webid}'">{else}<span>{/if}<i class="fa fa-thumbs-o-up"></i> Approve changes{if $show_approve_button}</a>{else}</span>{/if}</li><li>{if $show_publish_button}<a href="{dud_link}" onclick="window.location='{$domain}{$section}/publishPageConfirm?page_id={$page.webid}{if $item}&amp;item_id={$item.id}{/if}'">{else}<span>{/if}<i class="fa fa-check-circle"></i> Publish this page{if $show_publish_button}</a>{else}</span>{/if}</li>{if $item && $item.id}{if $show_publish_item_option}<li><a href="{dud_link}" onclick="window.location='{$domain}datamanager/publishItem?page_id={$page.webid}&amp;item_id={$item.id}&amp;from=preview'">Publish this {$item._model.name|lower}</a></li>{/if}{else}<li><a href="{$domain}websitemanager/addPage?page_id={$page.webid}"><i class="fa fa-plus-square-o"></i> Add a child page</a></li>{/if}{if $show_edit_item_option}<li><a href="{dud_link}" onclick="window.location='{$domain}datamanager/editItem?item_id={$item.id}&amp;page_id={$page.webid}&amp;from=pagePreview'">Edit this {$item._model.name|lower}</a></li>{/if}<li>{if $show_release_page_option}<a href="{dud_link}" onclick="window.location='{$domain}{$section}/releasePage?page_id={$page.webid}'">{else}<span>{/if}<i class="fa fa-unlock"></i> Release this page{if $show_release_page_option}</a>{else}</span>{/if}</li></ul>
      <script type="text/javascript">
      {literal}
      $('reload-preview').observe('click', function(e){
          hidePreview();
          document.getElementById('preview-iframe').contentWindow.location.reload(true);
          t1 = setTimeout(function(){previewSlow();}, 8000);
          t2 = setTimeout(function(){previewTimedOut();}, 20000);
          e.stop();
      });
      {/literal}</script>
    </div>

    {if $stylesheets._count > 0}
    <div id="preview-files-menu" class="js-menu">
      <ul></ul>
      <ul>{foreach from=$stylesheets item="stylesheet"}<li><a href="{$stylesheet.action_url}"><i class="fa fa-file-o"></i> {$stylesheet.label}</a></li>{/foreach}</ul>
      <script type="text/javascript"></script>
    </div>
    {/if}
    
  </div>
  
  <div id="preview" class="preview-component" style="display:none">
    <iframe class="building" id="preview-iframe" src="{$domain}website/renderEditableDraftPage?page_id={$page.webid}{if $item}&amp;item_id={$item.id}{/if}{if $request_parameters.author_id}&amp;author_id={$request_parameters.author_id}{/if}{if $request_parameters.search_query}&amp;q={$request_parameters.search_query}{/if}{if $request_parameters.tag}&amp;tag_name={$request_parameters.tag}{/if}{if $request_parameters.hash}#{$request_parameters.hash}{/if}" style="height:0px"></iframe>
  </div>
  
  <div id="preview-loading" class="preview-component">
    <p>Please wait. Rendering preview...</p>
    <p><img src="{$domain}Resources/System/Images/smartest-working-flat.gif" /></p>
  </div>
  
  <div id="preview-slow" class="preview-component" style="display:none">
    <p>Sorry for the wait. Just a bit longer... <a href="javascript:cancelWait()">Show now</a></p>
    <p><img src="{$domain}Resources/System/Images/smartest_working.gif" /></p>
  </div>
  
  <div id="preview-failed" style="display:none" class="preview-component">
    <p>Still no luck. Something stopped the page from building. <br />Try having a look at <a href="javascript:window.open('{$domain}website/renderEditableDraftPage?page_id={$page.webid}{if $item}&amp;item_id={$item.id}{/if}');">the page by itself</a>.</p>
  </div>

</div>

{elseif $show_item_list}

{load_interface file="choose_item.tpl"}

{elseif $show_tag_list}

{load_interface file="choose_tag.tpl"}

{/if}

</div>

<script type="text/javascript">

var actionsMenu = new Smartest.UI.Menu('preview-actions-menu', 'actions-menu-activator');

{literal}
  $('actions-menu-activator').observe('click', function(e){
    actionsMenu.toggleVisibility();
    e.stop();
  });
{/literal}

{if $stylesheets._count > 0}
var filesMenu = new Smartest.UI.Menu('preview-files-menu', 'files-menu-activator');

{literal}
  $('files-menu-activator').observe('click', function(e){
    filesMenu.toggleVisibility();
    e.stop();
  });
{/literal}
{/if}

</script>
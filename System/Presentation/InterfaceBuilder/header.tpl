<!DOCTYPE html>
        
<html>
  <head>
    <title>Smartest{if $_interface_title} | {$_interface_title}{/if}</title>
    
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="icon" href="{$domain}Resources/System/Images/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="{$domain}Resources/System/Images/favicon.ico" type="image/x-icon">
    
		<!--Load icons as fonts-->
		{if $is_msie}
		<link rel="stylesheet" type="text/css" href="{$domain}Resources/System/Icons/flaticons-eot.css" />
		<link rel="stylesheet" type="text/css" href="{$domain}Resources/System/Icons/font-awesome-eot.css" />
		{else}
		<link rel="stylesheet" type="text/css" href="{$domain}Resources/System/Icons/flaticons.css" />
		<link rel="stylesheet" type="text/css" href="{$domain}Resources/System/Icons/font-awesome.css" />
		{/if}
		
    <link rel="stylesheet" type="text/css" href="{$domain}Resources/System/Stylesheets/sm_style.css?r={$smartest_info.revision}{if $sm_developer_mode}&amp;n={$global_random_nonce}{/if}" />
    <link rel="stylesheet" type="text/css" href="{$domain}Resources/System/Stylesheets/sm_layout.css?r={$smartest_info.revision}{if $sm_developer_mode}&amp;n={$global_random_nonce}{/if}" />
    <link rel="stylesheet" type="text/css" href="{$domain}Resources/System/Stylesheets/sm_admin_menu.css?r={$smartest_info.revision}{if $sm_developer_mode}&amp;n={$global_random_nonce}{/if}" />
    <link rel="stylesheet" type="text/css" href="{$domain}Resources/System/Stylesheets/sm_actions_menu.css?r={$smartest_info.revision}{if $sm_developer_mode}&amp;n={$global_random_nonce}{/if}" />
    <link rel="stylesheet" type="text/css" href="{$domain}Resources/System/Stylesheets/sm_itemsview.css?r={$smartest_info.revision}{if $sm_developer_mode}&amp;n={$global_random_nonce}{/if}" />
    <link rel="stylesheet" type="text/css" href="{$domain}Resources/System/Stylesheets/sm_treeview.css?r={$smartest_info.revision}{if $sm_developer_mode}&amp;n={$global_random_nonce}{/if}" />
    <link rel="stylesheet" type="text/css" href="{$domain}Resources/System/Stylesheets/sm_dropdown_menu.css?r={$smartest_info.revision}{if $sm_developer_mode}&amp;n={$global_random_nonce}{/if}" />
    <link rel="stylesheet" type="text/css" href="{$domain}Resources/System/Stylesheets/sm_columns.css?r={$smartest_info.revision}{if $sm_developer_mode}&amp;n={$global_random_nonce}{/if}" />
    <link rel="stylesheet" type="text/css" href="{$domain}Resources/System/Stylesheets/sm_tabs.css?r={$smartest_info.revision}{if $sm_developer_mode}&amp;n={$global_random_nonce}{/if}" />
    {if $section=="assets" && $method=="previewAsset"}<link rel="stylesheet" type="text/css" href="{$domain}Resources/System/Stylesheets/sm_attachments.css?r={$smartest_info.revision}{if $sm_developer_mode}&amp;n={$global_random_nonce}{/if}" />{/if}
    <link rel="stylesheet" type="text/css" href="{$domain}Resources/System/Stylesheets/sm_modals.css?r={$smartest_info.revision}{if $sm_developer_mode}&amp;n={$global_random_nonce}{/if}" />
    <link rel="stylesheet" href="{$domain}Resources/System/Javascript/CodeMirror-5.2/lib/codemirror.css">
    <link rel="stylesheet" href="{$domain}Resources/System/Javascript/CodeMirror-5.2/addon/display/fullscreen.css">
		
		<script type="text/javascript" language="javascript">

       var sm_domain = '{$domain}';
       var sm_section = '{$section}';
       var sm_method = '{$method}';
       var sm_user_agent = {$sm_user_agent_json};
       var sm_cancel_uri = '{$sm_cancel_uri}';
       
    </script>
    
    <script type="text/javascript" src="{$domain}Resources/System/Javascript/scriptaculous/lib/prototype.js"></script>
    <script type="text/javascript" src="{$domain}Resources/System/Javascript/scriptaculous/src/effects.js"></script>
    <script type="text/javascript" src="{$domain}Resources/System/Javascript/scriptaculous/src/controls.js"></script>
    <script type="text/javascript" src="{$domain}Resources/System/Javascript/scriptaculous/src/slider.js"></script>
    <script type="text/javascript" src="{$domain}Resources/System/Javascript/syntacticx-livepipe-ui/src/livepipe.js"></script>
    <script type="text/javascript" src="{$domain}Resources/System/Javascript/syntacticx-livepipe-ui/src/scrollbar.js"></script>
    <script type="text/javascript" src="{$domain}Resources/System/Javascript/jscolor/jscolor.js"></script>
                                                                            
    <script type="text/javascript" src="{$domain}Resources/System/Javascript/smartest/interface.js?r={$smartest_info.revision}{if $sm_developer_mode}&amp;n={$global_random_nonce}{/if}"></script>
    <script type="text/javascript" src="{$domain}Resources/System/Javascript/smartest/treeview.js?r={$smartest_info.revision}{if $sm_developer_mode}&amp;n={$global_random_nonce}{/if}"></script>
    <script type="text/javascript" src="{$domain}Resources/System/Javascript/smartest/help.js?r={$smartest_info.revision}{if $sm_developer_mode}&amp;n={$global_random_nonce}{/if}"></script>
    <script type="text/javascript" src="{$domain}Resources/System/Javascript/smartest/prefs.js?r={$smartest_info.revision}{if $sm_developer_mode}&amp;n={$global_random_nonce}{/if}"></script>
    <script type="text/javascript" src="{$domain}Resources/System/Javascript/vsclabs/vsc-scrollwatcher.js"></script>
    
    <script type="text/javascript" src="{$domain}Resources/System/Javascript/CodeMirror-5.2/lib/codemirror.js"></script>
    <script type="text/javascript" src="{$domain}Resources/System/Javascript/CodeMirror-5.2/mode/css/css.js"></script>
    <script type="text/javascript" src="{$domain}Resources/System/Javascript/CodeMirror-5.2/mode/javascript/javascript.js"></script>
    <script type="text/javascript" src="{$domain}Resources/System/Javascript/CodeMirror-5.2/mode/xml/xml.js"></script>
    <script type="text/javascript" src="{$domain}Resources/System/Javascript/CodeMirror-5.2/mode/htmlmixed/htmlmixed.js"></script>
    <script type="text/javascript" src="{$domain}Resources/System/Javascript/CodeMirror-5.2/mode/htmlembedded/htmlembedded.js"></script>
    
    <script type="text/javascript">
      {literal}
      var HELP = new Smartest.HelpViewer();
      var PREFS = new Smartest.PreferencesBridge();
      var MODALS = new Smartest.AjaxModalViewer();
      Smartest.createNew = function(){MODALS.load('desktop/createDialog', 'Create something new');}
      
      document.observe('dom:loaded', function(){
      
        document.observe('scrolled:vertically', function(evt){
          
          if(evt.memo.currentScrollTop > 50){
            if(!$('primary-ajax-loader').hasClassName('scrolled')){
              $('primary-ajax-loader').addClassName('scrolled');
            }
          }else{
            if($('primary-ajax-loader').hasClassName('scrolled')){
              $('primary-ajax-loader').removeClassName('scrolled');
            }
          }
        });
      
        if(!$('actions-area')){
          $('primary-ajax-loader').addClassName('fullwidth');
        }
      
      });
      
      {/literal}
    </script>
    
    <style type="text/css">
      img{ldelim} behavior:url({$domain}Resources/System/Javascript/iepngfix/iepngfix.htc); {rdelim}
    </style>
		
  </head>
  
  <body class="nav-{if $admin_menu_contracted}contracted{else}expanded{/if}" id="body">
    
    <div id="help" style="display:none" class="modal-outer">
      <div id="help-viewer" class="modal">
        <div class="modal-scrollbar-track" id="help-scrollbar-track"><div class="modal-scrollbar-handle" id="help-scrollbar-handle"></div></div>
        <div id="help-updater" class="modal-updater">
          
        </div>
        <div id="help-title-bar" class="modal-title-bar">
          <a class="modal-closer" id="help-closer" href="#close-help"></a>
          <script type="text/javascript">
          {literal}$('help-closer').observe('click', function(e){
            HELP.hideViewer();
            e.stop();
          });{/literal}
          </script>
          <h2 id="help-title">Smartest Help Viewer</h2>
        </div>
      </div>
    </div>
    
    <div id="modal-outer" style="display:none" class="modal-outer">
      <div id="modal-inner" class="modal">
        <div class="modal-scrollbar-track" id="modal-scrollbar-track"><div class="modal-scrollbar-handle" id="modal-scrollbar-handle"></div></div>
        <div id="modal-updater" class="modal-updater"></div>
        <div class="modal-title-bar">
          <a class="modal-closer" id="modal-closer" href="#close-modal"></a>
          <script type="text/javascript">
          {literal}$('modal-closer').observe('click', function(e){
            MODALS.hideViewer();
            e.stop();
          });{/literal}
          </script>
          <h2 id="modal-title"></h2>
        </div>
      </div>
    </div>
    
    <script type="text/javascript">
      {literal}
      /* $('modal-outer').observe('click', function(e){
        MODALS.hideViewer();
        e.stop();
      });
      $('modal-inner').observe('click', function(e){
        // e.stop();
      });
      $('help').observe('click', function(e){
        HELP.hideViewer();
        e.stop();
      });
      $('help-viewer').observe('click', function(e){
        e.stop();
      }); */
      {/literal}
    </script>
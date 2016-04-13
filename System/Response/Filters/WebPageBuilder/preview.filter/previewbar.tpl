<div class="smartest_preview_top_bar" id="sm-preview-bar"<?sm:if $hide_preview_bar:?> style="display:none"<?sm:/if:?>>
    
    <div id="smartest-preview-bar-inner">
        
        Pre-Render Overhead: <?sm:$overhead_time:?>ms |
        Page Build Time: <?sm:$build_time:?>ms |
        Total time taken: <?sm:$total_time:?> 
        
        <a href="<?sm:if $has_item:?><?sm:$domain:?>datamanager/publishItem?item_id=<?sm:$item_id:?><?sm:else:?><?sm:$domain:?>websitemanager/publishPageConfirm?page_id=<?sm:$page_webid:?><?sm:/if:?>" target="_top" id="smartest-preview-publish-link" class="smartest-preview-button" title="Publish this <?sm:if $has_item:?>item<?sm:else:?>page<?sm:/if:?>"></a>
        <?sm:if !$hide_liberate_link:?> <a href="<?sm:$liberate_link_url:?>" target="_top" id="smartest-preview-liberate-link" title="Preview in full screen" class="smartest-preview-button"></a><?sm:else:?> <a href="<?sm:$preview_link_url:?>" id="smartest-preview-collapse-link" title="Back to Smartest preview" class="smartest-preview-button"></a><?sm:/if:?>
        <?sm:if $show_item_edit_link:?> <a href="<?sm:$domain:?>datamanager/openItem?item_id=<?sm:$item_id:?>&amp;from=preview&amp;page_webid=<?sm:$page_webid:?>" id="smartest-preview-edit-item" target="_top" class="smartest-preview-button" title="Edit this <?sm:$model_name:?>"></a><?sm:/if:?>
        <?sm:if $hide_liberate_link:?> <a href="<?sm:$domain:?>smartest/pages" id="smartest-preview-return-topages" title="Back to site pages" class="smartest-preview-button"></a><?sm:/if:?>
        
        <a id="sm-edit-button-toggle" href="#toggle-edit-buttons" class="<?sm:if $hide_preview_edit_buttons.value:?>hidden<?sm:else:?>showing<?sm:/if:?> smartest-preview-button" title="<?sm:if $hide_preview_edit_buttons.value:?>Show<?sm:else:?>Hide<?sm:/if:?> edit buttons"></a>
        
    </div>
    
    <a href="#hide-preview-bar" id="hide-preview-bar-link"></a>
    
</div>

<div id="sm-preview-hidden" <?sm:if !$hide_preview_bar:?> style="display:none"<?sm:/if:?>>
  <a href="#show-preview-bar" id="show-preview-bar-link" title="Expand preview bar"></a>
</div>

<script type="text/javascript">

  var _SM = {};
  _SM.editButtonsVisible = <?sm:$hide_preview_edit_buttons.not.truefalse:?>;
  _SM.domain = '<?sm:$domain:?>';
  
  var hideEditButtons = function(){
      
      var elements = document.getElementsByClassName('sm-edit-button');
      for (var i = 0; i < elements.length; i++) {
          elements[i].style.display = 'none';
      }
      _SM.editButtonsVisible = false;
      
      var state = 1;
      var URL = _SM.domain+'ajax:website/setPreviewEditButtonVisibility?state='+state;
      
      var req = new XMLHttpRequest();
      req.open("GET", URL, true);
      req.send();
      
      if(window.Smartest_adjustLayout && typeof window.Smartest_adjustLayout == 'function'){
        window.Smartest_adjustLayout(false);
      }
      
  }
  
  var showEditButtons = function(){

        var elements = document.getElementsByClassName('sm-edit-button');
        for (var i = 0; i < elements.length; i++) {
            elements[i].style.display = 'inline';
        }
        _SM.editButtonsVisible = true;
        
        var state = 0;
        var URL = _SM.domain+'ajax:website/setPreviewEditButtonVisibility?state='+state;
        
        var req = new XMLHttpRequest();
        req.open("GET", URL, true);
        req.send();
        
        if(window.Smartest_adjustLayout && typeof window.Smartest_adjustLayout == 'function'){
          window.Smartest_adjustLayout(true);
        }

    }
    
    document.getElementById('sm-edit-button-toggle').addEventListener('click', function(event){
        
        event.preventDefault();
        
        if(_SM.editButtonsVisible){
            hideEditButtons();
            document.getElementById('sm-edit-button-toggle').title = 'Show edit buttons';
            document.getElementById('sm-edit-button-toggle').className = 'hidden smartest-preview-button';
        }else{
            showEditButtons();
            document.getElementById('sm-edit-button-toggle').title = 'Hide edit buttons';
            document.getElementById('sm-edit-button-toggle').className = 'showing smartest-preview-button';
        }
        
    }, false);
    
    document.getElementById('hide-preview-bar-link').addEventListener('click', function(event){
      
        event.preventDefault();
        document.getElementById('sm-preview-bar').style.display = 'none';
        document.getElementById('sm-preview-hidden').style.display = 'block';
        var state = 1;
        var URL = _SM.domain+'ajax:website/setPreviewBarVisibility?state='+state;
        
        var req = new XMLHttpRequest();
        req.open("GET", URL, true);
        req.send();
        
    });
    
    document.getElementById('show-preview-bar-link').addEventListener('click', function(event){
      
        event.preventDefault();
        document.getElementById('sm-preview-bar').style.display = 'block';
        document.getElementById('sm-preview-hidden').style.display = 'none';
        var state = 0;
        var URL = _SM.domain+'ajax:website/setPreviewBarVisibility?state='+state;
        
        var req = new XMLHttpRequest();
        req.open("GET", URL, true);
        req.send();
        
    });
    
    document.getElementById('sm-preview-bar').style.display = 'none';
    <?sm:if !$hide_preview_bar:?>document.getElementById('sm-preview-bar').style.display = 'block';<?sm:/if:?>
  
</script>
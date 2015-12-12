<div id="work-area">
  
  {load_interface file="edit_tabs.tpl"}
  
  <h3>Downloads</h3>
  
  <div class="instruction">Files attached to this page as downloads</div>
  
  <div class="special-box">
    
    <p>To add a download to this page, start typing its name in the box below: {* <a href="#add-download" class="button" id="add-page-download-link">click here</a> *}</p>
    
    <form action="" method="get" id="file-search-form">
      <input type="text" id="download-file-select" name="download_file_select" value="" />
      <div id="download-file-select-autocomplete" class="autocomplete"></div>
    </form>
    
  </div>
  
  <ul class="basic-files-list" id="downloads-list">
{foreach from=$downloads item="download"}
    <li data-downloadid="{$download.id}" data-assetid="{$download.file.id}"><img src="{$download.file.small_icon}" alt="" /><span class="file-label">{$download.file.url}</span><a href="#download-info" class="download-info icon-button">i</a><a href="#remove-download" class="download-remove icon-button">X</a></li>
{/foreach}
  </ul>
  
  <script type="text/javascript">
  
  var connected_file_ids = '{$connected_file_ids}';
  var page_id = '{$page.id}';
  
  {literal}
  /* $('add-page-download-link').observe('click', function(evt){
    evt.stop();
    MODALS.load('websitemanager/addPageDownload', 'Connect downloadable file from media library');
  }); */
    
  var removeDownloadFromClickEvent = function(e2){
    
    e2.stop();
    
    if(confirm("Really remove this file as a download from this page?")){
      
      rl = Event.element(e2);
    
      var dlid = rl.up().readAttribute('data-downloadid');
      var assetid = rl.up().readAttribute('data-assetid');
  
      new Ajax.Request(sm_domain+'ajax:websitemanager/removePageDownload', {
    
        parameters: 'asset_id='+assetid+'&page_id='+page_id,
        onSuccess: function(removeDownloadResponse) {
          rl.up().fade({duration:0.25});
          if(removeDownloadResponse.responseJSON.success){
            // TODO: Display user message to confirm download has been removed
          }      
        }
      
      });
      
    }
  
  }
  
  $('file-search-form').observe('submit', function(e3){
    e3.stop();
    $('download-file-select').value = "";
    $('download-file-select').blur();
    return false;
  });
    
  new Ajax.Autocompleter('download-file-select', "download-file-select-autocomplete", sm_domain+"ajax:assets/assetSearch", {
      
      paramName: "query", 
      minChars: 3,
      delay: 50,
      width: 300,
      parameters: 'limit=other,embedded&skip='+connected_file_ids,
      afterUpdateElement : function(text, li) {
        
        var bits = li.id.split('-');
        
        // 0. Get file info
        
        var file;
        
        // new Ajax.Request(sm_domain+'ajax:assets/assetInfoJson?asset_id=');
        new Ajax.Request(sm_domain+'ajax:assets/assetInfoJson', {
          
          parameters: 'asset_id='+bits[1],
          onSuccess: function(fileInfoResponse) {
            
            // Handle the response content...
            file = fileInfoResponse.responseJSON;
            
            // 1. Make AJAX call to add file to downloads
        
            new Ajax.Request(sm_domain+'ajax:websitemanager/addPageDownload', {
              
              parameters: 'asset_id='+bits[1]+'&page_id='+page_id,
              onSuccess: function(createDownloadResponse) {
                
                console.log(file.type_info);
                // console.log(connected_file_ids);
                // connected_file_ids.push(file.id);
                
                $('download-file-select').value = "";
                $('download-file-select').blur();
                
                // 2. Add file to visual list
        
                var li = new Element('li');
                li.writeAttribute('data-assetid', bits[1]);
                li.writeAttribute('data-downloadid', createDownloadResponse.responseJSON.id)
                
                var icon = new Element('img');
                icon.src = '/Resources/Icons/'+file.type_info.icon;
                icon.alt = "";
                
                var label = new Element('span');
                label.addClassName('file-label');
                label.update(file.url);
                
                var infoLink = new Element('a');
                infoLink.href = "#download-info";
                infoLink.addClassName("download-info");
                infoLink.addClassName("icon-button");
                infoLink.update('i');
                
                var removeLink = new Element('a');
                removeLink.href = "#remove-download";
                removeLink.addClassName("download-remove");
                removeLink.addClassName("icon-button");
                removeLink.update('X');
                removeLink.observe('click', removeDownloadFromClickEvent);
                
                li.appendChild(icon);
                li.appendChild(label);
                li.appendChild(infoLink);
                li.appendChild(removeLink);
                
                $('downloads-list').appendChild(li);
                
              }
            });
            
          }
          
        });
        
      }
  });
  
  $$('a.download-remove').each(function(rl){
    rl.observe('click', removeDownloadFromClickEvent);
  });
  
  {/literal}
  </script>
  
</div>
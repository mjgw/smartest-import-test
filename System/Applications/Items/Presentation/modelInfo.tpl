<div id="work-area">
  
  <div class="edit-form-layout">
      
    <div class="edit-form-row">
      <div class="form-section-label">Model Name</div>
      {$model.name}/{$model.plural_name}
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Number of items</div>
      This site: <strong>{$num_items_on_site}</strong>; All sites: <strong>{$num_items_all_sites}</strong>
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Model class file</div>
      <code>{$class_file}</code> <span style="color:#999">({$class_file_size})</span>
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Primary site</div>
      {$main_site_name.xmlentities}
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Shared on all sites</div>
      {if $shared}Yes{else}No{/if} 
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Properties</div>
      {$model.properties} ({$model.properties._count} total) <a class="button small" href="#template-info" id="model-template-info-link">Template access info</a>
      <script type="text/javascript">
      var infoUrl = 'datamanager/showItemClassTemplateAccess?class_id={$model.id}';
      var infoTitle = 'Working with {$model.name} model';
      {literal}
      $('model-template-info-link').observe('click', function(e){
        e.stop();
        MODALS.load(infoUrl, infoTitle);
      });
      {/literal}
      </script>
    </div>
      
  </div>
  
</div>
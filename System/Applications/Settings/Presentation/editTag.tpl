<div id="work-area">
  
  {if $show_edit_tabs}
  {load_interface file="edit_tabs.tpl"}
  {/if}
  
  <h3>Edit tag: <span class="light">{$tag.label}</span></h3>
  
  <form action="{$domain}settings/updateTag" method="post">
    
    <input type="hidden" name="tag_id" value="{$tag.id}" />
    
    <div class="edit-form-row">
      <div class="form-section-label">Tag label:</div>
      <input type="text" name="tag_label" value="{$tag.label|escape:"quotes"}" />
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Tag slug:</div>
      <input type="text" name="tag_name" value="{$tag.name}" />
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Tag thumbnail image:</div>
      {image_select id="tag-icon-image" name="tag_icon_image" value=$tag.icon_image_asset_id}
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Tag description text </div>
      <div class="edit-form-sub-row">
        <textarea name="tag_description" style="width:500px;height:60px" id="tag-desc-tinymce">{$desc_text_editor_content}</textarea>
      </div>
    </div>
    
    <div class="buttons-bar">
      {save_buttons}
    </div>
    
  </form>
  
  <script src="{$domain}Resources/System/Javascript/tinymce4/tinymce.min.js"></script>
  <script language="javascript" type="text/javascript">
  {literal}

  tinymce.init({
      selector: "#tag-desc-tinymce",
      menubar: false,
      plugins: [
          "advlist autolink lists charmap print preview anchor",
          "searchreplace visualblocks code fullscreen",
          "media table contextmenu paste link wordcount"
      ],
      style_formats: [
          {title: 'Headers', items: [
              {title: 'h1', block: 'h1'},
              {title: 'h2', block: 'h2'},
              {title: 'h3', block: 'h3'},
              {title: 'h4', block: 'h4'},
              {title: 'h5', block: 'h5'},
              {title: 'h6', block: 'h6'}
          ]},

          {title: 'Blocks', items: [
              {title: 'p', block: 'p'},
              {title: 'div', block: 'div'},
              {title: 'pre', block: 'pre'}
          ]},

          {title: 'Containers', items: [
              {title: 'section', block: 'section', wrapper: true, merge_siblings: false},
              {title: 'article', block: 'article', wrapper: true, merge_siblings: false},
              {title: 'blockquote', block: 'blockquote', wrapper: true},
              {title: 'hgroup', block: 'hgroup', wrapper: true},
              {title: 'aside', block: 'aside', wrapper: true},
              {title: 'figure', block: 'figure', wrapper: true}
          ]}
      ],
    
      paste_word_valid_elements: "b,strong,i,em,h1,h2,h3,h4,p",
      toolbar: "styleselect | bold italic | link unlink | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent"

  });
  
  {/literal}

  </script>
  
</div>
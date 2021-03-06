<form action="{$domain}{$section}/updateAsset" method="post" name="newHtml" enctype="multipart/form-data">

  <input type="hidden" name="asset_id" value="{$asset.id}" />
  <input type="hidden" name="asset_type" value="{$asset.type}" />
    
    <div class="special-box">
      <span class="heading">Language</span>
      <select name="asset_language">
        <option value="">{$lang.label}</option>
    {foreach from=$_languages item="lang" key="langcode"}
        <option value="{$langcode}"{if $asset.language == $langcode} selected="selected"{/if}>{$lang.label}</option>
    {/foreach}
      </select>
    </div>
    
    {foreach from=$asset._editor_parameters key="parameter_name" item="parameter"}
    <div class="edit-form-row">
      <div class="form-section-label">{$parameter.label}</div>
      <input type="text" name="params[{$parameter_name}]" value="{$parameter.value}" style="width:250px" />
    </div>
    {/foreach}
    
    <div id="textarea-holder" style="width:100%">
        <textarea name="asset_content" id="tpl_textArea" wrap="virtual" style="width:100%;padding:0">{$textfragment_content}</textarea>
        <span class="form-hint">Editor powered by TinyMCE 4</span>
        <span id="wordcount"></span>
        <div class="buttons-bar">
            {save_buttons}
        </div>
    <div>
        
</form>

<!--<script language="javascript" type="text/javascript" src="{$domain}Resources/System/Javascript/tiny_mce/tiny_mce.js"></script>-->
<script src="{$domain}Resources/System/Javascript/tinymce4/tinymce.min.js"></script>

<script language="javascript" type="text/javascript">

{literal}

tinymce.init({
    selector: "#tpl_textArea",
    plugins: [
        "advlist autolink lists charmap print preview anchor",
        "searchreplace visualblocks code fullscreen",
        "table contextmenu paste link wordcount noneditable"
    ],
    style_formats: [
        {title: 'Headers', items: [
          {title: 'Header 2', block: 'h2'},
          {title: 'Header 3', block: 'h3'},
          {title: 'Header 4', block: 'h4'},
          {title: 'Header 5', block: 'h5'},
          {title: 'Header 6', block: 'h6'}
        ]},

        {title: 'Blocks', items: [
          {title: 'Paragraph', block: 'p'},
          {title: 'Blockquote', block: 'blockquote', wrapper: true},
          {title: 'div', block: 'div'},
          {title: 'pre', block: 'pre'}
        ]},

        {title: 'Containers', items: [
          {title: 'section', block: 'section', wrapper: true, merge_siblings: false},
          {title: 'article', block: 'article', wrapper: true, merge_siblings: false},
          {title: 'aside', block: 'aside', wrapper: true},
          {title: 'figure', block: 'figure', wrapper: true}
        ]}
    ],
    protect: [
        /\<xsl\:[^>]+\>/g,  // Protect <xsl:...>
        /<\?sm:.*?:\?>/g  // Protect php code
    ],
    paste_word_valid_elements: "b,strong,i,em,h1,h2,h3,h4,p",
    toolbar: "insertfile undo redo | styleselect | bold italic | link unlink | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | code",
    relative_urls : false,
    convert_urls: false,
    document_base_url : sm_domain,
    skin: "smartest"
});
  
  var AutoSaver = new PeriodicalExecuter(function(pe){
    // autosave routine
  }, 5);
  
{/literal}

</script>
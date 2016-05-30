<div class="edit-form-row">
  <div style="width:100%" id="text_window">
    <textarea name="content" id="tpl_textArea" wrap="virtual"></textarea>
    <span class="form-hint">Editor powered by TinyMCE 4</span>
  </div>
</div>

<script src="{$domain}Resources/System/Javascript/tinymce4/tinymce.min.js"></script>
<script language="javascript" type="text/javascript">
{literal}

tinymce.init({
    selector: "#tpl_textArea",
    plugins: [
        "advlist autolink lists charmap print preview anchor",
        "searchreplace visualblocks code fullscreen",
        "table contextmenu paste link wordcount"
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
    paste_word_valid_elements: "b,strong,i,em,h2,h3,h4,p",
    toolbar: "insertfile undo redo | styleselect | bold italic | link unlink | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | code"
});

{/literal}
</script>

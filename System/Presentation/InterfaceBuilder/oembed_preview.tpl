{if strlen($url_data.title)}
<div class="smartest-url-preview" id="smartest-url-preview-{$id}" style="background-image:url({$url_data.og_image_file.450x250.web_path})" data-url="{$url}">
  <span class="smartest-url-preview-label">Preview</span>
  <h4 class="smartest-url-preview-title"><a href="{$url}" target="_blank">{$url_data.title}</a></h4>
</div>
{/if}
<div id="work-area">
  <h3>New Uploads</h3>
  <form action="{$domain}{$section}/createAssetsFromNewUploads" method="post">
  <ul class="objects-list">
  {foreach from=$files item="file" name="files"}
    <li class="{cycle values="odd,even"}">
      
      {if $file.is_image}
      <a href="#preview" class="image-thumb-preview" data-file="{$file.current_directory}{$file.filename}"><img src="{$file.image.constrain_300x200.web_path}" alt="" style="width:{$file.image.constrain_150x100.width}px;height:{$file.image.constrain_150x100.height}px;float:right;display:block" /></a>
      {/if}
      
      <b>Label</b>: <input type="text" name="new_files[{$smarty.foreach.files.index}][name]" value="{$file.suggested_name}" /><br />
      <b>File Path</b>: <code>{$file.current_directory}{$file.filename}</code><input type="hidden" name="new_files[{$smarty.foreach.files.index}][filename]" value="{$file.current_directory}{$file.filename}" /><br />
      <b>Import as</b>:
      
      {if count($file.possible_types) > 1}
        {if $file.suffix_recognized}
        <select name="new_files[{$smarty.foreach.files.index}][type]">{foreach from=$file.possible_types item="ptype"}<option value="{$ptype.type.id}"{if $file.current_directory == $ptype.storage_location} selected="selected"{/if}>{$ptype.type.label}{if $file.current_directory != $ptype.storage_location} (will be moved to /{$ptype.storage_location}{$file.filename}){/if}</option>{/foreach}</select>
        {else}
        <select name="new_files[{$smarty.foreach.files.index}][type]">{foreach from=$file.possible_types item="ptype"}<option value="{$ptype.type.id}">{$ptype.type.label} (will be renamed {$ptype.filename})</option>{/foreach}</select>
        {/if}
      {else}
        {$file.possible_types[0].type.label}{if $file.current_directory != $file.possible_types[0].storage_location} (file will be moved to <code>{$file.possible_types[0].storage_location}{$file.filename}</code>){/if}<input type="hidden" name="new_files[{$smarty.foreach.files.index}][type]" value="{$file.possible_types[0].type.id}" />
      {/if}<br />
      
      <b>Size</b>: {$file.size}<br />
      <b>Shared</b>: <input type="checkbox" id="share_{$smarty.foreach.files.index}" name="new_files[{$smarty.foreach.files.index}][shared]" value="1" /><label for="share_{$smarty.foreach.files.index}">Check here to share this file with other sites</label><br />
      <b>Archive</b>: <input type="checkbox" id="archive_{$smarty.foreach.files.index}" name="new_files[{$smarty.foreach.files.index}][archive]" value="1" /><label for="archive_{$smarty.foreach.files.index}">Check here to archive this file straight away</label><br />
      {if !$file.suffix_recognized}<div class="warning">The suffix of this file (.{$file.actual_suffix}) has not been recognized.</div>{/if}
      
      {* {if count($file.possible_groups) && count($file.possible_types) == 1}
      <strong>Add to group</strong>:
      <div style="display:inline-block" id="groups-container-{$smarty.foreach.files.index}">
{foreach from=$file.possible_groups item="group"}
        <input type="checkbox" style="display:none" name="new_files[{$smarty.foreach.files.index}][groups][]" id="new-file-{$smarty.foreach.files.index}-group-{$group.id}"> <label for="new-file-{$smarty.foreach.files.index}-group-{$group.id}" class="checkbox-array">{$group.label}</label>
{/foreach}
      </div>
      <div class="breaker"></div>
      {/if} *}
      
    </li>
  {/foreach}
  </ul>
  <div class="buttons-bar">
    <input type="button" value="&lt;&lt; Back" onclick="window.location='{$domain}{$section}/detectNewUploads'" />
    <input type="submit" value="Finish" />
  </div>
  </form>
</div>

<script type="text/javascript">
{literal}
$$('a.image-thumb-preview').each(function(ipl){
  ipl.observe('click', function(e){
    e.stop();
    MODALS.load('assets/previewUnimportedImageFile?file_path='+ipl.readAttribute('data-file'), 'Unimported image preview');
  });
});
{/literal}
</script>
<select name="<?sm:$render_data.name:?>"<?sm:if $render_data.use_id:?> id="<?sm:$render_data.id:?>"<?sm:/if:?>>  
  <?sm:if $render_data.allow_blank:?><option value=""></option><?sm:/if:?>
  <?sm:foreach from=$render_data.options item="option":?>
    <option value="<?sm:$option.value:?>"<?sm:if $render_data.selected_value==$option.value:?> selected="selected"<?sm:/if:?>><?sm:$option.label:?></option>
  <?sm:/foreach:?>
</select>
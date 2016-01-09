<div{if $value.is_never} style="display:none"{/if} id="{$_input_data.id}-datefields">
  <div style="float:left;margin-right:10px;">
    Month<br />
    <select name="{$_input_data.name}[M]" style="width:100px">
    	<option value="{$_input_data.default_month}"{if $value.M == "00"} selected="selected"{/if}></option>
    	<option value="01"{if $value.M == "01" || (!$value.M && $_input_data.default_month == "01")} selected="selected"{/if}>January</option>
    	<option value="02"{if $value.M == "02" || (!$value.M && $_input_data.default_month == "02")} selected="selected"{/if}>February</option>
    	<option value="03"{if $value.M == "03" || (!$value.M && $_input_data.default_month == "03")} selected="selected"{/if}>March</option>
    	<option value="04"{if $value.M == "04" || (!$value.M && $_input_data.default_month == "04")} selected="selected"{/if}>April</option>
    	<option value="05"{if $value.M == "05" || (!$value.M && $_input_data.default_month == "05")} selected="selected"{/if}>May</option>
    	<option value="06"{if $value.M == "06" || (!$value.M && $_input_data.default_month == "06")} selected="selected"{/if}>June</option>
    	<option value="07"{if $value.M == "07" || (!$value.M && $_input_data.default_month == "07")} selected="selected"{/if}>July</option>
    	<option value="08"{if $value.M == "08" || (!$value.M && $_input_data.default_month == "08")} selected="selected"{/if}>August</option>
    	<option value="09"{if $value.M == "09" || (!$value.M && $_input_data.default_month == "09")} selected="selected"{/if}>September</option>
    	<option value="10"{if $value.M == "10" || (!$value.M && $_input_data.default_month == "10")} selected="selected"{/if}>October</option>
    	<option value="11"{if $value.M == "11" || (!$value.M && $_input_data.default_month == "11")} selected="selected"{/if}>November</option>
    	<option value="12"{if $value.M == "12" || (!$value.M && $_input_data.default_month == "12")} selected="selected"{/if}>December</option>
    </select>
  </div>
  <div style="float:left;margin-right:10px">
    Day<br />
    <select name="{$_input_data.name}[D]" style="width:70px">
    	<option value="{$_input_data.default_day}"{if $value.D == "00"} selected="selected"{/if}></option>
    	<option value="01"{if $value.D == "01" || (!$value.D && $_input_data.default_day == "01")} selected="selected"{/if}>1st</option>
    	<option value="02"{if $value.D == "02" || (!$value.D && $_input_data.default_day == "02")} selected="selected"{/if}>2nd</option>
    	<option value="03"{if $value.D == "03" || (!$value.D && $_input_data.default_day == "03")} selected="selected"{/if}>3rd</option>
    	<option value="04"{if $value.D == "04" || (!$value.D && $_input_data.default_day == "04")} selected="selected"{/if}>4th</option>
    	<option value="05"{if $value.D == "05" || (!$value.D && $_input_data.default_day == "05")} selected="selected"{/if}>5th</option>
    	<option value="06"{if $value.D == "06" || (!$value.D && $_input_data.default_day == "06")} selected="selected"{/if}>6th</option>
    	<option value="07"{if $value.D == "07" || (!$value.D && $_input_data.default_day == "07")} selected="selected"{/if}>7th</option>
    	<option value="08"{if $value.D == "08" || (!$value.D && $_input_data.default_day == "08")} selected="selected"{/if}>8th</option>
    	<option value="09"{if $value.D == "09" || (!$value.D && $_input_data.default_day == "09")} selected="selected"{/if}>9th</option>
    	<option value="10"{if $value.D == "10" || (!$value.D && $_input_data.default_day == "10")} selected="selected"{/if}>10th</option>
    	<option value="11"{if $value.D == "11" || (!$value.D && $_input_data.default_day == "11")} selected="selected"{/if}>11th</option>
    	<option value="12"{if $value.D == "12" || (!$value.D && $_input_data.default_day == "12")} selected="selected"{/if}>12th</option>
    	<option value="13"{if $value.D == "13" || (!$value.D && $_input_data.default_day == "13")} selected="selected"{/if}>13th</option>
    	<option value="14"{if $value.D == "14" || (!$value.D && $_input_data.default_day == "14")} selected="selected"{/if}>14th</option>
    	<option value="15"{if $value.D == "15" || (!$value.D && $_input_data.default_day == "15")} selected="selected"{/if}>15th</option>
    	<option value="16"{if $value.D == "16" || (!$value.D && $_input_data.default_day == "16")} selected="selected"{/if}>16th</option>
    	<option value="17"{if $value.D == "17" || (!$value.D && $_input_data.default_day == "17")} selected="selected"{/if}>17th</option>
    	<option value="18"{if $value.D == "18" || (!$value.D && $_input_data.default_day == "18")} selected="selected"{/if}>18th</option>
    	<option value="19"{if $value.D == "19" || (!$value.D && $_input_data.default_day == "19")} selected="selected"{/if}>19th</option>
    	<option value="20"{if $value.D == "20" || (!$value.D && $_input_data.default_day == "20")} selected="selected"{/if}>20th</option>
    	<option value="21"{if $value.D == "21" || (!$value.D && $_input_data.default_day == "21")} selected="selected"{/if}>21st</option>
    	<option value="22"{if $value.D == "22" || (!$value.D && $_input_data.default_day == "22")} selected="selected"{/if}>22nd</option>
    	<option value="23"{if $value.D == "23" || (!$value.D && $_input_data.default_day == "23")} selected="selected"{/if}>23rd</option>
    	<option value="24"{if $value.D == "24" || (!$value.D && $_input_data.default_day == "24")} selected="selected"{/if}>24th</option>
    	<option value="25"{if $value.D == "25" || (!$value.D && $_input_data.default_day == "25")} selected="selected"{/if}>25th</option>
    	<option value="26"{if $value.D == "26" || (!$value.D && $_input_data.default_day == "26")} selected="selected"{/if}>26th</option>
    	<option value="27"{if $value.D == "27" || (!$value.D && $_input_data.default_day == "27")} selected="selected"{/if}>27th</option>
    	<option value="28"{if $value.D == "28" || (!$value.D && $_input_data.default_day == "28")} selected="selected"{/if}>28th</option>
    	<option value="29"{if $value.D == "29" || (!$value.D && $_input_data.default_day == "29")} selected="selected"{/if}>29th</option>
    	<option value="30"{if $value.D == "30" || (!$value.D && $_input_data.default_day == "30")} selected="selected"{/if}>30th</option>
    	<option value="31"{if $value.D == "31" || (!$value.D && $_input_data.default_day == "31")} selected="selected"{/if}>31st</option>
    </select>
  </div>
  
  <div style="float:left">
    Year<br />
    <input type="text" name="{$_input_data.name}[Y]" size="5" maxlength="4" value="{if $value.Y}{$value.Y}{else}{$_input_data.default_year}{/if}" style="width:85px" />
  </div>
  
  <div class="breaker"></div>
  <div class="edit-form-sub-row">
    <a class="button small" href="#never" id="{$_input_data.id}-neverize">Clear value</a>
  </div>
  
</div>

<input type="hidden" name="{$_input_data.name}[is_never]" value="{if $value.is_never}1{else}0{/if}" id="{$_input_data.id}-is-never" />

<div id="{$_input_data.id}-no-value"{if !$value.is_never}style="display:none"{/if}><span style="color:#999">No date is recorded</span> <a class="button small" href="#add-value" id="{$_input_data.id}-add-value">Add value</a></div>

<script type="text/javascript">
(function(propertyId){ldelim}
{literal}
  var linkId = propertyId+'-add-value';
  var inputDivId = propertyId+'-datefields';
  var noteId = propertyId+'-no-value';
  
  $(linkId).observe('click', function(e){
    e.stop();
    Effect.Fade(noteId, {duration:0.15});
    Effect.Appear(inputDivId, {duration:0.15, delay:0.16});
    $(propertyId+'-is-never').value = 0;
  });
  
  $(propertyId+'-neverize').observe('click', function(e){
    e.stop();
    Effect.Fade(inputDivId, {duration:0.15});
    Effect.Appear(noteId, {duration:0.15, delay:0.16});
    $(propertyId+'-is-never').value = 1;
  });
  
{/literal}
{rdelim})('{$_input_data.id}');
</script>
<div id="track-{$_slider_input_data.name}" class="sm-numeric-slider">
	<div id="handle-{$_slider_input_data.name}" class="sm-numeric-slider-handle"></div>
</div>

<p id="value-{$_slider_input_data.name}" style="display:inline-block;margin:0px">{$_slider_input_data.value}{$_slider_input_data.value_unit}</p>

<input type="hidden" id="{$_slider_input_data.name}" name="{$_slider_input_data.name}" value="{$_slider_input_data.value}" />

<script type="text/javascript" language="javascript">
// <![CDATA[
		
var slider_{$_slider_input_data.name} = {literal}new Control.Slider('handle-{/literal}{$_slider_input_data.name}', 'track-{$_slider_input_data.name}', {literal}{
	onSlide: function(v) {
    $('value-{/literal}{$_slider_input_data.name}').innerHTML = Math.ceil({$_slider_input_data.minimum} + v * ({$_slider_input_data.maximum} - {$_slider_input_data.minimum})) + '{$_slider_input_data.value_unit}';
    $('{$_slider_input_data.name}').value = Math.ceil({$_slider_input_data.minimum} + v * ({$_slider_input_data.maximum} - {$_slider_input_data.minimum}));
    {if $_slider_input_data.slidehook}{$_slider_input_data.slidehook}(Math.ceil({$_slider_input_data.minimum} + v * ({$_slider_input_data.maximum} - {$_slider_input_data.minimum})), '{$_slider_input_data.name}');{/if}
    
  {literal}},
	onChange: function(v) {
    $('value-{/literal}{$_slider_input_data.name}').innerHTML = Math.ceil({$_slider_input_data.minimum} + v * ({$_slider_input_data.maximum} - {$_slider_input_data.minimum})) + '{$_slider_input_data.value_unit}';
    $('{$_slider_input_data.name}').value = Math.ceil({$_slider_input_data.minimum} + v * ({$_slider_input_data.maximum} - {$_slider_input_data.minimum}));
    {if $_slider_input_data.changehook}{$_slider_input_data.changehook}(Math.ceil({$_slider_input_data.minimum} + v * ({$_slider_input_data.maximum} - {$_slider_input_data.minimum})), '{$_slider_input_data.name}');{/if}
    
    {literal}}
});{/literal}

slider_{$_slider_input_data.name}.setValue({$_slider_input_data.js_value});

// ]]>
</script>
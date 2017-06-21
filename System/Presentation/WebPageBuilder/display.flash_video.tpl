<script type="text/javascript" src="<?sm:$domain:?>Resources/System/Javascript/flv_player/swfobject.js"></script>

<div id="sm_flv_<?sm:$asset_info.id:?>" style="width:<?sm:$render_data.width:?>px;height:<?sm:$render_data.height:?>px;background-color:rgba(0,0,0,0.2)" class="smartest-flash-content">
  <div style="margin:10px">This video requires <a href="https://get.adobe.com/flashplayer/" target="_blank">the Flash plugin</a> to play.</div>
</div>

<script type="text/javascript">
  var so = new SWFObject('<?sm:$domain:?>Resources/System/Assets/flv_player.swf','flv_asset_<?sm:$asset_info.stringid:?>','<?sm:$render_data.width:?>','<?sm:$render_data.height:?>','8');
  so.addParam('allowscriptaccess','always');
  so.addParam('allowfullscreen','true');
  so.addVariable('width','<?sm:$render_data.width:?>');
  so.addVariable('height','<?sm:$render_data.height:?>');
  so.addVariable('file','<?sm:$domain:?>Resources/Assets/<?sm:$asset_info.url:?>');
  so.addVariable('javascriptid','flv_asset_<?sm:$asset_info.stringid:?>');
  so.addVariable('enablejs','true');
  so.write('sm_flv_<?sm:$asset_info.id:?>');
</script>
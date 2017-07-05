<?sm:if $sm_user_agent.is_unsupported_browser:?>
<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B"
codebase="http://www.apple.com/qtactivex/qtplugin.cab" width="<?sm:$render_data.width:?>" height="<?sm:$render_data.height:?>" >
<param name="src" value="<?sm:$domain:?>Resources/Assets/<?sm:$asset_info.encoded_url:?>" />
<param name="autoplay" value="<?sm:$render_data.auto_start:?>" />
<param name="controller" value="<?sm:$render_data.show_controller:?>" />
<embed src="<?sm:$domain:?>Resources/Assets/<?sm:$asset_info.encoded_url:?>" type="<?sm:$asset_info.mime_type:?>" controller="<?sm:$render_data.show_controller:?>" 
pluginspage="http://www.apple.com/quicktime/download" width="<?sm:$render_data.width:?>" height="<?sm:$render_data.height:?>" autoplay="<?sm:$render_data.auto_start:?>"></embed>
</object>
<?sm:else:?>
<video width="<?sm:$render_data.width:?>" height="<?sm:$render_data.height:?>"<?sm: if _b($render_data.auto_start):?> autoplay<?sm:/if:?><?sm: if _b($render_data.show_controller):?> controls<?sm:/if:?>>
  <source src="<?sm:$domain:?>Resources/Assets/<?sm:$asset_info.encoded_url:?>" type="video/mp4">
  Your browser does not support the video tag.
</video>
<?sm:/if:?>
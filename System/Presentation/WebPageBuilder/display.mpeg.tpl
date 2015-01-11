<?sm:$sm_user_agent:?>

<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B"
codebase="http://www.apple.com/qtactivex/qtplugin.cab" width="<?sm:$render_data.width:?>" height="<?sm:$render_data.height:?>" >
<param name="src" value="<?sm:$domain:?>Resources/Assets/<?sm:$asset_info.url:?>" />
<param name="autoplay" value="<?sm:$render_data.auto_start:?>" />
<param name="controller" value="<?sm:$render_data.show_controller:?>" />
<embed src="<?sm:$domain:?>Resources/Assets/<?sm:$asset_info.url:?>" type="<?sm:$asset_info.mime_type:?>" controller="<?sm:$render_data.show_controller:?>" 
pluginspage="http://www.apple.com/quicktime/download" width="<?sm:$render_data.width:?>" height="<?sm:$render_data.height:?>" autoplay="<?sm:$render_data.auto_start:?>"></embed>
</object>
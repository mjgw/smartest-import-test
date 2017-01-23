<!--Smartest Text Attachment: <?sm:$_textattachment._name:?>-->
<figure class="sm-attachment" style="<?sm:if $_textattachment.float && $_textattachment.alignment != 'center' :?>width:<?sm:$_textattachment.div_width :?>px;<?sm:/if:?><?sm:if $_textattachment.border :?>border: 1px solid #ccc;<?sm:/if:?><?sm:if $_textattachment.float && $_textattachment.alignment != 'center' :?>float: <?sm:else:?>text-align: <?sm:/if:?><?sm:$_textattachment.alignment:?>; margin<?sm:if $_textattachment.alignment == "right" :?>-left<?sm:else if $_textattachment.alignment == "left" :?>-right<?sm:/if:?>: 10px;">
    
  <a onclick="return hs.expand(this)" href="<?sm:$_textattachment.asset.web_path :?>" title="<?sm:$_textattachment.caption:?>"><img src="<?sm:$_textattachment.thumbnail.url:?>" style="border:0px" alt="" /></a>
  <figcaption class="sm-attachment-caption" style="text-align:<?sm:$_textattachment.caption_alignment :?>;display:block; margin:5px 0 5px 0;font-size:0.8em;<?sm:if $_textattachment.float && $_textattachment.alignment != 'center' :?>width:<?sm:$_textattachment.thumbnail.width :?>px<?sm:/if:?>"><?sm:$_textattachment.caption:?> (Click to magnify) <?sm:if strlen($_textattachment.asset.render_data.credit):?>Image: <?sm:$_textattachment.asset.render_data.credit:?><?sm:/if:?> <?sm:$_textattachment.edit_link:?></figcaption>

</figure>
<!SM_ATT_HB>
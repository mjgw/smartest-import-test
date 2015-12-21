<div id="work-area">
  {load_interface file="edit_set_tabs.tpl"}
  <h3>Syndicate set: <span class="light">&ldquo;{$set.label}&rdquo;</span></h3>
  <form action="{$domain}sets/updateSetSyndication" method="post">
    <input type="hidden" name="set_id" value="{$set.id}" />
    
    <div class="edit-form-row">
      <div class="form-section-label">Syndicate as RSS</div>
      {boolean name="set_syndicate_as_rss" id="set-syndicate-as-rss" changehook="toggleRssSettingsVisibility" value=$set_syndicate_as_rss}
      <div class="form-hint">When syndicated, the RSS feed for this set will be available at: <strong>http://{$_site.domain}{$domain}feeds/rss/{$set.feed_nonce}/{$set.name}.xml</strong></div>
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Syndicate as Atom</div>
      {boolean name="set_syndicate_as_atom" id="set-syndicate-as-atom" changehook="test" value=$set_syndicate_as_atom}
      <div class="form-hint">When syndicated, the Atom feed for this set will be available at: <strong>http://{$_site.domain}{$domain}feeds/atom/{$set.feed_nonce}/{$set.name}.xml</strong></div>
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Syndicate as podcast to iTunes</div>
      {boolean name="set_syndicate_as_itunes" id="set-syndicate-as-itunes" changehook="test" value=$set_syndicate_as_itunes}
      <div class="form-hint">When syndicated, the iTunes RSS feed for this set will be available at: <strong>itpc://{$_site.domain}{$domain}feeds/itunes/{$set.feed_nonce}/{$set.name}.xml</strong></div>
    </div>
    
    <div class="form-section-label-full">Feed settings</div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Decription</div>
      <div class="breaker"></div>
      <textarea name="set_feed_description_text" id="description_textarea">{$set_feed_description_contents}</textarea>
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Feed image</div>
      {image_select name="rss_channel_image_id" id="rss-channel-image" value=$set_rss_feed_image}
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Feed author</div>
      <input type="text" name="set_feed_author" value="{$set_feed_author}" />
    </div>
    
    <div class="v-spacer"></div>
    
    <div class="buttons-bar">
      <input type="submit" value="Save" />
    </div>
    
  </form>
  
  <script src="{$domain}Resources/System/Javascript/tinymce4/tinymce.min.js"></script>
  <script type="text/javascript">
  {literal}
  
  var toggleRssSettingsVisibility = function(state){
    if(state){
      // Effect.BlindDown('rss-settings-container', {duration: 0.3});
    }else{
      // Effect.BlindUp('rss-settings-container', {duration: 0.3});
    }
  }
  
  tinymce.init({
      selector: "#description_textarea",
      plugins: [
          "advlist autolink lists charmap anchor",
          "paste link wordcount"
      ],
      paste_word_valid_elements: "b,strong,i,em,h1,h2,h3,h4,p",
      toolbar: "undo redo | styleselect | bold italic | link unlink"
  });
  
  {/literal}
  </script>
  
</div>

<div id="actions-area">
  
</div>
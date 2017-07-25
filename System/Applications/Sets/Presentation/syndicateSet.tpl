<div id="work-area">
  {load_interface file="edit_set_tabs.tpl"}
  <h3>Syndicate set: <span class="light">&ldquo;{$set.label}&rdquo;</span></h3>
  <form action="{$domain}sets/updateSetSyndication" method="post" id="syndication-form">
    <input type="hidden" name="set_id" value="{$set.id}" />
    
    <div class="edit-form-row">
      <div class="form-section-label">Syndicate as RSS</div>
      {boolean name="set_syndicate_as_rss" id="set-syndicate-as-rss" changehook="toggleRssSettingsVisibility" value=$set_syndicate_as_rss}
      <div class="form-hint">When syndicated, the RSS feed for this set will be available at: <strong>http://{$_site.domain}{$domain}feeds/rss/{$set.feed_nonce}/{$set.name}.xml</strong></div>
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Syndicate as Atom</div>
      {boolean name="set_syndicate_as_atom" id="set-syndicate-as-atom" value=$set_syndicate_as_atom}
      <div class="form-hint">When syndicated, the Atom feed for this set will be available at: <strong>http://{$_site.domain}{$domain}feeds/atom/{$set.feed_nonce}/{$set.name}.xml</strong></div>
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Syndicate to Apple Podcasts</div>
      {boolean name="set_syndicate_as_itunes" id="set-syndicate-as-itunes" value=$set_syndicate_as_itunes}
      <div class="form-hint">When syndicated, the Apple Podcasts XML feed for this set will be available at: <strong>itpc://{$_site.domain}{$domain}feeds/applepodcasts/{$set.feed_nonce}/{$set.name}.xml</strong></div>
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Syndicate as JSON</div>
      {boolean name="set_syndicate_as_json" id="set-syndicate-as-json" value=$set_syndicate_as_json}
      <div class="form-hint">When syndicated, the JSON feed for this set will be available at: <strong>http://{$_site.domain}{$domain}feeds/json/{$set.feed_nonce}/{$set.name}.json</strong></div>
    </div>
    
    <div class="form-section-label-full">Feed settings</div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Feed image</div>
      {image_select name="rss_channel_image_id" id="rss-channel-image" value=$set_rss_feed_image}
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Feed author</div>
      <input type="text" name="set_feed_author" value="{$set_feed_author}" />
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Decription</div>
      <div class="edit-form-sub-row">
        <textarea name="set_feed_description_text" id="description_textarea">{$set_feed_description_contents}</textarea>
      </div>
    </div>
    
  </form>
  
  <script src="{$domain}Resources/System/Javascript/tinymce4/tinymce.min.js"></script>
  <script type="text/javascript">
  {literal}
  
  var restartSavingTimer;
  
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
      toolbar: "undo redo | styleselect | bold italic | link unlink",
      relative_urls : false,
      convert_urls: false,
      document_base_url : sm_domain,
      skin: "smartest"
  });
  
  var saveForm = function(callbackFunction){
    $('primary-ajax-loader').show();
    $('syndication-form').request({
      onSuccess: function(){
        $('primary-ajax-loader').hide();
        if(callbackFunction && typeof callbackFunction == 'function'){
          callbackFunction();
        }
      }
    });
  }
  
  var respondToUserAction = function(){
    if(restartSavingTimer){
      clearTimeout(restartSavingTimer);
    }
    restartSavingTimer = setTimeout(function(){
      saveForm();
    }, 2000);
  }
  
  $('syndication-form').observe('keyup', respondToUserAction);
  $('syndication-form').observe('click', respondToUserAction);
  $('syndication-form').observe('image:chosen', respondToUserAction);
  $('syndication-form').observe('switch:changed', respondToUserAction);
  $$('#syndication-form select').each(function(select){
    select.observe('change', respondToUserAction);
  });
  
  {/literal}
  </script>
  
</div>
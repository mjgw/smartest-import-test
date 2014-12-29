<script type="text/javascript">
// <![CDATA[
  {literal}
  Smartest.AjaxModalViewer.variables.deleteAssetComment = function(commentId){
    if(confirm('Really delete this note?')){
      var commentDomId = 'comment-'+commentId;
      $(commentDomId).fade({duration: 0.5});
      setTimeout(function(){
        if(!$('none-yet') && $$('#comments-list li').length == 1){
            var div = new Element('div', {class: 'instruction'}).update('No notes yet');
            var li = new Element('li', {style: 'padding:5px;', id: 'none-yet'});
            li.appendChild(div);
            $('comments-list').appendChild(li);
          }
        }, 510);
      new Ajax.Request(sm_domain+'ajax:assets/removeAssetComment?comment_id='+commentId, {
        onSuccess: function(){
          new Ajax.Updater('comment-stream', sm_domain+'ajax:assets/assetComments', {
            parameters: {'asset_id': Smartest.AjaxModalViewer.variables.asset_id},
            evalScripts: true,
            onComplete: function(){
              Smartest.AjaxModalScroller = new Control.ScrollBar('modal-updater', 'modal-scrollbar-track');
              Smartest.AjaxModalScroller.scrollTo('bottom');
            }
          });
          $('comment-content').value = ' ';
        }
      });
    }
  }
  {/literal}
  // ]]>
</script>

    <ul id="comments-list">
{foreach from=$comments item="comment"}
      <li id="comment-{$comment.id}">
        <div class="comment-user-avatar" style="{if $comment.user.profile_pic.id && $comment.user.profile_pic.url != 'default_user_profile_pic.jpg'}background-image:url({if $sm_user_agent.is_supported_browser}{$comment.user.profile_pic.image.80x80.web_path}{else}{$comment.user.profile_pic.image.40x40.web_path}{/if}){else}background-color:#{$comment.user.temporary_colour.hex};color:{if $comment.user.temporary_colour.text_white}#fff{else}#000{/if}{/if}">
          {if !$comment.user.profile_pic.id || $comment.user.profile_pic.url == 'default_user_profile_pic.jpg'}{$comment.user.profile_initials}{/if}
        </div>
        <div class="comment-content">
          <p><b>{$comment.user.full_name}</b>, {$comment.posted_at}</p>
          <p>{$comment.content}</p>
          {if $_user.id == $comment.user.id}<p style="font-size:10px"><a href="#delete-comment-{$comment.id}" id="delete-comment-link-{$comment.id}" class="comment-delete button small" data-commentid="{$comment.id}">Delete</a></p>{/if}
        </div>
        <div class="breaker"></div>
      </li>
{foreachelse}
      <li style="padding:5px;" id="none-yet"><div class="instruction">No notes yet</div></li>
{/foreach}
    </ul>
    
<script type="text/javascript">
    $$('a.comment-delete').each(function(cdl){ldelim}
        cdl.observe('click', function(e){ldelim}
          Smartest.AjaxModalViewer.variables.deleteAssetComment(cdl.readAttribute('data-commentid'));
          e.stop();
        {rdelim});
    {rdelim});
</script>
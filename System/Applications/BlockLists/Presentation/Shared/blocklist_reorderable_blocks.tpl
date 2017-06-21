<ul id="blocks-sortable" class="sortable-content">
{foreach from=$blocks item="block"}
  <li class="block" id="sortableblock_{$block.id}">
    {$block.order_index} {$block.name}
  </li>
{foreachelse}
  <p><em>There are no blocks in this blocklist yet.</em></p>
{/foreach}
</ul>

{if count($blocks)}
<script type="text/javascript" src="/Resources/System/Javascript/scriptaculous/src/dragdrop.js"></script>
<script type="text/javascript">

var url = sm_domain+'ajax:blocklists/updateBlockOrder';
var blocklistId = {$blocklist.id};
{literal}
var IDs;
var IDs_string;

var itemsList = Sortable.create('blocks-sortable', {
      
      onUpdate: function(){
          
        IDs = Sortable.sequence('blocks-sortable');
        IDs_string = IDs.join(',');
        $('primary-ajax-loader').show();
        
        new Ajax.Request(url, {
          method: 'get',
          parameters: {blocklist_id: blocklistId, new_order: IDs_string},
          onSuccess: function(transport) {
            $('primary-ajax-loader').hide();
          }
        });
      },
      
//      handles:$$('#sortable-gallery-members li p a.handle'),
      
      constraint: false,
      scroll: window,
      scrollSensitivity: 35
      
  });
{/literal}
</script>
{/if}
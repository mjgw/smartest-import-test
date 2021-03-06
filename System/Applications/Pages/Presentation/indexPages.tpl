<div id="modal-work-area">
  
  <div id="test-id">
    <span id="indexing-info">Ready to start indexing</span>
  </div>
  
  <div class="v-spacer"> </div>
  
  <div id="indexing-progress-bar" class="progress-bar-outer">
    <div class="progress-bar-inner" id="indexing-progress-inner" style="width:0%;display:none"> </div>
  </div>
  
  <div class="buttons-bar">
    <a href="#index" class="button" id="indexing-start-button">Start</a>
    <a href="#done" class="button" id="indexing-done-button" style="display:none">Done</a>
  </div>
  
  <script type="text/javascript">// <![CDATA[

    {literal}
  
    var indexPageAndFollowUp = function(page_num){
      
      new Ajax.Request(sm_domain+'ajax:websitemanager/bulkIndexPages', {
        parameters: {
          page_num: page_num
        },
        onSuccess: function(response){
          if(response.responseJSON.current_page_num){
            responseInfo = response.responseJSON;
            $('indexing-progress-inner').show();
            if(responseInfo.next_page_num){
              $('indexing-progress-inner').style.width=responseInfo.percent_completed+'%';
              $('indexing-info').update('Indexing... ('+responseInfo.num_completed+' of '+responseInfo.num_cms_pages+' completed)');
              indexPageAndFollowUp(responseInfo.next_page_num);
            }else{
              $('indexing-progress-inner').style.width='100%';
              $('indexing-info').update('Successfully completed indexing. '+responseInfo.num_cms_pages+' pages indexed');
              $('indexing-start-button').hide();
              $('indexing-done-button').show();
            }
          }else{
            // No valid data returned
          }
        }
      });
    }
  
    $('indexing-start-button').observe('click', function(e){
      e.stop();
      $('indexing-info').update('Indexing pages...');
      $('indexing-progress-inner').show();
      $('indexing-progress-inner').style.width='10px';
      indexPageAndFollowUp(1);
    });
    
    $('indexing-done-button').observe('click', function(e){
      e.stop();
      MODALS.hideViewer();
    });
  
    {/literal}
  // ]]>
  </script>
  
</div>
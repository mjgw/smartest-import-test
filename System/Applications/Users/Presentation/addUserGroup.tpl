<div id="work-area">
  
  <script type="text/javascript">
      var itemNameFieldDefaultValue = '{$start_name}';
      var nameFieldFocussed = false;
  {literal}
      document.observe('dom:loaded', function(){
        
          $('user-group-label').observe('focus', function(){
              if($('user-group-label').getValue() == itemNameFieldDefaultValue || $('user-group-label').getValue() == ''){
                  $('user-group-label').removeClassName('unfilled');
                  $('user-group-label').setValue('');
              }
              nameFieldFocussed = true;
          });
        
          $('user-group-label').observe('blur', function(){
              if($('user-group-label').getValue() == itemNameFieldDefaultValue || $('user-group-label').getValue() == ''){
                  $('user-group-label').addClassName('unfilled');
                  $('user-group-label').setValue(itemNameFieldDefaultValue);
              }else{
                  $('user-group-label').removeClassName('error');
              }
              nameFieldFocussed = false;
          });
          
          $('user-group-label').observe('keyup', function(){
              if($('user-group-label').getValue() == itemNameFieldDefaultValue || $('user-group-label').getValue() == ''){
                  
              }else{
                  $('user-group-label').removeClassName('error');
              }
          });
        
          $('new-group-form').observe('submit', function(e){
            
              if($('user-group-label').value == '' || $('user-group-label').value == itemNameFieldDefaultValue){
                  $('user-group-label').addClassName('error');
                  e.stop();
              }
            
          });
        
          document.observe('keypress', function(e){
            
              if(e.keyCode == 13){
            
                  if(nameFieldFocussed && ($('user-group-label').value == 'Unnamed file group' || $('user-group-label').value == 'Unnamed gallery' || $('user-group-label').value == itemNameFieldDefaultValue || !$('user-group-label').value.charAt(0))){
                      $('user-group-label').addClassName('error');
                      e.stop();
                  }
            
              }
            
          });
        
      });
    
  {/literal}
  </script>
  
  <h3>Add user group</h3>
  
  <form action="{$domain}users/insertUserGroup" method="post" id="new-group-form">
    
    <div id="edit-form-layout">
      
      <div class="edit-form-row">
        <div class="form-section-label">Name this user group</div>
        <input type="text" name="user_group_label" value="{$start_name}" id="user-group-label" class="unfilled" />
      </div>
      
      <div class="edit-form-row">
        <div class="form-section-label">Type of users that can be included</div>
        <select name="user_group_mode" id="user-group-mode-select">
          <option value="SM_USERTYPE_ANY">All users</option>
          <option value="SM_USERTYPE_SYSTEM_USER">System users only</option>
          <option value="SM_USERTYPE_ORDINARY_USER">Ordinary users only</option>
        </select>
        <div class="form-hint" id="filegroup-type-hint">System users are those that can log into Smartest; ordinary users are user accounts created for any other purposes</div>
      </div>
      
      <div class="edit-form-row">
        <div class="buttons-bar">
          <input type="button" onclick="cancelForm();" value="Cancel" />
          <input type="submit" value="Save new group" id="save-button" />
        </div>
      </div>
      
    </div>
    
  </form>
  
</div>

<div id="actions-area">
  
</div>
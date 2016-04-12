<div id="work-area">
  <h3>Test OAuth client account</h3>
  <div class="special-box">
    <p><strong>Service</strong>: {$account.service.label}</p>
    <p><strong>Username</strong>: {$account.service.username_prefix}{$account.info.username}</p>
    <p><strong>Account created</strong>: {date_format date=$account.register_date}</p>
  </div>
</div>
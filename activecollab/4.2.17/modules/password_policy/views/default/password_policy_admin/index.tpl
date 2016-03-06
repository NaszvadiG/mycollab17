<div id="password_policy_settings">
  {form action=Router::assemble('password_policy_admin')}
    <div class="content_stack_wrapper">
      <div class="content_stack_element odd">
        <div class="content_stack_element_info">
          <h3>{lang}Password Requirements{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          {wrap field=min_password_length}
            {select_min_password_length name='password_policy[password_policy_min_length]' value=$password_policy_data.password_policy_min_length label='Min. Password Length'}
          {/wrap}

          {wrap field=password_policy_require_numbers}
            {yes_no name='password_policy[password_policy_require_numbers]' value=$password_policy_data.password_policy_require_numbers label='Require Numbers'}
          {/wrap}

          {wrap field=password_policy_require_mixed_case}
            {yes_no name='password_policy[password_policy_require_mixed_case]' value=$password_policy_data.password_policy_require_mixed_case label='Require Lowercase and Uppercase Letters'}
          {/wrap}

          {wrap field=password_policy_require_symbols}
            {yes_no name='password_policy[password_policy_require_symbols]' value=$password_policy_data.password_policy_require_symbols label='Require Symbols'}
          {/wrap}
        </div>
      </div>

      <div class="content_stack_element even">
        <div class="content_stack_element_info">
          <h3>{lang}Auto Expiry{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          {wrap field=password_policy_auto_expire}
            {select_password_auto_expire name='password_policy[password_policy_auto_expire]' value=$password_policy_data.password_policy_auto_expire label='Passwords Auto-Expire After'}
          {/wrap}
        </div>
      </div>

      <div class="content_stack_element even">
        <div class="content_stack_element_info">
          <h3>{lang}Expire Passwords{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          {wrap field=expire_passwords}
            {checkbox name=expire_passwords label='Mark All Passwords as Expired' value=false id=expire_passwords}
            <p class="aid" id="expire_passwords_aid">{lang}Select this option to mark all account passwords as expired (except yours){/lang}</p>
          {/wrap}
        </div>
      </div>
    </div>

    
    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>

<script type="text/javascript">
  $('#password_policy_settings #expire_passwords').click(function() {
    if(this.checked) {
      $('#expire_passwords_aid').addClass('expire_passwords_alert').text(App.lang('By submitting this form, you will mark all account passwords as expired! (except yours)'));
    } else {
      $('#expire_passwords_aid').removeClass('expire_passwords_alert').text(App.lang('Select this option to mark all account passwords as expired (except yours)'));
    } // if
  });
</script>
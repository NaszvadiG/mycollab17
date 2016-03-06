{title}Company and Role{/title}
{add_bread_crumb}Company and Role{/add_bread_crumb}

<div id="user_edit_company_and_role">
  {form action=$active_user->getEditCompanyAndRoleUrl() csfr_protect=true}
    {wrap_fields}
	    {wrap field=company_id}
	      {select_company name='user[company_id]' exclude=$exclude_ids value=$user_data.company_id user=$logged_user optional=false id=userCompanyId required=true can_create_new=false label='Company'}
	    {/wrap}

      {wrap field=role_and_permissions}
        {if Users::isLastAdministrator($active_user)}
          {label}Role and Permissions{/label}
          <p>{lang}Administrator{/lang} &mdash; {lang}Role of last administrator account can't be changed{/lang}</p>
        {else}
          {select_user_role name='user' value=$user_data required=true label='Role and Permissions'}
        {/if}
      {/wrap}
    {/wrap_fields}
    
    {wrap_buttons}
    	{submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>
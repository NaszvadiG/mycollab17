{title user_display_name=$active_user->getDisplayName()}Log in As :user_display_name{/title}
{add_bread_crumb}Log in As{/add_bread_crumb}

<div id="login_as">
  {form action=$active_user->getLoginAsUrl() method=post}
    <p>{lang user_display_name=$active_user->getDisplayName()}One click log in as :user_display_name{/lang}</p>

    {wrap_buttons}
      {submit}Sign In{/submit}
    {/wrap_buttons}
  {/form}
</div>
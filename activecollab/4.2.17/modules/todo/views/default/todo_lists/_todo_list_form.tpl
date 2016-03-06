<script type="text/javascript">
  App.widgets.FlyoutDialog.front().setAutoSize(false);
</script>

<div class="big_form_wrapper {if $active_todo_list->isNew()}two_form_sidebars{else}one_form_sidebar{/if}">
  <div class="main_form_column">
  	{wrap field=name}
  	  {text_field name='todo_list[name]' value=$todo_list_data.name id=todoListName class='title required validate_minlength 3' required=true label="Name" maxlength="150"}
  	{/wrap}
  	
    {wrap_editor field=body}
      {label}Description{/label}
  	  {editor_field name='todo_list[body]' id=todoListBody inline_attachments=$todo_list_data.inline_attachments object=$active_todo_list}{$todo_list_data.body nofilter}{/editor_field}
  	{/wrap_editor}
  </div>

  <div class="form_sidebar {if $active_todo_list->isNew()}form_first_sidebar{else}form_second_sidebar{/if}">
    {if Milestones::canAccess($logged_user, $active_project)}
		  {wrap field=milestone_id}
		    {label for=todoListMilestone}Milestone{/label}
		    {select_milestone name='todo_list[milestone_id]' value=$todo_list_data.milestone_id id=todoListMilestone project=$active_project user=$logged_user}
		  {/wrap}
		{/if}
    
    {wrap field=category_id}
      {select_todo_list_category name="todo_list[category_id]" value=$todo_list_data.category_id parent=$active_project user=$logged_user label='Category' success_event="category_created"}
    {/wrap}
		  
		{if $logged_user->canSeePrivate()}
		  {wrap field=visibility}
		    {label for=todoListVisibility}Visibility{/label}
		    {select_visibility name='todo_list[visibility]' value=$todo_list_data.visibility short_description=true object=$active_todo_list}
		  {/wrap}
		{else}
		  <input type="hidden" name="todo_list[visibility]" value="1">
		{/if}
  </div>
  {if $active_todo_list->isNew()}
    <div class="form_sidebar form_second_sidebar">
      {wrap field=notify_users}
        {select_subscribers name="notify_users" exclude=$active_todo_list_data.exclude_ids object=$active_todo_list user=$logged_user label='Notify People'}
        <div class="clear"></div>
      {/wrap}
    </div>
  {/if}
</div>
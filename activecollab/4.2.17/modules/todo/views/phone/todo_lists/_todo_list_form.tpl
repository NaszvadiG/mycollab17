{wrap field=name}
  {text_field name='todo_list[name]' value=$todo_list_data.name id=todo_list_name label='Name' required=true}
{/wrap}
  	
{wrap_editor field=body}
  {editor_field name='todo_list[body]' label='Description' id=todo_list_description}{$todo_list_data.body nofilter}{/editor_field}
{/wrap_editor}

{if $logged_user->canSeeMilestones($active_project)}
  {wrap field=milestone_id}
    {select_milestone name='todo_list[milestone_id]' value=$todo_list_data.milestone_id project=$active_project user=$logged_user label='Milestone' id=todo_list_milestone}
  {/wrap}
{/if}
		  
{if $logged_user->canSeePrivate()}
  {wrap field=visibility}
    {select_visibility name='todo_list[visibility]' value=$todo_list_data.visibility label='Visibility' id=todo_list_visibility object=$active_todo_list}
  {/wrap}
{else}
  <input type="hidden" name="todo_list[visibility]" value="1">
{/if}

<script type="text/javascript">
	$(document).ready(function() {
		App.Wireframe.SelectBox.init();
	});
</script>
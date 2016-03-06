{title}Edit Todo List{/title}
{add_bread_crumb}Update{/add_bread_crumb}

<div id="edit_todo_list">
  {form action=$active_todo_list->getEditUrl()}
    {include file=get_view_path('_todo_list_form', 'todo_lists', $smarty.const.TODO_MODULE)}
    
    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>
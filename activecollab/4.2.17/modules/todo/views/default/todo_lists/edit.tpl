{title}Edit Todo List{/title}
{add_bread_crumb}Edit Todo List{/add_bread_crumb}

<div id="edit_todo_list">
  {form action=$active_todo_list->getEditUrl() method=post class="big_form"}
    {include file=get_view_path('_todo_list_form', 'todo_lists', $smarty.const.TODO_MODULE)}
    
    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>
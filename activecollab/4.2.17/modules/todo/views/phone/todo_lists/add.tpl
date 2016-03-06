{title}New Todo List{/title}
{add_bread_crumb}New{/add_bread_crumb}

<div id="add_todo_list">
  {form action=$add_todo_list_url}
    {include file=get_view_path('_todo_list_form', 'todo_lists', $smarty.const.TODO_MODULE)}
    
    {wrap_buttons}
      {submit}Add Todo List{/submit}
    {/wrap_buttons}
  {/form}
</div>
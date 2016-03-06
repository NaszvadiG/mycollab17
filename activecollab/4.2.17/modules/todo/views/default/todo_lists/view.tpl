{add_bread_crumb}Details{/add_bread_crumb}

{object object=$active_todo_list user=$logged_user id=todo_list_details}
  <div class="wireframe_content_wrapper">{object_subtasks object=$active_todo_list user=$logged_user}</div>
  <div class="wireframe_content_wrapper">{object_history object=$active_todo_list user=$logged_user}</div>
{/object}
{title lang=false}{$active_todo_list->getName()}{/title}
{add_bread_crumb}Details{/add_bread_crumb}

{object object=$active_todo_list user=$logged_user}
	{object_subtasks object=$active_todo_list user=$logged_user}
{/object}
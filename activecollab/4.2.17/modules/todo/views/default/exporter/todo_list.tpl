<div id="object_main_info" class="object_info">
  <h1>{lang}Todo List{/lang}: {$todo_list->getName()}</h1>
</div>

<div id="object_details" class="object_info">
  {project_exporter_object_properties object=$todo_list}
</div>

{project_exporter_object_subtasks object=$todo_list}
{project_exporter_object_timerecords object=$todo_list user=$logged_user}
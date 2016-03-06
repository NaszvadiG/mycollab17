{title}Todo List Archive{/title}
{add_bread_crumb}List{/add_bread_crumb}

<div id="todo_lists">
  <div class="empty_content">
    <div class="objects_list_title">{lang}Todo List Archive{/lang}</div>
    <div class="objects_list_icon"><img src="{image_url name='icons/48x48/todolists.png' module=$smarty.const.TODO_MODULE}" alt=""/></div>
    <div class="object_list_details_additional_actions">
        <a href="{assemble route='project_todo_lists' project_slug=$active_project->getSlug()}" id="view_archive"><span>{lang}Browse Active{/lang}</span></a>
    </div>
    <div class="object_lists_details_tips">
      <h3>{lang}Tips{/lang}:</h3>
      <ul>
        <li>{lang}To select a todo list and load its details, please click on it in the list on the left{/lang}</li>
        <li>{lang}It is possible to select multiple todo lists at the same time. Just hold Ctrl key on your keyboard and click on all the todo lists that you want to select{/lang}</li>
      </ul>
    </div>
  </div>
</div>

{include file=get_view_path('_initialize_objects_list', 'todo_lists', $smarty.const.TODO_MODULE)}
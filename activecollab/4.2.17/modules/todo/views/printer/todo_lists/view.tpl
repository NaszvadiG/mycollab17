<div id="print_container">
{object object=$active_todo_list user=$logged_user}
  <div class="wireframe_content_wrapper">
    <div class="object_body with_shadow">
      <div class="object_content_wrapper"><div class="object_body_content formatted_content">
        {if $active_todo_list->inspector()->hasBody()}
          {$active_todo_list->inspector()->getBody() nofilter}
        {else}
          {lang}No description provided{/lang}
        {/if}
      </div>
      </div>
      {object_subtasks object=$active_todo_list user=$logged_user}
    </div>
  </div>
  
  <div class="wireframe_content_wrapper">{object_history object=$active_todo_list user=$logged_user}</div>
{/object}
</div>

<div id="object_main_info" class="object_info">
  <h1>{lang}Todo Lists{/lang}</h1>
</div>

<div class="object_info">
  <h3>{lang}Active Todo Lists{/lang}</h3>
  {if is_foreachable($active_objects)}
    {project_exporter_list_objects objects=$active_objects url_prefix='./' show_priority=true}
  {else}
    <p>{lang}There are no active todo lists in this project{/lang}</p>
  {/if}
</div>

{if is_foreachable($completed_objects)}
  <div class="object_info">
    <h3>{lang}Completed Todo Lists{/lang}</h3>
    {project_exporter_list_objects objects=$completed_objects url_prefix='./' show_priority=true}
  </div>
{/if}

<div class="clear"></div>
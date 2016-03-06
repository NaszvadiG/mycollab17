{title}All Todo Lists{/title}
{add_bread_crumb}All{/add_bread_crumb}

<div id="todo_lists">
	<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
		<li data-role="list-divider"><img src="{image_url name="icons/listviews/navigate-icon.png" module=$smarty.const.SYSTEM_MODULE interface=AngieApplication::INTERFACE_PHONE}" class="divider_icon" alt="">{lang}Navigate{/lang}</li>
		{if is_foreachable($todo_lists)}
			{foreach $todo_lists as $todo_list}
				<li><a href="{replace search='--TODOLISTID--' in=$todo_list_url replacement=$todo_list.id}">{$todo_list.name}</a></li>
			{/foreach}
		{else}
	  	<li>{lang}There are no active Todo Lists{/lang}</li>
		{/if}
	</ul>
</div>

<div class="archived_objects">
	<a href="{assemble route=project_todo_lists_archive project_slug=$active_project->getSlug()}" data-role="button" data-theme="k">{lang}Completed Todo Lists{/lang}</a>
</div>
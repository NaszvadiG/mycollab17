{title}Todo Lists{/title}
{add_bread_crumb}Todo Lists{/add_bread_crumb}

<div id="milestone_todo_lists">
	<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
		<li data-role="list-divider"><img src="{image_url name="icons/listviews/navigate.png" module=$smarty.const.TODO_MODULE interface=AngieApplication::INTERFACE_PHONE}" class="divider_icon" alt="">{lang}Todo Lists{/lang}</li>
		{if is_foreachable($todo_lists)}
			{foreach $todo_lists as $todo_list}
				<li><a href="{replace search='--TODOLISTID--' in=$todo_list_url replacement=$todo_list.id}">{$todo_list.name}</a></li>
			{/foreach}
		{else}
	  	<li>{lang}There are no active todo lists{/lang}</li>
		{/if}
	</ul>
</div>
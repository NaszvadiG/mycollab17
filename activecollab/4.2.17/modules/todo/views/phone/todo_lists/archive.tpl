{title}Completed Todo Lists{/title}
{add_bread_crumb}Complete{/add_bread_crumb}

<div id="archived_todo_lists">
	<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
		<li data-role="list-divider"><img src="{image_url name="icons/listviews/navigate-icon.png" module=$smarty.const.SYSTEM_MODULE interface=AngieApplication::INTERFACE_PHONE}" class="divider_icon" alt="">{lang}Navigate{/lang}</li>
		{if is_foreachable($todo_lists)}
	    {foreach $todo_lists as $todo_list}
	    	<li><a href="{$todo_list->getViewUrl()}">{$todo_list->getName()}</a></li>
	    {/foreach}
		{else}
			<li>{lang}There are no completed Todo Lists{/lang}</li>
		{/if}
	</ul>
</div>
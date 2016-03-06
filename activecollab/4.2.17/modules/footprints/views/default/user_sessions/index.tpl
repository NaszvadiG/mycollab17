{form action=$sessions_remove_url method=post autofocus=yes}
	<div id="user_sessions" class="sessions_container">
		<ul class="session_list"></ul>
	</div>
{wrap_buttons}
{submit}Terminate{/submit}
{/wrap_buttons}
{/form}

{include file=get_view_path('_initialize_sessions', 'user_sessions', $smarty.const.FOOTPRINTS_MODULE)}


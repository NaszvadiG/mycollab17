<div class="resource object_history object_section" id="object_history_{$_history_object->getId()}">
	<div class="object_history_logs object_section_content common_object_section_content">
		{if is_foreachable($_history_modifications)}
			{foreach $_history_modifications as $_history_modification}
				<div class="object_history_log">
					<div class="object_history_modification_head">{$_history_modification.head nofilter}</div>
					<ul>
						{foreach $_history_modification.modifications as $_history_modification_modification}
							<li>{$_history_modification_modification nofilter}</li>
						{/foreach}
					</ul>
				</div>
			{/foreach}
		{else}
			<p class="empty_page"><span class="inner">{lang}History is empty{/lang}</span></p>
		{/if}
	</div>
</div>

<script type="text/javascript">
	var wrapper = $('#object_history_{$_history_object->getId()}');
	var refresh_history_url = '{assemble route=object_history object_id=$_history_object->getId() object_class=get_class($_history_object) async=1}';

	var modifications_wrapper = wrapper.find('div.object_history_logs');

	App.Wireframe.Events.bind('{$_history_object->getUpdatedEventName()}.{$request->getEventScope()} {$_history_object->getDeletedEventName()}.{$request->getEventScope()}', function (event, object) {
		if (object['id'] != '{$_history_object->getId()}' || object['class'] != '{$_history_object|class}') {
			return false;
		} // if

		$.ajax({
			'url'      : refresh_history_url,
			'success'  : function (response) {
				response = $.trim(response);
				modifications_wrapper.empty();
				if (response) {
					modifications_wrapper.append(response);
				} else {
					modifications_wrapper.append('<p class="empty_page"><span class="inner">' + App.lang('History is empty') + '</span></p>');
				} // if

			}
		});
	});
</script>
{use_widget name="text_compare_dialog" module="text_compare"}

<div id="history_of_changes">
	{history_of_changes object=$active_object user=$logged_user}
</div>

<script type="text/javascript">
	var wrapper = $('#history_of_changes');

	wrapper.find('a.text_diffs').click(function() {
		var versions_to_compare = {
			final_version : App.lang('selected'),
			//final_name : App.lang('Selected'),
			final_body : $(this).parent().find('pre.new').html(),
			compare_with_version : App.lang('previous'),
			//compare_with_name : App.lang('Previous'),
			compare_with_body : $(this).parent().find('pre.old').html()
		};
		App.widgets.TextCompareDialog.compareText(this, versions_to_compare);
		return false;
	});
</script>
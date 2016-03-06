{title}MS Project Manager{/title}
{add_bread_crumb}Import/Export MS Projects{/add_bread_crumb}
{use_widget name="form" module="environment"}

<div id = "ms_project_manager">
{if $is_writable}
	<div class="content_stack_wrapper">
		<div class="content_stack_element">
			<div class="content_stack_element_info">
		    <h3>{lang}Import Microsoft Project{/lang}</h3>
		    <p class="aid">{lang}Upload an XML Microsoft Office Project file as Project in activeCollab{/lang}</p>
      </div>
      <div class="content_stack_element_body">
        {form action="$upload_url" method="post" enctype="multipart/form-data" id="upload_form"}
          {wrap field=file}
            {label for=projectIcon}File{/label}
            {file_field name=xmlfile id=xmlfile}
            <p class="aid">{max_file_size_warning}</p>
          {/wrap}
          {submit}Upload Project File{/submit}
        {/form}
        <div id="import_data_wrapper"></div>
      </div>

		</div>
		
		<div class="content_stack_element">
			<div class="content_stack_element_info">
		    <h3>{lang}Export Microsoft Project{/lang}</h3>
		    <p class="aid">{lang}Download project as XML Microsoft Office Project file{/lang}</p>
      </div>
      <div class="content_stack_element_body">
        {form action="$download_url" method="post" enctype="multipart/form-data" id="download_form"}
          {wrap field=visibility}
            {label}Visibility{/label}
            {select_visibility name='visibility' value=$visibility short_description=true}
          {/wrap}
          {wrap field=checkbox}
            {label}Add labels{/label}
            {if $active_project->hasTab('tasks',$logged_user)}
              {checkbox_field name='write_task' value='write_task' label="Add 'Task:' in front of task names"} <br />
            {/if}
          {/wrap}
          {submit}Export Project{/submit}
        {/form}
      </div>
		</div>
	</div>
{/if}
</div>

<script type="text/javascript">
  $('#ms_project_manager').each(function() {
    var wrapper = $(this);

    //upload form submit
    var upload_form = wrapper.find('#upload_form');
    upload_form.find('button').click(function() {
      var button = $(this);
      var file_input = upload_form.find('input[type=file]:first');

      if (!file_input.val()) {
        App.Wireframe.Flash.error('Please choose XML file which contains MS Project');
        return false;
      } // if

      button.hide().after('<img class="ajax_indicator" alt="ajax_indicator" src="' + App.Wireframe.Utils.indicatorUrl() + '" />');


      upload_form.ajaxSubmit({
        "url" : App.extendUrl(upload_form.attr('action'), {
          'async' : 1
        }),
        "method" : "post",
        "success" : function (response) {
          upload_form.find("img.ajax_indicator").remove();
          button.show();
          if (response.xml_valid === false) {
            App.Wireframe.Flash.error(response.message);
          } else {
            wrapper.find('#import_data_wrapper').hide('fast').html(response).show('fast').find('table').checkboxes();
            init_import_data();
          } //if
        }, //success
        "error" : function (response) {
          upload_form.find("img.ajax_indicator").remove();
          button.show();
          App.Wireframe.Flash.error(App.lang('An error occurred while trying to import MS Project file'));
        } //error

      });

      return false;
    });

    var init_import_data = function() {
      var import_form = wrapper.find('#msproject_importer_form'); //import form submit

      import_form.find('button').click(function() {
        var button = $(this);
        var checkboxes = import_form.find('input[type=checkbox]');

        var none_checked = true;

        checkboxes.each(function() {
          if ($(this)[0].checked) {
            none_checked = false;
          } //if
        }); //each

        if (none_checked === true) {
          App.Wireframe.Flash.error('Please choose data for import');
          return false;
        } // if

        button.hide().after('<img class="ajax_indicator" alt="ajax_indicator" src="' + App.Wireframe.Utils.indicatorUrl() + '" />');

        import_form.ajaxSubmit({
          "url" : App.extendUrl(import_form.attr('action'), {
            'async' : 1
          }),
          "method" : "post",
          "success" : function (response) {
            import_form.find("img.ajax_indicator").remove();
            button.show();
            if (response.imported === true) {
              App.widgets.FlyoutDialog.front().close();
              App.Wireframe.Content.reload();
            } else {
              App.Wireframe.Flash.error(response.message);
            } //if
          }, //success
          "error" : function (response) {
            import_form.find("img.ajax_indicator").remove();
            button.show();
            App.Wireframe.Flash.error(App.lang('An error occurred while trying to import MS Project'));
          } //error

        });

        return false;
      });
    }; //init_import_data
  });
</script>
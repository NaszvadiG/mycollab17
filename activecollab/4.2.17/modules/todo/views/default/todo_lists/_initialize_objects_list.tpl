{use_widget name="objects_list" module="environment"}

<script type="text/javascript">
  $('#new_todo_list').flyoutForm({
    'success_event' : 'todo_list_created',
    'title' 		: App.lang('New Todo list')
  });

  $('#todo_lists').each(function() {
    var wrapper = $(this);

    var project_id = {$active_project->getId()|json nofilter};
    var categories_map = {$categories|map nofilter};
    var milestones_map = {$milestones|map nofilter};

    var print_url = {$print_url|json nofilter};

    var init_options = {
      'id' : 'project_' + {$active_project->getId()} + '_todo_lists',
      'items' : {$todo_lists|json nofilter},
      'required_fields' : ['id', 'name', 'category_id', 'milestone_id', 'is_completed', 'permalink'],
      'requirements' : {
        'project_id' : '{$active_project->getId() nofilter}'
      },
      'objects_type' : 'todo_lists',
      'events' : App.standardObjectsListEvents(),
      'multi_title' : App.lang(':num Todo Lists Selected'),
      'multi_url' : '{assemble route=project_todo_lists_mass_edit project_slug=$active_project->getSlug()}',
      'multi_actions' : {$mass_manager|json nofilter},
      'print_url' : print_url,
      'reorder_url' : '{assemble route=project_todo_lists_reorder project_slug=$active_project->getSlug()}',
      'prepare_item' : function (item) {
        var result = {
          'id'              : item['id'],
          'name'            : item['name'],
          'is_completed'    : item['is_completed'],
          'project_id'      : item['project'] && item['project']['id'] ? item['project']['id'] : item['project_id'],
          'permalink'       : item['permalink'],
          'is_favorite'     : item['is_favorite'],
          'total_subtasks'  : item['total_subtasks'],
          'open_subtasks'   : item['open_subtasks'],
          'is_trashed'      : item['state'] == '1' ? 1 : 0,
          'is_archived'     : item['state'] == '2' ? 1 : 0,
          'visibility'      : item['visibility']
        };

        if(typeof(item['category']) == 'undefined') {
          result['category_id'] = item['category_id'];
        } else {
          result['category_id'] = item['category'] ? item['category']['id'] : 0;
        } // if

        if(typeof(item['milestone']) == 'undefined') {
          result['milestone_id'] = item['milestone_id'];
        } else {
          result['milestone_id'] = item['milestone'] ? item['milestone']['id'] : 0;
        } // if

        return result;
      },

      'render_item' : function (item) {
        var row = '<td class="todo_list_name">' + App.clean(item['name']) + App.Wireframe.Utils.renderVisibilityIndicator(item['visibility']) + '</td><td class="todo_list_options">';

        // Completed task
        if(item['is_completed']) {
          row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-mono-100.png', 'complete') + '">';

          // Still open
        } else {
          var total_subtasks = typeof(item['total_subtasks']) != 'undefined' && item['total_subtasks'] ? item['total_subtasks'] : 0;
          var open_subtasks = typeof(item['open_subtasks']) != 'undefined' && item['open_subtasks'] ? item['open_subtasks'] : 0;
          var completed_subtasks = total_subtasks - open_subtasks;

          var color_class = 'mono';

          if (completed_subtasks == 0) {
              row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-' + color_class + '-0.png', 'complete') + '">';
          } else {
            if(completed_subtasks >= total_subtasks) {
                row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-' + color_class + '-100.png', 'complete') + '">';
            } else {
                var percentage = Math.ceil((completed_subtasks / total_subtasks) * 100);

                if(percentage <= 10) {
                    row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-' + color_class + '-0.png', 'complete') + '">';
                } else if(percentage <= 20) {
                    row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-' + color_class + '-10.png', 'complete') + '">';
                } else if(percentage <= 30) {
                    row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-' + color_class + '-20.png', 'complete') + '">';
                } else if(percentage <= 40) {
                    row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-' + color_class + '-30.png', 'complete') + '">';
                } else if(percentage <= 50) {
                    row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-' + color_class + '-40.png', 'complete') + '">';
                } else if(percentage <= 60) {
                    row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-' + color_class + '-50.png', 'complete') + '">';
                } else if(percentage <= 70) {
                    row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-' + color_class + '-60.png', 'complete') + '">';
                } else if(percentage <= 80) {
                    row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-' + color_class + '-70.png', 'complete') + '">';
                } else if(percentage <= 90) {
                    row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-' + color_class + '-80.png', 'complete') + '">';
                } else {
                    row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-' + color_class + '-90.png', 'complete') + '">';
                } // if
            } // if
          } // if
        } // if

        row += '</td>';

        return row;
      },
      'grouping' : [{
        'label' : App.lang("Don't group"),
        'property' : '',
        'icon' : App.Wireframe.Utils.imageUrl('objects-list/dont-group.png', 'environment')
      }, {
        'label' : App.lang('By Category'),
        'property' : 'category_id',
        'map' : categories_map,
        'icon' : App.Wireframe.Utils.imageUrl('objects-list/group-by-category.png', 'categories')
      }, {
        'label' : App.lang('By Milestone'),
        'property' : 'milestone_id',
        'map' : milestones_map,
        'icon' : App.Wireframe.Utils.imageUrl('objects-list/group-by-milestones.png', 'system'),
        'default' : true,
        'uncategorized_label' : App.lang('No Milestone')
      }],
      'filtering' : []
    };

    if ({$in_archive|json nofilter}) {
      init_options.requirements.is_archived = 1;
    } else {
      init_options.requirements.is_archived = 0;
      init_options.filtering.push({
        'label' : App.lang('Status'),
        'property' : 'is_completed',
        'values' : [{
          'label' : App.lang('All Todo Lists'),
          'value' : '',
          'icon' : App.Wireframe.Utils.imageUrl('objects-list/active-and-completed.png', 'complete'),
          'default' : true, 'breadcrumbs' : App.lang('All Todo Lists')
        }, {
          'label' : App.lang('Open Todo Lists'),
          'value' : '0',
          'icon' : App.Wireframe.Utils.imageUrl('objects-list/active.png', 'complete'),
          'default' : true, 'breadcrumbs' : App.lang('Open Todo Lists')
        }, {
          'label' : App.lang('Completed Todo Lists'),
          'value' : '1',
          'icon' : App.Wireframe.Utils.imageUrl('objects-list/completed.png', 'complete'),
          'breadcrumbs' : App.lang('Completed Todo Lists')
        }]
      });
    } // if

    wrapper.objectsList(init_options);

    // todo_list added
    App.Wireframe.Events.bind('todo_list_created.content', function (event, todo_list) {
      if (todo_list['project_id'] == project_id) {
        wrapper.objectsList('add_item', todo_list);
      } else {
        if ($.cookie('ac_redirect_to_target_project')) {
          App.Wireframe.Content.setFromUrl(todo_list['urls']['view']);
        } // if
      } // if
    });

    // todo_list updated
    App.Wireframe.Events.bind('todo_list_updated.content', function (event, todo_list) {
      if (todo_list['project_id'] == project_id) {
        wrapper.objectsList('update_item', todo_list);
      } else {
        if ($.cookie('ac_redirect_to_target_project')) {
          App.Wireframe.Content.setFromUrl(todo_list['urls']['view']);
        } else {
          wrapper.objectsList('delete_selected_item');
        } // if
      } // if
    });

    // todo_list deleted
    App.Wireframe.Events.bind('todo_list_deleted.content', function (event, todo_list) {
      if (todo_list['project_id'] == project_id) {
        if (wrapper.objectsList('is_loaded', todo_list['id'], false)) {
          wrapper.objectsList('load_empty');
        } // if
        wrapper.objectsList('delete_item', todo_list['id']);
      } // if
    });

    // manage milestones
    App.objects_list_keep_milestones_map_up_to_date(wrapper, 'milestone_id', project_id);

    // Kepp categories map up to date
    App.objects_list_keep_categories_map_up_to_date(wrapper, 'category_id', {$active_todo_list->category()->getCategoryContextString()|json nofilter}, {$active_todo_list->category()->getCategoryClass()|json nofilter});

    // Pre select item if this is permalink
  {if $active_todo_list->isLoaded()}
    wrapper.objectsList('load_item', {$active_todo_list->getId()}, '{$active_todo_list->getViewUrl()}');
  {/if}
  });
</script>
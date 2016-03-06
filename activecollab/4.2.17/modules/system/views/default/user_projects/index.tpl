<div id="user_projects">
{if $projects}
  <table class="active_projects common" cellspacing="0">
    <tr>
      <th class="icon"></th>
      <th class="name">{lang}Project{/lang}</th>
      {if $is_archive}
      <th>{lang}Completed On{/lang}</th>
      {/if}
      <th class="label">{lang}Label{/lang}</th>
      <th class="role">{lang}Project Role{/lang}</th>
      <th class="options"></th>
    </tr>
  {foreach from=$projects item=project}
    <tr {if $project['completed_on']}class="completed"{/if}>
      <td class="icon"><img src="{get_project_icon_url($project.id, "16x16")}" alt="" /></td>
      <td class="name"><a href="{$project_url|replace:"--SLUG--":$project.slug}">{$project.name|clean}</a></td>
      {if $is_archive}
        <td class="completed_on">{$project.completed_on|date}</td>
      {/if}
      <td class="label">
        {if $project.label_id && isset($project_labels[$project.label_id])}
          {render_label label=$project_labels[$project.label_id]}
        {/if}
      </td>
      <td class="role">
        {if $project.role_id && isset($project_roles[$project.role_id]) && !$active_user->isProjectManager()}
          {$project_roles[$project.role_id]}
        {else}
          {if $project.leader_id == $active_user->getId()}
            {lang}Project Leader{/lang}
          {elseif $active_user->isAdministrator()}
            {lang}System Administrator{/lang}
          {elseif $active_user->isProjectManager()}
            {lang}Project Manager{/lang}
          {else}
            {lang}Custom{/lang}
          {/if}
        {/if}
      </td>
      <td class="options">
      {if !$active_user->isProjectManager() && ($project.leader_id != $active_user->getId()) && ($project.leader_id == $logged_user->getId() || $logged_user->isPeopleManager() || $logged_user->isProjectManager())}
        {assign_var name='change_permissions_title'}{lang user=$active_user->getFirstName(true)}Change :user's Permissions{/lang}{/assign_var}
        {link href=$user_permissions_url|replace:"--SLUG--":$project.slug|replace:"--UID--":$active_user->getId() title=$change_permissions_title class=change_permissions}<img src="{image_url name="icons/12x12/permissions.png" module=$smarty.const.SYSTEM_MODULE}" alt="">{/link}
      {/if}

      {if !$active_user->isProjectManager() && ($project.leader_id != $active_user->getId()) && ($project.leader_id == $logged_user->getId() || $logged_user->isPeopleManager() || $logged_user->isProjectManager())}
        {link href=$user_remove_url|replace:"--SLUG--":$project.slug|replace:"--UID--":$active_user->getId() title='Remove from Project' class=remove_from_project}<img src="{image_url name="icons/12x12/delete.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt="">{/link}
      {/if}
      </td>
    </tr>
  {/foreach}
  </table>
{else}
  <p class="empty_page"><span class="inner">{lang}There are no projects{/lang}</span></p>
{/if}
   <p class="projects_status_toggle"><a id="projects_url_toggle" href="{$projects_toggle_url}">{$projects_toggle_text}</a></p>
</div>
<script type="text/javascript">
  $('#user_projects').each(function() {
    var wrapper = $(this);

    wrapper.find('a.change_permissions').flyoutForm({
      'success_message' : App.lang('Permissions have been updated'),
      'success_event' : 'project_permissions_updated',
      'width' : 450
    });

    wrapper.find('a.remove_from_project').flyoutForm({
      'success_event' : 'project_people_updated',
      'width' : 500
    });

    // Refresh Content on One of the Listed Events
    var inline_tabs = wrapper.parents('.inline_tabs:first');

    if (inline_tabs.length) {
      var tabs_id = inline_tabs.attr('id');

      App.Wireframe.Events.bind('project_created.inline_tab project_updated.inline_tab project_deleted.inline_tab project_people_updated.inline_tab user_added_to_project.inline_tab project_permissions_updated.inline_tab', function (event, invoice) {
        App.widgets.InlineTabs.refresh(tabs_id);
      });

      var projects_tab = inline_tabs.find('div.inline_tabs_links ul li a.selected');
      var projects_toggle_link = $('#projects_url_toggle');
      var projects_page = projects_toggle_link.attr('href');

      // toggle archive/active projects
      projects_toggle_link.click(function () {
        projects_tab.attr('href', projects_page).click();
        return false;
      });
    } // if
  });
</script>
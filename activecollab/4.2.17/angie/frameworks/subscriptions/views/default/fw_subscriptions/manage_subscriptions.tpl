{use_widget name="quick_search" module="environment"}

<div id="object_subscriptions" class="fields_wrapper">
  {if is_foreachable($grouped_subscribers)}
    {wrap field=text}
      <input type="text" id="filter_manage_subscriptions" placeholder="{lang}Filter Subscribers{/lang}"/>
    {/wrap}
    {if $active_object instanceof Discussion}
       <div id="multiselect_container">
         <span>{lang}Subscribe: {/lang}</span>
         {link href="#" id="select_all"}All{/link}
         {link href="#" id="select_none"}None{/link}
       </div>
    {/if}
    {foreach $grouped_subscribers as $group_name => $group_subscribers}
      <table class="common manage_subscriptions_table" cellspacing="0">
        <thead>
        <tr>
          <th colspan="3" class="company_name">{$group_name}</th>
        </tr>
        </thead>
        <tbody>
        {foreach $group_subscribers as $subscriber}
          <tr class="{cycle values='odd,even'} subscriber" _search_index=" {strtolower($subscriber->getDisplayName())} ">
            <td class="avatar"><img src="{$subscriber->avatar()->getUrl(IUserAvatarImplementation::SIZE_SMALL)}" alt="" /></td>
            <td class="name">{$subscriber->getDisplayName()}</td>
            <td class="subscription">
              <input type="checkbox" class="auto input_checkbox" user_id="{$subscriber->getId()}" on_url="{$active_object->subscriptions()->getSubscribeUrl($subscriber)}" off_url="{$active_object->subscriptions()->getUnsubscribeUrl($subscriber)}" {if $active_object->subscriptions()->isSubscribed($subscriber)}checked="checked"{/if} {if !$can_be_managed}disabled="disabled"{/if} />
            </td>
          </tr>
        {/foreach}
        </tbody>
      </table>
    {/foreach}
  {/if}
</div>

<script type="text/javascript">

  // add quick search
  var filter_manage_subscriptions = $("#filter_manage_subscriptions");
  var subscriptions_tables = $('table.manage_subscriptions_table');

  filter_manage_subscriptions.quickSearch({
    'target' : subscriptions_tables,
    'rows' : 'tr.subscriber'
  });

  $(document).ready (function () {
    filter_manage_subscriptions.focus();

    var multiselect_container = $('div#object_subscriptions');
    var checkboxes = $('#object_subscriptions table td.subscription input[type=checkbox]');

    multiselect_container.find('a#select_all').click(function(){
      App.widgets.FlyoutDialog.front().startProcessing();
      var user_ids = new Array();
      $.each(checkboxes, function(k, object) {
        user_ids.push($(object).attr('user_id'));
      });
      $.ajax({
        'url' : App.extendUrl('{$subscribe_all_link}', { 'async' : 1 }),
        'data' : { 'submitted' : 'submitted',
                   'user_ids'   : user_ids
        },
        'type' : 'POST',
        'success' : function(response) {
          App.Wireframe.Flash.success(App.lang('All users successfully subscribed'));
          App.Wireframe.Events.trigger({$active_object->getUpdatedEventName()|json nofilter}, [ response ]);
          checkboxes.attr('checked','checked');
          App.widgets.FlyoutDialog.front().stopProcessing();
        },
        'error' : function(response) {
          App.widgets.FlyoutDialog.front().stopProcessing();
        }
      });

    });

    multiselect_container.find('a#select_none').click(function(){
      App.widgets.FlyoutDialog.front().startProcessing();
      $.ajax({
        'url' : App.extendUrl('{$unsubscribe_all_link}', { 'async' : 1 }),
        'data' : { 'submitted' : 'submitted' },
        'type' : 'POST',
        'success' : function(response) {
          App.Wireframe.Flash.success(App.lang('All users successfully unsubscribed'));
          App.Wireframe.Events.trigger({$active_object->getUpdatedEventName()|json nofilter}, [ response ]);
          checkboxes.removeAttr('checked');
          App.widgets.FlyoutDialog.front().stopProcessing();
        },
        'error' : function(response) {
          App.widgets.FlyoutDialog.front().stopProcessing();
        }
      });

    });

  });

  if ({$can_be_managed|json nofilter}) {
    $('#object_subscriptions table td.subscription input[type=checkbox]').asyncCheckbox({
      'success_event' : {$active_object->getUpdatedEventName()|json nofilter}
    });

    // add unsubscribe all action link
    App.widgets.FlyoutDialog.front().addButton('unsubscribe_all_users', {
      'icon' : App.Wireframe.Utils.imageUrl('/icons/12x12/delete-gray.png', 'environment'),
      'text' : App.lang('Unsubscribe Everyone'),
      'url'  : '{$unsubscribe_all_link}',
      'onclick' : function() {
        App.widgets.FlyoutDialog.front().startProcessing();
        $.ajax({
          'url' : App.extendUrl('{$unsubscribe_all_link}', { 'async' : 1 }),
          'data' : { 'submitted' : 'submitted' },
          'type' : 'POST',
          'success' : function(response) {
            App.Wireframe.Flash.success(App.lang('All users successfully unsubscribed'));
            App.Wireframe.Events.trigger({$active_object->getUpdatedEventName()|json nofilter}, [ response ]);
            App.widgets.FlyoutDialog.front().close();
          } // success
        });

        return false;
      } // onclick
    });
  } // if
</script>
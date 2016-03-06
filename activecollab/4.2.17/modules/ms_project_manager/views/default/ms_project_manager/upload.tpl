<div id="msproject_upload">
	
  {form action="$import_url" method="post" id="msproject_importer_form"}
    <table class="common">
    	<tr>
    		<th class="checkbox title">{checkbox_field class="master_checkbox" label="Insert all MS Project data"}</th>
    	</tr>
    	<tr>
    		<th class="title">{checkbox_field name="project_details" checked = $details_checked class="slave_checkbox input_checkbox" label="Insert MS project details"}</th>
    	</tr>
    	<tr>
    		<td><ul>
    			<li>Project name: {$project_details.name}</li>
    			<li>Start Date: {$project_details.starts_on}</li>
    			<li>Creation Date: {$project_details.created_on}</li>
    			{if isset($project_details.overview)}
    				<li>Project overview: {$project_details.overview}</li>
    			{/if}
    		  </ul>
        </td>
    	</tr>

      <tr>
        <th class="title">Choose MS Project milestones:</th>
      </tr>
      {foreach from=$project_objects.milestones item=milestone key=i}
        <tr>
          <td>
          {if (in_array($milestone.UID,$milestones))}
            {checkbox_field name="milestones[]" checked = true class="slave_checkbox input_checkbox" value=$milestone.UID label=$milestone.Name}
          {else}
            {checkbox_field name="milestones[]" checked = false class="slave_checkbox input_checkbox" value=$milestone.UID label=$milestone.Name}
          {/if}
            <ul>
              {foreach from=$project_objects.tasks item=task}
                {if (intval($milestone.UID) == intval($task.IsSubproject))}
                  <li>Task: {$task.Name}</li>
                  <ul>
                  {foreach from=$project_objects.subtasks item=subtask}
                    {if (intval($task.UID) == intval($subtask.IsSubproject))}
                      <li>Subtask: {$subtask.Name}</li>
                    {/if}
                  {/foreach}
                  </ul>
                {/if}
              {/foreach}
    		    </ul>
          </td>
        </tr>
      {/foreach}

      <tr>
        <th class="title">Choose MS Project tasks:</th>
      </tr>
      {foreach from=$project_objects.tasks item=task key=i}
    	  {if (-1 == intval($task.IsSubproject))}
          <tr>
            <td>
             {if (in_array($task.UID,$tasks))}
               {checkbox_field name="tasks[]" checked = true class="slave_checkbox input_checkbox" value=$task.UID label=$task.Name}
             {else}
               {checkbox_field name="tasks[]" checked = false class="slave_checkbox input_checkbox" value=$task.UID label=$task.Name}
             {/if}
              <ul>
                {foreach from=$project_objects.subtasks item=subtask}
                  {if (intval($task.UID) == intval($subtask.IsSubproject))}
                    <li>Subtask: {$subtask.Name}</li>
                  {/if}
                {/foreach}
              </ul>
            </td>
          </tr>
    	  {/if}
      {/foreach}
    </table>
    <div class="import_data_submit">
      {submit}Import MS Project Data{/submit}
    </div>
  {/form}
</div>
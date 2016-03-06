{title}{$page_title}{/title}

{if is_foreachable($todo_lists)}
	
	{foreach from=$todo_lists key=map_name item=object_list name=print_object}
		<table class="todo_table common" cellspacing="0">
          <thead>
            <tr>
              <th colspan="5">{$map_name}</th>
            </tr>
          </thead>
          <tbody>
		  {foreach from=$object_list item=object}	
		  <tr>
		  	<td class="todo_id" align="left">#{$object->getId()}</td>
		    <td class="name">{$object->getName()}</td>
		   </tr>    
		  {/foreach}
		 </tbody>
	  </table>
	{/foreach}
{else}
	<p>{lang}There are no To Do Lists that match this criteria{/lang}</p>
{/if}
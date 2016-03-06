<div id="access_logs">
	{if $data}
		{foreach $data as $date => $logs}
			<table class="common">
				<thead>
				<tr>
					<th class="date" align="center" colspan="2">{past date=$date user=$logged_user}</th>
				</tr>
				</thead>
				<tbody>
				{foreach $logs as $log}
					<tr>
						<td class="subject">{$log.text nofilter}</td>
						<td class="timestamp">{$log.time nofilter}</td>
					</tr>
				{/foreach}
				</tbody>
			</table>
		{/foreach}
	{else}
		<p class="empty_page" style="display: block;">There is no activities...</p>
	{/if}
</div>
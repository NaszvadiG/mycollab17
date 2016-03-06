<div id="performed_by_this_user">
	{if $data}
	{foreach $data as $date => $logs}
		<table class="common">
			<thead>
				<tr>
					<th class="date" align="center" colspan="3">{past date=$date user=$logged_user}</th>
				</tr>
			</thead>
			<tbody>
				{foreach $logs as $log}
					<tr>
						<td class="subject">
							<ul>
								{foreach $log.text as $text}
									<li>{$text nofilter}</li>
								{/foreach}
							</ul>
						</td>
						<td class="user">
							{if $log.for}
								{lang}for{/lang} {$log.for nofilter}
							{else}
								&nbsp;
							{/if}
						</td>
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
<div class="panel panel-success">
	<div class="panel-heading text-center">
		<span class="lead">XML Відповідь</span>
	</div>
	{foreach from=$xml_answer item=xml key=action}
	<div class="panel-body">
		<pre>			
			{$xml}
		</pre>
	</div>
	{/foreach}
</div>
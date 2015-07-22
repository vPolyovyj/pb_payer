<div class="page-header">
	<h1>Крок 4:<small> внесення грошової суми</small></h1>
</div>

<p class="lead">URL: {$server_url}</p>

{if $pay_msg != ''}
	<div class="alert alert-info text-center" role="alert">
		<span class="glyphicon glyphicon-exclamation-sign lead" aria-hidden="true"></span>
		<span class="sr-only">Error:</span>
		<span class="lead">&nbsp;{$pay_msg}</span>
		{if $pay_status == 1}
		<br />
		<span class="lead"><p>Ви можете скасувати платіж натиснувши кнопку <b>Скасувати</b></span>
		{/if}
	</div>
{/if}

{if $error_msg != ''}
	<div class="alert alert-danger text-center" role="alert">
		<span class="glyphicon glyphicon-exclamation-sign lead" aria-hidden="true"></span>
		<span class="sr-only">Error:</span>
		<strong>{$error_msg}</strong>
	</div>
{/if}
<form action="{$base_url}" method="get" class="form-horizontal">
	<input type="hidden" name="page" value="{$page}" />
	<input type="hidden" name="num" value="{$pay['num']}" />
	<input type="hidden" name="service_code" value="{$pay['service_code']}" />
	<input type="hidden" name="sum" value="{$pay['sum']}" />
	<input type="hidden" name="reference" value="{$reference}" />
	<div class="btn-group" role="group">
	{if $pay_status == 1}
		<input type="submit" class="btn btn-danger btn-lg" name="cancel" value="Скасувати" />
	{/if}
		<input type="submit" class="btn btn-default btn-lg" name="back" value="Назад" />
	</div>
</form>

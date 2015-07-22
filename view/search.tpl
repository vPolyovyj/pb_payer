<div class="page-header">
	<h1>
		Крок 3:<small> пошук клієнта</small>
		{if $demo_url == $server_url && !isset($presearch_id) && !isset($debts)}
		<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#users_modal">
			<span class="glyphicon glyphicon-info-sign"></span>
		</button>
		{/if}
	</h1>
</div>

<p class="lead">URL: {$server_url}</p>

{if $error_msg != ''}
	<div class="alert alert-danger text-center" role="alert">
		<span class="glyphicon glyphicon-exclamation-sign lead" aria-hidden="true"></span>
		<span class="sr-only">Error:</span>
		<strong>{$error_msg}</strong>
	</div>
{/if}

{if !isset($debts)}
	{if !isset($presearch_id)}
	<form action="{$base_url}" method="get" class="form-horizontal">
		<input type="hidden" name="page" value="{$page}">
		<div class="form-group">
			<label for="num">Особовий рахунок</label>
			<input type="text" id="num" name="num" class="form-control" value="{$search['num']}" />
		</div>
		<div class="btn-group" role="group">
			<input type="submit" class="btn btn-success btn-lg" name="search" value="Шукати" />
			<input type="button" class="btn btn-default btn-lg" name="back" value="Назад" onclick="goback()"/>
		</div>
	</form>
	{/if}
{else}

	{include file='view/payer.tpl'}
	{include file='view/debts.tpl'}

	<form action="{$base_url}" method="get" class="form-horizontal">
		<input type="hidden" name="page" value="{$page}">
		<div class="btn-group" role="group">
			<input type="submit" class="btn btn-default btn-lg" name="back" value="Назад"/>{*onclick="goback()"*}
		</div>
	</form>
{/if}

{include file='view/usersscope.tpl'}
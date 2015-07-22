<div class="page-header">
	<h1>
		Крок 2:<small> попередній пошук клієнта</small>
	{if $demo_url == $server_url && !isset($payers)}
		<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#users_modal">
			<span class="glyphicon glyphicon-info-sign"></span>			
		</button>
	{/if}
	</h1>
</div>

<p class="lead">URL: {$server_url}</p>

{if !isset($payers)}
	{if $error_msg != ''}
		<div class="alert alert-danger text-center" role="alert">
			<span class="glyphicon glyphicon-exclamation-sign lead" aria-hidden="true"></span>
			<span class="sr-only">Error:</span>
			<strong>{$error_msg}</strong>
		</div>
	{/if}

	<form action="{$base_url}" method="get" class="form-horizontal">
		<input type="hidden" name="page" value="{$page}" />
		<input type="hidden" name="presearch_flag" value="{$presearch_flag}" />
	{if $presearch_flag == 1}
		<div class="form-group">
			<label for="street">Вулиця</label>
			<input type="text" id="street" name="street" class="form-control" value="{$presearch['street']}" />
		</div>
		<div class="form-group">
			<label for="house">Будинок</label>
			<input type="text" id="house" name="house" class="form-control" value="{$presearch['house']}" />
		</div>
		<div class="form-group">
			<label for="branch">Корпус</label>
			<input type="text" id="branch" name="branch" class="form-control" value="{$presearch['branch']}" />
		</div>
		<div class="form-group">
			<label for="flat">Квартира</label>
			<input type="text" id="flat" name="flat" class="form-control" value="{$presearch['flat']}" />
		</div>
	{elseif $presearch_flag == 2}
		<div class="form-group">
			<label for="pb">Особовий рахунок</label>
			<input type="text" id="pn" name="pn" class="form-control" value="{$presearch['pn']}" />
		</div>
	{/if}
		<div class="btn-group" role="group">
			<input type="submit" class="btn btn-success btn-lg" name="presearch" value="Шукати" />
			<input type="submit" class="btn btn-default btn-lg" name="back" value="Назад"/>
		</div>
	</form>
{else}
	<table class="table table-bordered table-condensed table-hover table-striped">
		<caption class="lead">Результати запиту</caption>
		<tr>
			<th>ПІБ</th>
			<th>Особовий рахунок</th>
			<th>Дії</th>
		</tr>
		{foreach from=$payers item=payer}
			<tr>
				<td>{$payer['name']}</td>
				<td>{$payer['num']}</td>
				<td>
					<a class="btn btn-success btn" href="{$base_url}?page=search&amp;pn={$payer['num']}" title="Переглянути">
						<span class="glyphicon glyphicon-search"></span>
					</a>
				</td>
			</tr>
		{/foreach}
		</table>
{/if}

{include file='view/usersscope.tpl'}
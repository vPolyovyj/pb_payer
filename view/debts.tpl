{if isset($debts)}
{foreach from=$debts item=debt}
<table class="table table-bordered table-condensed table-hover table-striped">
<tr>
	<th colspan="2">Компанія</th>
</tr>
<tr>
	<th>Назва</th><td>{$debt['company_name']}</td>
</tr>
<tr>
	<th>МФО</th><td>{$debt['company_mfo']}</td>
</tr>
<tr>
	<th>ОКПО</th><td>{$debt['company_okpo']}</td>
</tr>
<tr>
	<th>Рахунок компанії</th><td>{$debt['company_accnt']}</td>
</tr>
<tr>
	<th colspan="2">Рахунок</th>
</tr>
<tr>
	<th>Послуга</th><td>{$debt['service_name']}</td>
</tr>
<tr>
	<th>Тариф</th><td>{$debt['service_price']}</td>
</tr>
<tr>
	<th>Сума до оплати</th><td>{$debt['amount_to_pay']}</td>
</tr>
<tr>
	<th>Заборгованість</th><td>{$debt['debt']}</td>
</tr>
<tr>
	<th>Призначеня платежу</th><td>{$debt['destination']}</td>
</tr>
<tr>
	<th>Період</th><td>{$debt['year']}{$debt['month']}</td>
</tr>
<tr>
	<th>Нарахована сума</th><td>{$debt['charge']}</td>
</tr>
<tr>
	<th>Сума на рахунку</th><td>{$debt['balance']}</td>
</tr>
<tr>
	<th>Перерахунок</th><td>{$debt['recalc']}</td>
</tr>
<tr>
	<th>Субсидії</th><td>{$debt['subsidies']}</td>
</tr>
<tr>
	<th>Пільги</th><td>{$debt['remission']}</td>
</tr>
<tr>
	<th>Сума останньої оплати</th><td>{$debt['lastPaying']}</td>
</tr>
{if $debt['amount_to_pay'] != 0}
<tr>
	<th colspan="2">
		<form action="{$base_url}" method="get" class="form-inline">
			<div class="form-group">
				<input type="hidden" name="page" value="{$page}">
				<input type="hidden" name="num" value="{$debt['num']}">
				<input type="hidden" name="service_code" value="{$debt['service_code']}">
				<input type="hidden" name="search" value="{$get_search}">
				<label for="sum">Сума</label>
				<div class="input-group">
					<input type="text" class="form-control" id="sum" name="sum">
					<span class="input-group-btn">
						<input type="submit" class="btn btn-success" id="pay" name="pay" value="Оплатити">
					</span>
  				</div>
			</div>
		</form>
	</th>
</tr>
{/if}
</table>
{/foreach}
{/if}
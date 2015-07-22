<div class="page-header">
	<h1>Крок 1:<small> вибір способу пошуку клієнта</small></h1>
</div>

{if $error_msg != ''}
	<div class="alert alert-danger text-center" role="alert"><strong>{$error_msg}</strong></div>
{/if}

<form action="{$base_url}" method="get" name="select_from" id="select_from" class="form-horizontal">
	<div class="form-group">
		<input type="hidden" name="page" value="{$page}">
		<label for="server_url">URL сервера</label>
		<div class="input-group">
			<input type="text" id="server_url" class="form-control" name="server_url" value="{$server_url}">
			<span class="input-group-btn">
				<button class="btn btn-info" type="button" data-toggle="modal" data-target="#url_modal">
					<span class="glyphicon glyphicon-info-sign"></span>&nbsp;
				</button>
			</span>
		</div>
	</div>
	<div class="btn-group-vertical center-block" role="toolbar">
		<input type="submit" class="btn btn-success btn-lg" name="presearch_by_addr" value="Попередній пошук по адресі">
		<input type="submit" class="btn btn-success btn-lg" name="presearch_by_pn" value="Попередній пошук по ОР">
		<input type="submit" class="btn btn-success btn-lg" name="search" value="Пошук">
	</div>
</form>

<div id="url_modal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header text-center">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h3 class="modal-title" id="myModalLabel2">URL сервера</h3>
			</div>
			<div class="modal-body">
				<div class="container-fluid">
					<p class="lead">Тут Ви можете задати адресу свого сервера для перевірки роботи Вашого шлюзу.</p>
					<p class="lead">Впевніться у коректності заданої Вами адреси!</p>
				</div>

				<div class="modal-footer boxed-grey">
					<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Закрити</button>
				</div>
			</div>
		</div>
	</div>
</div>

<ul class="list-group">
	<div class="list-group">
		<li class="list-group-item">
			<h4 class="list-group-item-heading">Попередній пошук по адресі</h4>
			<p class="list-group-item-text">
			Повертає список користувачі, які відповідають параметрам пошуку.
			Максимальна кількість записів у результуючому списку - 5.<br />
			Вулиця - код із довідника.<br />
			У разі успішного виконання виконується основний пошук по конкретному
			запису
			</p>
		</li>
		<li class="list-group-item">
			<h4 class="list-group-item-heading">Попередній пошук по ОР (особовому рахунку)</h4>
			<p class="list-group-item-text">
			Повертає список користувачі, які відповідають параметрам пошуку.
			Максимальна кількість записів у результуючому списку - 5.<br />
			У разі успішного виконання виконується основний пошук по конкретному
			запису
			</p>
		</li>
		<li class="list-group-item">
			<h4 class="list-group-item-heading">Пошук</h4>
			<p class="list-group-item-text">
			Дає змогу однозначно ідентифікувати клієнта по унікальному ідентифікатору (в даному
			випадку це особовий рахунок)
			У разі успішного виконання повертає дані про боргові зобов'язання клієнта.
			У протилежному випаду генерує помилку.
			</p>
		</li>
	</div>
</ul>
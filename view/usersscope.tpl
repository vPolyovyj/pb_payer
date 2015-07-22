<div id="users_modal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header text-center">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h3 class="modal-title" id="myModalLabel2">
					Якщо Ви використовуєте сервер за промовчанням то для пошуку 
					використовуйте такий перелік клієнтів
				</h3>
			</div>
			<div class="modal-body">
				<div class="container-fluid">
					<div class="table-responsive">
					<table class="table table-bordered table-striped table-condensed table-hover">
						<tr>
							<th>ПІБ</th>
							<th>Особовий рахунок</th>
							<th>Телефон</th>
							<th>Адреса</th>
						</tr>
						{foreach from=$test_payers item=payer}
						<tr>
							<td>{$payer['name']}</td>
							<td>{$payer['num']}</td>
							<td>{$payer['phone']}</td>
							<td>{$payer['address']}</td>
						</tr>
						{/foreach}
					</table>
					</div>
				</div>

				<div class="modal-footer boxed-grey">
					<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Закрити</button>
				</div>
			</div>
		</div>
	</div>
</div>
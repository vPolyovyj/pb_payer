				</div>
			</div>
		</div>
{if $page != 'main' and {$xml_answer|count != 0} and {$xml_query|count != 0}}

		<br />

		<div class="container">
			<div class="row">
				<div class="col-md-6">{include file="view/codescope_query.tpl"}</div>
				<div class="col-md-6">{include file="view/codescope_answer.tpl"}</div>
			</div>
		</div>
{/if}
	</body>
</html>
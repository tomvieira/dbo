<? require('header.php') ?>
<? require_once('auth.php') ?>
<?
	if(!hasPermission('calendario'))
	{
		header("Location: requisicao.php");
		exit();
	}
?>
<div class="row" style="max-width: 1300px;">
	<div class="large-12 columns">
		<div id="calendario">
		<?
			$_calendar = new calendar();
			$calendar_date = (($_GET['calendar_date'])?(dboescape($_GET['calendar_date'])):(date('Y-m-a')));
			list($ano, $mes, $dia) = explode("-", $calendar_date);

			$function_name = 'getItemCalendarServico';
			$req = new requisicao_item("WHERE data_agendada >= '".$ano."-".$mes."-01 00:00:00' AND data_agendada <= '".$ano."-".$mes."-d 23:59:59' AND status <= '".STATUS_CONCLUIDO."' ORDER BY data_agendada");
			if($req->size())
			{
				do {
					$array_aux = array(
						'function_name' => $function_name,
						'data' => clone $req
					);
					$data_set[substr($req->data_agendada, 0, 10)][] = $array_aux;
				}while($req->fetch());
			}
			$function_name = 'getItemCalendarManutencao';
			$sql = "
				SELECT 
					equipamento_manutencao.*,
					tipo_equipamento.nome AS tipo_equipamento
				FROM
					equipamento_manutencao,
					tipo_equipamento,
					equipamento
				WHERE 
					equipamento_manutencao.equipamento = equipamento.id AND
					equipamento.tipo_equipamento = tipo_equipamento.id AND
					equipamento_manutencao.data_agendada >= '".$ano."-".$mes."-01' AND 
					equipamento_manutencao.data_agendada <= '".$ano."-".$mes."-31' 
				ORDER BY 
					tipo_equipamento.nome,
					equipamento_manutencao.data_agendada
			";
			$man = new equipamento_manutencao();
			$man->query($sql);
			if($man->size())
			{
				do {
					$array_aux = array(
						'function_name' => $function_name,
						'data' => clone $man
					);
					$data_set[$man->data_agendada][] = $array_aux;
				}while($man->fetch());
			}
			$params = new obj();
			$params->week_set = 'short';
			$params->data_set = $data_set;

			echo $_calendar->drawCalendar($mes,$ano,$params);
		?>
		</div>
	</div><!-- col -->
</div><!-- row -->


<script type="text/javascript" charset="utf-8">

	function reloadList() {
		//usar peixePost() com o peixelaranja JSFW
		peixeGet($('#calendar').attr('data-active-url'), function(data) {
				var result = $.parseHTML(data);
				$('.wrapper-calendar').fadeHtml($(result).find('.wrapper-calendar').html());
			}
		)
		getNotifications();
	}

	$(document).ready(function(){
		activeMainNav('calendario');
	
		$(document).on('click', '.trigger-requisicao', function(e){
			e.preventDefault();
			e.stopPropagation();
			var clicado = $(this);
			gerenciarRequisicao(clicado.attr('data-id-requisicao'), clicado.attr('data-id-servico'));
		});

		$(document).on('click', 'table.calendar td.day', function(){
			clicado = $(this);
			if(!clicado.hasClass('active')){
				clicado.addClass('active');
			}
			else {
				clicado.removeClass('active');
			}
		});

	}) //doc.ready

</script>
<? require('footer.php') ?>
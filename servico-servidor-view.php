<?
	require_once('lib/includes.php');

	$serv = new requisicao_item("WHERE id = '".addslashes($_GET['id'])."' AND token = '".addslashes($_GET['token'])."'");
	if(!$serv->size())
	{
		criticalError('O serviço não existe');
	}
	$loc = new local($serv->local);
	$req = new requisicao($serv->requisicao);

?>
<!doctype html>
<html dir="ltr" lang="pt-BR">
<head>
	<!-- <meta name="robots" content="noindex"> -->
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title>Serviço <?= $serv->requisicao ?>/<?= $serv->numero ?></title>
	<meta name="description" content="">

	<link rel="shortcut icon" href="images/favicon.ico"><!-- 16x16 -->
	<link rel="icon" href="images/icon.png" sizes="32x32"><!-- do other sizes if necessary -->
	<link rel="apple-touch-icon" href="images/apple-touch-icon-iphone.png" /><!-- 57x57 -->
	<link rel="apple-touch-icon" sizes="72x72" href="images/apple-touch-icon-ipad.png" />
	<link rel="apple-touch-icon" sizes="114x114" href="images/apple-touch-icon-iphone4.png" />

	<!-- Foundation 4.3.0 -->
	<link rel="stylesheet" href="css/foundation.css" />
	<link rel="stylesheet" href="css/common.css" />
	<script src="js/vendor/custom.modernizr.js"></script>
	<!-- <script>document.write('<script src=' +	('__proto__' in {} ? 'js/vendor/zepto' : 'js/vendor/jquery') +	'.js><\/script>')</script> -->
	<script src="js/vendor/jquery.js"></script>
	<script src="js/foundation.min.js"></script>
	<!-- <script src="js/foundation/foundation.js"></script> -->
	<!-- <script src="js/foundation/foundation.alerts.js"></script> -->
	<!-- <script src="js/foundation/foundation.clearing.js"></script> -->
	<!-- <script src="js/foundation/foundation.cookie.js"></script> -->
	<!-- <script src="js/foundation/foundation.dropdown.js"></script> -->
	<!-- <script src="js/foundation/foundation.forms.js"></script> -->
	<!-- <script src="js/foundation/foundation.joyride.js"></script> -->
	<!-- <script src="js/foundation/foundation.magellan.js"></script> -->
	<!-- <script src="js/foundation/foundation.orbit.js"></script> -->
	<!-- <script src="js/foundation/foundation.reveal.js"></script> -->
	<!-- <script src="js/foundation/foundation.section.js"></script> -->
	<!-- <script src="js/foundation/foundation.tooltips.js"></script> -->
	<!-- <script src="js/foundation/foundation.topbar.js"></script> -->
	<!-- <script src="js/foundation/foundation.interchange.js"></script> -->
	<!-- <script src="js/foundation/foundation.placeholder.js"></script> -->
	<!-- <script src="js/foundation/foundation.abide.js"></script> -->

	<!-- Peixe Laranja JSFW -->
	<script src="js/peixelaranja.js"></script>
	<script src='js/sistema.js'></script>

	<meta name="viewport" content="width=device-width" />
	<link rel="stylesheet" media="screen" href="css/style-view.css">

</head>
<body>

	<div class='row' style='margin-top: 2em;'>
		<div class='large-6 columns'>
			<h2 class="subheader">Serviço nº <?= $serv->requisicao ?>/<?= $serv->numero ?></h2>
		</div>
		<div class='large-6 columns text-right'>
			<p>
				Aberto em: <strong><?= date('d/m/Y H:i', strtotime($req->created_on)) ?></strong><br />
				Requisitante: <strong><?= $req->nome_requisitante ?> &bull; tel: <?= $req->telefone_requisitante ?></strong>
			</p>
		</div><!-- col -->
		<hr>
	</div><!-- row -->
	<div class='row'>
		<div class='large-9 columns'>
			<div class='row'>
				<div class='large-3 columns'><p><strong>Prioridade:</strong></p></div>
				<div class='large-9 columns'><p><?= $serv->getValue('prioridade', $serv->prioridade) ?></p></div><!-- col -->
			</div><!-- row -->
			<div class='row'>
				<div class='large-3 columns'><p><strong>Tipo de Serviço:</strong></p></div>
				<div class='large-9 columns'><p><?= $serv->___tipo_servico___nome ?></p></div><!-- col -->
			</div><!-- row -->
			<div class='row'>
				<div class='large-3 columns'><p><strong>Local:</strong></p></div>
				<div class='large-9 columns'><p><?= $loc->getSmartLocal() ?></p></div><!-- col -->
			</div><!-- row -->
			<div class='row'>
				<div class='large-3 columns'><p><strong>Descrição:</strong></p></div>
				<div class='large-9 columns'><p><?= $serv->descricao ?></p></div><!-- col -->
			</div><!-- row -->
		</div><!-- col -->
		<div class='large-3 columns'>
			<?
				if($serv->status == STATUS_ATRIBUIDO)
				{
					?>
					<input type='button' id='button-iniciar-servico' name='' value="Iniciar Serviço" class="button secondary radius large expand trigger-iniciar-servico" data-id='<?= $serv->id ?>'/>
					<?
				}
				if($serv->status == STATUS_EM_ANDAMENTO)
				{
					if($serv->finalizado_servidor == 1)
					{
						?>
						<span class="button radius large expand secondary">
							Serviço<br />finalizado
						</span>
						<p class="text-center">Aguardando a conclusão pelo supervisor.</p>
						<?
					}
					else
					{
						?>
						<input type='button' id='button-finalizar-servico' name='' value="Finalizar Serviço" class="button radius large expand trigger-finalizar-servico" data-id='<?= $serv->id ?>'/>
						<?
					}
				}
			?>
			<input type='button' id="button-fechar-janela" name='' value="Fechar Janela" class="button secondary radius large expand trigger-close-modal hidden"/>
		</div><!-- col -->
	</div><!-- row -->
	
	<script>

		function triggerServico(id, acao, horario){
			peixeGet(
				'ajax-trigger-servicos.php',
				{
					id: id,
					acao: acao,
					horario: horario
				},
				function(data) {
					var result = $.parseJSON(data);
					if(result.message){
						setPeixeMessage(result.message);
						showPeixeMessage();
					}
					if(result.success){
						parent.reloadList();
						$('#button-iniciar-servico:visible, #button-finalizar-servico:visible').fadeOut(function(){
							$('#button-fechar-janela').fadeIn();
						})
					}
				}
			)
			return false;
		}

		$(document).foundation();

		$(document).ready(function(){

			$(document).on('click', '.trigger-iniciar-servico', function(){
				var ans = confirm("Tem certeza que deseja iniciar este serviço?");
				if (ans==true) {
					clicado = $(this);
					/*var ans = prompt("Digite o horário aproximado da chegada do servidor ao local do serviço: (hh:mm)");
					if (ans!=null)
					{
						var horario = ans;	
					}
					else 
					{
						var horario = '';
					}*/
					var horario = '';
					triggerServico(clicado.attr('data-id'), 'iniciar', horario);
				} else {
					return false;
				}
			});

			$(document).on('click', '.trigger-finalizar-servico', function(){
				var ans = confirm("Tem certeza que deseja finalizar este serviço?");
				if (ans==true) {
					clicado = $(this);
					triggerServico(clicado.attr('data-id'), 'finalizar');
				} else {
					return false;
				}
			});

			$(document).on('click', '#button-fechar-janela', function(){
				parent.$.fn.colorbox.close();
			});

		}) //doc.ready

	</script>
</body>
</html>
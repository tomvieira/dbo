<? require('header.php') ?>
<? require_once('auth.php') ?>
<?

	if(!hasPermission('painel-servidores'))
	{
		header("Location: requisicao.php");
		exit();
	}

	if($_GET['tipo'])
	{
		$tipo_filtro = $_GET['tipo'];
	}
	else
	{
		$opt = new opcao();
		$tipo = $opt->get('painel-servidores-filtro', loggedUser());
		if($tipo)
		{
			$tipo_filtro = $tipo;
		}
		else
		{
			$tipo_filtro = 'servidores';
		}
	}

	$servidores_nao_atribuidos = getIdsServidoresNaoAtribuidos();
	$servidores_atribuidos = getIdsServidoresAtribuidos();
	$prestadores_nao_atribuidos = getIdsPrestadoresNaoAtribuidos();
	$prestadores_atribuidos = getIdsPrestadoresAtribuidos();

	/* ids presentes na SQL */
	if($tipo_filtro == 'servidores')
	{
		$ids_atribuidos = $servidores_atribuidos;
	} 
	elseif($tipo_filtro == 'prestadores-servico')
	{
		$ids_atribuidos = $prestadores_atribuidos;
	}
	elseif($tipo_filtro == 'todos')
	{
		$ids_atribuidos = getIdsAtribuidos();
	}
	elseif($tipo_filtro == 'meus-servicos')
	{
		$ids_atribuidos = (array)$_pes->id;
	}
?>

<div id="painel-servidores">
<div class='row full' style='max-width: 1300px;'>
	<div class='large-<?= ((sizeof($servidores_nao_atribuidos) && hasPermission('gerenciar-servicos'))?('10'):('12')) ?> columns'>
		<div class='row'>
			<div class='large-6 columns'><h3>Atribuídos</h3></div><!-- col -->
			<div class='large-6 columns text-right' style='position: relative;'>
				<dl class="sub-nav filter-tipo-servidor" style="<?= ((!hasPermission('gerenciar-servicos'))?('display: none;'):('')) ?>">
					<dd class="<?= (($tipo_filtro == 'meus-servicos')?('active'):('')) ?>"><a href="?tipo=meus-servicos">Meus Serviços</a></dd>
					<dd class="<?= (($tipo_filtro == 'servidores')?('active'):('')) ?>"><a href="?tipo=servidores">Servidores</a></dd>
					<dd class="<?= (($tipo_filtro == 'prestadores-servico')?('active'):('')) ?>"><a href="?tipo=prestadores-servico">Prestadores de Serviço</a></dd>
					<dd class="<?= (($tipo_filtro == 'todos')?('active'):('')) ?>"><a href="?tipo=todos">Todos</a></dd>
				</dl>
			</div><!-- col -->
		</div><!-- row -->
		<div id="lista-atribuidos">
			<form method="post" class="no-margin" id="form-impressao">
				<?
					if(sizeof($ids_atribuidos))
					{	
						$tabela = $_pes->__module_scheme->tabela;
						$sql = "
							SELECT 
								".$tabela.".*,
								CASE
									WHEN ".$tabela.".id = ".$_pes->id." THEN 1
									ELSE 2
								END AS ordem
							FROM
								".$tabela."
							WHERE 
								".$tabela.".id IN (".((hasPermission('gerenciar-servicos'))?(implode(',', $ids_atribuidos)):(loggedUser())).") 
							ORDER BY ordem, ".$tabela.".nome
						";
						$servidor = new servidor();
						$servidor->query($sql);
						if($servidor->size())
						{
							$pes_aux = new pessoa();
							do {
								?>
								<div class='row' id='pessoa-<?= $servidor->id ?>'>
									<div class='large-12 columns'>
										<div class="wrapper-servidor panel radius">
											<div class='row'>
												<div class='large-12 columns offset-badge'>
													<?
														$sql = "
															SELECT
																requisicao_item.*,
																requisicao.nome_requisitante AS nome_requisitante,
																CASE
																	WHEN requisicao_item.status = '".STATUS_AGUARDANDO_REQUISITANTE."' THEN 3
																	WHEN requisicao_item.status = '".STATUS_AGUARDANDO_APROVACAO_DIRETORIA."' THEN 2
																	ELSE 1
																END AS ordenacao
															FROM
																requisicao_item,
																requisicao
															WHERE requisicao_item.id IN (
																SELECT 
																	DISTINCT ris.item
																FROM 
																	requisicao_item_servidor ris,
																	requisicao_item ri
																WHERE 
																	ris.servidor = '".$servidor->id."' AND
																	ris.item = ri.id AND
																	ri.inativo = 0 AND
																	ri.status IN (".STATUS_ATRIBUIDO.",".STATUS_EM_ANDAMENTO.", ".STATUS_AGUARDANDO_REQUISITANTE.", ".STATUS_AGUARDANDO_APROVACAO_DIRETORIA.")
															)
															AND
																requisicao_item.requisicao = requisicao.id
															ORDER BY finalizado_servidor, ordenacao, prioridade, created_on;
														";
														$serv = new requisicao_item();
														$serv->query($sql);
														if($serv->size())
														{
															$requisitante = new pessoa($servico->id_requisitante);
															?>
															<h3 class="show-for-small"><?= $servidor->getShortName(); ?></h3>
															<div class="badge hide-for-small"><?= $servidor->getBadge(); ?></div>
															<div class="righter">
																<table class="transparent servicos-servidores responsive">
																	<thead>
																		<tr>
																			<th style='width: 1%;' class="hide-for-small"><input title='Marcar / Desmarcar todos' class="no-margin trigger-mark-all" type='checkbox'/></th>
																			<th class="text-right" style='width: 10px'>Nº</th>
																			<th class="text-center" style='width: 10px'><span title="Prioridade">P</span></th>
																			<th class="text-center" style='width: 10px'><span title="Status">S</span></th>
																			<th class="text-center" style='width: 10px'><span title="Agendamento">A</span></th>
																			<th>Serviço</th>
																			<th>Informações</th>
																			<th></th>
																		</tr>
																	</thead>
																	<tbody>
																	<?
																		do {
																			$loc = new local($serv->local);
																			?>
																			<tr data-servidor='<?= $servidor->id ?>' data-requisicao="<?= $serv->requisicao ?>" data-requisicao-item="<?= $serv->id ?>" data-token='<?= $serv->token ?>' class="trigger-detalhes-servico linha-servico-<?= $serv->id ?> <?= (($serv->finalizado_servidor == 1)?('linha-finalizado'):((($serv->status == STATUS_EM_ANDAMENTO)?('linha-em-andamento'):('')))) ?>">
																				<td class="stop-propagation hide-for-small">
																					<?
																						if($serv->finalizado_servidor != 1)
																						{
																							?>
																							<input class="no-margin stop-propagation" type='checkbox' name='imprimir[]' id='' value="<?= $serv->id ?>"/>
																							<?
																						}
																					?>
																				</td>
																				<td class="text-right" data-title='Número'><?= $serv->requisicao ?>/<?= $serv->numero ?></td>
																				<td data-title='Prioridade'><span class="tag-prioridade help radius prioridade-<?= $serv->prioridade ?>" title=<?= $serv->getValue('prioridade', $serv->prioridade) ?>><?= substr($serv->getValue('prioridade', $serv->prioridade), 0, 1) ?></span></td>
																				<td data-title='Status'><span class="tag-status help radius status-<?= (($serv->finalizado_servidor == 0)?($serv->status):('finalizado')) ?>" title="<?= (($serv->finalizado_servidor == 0)?($serv->getValue('status', $serv->status)):('Finalizado')) ?>"><?= (($serv->finalizado_servidor == 0)?(substr($serv->getValue('status', $serv->status), 0, 1)):('F')) ?></span></td>
																				<td data-title='Agendamento'>
																					<?
																						$title_agendamento = '';
																						$status_agendamento = getStatusAgendamentoServico($serv);
																						if($status_agendamento == 'nao-agendado')
																						{
																							$title_agendamento = "Não agendado";
																						}
																						elseif($status_agendamento == 'avisado')
																						{
																							$title_agendamento = "Agendado para ";
																							$dias_agendamento = diasPassados(date("Y-m-d"), $serv->data_agendada);
																							switch($dias_agendamento)
																							{
																								case 0: 
																									$title_agendamento .= "hoje";
																									$status_agendamento = 'atrasado'; //para mostrar as atividades do dia em vermelho
																									break;
																								case 1: 
																									$title_agendamento .= "amanhã";
																									break;
																								default:
																									$title_agendamento .= "daqui ".$dias_agendamento." dias";
																							}
																						}
																						elseif($status_agendamento == 'atrasado')
																						{
																							$dias_agendamento = abs(diasPassados(date("Y-m-d"), $serv->data_agendada));
																							switch($dias_agendamento)
																							{
																								case 0: 
																									$title_agendamento = "Agendado para hoje";
																									break;
																								case 1: 
																									$title_agendamento = "Atrasado 1 dia";
																									break;
																								default:
																									$title_agendamento = "Atrasado ".$dias_agendamento." dias";
																							}
																						}
																						elseif($status_agendamento == 'programado')
																						{
																							$dias_agendamento = abs(diasPassados(date("Y-m-d"), $serv->data_agendada));
																							$title_agendamento = "Agendado para daqui ".$dias_agendamento." dias";
																						}
																					?>
																				<span class="tag-status help radius status-<?= $status_agendamento ?>" title="<?= $title_agendamento ?>"><i class="fa-clock-o"></i></span></td>
																				<td data-title='Serviço'><div style="min-width: 110px;"><?= $serv->___tipo_servico___nome ?></div></td>
																				<td data-title='Info' style="width: 100%;">
																					<span class="show-for-small">
																					<?
																						$pes_aux->id = $serv->nome_requisitante;
																						$pes_aux->nome = $serv->nome_requisitante;
																						$string = $pes_aux->getShortName()." - ".$loc->getSmartLocal();
																						echo $string;
																					?>
																					</span>
																					<div class="hide-for-small">
																						<div style="width: 100%;">
																							<div style="font-size: 11px; margin-top: -3px;" class="linha-nome">
																								<?
																									echo maxString($string, 100);
																								?>
																							</div>
																							<div style="font-size: 13px; margin-bottom: -3px;" class="linha-servico">
																								<?= maxString($serv->descricao, 70) ?>
																							</div>
																						</div>
																					</div>
																				</td>
																				<td class="text-right">
																					<?
																						if($serv->status == STATUS_ATRIBUIDO)
																						{
																							?>
																							<input type="button" data-numero='<?= $serv->requisicao ?>/<?= $serv->numero ?>' data-servico="<?= $serv->id ?>" class="button secondary radius tiny no-margin trigger-iniciar-servico" data-servico="<?= $serv->id ?>" value='Iniciar'>
																							<?
																						}
																						elseif($serv->status == STATUS_EM_ANDAMENTO && $serv->finalizado_servidor == 0)
																						{
																							?>
																							<input type="button" class="button radius tiny no-margin trigger-finalizar-servico" data-numero='<?= $serv->requisicao ?>/<?= $serv->numero ?>' data-servico="<?= $serv->id ?>" value='Finalizar'>
																							<?
																						}
																						elseif($serv->status == STATUS_AGUARDANDO_APROVACAO_DIRETORIA)
																						{
																							?>
																							<input type="button" class="button radius tiny no-margin disabled warning" data-numero='<?= $serv->requisicao ?>/<?= $serv->numero ?>' data-servico="<?= $serv->id ?>" value='Aguardando' title="Este serviço está aguardando Para maiores informações, verifique o histórico.">
																							<?
																						}
																						elseif($serv->status == STATUS_AGUARDANDO_REQUISITANTE)
																						{
																							?>
																							<input type="button" class="button radius tiny no-margin disabled secondary" data-numero='<?= $serv->requisicao ?>/<?= $serv->numero ?>' data-servico="<?= $serv->id ?>" value='Ag. Requis.' title="Este serviço está aguardando contato do requisitante.">
																							<?
																						}
																						else
																						{
																							?>
																							<input type='button' class="button disabled radius tiny no-margin" value="Finalizado"/>
																							<?
																						}
																					?>
																				</td>
																			</tr>												
																			<?
																		}while($serv->fetch());
																	?>
																	</tbody>
																</table>
																<div class="hide-for-small">
																	<input type='button' name='' class="button radius small no-margin trigger-impressao" value="Imprimir serviços marcados"/>
																</div>
															</div>
															<?
														}
														else
														{
															?><h2 class="text-center no-margin">Você não possui serviços atribuidos</h2><?
														}
													?>
												</div><!-- col -->
											</div>
										</div><!-- row --><!-- wrapper-servidor -->
									</div>
								</div><!-- row -->
								<?
							}while($servidor->fetch());
						}
					}
					else
					{
						?>
						<div class="panel radius">
							<h2 class="text-center no-margin">Não há serviços atribuidos a ninguém</h2>
						</div>
						<?
					}
				?>
			</form>
		</div>
	</div>
	<?
		if(sizeof($servidores_nao_atribuidos) && hasPermission('gerenciar-servicos') && ($tipo_filtro == 'servidores' || $tipo_filtro == 'todos'))
		{
			?>
			<div class='large-2 columns hide-for-small'>
				<h3>Inativos</h3>
				<?
					if(sizeof($servidores_nao_atribuidos))
					{
						foreach($servidores_nao_atribuidos as $key => $value)
						{
							?>
							<div class="panel radius wrapper-servidor nao-atribuidos">
							<?
								$servidor = new servidor($value);
								$servidor->getBadge();
							?>
							</div>
							<?
						}
					}
				?>
			</div><!-- col -->
			<?
		}
	?>
</div><!-- row -->

</div><!-- painel-servidores -->


<script>

	/* recarrega a lista de servicos de todo mundo */
	function reloadList() {
		peixeGet(
			document.URL,
			{},
			function(data) {
				var result = $.parseHTML(data);
				$('#painel-servidores').replaceWith($(result).find('#painel-servidores'));
				getNotifications();
			}
		)
		return false;
	}

	function triggerServico(id, acao, horario){
		peixeGet(
			'ajax-trigger-servicos.php',
		    {
				id: id,
				acao: acao,
				horario: horario
			},
			function(data) {
				console.log(data);
				var result = $.parseJSON(data);
				if(result.message){
					setPeixeMessage(result.message);
					showPeixeMessage();
				}
				if(result.success){
					reloadList();
				}
			}
		)
		return false;
	}

	function visualizarServico(id, token) {
		$.colorbox({
			href:"servico-servidor-view.php?&id="+id+"&token="+token,
			iframe: true,
			width: 980,
			height: '98%',
			overlayClose: false,
			escKey: false,
			fixed: true
		});
	}

	function visualizarImpressao(ids) {
		$.colorbox({
			href:"servico-imprimir.php?"+ids,
			iframe: true,
			width: 980,
			height: '98%',
			overlayClose: false,
			fixed: true
		});
	}

	$(document).ready(function(){
		activeMainNav('painel-servidores');

		$(document).on('click', '.trigger-iniciar-servico, .trigger-finalizar-servico', function(e){
			e.preventDefault();
			e.stopPropagation();
			clicado = $(this);
			if(clicado.hasClass('trigger-iniciar-servico')){ /* iniciando */
				var ans = confirm("Tem certeza que deseja iniciar o serviço '"+clicado.attr('data-numero')+"'?");
				if (ans==true) {
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
					triggerServico(clicado.attr('data-servico'), 'iniciar', horario);
				} else {
					return false;					
				}
			}
			else if(clicado.hasClass('trigger-finalizar-servico')){
				var ans = confirm("Tem certeza que deseja finalizar o serviço '"+clicado.attr('data-numero')+"'?");
				if (ans==true) {
					triggerServico(clicado.attr('data-servico'), 'finalizar', '');
				} else {
					return false;					
				}
			}
		});

		$(document).on('click', '.stop-propagation', function(e){
			e.stopPropagation();
		});

		$(document).on('click', '.trigger-detalhes-servico', function(){
			<?
				if(hasPermission('gerenciar-servicos')){
					?>
					gerenciarRequisicao($(this).attr('data-requisicao'), $(this).attr('data-requisicao-item'));
					<?
				}
				else
				{
					?>
					visualizarServico($(this).attr('data-requisicao-item'), $(this).attr('data-token'));
					<?
				}
			?>
		});

		$(document).on('click', '.trigger-mark-all', function(){
			var clicado = $(this);
			if(clicado.is(':checked')){
				clicado.closest('table').find('td input[type="checkbox"]').each(function(){
					$(this).prop('checked', true);
				});
			}
			else {
				clicado.closest('table').find('td input[type="checkbox"]').each(function(){
					$(this).prop('checked', false);
				});
			}
		});

		$(document).on('click', '.trigger-impressao', function(){
			clicado = $(this);
			var ids = clicado.closest('form').serialize();
			if(ids.length == 0){
				alert('Não há serviços selecionados.');
			}
			else {
				visualizarImpressao(ids);
			}
		});

		$(document).on('click', '.filter-tipo-servidor a', function(e){
			e.preventDefault();
			clicado = $(this);
	
			//extraindo a variavel certa
			partes = clicado.attr('href').split('=');
			console.log(partes[1]);

			peixePost(
				'ajax-common-actions.php',
				{
					action: 'set-logged-option',
					option: 'painel-servidores-filtro',
					value: partes[1]
				},
				function(data) {
					var result = $.parseJSON(data);
					if(result.message){
						setPeixeMessage(result.message);
						showPeixeMessage();
					}
				}
			)

			//recarregando a pagina
			peixeGet(
				$(this).attr('href'),
				function(data) {
					var result = $.parseHTML(data);
					$('#painel-servidores').fadeHtml($(result).find('#painel-servidores'));
				}
			)
			return false;
		});

	}) //doc.ready
</script>
<? require('footer.php') ?>
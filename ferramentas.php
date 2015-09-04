<?
	require_once('lib/includes.php');

	if(!hasPermission('painel-ferramentas'))
	{
		setMessage('<div class="error">Erro: Você não tem permissão de acesso a esta tela.</div>');
		header("Location: index.php");
		exit();
	}

	require_once('header.php');
?>
<div class="row almost full">
	<div class="large-12 columns">
		<h3>Ferramentas do sistema</h3>

		<div class="row">
			<div class="large-3 columns">
				<ul class="side-nav" id="nav-ferramentas">
					<li><a href="<?= SITE_URL ?>/ferramentas.php?section=servicos-avaliacao-pendente">Serviços com avaliação pendente</a></li>
				</ul>
			</div>
			<div class="large-9 columns">
				<div id="active-section">
					<?
						if(!$_GET['section'])
						{
							?>
							<div class="helper arrow-left top-5">
								<p class="no-margin">Selecione uma opção ao lado.</p>
							</div>
							<?
						}
						elseif($_GET['section'] == 'servicos-avaliacao-pendente')
						{
							$sql = "
								SELECT
									avaliacao.*,
									requisicao.nome_requisitante,
									requisicao.telefone_requisitante,
									requisicao.email_requisitante,
									requisicao.created_on,
									CONCAT(requisicao.id, '/', requisicao_item.numero) AS numero_requisicao,
									requisicao.id AS requisicao_id,
									requisicao_item.id AS requisicao_item_id,
									requisicao_item.token AS requisicao_item_token
								FROM requisicao_item
								INNER JOIN avaliacao ON
									avaliacao.requisicao_item = requisicao_item.id
								INNER JOIN requisicao ON
									requisicao.id = requisicao_item.requisicao
								WHERE 
									requisicao_item.status = '".STATUS_CONCLUIDO."' AND
									requisicao_item.arquivo_morto IS NULL
								ORDER BY
									requisicao.created_on ASC
							";
							$aval = new avaliacao();
							$aval->query($sql);
							if($aval->size())
							{
								?>
								<div class="row">
									<div class="large-8 columns large-centered">
										<div class="helper arrow-bottom margin-bottom hide-for-small" style="margin-top: -43px;">
											<p class="no-margin">A tabela abaixo mostra todas as requisições já concluídas que possuem avaliação pendente do usuário. Em muitos casos os usuários já estão desvinculados da unidade. Você pode enviar o link de avaliação das requisições para outros usuários utilizando o ícone <i class="fa-chain color warning"></i> ao final de cada linha. Existem <strong class="color warning"><?= $aval->size() ?></strong> avaliações pendentes.</p>
										</div>
									</div>
								</div>
								<table class="list responsive tablesorter" id="tabela-avaliacoes-pendentes">
									<thead>
										<tr>
											<th style="width: 20%;">Nro</th>
											<th style="width: 20%;" class="sorter-false">Data</th>
											<th style="width: 59%">Requisitante</th>
											<th style="width: 1%;" class="filter-false sorter-false text-right"><span class="button radius secondary no-margin tiny single trigger-tablesorter-filters" data-tabela="#tabela-avaliacoes-pendentes"><i class="fa-search"></i></span></th>
										</tr>
									</thead>
									<tbody>
										<?
											do {
												?>
												<tr class="pointer trigger-gerenciar-requisicao-for-medium-up" data-requisicao_id="<?= $aval->requisicao_id ?>" data-requisicao_item_id="<?= $aval->requisicao_item_id ?>">
													<td data-title="Número"><?= $aval->numero_requisicao ?></td>
													<td data-title="Data"><?= dboDate('d/m/Y', strtotime($aval->created_on)) ?></td>
													<td data-title="Requisitante" data-text="<?= dboescape($aval->nome_requisitante) ?>">
														<?= $aval->nome_requisitante ?>
														<span class="hover-info">
															<i class="fa-phone color light help fa-fw" title="<?= $aval->telefone_requisitante ?>" onclick="event.stopPropagation()"></i><i class="fa-envelope color light help fa-fw" title="<?= $aval->email_requisitante ?> - clique para copiar" onclick="prompt('Pressione CTRL + C para copiar o endereço de email', '<?= $aval->email_requisitante ?>'); event.stopPropagation();"></i>
														</span>
													</td>
													<td class="nowrap control-icons">
														<span title="Clique para reenviar a avaliação ao usuário" id="button-reenviar-<?= $aval->id ?>"><a class="button tiny no-margin radius peixe-json stop-propagation" style="color: #fff;" href="ajax-ferramentas.php?action=reenviar-avaliacao&avaliacao_id=<?= $aval->id ?>" data-confirm="Tem certeza que deseja reenviar esta avaliação ao usuário?" peixe-log><i class="fa-envelope single fa-fw" style="margin: 0; font-size: 14px;"></i></span></a>
														<span class="button tiny no-margin radius" onClick="prompt('Pressione CTRL + C para copiar o link da avaliação. Você pode enviar este link por e-mail para outro usuário realizar a avaliação.', '<?= $aval->getPermalink(array('requisicao_item_token' => $aval->requisicao_item_token)); ?>'); event.stopPropagation();"><i class="fa-chain single fa-fw" style="font-size: 14px;"></i></span>
														<span title="Clique para arquivar esta requisição como arquivo morto. Ela não será mais mostrada nesta lista, mas continuará disponível na tela de todas as requisições." id="button-arquivar-<?= $aval->id ?>"><a class="button secondary tiny no-margin radius stop-propagation trigger-arquivar" href="ajax-ferramentas.php?action=arquivar-requisicao&requisicao_item_id=<?= $aval->requisicao_item_id ?>"><i class="fa-folder-open single fa-fw" style="margin: 0; font-size: 14px;"></i></span></a>
													</td>
												</tr>
												<?
											}while($aval->fetch());
										?>
									</tbody>
								</table>
								<?
							}
						}
					?>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="js/tablesorter/js/jquery.tablesorter.combined.min.js"></script>

<script>
	URL = document.URL;

	$(document).on('click', '#nav-ferramentas a', function(e){
		e.preventDefault();
		clicado = $(this);
		URL = clicado.attr('href');
		$('#nav-ferramentas li').removeClass('active');
		clicado.closest('li').addClass('active');
		peixeGet(URL, function(d) {
			var html = $.parseHTML(d);
			/* item 1 */
			handler = '#active-section';
			content = $(html).find(handler).html();
			if(typeof content != 'undefined'){
				$(handler).fadeHtml(content, function(){ tableSortInit() });
			}
		})
		return false;
	});

	$(document).on('click', '.trigger-arquivar', function(e){
		e.stopPropagation();
		e.preventDefault();
		clicado = $(this);
		var ans = prompt("Digite a razão para arquivar esta requisição:");
		if (ans!=null)
		{
			peixeJSON(clicado.attr('href'), { arquivo_morto: ans }, '', true);
			return false;
		}
		else {
			setPeixeMessage('<div class="error">Erro: Todos os arquivamentos precisam ser justificados.</div>');
			showPeixeMessage();
		}
	});

	function tableSortInit() {
		$('table.tablesorter').tablesorter({
			widgets: ["stickyHeaders", "filter"],
			showProcessing: true,
			widgetOptions: {
				filter_placeholder: {
					search : '...',
					select : '...',
					from   : '', // datepicker range "from" placeholder
					to     : ''  // datepicker range "to" placeholder
				},
				filter_excludeFilter: {
					'.no-filter': 'exact'
				},
				filter_hideFilters: true
			}
		});
	}

	tableSortInit();

	activeMainNav('sistema');

</script>

<? require_once("footer.php"); ?>
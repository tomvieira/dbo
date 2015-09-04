<? require_once('relatorios-header.php'); ?>

<style>
	tr.active td,
	a.active { background: yellow; }

	@media screen {
		.print-only { display: none; }
	}

	@media print {
		a.trigger { text-decoration: none; }
	}

</style>

<?
	if(!$_GET['pessoa'])
	{
		?>
		<h4 class="text-center">Erro: nenhum servidor ou prestador de serviço foi selecionado.</h4>
		<?
		exit();
	}

	/* 
	Informações a resgatar: 
	ok - O que a pessoa é (servidor/prestador)
	ok - Nome do Servidor
	ok - Total de requisições atendidas (concluidas) no período
	ok - Total de requisições atribuidas ainda não finalizadas
	ok - Média das notas que o servidor recebeu
	ok - Textos dos Feedbacks recebidos pelo servidor no período
	ok - Lista de todas as requisições concluidas
	*/

	//descobrindo o que a pessoa é...

	$ids_servidores = getIdsServidores();
	$ids_prestadores = getIdsPrestadores();

	//pegar todos os apelidos dos servidores e empresas
	$sql = "
		SELECT
			".$_pes->getTable().".*
		FROM
			".$_pes->getTable().",
			requisicao_item_servidor
		WHERE
			requisicao_item_servidor.servidor = ".$_pes->getTable().".id
	";
	$obj = new pessoa();
	$obj->query($sql);
	if($obj->size())
	{
		do {
			$apelidos[$obj->id] = $obj->getShortName();
		}while($obj->fetch());
	}

	if(in_array($_GET['pessoa'], $ids_servidores))
	{
		$definicao = "servidor";
		$definicao_plural = "servidores";
	}
	elseif(in_array($_GET['pessoa'], $ids_prestadores))
	{
		$definicao = "prestador de serviço";
		$definicao_plural = "prestadores de serviço";
	}
	else
	{
		?><h4 class="text-center">Erro: A pessoa selecionada não é servidor, nem prestador de serviço.</h4><?
		exit();
	}

	//pegando todos os dados da pessoa
	$pes = new pessoa(dboescape($_GET['pessoa']));

	//pegando todas os servicos concluidos que esta pessoa participou
	$sql = "
		SELECT
			requisicao.data AS data_criacao,
			requisicao.id AS numero_requisicao,
			requisicao_item.numero AS numero_servico,
			requisicao_item.*,
			tipo_servico.nome AS nome_tipo_servico,
			categoria_servico.nome AS nome_categoria_servico
		FROM
			requisicao,
			requisicao_item,
			requisicao_item_servidor,
			tipo_servico,
			categoria_servico
		WHERE 
			(
				requisicao_item.status = '".STATUS_CONCLUIDO."' OR
				requisicao_item.status = '".STATUS_AVALIADO."'
			) AND
			requisicao_item.tipo_servico = tipo_servico.id AND
			tipo_servico.categoria_servico = categoria_servico.id AND
			requisicao_item.requisicao = requisicao.id AND
			requisicao_item_servidor.item = requisicao_item.id AND
			requisicao_item_servidor.servidor = '".$pes->id."' AND
			requisicao.data >= '".$data_i."' AND
			requisicao.data <= '".$data_f."'
		ORDER BY
			numero_requisicao,
			numero_servico
	";
	$serv = new requisicao_item();
	$serv->query($sql);
	if($serv->size())
	{

		$avaliacoes[qualidade] = array();
		$avaliacoes[tempo] = array();
		$avaliacoes[feedback] = array();
		$avaliacoes[comentario] = array();
		$avaliacoes[tipo_servico] = array();

		//salvando o total de servicos concluidos
		$total_servicos_concluidos = $serv->size();

		do {
			$aval = new avaliacao("WHERE requisicao_item = '".$serv->id."' AND respondido_em IS NOT NULL");
			if($aval->size())
			{
				$avaliacoes[qualidade][] = $aval->qualidade;
				$avaliacoes[tempo][] = $aval->tempo;
				$avaliacoes[feedback][] = $aval->feedback;
				$avaliacoes[comentario][] = array(
					'comentario' => $aval->comentario,
					'numero_servico' => $serv->numero_requisicao."/".$serv->numero_servico,
					'id_servico' => $serv->id,
					'tipo_servico' => $serv->nome_tipo_servico,
				);
				if(
					$aval->qualidade < 3 ||
					$aval->tempo < 3 ||
					$aval->feedback < 3
				)
				{
					$alertadas[] = $serv->id;
				}
			}

			//salvando array com as informações dos servicos
			$categorias[$serv->nome_categoria_servico]++;
			$tipos_de_servico[$serv->nome_tipo_servico]++;

			$servicos[] = array(
				'id_servico' => $serv->id,
				'numero_requisicao'	=> $serv->numero_requisicao,
				'numero_servico' => $serv->numero_servico,
				'nome_tipo_servico' => $serv->nome_tipo_servico,
				'descricao' => $serv->descricao,
				'token' => $serv->token,
				'qualidade' => $aval->qualidade,
				'tempo' => $aval->tempo,
				'feedback' => $aval->feedback
			);


		}while($serv->fetch());
	}

	if(is_array($categorias))
	{
		asort($categorias);
		$categorias = array_reverse($categorias);
		asort($tipos_de_servico);
		$tipos_de_servico = array_reverse($tipos_de_servico);
	}


	//pegando o numero de requisicoes atribuidas
	$sql = "
		SELECT
			COUNT(requisicao_item.id) AS total
		FROM
			requisicao_item,
			requisicao_item_servidor
		WHERE
			(
				requisicao_item.status = '".STATUS_ATRIBUIDO."' OR
				requisicao_item.status = '".STATUS_APROVADO."' OR
				requisicao_item.status = '".STATUS_EM_ANDAMENTO."'
			) AND
			requisicao_item_servidor.item = requisicao_item.id AND
			requisicao_item_servidor.servidor = '".$pes->id."'
	";
	
	$res = dboQuery($sql);
	$lin = dboFetchObject($res);
	$total_atribuidos = $lin->total;
	
?>

<header id="main-header">
	<div class="row">
		<div class="large-12 columns text-center">
			<h6><?= UNIDADE_NAME ?> - UNESP - <?= UNIDADE_CIDADE ?></h5>
			<h6><?= $_conf->nome_secao ?></h5>
			<h3>Relatório Servidores e Prestadores de Serviços</h3>
			<h6 class="subheader">Período de <?= date('d/m/Y', strtotime($data_i)) ?> a <?= date('d/m/Y', strtotime($data_f)) ?></h6>
		</div><!-- col -->
	</div><!-- row -->
</header>

<hr>

<section>

	<div id="resumo">
		<div class="row">
			<div class="large-12 columns">

				<h4><strong><?= $pes->nome; ?></strong></h4>

				<div class="row">
					<div class="large-12 columns">
						<img src="<?= $pes->getFoto(); ?>" alt="" class="left" style="margin-top: 8px; border: 1px solid #CCC; padding: 5px; width: 162px;">
						<table style="margin-left: 190px; width: calc(100% - 190px);">
							<tbody>
								<tr>
									<th class="text-left">Categoria</th>
									<td class="text-right"><?= ucfirst($definicao) ?></td>
								</tr>
								<tr>
									<th class="text-left">Total de serviços atribuídos no momento (<?= dboDate('d/M/Y') ?>)</th>
									<td class="text-right"><?= $total_atribuidos ?></td>
								</tr>
								<tr>
									<th class="text-left">Total de serviços concluídos no período</th>
									<td class="text-right"><?= intval($total_servicos_concluidos) ?></td>
								</tr>
								<tr>
									<th class="text-left" style="vertical-align: top;" rowspan='3'>
										Média das avaliações no período<br /><br />
										<?
											if(sizeof($avaliacoes['qualidade']))
											{
												?>
												<span style="font-weight: normal; line-height: 1.4;"><strong>Obs.:</strong><br />Confiabilidade do dado: <strong><?= number_format(sizeof($avaliacoes['qualidade'])/$total_servicos_concluidos*100, 1, ',', '.') ?>%</strong><br />Serviços avaliados: <strong><?= sizeof($avaliacoes['qualidade']) ?> de <?= $total_servicos_concluidos ?></strong></span>
												<?
											}
										?>
									</th>
									<td class="text-right" style=" border-left: 1px solid #555;">Qualidade: &nbsp;&nbsp;&nbsp; <?= ((sizeof($avaliacoes[qualidade]))?(number_format(array_sum($avaliacoes['qualidade']) / count($avaliacoes['qualidade']), 2, ',', '.')):('-')) ?></td>
								</tr>
								<tr>
									<td class="text-right" style="border-left: 1px solid #555;">Tempo: &nbsp;&nbsp;&nbsp; <?= ((sizeof($avaliacoes[tempo]))?(number_format(array_sum($avaliacoes['tempo']) / count($avaliacoes['tempo']), 2, ',', '.')):('-')) ?></td>
								</tr>
								<tr>
									<td class="text-right" style="border-left: 1px solid #555;">Feedback: &nbsp;&nbsp;&nbsp; <?= ((sizeof($avaliacoes[feedback]))?(number_format(array_sum($avaliacoes['feedback']) / count($avaliacoes['feedback']), 2, ',', '.')):('-')) ?></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div><!-- col -->
		</div><!-- row -->
		<div class="row" style="padding-top: 1em;">
			<div class="large-6 columns">
				<h4>Áreas atuadas</h4>
				<?
					if(sizeof($categorias))
					{
						?>
						<table>
							<thead>
								<tr>
									<th>Área</th>
									<th class="text-right">Serviços realizados</th>
									<th class="text-right">%</th>
								</tr>
							</thead>
							<tbody>
								<?
									foreach($categorias as $area => $quantidade)
									{
										?>
										<tr>
											<td><?= $area ?></td>
											<td class="text-right"><?= $quantidade ?></td>
											<td class="text-right"><?= number_format($quantidade/$total_servicos_concluidos*100, 1, ',', '.') ?>%</td>
										</tr>
										<?
									}
								?>
							</tbody>
						</table>
						<?
					}
					else
					{
						?>
						<p>- Nenhum serviço no período -</p>
						<?
					}
				?>
			</div>
			<div class="large-6 columns">
				<h4>Tipos de serviços realizados</h4>
				<?
					if(sizeof($tipos_de_servico))
					{
						?>
						<table>
							<thead>
								<tr>
									<th>Tipo de serviço</th>
									<th class="text-right">Quantidade</th>
									<th class="text-right">%</th>
								</tr>
							</thead>
							<tbody>
								<?
									foreach($tipos_de_servico as $servico => $quantidade)
									{
										?>
										<tr>
											<td><a href="#box-servicos" class="trigger-filter-tipo-servico trigger" data-tipo_servico="<?= htmlSpecialChars($servico) ?>"><?= $servico ?></a></td>
											<td class="text-right"><?= $quantidade ?></td>
											<td class="text-right"><?= number_format($quantidade/$total_servicos_concluidos*100, 1, ',', '.') ?>%</td>
										</tr>
										<?
									}
								?>
							</tbody>
						</table>
						<?
					}
					else
					{
						?>
						<p>- Nenhum serviço no período -</p>
						<?
					}
				?>
			</div>
		</div>
	</div>

	<?

		if($_GET['mostrar_servicos'])
		{
			?>
			<div id="box-servicos">
				
				<hr>
				
				<div class="row">
					<div class="large-6 columns"><h4>Serviços concluídos</h4></div>
					<div class="large-6 columns text-right">
						<a href="#" class="trigger-mostrar-todos-servicos button small no-margin radius" style="display: none;">Limpar filtros</a>
					</div>
				</div>

				<?
					if(sizeof($servicos))
					{
						?>
						<table>
							<thead>
								<tr>
									<th>Nº</th>
									<th>Tipo de serviço</th>
									<th>Descrição</th>
									<?
										if($_GET['mostrar_grupo_servidores'])
										{
											?>
											<th>Grupo</th>
											<?
										}							
									?>
								</tr>
							</thead>
							<tbody>
								<?
									foreach($servicos as $key => $dados)
									{
										?>
										<tr id="serv-<?= $dados['id_servico'] ?>" class="item-servico" data-tipo_servico="<?= htmlSpecialChars($dados['nome_tipo_servico']) ?>">
											<td class="page-break-avoid"><a class="no-print" href="servico-user-view.php?&id=<?= $dados['id_servico'] ?>&token=<?= $dados['token'] ?>" target="_blank"><?= $dados['numero_requisicao'] ?>/<?= $dados['numero_servico'] ?></a><span class="print-only"><?= $dados['numero_requisicao'] ?>/<?= $dados['numero_servico'] ?></span></td>
											<td class="page-break-avoid"><?= $dados['nome_tipo_servico'] ?></td>
											<td class="page-break-avoid"><?= (($_GET['mostrar_descricao_completa'])?($dados['descricao']):(maxString($dados['descricao'], 60))) ?></td>
											<?
												if($_GET['mostrar_grupo_servidores'])
												{
													?>
													<td>
													<?
													$sql = "
														SELECT
															servidor
														FROM
															requisicao_item_servidor
														WHERE
															item = '".$dados['id_servico']."' AND
															servidor <> '".$pes->id."'
													";
													$res = dboQuery($sql);
													if(dboAffectedRows())
													{
														$aux = array();
														while($lin = dboFetchObject($res))
														{
															$aux[] = $apelidos[$lin->servidor];
															sort($aux);
														}
														?>
														<span style="font-size: 9px;"><?= implode(", ", $aux) ?></span>
														<?
													}
													?>
													</td>
													<?
												}
											?>
										</tr>								
										<?
									}
								?>
							</tbody>
						</table>
						<?
					}
					else
					{
						?>
						<div class="row">
							<div class="large-12 columns"><p>- Nenhum serviço no período -</p></div>
						</div>
						<?
					}
				?>
			</div>
			<?
		}
		
		
		if($_GET['mostrar_comentarios'])
		{
			?>
			<div id="box-feedbacks">

				<hr>
				
				<div class="row">
					<div class="large-12 columns">
						<h4>Comentários dos usuários</h4>
						<?
							$count = 0;
							if(sizeof($avaliacoes))
							{
								foreach($avaliacoes['comentario'] as $key => $comment)
								{
									if(strlen($comment['comentario']) >= 4)
									{
										$count++;
										?>
										<p class="item-comentario" data-tipo_servico="<?= htmlSpecialChars($comment['tipo_servico']) ?>">"<?= $comment['comentario'] ?>" <small><?= (($_GET['mostrar_servicos'])?('- <a class="trigger-find-servico trigger" data-id_servico="'.$comment['id_servico'].'" href="#serv-'.$comment['id_servico'].'">'.$comment['numero_servico'].'</a>'):('')) ?></small></p>
										<?
									}
								}
							}
							if($count == 0)
							{
								?>
								<p>- Nenhum comentário relevante no período -</p>
								<?
							}
						?>
					</div>
				</div>
			</div>
			<?
		}


	?>
	
</section>

<script>
	$(document).ready(function(){
		$(document).on('click', '.trigger-find-servico', function(){

			clicado = $(this);
			$('tr.item-servico').removeClass('active');
			$('a.trigger-find-servico').removeClass('active');

			clicado.addClass('active');
			$('#serv-'+clicado.data('id_servico')).addClass('active');
		});

		$(document).on('click', '.trigger-filter-tipo-servico', function(e){

			clicado = $(this);
			$('tr.item-servico').removeClass('active');
			$('a.trigger').removeClass('active');

			clicado.addClass('active');
			$('tr.item-servico').removeClass('active').hide();
			$('tr.item-servico[data-tipo_servico="'+clicado.data('tipo_servico')+'"]').show().addClass('active');
			$('.trigger-mostrar-todos-servicos').show();
			$('p.item-comentario').hide();
			$('p.item-comentario[data-tipo_servico="'+clicado.data('tipo_servico')+'"]').show();
		});

		$(document).on('click', '.trigger-mostrar-todos-servicos', function(e){
			e.preventDefault();

			clicado = $(this);
			$('tr.item-servico').removeClass('active');
			$('a.trigger').removeClass('active');

			$('tr.item-servico').show();
			$('p.item-comentario').show();
			clicado.fadeOut();
		
		});

	}) //doc.ready
</script>

<? require_once('relatorios-footer.php'); ?>
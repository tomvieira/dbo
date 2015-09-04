<? require_once('relatorios-header.php'); ?>

<header id="main-header">
	<div class="row">
		<div class="large-12 columns text-center">
			<h6><?= UNIDADE_NAME ?> - UNESP - <?= UNIDADE_CIDADE ?></h5>
			<h6><?= $_conf->nome_secao ?></h5>
			<h3>Relatório de avaliações</h3>
			<h6 class="subheader">Período de <?= date('d/m/Y', strtotime($data_i)) ?> a <?= date('d/m/Y', strtotime($data_f)) ?></h6>
		</div><!-- col -->
	</div><!-- row -->
</header>

<hr>

<section>
	<div class="row">
		<div class="large-12 columns">

			<?php
				if($_GET['local'] > 0)
				{
					$loc = new local($_GET['local']);
				}
			?>

			<h4>Resumo das Avaliações </h4>
			<?
				if($loc)
				{
					?>
					<p><strong>Local:</strong> <?= $loc->getSmartLocal() ?></p>
					<?php
				}

				/* sql resumo, somente avaliações respondidas */
				$sql = "
					SELECT
						aval.*,
						serv.numero AS numero,
						serv.requisicao AS requisicao,
						serv.token AS requisicao_item_token,
						serv.descricao AS descricao,
						serv.local AS local,
						req.nome_requisitante AS nome_requisitante
					FROM
						avaliacao aval,
						requisicao_item serv,
						requisicao req
					WHERE
						aval.created_on >= '$data_i' AND
						aval.created_on <= '$data_f' AND
						aval.requisicao_item = serv.id AND
						serv.requisicao = req.id AND
						serv.status <> ".STATUS_CANCELADO."
						".($loc ? "AND serv.local IN(".implode(',', $loc->getTreeIds()).") " : "")."
					ORDER BY 
						aval.respondido_em
				";

				$res = dboQuery($sql);
				
				if(dboAffectedRows())
				{

					$comentarios = array();

					$dados_resumo = array();
					while($lin = dboFetchObject($res))
					{

						if(strlen(trim($lin->comentario)))
						{
							$comentarios[] = $lin->comentario;
						}

						list($data, $hora) = explode(" : ", $lin->created_on);
						list($ano, $mes, $dia) = explode("-", $data);

						$dados_resumo[$ano][intval($mes)][quantidade_total]++;

						if($lin->respondido_em != NULL)
						{

							$dados_resumo[$ano][intval($mes)][quantidade]++;

							$dados_resumo[$ano][intval($mes)][qualidade][5] = (($lin->qualidade == 5)?($dados_resumo[$ano][intval($mes)][qualidade][5]+1):($dados_resumo[$ano][intval($mes)][qualidade][5]));
							$dados_resumo[$ano][intval($mes)][qualidade][4] = (($lin->qualidade == 4)?($dados_resumo[$ano][intval($mes)][qualidade][4]+1):($dados_resumo[$ano][intval($mes)][qualidade][4]));
							$dados_resumo[$ano][intval($mes)][qualidade][3] = (($lin->qualidade == 3)?($dados_resumo[$ano][intval($mes)][qualidade][3]+1):($dados_resumo[$ano][intval($mes)][qualidade][3]));
							$dados_resumo[$ano][intval($mes)][qualidade][2] = (($lin->qualidade == 2)?($dados_resumo[$ano][intval($mes)][qualidade][2]+1):($dados_resumo[$ano][intval($mes)][qualidade][2]));
							$dados_resumo[$ano][intval($mes)][qualidade][1] = (($lin->qualidade == 1)?($dados_resumo[$ano][intval($mes)][qualidade][1]+1):($dados_resumo[$ano][intval($mes)][qualidade][1]));
							$dados_resumo[$ano][intval($mes)][qualidade_total] += $lin->qualidade;

							$dados_resumo[$ano][intval($mes)][tempo][5] = (($lin->tempo == 5)?($dados_resumo[$ano][intval($mes)][tempo][5]+1):($dados_resumo[$ano][intval($mes)][tempo][5]));
							$dados_resumo[$ano][intval($mes)][tempo][4] = (($lin->tempo == 4)?($dados_resumo[$ano][intval($mes)][tempo][4]+1):($dados_resumo[$ano][intval($mes)][tempo][4]));
							$dados_resumo[$ano][intval($mes)][tempo][3] = (($lin->tempo == 3)?($dados_resumo[$ano][intval($mes)][tempo][3]+1):($dados_resumo[$ano][intval($mes)][tempo][3]));
							$dados_resumo[$ano][intval($mes)][tempo][2] = (($lin->tempo == 2)?($dados_resumo[$ano][intval($mes)][tempo][2]+1):($dados_resumo[$ano][intval($mes)][tempo][2]));
							$dados_resumo[$ano][intval($mes)][tempo][1] = (($lin->tempo == 1)?($dados_resumo[$ano][intval($mes)][tempo][1]+1):($dados_resumo[$ano][intval($mes)][tempo][1]));
							$dados_resumo[$ano][intval($mes)][tempo_total] += $lin->tempo;

							$dados_resumo[$ano][intval($mes)][feedback][5] = (($lin->feedback == 5)?($dados_resumo[$ano][intval($mes)][feedback][5]+1):($dados_resumo[$ano][intval($mes)][feedback][5]));
							$dados_resumo[$ano][intval($mes)][feedback][4] = (($lin->feedback == 4)?($dados_resumo[$ano][intval($mes)][feedback][4]+1):($dados_resumo[$ano][intval($mes)][feedback][4]));
							$dados_resumo[$ano][intval($mes)][feedback][3] = (($lin->feedback == 3)?($dados_resumo[$ano][intval($mes)][feedback][3]+1):($dados_resumo[$ano][intval($mes)][feedback][3]));
							$dados_resumo[$ano][intval($mes)][feedback][2] = (($lin->feedback == 2)?($dados_resumo[$ano][intval($mes)][feedback][2]+1):($dados_resumo[$ano][intval($mes)][feedback][2]));
							$dados_resumo[$ano][intval($mes)][feedback][1] = (($lin->feedback == 1)?($dados_resumo[$ano][intval($mes)][feedback][1]+1):($dados_resumo[$ano][intval($mes)][feedback][1]));
							$dados_resumo[$ano][intval($mes)][feedback_total] += $lin->feedback;

							if($lin->qualidade < 3 || $lin->tempo < 3 || $lin->feedback < 3) 
							{
								$dados_resumo[$ano][intval($mes)][alerta][$lin->id] = $lin;
							}
							else
							{
								$dados_resumo[$ano][intval($mes)][alerta][$lin->id] = false;
							}
						
						}
						else
						{
							$dados_resumo[$ano][intval($mes)][quantidade_nao_respondida]++;
						}
					}
				}

				if(sizeof($dados_resumo))
				{
					ksort($dados_resumo);

					foreach($dados_resumo as $key => $value)
					{
						ksort($dados_resumo[$key]);
					}

					?>
					<table align="center" class="border">
						<thead>
							<tr>
								<th rowspan="2" class="border-top border-left none"></th>
								<?
									foreach($dados_resumo as $ano => $meses)
									{
										?>
										<th colspan='<?= sizeof($meses)*3 ?>' class="text-center border-left"><?= $ano ?></th>
										<?
									}						
								?>
							</tr>
							<tr>
								<?
									foreach($dados_resumo as $ano => $meses)
									{
										foreach($meses as $mes => $dados)
										{
											?><th colspan="3" class="text-center border-left"><?= month_short_name($mes) ?></th><?
										}
									}						
								?>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><strong>Qtd avaliações enviadas</strong></td>
								<?
									foreach($dados_resumo as $ano => $meses)
									{
										foreach($meses as $mes => $dados)
										{
											?>
											<td class="text-right border-left" colspan="2"><?= $dados[quantidade_total] ?></td>
											<td rowspan='2' class="text-center border-left"><span class="text-center has-tip tip-top" data-tooltip title="Taxa respondidas/enviadas"><?= 100-intval($dados[quantidade_nao_respondida]/$dados[quantidade_total]*100) ?>%</span></td>
											<?
										}
									}						
								?>
							</tr>
							<tr>
								<td><strong>Qtd não respondida</strong></td>
								<?
									foreach($dados_resumo as $ano => $meses)
									{
										foreach($meses as $mes => $dados)
										{
											?><td class="text-right border-left" colspan="2"><?= $dados[quantidade_nao_respondida] ?></td><?
										}
									}						
								?>
							</tr>
							<tr class="border-top thick">
								<td rowspan='6'><strong>Qualidade</strong></td>
								<?
									foreach($dados_resumo as $ano => $meses)
									{
										foreach($meses as $mes => $dados)
										{
											?>
											<td class="border-left thick">*****</td>
											<td class="text-right"><?= $dados[qualidade][5] ?></td>
											<td class="text-center"><?= getPercent($dados[qualidade][5], $dados[quantidade_total]-$dados[quantidade_nao_respondida]) ?></td>
											<?
										}
									}						
								?>
							</tr>
							<tr>
							<?
								foreach($dados_resumo as $ano => $meses)
								{
									foreach($meses as $mes => $dados)
									{
										?>
										<td class="border-left thick">****</td>
										<td class="text-right"><?= $dados[qualidade][4] ?></td>
										<td class="text-center"><?= getPercent($dados[qualidade][4], $dados[quantidade_total]-$dados[quantidade_nao_respondida]) ?></td>
										<?
									}
								}						
							?>
							</tr>
							<tr>
							<?
								foreach($dados_resumo as $ano => $meses)
								{
									foreach($meses as $mes => $dados)
									{
										?>
										<td class="border-left thick">***</td>
										<td class="text-right"><?= $dados[qualidade][3] ?></td>
										<td class="text-center"><?= getPercent($dados[qualidade][3], $dados[quantidade_total]-$dados[quantidade_nao_respondida]) ?></td>
										<?
									}
								}						
							?>
							</tr>
							<tr>
							<?
								foreach($dados_resumo as $ano => $meses)
								{
									foreach($meses as $mes => $dados)
									{
										?>
										<td class="border-left thick">**</td>
										<td class="text-right"><?= $dados[qualidade][2] ?></td>
										<td class="text-center"><?= getPercent($dados[qualidade][2], $dados[quantidade_total]-$dados[quantidade_nao_respondida]) ?></td>
										<?
									}
								}						
							?>
							</tr>
							<tr>
							<?
								foreach($dados_resumo as $ano => $meses)
								{
									foreach($meses as $mes => $dados)
									{
										?>
										<td class="border-left thick">*</td>
										<td class="text-right"><?= $dados[qualidade][1] ?></td>
										<td class="text-center"><?= getPercent($dados[qualidade][1], $dados[quantidade_total]-$dados[quantidade_nao_respondida]) ?></td>
										<?
									}
								}						
							?>
							</tr>
							<tr>
							<?
								foreach($dados_resumo as $ano => $meses)
								{
									foreach($meses as $mes => $dados)
									{
										?>
										<td class="border-left thick"><strong>Média</strong></td>
										<td class="text-right" colspan="2"><strong><?= number_format($dados[qualidade_total]/$dados[quantidade], 2, ',', '') ?></strong></td>
										<?
									}
								}						
							?>
							</tr>
							<tr class="border-top thick">
								<td rowspan='6'><strong>Tempo</strong></td>
								<?
									foreach($dados_resumo as $ano => $meses)
									{
										foreach($meses as $mes => $dados)
										{
											?>
											<td class="border-left thick">*****</td>
											<td class="text-right"><?= $dados[tempo][5] ?></td>
											<td class="text-center"><?= getPercent($dados[tempo][5], $dados[quantidade_total]-$dados[quantidade_nao_respondida]) ?></td>
											<?
										}
									}						
								?>
							</tr>
							<tr>
							<?
								foreach($dados_resumo as $ano => $meses)
								{
									foreach($meses as $mes => $dados)
									{
										?>
										<td class="border-left thick">****</td>
										<td class="text-right"><?= $dados[tempo][4] ?></td>
										<td class="text-center"><?= getPercent($dados[tempo][4], $dados[quantidade_total]-$dados[quantidade_nao_respondida]) ?></td>
										<?
									}
								}						
							?>
							</tr>
							<tr>
							<?
								foreach($dados_resumo as $ano => $meses)
								{
									foreach($meses as $mes => $dados)
									{
										?>
										<td class="border-left thick">***</td>
										<td class="text-right"><?= $dados[tempo][3] ?></td>
										<td class="text-center"><?= getPercent($dados[tempo][3], $dados[quantidade_total]-$dados[quantidade_nao_respondida]) ?></td>
										<?
									}
								}						
							?>
							</tr>
							<tr>
							<?
								foreach($dados_resumo as $ano => $meses)
								{
									foreach($meses as $mes => $dados)
									{
										?>
										<td class="border-left thick">**</td>
										<td class="text-right"><?= $dados[tempo][2] ?></td>
										<td class="text-center"><?= getPercent($dados[tempo][2], $dados[quantidade_total]-$dados[quantidade_nao_respondida]) ?></td>
										<?
									}
								}						
							?>
							</tr>
							<tr>
							<?
								foreach($dados_resumo as $ano => $meses)
								{
									foreach($meses as $mes => $dados)
									{
										?>
										<td class="border-left thick">*</td>
										<td class="text-right"><?= $dados[tempo][1] ?></td>
										<td class="text-center"><?= getPercent($dados[tempo][1], $dados[quantidade_total]-$dados[quantidade_nao_respondida]) ?></td>
										<?
									}
								}						
							?>
							</tr>
							<tr>
							<?
								foreach($dados_resumo as $ano => $meses)
								{
									foreach($meses as $mes => $dados)
									{
										?>
										<td class="border-left thick"><strong>Média</strong></td>
										<td class="text-right" colspan="2"><strong><?= number_format($dados[tempo_total]/$dados[quantidade], 2, ',', '') ?></strong></td>
										<?
									}
								}						
							?>
							</tr>
							<tr class="border-top thick">
								<td rowspan='6'><strong>Feedback</strong></td>
								<?
									foreach($dados_resumo as $ano => $meses)
									{
										foreach($meses as $mes => $dados)
										{
											?>
											<td class="border-left thick">*****</td>
											<td class="text-right"><?= $dados[feedback][5] ?></td>
											<td class="text-center"><?= getPercent($dados[feedback][5], $dados[quantidade_total]-$dados[quantidade_nao_respondida]) ?></td>
											<?
										}
									}						
								?>
							</tr>
							<tr>
							<?
								foreach($dados_resumo as $ano => $meses)
								{
									foreach($meses as $mes => $dados)
									{
										?>
										<td class="border-left thick">****</td>
										<td class="text-right"><?= $dados[feedback][4] ?></td>
										<td class="text-center"><?= getPercent($dados[feedback][4], $dados[quantidade_total]-$dados[quantidade_nao_respondida]) ?></td>
										<?
									}
								}						
							?>
							</tr>
							<tr>
							<?
								foreach($dados_resumo as $ano => $meses)
								{
									foreach($meses as $mes => $dados)
									{
										?>
										<td class="border-left thick">***</td>
										<td class="text-right"><?= $dados[feedback][3] ?></td>
										<td class="text-center"><?= getPercent($dados[feedback][3], $dados[quantidade_total]-$dados[quantidade_nao_respondida]) ?></td>
										<?
									}
								}						
							?>
							</tr>
							<tr>
							<?
								foreach($dados_resumo as $ano => $meses)
								{
									foreach($meses as $mes => $dados)
									{
										?>
										<td class="border-left thick">**</td>
										<td class="text-right"><?= $dados[feedback][2] ?></td>
										<td class="text-center"><?= getPercent($dados[feedback][2], $dados[quantidade_total]-$dados[quantidade_nao_respondida]) ?></td>
										<?
									}
								}						
							?>
							</tr>
							<tr>
							<?
								foreach($dados_resumo as $ano => $meses)
								{
									foreach($meses as $mes => $dados)
									{
										?>
										<td class="border-left thick">*</td>
										<td class="text-right"><?= $dados[feedback][1] ?></td>
										<td class="text-center"><?= getPercent($dados[feedback][1], $dados[quantidade_total]-$dados[quantidade_nao_respondida]) ?></td>
										<?
									}
								}						
							?>
							</tr>
							<tr>
							<?
								foreach($dados_resumo as $ano => $meses)
								{
									foreach($meses as $mes => $dados)
									{
										?>
										<td class="border-left thick"><strong>Média</strong></td>
										<td colspan="2" class="text-right"><strong><?= number_format($dados[feedback_total]/$dados[quantidade], 2, ',', '') ?></strong></td>
										<?
									}
								}						
							?>
							</tr>
							<tr class="no-print border-top thick">
								<td><span class="has-tip tip-top" data-tooltip title="Avaliações que tiveram notas abaixo de 3">Avaliações alertadas</span></td>
								<?
									foreach($dados_resumo as $ano => $meses)
									{
										foreach($meses as $mes => $dados)
										{
											if(sizeof($dados[alerta]))
											{
												$infos = array();
												foreach($dados[alerta] as $id => $info)
												{
													if($info)
													{
														$infos[] = makeShortRequisicaoItemLink($info);
													}
												}
											}
											?>
											<td colspan="3" valign="top" class="text-right border-left"><?= ((sizeof($infos))?(implode("<br />", $infos)):('')) ?></td>
											<?
										}
									}						
								?>
							</tr>
						</tbody>
					</table>
					<?
				}
			?>
			
		</div><!-- col -->
	</div><!-- row -->
	
</section>

<?
	if(sizeof($comentarios))
	{
		$total = sizeof($comentarios);
		$metade = ceil($total/2);
		?>
		<hr class="no-print">
		<div class="page-break-after"></div>
		<header class="page-header"></header>
		<section class="comentarios">
			<div class="row">
				<div class="large-12 columns">
					<h4>Comentários dos avaliadores</h4>
					<div class="row">
						<div class="large-6 columns">
							<?
								for($i=1; $i<=$total; $i++)
								{
									if($i == $metade)
									{
										?>
										</div>
										<div class="large-6 columns">
										<?
									}
									?>
									<blockquote class="avoid-break-inside"><?= $comentarios[$i-1] ?></blockquote>
									<?
								}
							?>
						</div>
					</div>
				</div>
			</div>
		</section>
		<?
	}
?>


<? require_once('relatorios-footer.php'); ?>
<? require_once('relatorios-header.php'); ?>

<header id="main-header">
	<div class="row">
		<div class="large-12 columns text-center">
			<h6><?= UNIDADE_NAME ?> - UNESP - <?= UNIDADE_CIDADE ?></h5>
			<h6><?= $_conf->nome_secao ?></h5>
			<h3>Relatório Total de Serviços</h3>
			<h6 class="subheader">Período de <?= date('d/m/Y', strtotime($data_i)) ?> a <?= date('d/m/Y', strtotime($data_f)) ?></h6>
		</div><!-- col -->
	</div><!-- row -->
</header>

<?php
	//especifico para este relatorio, pois há requisições
	//com data anterior ao desenvolvimento do sistema
	if($data_i == '2013-09-01')
	{
		$data_i = '2005-01-01';
	}
	$data_f .= ' 23:59:59';
?>

<hr>

<section>
	<div class="row">
		<div class="large-12 columns">
		
			<h4>Total de Serviços</h4>
			<?php
				if($_GET['local'] > 0)
				{
					$loc = new local($_GET['local']);
				}
				
				if($loc)
				{
					?>
					<p><strong>Local:</strong> <?= $loc->getSmartLocal() ?></p>
					<?php
				}

				/* sql resumo, somente avaliações respondidas */
				/* $sql = "
					SELECT 
						categoria_servico.nome AS nome,
						count(requisicao_item.*) AS total,
					FROM
						categoria_servico, 
						tipo_servico,
						requisicao_item
					WHERE
						requisicao_item.tipo_servico = tipo_servico.id AND
						tipo_servico.categoria_servico = categoria_servico.id AND
						requisicao_item.status <> ".STATUS_CANCELADO."
					GROUP BY 
						nome
					ORDER BY 
						nome
				";*/
				$sql = "
					SELECT 
						categoria_servico.nome AS nome,
						COUNT(categoria_servico.nome) AS total
					FROM
						categoria_servico, 
						tipo_servico,
						requisicao_item
					WHERE
						requisicao_item.tipo_servico = tipo_servico.id AND
						tipo_servico.categoria_servico = categoria_servico.id AND
						requisicao_item.status <> ".STATUS_CANCELADO." AND
						requisicao_item.created_on >= '$data_i' AND
						requisicao_item.created_on <= '$data_f'
						".($loc ? "AND requisicao_item.local IN(".implode(',', $loc->getTreeIds()).") " : "")."
					GROUP BY 
						nome
					ORDER BY 
						total DESC
				";

				$res = dboQuery($sql);
				
				if(dboAffectedRows())
				{
					$dados_resumo = array();
					$count = 0;
					while($lin = dboFetchObject($res))
					{
						$total_geral += $lin->total;
						$dados_resumo[$count][nome] = $lin->nome;
						$dados_resumo[$count][total] = $lin->total;
						$count++;
					}
				}

				if(sizeof($dados_resumo))
				{
					?>
					<table>
						<thead>
							<tr>
								<th class="text-left">Categoria de Serviço</th>
								<th class="text-right">Número de Serviços</th>
								<th class="text-right">% do Total</th>
							</tr>
						</thead>
						<tbody>
						<?
							foreach($dados_resumo as $key => $dados)
							{
								?>
								<tr>
									<td><?= $dados[nome] ?></td>
									<td class="text-right"><?= $dados[total] ?></td>
									<td class="text-right"><?= number_format($dados[total]/$total_geral*100, 1, ',', '') ?>%</td>
								</tr>
								<?
							}
						?>
						<tr>
							<td><strong>Total</strong></td>
							<td class="text-right"><strong><?= $total_geral ?></strong></td>
							<td class="text-right"><strong>100%</strong></td>
						</tr>
						</tbody>
					</table>
					<?
				}
			?>
			
			<p><strong>Observação:</strong> Este relatório contempla inclusive serviços que ainda não foram finalizados.</p>

		</div><!-- col -->
	</div><!-- row -->
	
</section>

<? require_once('relatorios-footer.php'); ?>
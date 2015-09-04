<? require_once('lib/includes.php'); ?>
<? require_once('auth.php'); ?>
<a class="close-reveal-modal">&#215;</a>
<?
	$local = new local(dboescape($_GET['local']));
	if($local->size())
	{
		?>
		<div class="row full">
			<div class="large-5 columns">
				<div class="sigla-local"><?= $local->sigla ?></div>
			</div>
			<div class="large-7 columns">
				<h3 class="no-margin"><?= $local->nome ?></h3>
			</div>
		</div>
		<div class="row full">
			<div class="large-12 columns">
				<hr>
			</div>
		</div>
		<div class="row full">
			<div class="large-12 columns">
				<?
					$chaves_ids = $local->getChavesIds();
					if(sizeof($chaves_ids))
					{
						$chave = new chave("WHERE numero IN(".implode(",", $chaves_ids).") ORDER BY numero");
						if($chave->size())
						{
							?>
							<div class="scrollbar">
								<table class="no-margin responsive">
									<thead>
										<tr>
											<th style="width: 1px;">Chave</th>
											<th style="width: 1px;"></th>
											<th>Local</th>
											<th class="nowrap">Retirada por</th>
											<th>Há</th>
										</tr>
									</thead>
									<tbody>
										<?
											do {
												$loc = new local($chave->local);
												$mov = $chave->getUltimaMovimentacao();
												?>
												<tr data-trigger-modal-chave="<?= $chave->numero ?>" class="pointer">
													<td data-title="Chave"><span class="label alt chave radius"><i class="fa-key"></i> <?= $chave->numero ?></span></td>
													<td data-title="Status"><span class="label alt radius status-<?= (($mov->operacao)?($mov->operacao):('devolucao')) ?>"><i class="fa-arrow-<?= (($mov->operacao == 'retirada')?('right'):('left')) ?>"></i></span></td>
													<td data-title="Local"><?= $loc->getSmartLocal() ?><?= ((strlen($chave->local_detalhe))?(" - ".$chave->local_detalhe):('')) ?></td>
													<td data-title="Retirada por"><?= (($mov->size() && $mov->retirada_por)?(pessoa::stGetShortName($mov->retirada_por)):('')) ?></td>
													<td data-title="Há"><?= (($mov->operacao == 'retirada' && $mov->data_hora)?('<span class="help nowrap" title="'.dataHoraNormal($mov->data_hora).'">'.ago($mov->data_hora, false).'</span>'):('')) ?></td>
												</tr>
												<?
											}while($chave->fetch());
										?>
									</tbody>
								</table>
							</div>
							<?
						}
					}
					else
					{
						?><h2 class="text-center">Este local ainda não possui chaves cadastradas</h2><?
					}
				?>
			</div>
		</div>
		
		<?
	}
	else
	{
		?><h1 class="text-center">Erro: O local especificado não existe.</h1><?
	}
?>
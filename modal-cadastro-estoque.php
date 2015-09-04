<?
	require_once('lib/includes.php');

	CSRFCheckRequest();

	if(secureUrl())
	{
		if(!hasPermission('gerenciar-estoque'))
		{
			?>
			<h1 class="text-center">Permissão negada</h1>
			<?
		}
		else
		{
			$mat = new material($_GET['material_id']);
			$est = new estoque();
			if($mat->size())
			{
				?>
				<a class="close-reveal-modal">&#215;</a>
				<div class="row">
					<div class="large-12 columns">
						<h3>Gerenciar estoque</h3>

						<hr class="small">

						<div class="row">
							<div class="large-12 columns">
								<div class="dynamic item">
									<label for="">Material</label>
									<p class="no-margin"><?= $mat->nome ?></p>
								</div>
								<div class="dynamic item">
									<label for="">Qtd. em Estoque</label>
									<p class="no-margin"><?= $mat->getQtdEstoque(array('color' => true, 'unidade' => true)); ?></p>
								</div>
							</div>
						</div>

						<hr class="small">

						<form method="post" action="<?= secureUrl('ajax-cadastro-estoque.php?action=cadastro&material_id='.$mat->id.'&'.CSRFVar()) ?>" class="no-margin peixe-json" id="form-cadastro-estoque" peixe-log>
							<div class="row">
								<div class="large-4 columns item">
									<label for="">Operação</label>
									<select name="operacao" required>
										<option value="">...</option>
										<?= $est->getMulti('operacao', 'select') ?>
									</select>
								</div>
								<div class="large-4 columns item">
									<label for="">Quantidade</label>
									<div class="row collapse">
										<div class="large-6 columns"><input type="number" name="quantidade" id="" value="" required class="text-right"/></div>
										<div class="large-6 columns"><span class="postfix radius"><?= $mat->getValue('unidade', $mat->unidade) ?></span></div>
									</div>
								</div>
								<div class="large-4 columns item">
									<label for="">Custo unitário</label>
									<div class="row collapse">
										<div class="large-8 columns"><input type="text" name="custo_unitario" id="" value="" class="price text-right" required/></div>
										<div class="large-4 columns"><span class="postfix radius">R$</span></div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="large-12 columns item">
									<label for="">Comentário</label>
									<textarea name="comentario" class="autosize" id="" required></textarea>
								</div>
							</div>

							<div class="row">
								<div class="large-12 columns text-right">
									<input type="submit" name="" id="" value="Atualizar estoque" class="button no-margin radius"/>
								</div>
							</div>
							
							<?= submitToken(); ?>
						</form>
						
					</div>
				</div>
				<?
			}
			else
			{
				?><h1 class="text-center">Material inexistente</h1><?
			}
		}
	}
	else
	{
		?><h1 class="text-center">Tentativa de acesso insegura</h1><?
	}

?>
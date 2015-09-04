<?
	require_once('lib/includes.php');

	if(isset($_GET['padding']))
	{
		?><div style="padding: 30px;"><?
	}

	CSRFCheckRequest();

	if(secureUrl())
	{
		$eq = new equipamento($_GET['equipamento_id']);
		$serv = new requisicao_item($_GET['requisicao_item_id']);

		if($_conf->local_entrada_equipamentos)
		{
			$local_entrada = $_conf->local_entrada_equipamentos;
			$loc = new local($_conf->local_entrada_equipamentos);
			$local_entrada_aux = $loc->getSmartLocal();
		}

		if($_conf->local_saida)
		{
			$local_saida = $_conf->local_saida;
			$loc = new local($_conf->local_saida);
			$local_saida_aux = $loc->getSmartLocal();
		}

		$local_requisicao_aux = $serv->_local->getSmartLocal();
		$local_requisicao = $serv->local;
		?>
		<form method="post" action="<?= secureUrl('ajax-equipamento-relacionado.php?action=update-situacao-equipamento&equipamento_id='.$eq->id.'&requisicao_item_id='.$_GET['requisicao_item_id'].'&'.CSRFVar()) ?>" class="no-margin peixe-json" id="form-situacao-equipamento-relacionado">
			<div class="row">
				<div class="large-12 columns item">
					<h3 class="nowrap-for-medium-up">Status do equipamento</h3>
					<hr class="small">
						<?
							if(!$eq->_tipo_equipamento->nome)
							{
								?>
								<label for="">Defina o tipo de Equipamento</label>
								<select name="tipo_equipamento" required>
									<option value="">Selecione...</option>
									<?
										$sql = "SELECT nome, id FROM tipo_equipamento ORDER BY nome";
										$res = dboQuery($sql);
										if(dboAffectedRows())
										{
											while($lin = dboFetchObject($res))
											{
												?>
												<option value="<?= $lin->id ?>"><?= $lin->nome ?></option>
												<?
											}
										}
									?>
								</select>
								<?
							}
						?>
						<p class="no-margin"><span class="nowrap-for-medium-up"><?= $eq->getSmartName(); ?></span></p>
					<hr class="small">
				</div>
			</div>
			
			<div class="row">
				<div class="large-12 columns item">
					<label for="">Evento</label>
					<select name="situacao" required id="situacao-equipamento-relacionado">
						<option value="" data-solicitar-responsavel="false" data-local_aux="" data-local="" data-show="false">...</option>
						<option value="entrada" data-solicitar-responsavel="false" data-local_aux="<?= htmlSpecialChars($local_entrada_aux) ?>" data-local="<?= $local_entrada ?>" data-show="true">Entrada n<?= (($_conf->genero == 'f')?('a'):('o')) ?> <?= $_conf->nome_curto_secao ?></option>
						<option value="saida" data-solicitar-responsavel="false" data-local_aux="<?= htmlSpecialChars($local_requisicao_aux) ?>" data-local="<?= $local_requisicao ?>" data-show="true">Devolução ao usuário</option>
						<option value="resolvido_local" data-solicitar-responsavel="false" data-local_aux="<?= htmlSpecialChars($local_requisicao_aux) ?>" data-local="<?= $local_requisicao ?>" data-show="false">Serviço realizado no local</option>
						<option value="assistencia_tecnica" data-solicitar-responsavel="true" data-local_aux="<?= htmlSpecialChars($local_saida_aux) ?>" data-local="<?= $local_saida ?>" data-show="false">Enviado à assistência técnica</option>
						<option value="baixa" data-solicitar-responsavel="false" data-local_aux="<?= htmlSpecialChars($local_saida_aux) ?>" data-local="<?= $local_saida ?>" data-show="false">Baixa</option>
					</select>
				</div>
			</div>
			
			<div class="row" style="display: none;" id="wrapper-local-equipamento-relacionado">
				<div class="large-12 columns item">
					<div class="row collapse">
						<div class="small-6 columns"><label for="">Local</label></div>
						<div class="small-6 columns text-right"><label for=""><a href="#" class="trigger-alterar-local" data-target="#local-situacao-aux"><i class="fa-pencil"></i> Alterar local</a></label></div>
					</div>
					<input type="text" name="local_aux" id="local-situacao-aux" value="" class="localpick" data-target="#local-situacao" required readonly data-readonly="true"/>
					<input type="hidden" name="local" id="local-situacao" value=""/>
				</div>
			</div>

			<div class="row" style="display: none;" id="wrapper-responsavel-assistencia_tecnica">
				<div class="large-12 columns item">
					<label for="">Responsável na assistência técnica <span class="bullet-required">*</span></label>
					<input type="text" name="responsavel_assistencia_tecnica" id="" value="" placeholder="Digite nome e telefone..." maybe-required/>
				</div>
			</div>

			<div class="row">
				<div class="large-12 columns">
					<label for="">Comentário</label>
					<textarea name="comentario" id="" class="autosize" style="height: 107px;"></textarea>
				</div>
			</div>

			<div class="row">
				<div class="large-12 columns text-right">
					<input type="submit" name="" id="" value="Atualizar situação" class="button radius no-margin"/>
				</div>
			</div>
			<?= submitToken(); ?>
		</form>
		<?
	}
	else
	{
		?>
		<h1>Tentantiva de acesso insegura</h1>
		<?
	}

	if(isset($_GET['padding']))
	{
		?></div><?
	}
?>
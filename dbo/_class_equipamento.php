<?

/* ================================================================================================================== */
/* DBO CLASS FILE FOR MODULE 'equipamento' ======================================= AUTO-CREATED ON 16/04/2013 08:41:50 */
/* ================================================================================================================== */

/* IMPORTANT: This file is generated only in the first DBO sync, what means you should edit only via text editor. */

if(!class_exists('equipamento'))
{
	class equipamento extends dbo
	{
		/* smart constructor: will perform load() upon numeric argument and loadAll() upon string argument */
		function __construct($foo = '')
		{
			parent::__construct('equipamento');
			if($foo != '')
			{
				if(is_numeric($foo))
				{
					$this->id = $foo;
					$this->load();
				}
				elseif(is_string($foo))
				{
					$this->loadAll($foo);
				}
			}
		}

		//your methods here

		function save()
		{
			
			global $_pes;

			$ret = parent::save();

			//adicionando equipamento ao estoque, se for o caso.
			if(intval($this->vinculo_material) > 0)
			{
				$est = new estoque();
				$est->operacao = 'entrada';
				$est->material = $this->vinculo_material;
				$est->data = $est->now();
				$est->quantidade = 1;
				$est->comentario = 'Cadastro de equipamento em inventário';
				$est->created_by = loggedUser();
				$est->created_by_equipamento = $this->id;
				$est->equipamento = $this->id;
				$est->save();
			}

			//criar local inicial
			$mov = new equipamento_movimentacao();
			$mov->created_by = loggedUser();
			$mov->created_on = dboNow();
			$mov->data = dboNow();
			$mov->equipamento = $this->id;
			$mov->responsavel = (($this->responsavel)?($this->responsavel):($_pes->nome));
			$mov->local_destino = $this->local;
			$mov->local_detalhe = $this->local_detalhe;
			$mov->justificativa = "Local inicial";
			$mov->status = $this->status;
			$mov->save();

			return $ret;
		}

		function delete()
		{
			//removendo todas as movimentações
			$mov = new equipamento_movimentacao("WHERE equipamento = '".$this->id."'");
			if($mov->size())
			{
				do {
					$mov->delete();
				}while($mov->fetch());
			}

			//removendo manutenções
			$man = new equipamento_manutencao("WHERE equipamento = '".$this->id."'");
			if($man->size())
			{
				do {
					$man->delete();
				}while($man->fetch());
			}

			//removendo do estoque
			$est = new estoque("WHERE equipamento = '".$this->id."'");
			if($est->size())
			{
				do {
					$est->forceDelete();
				}while($est->fetch());
			}

			//remover relacoes com requisicao
			$rel = new requisicao_item_equipamento("WHERE equipamento = '".$this->id."'");
			if($rel->size())
			{
				do {
					$rel->delete();
				}while($rel->fetch());
			}

			//deletando o equip em si
			return parent::delete();
		}

		function update()
		{

			$old = new equipamento($this->id);

			//verificando se é necessário criar uma movimentação
			if(
				$old->local != $this->local ||
				$this->responsavel
			)
			{
				//se isso não está saindo do autoadmin
				if(!$_POST['__dbo_update_flag'])
				{
					$mov = new equipamento_movimentacao();
					$mov->created_by = loggedUser();
					$mov->created_on = dboNow();
					$mov->data = (($this->data)?($this->data):(dboNow()));
					$mov->equipamento = $this->id;
					$mov->responsavel = $this->responsavel;
					$mov->justificativa = $this->justificativa;
					$mov->local_destino = $this->local;
					$mov->local_detalhe = $this->local_detalhe;
					$mov->created_by_requisicao_item = (($this->created_by_requisicao_item)?($this->created_by_requisicao_item):($this->null()));
					$mov->created_by_requisicao_item_equipamento = (($this->created_by_requisicao_item_equipamento)?($this->created_by_requisicao_item_equipamento):($this->null()));
					$mov->status = $this->status;
					$mov->save();
				}
			}

			//verificando se é necessário alterar a última movimentação
			if(
				$old->local_detalhe != $this->local_detalhe ||
				$old->status != $this->status
			)
			{
				$mov = new equipamento_movimentacao("WHERE equipamento = '".$this->id."' ORDER BY id DESC LIMIT 1");
				if($mov->size())
				{
					$mov->local_detalhe = $this->local_detalhe;
					$mov->status = $this->status;
					$mov->update();
				}
			}

			$ret = parent::update();

			//atualizando as informações do estoque
			$est = new estoque("WHERE equipamento = '".$this->id."'");

			//deletando do estoque, se o vinculo for removido.
			if($this->vinculo_material == $this->null())
			{
				//removendo do estoque
				if($est->size())
				{
					$est->forceDelete();
				}
			}
			else
			{
				//atualizando
				if($est->size())
				{
					$est->pocket('material_anterior', $est->material);
					$est->custo_unitario = $this->custo;
					$est->material = $this->vinculo_material;
					$est->update();
				}
				else
				{
					$est->operacao = 'entrada';
					$est->material = $this->vinculo_material;
					$est->data = $est->now();
					$est->quantidade = 1;
					$est->custo_unitario = $this->custo;
					$est->comentario = 'Vínculo do equipamento com o estoque';
					$est->created_by = loggedUser();
					$est->created_by_equipamento = $this->id;
					$est->equipamento = $this->id;
					$est->save();
				}
			}
			return $ret;
		}

		function recoverLocal()
		{
			$mov = new equipamento_movimentacao("WHERE equipamento = '".$this->id."' ORDER BY id DESC LIMIT 1");
			if($mov->size())
			{
				$this->local = $mov->local_destino;
				$this->local_detalhe = $mov->local_detalhe;
				$this->status = $mov->status;
				parent::update();
			}
		}

		function getSmartName($params = array())
		{
			extract($params);

			$color = ((!isset($color))?(true):($color));

			$nome = (($this->_tipo_equipamento->nome)?($this->_tipo_equipamento->nome):('<span class="'.(($color)?('color alert'):('')).'">Equipamento indefinido</span>')).(($this->equipamento_marca)?(' '.$this->_equipamento_marca->nome):('')).((strlen(trim($this->modelo)))?(' '.$this->modelo):('')).((strlen(trim($this->codigo)))?(' - Cod. '.$this->codigo):('')).((strlen(trim($this->patrimonio)))?(' - Pat. '.$this->patrimonio):('')).((strlen(trim($this->ai)))?(' - A.I. '.$this->ai):(''));
			return $nome;
		}

		function getBreadcrumbIdentifier()
		{
			return ((strlen(trim($this->codigo)))?('Cod. '.$this->codigo):('')).((strlen(trim($this->patrimonio)))?(' - Pat. '.$this->patrimonio):('')).((strlen(trim($this->ai)))?(' - AI '.$this->ai):(''));
		}

		static function getPainelList($params = array())
		{
			extract($params);

			$painel_material_show_list_equipamentos = (array)$painel_material_show_list_equipamentos;

			$tip = new tipo_equipamento();
			$eq = new equipamento();

			$sql = "
				SELECT
					tipo_equipamento.*,
					(SELECT COUNT(*) FROM equipamento WHERE tipo_equipamento = tipo_equipamento.id AND status <> '".STATUS_EQUIPAMENTO_BAIXA."' AND externo = 0) AS total_inventario,
					(SELECT COUNT(*) FROM equipamento WHERE tipo_equipamento = tipo_equipamento.id AND status = '".STATUS_EQUIPAMENTO_EMPRESTADO."' AND externo = 0) AS total_inventario_emprestado,
					(SELECT COUNT(*) FROM equipamento WHERE tipo_equipamento = tipo_equipamento.id AND status = '".STATUS_EQUIPAMENTO_NAO_LOCALIZADO."' AND externo = 0) AS total_inventario_nao_localizado
					".((ALERTAR_EQUIPAMENTOS_SEM_CODIGO)?(", (SELECT COUNT(*) FROM equipamento WHERE tipo_equipamento = tipo_equipamento.id AND status <> '".STATUS_EQUIPAMENTO_BAIXA."' AND codigo IS NULL AND externo = 0) AS total_inventario_sem_codigo"):(''))."
				FROM
					tipo_equipamento
				ORDER BY 
					nome
			";
			$tip->query($sql);

			ob_start();
			?>
			<div class="row full">
				<div class="large-8 columns">
					<table class="list responsive">
						<thead>
							<tr>
								<th>Tipo de equipamento</th>
								<th class="text-right help" title="Quantidade em inventário">Qtde.</th>
								<th></th>
								<th style="width: 1%;" class="text-right nowrap">
									<?
										if(hasPermission('insert', 'tipo_equipamento'))
										{
											?><span class="button radius tiny no-margin trigger-colorbox-modal" data-url="dbo_admin.php?dbo_mod=tipo_equipamento&dbo_new=1&dbo_modal=1&body_class=hide-breadcrumb<?= dboAdminRedirectCode('javascript: parent.reloadList(); parent.$.colorbox.close();') ?>"><i class="fa-plus"></i> Novo</span><?
										}
									?>
								</th>
							</tr>
						</thead>
						<tbody>
							<?
								if($tip->size())
								{
									do {
										//alertar equipamentos em codigo
										if(ALERTAR_EQUIPAMENTOS_SEM_CODIGO)
										{
											$total_inventario_sem_codigo += $tip->total_inventario_sem_codigo;
										}
										$total_inventario_emprestado += $tip->total_inventario_emprestado;
										$total_inventario_nao_localizado += $tip->total_inventario_nao_localizado;
										?>
										<tr class="pointer trigger-list-equipamentos <?= ((in_array($tip->id, $painel_material_show_list_equipamentos))?('active loaded'):('')) ?>" data-tipo_equipamento_id="<?= $tip->id ?>">
											<td data-title="Tipo de equip.">
												<?= $tip->nome ?> 
												<?= ((ALERTAR_EQUIPAMENTOS_SEM_CODIGO && $tip->total_inventario_sem_codigo > 0)?('<i class="fa-exclamation-circle color warning help font-14" title="'.$tip->total_inventario_sem_codigo.' equipamento(s) sem código de inventário"></i>'):('')) ?>
												<?= (($tip->total_inventario_nao_localizado > 0)?('<i class="fa-exclamation-circle color warning help font-14" title="'.$tip->total_inventario_nao_localizado.' equipamento(s) não localizado(s)"></i>'):('')) ?>
												<?= (($tip->total_inventario_emprestado > 0)?('<i class="fa-exclamation-circle color light help font-14" title="'.$tip->total_inventario_emprestado.' equipamento(s) emprestado(s)"></i>'):('')) ?>
											</td>
											<td class="text-right-for-medium-up" data-title="Quantidade"><?= $tip->total_inventario ?><span><span class="show-for-small"> uni.</span></span></td>
											<td class="hide-for-small">uni.</td>
											<td class="text-right control-icons nowrap">
												<?= ((hasPermission('update', 'tipo_equipamento'))?('<a href="#" data-url="dbo_admin.php?dbo_mod=tipo_equipamento&dbo_update='.$tip->id.'&dbo_modal=1&body_class=hide-breadcrumb'.dboAdminPostCode('javascript: parent.reloadList();').'" class="trigger-colorbox-modal stop-propagation"><i class="fa-fw fa-pencil"></i></a>'):('')) ?>
												<?= ((hasPermission('insert', 'equipamento'))?('<span class="button radius tiny nowrap no-margin stop-propagation trigger-colorbox-modal" data-url="dbo_admin.php?dbo_mod=equipamento&dbo_new=1&dbo_modal=1&body_class=hide-breadcrumb&dbo_fixo='.$tip->encodeFixos('tipo_equipamento='.$tip->id).dboAdminPostCode('javascript: parent.reloadList(); ').'"><i class="fa-plus"></i> Novo</span>'):('')) ?></td>
										</tr>
										<?
										if(in_array($tip->id, $painel_material_show_list_equipamentos))
										{
											?>
											<tr class="sublist">
												<td colspan="10"><?= equipamento::getPainelListDetalhe($tip->id, $params) ?></td>
											</tr>
											<?
										}
									}while($tip->fetch());
								}
								else
								{
									?>
									<tr>
										<td colspan="10" class="text-center">- Não há equipamentos de inventário cadastrados -</td>
									</tr>
									<?
								}
							?>
						</tbody>
					</table>
				</div>
				<div class="large-4 columns">
					<h3>Informações</h3>
					<fieldset>
						<p class="no-margin-for-small font-14"><i class="fa-tag fa-fw color light"></i> Próximo código disponível: <span class="label dark radius font-20 top-1"><?= equipamento::getProximoCodigoDisponive(); ?></span></p>
					</fieldset>
					<fieldset>
						<legend>Alertas</legend>
						<?
							if(
								$total_inventario_emprestado > 0 ||
								$total_inventario_sem_codigo > 0 ||
								$total_inventario_nao_localizado > 0
							)
							{
								if($total_inventario_sem_codigo > 0)
								{
									?>
									<p class="font-14 no-margin-for-small"><i class="fa-exclamation-circle fa-fw color warning font-14"></i> Há <?= $total_inventario_sem_codigo ?> equipamento<?= (($total_inventario_sem_codigo > 1)?('s'):('')) ?> sem código no inventário.</p>
									<?
								}
								if($total_inventario_nao_localizado > 0)
								{
									?>
									<p class="font-14 no-margin-for-small"><i class="fa-exclamation-circle fa-fw color warning font-14"></i> Há <?= $total_inventario_nao_localizado ?> equipamento<?= (($total_inventario_nao_localizado > 1)?('s'):('')) ?> não localizado<?= (($total_inventario_nao_localizado > 1)?('s'):('')) ?> no inventário.</p>
									<?
								}
								if($total_inventario_emprestado > 0)
								{
									?>
									<p class="font-14 no-margin-for-small"><i class="fa-exclamation-circle fa-fw color light font-14"></i> Há <?= $total_inventario_emprestado ?> equipamento<?= (($total_inventario_emprestado > 1)?('s'):('')) ?> emprestado<?= (($total_inventario_emprestado > 1)?('s'):('')) ?>.</p>
									<?
								}
							}
							else
							{
								?>
								<p class="no-margin-for-small color light text-center font-14">- Não há alertas -</p>
								<?
							}
						?>
					</fieldset>
					<fieldset class="hide-for-small">
						<legend>Últimas movimentações</legend>
						<?
							$mov = new equipamento_movimentacao("ORDER BY created_on DESC LIMIT 3");
							if($mov->size())
							{
								do {
									?>
									<p class="font-14 <?= (($mov->getIterator() == $mov->size())?('no-margin-for-small'):('')) ?>">
										<span class="color light"><i class="fa-clock-o fa-fw help" title="Data"></i> <?= ago($mov->created_on) ?>, <?= dboDate('d/m/Y H:i', strtotime($mov->created_on)) ?></span><br />
										<a href="<?= $mov->_equipamento->getModalPermalink(array('append' => dboAdminPostCode('javascript: parent.reloadList();'))); ?>" rel="modal"><i class="fa-tag fa-fw" title="Equipamento"></i> <span style="text-decoration: underline;"><?= $mov->_equipamento->getSmartName(); ?></span></a><br />
										<i class="fa-fw fa-map-marker color light help" title="Local de destino"></i> <?= $mov->_local_destino->getSmartLocal().((strlen(trim($mov->local_detalhe)))?(' ('.$mov->local_detalhe.')'):('')) ?><br />
										<i class="fa-user fa-fw color light help" title="Responsável no local de destino"></i> <?= $mov->responsavel ?>
									</p>
									<?
								}while($mov->fetch());
							}
							else
							{
								?>
								<p class="no-margin color light text-center font-14">- Não há movimentações -</p>
								<?
							}
						?>
					</fieldset>
				</div>
			</div>
			<?
			return ob_get_clean();
		}

		static function getPainelListManutencao($params = array())
		{
			global $_conf;

			extract($params);

			$painel_material_show_list_manutencao = (array)$painel_material_show_list_manutencao;
			
			ob_start();
			?>
			<div class="row full">
				<div class="large-8 columns">
					<table class="list reponsive">
						<thead>
							<tr>
								<th>Status</th>
								<th class="text-right help" title="Quantidade em inventário">Qtde.</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?
								//pegando os alertas
								$total_equipamentos_indefinidos = equipamento::getEquipamentosIndefinidos();
								$total_equipamentos_sem_interacao = equipamento::getEquipamentosSemInteracao();

								//pegando o total de equipamentos na Seção
								$sql = "
									SELECT
										equipamento
									FROM
										requisicao_item_equipamento r1
									WHERE
										r1.situacao = 'entrada' AND
										id = (
											SELECT 
												MAX(id) 
											FROM 
												requisicao_item_equipamento r2
											WHERE
												r1.equipamento = r2.equipamento
										)
								";
								$res = dboQuery($sql);
								$lin = dboFetchObject($res);
								$total = dboAffectedRows();

							?>
							<tr class="pointer trigger-list-manutencao <?= ((in_array('entrada', $painel_material_show_list_manutencao))?('active loaded'):('')) ?>" data-situacao="entrada">
								<td>
									<i class="fa-wrench fa-3x pull-left fa-fw color light"></i> 
									<span class="color light"> Equipamentos n<?= (($_conf->genero == 'f')?('a'):('o')) ?></span>
									<div class="top-4 pretty font-light font-21"><?= $_conf->nome_secao ?></div>
								</td>
								<td class="text-right font-21"><?= $total ?></td>
								<td></td>
							</tr>
							<?
								if(in_array('entrada', $painel_material_show_list_manutencao))
								{
									?>
									<tr class="sublist">
										<td colspan="10"><?= equipamento::getPainelListManutencaoDetalhe('entrada', $params) ?></td>
									</tr>
									<?
								}

								//pegando o total de equipamentos na assistencia tecnica
								$sql = "
									SELECT
										equipamento
									FROM
										requisicao_item_equipamento r1
									WHERE
										r1.situacao = 'assistencia_tecnica' AND
										id = (
											SELECT 
												MAX(id) 
											FROM 
												requisicao_item_equipamento r2
											WHERE
												r1.equipamento = r2.equipamento
										)
								";
								$res = dboQuery($sql);
								$lin = dboFetchObject($res);
								$total = dboAffectedRows();

							?>
							<tr class="pointer trigger-list-manutencao <?= ((in_array('assistencia_tecnica', $painel_material_show_list_manutencao))?('active loaded'):('')) ?>" data-situacao="assistencia_tecnica">
								<td>
									<i class="fa-truck fa-3x pull-left fa-fw color light"></i> 
									<span class="color light"> Equipamentos na</span>
									<div class="top-4 pretty font-light font-21">Assistência Técnica</div>
								</td>
								<td class="text-right font-21"><?= $total ?></td>
								<td></td>
							</tr>
							<?
								if(in_array('assistencia_tecnica', $painel_material_show_list_manutencao))
								{
									?>
									<tr class="sublist">
										<td colspan="10"><?= equipamento::getPainelListManutencaoDetalhe('assistencia_tecnica', $params) ?></td>
									</tr>
									<?
								}

								//pegando equipamentos relacionados
								$sql = "
									SELECT
										DISTINCT equipamento
									FROM
										requisicao_item_equipamento
									JOIN
										requisicao_item ON
										requisicao_item.id = requisicao_item_equipamento.requisicao_item
									WHERE
										requisicao_item.status < ".STATUS_SERVICO_CONCLUIDO."
								";
								$res = dboQuery($sql);
								$lin = dboFetchObject($res);
								$total = dboAffectedRows();

							?>
							<tr class="pointer trigger-list-manutencao <?= ((in_array('requisicoes_abertas', $painel_material_show_list_manutencao))?('active loaded'):('')) ?>" data-situacao="requisicoes_abertas">
								<td>
									<i class="fa-link fa-3x pull-left fa-fw color light"></i> 
									<span class="color light"> Equipamentos em </span>
									<div class="top-4 pretty font-light font-21">
										Requisições Abertas
										<span class="font-14">
											<?
												if($total_equipamentos_indefinidos)
												{
													?>
													<span class="font-14 top-minus-1"><i class="fa-exclamation-circle color alert help" title="Há <?= $total_equipamentos_indefinidos ?> equipamento<?= (($total_equipamentos_indefinidos > 1)?('s'):('')) ?> indefinido<?= (($total_equipamentos_indefinidos > 1)?('s'):('')) ?>"></i></span>
													<?
												}
												if($total_equipamentos_sem_interacao)
												{
													?>
													<span class="font-14 top-minus-1"><i class="fa-exclamation-circle color warning help" title="Há <?= $total_equipamentos_sem_interacao ?> equipamento<?= (($total_equipamentos_sem_interacao > 1)?('s'):('')) ?> relacionados sem interação"></i></span>
													<?
												}
											?>
										</span>
									</div>
								</td>
								<td class="text-right font-21"><?= $total ?></td>
								<td></td>
							</tr>
							<?
								if(in_array('requisicoes_abertas', $painel_material_show_list_manutencao))
								{
									?>
									<tr class="sublist">
										<td colspan="10"><?= equipamento::getPainelListManutencaoDetalhe('requisicoes_abertas', $params) ?></td>
									</tr>
									<?
								}

							?>
						</tbody>
					</table>
				</div>
				<div class="large-4 columns">
					<h3>Informações</h3>
					<fieldset>
						<legend>Alertas</legend>
						<?
							if(
								$total_equipamentos_indefinidos > 0 ||
								$total_equipamentos_sem_interacao > 0
							)
							{
								if($total_equipamentos_indefinidos)
								{
									?>
									<p class="font-14 no-margin-for-small"><i class="fa-exclamation-circle color alert fa-fw"></i> Há <?= $total_equipamentos_indefinidos ?> equipamento<?= (($total_equipamentos_indefinidos > 1)?('s'):('')) ?> indefinido<?= (($total_equipamentos_indefinidos > 1)?('s'):('')) ?></p>
									<?
								}
								if($total_equipamentos_sem_interacao)
								{
									?>
									<p class="font-14 no-margin-for-small"><i class="fa-exclamation-circle color warning fa-fw"></i> Há <?= $total_equipamentos_sem_interacao ?> equipamento<?= (($total_equipamentos_sem_interacao > 1)?('s'):('')) ?> relacionados sem interação</p>
									<?
								}
							}
							else
							{
								?>
								<p class="no-margin color light text-center font-14">- Não há alertas -</p>
								<?
							}
						?>
					</fieldset>
					<fieldset>
						<legend>Últimas movimentações</legend>
						<?
							$mov = new equipamento_movimentacao("ORDER BY created_on DESC LIMIT 3");
							if($mov->size())
							{
								do {
									?>
									<p class="font-14 <?= (($mov->getIterator() == $mov->size())?('no-margin-for-small'):('')) ?>">
										<span class="color light"><i class="fa-clock-o fa-fw help" title="Data"></i> <?= ago($mov->created_on) ?>, <?= dboDate('d/m/Y H:i', strtotime($mov->created_on)) ?></span><br />
										<a href="<?= $mov->_equipamento->getModalPermalink(array('append' => dboAdminPostCode('javascript: parent.reloadList();'))); ?>" rel="modal"><i class="fa-tag fa-fw" title="Equipamento"></i> <span style="text-decoration: underline;"><?= $mov->_equipamento->getSmartName(); ?></span></a><br />
										<i class="fa-fw fa-map-marker color light help" title="Local de destino"></i> <?= $mov->_local_destino->getSmartLocal().((strlen(trim($mov->local_detalhe)))?(' ('.$mov->local_detalhe.')'):('')) ?><br />
										<i class="fa-user fa-fw color light help" title="Responsável no local de destino"></i> <?= $mov->responsavel ?>
									</p>
									<?
								}while($mov->fetch());
							}
							else
							{
								?>
								<p class="no-margin color light text-center font-14">- Não há movimentações -</p>
								<?
							}
						?>
					</fieldset>
				</div>
			</div>
			<?
			return ob_get_clean();
		}

		static function getPainelListDetalhe($tipo_equipamento_id, $params = array())
		{

			extract($params);

			//setando parametros

			$painel_material_order_by = ((!$painel_material_order_by)?('codigo'):($painel_material_order_by));

			if($painel_material_order_by == 'patrimonio')
			{
				$order_by = 'ABS(patrimonio)';
				$search_pattern = '/(- )(Pat\. [0-9]+)/is';
			}
			elseif($painel_material_order_by == 'ai')
			{
				$order_by = 'ABS(ai)';
				$search_pattern = '/(- )(A\.I\. [0-9]+)/is';
			}
			else
			{
				$order_by = 'ABS(codigo)';
				$search_pattern = '/(- )(Cod\. [0-9]+)/is';
			}

			ob_start();
			$eq = new equipamento("WHERE tipo_equipamento = '".$tipo_equipamento_id."' AND externo = 0 ORDER BY ".$order_by);
			if($eq->size())
			{
				?>
				<table class="list responsive no-margin-for-small">
					<thead>
						<tr>
							<th>
							Equipamento 
							<span data-order_by="codigo" class="label trigger-order-by alt radius <?= (($painel_material_order_by == 'codigo')?(''):('cancel pointer')) ?>" title="Ordenar por código">Código <?= (($painel_material_order_by == 'codigo')?('<i class="fa-caret-down"></i>'):('')) ?></span> 
							<span data-order_by="patrimonio" class="label trigger-order-by alt radius <?= (($painel_material_order_by == 'patrimonio')?(''):('cancel pointer')) ?>" title="Ordenar por patrimônio">Patrimônio <?= (($painel_material_order_by == 'patrimonio')?('<i class="fa-caret-down"></i>'):('')) ?></span> 
							<span data-order_by="ai" class="label trigger-order-by alt radius <?= (($painel_material_order_by == 'ai')?(''):('cancel pointer')) ?>" title="Ordenar por A.I.">A.I. <?= (($painel_material_order_by == 'ai')?('<i class="fa-caret-down"></i>'):('')) ?></span> 
							<th>Status</th>
							<th>Local</th>
						</tr>
					</thead>
					<tbody>
						<?
							do {
								$loc = new local($eq->local);
								?>
								<tr class="trigger-colorbox-modal pointer" data-url="<?= $eq->getModalPermalink(array('append' => dboAdminPostCode('javascript: parent.reloadList(); '))); ?>">
									<td data-title="Equipamento">
										<?= preg_replace($search_pattern, '${1}<strong>${2}</strong>', $eq->getSmartName()) ?>
										<?= ((ALERTAR_EQUIPAMENTOS_SEM_CODIGO && $eq->codigo == null)?(' <i class="fa-exclamation-circle color warning help font-14" title="Equipamento sem código de inventário"></i>'):('')) ?>
										<?= (($eq->status == STATUS_EQUIPAMENTO_NAO_LOCALIZADO)?('<i class="fa-exclamation-circle color warning help font-14" title="Equipamento não localizado"></i>'):('')) ?>
										<?= (($eq->status == STATUS_EQUIPAMENTO_EMPRESTADO)?('<i class="fa-exclamation-circle color light help font-14" title="Equipamento emprestado"></i>'):('')) ?>
									</td>
									<td data-title="Status"><?= $eq->getValue('status', $eq->status) ?></td>
									<td data-title="Local"><?= $loc->getSmartLocal() ?></td>
								</tr>
								<?
							}while($eq->fetch());
						?>
					</tbody>
				</table>
				<?
			}
			else
			{
				?><p class="text-center no-margin-for-small">- Não há equipamentos cadastrados nesta categoria -</p><?
			}
			return ob_get_clean();
		}

		static function getPainelListManutencaoDetalhe($situacao, $params = array())
		{

			extract($params);

			//setando parametros

			/*$painel_material_order_by = ((!$painel_material_order_by)?('codigo'):($painel_material_order_by));

			if($painel_material_order_by == 'patrimonio')
			{
				$order_by = 'ABS(patrimonio)';
				$search_pattern = '/(- )(Pat\. [0-9]+)/is';
			}
			elseif($painel_material_order_by == 'ai')
			{
				$order_by = 'ABS(ai)';
				$search_pattern = '/(- )(A\.I\. [0-9]+)/is';
			}
			else
			{
				$order_by = 'ABS(codigo)';
				$search_pattern = '/(- )(Cod\. [0-9]+)/is';
			}*/

			$search_pattern = '/(- )(Pat\. [0-9]+)/is';

			$columns = array('equipamento', 'status_relacionamento', 'data', 'status_requisicao');

			if($situacao == 'requisicoes_abertas')
			{
				$sql = "
					SELECT 
						equipamento.*,
						requisicao_item.created_on AS requisicao_data,
						requisicao_item.id AS requisicao_item_id
					FROM equipamento
					JOIN requisicao_item_equipamento ON
						requisicao_item_equipamento.equipamento = equipamento.id
					JOIN requisicao_item ON
						requisicao_item.id = requisicao_item_equipamento.requisicao_item
					WHERE
						requisicao_item.status < ".STATUS_SERVICO_CONCLUIDO."
					GROUP BY
						equipamento.id
					ORDER BY
						requisicao_data ASC
				";
				array_push($columns, 'local');
			}
			//equipamentos na DTI
			elseif($situacao == 'entrada')
			{
				$sql = "
					SELECT 
						equipamento.*,
						requisicao_item.created_on AS requisicao_data,
						requisicao_item.id AS requisicao_item_id
					FROM 
						equipamento
					JOIN requisicao_item_equipamento r1 ON
						r1.equipamento = equipamento.id
					JOIN requisicao_item ON
						requisicao_item.id =  r1.requisicao_item
					WHERE
						r1.situacao = 'entrada' AND
						r1.id = (
							SELECT 
								MAX(id) 
							FROM 
								requisicao_item_equipamento r2
							WHERE
								r1.equipamento = r2.equipamento
						)
					ORDER BY
						requisicao_data ASC
				";
				array_push($columns, 'local');
			}
			//equipamentos na DTI
			elseif($situacao == 'assistencia_tecnica')
			{
				$sql = "
					SELECT 
						equipamento.*,
						requisicao_item.id AS requisicao_item_id,
						requisicao_item.created_on AS requisicao_data,
						equipamento_movimentacao.responsavel AS responsavel
					FROM 
						equipamento
					JOIN requisicao_item_equipamento r1 ON
						r1.equipamento = equipamento.id
					JOIN requisicao_item ON
						requisicao_item.id = r1.requisicao_item
					JOIN equipamento_movimentacao ON
						equipamento_movimentacao.equipamento = equipamento.id
					WHERE
						equipamento_movimentacao.id = (
							SELECT 
								MAX(id) 
							FROM 
								equipamento_movimentacao em2
							WHERE
								em2.equipamento = equipamento.id
						) AND
						r1.situacao = 'assistencia_tecnica' AND
						r1.id = (
							SELECT 
								MAX(id) 
							FROM 
								requisicao_item_equipamento r2
							WHERE
								r1.equipamento = r2.equipamento
						)
					ORDER BY
						requisicao_data ASC
				";
				array_push($columns, 'responsavel');
			}

			$eq = new equipamento();
			$eq->query($sql);
		
			ob_start();
			if($eq->size())
			{
				?>
				<table class="list responsive no-margin">
					<thead>
						<tr>
							<th>Equipamento</th>
							<th style="width: 1px">Eq.</th>
							<th class="help" title="Requisição" class="text-center" style="width: 1px">Rq.</th>
							<th class="help" title="Status do equipamento">St.</th>
							<th class="help" title="Data da última interação">Data Int.</th>
							<?
								if(in_array('local', $columns))
								{
									?><th>Local</th><?
								}
								if(in_array('responsavel', $columns))
								{
									?><th>Responsável</th><?
								}
							?>
							<th class="help" title="Status da requisição" style="width: 1px;">Status. Req.</th>
						</tr>
					</thead>
					<tbody>
						<?
							do {
								$rel = new requisicao_item_equipamento("WHERE equipamento = '".$eq->id."' AND requisicao_item = '".$eq->requisicao_item_id."' ORDER BY id DESC LIMIT 1");
								$serv = new requisicao_item($eq->requisicao_item_id);
								?>
								<tr class="trigger-gerenciar-requisicao-for-medium-up pointer" data-requisicao_id="<?= $serv->requisicao ?>" data-requisicao_item_id="<?= $serv->id ?>">
									<td data-title="Equipamento"><?= preg_replace($search_pattern, '${1}<strong>${2}</strong>', $eq->getSmartName()) ?></td>
									<td class="nowrap-for-medium-up" data-title="Vis. equip."><?= $eq->getLinkTag(); ?></td>
									<td class="nowrap-for-medium-up" data-title="Requisição"><?= $serv->getTagModal() ?></td>
									<td data-title="Status"><?= $rel->getTagSituacao() ?></td>
									<td data-title="Data Req."><span class="help" title="<?= dboDate('d/M/Y H:i', strtotime($rel->data)) ?>"><?= ago($rel->data) ?></span></td>
									<?
										if(in_array('local', $columns))
										{
											$loc = new local($eq->local);
											?><td data-title="Local atual"><?= $loc->getSmartLocal() ?></td><?
										}
										if(in_array('responsavel', $columns))
										{
											?><td data-title="Responsável"><?= $eq->responsavel ?></td><?
										}
									?>
									<td class="nowrap-for-medium-up" data-title="Status Req."><span class="radius tag-status status-<?= $serv->status ?>" title='<?= $serv->getValue('status', $serv->status) ?>'><?= (($params[status] == 'aguardando')?(substr($serv->getValue('status', $serv->status), 0, 2)):($serv->getValue('status', $serv->status))) ?></span></td>
								</tr>
								<?
							}while($eq->fetch());
						?>
					</tbody>
				</table>
				<?
			}
			else
			{
				?><p class="text-center no-margin">- Não há equipamentos nesta situação -</p><?
			}
			return ob_get_clean();
		}

		static function getEquipamentosIndefinidos($params = array('return' => 'total'))
		{
			extract($params);

			if($return == 'total')
			{
				$sql = "SELECT COUNT(*) AS total FROM equipamento WHERE tipo_equipamento < 1;";
				$res = dboQuery($sql);
				$lin = dboFetchObject($res);
				return $lin->total;
			}
			elseif($return == 'object')
			{
				$sql = "SELECT * FROM equipamento WHERE tipo_equipamento < 1 ORDER BY patrimonio;";
				$eq = new equipamento();
				$eq->query($sql);
				return $eq;
			}
		}

		static function getEquipamentosSemInteracao($params = array('return' => 'total'))
		{
			extract($params);

			if($return == 'total')
			{
				$sql = "
					SELECT 
						COUNT(*) AS total
					FROM 
						requisicao_item_equipamento r1
					JOIN requisicao_item ON
						requisicao_item.id = r1.requisicao_item
					WHERE 
						requisicao_item.status < '".STATUS_CANCELADO."' AND
						r1.situacao IS NULL AND
						r1.id = (
							SELECT 
								MAX(id) 
							FROM 
								requisicao_item_equipamento r2
							WHERE
								r2.requisicao_item = r1.requisicao_item AND
								r2.equipamento = r1.equipamento
						)
				";
				$res = dboQuery($sql);
				$lin = dboFetchObject($res);
				return $lin->total;
			}
			elseif($return == 'object')
			{
				$sql = "
					SELECT 
						equipamento.*,
						requisicao_item.numero AS requisicao_item_numero,
						requisicao_item.requisicao AS requisicao
					FROM 
						requisicao_item_equipamento r1
					JOIN equipamento ON
						equipamento.id = r1.equipamento
					JOIN requisicao_item ON
						requisicao_item.id = r1.requisicao_item
					WHERE 
						requisicao_item.status != '".STATUS_CANCELADO."' AND
						r1.situacao IS NULL AND
						r1.id = (
							SELECT 
								MAX(id) 
							FROM 
								requisicao_item_equipamento r2
							WHERE
								r2.requisicao_item = r1.requisicao_item AND
								r2.equipamento = r1.equipamento
						)
				";
				$eq = new equipamento();
				$eq->query($sql);
				return $eq;
			}
		}

		static function getProximoCodigoDisponive()
		{
			$sql = "SELECT MAX(ABS(codigo)) AS ultimo FROM equipamento;";
			$res = dboQuery($sql);
			$lin = dboFetchObject($res);
			return $lin->ultimo+1;
		}

		function getLinkTag($params = array())
		{
			extract($params);

			$fixed_width = ((!isset($fixed_width))?(true):($fixed_width));
			
			ob_start();
			?>
				<span class="font-14">
					<a href="#" data-url="<?= $this->getModalPermalink(array('hide_acoes' => 1)) ?>" class="trigger-colorbox-modal stop-propagation">
						<span class="has-tip tip-top pointer hide-for-small" data-tooltip title="<?= htmlSpecialChars($this->getSmartName()) ?>"><i class="fa-tag <?= (($fixed_width)?('fa-fw'):('')) ?>"></i></span>
						<span class="show-for-small has-tip pointer"><i class="fa-tag <?= (($fixed_width)?('fa-fw'):('')) ?>"></i> Visualizar</span>
					</a>
				</span>
			<?
			return ob_get_clean();
			//return '<a href="#" data-url="'.$this->getModalPermalink(array('hide_acoes' => 1)).'" target="_blank" class="trigger-colorbox-modal has-tip tip-top font-14 stop-propagation" data-tooltip title="'.htmlSpecialChars($this->getSmartName()).'"><i class="fa-tag '.(($fixed_width)?('fa-fw'):('')).' pointer"></i><span class="show-for-small"> Visualizar</span></a>';
		}

		function getModalPermalink($params = array())
		{
			extract($params);
			return $this->getAdminPermalink().($this->tipo_equipamento ? '&dbo_fixo='.$this->encodeFixos('tipo_equipamento='.$this->tipo_equipamento) : '').'&body_class=modal hide-breadcrumb hide-insert-button '.(($hide_acoes)?(' hide-acoes'):('')).dboAdminPostCode('javascript: if(typeof parent["reloadList"] == "function") { parent.reloadList(); } if(typeof parent["reloadEquipamentosRelacionados"] == "function") { parent.reloadEquipamentosRelacionados(); } ').(($append)?($append):(''));
		}

		function getAdminPermalink()
		{
			return preg_replace('/(\/dbo$)/is', '', DBO_URL).'/dbo_admin.php?dbo_mod=equipamento&dbo_update='.$this->id;
		}

		static function getMenuEquipamentosSemFoto($params = array())
		{
			extract($params);

			$total = equipamento::getEquipamentosSemFoto(array('return' => 'total'));

			if($total)
			{
				return '<li id="menu-equipamentos-sem-foto" class="show-for-small"><a href="equipamentos-sem-foto.php">Equipamentos sem foto <i class="fa-camera color medium fa-fw"></i><span class="wrapper-notifications"><span class="notification-bubble warning">'.$total.'</span></span></a></li>';
			}
		}

		static function getEquipamentosSemFoto($params = array())
		{
			extract($params);

			if($return == 'total')
			{
				$sql = "
					SELECT
						COUNT(DISTINCT equipamento.id) AS total
					FROM
						equipamento
					JOIN requisicao_item_equipamento ON
						requisicao_item_equipamento.equipamento = equipamento.id
					WHERE
						equipamento.foto = ''
				";
				$res = dboQuery($sql);
				$lin = dboFetchObject($res);
				return $lin->total;
			}
			elseif($return == 'object')
			{
				$sql = "
					SELECT
						equipamento.*
					FROM
						equipamento
					JOIN requisicao_item_equipamento ON
						requisicao_item_equipamento.equipamento = equipamento.id
					WHERE
						equipamento.foto = ''
					GROUP BY equipamento.id
					ORDER BY equipamento.patrimonio
				";

				$eq = new equipamento();
				$eq->query($sql);
				return $eq;
			}
		}

	} //class declaration
} //if ! class exists

function form_equipamento_append($tipo, $obj)
{
	ob_start();
	$fixos = $obj->getFixos();
	if($tipo == 'insert' && $fixos[tipo_equipamento])
	{
		$man = new equipamento_manutencao_periodica("WHERE tipo_equipamento = '".$fixos[tipo_equipamento]."' ORDER BY nome");
		if($man->size())
		{
			?>
			<div class="row">
				<div class="large-12 columns">
					<h4>Próximas manutenções periódicas</h4>
					<div class="row hide-form-small">
						<div class="large-4 columns large-centered">
							<div class="helper arrow-bottom margin-bottom text-center">Deixe a data em branco para ignorar o agendamento</div>
						</div><!-- col -->
					</div><!-- row -->
					
					<div class="row hide-for-small">
						<div class="large-4 columns"><label></label></div><!-- col -->
						<div class="large-4 columns"><label>Data Agendada</label></div><!-- col -->
						<div class="large-4 columns"><label>Observações</label></div><!-- col -->
					</div><!-- row -->
					
					<?
						do {
							$Y = (($man->periodo == 'Y')?($man->valor):(0));
							$m = (($man->periodo == 'm')?($man->valor):(0));
							$d = (($man->periodo == 'd')?($man->valor):(0));
							?>
							<script>
								function suggestData(clicado) {
									clicado = $(clicado);
									clicado.closest('.row').find('input').val(clicado.attr('data-data'));
								}
							</script>
							<div class="row">
								<div class="large-4 columns"><span class="form-height-fix has-tip tip-top" data-tooltip title="<?= $man->getSmartNome(); ?>"><?= $man->nome ?></span></div><!-- col -->
								<div class="large-4 columns">
									<div class="row collapse">
										<div class="small-6 columns"><input type="text" placeholder="data" name="aux_data_agendada[<?= $man->id ?>]" class="datepick" value=""/></div>
										<div class="small-6 columns"><span class="postfix secondary radius pointer" onClick="suggestData(this)" data-data="<?= date('d/m/Y', strtotime(somaDataAMD(date('Y-m-d'), $Y, $m, $d))) ?>"><?= $man->valor ?> <?= $man->getNomePeríodo($man->periodo, $man->valor) ?></span></div><!-- col -->
									</div><!-- row -->
									
								</div><!-- col -->
								<div class="large-4 columns"><input type="text" name="aux_observacao[<?= $man->id ?>]" value="" placeholder='observação'/></div><!-- col -->
							</div><!-- row -->
							<?
						}while($man->fetch());
					?>
				</div><!-- col -->
			</div><!-- row -->
			<?
		}
	}
	elseif($tipo == 'update')
	{
		?>
		<div id="detalhes-movimentacao" style="display: none;">
			<div class="row">
				<div class="large-4 columns item">
					<label>Data da mudança de local <span style="color: red">*</span></label>
					<input type="text" name="movimentacao_data" value="" class="datepick required" data-name="Data da mudança de local"/>
				</div>
				<div class="large-8 columns item">
					<label>Responsável pelo equipamento no novo local</label>
					<div class='row collapse'>
						<div class='small-9 large-10 columns'><input type="text" name="movimentacao_responsavel" value="" data-name="Responsável pelo equipamento no novo local"/></div>
						<div class='small-3 large-2 columns'><input type='button' name='' tabindex='-1' value="Alterar" class="nome-clearer disabled button postfix radius"/></div>
					</div>				
					
				</div>
			</div>
			<div class="row">
				<div class="large-12 columns item">
					<label>Justificativa da mudança <span style="color: red">*</span></label>
					<input type="text" name="movimentacao_justificativa" value="" class="required" data-name="Justificativa da mudança"/>
				</div>
			</div>
		</div>
		<?
		$ob_result = ob_get_clean();
		$ob_result = str_replace(array("\n", "\n\r", "\t"), "", trim(dboescape($ob_result)));
		ob_start();
		?>
		<div class="row">
			<div class="large-12 columns">
				<h4>Movimentações</h4>
			</div>
		</div>
		<?
			$mov = new equipamento_movimentacao("WHERE equipamento = '".$obj->id."' ORDER BY id");
			if($mov->size())
			{
				$req = new requisicao();
				?>
				<div class="row">
					<div class="large-12 columns">
						<table class="font-14 responsive">
							<thead>
								<tr>
									<th>Data</th>
									<th></th>
									<th>Local de destino</th>
									<th title="Responsável no local" class="help nowrap">Resp. no local</th>
								</tr>
							</thead>
							<tbody>
								<?
									do {
										$loc = new local($mov->local_destino);
										?>
										<tr class="help" title="Cadastrado por <?= htmlSpecialChars($mov->_created_by->nome) ?> em <?= dboDate('d/M/Y H:i', strtotime($mov->created_on)) ?>">
											<td data-title="Data"><span class="nowrap-for-medium-up" title="<?= dboDate('d/M/Y H:i', strtotime($mov->created_on)) ?>"><?= ago($mov->created_on) ?></span></td>
											<td class="nowrap-for-medium-up" data-title="Informações">
												<span class="has-tip tip-top font-14" data-tooltip title="<?= htmlSpecialChars($mov->justificativa) ?>"><i class="fa-comment-o fa-fw"></i></span>
												<?
													//pegando os dados da requisição que este equipamento está relacionado.
													//primeiro checando se foi criado por movimentação de estoque/inventario
													if($mov->created_by_requisicao_item)
													{
														if($req->id != $mov->created_by_requisicao_item)
														{
															$req = new requisicao_item($mov->created_by_requisicao_item);
														}
														echo '<span class="font-14 has-tip tip-top" data-tooltip title="Saída de estoque"><i class="fa-arrow-circle-right fa-fw color light"></i></span> '.$req->getTagModal();
													}
													elseif($mov->created_by_requisicao_item_equipamento)
													{
														echo '<span class="font-14 has-tip tip-top" data-tooltip title="Relacionado'.(($mov->_created_by_requisicao_item_equipamento->comentario)?(': '.$mov->_created_by_requisicao_item_equipamento->comentario):('')).'"><i class="fa-link fa-fw"></i></span> '.$mov->_created_by_requisicao_item_equipamento->_requisicao_item->getTagModal();
													}
												?>
											</td>
											<td data-title="Local"><?= $loc->getSmartLocal().((strlen(trim($mov->local_detalhe)))?(' ('.$mov->local_detalhe.')'):('')) ?></td>
											<td data-title="Responsável"><?= $mov->responsavel ?></td>
											</td>
										</tr>
										<?
									}while($mov->fetch());
								?>
							</tbody>
						</table>
					</div>
				</div>
				<?
			}
			else
			{
				?><p class="text-center">- Não há movimentações -</p><?
			}
			//pegando o historico de requisições relacionadas deste equipamento
			$sql = "
				SELECT 
					requisicao_item.*,
					requisicao.nome_requisitante
				FROM 
					requisicao_item
				JOIN requisicao_item_equipamento ON
					requisicao_item.id = requisicao_item_equipamento.requisicao_item
				JOIN requisicao ON
					requisicao.id = requisicao_item.requisicao
				WHERE
					requisicao_item_equipamento.equipamento = '".$obj->id."'
				GROUP BY
					requisicao_item.id
				ORDER BY
					requisicao_item.created_on
			";
			$serv = new requisicao_item();
			$serv->query($sql);
			if($serv->size())
			{
				?>
				<div class="row">
					<div class="large-12 columns">
						<h4>Requisições relacionadas</h4>
					</div>
				</div>
				<div class="row">
					<div class="large-12 columns">
						<table class="font-14 responsive">
							<thead>
								<tr>
									<th>Data</th>
									<th><i class="fa-fw fa-spacer"></i> Nro.</th>
									<th>Tipo de serviço</th>
									<th>Requisitante</th>
								</tr>
							</thead>
							<tbody>
								<?
									do {
										?>
										<tr>
											<td class="nowrap-for-medium-up" data-title="Data"><span class="nowrap-for-medium-up" title="<?= dboDate('d/M/Y H:i', strtotime($serv->created_on)) ?>"><?= ago($serv->created_on) ?></span></td>
											<td class="nowrap-for-medium-up" data-title="Requisição"><?= $serv->getTagModal(); ?> <?= $serv->getSmartNumber(); ?></td>
											<td data-title="Tipo de serviço"><?= $serv->_tipo_servico->nome ?></td>
											<td data-title=""><?= $serv->_requisicao->nome_requisitante ?></td>
										</tr>
										<?
									}while($serv->fetch());
								?>
							</tbody>
						</table>
					</div>
				</div>
				<?
			}
		?>
		<script>

			var local = $('#item-local').find('input[name="local"]');
			var local_atual = local.val();
			
			function checkNewLocal() {
				var detalhes = $('#detalhes-movimentacao');

				if(local.val() != local_atual && local.val() != ''){
					detalhes.slideDown();
				}
				else if(detalhes.is(':visible')) {
					detalhes.find('input[type="text"]').each(function(){
						$(this).val('').removeClass('ok');
					})
					detalhes.slideUp();
				}
			}

			$(document).ready(function(){
				target = $("#item-local").closest('.row');
				$('<?= $ob_result ?>').insertAfter(target);

				setInterval(function(){
					checkNewLocal();
				}, 500);

				$(document).on('focus', 'input[name="movimentacao_responsavel"]', function(){
					$(this).autocomplete({
						source: function(request, response){
							$.get("ajax-requisitantes.php", {term:request.term}, function(data){
								response($.map(data, function(item) {
									return {
										label: item.nome,
										nome: item.nome,
										email: item.email,
										value: item.id
									}
								}))
							}, "json");
						},
						minLength: 3,
						dataType: "json",
						cache: false,
						focus: function(event, ui) {
							return false;
						},
						delay: 1,
						select: function(event, ui) {
							if(ui.item.value != '-1'){
								$(this).val(ui.item.nome).attr('readonly', 'readonly').removeClass('error').addClass('ok').closest('.row').find('.nome-clearer').removeClass('disabled');
							}
							return false;
						}
					});
				})

				$(document).on('click', '.nome-clearer:not(.disabled)', function(){
					$(this).addClass('disabled');
					$('input[name="movimentacao_responsavel"]').removeClass('ok').removeAttr('readonly').val('').focus();;
				})

			}) //doc.ready
		</script>	
		<?
	}
	$ob_result = ob_get_clean();
	return $ob_result;
}

function validation_equipamento($tipo, $obj)
{
	ob_start();
	?>
	<script>
		$(document).on('submit', '#module-equipamento form', function(){
			if(
				$.trim($('input[name="codigo"]').val()) == '' &&
				$.trim($('input[name="patrimonio"]').val()) == '' &&
				$.trim($('input[name="ai"]').val()) == ''
				){
				alert("Erro: Você precisa preencher um destes campos:\n\n- Código\n- Patrimônio\n- Equipamento");
				return false;
			}			
		});
	</script>
	<?
	return ob_get_clean();
}

?>
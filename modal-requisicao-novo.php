<? require_once("lib/includes.php"); ?>
<?
	if(!hasPermission('gerenciar-servicos'))
	{
		criticalError('Você não tem permissão para gerenciar serviços');
	}

	$req = new requisicao("WHERE id = '".dboescape($_GET['id_requisicao'])."' AND inativo = 0");
	if(!$req->size())
	{
		criticalError("Requisição não existe ou foi deletada");
	}

	$definir_feedback = hasPermission('definir-dias-feedback');

	$serv = new requisicao_item("WHERE requisicao = '".$req->id."' AND inativo = 0 AND id = '".dboescape($_GET['id_servico'])."' ORDER BY numero");
	$local = $serv->_local->getSmartLocal();
	$ids_servidores_atribuidos = array_filter((array)$serv->getServidoresAtribuidos());
	$ids_prestadores_atribuidos = array_filter((array)$serv->getPrestadoresAtribuidos());
?>
<div id="modal-requisicao">
	<form action="<?= secureUrl('ajax-modal-requisicao.php?servico_id='.$serv->id.'&action=salvar-alteracoes') ?>" method="post" id="form-modal-requisicao">
		<div class="row full">
		
			<div class="large-9 columns">
				<div class="row">
					<div class="large-1 columns">
						<div class="row collapse">
							<div class="small-6 columns show-for-small">
								<h3 class="no-margin-for-small <?= ((strlen($local) > 75)?('top-minus-18'):('')) ?>" style="font-size: 45px;">
									<div class="trigger-small-toggle" style="color: #ccc;">
										<i class="fa fa-eye icon-show"></i>
										<i class="fa fa-eye-slash icon-hide"></i>
									</div>
								</h3>
							</div>
							<div class="small-6 columns" id="modal-requisicao-numero"><h3 class="no-margin-for-small text-right <?= ((strlen($local) > 75)?('top-minus-18'):('')) ?>" style="font-size: 45px;"><?= $req->id ?>/<?= $serv->numero ?></h3></div>
						</div>
					</div>
					<div class="large-11 columns text-right">
						<p class="font-14 no-margin-for-small">
							<span class="help" title="Data de abertura da requisição"><?= date("d/m H:i", strtotime($req->data)) ?> (<?= ago($req->data) ?>) <i class="fa fa-fw fa-clock-o"></i></span><br />
							<span class="help" title='Requisitante'><?= $req->nome_requisitante ?></span> <span class="help" title='Telefone'>(<?= $req->telefone_requisitante ?>)</span> <i class="fa fa-fw fa-phone"></i><br />
							<span title="Local do serviço" class="help"><?= $local; ?> <i class="fa fa-fw fa-map-marker"></i></span>
						</p>
					</div>
				</div>
				<div class="row">
					<div class="large-12 columns">
						<hr>
						<p>
							<strong><?= $serv->_tipo_servico->nome ?></strong> - <?= $serv->descricao ?>
							<?php
								if(strlen(trim($serv->anexo)))
								{
									?><br /><a href="<?= getDownloadUrl($serv->anexo) ?>" class="radius no-margin"><i class="fa fa-cloud-download"></i> <span class="underline">Baixar anexo</span></a><?php
								}
							?>	
						</p>
						<hr>
						<?
							if($serv->finalizado_servidor == 1)
							{
								?>
								<div class="row">
									<div class="large-12 columns">
										<div class="panel warning-border" style="border-width: 2px;">
											<div class="row">
												<div class="large-9 columns"><p class="no-margin text-center text-left-for-medium-up">O serviço foi finalizado pelo responsável. Deseja Reabrir?</p></div>
												<div class="large-3 columns text-center text-right-for-medium-up">
													<p class="no-margin-for-small">
														<input type='radio' name='finalizado_servidor' value="0" id="finalizado_servidor-0" class="no-margin-for-small" <?= (($serv->finalizado_servidor == 0)?('checked'):('')) ?> /><label for="finalizado_servidor-0" style="font-size: 16px; text-transform: none;">Sim</label> 
														<input type='radio' name='finalizado_servidor' value="1" id="finalizado_servidor-1" class="no-margin-for-small" <?= (($serv->finalizado_servidor == 1)?('checked'):('')) ?> /><label for="finalizado_servidor-1" style="font-size: 16px; text-transform: none;">Não</label>
													</p>
												</div>
											</div>
										</div>							
									</div>
								</div>
								<?
							}
						?>
						<div class="row small-toggle">
							<div class="large-4 columns">
								<label for=""><i class="fa fa-sitemap font-14"></i> Tipo de serviço</label>
								<select name="tipo_servico">
									<?= $serv->getOptionsServicos($serv->tipo_servico) ?>
								</select>
							</div>
							<div class="large-8 columns">
								<div id="modal-requisicao-wrapper-local" class="item-local" style="width: 100%;">
									<div class="row collapse">
										<div class="small-6 columns"><label for=""><i class="fa fa-map-marker font-14"></i> Local</label></div>
										<div class="small-6 columns text-right"><label for=""><a href="#" class="trigger-alterar-local" data-target="#modal-requisicao-local_aux"><i class="fa fa-pencil font-14"></i> Alterar local</a></label></div>
									</div>
									<input type="text" name="local_aux" class="localpick" readonly id="modal-requisicao-local_aux" value="<?= $local ?>" data-target="#modal-requisicao-local" data-readonly="true"/>
									<input type="hidden" name="local" id="modal-requisicao-local" value="<?= $serv->_local->id ?>"/>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="large-4 columns">
								<label for=""><i class="fa fa-asterisk font-14"></i> Status</label>
								<select name="status" data-last-value="<?= $serv->status ?>" id="modal-requisicao-status">
									<?= $serv->getMulti('status', 'select', $serv->status) ?>
								</select>
							</div>
							<div class="large-4 columns">
								<label for=""><i class="fa fa-exclamation-triangle font-14"></i> Prioridade</label>
								<select name="prioridade" data-last-value="<?= $serv->prioridade ?>">
									<?= $serv->getMulti('prioridade', 'select', $serv->prioridade) ?>
								</select>
							</div>
							<div class="large-4 columns small-toggle">
								<label for=""><i class="fa fa-calendar font-14"></i> Agendado para</label>
								<input type="text" class="datepick" name="data_agendada" value="<?= (($serv->data_agendada)?(date('d/m/Y', strtotime($serv->data_agendada))):('')) ?>" placeholder='Selecione...' mask="99/99/9999"/>
							</div>
						</div>
		
						<div class="row">
							<div class="large-8 columns">
								<div id="wrapper-modal-requisicao-comentario" style="width: 100% !important;">
									<label for=""><i class="fa fa-comment font-14"></i> Comentário para o Histórico</label>
									<textarea name="comentario" id="modal-requisicao-comentario"></textarea>
								</div>
							</div>
							<div class="large-4 columns" id="modal-requisicao-wrapper-enviar_email">
								<label for="" class="help" title="Deseja enviar um e-mail ao requisitante informado esta atualização no serviço?"><i class="fa fa-envelope"></i> Avisar o requisitante?</label>
								<div class="row dbo-switch" id="switch-aviso">
									<div class="small-6 columns">
										<input type="radio" style="display: none;" name="enviar_email" id="enviar-email-1" value="1" data-color="primary"/><label for="enviar-email-1" class="margin-bottom">Sim</label>
									</div>
									<div class="small-6 columns">
										<input type="radio" style="display: none;" name="enviar_email" id="enviar-email-0" value="0" data-color="alert"/><label for="enviar-email-0" class="margin-bottom">Não</label>
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="large-8 columns">
								<section id="equipamentos-relacionados">
									<div class="row">
										<div class="large-12 columns">
											<label for=""><i class="fa-tag"></i> Equipamentos relacionados &nbsp; <a href="javascript:void(null);" onClick="triggerProcurarEquipamentoRelacionado();"><i class="fa-search"></i> Procurar</a></label>
											<div id="wrapper-tabela-equipamentos-relacionados" class="margin-bottom">
												<?= $serv->getTabelaEquipamentosRelacionados() ?>
											</div>
										</div>
									</div>
								</section>
							</div>
							<div class="large-4 columns">
								<section id="responsaveis-atribuidos" class="hide-for-small">
									<div class="row">
										<div class="large-12 columns">
											<label for=""><i class="fa fa-user"></i> Responsáveis atribuídos</label>
											<div class="tags">
												<span class="tag-servidor tag-nenhum" style="<?= ((sizeof($ids_servidores_atribuidos) || sizeof($ids_prestadores_atribuidos))?('display: none;'):('')) ?>">Nenhum responsável atribuído</span>
												<?
													foreach($global_ids_servidores as $id)
													{
														$pes = new pessoa($id);
														?>
														<input type="checkbox" name="responsaveis_atribuidos[]" <?= ((in_array($pes->id, $ids_servidores_atribuidos))?('checked'):('')) ?> value="<?= $pes->id ?>"/><label style="cursor: help;" title="Utilize a caixa preta ao lado para remover/adicionar responsáveis." class="tag-servidor" ><?= $pes->getShortName() ?></label>
														<?
													}
													foreach($global_ids_prestadores as $id)
													{
														$pes = new pessoa($id);
														?>
														<input type="checkbox" name="responsaveis_atribuidos[]" <?= ((in_array($pes->id, $ids_prestadores_atribuidos))?('checked'):('')) ?> value="<?= $pes->id ?>"/><label style="cursor: help;" title="Utilize a caixa preta ao lado para remover/adicionar responsáveis." class="tag-prestador"><?= $pes->getShortName() ?></label>
														<?
													}
												?>
											</div>
										</div>
									</div>
								</section>
							</div>
						</div>

						<div class="row show-for-small">
							<div class="large-12 columns">
								<label for=""><i class="fa fa-user"></i> Responsáveis atribuídos</label>
								<div id="responsaveis-atribuidos-small"></div> 
							</div>
						</div>

						<section id="administrativo" class="peixe-section <?= ((strlen(trim($serv->observacao)) || strlen(trim($serv->data_agendada_conclusao)))?('open'):('closed')) ?> small-toggle">
							<div class="row">
								<div class="large-12 columns">
									<h4 class="trigger-peixe-section"><i class="fa fa-chevron-up icon-closed"></i><i class="fa fa-chevron-down icon-open"></i> Controle interno</h4>
								</div>
							</div>
							<div class="row peixe-section-content">
								<div class="large-8 columns">
									<label for=""><i class="fa fa-key font-14"></i> Observação da Seção</label>
									<textarea name="observacao" placeholder="Invisível para o usuário..." value="" id="requisicao-observacao"><?= htmlSpecialChars($serv->observacao) ?></textarea>
								</div>
								<div class="large-4 columns">
									<label for="" class="help" title="Número máximo de dias que a requisição pode ficar parada, sem ao menos um feedback para o usuário. Digite 0 (zero) para não monitorar."><i class="fa fa-refresh font-14"></i> Feedback</label>
									<div class="row collapse">
										<div class="large-8 small-8 columns">
										<?
											if($definir_feedback)
											{
												?>
												<input type="text" name="dias_feedback" value="<?= $serv->dias_feedback ?>" class="text-right"/>
												<?
											}
											else
											{
												?>
												<span class="prefix text-right"><?= intval($serv->dias_feedback) ?></span>
												<?
											}
										?>
										</div>
										<div class="large-4 small-4 columns"><span class="postfix">dias</span></div>
									</div>
									<label for=""><i class="fa fa-clock-o"></i> Agendar conclusão <span class="has-tip tip-top" data-tooltip title="Escolha um dia e hora para que este chamado seja concluído automaticamente pelo sistema."><i class="fa-question-circle"></i></span></label>
									<input type="text" name="data_agendada_conclusao" class="datetimepick" id="" value="<?= (($serv->data_agendada_conclusao)?(dboDate('d/m/Y H:i', strtotime($serv->data_agendada_conclusao))):('')) ?>"/>
								</div>
							</div>
						</section>

						<div class="small-toggle">
							<section id="materiais-utilizados" class="peixe-section <?= (($serv->temMateriaisAtribuidos())?('open'):('closed')) ?>">
								<div class="row">
									<div class="large-12 columns">
										<h4 class="trigger-peixe-section"><i class="fa fa-chevron-up icon-closed"></i><i class="fa fa-chevron-down icon-open"></i> Materiais e custos</h4>
									</div>
								</div>
								<div class="peixe-section-content">
									<div class="row">
										<div class="large-<?= ((!hasPermission('atribuir-estoque') && !hasPermission('atribuir-inventario'))?('12'):('8')) ?> columns">
											<div id="wrapper-tabela-materiais-utilizados">
												<?= $serv->getTabelaMateriaisUtilizados(); ?>
											</div>
										</div>
										<div class="large-4 columns">
											<div id="wrapper-controles-estoque" style="padding-top: 12px;">
												<?
													if(hasPermission('atribuir-inventario'))
													{
														?>
														<div class="row">
															<div class="large-12 columns">
																<label for="">Equipamento de inventário <span class="has-tip tip-top" title="Se este equipamento faz parte de seu inventário monitorado, digite aqui o Código, Patrimônio ou AI." data-tooltip><i class="fa-question-circle"></i></span></label>
																<input type="text" name="estoque_equipamento_aux" id="estoque_equipamento_aux" value="" placeholder="digite o código, patrim., AI..."/>
																<input type="hidden" name="estoque_equipamento" id="estoque_equipamento" value=""/>
															</div>
														</div>
														<?
													}

													if(hasPermission('atribuir-estoque') || hasPermission('atribuir-inventario'))
													{
														?>
														<div class="row">
															<div class="large-12 columns">
																<label for="">Material de estoque</label>
																<input type="text" name="estoque_material_aux" id="estoque_material_aux" value="" placeholder="digite o nome do material..."/>
																<input type="hidden" name="estoque_material" id="estoque_material" value=""/>
															</div>
														</div>
														
														<div class="row">
															<div class="large-12 columns">
																<label for="">Qtd. utilizada</label>
																<div class="row collapse">
																	<div class="large-8 columns">
																		<input type="number" name="estoque_quantidade" id="estoque_quantidade" value="" class="text-right"/>
																	</div>
																	<div class="large-4 columns hide-for-small">
																		<span class="postfix" id="estoque_unidade_aux"></span>
																	</div>
																</div>
															</div>
														</div>
														
														<div class="row">
															<div class="large-12 columns">
																<span class="button radius expand trigger-atribuir-material" data-url="<?= secureUrl('ajax-servico-estoque.php?action=atribuir-material&requisicao_item_id='.$serv->id.'&'.CSRFVar()) ?>"><i class="fa-arrow-left"></i> Atribuir ao serviço</span>
															</div>
														</div>
														<?
													}
												?>
											</div>
										</div>
									</div>
								</div>
							</section>
						</div>
		
						<div class="row small-toggle">
							<div class="large-12 columns">
								<?
									$hist = new historico("WHERE requisicao_item = '".$serv->id."' ORDER BY data");
									if($hist->size() > 1)
									{
									?>
										<section class="open peixe-section" id="section-historico">
											<h4 class="trigger-peixe-section"><i class="fa fa-chevron-up icon-closed"></i><i class="fa fa-chevron-down icon-open"></i> Histórico do serviço</h4>
											<div class="peixe-section-content">
												<table id="historico-<?= $serv->id ?>" class="list-historico responsive">
													<thead>
														<tr>
															<th>Data</th>
															<th>Prior.</th>
															<th>Status</th>
															<th>Comentário</th>
														</tr>
													</thead>
													<tbody>
														<?
															$prioridade_anterior = '';
															$status_anterior = '';
															do {
																$pes = new servidor($hist->created_by);
																?>
																<tr title='<?= $pes->nome ?>'>
																	<td data-title="Data" style="white-space: nowrap;"><span class="help" title="<?= date('d/m/Y H:i', strtotime($hist->data)) ?>"><?= ago($hist->data) ?></span></td>
																	<td data-title="Prioridade"><span class="<?= (($hist->prioridade == $prioridade_anterior)?('text-light'):('')) ?>"><?= $serv->getValue('prioridade', $hist->prioridade) ?></span></td>
																	<td style='white-space: nowrap;' data-title="Status"><span class="<?= (($hist->status == $status_anterior)?('text-light'):('')) ?>"><?= $serv->getValue('status', $hist->status) ?></span></td>
																	<td data-title="Comentário">
																		<span style="word-wrap: break-word;">
																			<?= $hist->getTagsAtribuicoes(); ?>
																			<?= $hist->comentario ?>
																		</span>
																	</td>
																</tr>
																<?
																$prioridade_anterior = $hist->prioridade;
																$status_anterior = $hist->status;
															}while($hist->fetch());
														?>
													</tbody>
												</table>
											</div>
										</section>
									<?
									}
								?>
							</div>
						</div>

					</div>
				</div>
			</div>
			<div class="large-3 columns">
				<div id="requisicao-sidebar-container" >
					<div class="row" id="requisicao-sidebar">
						<div class="large-12 columns">
							<div class="wrapper-submit-buttons">
								<ul class="small-block-grid-2">
									<li><button class="button radius expand"><p class="no-margin-for-small"><i class="fa fa-check"></i><br />Salvar</p></button></li>
									<li><button class="button radius secondary expand" onClick="closeModalRequisicao(); return false;"><p class="no-margin-for-small"><i class="fa fa-close"></i><br />Fechar</p></button></li>
								</ul>
							</div>
						</div>
						<div class="large-12 columns hide-for-small">
							<div class="wrapper-lista-responsaveis">
								<div class="lista-responsaveis" style="margin-bottom: 1em;">
									<div class="text-center margin-bottom-for-small seletor-responsaveis">
										<span class="active" data-show=".lista-servidores" data-hide=".lista-prestadores">Serv.</span>
										<span data-hide=".lista-servidores" data-show=".lista-prestadores">Prest. Serv.</span>
									</div>
									<ul class="small-block-grid-4 large-block-grid-2 lista-servidores">
										<?
											$serv = new servidor("WHERE id IN (".implode(',', getIdsServidores()).") ORDER BY nome");
											if($serv->size())
											{
												do {
													?>
													<li class="<?= ((in_array($serv->id, $ids_servidores_atribuidos))?('active'):('')) ?>"><?= $serv->getBadge(); ?></li>
													<?
												}while($serv->fetch());
											}
										?>
									</ul>
									<ul class="small-block-grid-2 large-block-grid-1 lista-prestadores" style="display: none;">
										<?
											$serv = new servidor("WHERE id IN (".implode(',', getIdsPrestadores()).") ORDER BY apelido");
											if($serv->size())
											{
												do {
													?>
													<li class="<?= ((in_array($serv->id, $ids_prestadores_atribuidos))?('active'):('')) ?>"><?= $serv->getBadge('small'); ?></li>
													<?
												}while($serv->fetch());
											}
										?>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
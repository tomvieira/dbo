<?

/* ================================================================================================================== */
/* DBO CLASS FILE FOR MODULE 'requisicao_item' ================================== AUTO-CREATED ON 11/04/2013 09:47:01 */
/* ================================================================================================================== */

/* IMPORTANT: This file is generated only in the first DBO sync, what means you should edit only via text editor. */

if(!class_exists('requisicao_item'))
{
	class requisicao_item extends dbo
	{
		/* smart constructor: will perform load() upon numeric argument and loadAll() upon string argument */
		function __construct($foo = '')
		{
			parent::__construct(get_class($this));
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

		var $novos_chamados = array();

		function getMulti($field, $type = 'select', $active = '-1')
		{
			$source = $this->__module_scheme->campo[$field]->valores;
			$return = '';
			if(is_array($source))
			{
				foreach($source as $key => $value)
				{
					if($type == 'select')
					{
						$return .= "<option value=\"".$key."\" ".(($active == $key)?('selected'):('')).">".$value."</option>\n";
					}
				}
			}
			return $return;
		}

		function getValue($field, $active)
		{
			$source = $this->__module_scheme->campo[$field]->valores;
			if(is_array($source))
			{
				return $source[$active];
			}
		}

		function getOptionsServicos($active = '-1')
		{
			$ser = new tipo_servico("WHERE inativo = 0 ORDER BY nome");
			if($ser->size())
			{
				do {
					$return .= "<option value='".$ser->id."' ".(($ser->id == $active)?('selected'):('')).">".$ser->nome."</option>";
				}while($ser->fetch());
			}
			return $return;
		}

		function getPainelList($params = array())
		{
			global $_STATUS_NAMES;
			global $_pes;
			global $_pessoa;
			global $_local;
			global $_system;

			$uni = new unidade();

			$params = (array)$params;

			/* verificando e definindo status */

			if($params[status] == 'novos')
			{
				$sql_status = "0,2";
				$columns = array(
					'prioridade',
					'data',
					'requisitante',
					'tipo-servico',
					'servico',
					'local'
				);
			}
			elseif($params[status] == 'atribuidos')
			{
				$sql_status = "3,5";
				$columns = array(
					'prioridade',
					'data',
					'requisitante',
					'servico',
					'local',
					'servidores'
				);
			}
			elseif($params[status] == 'aguardando')
			{
				$sql_status = "1,4";
				$columns = array(
					'prioridade',
					'data',
					'tempo-aguardando',
					'requisitante',
					'servico',
					'local',
					'historico',
					'servidores'
				);
			}
			elseif($params[status] == 'feedbacks-atrasados')
			{
				$sql_status = "1,4";
				$columns = array(
					'prioridade',
					'data',
					'dias-sem-feedback',
					'servico',
					'local',
					'ultimo-historico',
					'requisitante'
				);
			}
			elseif($params[status] == 'encerrados')
			{
				$sql_status = "6,7,8,9";
				$columns = array(
					'requisitante',
					'servico',
					'local'
				);
			}
			elseif($params[status] == 'todos')
			{
				$columns = array(
					'data',
					'requisitante',
					'tipo-servico',
					'servico',
					'local'
				);
			}

			if(pessoaHasNivelTecnico($_pes->id))
			{
				$columns[] = 'unidade';
			}

			/* termos especificos da busca */
			if(strlen($params['term']))
			{
				
				/* inicializando flags de busca */
				$id_requisicao = '';
				$id_requisicao_item = '';

				$terms = explode(" ", trim($params['term']));

				foreach($terms as $key => $term)
				{
					$term = trim($term);

					/* descobrindo se a pessoa digitou um numero completo de requisicao */
					if ($c = preg_match_all("/(\\d+)\\/(\\d+)/is", $term, $matches))
					{
						$id_requisicao = $matches[1][0];
						$numero_requisicao_item = $matches[2][0];
						$sql_id_parts[] = " (r.id = '".$id_requisicao."' AND ri.numero = '".$numero_requisicao_item."') ";
					}
					/* descobrindo se a pessoa digitou somente o numero da requisicao */
					elseif($c = preg_match_all("/(\\d+)/is", $term, $matches))
					{
						$id_requisicao = $matches[1][0];
						$sql_id_parts[] = " (r.id = '".$id_requisicao."') ";
					}
					/* string normal */
					else
					{
						$sql_string_parts[] = "(
							ri.descricao LIKE '%".$term."%' OR
							r.nome_requisitante LIKE '%".$term."%' OR
							r.email_requisitante LIKE '%".$term."%' OR
							ts.nome LIKE '%".$term."%' OR
							cs.nome LIKE '%".$term."%' OR
							l.nome LIKE '%".$term."%' OR
							l.sigla LIKE '%".$term."%' OR
							l.nome_alternativo LIKE '%".$term."%' OR
							l2.nome LIKE '%".$term."%' OR
							l2.sigla LIKE '%".$term."%' OR
							l2.nome_alternativo LIKE '%".$term."%'
						)";
					}
				}

			}

			$sql = "
				SELECT 
					ri.*,
					uni.sigla AS sigla_unidade
				FROM
					requisicao r,
					requisicao_item ri,
					tipo_servico ts,
					categoria_servico cs,
					".$uni->getTable()." uni,
					".$_pessoa->getTable()." p,
					".$_local->getTable()." l
				LEFT JOIN ".$_local->getTable()." l2 ON 
					l.pai = l2.id
				WHERE
					(
						r.unidade = '".sistema::getUnidadeAtiva()."'
						".(pessoaHasNivelTecnico($_pes->id, 2) ? " OR (ri.nivel_tecnico = 2 AND ri.tipo_servico IN (".implode(',', getPessoaNiveisTecnicos($_pes->id)[2])."))" : '')."
						".(pessoaHasNivelTecnico($_pes->id, 3) ? " OR (ri.nivel_tecnico = 3 AND ri.tipo_servico IN (".implode(',', getPessoaNiveisTecnicos($_pes->id)[3])."))" : '')."
					) AND
					r.unidade = uni.id AND
					ri.requisicao = r.id AND
					ri.tipo_servico = ts.id AND
					ts.categoria_servico = cs.id AND
					ri.created_by = p.id AND
					ri.local = l.id AND
					".(($_system['contexto_areas'])?(" cs.id IN(".implode(",", $_system['contexto_areas']).") AND "):(''))."
					".((sizeof($sql_id_parts))?(" ( ".implode(" OR ", $sql_id_parts)." ) AND "):(""))."
					".((sizeof($sql_string_parts))?(" ( ".implode(" AND ", $sql_string_parts)." ) AND "):(""))."
					".((strlen($sql_status))?(" ri.status IN (".$sql_status.") AND "):(""))."
					ri.inativo = 0
				ORDER BY 
					".((in_array('prioridade', $columns))?(" ri.prioridade, "):(''))."
					ri.created_on, 
					ri.requisicao, 
					ri.numero
			";

			//pega as requisicoes de acordo com os status
			if(
				($params[status] == 'encerrados' || $params[status] == 'todos') && 
				(!strlen(trim($params['term'])) && !$params[show_all])
			)
			{
				?>
				<div class="row">
					<div class="large-12 columns text-right">
						<div class="helper arrow-top text-center" style="max-width: 350px;">
							Esta aba retorna muitos resultados. Faça uma busca mais específica.<br /><br />Se preferir ver todos os resultados, <a href="#" class="trigger-show-all" data-status="<?= $params[status] ?>">clique aqui</a>.<br />(a consulta pode demorar)
						</div>
					</div><!-- col -->
				</div><!-- row -->
				
				<?
			}
			else
			{
				$item = new requisicao_item();
				if($params[status] == 'feedbacks-atrasados')
				{
					$item->getFeedbacksAtrasados();
				}
				else
				{
					$item->query($sql);
				}
				if($item->size())
				{
					?>
					<table id='painel-requisicoes' class="responsive list">
						<thead>
							<tr>
								<th style='width: 1%'><span class='help' title='Número da Requisição / Item'>Nº</span></th>
								<th style='width: 1%' class="text-center"><span title='Status' class="help">S</span></th>
								<?= ((in_array('prioridade', $columns))?("<th style='width: 1%' class='text-center'><span class='help' title='Prioridade'>P</span></th>"):('')) ?>
								<?= ((in_array('data', $columns))?("<th><span class='help' title='Data de abertura do serviço'>Aberto</span></th>"):('')) ?>
								<?= ((in_array('dias-sem-feedback', $columns))?("<th><span class='help' title='Quantidade de dias que o requisitante está sem feedback sobre este serviço'>Sem feedback</span></th>"):('')) ?>
								<?= ((in_array('tempo-aguardando', $columns))?("<th>Aguardando</th>"):('')) ?>
								<?= ((in_array('requisitante', $columns))?("<th>Requisitante</th>"):('')) ?>
								<?= ((in_array('tipo-servico', $columns))?("<th>Categoria</th>"):('')) ?>
								<?= ((in_array('servico', $columns))?("<th>Serviço</th>"):('')) ?>
								<?= ((in_array('descricao', $columns))?("<th>Descrição</th>"):('')) ?>
								<?= ((in_array('local', $columns))?("<th>Local</th>"):('')) ?>
								<?= ((in_array('historico', $columns))?("<th>Histórico</th>"):('')) ?>
								<?= ((in_array('ultimo-historico', $columns))?("<th>Histórico</th>"):('')) ?>
								<?= ((in_array('servidores', $columns))?("<th>Responsáveis</th>"):('')) ?>
								<?= ((in_array('unidade', $columns))?("<th>Unidade</th>"):('')) ?>
							</tr>
						</thead>
						<tbody>
						<?
							$total_status = array();
							do {
								$total_status[$item->status]++;
								$req = new requisicao($item->requisicao);
								$loc = new local($item->local);
								?>
								<tr onClick="gerenciarRequisicao(<?= $item->requisicao ?>, <?= $item->id ?>)" data-status="<?= $item->status ?>" class="pointer">
									<td data-title='Número'><?= $item->requisicao ?>/<?= $item->numero ?></td>
									<td data-title='Status'>
									<?
										if($item->finalizado_servidor == 1 && $item->status == STATUS_EM_ANDAMENTO)
										{
											?>
											<span class="radius tag-status status-alert help" title="Finalizado pelo servidor">Finalizado</span>
											<?
										}
										else
										{
											?>
											<span class="radius tag-status status-<?= $item->status ?>" title='<?= $item->getValue('status', $item->status) ?>'><?= (($params[status] == 'aguardando')?(substr($item->getValue('status', $item->status), 0, 2)):($item->getValue('status', $item->status))) ?></span>
											<?
										}
									?>
									</td>
									<?
										if(in_array('prioridade', $columns))
										{
											?>
											<td data-title="Prioridade"><span class="radius tag-prioridade help prioridade-<?= $item->prioridade ?>" title='<?= $item->getValue('prioridade', $item->prioridade) ?>'><?= substr($item->getValue('prioridade', $item->prioridade), 0, 1) ?></span></td>
											<?
										}
										if(in_array('data', $columns))
										{
											?>
											<td data-title='Aberto'><span class="help" title="<?= date("d/m/Y H:i", strtotime($req->data)) ?>"><?= ago($req->data) ?></span></td>
											<?
										}
										if(in_array('dias-sem-feedback', $columns))
										{
											?>
											<td data-title="Sem feedback">há <?= $item->dias_passados ?> dias</span></td>
											<?
										}
										if(in_array('tempo-aguardando', $columns))
										{
											$historico = $item->getLastHistorico($item->status);
											?>
											<td data-title="Aguardando"><span class="help" title="<?= date("d/m/Y H:i", strtotime($historico->data)) ?>"><?= ago($historico->data) ?></span></td>
											<?
										}
										if(in_array('requisitante', $columns))
										{
											?>
											<td data-title='Requisitante'><?= $req->nome_requisitante ?></td>
											<?
										}
										if(in_array('tipo-servico', $columns))
										{
											?>
											<td data-title='Categoria'><?= $item->___tipo_servico___categoria_servico___nome ?></td>
											<?
										}
										if(in_array('servico', $columns))
										{
											?>
											<td style='white-space: no-wrap;' data-title='Serviço'><?= $item->___tipo_servico___nome ?></td>
											<?
										}
										if(in_array('descricao', $columns))
										{
											?>
											<td data-title="Descrição"><?= $item->descricao ?></td>
											<?
										}
										if(in_array('local', $columns))
										{
											?>
											<td data-title='Local'><?= $loc->getSmartLocal(); ?></td>
											<?
										}
										if(in_array('historico', $columns))
										{
											//historico instanciado no tempo-aguardando
											?>
											<td data-title='Histórico'><?= ((strlen(trim($historico->comentario)))?($historico->comentario):('-')); ?></td>
											<?
										}
										if(in_array('ultimo-historico', $columns))
										{
											//historico instanciado no tempo-aguardando
											?>
											<td data-title='Histórico'><?= ((strlen(trim($historico->comentario)))?($historico->comentario):('-')); ?></td>
											<?
										}
										if(in_array('servidores', $columns))
										{
											?>
											<td data-title='Responsáveis'>
												<?
													$servidores = $item->getNomesServidoresAtribuidos();
													if(is_array($servidores))
													{
														foreach($servidores as $key => $value)
														{
															?>
															<span class="tag-servidor help" title='Servidor da Unidade' style='margin-top: 1px;'><?= $value ?></span>
															<?
														}
													}
													$prestadores = $item->getNomesPrestadoresAtribuidos();
													if(is_array($prestadores))
													{
														foreach($prestadores as $key => $value)
														{
															?>
															<span class="tag-prestador help" title='Prestador de Serviço' style='margin-top: 1px;'><?= $value ?></span>
															<?
														}
													}
												?>
											</td>
											<?
										}
										if(in_array('unidade', $columns))
										{
											?>
											<td><span class="label secondary alt radius color medium"><?= $item->sigla_unidade ?></span></td>
											<?php
										}
									?>
								</tr>
								<?
							}while($item->fetch());
						?>
						</tbody>
					</table>
					<div class="list-info hide-for-small radius">
					<?
						if(is_array($total_status))
						{
							ksort($total_status);
							foreach($total_status as $key => $value)
							{
								?>
								<div class="tag-status status-<?= $key ?> status-filter radius" data-status='<?= $key ?>'>
									<span class="nome-status"><?= $_STATUS_NAMES[$key] ?></span><span class="valor"><?= $value ?></span>
								</div>
								<?
							}
							if(sizeof($total_status) > 1)
							{
								?>
								<div class="tag-status status-total status-filter radius" data-status='total'>
									<span class="nome-status">Total</span><span class="valor"><?= array_sum($total_status) ?></span>
								</div>
								<?
							}
						}
					?>
					</div>
					<?
				}
				else
				{
					?><div class="text-center" style='padding-top: 100px;'>Não há serviços referentes a esta busca</div><?
				}
			}
		} //getPainelList

		function getSmartHistoryDate($status, $datas)
		{
			if(strlen($datas[$status]))
			{
				return $datas[$status];
			}
			else
			{
				if($status == STATUS_APROVADO)
				{
					if(strlen($datas[STATUS_ATRIBUIDO]))
					{
						return $datas[STATUS_ATRIBUIDO];
					}
					elseif(strlen($datas[STATUS_CONCLUIDO]))
					{
						return $datas[STATUS_CONCLUIDO];
					}
					return false;
				}	
				return false;
			}
		}

		function getStatusChart()
		{

			global $_STATUS_NAMES;

			/* antes de mais nada, precisamos pegar todos os status de historico que ocorreram com esta requisição. */
			$hist = new historico("WHERE requisicao_item  = '".addslashes($this->id)."' ORDER BY id");

			/* criamos um vetor com todos as datas possiveis para todos os estados relacionados a cada status, priorizando sempre pela data mais recente */
			$datas = array();
			do {
				$datas[$hist->status] = $hist->data;
			}while($hist->fetch());

			/* o diagrama é composto por 4 datas, variando de acordo com cadas status vamos checar por prioridades de status. */

			if($this->status == STATUS_AVALIADO) // 9
			{
				$status_atual = STATUS_AVALIADO;

				$cor_1 = 'ok';
				$cor_2 = 'ok';
				$cor_3 = 'ok';
				$cor_4 = 'ok';

				$status_1 = STATUS_NOVO;
				$status_2 = STATUS_APROVADO;
				$status_3 = STATUS_CONCLUIDO;
				$status_4 = STATUS_AVALIADO;

				$data_1 = (($this->getSmartHistoryDate($status_1, $datas))?(date('d/m', strtotime($this->getSmartHistoryDate($status_1, $datas)))):(date('d/m', strtotime($datas[$status_atual]))));
				$data_2 = (($this->getSmartHistoryDate($status_2, $datas))?(date('d/m', strtotime($this->getSmartHistoryDate($status_2, $datas)))):(date('d/m', strtotime($datas[$status_atual]))));
				$data_3 = (($this->getSmartHistoryDate($status_3, $datas))?(date('d/m', strtotime($this->getSmartHistoryDate($status_3, $datas)))):(date('d/m', strtotime($datas[$status_atual]))));
				$data_4 = (($this->getSmartHistoryDate($status_4, $datas))?(date('d/m', strtotime($this->getSmartHistoryDate($status_4, $datas)))):(date('d/m', strtotime($datas[$status_atual]))));
			}
			elseif($this->status == STATUS_CANCELADO) //8
			{
				$status_atual = STATUS_CANCELADO;

				$cor_1 = 'cancel';
				$cor_2 = 'cancel';
				$cor_3 = 'cancel';
				$cor_4 = 'cancel';

				$status_1 = STATUS_NOVO;
				$status_2 = '';
				$status_3 = '';
				$status_4 = STATUS_CANCELADO;

				$data_1 = (($this->getSmartHistoryDate($status_1, $datas))?(date('d/m', strtotime($this->getSmartHistoryDate($status_1, $datas)))):(date('d/m', strtotime($datas[$status_atual]))));
				$data_2 = '';
				$data_3 = '';
				$data_4 = (($this->getSmartHistoryDate($status_4, $datas))?(date('d/m', strtotime($this->getSmartHistoryDate($status_4, $datas)))):(date('d/m', strtotime($datas[$status_atual]))));
			}
			elseif($this->status == STATUS_NAO_APROVADO) //7
			{
				$status_atual = STATUS_NAO_APROVADO;

				$cor_1 = 'ok';
				$cor_2 = 'alert';
				$cor_3 = 'cancel';
				$cor_4 = 'cancel';

				$status_1 = STATUS_NOVO;
				$status_2 = STATUS_NAO_APROVADO;
				$status_3 = '';
				$status_4 = '';

				$data_1 = (($this->getSmartHistoryDate($status_1, $datas))?(date('d/m', strtotime($this->getSmartHistoryDate($status_1, $datas)))):(date('d/m', strtotime($datas[$status_atual]))));
				$data_2 = (($this->getSmartHistoryDate($status_2, $datas))?(date('d/m', strtotime($this->getSmartHistoryDate($status_2, $datas)))):(date('d/m', strtotime($datas[$status_atual]))));
				$data_3 = '';
				$data_4 = '';
			}
			elseif($this->status == STATUS_CONCLUIDO) //6
			{
				$status_atual = STATUS_CONCLUIDO;

				$cor_1 = 'ok';
				$cor_2 = 'ok';
				$cor_3 = 'ok';
				$cor_4 = 'warning';

				$status_1 = STATUS_NOVO;
				$status_2 = STATUS_APROVADO;
				$status_3 = STATUS_CONCLUIDO;
				$status_4 = STATUS_AVALIADO;

				$data_1 = (($this->getSmartHistoryDate($status_1, $datas))?(date('d/m', strtotime($this->getSmartHistoryDate($status_1, $datas)))):(date('d/m', strtotime($datas[$status_atual]))));
				$data_2 = (($this->getSmartHistoryDate($status_2, $datas))?(date('d/m', strtotime($this->getSmartHistoryDate($status_2, $datas)))):(date('d/m', strtotime($datas[$status_atual]))));
				$data_3 = (($this->getSmartHistoryDate($status_3, $datas))?(date('d/m', strtotime($this->getSmartHistoryDate($status_3, $datas)))):(date('d/m', strtotime($datas[$status_atual]))));
				$data_4 = '';
			}
			elseif($this->status == STATUS_EM_ANDAMENTO) //5
			{
				$status_atual = STATUS_EM_ANDAMENTO;

				$cor_1 = 'ok';
				$cor_2 = 'ok';
				$cor_3 = 'ongoing';
				$cor_4 = 'cancel';

				$status_1 = STATUS_NOVO;
				$status_2 = STATUS_APROVADO;
				$status_3 = STATUS_EM_ANDAMENTO;
				$status_4 = STATUS_AVALIADO;

				$data_1 = (($this->getSmartHistoryDate($status_1, $datas))?(date('d/m', strtotime($this->getSmartHistoryDate($status_1, $datas)))):(date('d/m', strtotime($datas[$status_atual]))));
				$data_2 = (($this->getSmartHistoryDate($status_2, $datas))?(date('d/m', strtotime($this->getSmartHistoryDate($status_2, $datas)))):(date('d/m', strtotime($datas[$status_atual]))));
				$data_3 = (($this->getSmartHistoryDate($status_3, $datas))?(date('d/m', strtotime($this->getSmartHistoryDate($status_3, $datas)))):(date('d/m', strtotime($datas[$status_atual]))));
				$data_4 = '';
			}
			elseif($this->status == STATUS_AGUARDANDO_REQUISITANTE) //4
			{
				$status_atual = STATUS_AGUARDANDO_REQUISITANTE;

				$cor_1 = 'ok';
				$cor_2 = 'ok';
				$cor_3 = 'alert';
				$cor_4 = 'cancel';

				$status_1 = STATUS_NOVO;
				$status_2 = STATUS_APROVADO;
				$status_3 = STATUS_AGUARDANDO_REQUISITANTE;
				$status_4 = STATUS_AVALIADO;

				$data_1 = (($this->getSmartHistoryDate($status_1, $datas))?(date('d/m', strtotime($this->getSmartHistoryDate($status_1, $datas)))):(date('d/m', strtotime($datas[$status_atual]))));
				$data_2 = (($this->getSmartHistoryDate($status_2, $datas))?(date('d/m', strtotime($this->getSmartHistoryDate($status_2, $datas)))):(date('d/m', strtotime($datas[$status_atual]))));
				$data_3 = (($this->getSmartHistoryDate($status_3, $datas))?(date('d/m', strtotime($this->getSmartHistoryDate($status_3, $datas)))):(date('d/m', strtotime($datas[$status_atual]))));
				$data_4 = '';
			}
			elseif($this->status == STATUS_ATRIBUIDO) //3
			{
				$status_atual = STATUS_ATRIBUIDO;

				$cor_1 = 'ok';
				$cor_2 = 'ok';
				$cor_3 = 'warning';
				$cor_4 = 'cancel';

				$status_1 = STATUS_NOVO;
				$status_2 = STATUS_APROVADO;
				$status_3 = STATUS_ATRIBUIDO;
				$status_4 = STATUS_AVALIADO;

				$data_1 = (($this->getSmartHistoryDate($status_1, $datas))?(date('d/m', strtotime($this->getSmartHistoryDate($status_1, $datas)))):(date('d/m', strtotime($datas[$status_atual]))));
				$data_2 = (($this->getSmartHistoryDate($status_2, $datas))?(date('d/m', strtotime($this->getSmartHistoryDate($status_2, $datas)))):(date('d/m', strtotime($datas[$status_atual]))));
				$data_3 = (($this->getSmartHistoryDate($status_3, $datas))?(date('d/m', strtotime($this->getSmartHistoryDate($status_3, $datas)))):(date('d/m', strtotime($datas[$status_atual]))));
				$data_4 = '';
			}
			elseif($this->status == STATUS_APROVADO) //2
			{
				$status_atual = STATUS_APROVADO;

				$cor_1 = 'ok';
				$cor_2 = 'ok';
				$cor_3 = 'cancel';
				$cor_4 = 'cancel';

				$status_1 = STATUS_NOVO;
				$status_2 = STATUS_APROVADO;
				$status_3 = STATUS_CONCLUIDO;
				$status_4 = STATUS_AVALIADO;

				$data_1 = (($this->getSmartHistoryDate($status_1, $datas))?(date('d/m', strtotime($this->getSmartHistoryDate($status_1, $datas)))):(date('d/m', strtotime($datas[$status_atual]))));
				$data_2 = (($this->getSmartHistoryDate($status_2, $datas))?(date('d/m', strtotime($this->getSmartHistoryDate($status_2, $datas)))):(date('d/m', strtotime($datas[$status_atual]))));
				$data_3 = '';
				$data_4 = '';
			}
			elseif($this->status == STATUS_AGUARDANDO_APROVACAO_DIRETORIA) //1
			{
				$status_atual = STATUS_AGUARDANDO_APROVACAO_DIRETORIA;

				$cor_1 = 'ok';
				$cor_2 = 'warning';
				$cor_3 = 'cancel';
				$cor_4 = 'cancel';

				$status_1 = STATUS_NOVO;
				$status_2 = STATUS_AGUARDANDO_APROVACAO_DIRETORIA;
				$status_3 = STATUS_CONCLUIDO;
				$status_4 = STATUS_AVALIADO;

				$data_1 = (($this->getSmartHistoryDate($status_1, $datas))?(date('d/m', strtotime($this->getSmartHistoryDate($status_1, $datas)))):(date('d/m', strtotime($datas[$status_atual]))));
				$data_2 = (($this->getSmartHistoryDate($status_2, $datas))?(date('d/m', strtotime($this->getSmartHistoryDate($status_2, $datas)))):(date('d/m', strtotime($datas[$status_atual]))));
				$data_3 = '';
				$data_4 = '';
			}
			elseif($this->status == STATUS_NOVO) //0
			{
				$status_atual = STATUS_NOVO;

				$cor_1 = 'ok';
				$cor_2 = 'cancel';
				$cor_3 = 'cancel';
				$cor_4 = 'cancel';

				$status_1 = STATUS_NOVO;
				$status_2 = STATUS_APROVADO;
				$status_3 = STATUS_CONCLUIDO;
				$status_4 = STATUS_AVALIADO;

				$data_1 = (($this->getSmartHistoryDate($status_1, $datas))?(date('d/m', strtotime($this->getSmartHistoryDate($status_1, $datas)))):(date('d/m', strtotime($datas[$status_atual]))));
				$data_2 = '';
				$data_3 = '';
				$data_4 = '';
			}
			ob_start();
			?>
			<div class="status-chart">
				<div class="state">
					<div class="shape <?= $cor_1 ?>">
						<div class="date"><?= ((strlen($data_1))?($data_1):('')) ?></div>
					</div>
					<?= ((strlen($status_1))?('<div class="info radius">'.$_STATUS_NAMES[$status_1].'</div>'):('')) ?>
				</div>
				<div class="state">
					<div class="shape <?= $cor_2 ?>">
						<div class="date"><?= ((strlen($data_2))?($data_2):('')) ?></div>
					</div>
					<?= ((strlen($status_2))?('<div class="info radius">'.$_STATUS_NAMES[$status_2].'</div>'):('')) ?>
				</div>
				<div class="state">
					<div class="shape <?= $cor_3 ?>">
						<div class="date"><?= ((strlen($data_3))?($data_3):('')) ?></div>
					</div>
					<?= ((strlen($status_3))?('<div class="info radius">'.$_STATUS_NAMES[$status_3].'</div>'):('')) ?>
				</div>
				<div class="state">
					<div class="shape <?= $cor_4 ?>">
						<div class="date"><?= ((strlen($data_4))?($data_4):('')) ?></div>
					</div>
					<?= ((strlen($status_4))?('<div class="info radius">'.$_STATUS_NAMES[$status_4].'</div>'):('')) ?>
				</div>
			</div>
			<?
			$ob_result = ob_get_clean();

			return $ob_result;
		} //getStatusChart

		function getHistorico($type = 'compact')
		{
			$hist = new historico("WHERE requisicao_item = '".addslashes($this->id)."' ORDER BY data");
			if($hist->size())
			{
				ob_start();
				?>
				<table class="historico">
					<thead>
						<tr>
							<th>Data</th>
							<?= (($type == 'complete')?('<th title="Prioridade">Prior.</th>'):('')) ?>
							<th>Status</th>
							<th>Comentário</th>
						</tr>
					</thead>
					<tbody>
						<?
							$status_anterior = '';
							$prioridade_anterior = '';
							do {
								if(
									//$type == 'compact' &&
									$hist->status == $status_anterior &&
									!strlen(trim($hist->comentario))
								)
								{
									continue;
								}
								?>
								<tr>
									<td>
										<div class="hide-for-small"><span class="help" title="<?= date('d/m/Y H:i', strtotime($hist->data)) ?>"><?= ago($hist->data) ?></span></div>
										<div class="show-for-small"><?= date('d/m', strtotime($hist->data)) ?></div>
									</td>
									<?
										if($type == 'complete')
										{
											?>
											<td><?= (($prioridade_anterior != $hist->prioridade)?($this->getValue('prioridade', $hist->prioridade)):('')) ?></td>
											<?
										}									
									?>
									<td><?= (($hist->status != $status_anterior)?($this->getValue('status', $hist->status)):('')) ?></td>
									<td><?= $hist->comentario ?></td>
								</tr>
								<?
								$status_anterior = $hist->status;
								$prioridade_anterior = $hist->prioridade;
							}while($hist->fetch());					
						?>
					</tbody>
				</table>
				<?
				$ob_result = ob_get_clean();
				return $ob_result;
			}
		}

		function getServidoresAtribuidos()
		{
			$sql = "
				SELECT 
					ris.servidor AS servidor
				FROM 
					requisicao_item_servidor ris,
					pessoa_perfil pf
				WHERE 
					ris.servidor = pf.pessoa AND
					(
						pf.perfil = ".ID_PERFIL_SERVIDOR." OR
						pf.perfil = ".ID_PERFIL_ESTAGIARIO."
					) AND
					ris.item = '".addslashes($this->id)."' ORDER BY servidor";
			$res = dboQuery($sql);
			if(dboAffectedRows())
			{
				while($lin = dboFetchObject($res))
				{
					$ids[] = $lin->servidor;
				}
				return $ids;
			}
			return false;
		}

		function getAtribuidos()
		{
			$sql = "
				SELECT 
					ris.servidor AS servidor
				FROM 
					requisicao_item_servidor ris
				WHERE 
					ris.item = '".addslashes($this->id)."' ORDER BY servidor";
			$res = dboQuery($sql);
			if(dboAffectedRows())
			{
				while($lin = dboFetchObject($res))
				{
					$ids[] = $lin->servidor;
				}
				return $ids;
			}
			return false;
		}

		function getPrestadoresAtribuidos()
		{
			$sql = "
				SELECT 
					ris.servidor AS servidor
				FROM 
					requisicao_item_servidor ris,
					pessoa_perfil pf
				WHERE 
					ris.servidor = pf.pessoa AND
					pf.perfil = ".ID_PERFIL_PRESTADOR_SERVICO." AND
					ris.item = '".addslashes($this->id)."' ORDER BY servidor";
			$res = dboQuery($sql);
			if(dboAffectedRows())
			{
				while($lin = dboFetchObject($res))
				{
					$ids[] = $lin->servidor;
				}
				return $ids;
			}
			return false;
		}

		function getNomesServidoresAtribuidos($tipo = 'short-name')
		{
			$servidores = $this->getServidoresAtribuidos();
			if($servidores)
			{
				$serv = new servidor("WHERE id IN (".implode(',', $servidores).")");
				if($serv->size())
				{
					do {
						$nomes[] = $serv->getShortName();
					}while($serv->fetch());
					return $nomes;
				}
			}
		}
		
		function getNomesPrestadoresAtribuidos($tipo = 'short-name')
		{
			$servidores = $this->getPrestadoresAtribuidos();
			if($servidores)
			{
				$serv = new servidor("WHERE id IN (".implode(',', $servidores).")");
				if($serv->size())
				{
					do {
						$nomes[] = $serv->getShortName();
					}while($serv->fetch());
					return $nomes;
				}
			}
		}
		
		function getNomesAtribuidos($tipo = 'short-name')
		{
			$servidores = $this->getAtribuidos();
			if($servidores)
			{
				$serv = new servidor("WHERE id IN (".implode(',', $servidores).")");
				if($serv->size())
				{
					do {
						$nomes[] = $serv->getShortName();
					}while($serv->fetch());
					return $nomes;
				}
			}
		}
		
		function getLastHistorico($status = false)
		{
			$hist = new historico("WHERE requisicao_item = '".addslashes($this->id)."' ".(($status)?(" AND status = '".$status."' "):(''))." ORDER BY data DESC");
			return $hist;
		}

		function createAvaliacao()
		{
			/* cria a avaliacao para o item atual, se não existir */
			$aval = new avaliacao("WHERE requisicao_item = '".addslashes($this->id)."'");
			$aval->requisicao_item = $this->id;
			$aval->created_on = $aval->now();
			$aval->respondido_em = $aval->null();
			$aval->token = generatePassword();
			$aval->saveOrUpdate();
			return $aval->id;
		}

		function getTotalStatus($status)
		{
			global $_system;
			$sql = "
				SELECT 
					DISTINCT requisicao_item.*,
					categoria_servico.nome AS nome_area
				FROM
					requisicao_item,
					tipo_servico,
					categoria_servico,
					requisicao_item_servidor
				WHERE 
					requisicao_item.tipo_servico = tipo_servico.id AND
					tipo_servico.categoria_servico = categoria_servico.id AND
					".(($_system['notificacoes_areas'] || $_system['contexto_areas'])?(
					"(
						".(($_system['notificacoes_areas'])?(" categoria_servico.id IN(".implode(",", $_system['notificacoes_areas']).") OR "):((($_system['contexto_areas'])?(" categoria_servico.id IN(".implode(",", $_system['contexto_areas']).") OR "):(''))))."
						(
							requisicao_item_servidor.item = requisicao_item.id AND
							requisicao_item_servidor.servidor = '".loggedUser()."'
						)
					) AND"
					):(''))."
					requisicao_item.status = ".$status." AND
					requisicao_item.inativo = 0
			";
			$res = dboQuery($sql);
			$total = dboAffectedRows();
			if($total > 0 && $status == STATUS_NOVO)
			{
				while($lin = dboFetchObject($res))
				{
					$this->addNovoChamado($lin);
				}
			}
			return $total;
		}

		function getServicosStatus($status, $pessoa)
		{
			global $_system;
			if(!$pessoa)
			{
				$pessoa = loggedUser();
			}
			$sql = "
				SELECT 
					DISTINCT requisicao_item.*,
					categoria_servico.nome AS nome_area
				FROM
					requisicao_item,
					tipo_servico,
					categoria_servico,
					requisicao_item_servidor
				WHERE 
					requisicao_item.tipo_servico = tipo_servico.id AND
					tipo_servico.categoria_servico = categoria_servico.id AND
					".(($_system['notificacoes_areas'] || $_system['contexto_areas'])?(
					"(
						".(($_system['notificacoes_areas'])?(" categoria_servico.id IN(".implode(",", $_system['notificacoes_areas']).") OR "):((($_system['contexto_areas'])?(" categoria_servico.id IN(".implode(",", $_system['contexto_areas']).") OR "):(''))))."
						(
							requisicao_item_servidor.item = requisicao_item.id AND
							requisicao_item_servidor.servidor = '".$pessoa."'
						)
					) AND"
					):(''))."
					requisicao_item.status = ".$status." AND
					requisicao_item.inativo = 0
			";
			$res = dboQuery($sql);
			if(dboAffectedRows())
			{
				return $res;
			}
			return false;
		}

		function getTotalAtrasado()
		{
			global $_system;
			$sql = "
				SELECT 
					COUNT(DISTINCT requisicao_item.id) AS total
				FROM
					requisicao_item,
					tipo_servico,
					categoria_servico,
					requisicao_item_servidor
				WHERE 
					requisicao_item.tipo_servico = tipo_servico.id AND
					tipo_servico.categoria_servico = categoria_servico.id AND
					".(($_system['notificacoes_areas'] || $_system['contexto_areas'])?(
					"(
						".(($_system['notificacoes_areas'])?(" categoria_servico.id IN(".implode(",", $_system['notificacoes_areas']).") OR "):((($_system['contexto_areas'])?(" categoria_servico.id IN(".implode(",", $_system['contexto_areas']).") OR "):(''))))."
						(
							requisicao_item_servidor.item = requisicao_item.id AND
							requisicao_item_servidor.servidor = '".loggedUser()."'
						)
					) AND"
					):(''))."
					requisicao_item.data_agendada IS NOT NULL AND
					requisicao_item.data_agendada <".((ALERTAR_SERVICOS_DO_DIA)?("="):(""))." '".date('Y-m-d')."' AND
					requisicao_item.status < ".STATUS_CONCLUIDO." AND
					requisicao_item.inativo = 0
			";
			$res = dboQuery($sql);
			$lin = dboFetchObject($res);
			return $lin->total;
		}

		function getAtrasadosPessoa($pessoa = false)
		{
			global $_system;
			if(!$pessoa)
			{
				$pessoa = loggedUser();
			}
			$sql = "
				SELECT 
					DISTINCT(requisicao_item.id) AS grouper,
					requisicao_item.*
				FROM
					requisicao_item,
					tipo_servico,
					categoria_servico,
					requisicao_item_servidor
				WHERE 
					requisicao_item.tipo_servico = tipo_servico.id AND
					tipo_servico.categoria_servico = categoria_servico.id AND
					".(($_system['notificacoes_areas'] || $_system['contexto_areas'])?(
					"(
						".(($_system['notificacoes_areas'])?(" categoria_servico.id IN(".implode(",", $_system['notificacoes_areas']).") OR "):((($_system['contexto_areas'])?(" categoria_servico.id IN(".implode(",", $_system['contexto_areas']).") OR "):(''))))."
						(
							requisicao_item_servidor.item = requisicao_item.id AND
							requisicao_item_servidor.servidor = '".$pessoa."'
						)
					) AND"
					):(''))."
					requisicao_item.data_agendada IS NOT NULL AND
					requisicao_item.data_agendada <".((ALERTAR_SERVICOS_DO_DIA)?("="):(""))." '".date('Y-m-d')."' AND
					requisicao_item.status < ".STATUS_CONCLUIDO." AND
					requisicao_item.inativo = 0
				GROUP BY
					grouper
				ORDER BY
					data_agendada DESC
			";
			$this->query($sql);
		}

		function addNovoChamado($chamado)
		{
			$this->novos_chamados[$chamado->id] = $chamado;
		}

		function getNovosChamados()
		{
			if(sizeof($this->novos_chamados))
			{
				return $this->novos_chamados;
			}
			return 0;
		}

		function getTotalAvisado()
		{
			global $_system;
			$sql = "
				SELECT 
					COUNT(DISTINCT requisicao_item.id) AS total
				FROM
					requisicao_item,
					tipo_servico,
					categoria_servico,
					requisicao_item_servidor
				WHERE 
					requisicao_item.tipo_servico = tipo_servico.id AND
					tipo_servico.categoria_servico = categoria_servico.id AND 
					".(($_system['notificacoes_areas'] || $_system['contexto_areas'])?(
					"(
						".(($_system['notificacoes_areas'])?(" categoria_servico.id IN(".implode(",", $_system['notificacoes_areas']).") OR "):((($_system['contexto_areas'])?(" categoria_servico.id IN(".implode(",", $_system['contexto_areas']).") OR "):(''))))."
						(
							requisicao_item_servidor.item = requisicao_item.id AND
							requisicao_item_servidor.servidor = '".loggedUser()."'
						)
					) AND"
					):(''))."
					requisicao_item.data_agendada IS NOT NULL AND
					requisicao_item.data_agendada <= '".somaDataAMD(date('Y-m-d'), 0, 0, DIAS_DE_AVISO)."' AND
					requisicao_item.data_agendada >".((ALERTAR_SERVICOS_DO_DIA)?(""):("="))." '".somaDataAMD(date('Y-m-d'))."' AND
					requisicao_item.status < ".STATUS_CONCLUIDO." AND
					requisicao_item.inativo = 0
			";
			$res = dboQuery($sql);
			$lin = dboFetchObject($res);
			return $lin->total;
		}

		function getTotalFinalizado()
		{
			global $_system;
			$sql = "
				SELECT 
					COUNT(requisicao_item.id) AS total
				FROM
					requisicao_item,
					tipo_servico,
					categoria_servico
				WHERE 
					requisicao_item.tipo_servico = tipo_servico.id AND
					tipo_servico.categoria_servico = categoria_servico.id AND
					".(($_system['notificacoes_areas'])?(" categoria_servico.id IN(".implode(",", $_system['notificacoes_areas']).") AND "):((($_system['contexto_areas'])?(" categoria_servico.id IN(".implode(",", $_system['contexto_areas']).") AND "):(''))))."
					requisicao_item.status < 6 AND
					requisicao_item.inativo = 0 AND
					requisicao_item.finalizado_servidor <> 0
			";
			$res = dboQuery($sql);
			$lin = dboFetchObject($res);
			return $lin->total;
		}

		function getAvisadosPessoa($pessoa = false)
		{
			global $_system;
			if(!$pessoa)
			{
				$pessoa = loggedUser();
			}
			$sql = "
				SELECT
					DISTINCT(requisicao_item.id) AS grouper,
					requisicao_item.*
				FROM
					requisicao_item,
					tipo_servico,
					categoria_servico,
					requisicao_item_servidor
				WHERE 
					requisicao_item.tipo_servico = tipo_servico.id AND
					tipo_servico.categoria_servico = categoria_servico.id AND 
					".(($_system['notificacoes_areas'] || $_system['contexto_areas'])?(
					"(
						".(($_system['notificacoes_areas'])?(" categoria_servico.id IN(".implode(",", $_system['notificacoes_areas']).") OR "):((($_system['contexto_areas'])?(" categoria_servico.id IN(".implode(",", $_system['contexto_areas']).") OR "):(''))))."
						(
							requisicao_item_servidor.item = requisicao_item.id AND
							requisicao_item_servidor.servidor = '".$pessoa."'
						)
					) AND"
					):(''))."
					requisicao_item.data_agendada IS NOT NULL AND
					requisicao_item.data_agendada <= '".somaDataAMD(date('Y-m-d'), 0, 0, DIAS_DE_AVISO)."' AND
					requisicao_item.data_agendada >".((ALERTAR_SERVICOS_DO_DIA)?(""):("="))." '".somaDataAMD(date('Y-m-d'))."' AND
					requisicao_item.status < ".STATUS_CONCLUIDO." AND
					requisicao_item.inativo = 0
				GROUP BY 
					grouper
				ORDER BY
					data_agendada DESC
			";
			$this->query($sql);
		}

		function getFinalizados()
		{
			global $_system;
			$sql = "
				SELECT 
					requisicao_item.*,
					tipo_servico.nome AS tipo_servico_nome
				FROM
					requisicao_item,
					tipo_servico,
					categoria_servico
				WHERE 
					requisicao_item.tipo_servico = tipo_servico.id AND
					tipo_servico.categoria_servico = categoria_servico.id AND
					".(($_system['notificacoes_areas'])?(" categoria_servico.id IN(".implode(",", $_system['notificacoes_areas']).") AND "):((($_system['contexto_areas'])?(" categoria_servico.id IN(".implode(",", $_system['contexto_areas']).") AND "):(''))))."
					requisicao_item.status < 6 AND
					requisicao_item.inativo = 0 AND
					requisicao_item.finalizado_servidor <> 0
			";
			$res = dboQuery($sql);
			if(dboAffectedRows())
			{
				return $res;
			}
			return false;
		}

		function getTotalUsuario($status)
		{
			if(!loggedUser())
			{
				return 0;
			}
			$sql = "
				SELECT 
					COUNT(ri.id) AS total
				FROM
					requisicao r,
					requisicao_item ri
				WHERE
					ri.requisicao = r.id AND
					ri.status = '".$status."' AND
					ri.inativo = 0 AND
					(
						r.email_requisitante = '".$_SESSION['user']."'
					)
			";
			$res = dboQuery($sql);
			$lin = dboFetchObject($res);
			return $lin->total;
		}

		function getItensUsuario($status)
		{
			global $pessoa;
			$sql = "
				SELECT 
					ri.*
				FROM
					requisicao r,
					requisicao_item ri
				WHERE
					ri.requisicao = r.id AND
					ri.status = '".$status."' AND
					ri.inativo = 0 AND
					(
						r.email_requisitante = '".$pessoa->user."'
					)
			";
			$res = dboQuery($sql);
			if(dboAffectedRows())
			{
				return $res;
			}
			return false;
		}

		function getTotalFeedbacksAtrasados()
		{
			global $_system;
			$sql = "
				SELECT
					serv.*,
					DATEDIFF(NOW(), hist.data) AS dias_passados,
					DATEDIFF(NOW(), hist.data)-serv.dias_feedback AS dias_passados_do_prazo,
					hist.comentario AS ultimo_historico
				FROM
					requisicao_item serv,
					requisicao_item_historico hist,
					tipo_servico tipo_serv,
					categoria_servico categ_serv
				WHERE
					serv.status < ".STATUS_CONCLUIDO." AND
					serv.inativo = 0 AND
					serv.dias_feedback > 0 AND
					(
						DATE_ADD(hist.data, INTERVAL serv.dias_feedback DAY) <= NOW()
					) AND
					serv.tipo_servico = tipo_serv.id AND
					tipo_serv.categoria_servico = categ_serv.id AND
					".(($_system['notificacoes_areas'])?(" categ_serv.id IN(".implode(",", $_system['notificacoes_areas']).") AND "):(''))."
					hist.requisicao_item = serv.id AND
					hist.id = (
						SELECT 
							MAX(id)
						FROM 
							requisicao_item_historico
						WHERE
							requisicao_item = serv.id
						LIMIT 1
					)
				ORDER BY dias_passados_do_prazo DESC		
			";
			$this->query($sql);
			return $this->size();
		}

		function getFeedbacksAtrasados()
		{
			global $_system;
			$sql = "
				SELECT
					serv.*,
					DATEDIFF(NOW(), hist.data) AS dias_passados,
					DATEDIFF(NOW(), hist.data)-serv.dias_feedback AS dias_passados_do_prazo,
					hist.comentario AS ultimo_historico
				FROM
					requisicao_item serv,
					requisicao_item_historico hist,
					tipo_servico tipo_serv,
					categoria_servico categ_serv
				WHERE
					serv.status < ".STATUS_CONCLUIDO." AND
					serv.inativo = 0 AND
					serv.dias_feedback > 0 AND
					(
						DATE_ADD(hist.data, INTERVAL serv.dias_feedback DAY) <= NOW()
					) AND
					serv.tipo_servico = tipo_serv.id AND
					tipo_serv.categoria_servico = categ_serv.id AND
					".(($_system['contexto_areas'])?(" categ_serv.id IN(".implode(",", $_system['contexto_areas']).") AND "):(''))."
					hist.requisicao_item = serv.id AND
					hist.id = (
						SELECT 
							MAX(id)
						FROM 
							requisicao_item_historico
						WHERE
							requisicao_item = serv.id
						LIMIT 1
					)
				ORDER BY dias_passados_do_prazo DESC		
			";
			$this->query($sql);
		}

		function getNotifications()
		{
			/* pega as notificaoes e salva no objeto */
			$this->total_novo = $this->getTotalStatus(STATUS_NOVO);
			$this->total_aguardando = $this->getTotalStatus(STATUS_AGUARDANDO_APROVACAO_DIRETORIA);
			$this->total_finalizado = $this->getTotalFinalizado();
			$this->total_usuario_concluido = $this->getTotalUsuario(STATUS_CONCLUIDO);
			$this->total_usuario_aguardando = $this->getTotalUsuario(STATUS_AGUARDANDO_REQUISITANTE);
			$this->total_feedback_atrasado = $this->getTotalFeedbacksAtrasados();
		}

		function getNotificationTag($tipo)
		{
			if(!isset($this->total_novo))
			{
				$this->getNotifications();
			}
			if($tipo == 'novo' && $this->total_novo > 0)
			{
				$mensagem = (($this->total_novo > 1)?($this->total_novo." serviços novos precisam de atribuição"):($this->total_novo." serviço novo precisa de atribuição"));
				return "<span class='notification novo help' title='".$mensagem."'>".$this->total_novo."</span>";
			}
			if($tipo == 'aguardando' && $this->total_aguardando > 0)
			{
				$mensagem = (($this->total_aguardando > 1)?($this->total_aguardando." serviços estão aguardando"):($this->total_aguardando." serviço está aguardando"));
				return "<span class='notification aguardando help' title='".$mensagem."'>".$this->total_aguardando."</span>";
			}
			if($tipo == 'finalizado' && $this->total_finalizado > 0)
			{
				$mensagem = (($this->total_finalizado > 1)?($this->total_finalizado." serviços finalizados precisam ser concluídos"):($this->total_finalizado." serviço finalizado precisa ser concluído"));
				return "<span class='notification finalizado help' title='".$mensagem."'>".$this->total_finalizado."</span>";
			}
			if($tipo == 'pendente')
			{
				$total = $this->total_finalizado + $this->total_novo + $this->total_feedback_atrasado;
				if($total > 0)
				{
					$mensagem = (($total > 1)?($total." serviços precisam de sua atenção"):($total." serviço precisa de sua atenção"));
					return "<span class='notification pendente help' title='".$mensagem."'>".$total."</span>";
				}
			}
			if($tipo == 'usuario_aguardando')
			{
				if($this->total_usuario_aguardando > 0)
				{
					$mensagem = (($this->total_usuario_aguardando > 1)?($this->total_usuario_aguardando." serviços estão aguardando seu contato"):($this->total_usuario_aguardando." serviço está aguardando seu contato"));
					return "<span class='notification usuario_aguardando help' title='".$mensagem."'>".$this->total_usuario_aguardando."</span>";
				}
				else
				{
					return '';
				}
			}
			if($tipo == 'usuario_concluido')
			{
				if($this->total_usuario_concluido > 0)
				{
				$mensagem = (($this->total_usuario_concluido > 1)?($this->total_usuario_concluido." serviços concluídos estão aguardando sua avaliação"):($this->total_usuario_concluido." serviço concluído está aguardando sua avaliação"));
				return "<span class='notification usuario_concluido help' title='".$mensagem."'>".$this->total_usuario_concluido."</span>";
				}
				else
				{
					return '';
				}
			}
			if($tipo == 'feedback_atrasado')
			{
				if($this->total_feedback_atrasado > 0)
				{
				$mensagem = (($this->total_feedback_atrasado > 1)?($this->total_feedback_atrasado." serviços estão com feedback atrasado para o requisitante"):($this->total_feedback_atrasado." serviço está com feedback atrasado para o requisitante"));
				return "<span class='notification feedback_atrasado help' title='".$mensagem."'>".$this->total_feedback_atrasado."</span>";
				}
				else
				{
					return '';
				}
			}
		}

		function nuncaAntes($status)
		{
			$sql = "SELECT id FROM requisicao_item_historico WHERE requisicao_item = '".$this->id."' AND status = '".$status."';";
			dboQuery($sql);
			if(dboAffectedRows())
			{
				return false;
			}
			return true;
		}

		static function showRequestedModal()
		{
			if($_SESSION[sysId()]['show_modal_requisicao']['requisicao_id'])
			{
				?>
				<script>
					$(document).ready(function(){
						gerenciarRequisicao(<?= $_SESSION[sysId()]['show_modal_requisicao']['requisicao_id'] ?>, <?= $_SESSION[sysId()]['show_modal_requisicao']['requisicao_item_id'] ?>)
					}) //doc.ready
				</script>
				<?
				unset($_SESSION[sysId()]['show_modal_requisicao']);
			}			
		}

		function getTabelaMateriaisUtilizados($params = array())
		{
			ob_start();
			
			extract($params);

			$sql = "
				SELECT
					estoque.*,
					material.nome AS material_nome,
					material.unidade AS material_unidade
				FROM
					estoque, material
				WHERE
					estoque.material = material.id AND
					estoque.requisicao_item = '".$this->id."'
				ORDER BY 
					estoque.id
			";
			$est = new estoque();
			$est->query($sql);
			?>
			<table class="list <?= $table_class ?>" style="margin-bottom: 8px;">
				<thead>
					<tr>
						<th>Material / Custo</th>
						<th class="text-right" style="width: 1px">Qtd.</th>
						<th class="text-right" style="width: 1px">Valor&nbsp;uni.</th>
						<?= ((!$view_only)?('<th style="width: 27px"></th>'):('')) ?>
					</tr>
				</thead>
				<tbody>
					<?
						if($est->size())
						{
							$total = 0;
							do {
								$total += (($est->custo_unitario > 0)?($est->custo_unitario*$est->quantidade):(0));
								?>
									<td><?= $est->material_nome ?> <?= (($est->equipamento)?($est->_equipamento->getLinkTag()):('')) ?></td>
									<td class="text-right nowrap"><?= $est->quantidade ?> <?= $est->material_unidade ?></td>
									<td class="text-right"><?= (($est->custo_unitario > 0)?(reais($est->custo_unitario)):('-')) ?></td>
									<?
										if(!$view_only)
										{
											?>
											<td class="control-icons text-center" style="width: 1px">
												<?
													if(hasPermission('atribuir-estoque') || hasPermission('atribuir-inventario'))
													{
														?>
														<a href="<?= secureUrl('ajax-servico-estoque.php?action=desatribuir-material&requisicao_item_id='.$this->id.'&estoque_id='.$est->id.'&'.CSRFVar()) ?>" data-confirm="Tem certeza que deseja remover este material?" class="peixe-json"><i class="fa-times"></i></a>
														<?
													}
												?>
											</td>
											<?
										}
									?>
								</tr>
								<?
							}while($est->fetch());
						}

						//pegando agora o custo adicional da requisicao..
						$sql = "SELECT * FROM requisicao_item_custo_adicional WHERE requisicao_item = '".$this->id."' ORDER BY id;";
						$res = dboQuery($sql);
						$tem_custo_adicional = dboAffectedRows();
						if($tem_custo_adicional)
						{
							while($lin = dboFetchObject($res))
							{
								$total += $lin->custo;
								?>
								<tr>
									<td colspan="2"><?= $lin->descricao ?></td>
									<td class="text-right"><?= (($lin->custo > 0)?(reais($lin->custo)):('-')) ?></td>
									<?
										if(!$view_only)
										{
											?>
											<td class="control-icons text-center"><a href="<?= secureUrl('ajax-servico-estoque.php?action=remover-custo-adicional&requisicao_item_id='.$this->id.'&custo_adicional_id='.$lin->id.'&'.CSRFVar()) ?>" data-confirm="Tem certeza que deseja remover este custo adicional?" class="peixe-json"><i class="fa-times"></i></a></td>
											<?
										}
									?>
								</tr>
								<?
							}
						}
						if(!$view_only)
						{
							?>
							<tr class="custo-adicional" id="form-custo-adicional" style="display: none;">
								<td colspan="2"><input type="text" name="custo_adicional_descricao" id="custo_adicional_descricao" value="" placeholder="Digite a descrição do custo, ex: Mão de obra"/></td>
								<td><input type="text" name="custo_adicional_valor" id="custo_adicional_valor" value="" class="price text-right" placeholder="Valor..."/></td>
								<td style="position: relative;"><span class="button tiny radius no-margin trigger-incluir-custo-adicional" title="Incluir custo adicional" style="position: absolute; top: 6px; left: 2px;" data-url="<?= secureUrl('ajax-servico-estoque.php?action=incluir-custo-adicional&requisicao_item_id='.$this->id.'&'.CSRFVar()) ?>"><i class="fa-check fa" style="margin: 0;"></i></span></td>
							</tr>
							<?
						}
					?>
					<tfoot>
						<tr class="custo-total">
							<td colspan="2" class="text-right">
								<?= ((!$view_only)?('<a href="#" class="trigger-custo-adicional" style="float: left;"><i class="fa-plus"></i> Custo adicional</a>'):('')) ?>
								<strong>Custo total</strong>
							</td>
							<td class="text-right"><strong><?= reais($total) ?></strong></td>
							<?= ((!$view_only)?('<td></td>'):('')) ?>
						</tr>
					</tfoot>
				</tbody>
			</table>
			<div id="estoque-helpers" class="hide-for-small">
				<?
					if(!$est->size() && !$tem_custo_adicional)
					{
						?>
						<div class="row">
							<div class="large-<?= ((!hasPermission('atribuir-estoque') || !hasPermission('atribuir-inventario'))?('4'):('6')) ?> columns">
								<div class="helper arrow-top no-margin">
									<p class="no-margin font-12">Clique aqui para incluir custos fora do estoque, como mão de obra, taxas, frete, etc.</p>
								</div>
							</div>
							<?
								if(hasPermission('atribuir-estoque') || hasPermission('atribuir-inventario'))
								{
									?>
									<div class="large-6 columns">
										<div class="helper arrow-right no-margin">
											<p class="no-margin font-12">Utilize estes campos para atribuir materiais de estoque ou inventário ao serviço.</p>
										</div>
									</div>
									<?
								}
							?>
						</div>
						<?
					}
				?>
			</div>
			<?
			return ob_get_clean();
		}

		function getTabelaEquipamentosRelacionados($params = array())
		{
			ob_start();

			$sql = "SELECT DISTINCT equipamento FROM requisicao_item_equipamento WHERE requisicao_item = '".$this->id."' ORDER BY id";
			$res = dboQuery($sql);
			$tem = dboAffectedRows();
			?>
			<span class="tag-servidor tag-nenhum-general" id="tag-nenhum-equipamento-relacionado" style="<?= (($tem)?('display: none;'):('')) ?>">Nenhum equipamento relacionado</span>
			<div id="form-relacionar-equipamento" style="display: none;" data-url="<?= secureUrl('ajax-equipamento-relacionado.php?action=relacionar-equipamento&requisicao_item_id='.$this->id.'&'.CSRFVar()) ?>" data-url-reload="<?= secureUrl('ajax-equipamento-relacionado.php?action=reload-list&requisicao_item_id='.$this->id.'&'.CSRFVar()) ?>">
				<div class="row collapse">
					<div class="large-9 columns">
						<input type="text" name="" id="relacionar-equipamento" class="no-margin" value="" placeholder="Digite o número do patrimônio para procurar..."/>
					</div>
					<div class="large-3 columns">
						<a href="#" class="button radius no-margin postfix trigger-colorbox-modal" data-url="dbo_admin.php?dbo_mod=equipamento&dbo_new=1&dbo_modal=1&body_class=hide-breadcrumb&requisicao_item_id=<?= $this->id ?>&dbo_return_function=relacionaEquipamentoRequisicaoItem<?= dboAdminRedirectCode(' javascript: parent.$.colorbox.close(); parent.reloadEquipamentosRelacionados(); ') ?>"><i class="fa-plus"></i> Novo</a>
					</div>
				</div>
			</div>
			<?
			if($tem)
			{
				?>
				<table class="list responsive">
					<thead>
						<tr>
							<th colspan="4" style="padding: 2px;"></th>
						</tr>
					</thead>				
					<tbody>
						<?
							while($lin = dboFetchObject($res))
							{
								$rel = new requisicao_item_equipamento("WHERE equipamento = '".$lin->equipamento."' AND requisicao_item = '".$this->id."' ORDER BY id DESC LIMIT 1");
								?>
								<tr class="pointer trigger-detalhes-equipamentos-relacionados" data-url="<?= secureUrl('ajax-equipamento-relacionado.php?action=get-tabela-detalhes&requisicao_item_id='.$this->id.'&equipamento_id='.$lin->equipamento.'&'.CSRFVar()) ?>">
									<td data-title="Equipamento"><?= $rel->_equipamento->getSmartName(); ?></td>
									<td data-title="Vis. equip."><?= $rel->_equipamento->getLinkTag(); ?></td>
									<td data-title="Status"><?= $rel->getTagSituacao(); ?></td>
									<td class="text-right control-icons nowrap"><span class="button tiny radius no-margin trigger-modal-equipamentos-relacionados stop-propagation top-minus-1" style="text-transform: uppercase;" data-url="<?= secureUrl('modal-equipamento-relacionado.php?requisicao_item_id='.$this->id.'&equipamento_id='.$lin->equipamento.'&padding&'.CSRFVar()) ?>"><i class="fa-refresh"></i> Status</span><a href="<?= secureUrl('ajax-equipamento-relacionado.php?action=desrelacionar-equipamento&equipamento_id='.$rel->_equipamento->id.'&requisicao_item_id='.$this->id.'&'.CSRFVar()) ?>" class="peixe-json font-14" data-confirm="<?= htmlSpecialChars($rel->_equipamento->getSmartName()) ?>\n\nTem certeza que deseja remover a relação deste equipamento com a requisição?\n\nTodos os históricos deste relacionamento serão deletados."><i class="fa-times fa-fw"></i></a></td>
								</tr>
								<?
							}
						?>
					</tbody>
				</table>
				<?
			}
			return ob_get_clean();
		}

		function concluir()
		{
			//setando o status como concluído
			$this->status = STATUS_CONCLUIDO;
			$this->data_agendada_conclusao = $this->null();
			$this->update();

			//criando o historico de conclusão
			$hist = new historico();
			$hist->requisicao_item = $this->id;
			$hist->created_by = 0;
			$hist->data = dboNow();
			$hist->prioridade = $this->prioridade;
			$hist->status = $this->status;
			$hist->comentario = '';
			$hist->save();

			//enviando o e-mail de avaliacao
			$id_avaliacao = $this->createAvaliacao();
			disparaEmail('usuario-servico-concluido', array('id_avaliacao' => $id_avaliacao));
		}

		function temMateriaisAtribuidos()
		{
			$total = 0;

			$sql = "SELECT COUNT(*) AS total FROM requisicao_item_custo_adicional WHERE requisicao_item = '".$this->id."';";
			$res = dboQuery($sql);
			$lin = dboFetchObject($res);
			$total += $lin->total;

			$sql = "SELECT COUNT(*) AS total FROM estoque WHERE requisicao_item = '".$this->id."';";
			$res = dboQuery($sql);
			$lin = dboFetchObject($res);
			$total += $lin->total;

			return $total;
		}

		function relacionaEquipamentos($pats = array())
		{
			if(sizeof($pats))
			{
				//verificando se existem os itens cadastrados na tabela de inventário.
				foreach($pats as $pat)
				{
					//se não existe o equipamento, cadastra.
					$eq = new equipamento("WHERE patrimonio = '".intval($pat)."'");
					if(!$eq->size())
					{
						$eq->patrimonio = intval($pat);
						$eq->local = $this->local;
						$eq->status = STATUS_EQUIPAMENTO_EM_USO;
						$eq->responsavel = $this->_requisicao->nome_requisitante;
						$eq->externo = 1;
						$eq->created_by = loggedUser();
						$eq->created_on = dboNow();
						$eq->equipamento_marca = '-1';
						$eq->save();
					}

					//cria o relacionamento
					$rel = new requisicao_item_equipamento();
					$rel->requisicao_item = $this->id;
					$rel->equipamento = $eq->id;
					$rel->situacao = $rel->null();
					$rel->data = dboNow();
					$rel->created_by = loggedUser();
					$rel->save();
				}
			}
		}

		function temEquipamentosRelacionadosPendentes()
		{
			$sql = "
				SELECT 
					COUNT(*) AS total
				FROM 
					requisicao_item_equipamento r1
				WHERE 
					r1.requisicao_item = '".$this->id."' AND
					(
						r1.situacao IS NULL OR
						r1.situacao IN ('entrada', 'assistencia_tecnica')
					) AND
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
			if($lin->total > 0)
			{
				return true;
			}
			return false;
		}

		function getTagModal($params = array())
		{
			extract($params);

			ob_start();
			?>
			<span class="font-14">
				<span <?= (($no_click)?(''):('onClick="gerenciarRequisicao('.$this->requisicao.', '.$this->id.')"')) ?> class="stop-propagation">
					<span class="has-tip tip-top hide-for-small" data-tooltip title="Serviço <?= $this->getSmartNumber() ?>"><i class="fa-file-text-o fa-fw pointer"></i></span>
					<span class="has-tip show-for-small pointer"><i class="fa-file-text-o fa-fw"></i> Visualizar</span>
				</span>
		</span>
			<?
			return ob_get_clean();
			//return '<span class="font-14 has-tip tip-top stop-propagation" data-tooltip title="Serviço '.$this->getSmartNumber().'"><i class="fa-file-text-o fa-fw single pointer"></i></span>';
		}

		function getSmartNumber()
		{
			return $this->requisicao.'/'.$this->numero;
		}

		function getPermalink()
		{
			return DBO_URL."/../servico-user-view.php?&id=".$this->id."&token=".$this->token;
		}

		function getPermalinkModal()
		{
			return DBO_URL."/../intent.php?action=show_modal_requisicao&requisicao_id=".$this->requisicao."&requisicao_item_id=".$this->id;
		}

	} //class declaration
} //if ! class exists

?>
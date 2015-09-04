<?

/* ================================================================================================================== */
/* DBO CLASS FILE FOR MODULE 'requisicao_item_equipamento' ====================== AUTO-CREATED ON 18/03/2015 10:15:54 */
/* ================================================================================================================== */

/* IMPORTANT: This file is generated only in the first DBO sync, what means you should edit only via text editor. */

if(!class_exists('requisicao_item_equipamento'))
{
	class requisicao_item_equipamento extends dbo
	{
		/* smart constructor: will perform load() upon numeric argument and loadAll() upon string argument */
		function __construct($foo = '')
		{
			parent::__construct('requisicao_item_equipamento');
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

		function getTagSituacao($params = array())
		{
			global $_conf;
			if($this->situacao == 'entrada')
			{
				$title = "Equipamento n".(($_conf->genero = 'f')?('a'):('o')).' '.$_conf->nome_curto_secao;
				$icon = 'wrench';
			}
			elseif($this->situacao == 'saida')
			{
				$title = "Equipamento devolvido ao usuário";
				$icon = "check";
				$color = 'primary';
			}
			elseif($this->situacao == 'resolvido_local')
			{
				$title = "Serviço realizado no local";
				$icon = "check";
				$color = 'primary';
			}
			elseif($this->situacao == 'assistencia_tecnica')
			{
				$title = "Equipamento enviado à assistência técnica";
				$icon = "truck";
			}
			elseif($this->situacao == 'baixa')
			{
				$title = "Baixa";
				$icon = "trash";
			}
			else
			{
				$title = "Equipamento aguardando interação d".(($_conf->genero = 'f')?('a'):('o')).' '.$_conf->nome_curto_secao;
				$icon = 'exclamation-circle';
				$color = 'warning';
			}

			ob_start();
			?>
			<span class="has-tip tip-top font-14 color <?= $color ?>" data-tooltip title="<?= $title ?>"><i class="fa-fw fa-<?= $icon ?>"></i><span class="show-for-small"> Visualizar</span></span>
			<?
			return ob_get_clean();
		}

		function delete()
		{
			$ret = parent::delete();
			$mov = new equipamento_movimentacao("WHERE created_by_requisicao_item_equipamento = '".$this->id."' ORDER BY id DESC");
			if($mov->size())
			{
				do {
					$mov->delete();
				}while($mov->fetch());
			}
			return $ret;
		}

		static function getTabelaDetalhes($params = array())
		{
			extract($params);
			ob_start();
			$rel = new requisicao_item_equipamento("WHERE requisicao_item = '".$requisicao_item_id."' AND equipamento = '".$equipamento_id."' AND situacao IS NOT NULL ORDER BY id");
			if($rel->size())
			{
				?>
				<table class="no-margin list">
					<thead>
						<tr>
							<th>Data</th>
							<th class="help" title="Status">St.</th>
							<th>Local</th>
							<th colspan="2"></th>
						</tr>
					</thead>
					<tbody>
						<?
							do {
								?>
								<tr class="help" title="Operação cadastrada por <?= $rel->_created_by->getShortName() ?>">
									<td><span class="help" title="<?= dboDate('d/M/Y H:i', strtotime($rel->data)) ?>"><?= ago($rel->data) ?></span></td>
									<td><?= $rel->getTagSituacao() ?></td>
									<td><?= $rel->_local->getSmartLocal(); ?></td>
									<td><?= (($rel->comentario)?('<span class="has-tip tip-top font-14" data-tooltip title="'.htmlSpecialChars($rel->comentario).'"><i class="fa-fw fa-comment-o"></i></span>'):('')) ?></td>
									<td class="control-icons">
										<?
											if($rel->getIterator() == $rel->size())
											{
												?><a href="<?= secureUrl('ajax-equipamento-relacionado.php?action=delete-situacao-equipamento&requisicao_item_id='.$rel->requisicao_item.'&requisicao_item_equipamento_id='.$rel->id.'&'.CSRFVar()) ?>" class="peixe-json font-14" data-confirm="Tem certeza que deseja excluir este histórico?"><i class="fa-times single"></i></a><?
											}
										?>
									</td>
								</tr>
								<?
							}while($rel->fetch());
						?>
					</tbody>
				</table>
				<?
			}
			else
			{
				?>
				<p class="no-margin-for-small">Ainda não há interações registradas.</p>
				<?				
			}
			return ob_get_clean();
		}

	} //class declaration
} //if ! class exists

?>
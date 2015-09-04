<?

class servidor extends pessoa
{

	function getBadge($size = 'normal')
	{
		global $global_ids_prestadores;
		$prestadores = (array)$global_ids_prestadores;
		$atrib = $this->getNumeroAtribuicoes();
		$name = $this->getShortName();
		?>
		<div class="servidor-badge size-<?= $size ?> <?= ((in_array($this->id, $prestadores))?('tipo-prestador'):('')) ?>" data_modulo='servidor' data_id="<?= $this->id ?>" data_nome="<?= htmlSpecialChars($name) ?>" data-id="<?= $this->id ?>" data-nome="<?= htmlSpecialChars($name) ?>">
			<span class="pic"><img src='<?= $this->getFoto() ?>'/></span>
			<span class="nome"><?= $name ?></span>
			<span class="numero-atribuicoes atribuicoes-<?= $atrib ?>" title='<?= $atrib ?> atribuições ativas'><?= $atrib ?></span>
		</div>		
		<?
	}

	function getNumeroAtribuicoes()
	{
		$sql = "
			SELECT COUNT(ris.servidor) AS total
			FROM 
				requisicao_item_servidor ris,
				requisicao_item ri
			WHERE
				ris.item = ri.id
				AND ris.servidor = '".$this->id."'
				AND ri.inativo = 0
				AND ri.status IN (3,5)
		";
		$res = dboQuery($sql);
		$lin = dboFetchObject($res);
		return $lin->total;
	}

}

?>
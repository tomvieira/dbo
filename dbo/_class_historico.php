<?

/* ================================================================================================================== */
/* DBO CLASS FILE FOR MODULE 'historico' ======================================== AUTO-CREATED ON 17/04/2013 09:31:49 */
/* ================================================================================================================== */

/* IMPORTANT: This file is generated only in the first DBO sync, what means you should edit only via text editor. */

if(!class_exists('historico'))
{
	class historico extends dbo
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
		function encodeAtribuicoes($in, $out)
		{
			if(sizeof($in) || sizeof($out))
			{
				$this->atribuicoes = implode(",", $in)."\n".implode(',', $out);
			}
		}

		function decodeAtribuicoes()
		{
			if($this->atribuicoes != null)
			{
				list($in, $out) = explode("\n", $this->atribuicoes);
				return array(explode(",", $in), explode(",", $out));
			}
			return array(array(), array());
		}

		function getTagsAtribuicoes()
		{
			global $global_ids_servidores;
			ob_start();
			list($in, $out) = $this->decodeAtribuicoes();
			$in = array_filter($in);
			$out = array_filter($out);
			if(sizeof($in) || sizeof($out))
			{
				if(sizeof($in))
				{
					foreach($in as $id)
					{
						$pes = new servidor($id);
						?>
						<div style="margin-bottom: 1px; position: relative; left: -11px;">
							<span class="tag"><i class="fa fa-arrow-left"></i></span><span title="<?= $pes->getShortName() ?> foi atribuíd<?= $pes->getGenero() ?> ao serviço" class="tag-<?= ((in_array($id, $global_ids_servidores))?('servidor'):('prestador')) ?>"><?= $pes->getShortName(); ?></span>
						</div>
						<?
					}
				}
				if(sizeof($out))
				{
					foreach($out as $id)
					{
						$pes = new servidor($id);
						?>
						<div style="margin-top: 1px; opacity: .2;">
							<span title="<?= $pes->getShortName() ?> foi desatribuíd<?= $pes->getGenero() ?> do serviço" class="tag-servidor"><?= $pes->getShortName(); ?></span><span class="tag"><i class="fa fa-arrow-right"></i></span>
						</div>
						<?
					}
				}
			}
			return ob_get_clean();
		}

	} //class declaration
} //if ! class exists

?>
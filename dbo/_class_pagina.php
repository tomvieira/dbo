<?

/* ================================================================================================================== */
/* DBO CLASS FILE FOR MODULE 'pagina' =========================================== AUTO-CREATED ON 11/06/2015 01:53:43 */
/* ================================================================================================================== */

/* IMPORTANT: This file is generated only in the first DBO sync, what means you should edit only via text editor. */

if(!class_exists('pagina'))
{
	class pagina extends dbo
	{

		var $client_object_key = '__client_key';

		/* smart constructor: will perform load() upon numeric argument and loadAll() upon string argument */
		function __construct($foo = '', $params = array())
		{
			parent::__construct('pagina');
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
		static function smartLoad($params = array())
		{
			global $_system;
			extract($params);

			$obj = new self();

			//load por id
			if($slug || $id || $tipo)
			{
				$campo = $slug ? 'slug': 'id';
				if(!$tipo)
				{
					$sql = "SELECT tipo FROM ".$obj->getTable()." WHERE ".$campo." = '".dboescape(${$campo})."'";
					$res = dboQuery($sql);
					if(dboAffectedRows())
					{
						$lin = dboFetchObject($res);
						$tipo = $lin->tipo;
					}
				}
				$ext_mod = $_system['pagina_tipo'][$tipo]['extension_module'];
				if($ext_mod)
				{	
					$ext_mod = new $ext_mod();
					$sql_part = "
						LEFT JOIN ".$ext_mod->getTable()." ON
							".$ext_mod->getTable().".".$ext_mod->getPk()." = ".$obj->getTable().".id
					";
				}
				$sql = "
					SELECT * FROM ".$obj->getTable()."
					".$sql_part."
					WHERE ".$obj->getTable().".".$campo." = '".dboescape(${$campo})."';
				";
			}
			elseif($categorias)
			{
				//implementar depois.
			}

			//se de fato existe um modulo cliente...
			if($ext_mod)
			{
				//seta o objeto como host de dados
				$obj->{$obj->client_object_key} = $ext_mod;
				$obj->{$obj->client_object_key}->setHostObject($obj);
			}
		
			$obj->query($sql);

			return $obj;
		}

		function queryPaginas($params = array())
		{
			global $_system;
			extract($params);

			$part_where = array();

			//valores default
			$tipo = $tipo !== null ? $tipo : 'pagina';
			$order_by = $order_by !== null ? $order_by : 'titulo';
			$order = $order !== null ? $order : 'ASC';
			$limit = $limit !== null ? $limit : '';
			if($where) $part_where[] = $where;
			if($slug) $part_where[] = "slug = '".$slug."'";
			$custom_page = $tipo == 'pagina' ? false : true;

			//verifica se é um módulo extendido
			$ext_mod = $_system['pagina_tipo'][$tipo]['extension_module'];

			//se é extendido, seta o objeto host
			if($ext_mod)
			{
				$ext_mod = new $ext_mod();
				$this->{$this->client_object_key} = $ext_mod;
				$this->{$this->client_object_key}->setHostObject($this);
				$part_select_extended = ", ".$ext_mod->getTable().".*";
				$part_join_extended = "
					LEFT JOIN ".$ext_mod->getTable()." ON
						".$ext_mod->getTable().".".$ext_mod->getPk()." = ".$this->getTable().".id					
				";
			}
			
			//pegando todas as paginas por categorias
			if($cat || $custom_page)
			{
				$part_join_categorias = "
					".(!$cat ? "LEFT" : "")." JOIN pagina_categoria ON
						pagina_categoria.pagina = pagina.id 
						".($cat ? " AND pagina_categoria.categoria IN(".$cat.") " : "")."
				";
			}


			//variaveis do banco de dados
			$tabela = $this->getTable();

			//query parts
			$part_where[] = "status = 'publicado'";
			$part_where[] = "data <= '".dboNow()."'";
			$part_where[] = "tipo = '".$tipo."'";

			$sql = "
				SELECT 
					".($pagination ? "SQL_CALC_FOUND_ROWS" : "")."
					pagina.*
					".$part_select_extended."
					".($custom_page ? ", GROUP_CONCAT(pagina_categoria.categoria) AS categorias_ids" : "")."
				FROM 
					".$tabela."
					".$part_join_extended."
					".$part_join_categorias."
				WHERE
					".implode(" AND ", $part_where)."
				GROUP BY
					pagina.slug
				ORDER BY 
					".$order_by." ".$order."
				".($limit ? "LIMIT ".$limit : "")."
			";

			//se tiver paginação, coloca os limits.
			if($pagination) { $this->forcePagination($pagination); }

			$this->query($sql);
		}

		function mais()
		{
			return is_object($this->getClientObject(0)) ? $this->getClientObject(0) : false;
		}

		function slug()
		{
			return $this->slug;
		}
		
		function data($params = array())
		{
			extract($params);
			$formato = $formato !== null ? $formato : 'd/M/Y H:i';
			if($ago)
			{
				return '<span title="'.dboDate($formato, strtotime($this->data)).'">'.ago($this->data).'</span>';
			}
			return dboDate($formato, strtotime($this->data));
		}

		function titulo()
		{
			return $this->titulo;
		}

		function texto()
		{
			return dboContent($this->texto);
		}

		function resumo($size = 440, $params = array())
		{
			extract($params);
			$more = $more ? $more : ' [...]';
			if(strlen(trim($this->resumo)))
			{
				return textOnly($this->resumo);
			}
			else
			{
				return maxString(textOnly(strip_shortcodes(strip_tags($this->texto))), $size, array('more' => $more));
			}
		}

		function permalink()
		{
			global $_system;
			return SITE_URL;
		}

		static function startPaginaEngine()
		{
			if(thisPage().".php" == PAGINA_ENGINE_FILE)
			{
				pagina::paginaEngine();
			}
		}

		static function paginaEngine()
		{
			global $_system;
			global $_pagina;
			global $_pagina_backup;
			global $_category_tree;
			global $_pagina_tipo;
			global $_slug;

			$_slug = $_slug !== null ? $_slug : $_GET['slug'];

			if($_slug)
			{
				$_pagina_backup = false;

				//verificamos se a página existe tentando pegar seu tipo no banco
				$_pagina = new pagina();
				$_pagina->query("SELECT tipo FROM pagina WHERE slug = '".dboescape($_slug)."'");

				//a página existe no banco
				if($_pagina->size())
				{
					//fazemos a query completa
					queryPaginas(array(
						'slug' => dboescape($_slug),
						'tipo' => $_pagina->tipo,
					));
					//setando o tipo de página no contexto global
					$_pagina_tipo = $_pagina->tipo;
					//primeiro checa a especificidade por tipo e pagina
					if(file_exists($_pagina->tipo.'-'.$_pagina->slug.'.php'))
					{
						include($_pagina->tipo.'-'.$_pagina->slug.'.php');
						exit();
					}
					//fallback para a pagina padrão do tipo especificado
					elseif($_pagina->tipo != 'pagina' && file_exists($_pagina->tipo.'.php'))
					{
						include($_pagina->tipo.'.php');
						exit();
					}
				}
				//se não existe a página requisitada no banco, quer dizer que está tentando acessar algum outro arquivo. Então redirecionamos para ele.
				elseif(file_exists($_slug))
				{
					//isso dá loop infinito... consertar depois
					header("Location: ".SITE_URL."/".$_slug);
					exit();
				}
				elseif(file_exists('404.php'))
				{
					include('404.php');
					exit();
				}
				else
				{
					echo "404";
					exit();
				}
			}
		}			

		function renderStatusSelector($params = array())
		{
			extract($params);
			$active = $active ? $active : 'tudo';
			$total_geral = 0;

			$sql = "
				SELECT 
					COUNT(*) AS total,
					status
				FROM 
					".$this->getTable()."
					".((!hasPermission('all', 'pagina-'.$tipo))?(" AND autor = '".loggedUser()."'"):(''))."
				WHERE
					tipo = '".$tipo."'
				GROUP BY
					status
			";
			$res = dboQuery($sql);
			if(dboAffectedRows())
			{
				while($lin = dboFetchObject($res))
				{
					if($lin->status != 'lixeira')
					{
						$total_geral += $lin->total;
					}
					$status[$lin->status] = $lin->total;
				}
			}

			$status_order = array(
				'publicado' => 'Publicad'.$genero.'s',
				'agendado' => 'Agendad'.$genero.'s',
				'rascunho' => 'Rascunhos',
				'lixeira' => 'Lixeira'
			);

			ob_start();
			?>
			<dl class="sub-nav no-margin" id="list-status-selector">
				<dd class="<?= $active == 'tudo' ? 'active' : '' ?>"><a href="#" class="peixe-reload" peixe-reload="#list-table,.acoes-em-massa,#list-pagination,#list-pagination-bottom,.list-numero-itens,#list-search,#list-data-selector" data-keep-url="!pagina_status&!pag&!s&!m">Tudo <span class="font-12" style="font-weight: normal; opacity: .7;">(<?= $total_geral ?>)</span></a></dd>
				<?
					foreach($status_order as $status_slug => $status_titulo)
					{
						if(in_array($status_slug, array_keys($status_order)) && $status[$status_slug])
						{
							?>
							<dd class="<?= $active == $status_slug ? 'active' : '' ?>"><a href="#" class="peixe-reload" peixe-reload="#list-table,.acoes-em-massa,#list-pagination,#list-pagination-bottom,.list-numero-itens,#list-search,#list-data-selector" data-keep-url="pagina_status=<?= $status_slug ?>&!pag&!s&!m"><?= $status_titulo ?> <span class="font-12" style="font-weight: normal; opacity: .7;">(<?= $status[$status_slug] ?>)</span></a></dd>
							<?
						}
					}
				?>
			</dl>						
			<?
			return ob_get_clean();
		}

		function getUrlOrderBy($coluna)
		{
			return $this->keepUrl('order_by='.$coluna.'&order='.($_GET['order_by'] == $coluna && $_GET['order'] == 'ASC' ? 'DESC' : 'ASC'));
		}

		function getLinkOrderBy($coluna, $titulo)
		{
			if($_GET['order_by'] == $coluna)
			{
				if($_GET['order'] == 'DESC')
				{
					$icon = ' <i class="fa fa-caret-down"></i>';
				}
				else
				{
					$icon = ' <i class="fa fa-caret-up"></i>';
				}
			}
			return '<a href="'.$this->getUrlOrderBy($coluna).'" class="peixe-reload" peixe-reload="#list-table,#pagination,#list-view-selector">'.$titulo.$icon.'</a>';
		}

		//seta detalhes desta pagina no campo detail. Os detalhes são armazenados como um objeto JSON encodado
		function setDetail($key, $value)
		{
			$detail = json_decode($this->detail, true);
			$detail[$key] = $value;
			$this->detail = json_encode($detail);
		}

		function getDetail($key)
		{
			$detail = json_decode($this->detail, true);
			return $detail[$key];
		}

		function removeDetail($key)
		{
			$detail = json_decode($this->detail, true);
			unset($detail[$key]);
			$this->detail = json_encode($detail);
		}

		static function registerMenus(&$modulos)
		{
			global $_system;
			if(is_array($_system['pagina_tipo']) && sizeof($_system['pagina_tipo']))
			{
				foreach($_system['pagina_tipo'] as $slug => $details)
				{
					$key = safeArrayKey($details['order_by'], $modulos);
					
					if(file_exists(DBO_PATH."/../images/module_icons/pagina-".$slug.".png"))
					{
						$modulos[$key]['icon'] = 'pagina-'.$slug.".png";
					} else {
						$modulos[$key]['icon'] = "_icone_generico.png";
					}
					$modulos[$key]['titulo'] = ucfirst($details['titulo_plural']);
					$modulos[$key]['var'] = 'pagina-'.$slug;
					$modulos[$key]['custom_url'] = 'dbo_admin.php?dbo_mod=pagina&dbo_pagina_tipo='.$slug;
				}
			}
		}

		static function mandarParaLixeira($ids, $params = array())
		{
			$ids = (array)$ids;
			extract($params);

			$nro_excluidos = 0;

			if(sizeof($ids))
			{
				foreach($ids as $id)
				{
					$pag = new pagina($id);
					if(hasPermission('delete', 'pagina-'.$pag->tipo) && (hasPermission('all', 'pagina-'.$pag->tipo) || $pag->autor == loggedUser()))
					{
						$pag->setDetail('last_status', $pag->status);
						$pag->status = 'lixeira';
						$pag->deleted_on = dboNow();
						$pag->deleted_by = loggedUser();
						$pag->update();
						if($single)
						{
							return $pag->id;
						}
						$nro_excluidos++;
					}
				}
			}
			return $nro_excluidos;
		}

		static function excluirDefinitivamente($ids, $params = array())
		{
			$ids = (array)$ids;
			extract($params);

			$nro_excluidos = 0;

			if(sizeof($ids))
			{
				foreach($ids as $id)
				{
					$pag = new pagina($id);
					if(hasPermission('delete', 'pagina-'.$pag->tipo) && (hasPermission('all', 'pagina-'.$pag->tipo) || $pag->autor == loggedUser()))
					{
						$pag->forceDelete();
						if($single)
						{
							return $pag->id;
						}
						$nro_excluidos++;
					}
				}
			}
			return $nro_excluidos;
		}

		static function restaurarDaLixeira($ids, $params = array())
		{
			$ids = (array)$ids;
			extract($params);

			$nro_restaurados = 0;

			if(sizeof($ids))
			{
				foreach($ids as $id)
				{
					$pag = new pagina($id);
					if(hasPermission('delete', 'pagina-'.$pag->tipo) && (hasPermission('all', 'pagina-'.$pag->tipo) || $pag->autor == loggedUser()))
					{
						$pag->status = $pag->getDetail('last_status');
						$pag->removeDetail('last_status');
						$pag->deleted_on = $pag->null();
						$pag->deleted_by = 0;
						$pag->update();
						if($single)
						{
							return $pag->id;
						}
						$nro_restaurados++;
					}
				}
			}
			return $nro_restaurados;
		}

		static function renderMenuAdminStructure($pagina_tipo)
		{
			global $_system;
			ob_start();
			$pagina = new pagina("WHERE status = 'Publicado' AND deleted_by = 0 AND tipo = '".$pagina_tipo."' ORDER BY titulo");
			if($pagina->size())
			{
				?>
				<li class="accordion-navigation">
					<a href="#acc-<?= $pagina_tipo ?>"><?= ucfirst($_system['pagina_tipo'][$pagina_tipo]['titulo_plural']) ?></a>
					<div id="acc-<?= $pagina_tipo ?>" class="content">
						<ul class="no-bullet font-14 no-margin">
							<?
								do {
									?>
									<li><input type="checkbox" name="item-pagina[<?= $pagina->id ?>]" id="pagina-<?= $pagina->id ?>" data-titulo="<?= htmlSpecialChars($pagina->titulo()) ?>" data-slug="<?= $_system['pagina_tipo'][$pagina_tipo]['slug_prefix'] ? $_system['pagina_tipo'][$pagina_tipo]['slug_prefix']."/" : '' ?><?= $pagina->slug(); ?>" data-pagina_id="<?= $pagina->id ?>" data-tipo="pagina"/> <label for="pagina-<?= $pagina->id ?>"><?= $pagina->titulo(); ?></label></li>
									<?
								}while($pagina->fetch());
							?>
						</ul>
						<hr class="small">
						<div class="row">
							<div class="large-5 columns"><a href="#" class="trigger-selecionar-todas-paginas top-2">Selecionar tod<?= $_system['pagina_tipo'][$pagina_tipo]['genero'] ?>s</a></div>
							<div class="large-7 columns text-right"><span class="button radius small no-margin trigger-adicionar-paginas secondary">Adicionar ao menu <i class="fa-arrow-right fa"></i></span></div>
						</div>
					</div>
				</li>
				<?
			}
			return ob_get_clean();
		}

		function imagemUrl($params = array())
		{
			$params['size'] = $params['size'] ? $params['size'] : 'medium';
			extract($params);

			if($this->imagem_destaque)
			{
				$url = DBO_URL."/upload/dbo-media-manager/";
				$image = $this->imagem_destaque;
				if(file_exists(DBO_PATH."/upload/dbo-media-manager/thumbs/".$size.'-'.$image)) 
				{
					$url .= 'thumbs/';
					$image = $size.'-'.$image;
				} 
				$url .= $image;
			}
			else
			{
				preg_match('/<img.+src=[\'"](?P<src>.+)[\'"].*>/i', $this->texto, $image);
				$image = $image['src'];

				//tratamento do tamanho pedido pelo usuário
				if($size && $image)
				{
					//separa o arquivo da url
					$parts = explode('/', $image);
					$file = array_pop($parts);
					$url = implode('/', $parts).'/';
					
					//separa o tamanho do arquivo
					$parts = explode('-', $file);

					//verifica se era um arquivo composto
					if(sizeof($parts) > 1)
					{
						array_shift($parts);
						$parts = implode('-', $parts);

						//tenta achar o arquivo com tamanho espeficicado pelo usuário
						if(file_exists(filterMediaManagerPath($url.$size.'-'.$parts)))
						{
							$file = $size.'-'.$parts;
						}
					}
					$url = $url.$file;
				}
				else
				{
					$url = $image;
				}
				$url = filterMediaManagerUrl($url);
			}
			if(!$url && $show_placeholder)
			{
				$url = DBO_IMAGE_PLACEHOLDER;
			}
			return $url;
		}

		function imagem($params = array())
		{
			extract($params);
			$image = $this->imagemUrl($params);
			if($image)
			{
				return '<img class="'.$classes.'" src="'.$image.'"/>';
			}
		}

		function imagemAjustada($params = array())
		{
			$image = $this->imagemUrl($params);
			return imagemAjustada($image, $params);
		}

		function getCategoryIds($params = array())
		{
			extract($params);
			$join = $this->getDetails('categoria')->join;

			$result = array();

			$sql = "SELECT ".$join->chave2." FROM ".$join->tabela_ligacao." WHERE ".$join->chave1." = '".$this->id."';";
			$res = dboQuery($sql);
			if(dboAffectedRows())
			{
				while($lin = dboFetchObject($res))
				{
					$result[] = $lin->{$join->chave2};
				}
			}
			return $result;
		}

		function addCategoria($cat_id)
		{
			$join = $this->getDetails('categoria')->join;
			$tabela = $join->tabela_ligacao;
			$sql = "INSERT INTO ".$tabela." (pagina, categoria) VALUES ('".$this->id."', '".$cat->id."')";
			dboQuery($sql);
		}

		function removeCategoria($cat_id)
		{
			$join = $this->getDetails('categoria')->join;
			$tabela = $join->tabela_ligacao;
			$sql = "DELETE FROM ".$tabela." WHERE pagina = '".$this->id."' AND categoria = '".$cat_id."'";
			dboQuery($sql);
		}

		function categorias($separator = null, $params = array())
		{
			/*
			* @params
			*  somente_folhas: true | false - retorna somente as categorias mais específicas
			*  classes: ... - classes CSS separadas por espaço
			*/
			global $_category_tree;
			extract($params);
			if($this->categorias_ids != 0)
			{
				$links = array();
				if(!$_category_tree[$this->tipo])
				{
					$_category_tree[$this->tipo] = categoria::getCategoryStructure($this->tipo);
				}
				$categorias_ids = explode(',', $this->categorias_ids);
				foreach($categorias_ids as $cat_id)
				{
					$cat_info = categoria::getCategoriaInfo($cat_id, $_category_tree[$this->tipo], $params);
					if($cat_info)
					{
						$links[$cat_info['slug']] = '<a '.($classes ? 'classes="'.$classes.'"' : '').' href="'.SITE_URL.'/'.$cat_info['full_slug'].'">'.ucfirst($cat_info['nome']).'</a>';
					}
				}
				if($separator)
				{
					return implode($separator, $links);
				}
				return $links;
			}
			return false;
		}

	} //class declaration
} //if ! class exists

//definindo o nome padrão para o arquivo de processamento de página
define(PAGINA_ENGINE_FILE, 'pagina.php');

//funções para templating
function pagina()
{
	global $_pagina;
	return $_pagina;
}

function paginaCategorias($separator = null, $params = array())
{
	global $_pagina;
	return $_pagina->categorias($separator, $params);
}

function paginaData($params = array())
{
	global $_pagina;
	return $_pagina->data($params);
}

function paginaSplitter($rest = null, $params = array())
{
	global $_pagina;
	return $_pagina->splitter($rest, $params);
}

function paginaTitulo()
{
	global $_pagina;
	return $_pagina->titulo();
}

function paginaTipo()
{
	global $_pagina_tipo;
	return $_pagina_tipo;
}

function paginaId()
{
	global $_pagina;
	return $_pagina->id;
}

function paginaSlug()
{
	global $_pagina;
	return $_pagina->slug();
}

function paginaSubtitulo()
{
	global $_pagina;
	return $_pagina->subtitulo;
}

function paginaResumo($size = 440, $params = array())
{
	global $_pagina;
	extract($params);
	return $_pagina->resumo($size, $params);
}

function paginaTexto()
{
	global $_pagina;
	return $_pagina->texto();
}

function paginaPermalink()
{
	global $_pagina;
	global $_system;

	$slug_prefix = $_system['pagina_tipo'][$_pagina->tipo]['slug_prefix'];
	return $_pagina->permalink().'/'.($slug_prefix ? $slug_prefix.'/' : '').$_pagina->slug();
}

function paginaImagem($params = array())
{
	global $_pagina;
	return $_pagina->imagem($params);
}

function paginaImagemAjustada($params = array())
{
	global $_pagina;
	return $_pagina->imagemAjustada($params);
}

function paginaImagemUrl($params = array())
{
	global $_pagina;
	return $_pagina->imagemUrl($params);
}

function queryPaginas($params = array())
{
	global $_pagina;
	global $_pagina_backup;
	
	if($_pagina_backup === false)
		$_pagina_backup = clone $_pagina;

	return $_pagina->queryPaginas($params);
}

function resetQueryPaginas()
{
	global $_pagina;
	global $_pagina_backup;

	if($_pagina_backup !== false)
	{
		$_pagina = clone $_pagina_backup;
		$_pagina_backup = false;
	}
}

function listaPaginas()
{
	global $_pagina;
	return $_pagina->fetch();
}

function haPaginas()
{
	global $_pagina;
	return $_pagina->size();
}

function breadcrumbs($params = array())
{
	extract($params);
	/*
		@params
		 prepend: array - adiciona elementos no início do breadcrumb
	*/
	$bca = breadcrumbsArray($params);
	if(sizeof($bca))
	{
		?>
		<ul class="breadcrumbs <?= $classes ?>" style="<?= $styles ?>">
			<?php
				foreach($bca as $data)
				{
					?>
					<li class="<?= end(explode('/', $_GET['slug'])) == $data['slug'] ? 'current' : '' ?>"><a href="<?= $data['url'] ?>"><?= $data['text'] ?></a></li>
					<?php
				}
				if($extended && is_array($data['children']))
				{
					?>
					<li>
						<?php
							foreach($data['children'] as $ext)
							{
								?><a href="<?= $ext['url'] ? $ext['url'] : $ext['full_slug'] ?>"><?= $ext['text'] ? $ext['text'] : ($ext['nome'] ? $ext['nome'] : $ext['titulo']) ?></a><?php
								if(end($data['children']) != $ext) echo "<br />";
							}
						?>
					</li>
					<?php
				}
			?>
		</ul>		
		<?php
	}
}

function breadcrumbsArray($params = array())
{
	global $_category_tree;
	global $_pagina_tipo;
	extract($params);

	$bca = array();

	if($append)
	{
		$bca = $append;
	}

	if(thisPage() == 'categoria' && $_GET['slug'])
	{
		$parts = explode('/', $_GET['slug']);
		if(sizeof($parts))
		{
			foreach($parts as $slug)
			{
				$info = categoria::getCategoriaInfo($slug, $_category_tree[$_pagina_tipo], array(
					'key' => 'slug',
				));
				$bca[] = array(
					'slug' => $info['slug'],
					'url' => $info['full_slug'],
					'text' => $info['nome'],
					'children' => $info['children'],
				);
			}
		}
	}
	return $bca;
}

function siteDescricao()
{
	global $_conf;
	return $_conf->site_descricao;
}

function siteHead($params = array())
{
	global $_conf;
	extract($params);

	$og = array();	
	
	ob_start();

	//locale é sempre a mesma coisa
	$og['og:locale'] = 'pt_BR';

	//checando se estamos em uma página
	if(thisPage().'.php' == PAGINA_ENGINE_FILE)
	{
		//se é home
		if(paginaSlug() == 'home')
		{
			$og['og:type'] = 'website';
			$og['og:title'] = siteTitulo();
			$og['og:description'] = siteDescricao();
			$og['og:url'] = SITE_URL;
			$og['og:image'] = SITE_URL.'/images/og.png';
		}
		else
		{
			$og['og:type'] = 'article';
			$og['og:title'] = siteTitulo();
			$og['og:description'] = paginaResumo();
			$og['og:url'] = paginaPermalink();
			$og['og:image'] = paginaImagemUrl(array('size' => 'medium'));
		}
	}

	$og['og:site_name'] = $_conf->site_titulo;

	foreach($og as $prop => $content)
	{
		?>
		<meta property="<?= $prop ?>" content="<?= $content ?>" />
		<?php
	}

	return ob_get_clean();
}

//hook para criação dinamica das paginas
global $hooks;
$hooks->add_action('dbo_includes_after', 'pagina::startPaginaEngine');

//definindo os tipos de páginas deste sistema.
global $_system;
$_system['pagina_tipo']['pagina'] = array(
	'tipo' => 'pagina',
	'titulo' => 'página',
	'titulo_plural' => 'páginas',
	'genero' => 'a'
);

function siteTitulo($titulo = false, $params = array())
{
	global $_conf;
	global $_pagina;

	extract($params);

	$separator = (($separator)?($separator):('|'));

	config::init();

	$retorno = '';

	//se existe uma página instanciada, colocar o titulo dela antes do titulo do site.
	if(is_object($_pagina) && $_pagina->slug() != 'home')
	{
		$retorno = paginaTitulo()." ".$separator." ";
	}

	//se existe uma titulo setado nas configurações, usa ele.
	if(strlen(trim($_conf->getSiteTitulo())))
	{
		$retorno .= $_conf->getSiteTitulo();
	}
	else
	{
		$retorno .= $titulo;
	}

	return $retorno;
}

function siteConfig()
{
	global $_conf;
	return $_conf;
}

function siteBodyClass($params = array())
{
	extract($params);
}

//auto admin das páginas, realiza toda a lógica para as páginas do sistema. Override da função no DBO.
function auto_admin_pagina($params = array())
{
	require_once(DBO_PATH.'/core/dbo-pagina-admin.php');
	return autoAdminPagina($params);
}

?>
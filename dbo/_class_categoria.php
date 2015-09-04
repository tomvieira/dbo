<?

/* ================================================================================================================== */
/* DBO CLASS FILE FOR MODULE 'categoria' ======================================== AUTO-CREATED ON 20/07/2015 13:52:00 */
/* ================================================================================================================== */

/* IMPORTANT: This file is generated only in the first DBO sync, what means you should edit only via text editor. */

if(!class_exists('categoria'))
{
	class categoria extends dbo
	{
		/* smart constructor: will perform load() upon numeric argument and loadAll() upon string argument */
		function __construct($foo = '')
		{
			parent::__construct('categoria');
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

		//sobrecarga
		function save()
		{
			$id = parent::save();
			if(is_numeric($this->mae))
			{
				//tira a flag de folha do pai, se for o caso.
				$cat = new categoria($this->mae);
				if($cat->folha == 1)
				{
					$cat->folha = 0;
					$cat->update();
				}
			}
			return $id;
		}

		static function getCategoryStructure($pagina_tipo = null, $params = array())
		{
			global $_system;
			extract($params);

			$tree = array();

			$cat = new categoria();
			$sql = "
				SELECT * FROM ".$cat->getTable()." 
				WHERE 
					mae ".($mae ? " = '".$mae."' " : " IS NULL AND 
					pagina_tipo = '".$pagina_tipo."' ")." 
				ORDER BY nome
			";
			$cat->query($sql);
			if($cat->size())
			{
				do {
					$dados = array(
						'id' => $cat->id,
						'slug' => $cat->slug,
						'nome' => $cat->nome,
						'full_slug' => $_system['pagina_tipo'][$pagina_tipo]['slug_prefix'].'/categorias/'.$cat->slug,
					);
					if($cat->folha == 0)
					{
						$params['full_slug_prefix'] = $_system['pagina_tipo'][$pagina_tipo]['slug_prefix'].'/categorias/'.$cat->slug.'/';
						$dados['children'] = categoria::getCategoryChildren($cat, $params);
					}
					$tree[] = $dados;
				}while($cat->fetch());
			}
			return $tree;
		}

		static function getCategoryChildren($cat, $params = array())
		{
			extract($params);

			$tree = array();

			$filha = new categoria();
			$sql = "
				SELECT * FROM ".$cat->getTable()." 
				WHERE 
					mae = '".$cat->id."'
				ORDER BY nome;
			";
			$filha = new categoria();
			$filha->query($sql);
			if($filha->size())
			{
				do {
					$dados = array(
						'id' => $filha->id,
						'slug' => $filha->slug,
						'nome' => $filha->nome,
						'full_slug' => $full_slug_prefix.$filha->slug,
					);
					if($filha->folha == 0)
					{
						$params['full_slug_prefix'] = $full_slug_prefix.$filha->slug.'/';
						$dados['children'] = categoria::getCategoryChildren($filha, $params);
					}
					$tree[] = $dados;
				}while($filha->fetch());
			}
			return $tree;
		}

		static function startCategoriaEngine()
		{
			if(thisPage().".php" == CATEGORIA_ENGINE_FILE)
			{
				categoria::categoriaEngine();
			}
		}
		
		static function categoriaEngine()
		{
			if(thisPage().".php" == CATEGORIA_ENGINE_FILE)
			{
				global $_system;
				global $_pagina;
				global $_pagina_backup;
				global $_category_tree;
				global $_pagina_tipo;
				global $_categoria;
				//situação para categorias especificas
				if($_GET['slug'])
				{
					$slug = preg_replace('/\/$/is', '', $_GET['slug']);
					$slug = explode("/", $slug);
					$slug = array_pop($slug);
					$_categoria = new categoria("WHERE slug = '".dboescape($slug)."'");
					if($_categoria->size())
					{
						$_pagina = new pagina();
						$_pagina_tipo = $_categoria->pagina_tipo;
						$_category_tree[$_categoria->pagina_tipo] = categoria::getCategoryStructure($_pagina_tipo);
						queryPaginas(array(
							'cat' => $_categoria->id,
							'tipo' => $_pagina_tipo,
						));
						//testa especificidade pelas cutomizações do tempalte
						if(file_exists($_pagina_tipo.'-categoria-'.$_categoria->slug.'.php'))
						{
							include($_pagina_tipo.'-categoria-'.$_categoria->slug.'.php');
							exit();
						}
						elseif(file_exists($_pagina_tipo.'-categoria.php'))
						{
							include($_pagina_tipo.'-categoria.php');
							exit();
						}
					}
					//se não existe a página requisitada no banco, quer dizer que está tentando acessar algum outro arquivo. Então redirecionamos para ele.
					elseif(file_exists($_GET['slug']))
					{
						//isso dá loop infinito... consertar depois
						header("Location: ".SITE_URL."/".$_GET['slug']);
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
				elseif($_GET['slug_prefix'])
				{
					foreach($_system['pagina_tipo'] as $tipo => $data)
					{
						if($data['slug_prefix'] == $_GET['slug_prefix'])
						{
							$_pagina = new pagina();
							$_pagina_tipo = $tipo;
							queryPaginas(array(
								'tipo' => $tipo,
							));
							$_category_tree[$tipo] = categoria::getCategoryStructure($tipo);

							//testa especificidade pelas cutomizações do tempalte
							if(file_exists($_pagina_tipo.'-categorias.php'))
							{
								include($_pagina_tipo.'-categorias.php');
								exit();
							}
							elseif(file_exists($_pagina_tipo.'-categoria.php'))
							{
								include($_pagina_tipo.'-categoria.php');
								exit();
							}
						}
					}
				}
			}
		}			

		static function getCategoriaInfo($cat_id, $tree, $params = array())
		{
			extract($params);
			/*
				@params
				 key: id | slug
			*/

			//valores default
			$key = $key !== null ? $key : 'id';

			//busca recursiva pelas informações deste id
			if(sizeof($tree))
			{
				foreach($tree as $node)
				{
					if($node[$key] == $cat_id)
					{
						//se for para retornar somente folhas, mas este tiver filhos, não retorna.
						if($somente_folhas && is_array($node['children']))
						{
							return false;
						}
						return $node;
					}
					elseif(is_array($node['children']))
					{
						$return = categoria::getCategoriaInfo($cat_id, $node['children'], $params);
					}

					if($return)
					{
						return $return;
					}
				}
			}
		}

		static function renderTreeMenu($tree, $params = array())
		{
			extract($params);
			if(sizeof($tree))
			{
				ob_start();
				?>
				<ul class="<?= $classes ?>">
					<?php
						foreach($tree as $node)
						{
							?>
							<li><a href="<?= SITE_URL ?>/<?= $node['full_slug'] ?>"><?= $node['nome'] ?></a>
							<?php
							if(is_array($node['children']))
							{
								$params['classes'] .= ' children ';
								echo categoria::renderTreeMenu($node['children'], $params);
							}
							?>
							</li>
							<?php
						}
					?>
				</ul>
				<?php
				return ob_get_clean();
			}
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
	
	} //class declaration
} //if ! class exists

//definindo o nome padrão para o arquivo de processamento de página
define(CATEGORIA_ENGINE_FILE, 'categoria.php');

//hook para criação categorias dinâmicas
global $hooks;
$hooks->add_action('dbo_includes_after', 'categoria::startCategoriaEngine');

//funções para templating

function carregaArvoreDeCategorias($pagina_tipo = null, $params = array())
{
	global $_category_tree;
	$_category_tree[$pagina_tipo] = categoria::getCategoryStructure($pagina_tipo, $params);
}

function arvoreDeCategorias($pagina_tipo = null)
{
	global $_category_tree;
	if($pagina_tipo)
	{
		return $_category_tree[$pagina_tipo];
	}
	return $_category_tree;
}

function categoriaNome()
{
	global $_categoria;
	return $_categoria->nome;
}

function haSubcategorias()
{
	global $_categoria;
	global $_category_tree;
	global $_pagina_tipo;
	$foo = categoria::getCategoriaInfo($_categoria->id, $_category_tree[$_pagina_tipo]);
	return is_array($foo['children']);
}

function subcategorias($separator, $params = array())
{
	global $_categoria;
	global $_category_tree;
	global $_pagina_tipo;
	$cats = categoria::getCategoriaInfo($_categoria->id, $_category_tree[$_pagina_tipo]);
	$cats = $cats['children'];
	foreach($cats as $cat)
	{
		$return .= '<a href="'.$cat['full_slug'].'">'.$cat['nome'].'</a>'.(end($cats) != $cat ? $separator : '');
	}
	return $return;
}

?>
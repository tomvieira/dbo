<?

/* ================================================================================================================== */
/* DBO CLASS FILE FOR MODULE 'pagina' =========================================== AUTO-CREATED ON 11/06/2015 01:53:43 */
/* ================================================================================================================== */

/* IMPORTANT: This file is generated only in the first DBO sync, what means you should edit only via text editor. */

if(!class_exists('pagina'))
{
	class pagina extends dbo
	{
		/* smart constructor: will perform load() upon numeric argument and loadAll() upon string argument */
		function __construct($foo = '')
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

		function getSlug()
		{
			return $this->slug;
		}
		
		function getTitulo()
		{
			return $this->titulo;
		}

		static function titulo()
		{
			global $_pagina;
			return $_pagina->getTitulo();
		}

		static function slug()
		{
			global $_pagina;
			return $_pagina->getSlug();
		}

		static function subtitulo()
		{
			global $_pagina;
			return $_pagina->subtitulo;
		}

		static function texto()
		{
			global $_pagina;
			return dboContent($_pagina->texto);
		}

		static function paginaEngine()
		{
			if(thisPage().".php" == PAGINA_ENGINE_FILE)
			{
				global $_pagina;
				if($_GET['slug'])
				{
					$_pagina = new pagina("WHERE slug = '".$_GET['slug']."'");
					if($_pagina->size())
					{
						if(file_exists('pagina-'.$_pagina->slug.'.php'))
						{
							//inlcui a página personalizada
							include('pagina-'.$_pagina->slug.'.php');
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
			}
		}			

	} //class declaration
} //if ! class exists

//definindo o nome padrão para o arquivo de processamento de página
if(!defined(PAGINA_ENGINE_FILE))
{
	define(PAGINA_ENGINE_FILE, 'pagina.php');
}

//hook para criação dinamica das paginas
global $hooks;
$hooks->add_action('dbo_post_includes', 'pagina::paginaEngine');

function auto_admin_paginassss($obj)
{
	ob_start();
	?>
	<div class="row">
		<div class="large-9 columns">
			<div class="breadcrumb">
				<ul class="no-margin">
					<li><a href="cadastros.php">Cadastros</a></li>
					<li><a href="#">Páginas</a></li>
				</ul>
			</div>
		</div>
		<div class="large-3 columns text-right">
			<?= ((hasPermission('insert', 'pagina'))?('<button class="button small radius no-margin top-less-15"><i class="fa fa-plus"></i> Nova página</button>'):('')) ?>
		</div>
	</div>
	<hr class="small">
	<?
	return ob_get_clean();
}

function list_paginasssss($obj)
{
	ob_start();
	?>
	<div class="row full">
		<div class="large-12 columns">
			<div id="list-pagina">
				<table class="responsive list">
					<thead>
						<tr>
							<th>asdasd</th>
						</tr>
					</thead>
					<tbody>
						<tr class="pointer">
							<td>asdasd</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<?
	return ob_get_clean();
}

function form_paginasssss_update($obj)
{
	ob_start();
	?>
	:D
	<?
	return ob_get_clean();
}

function form_paginasssss_insert($obj)
{
	ob_start();
	?>
	:)
	<?
	return ob_get_clean();
}

?>
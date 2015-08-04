<?

/* ================================================================================================================== */
/* DBO CLASS FILE FOR MODULE 'menu' ============================================= AUTO-CREATED ON 12/06/2015 16:49:15 */
/* ================================================================================================================== */

/* IMPORTANT: This file is generated only in the first DBO sync, what means you should edit only via text editor. */

if(!class_exists('menu'))
{
	class menu extends dbo
	{
		/* smart constructor: will perform load() upon numeric argument and loadAll() upon string argument */
		function __construct($foo = '')
		{
			parent::__construct('menu');
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

		static function gerarDDItemTemplate($data)
		{
			extract($data);

			//criando a string de data-attributes para o item
			foreach($data as $key => $value)
			{
				$str_data .= 'data-'.$key.'="'.htmlSpecialChars($value).'" ';
			}

			ob_start();
			?>
			<li class="dd-item" <?= $str_data ?>>
				<div class="dd-handle"><?= $titulo ?></div>
				<div class="dd-tipo closed"><?= (($tipo == 'pagina')?('Página'):((($tipo == 'link')?('Link'):('')))) ?> <i class="fa fa-fw fa-caret-down icon-open"></i><i class="fa fa-fw fa-caret-up icon-closed"></i></div>
				<div class="panel dd-detalhes hide">
					<div class="row">
						<div class="large-6 columns">
							<label><?= $titulo ?></label>
							<input type="text" name="titulo" value="<?= htmlSpecialChars($titulo) ?>"/>
						</div>
						<div class="large-6 columns">
							<label>Classes CSS</label>
							<input type="text" name="classes" value="<?= htmlSpecialChars($classes) ?>"/>
						</div>
					</div>
					<?
						if($tipo == 'link')
						{
							?>
							<div class="row">
								<div class="large-12 columns">
									<label for="">URL do link</label>
									<input type="text" name="url" value="<?= htmlSpecialChars($url) ?>"/>
								</div>
							</div>
							<?
						}
					?>
					<div class="row">
						<div class="large-12 columns text-right">
							<?
								if($tipo == 'pagina')
								{
									?>
									<a href="<?= SITE_URL ?>/<?= $slug ?>" target="_blank">Visualizar página</a>
									<?
								}
								elseif($tipo == 'link')
								{
									?>
									<a href="<?= $url ?>" target="_blank">Acessar Link</a>
									<?
								}
							?>
							| <a href="#">Excluir menu</a>
						</div>
					</div>
				</div>
			</li>			
			<?
			return ob_get_clean();
		}

	} //class declaration
} //if ! class exists

function auto_admin_menu()
{
	ob_start();
	
	echo dboImportJs('nestable');

	?>
	<div class="row">
		<div class="large-12 columns">
			<div class="breadcrumb">
				<ul class="no-margin">
					<li><a href="cadastros.php">Cadastros</a></li>
					<li><a href="#">Menus</a></li>
				</ul>
			</div>
		</div>
	</div>
	<hr>
	
	<div class="row almost full">
		<div class="large-4 columns">
			<ul class="accordion" data-accordion>
				<?
					if(class_exists('pagina'))
					{
						$pagina = new pagina("WHERE status = 'Publicado' ORDER BY titulo");
						if($pagina->size())
						{
							?>
							<li class="accordion-navigation">
								<a href="#acc-paginas">Páginas</a>
								<div id="acc-paginas" class="content active">
									<ul class="no-bullet font-14 no-margin">
										<?
											do {
												?>
												<li><input type="checkbox" name="item-pagina[<?= $pagina->id ?>]" id="pagina-<?= $pagina->id ?>" data-titulo="<?= htmlSpecialChars($pagina->getTitulo()) ?>" data-slug="<?= $pagina->getSlug(); ?>" data-pagina_id="<?= $pagina->id ?>" data-tipo="pagina"/> <label for="pagina-<?= $pagina->id ?>"><?= $pagina->getTitulo(); ?></label></li>
												<?
											}while($pagina->fetch());
										?>
									</ul>
									<hr class="small">
									<div class="row">
										<div class="large-5 columns"><a href="#" class="trigger-selecionar-todas-paginas top-2">Selecionar todas</a></div>
										<div class="large-7 columns text-right"><span class="button radius small no-margin trigger-adicionar-paginas secondary">Adicionar ao menu <i class="fa-arrow-right fa"></i></span></div>
									</div>
									<script>
										$(document).ready(function(){
								
											$(document).on('click', '.trigger-adicionar-paginas', function(){
												pags = $('input[name^="item-pagina"]:checked');
												if(pags.length){
													pags.each(function(){
														adicionarDDItem($(this).data());
													})
												}
												else {
													alert('Selecione uma ou mais páginas da lista para adicionar ao menu ativo');
												}
											});
								
											$(document).on('click', '.trigger-selecionar-todas-paginas', function(e){
												e.preventDefault();
												$('input[name^="item-pagina"]:not("checked")').each(function(){
													$(this).attr('checked', true);
												})
											});
								
										}) //doc.ready
									</script>
								</div>
							</li>
							<?
						}
					}
				?>
				<li class="accordion-navigation">
					<a href="#acc-links">Links personalizados</a>
					<div id="acc-links" class="content">
						<div class="row">
							<div class="large-12 columns">
								<label>Url</label>
								<input type="text" name="url" value="" placeholder="http://"/>
							</div>
							<div class="large-12 columns">
								<label>Texto do link</label>
								<input type="text" name="titulo" value=""/>
							</div>
						</div>
						<hr class="small">
						<div class="row">
							<div class="large-12 columns text-right"><span class="button radius small no-margin trigger-adicionar-link secondary">Adicionar ao menu <i class="fa-arrow-right fa"></i></span></div>
						</div>
					</div>
				</li>
			</ul>
		</div>
		<div class="large-8 columns" id="right-panel">
			<div class="row">
				<div class="large-8 columns">
					<dl class="sub-nav no-margin top-4" id="nav-menus-disponiveis">
						<dt>Menus:</dt>
						<?
							$men = new menu("ORDER BY id");
							if($men->size()) {
								do {
									?>
									<dd class="<?= (($men->getIterator() == $men->size())?('active'):('')) ?>" data-menu_id="<?= $men->id ?>"><a href="<?= $men->slug ?>"><?= $men->nome ?></a></dd>
									<?
								}while($men->fetch());								
							}
						?>
					</dl>			
				</div>
				<div class="large-4 columns text-right">
					<?
						if(hasPermission('insert', 'menu'))
						{
							?>
							<span class="button radius small no-margin top-less-11" id="button-cadastrar-novo"><i class="fa fa-plus fa-fw"></i> Cadastrar novo</span>
							<?
						}
					?>
					<span class="button radius small no-margin top-less-11 secondary" id="button-voltar" style="display: none;"><i class="fa fa-fw fa-arrow-left"></i> Voltar</span>
				</div>
			</div>
			<hr class="small">
			<div id="form-menu-update">
				<?
					//mostrando o seletor de menus, se houver menu disponivel
					if($men->size())
					{
						?>
							<div class="dd">
								<ol class="dd-list" style="min-height: 200px;"></ol>
							</div>
							<hr>
							<div class="row">
								<div class="large-12 columns text-right">
									<a href="#" id="button-excluir-menu">Excluir menu</a> &nbsp;&nbsp;&nbsp;&nbsp;
									<span class="button radius" id="button-salvar-menu">Salvar menu</span>
								</div>
							</div>
						<?
					}
					else
					{
						?>
						<h3 class="text-center" style="padding-top: 5em;" id="msg-nao-ha-menus">- Não há menus cadastrados- </h3>
						<?
					}
				?>
			</div>
			<form method="post" action="ajax-dbo-menu.php?action=novo-menu" class="no-margin peixe-json" id="form-novo-menu" style="display: none;" peixe-log>
				<div class="row">
					<div class="large-8 columns item">
						<label for="input-novo-menu-nome">Digite o nome do novo menu</label>
						<input type="text" name="nome" id="input-novo-menu-nome" value="" required/>
					</div>
				</div>
				<div class="row">
					<div class="large-12 columns">
						<input class="button radius" type="submit" value="Inserir menu"/>
					</div>
				</div>
			</form>
		</div>
	</div>
	<script>
		
		function updateDDItemTitulo(dd_item) {
			dd_item.find('.dd-handle').text(dd_item.find('input[name="titulo"]').val());
		}

		function adicionarDDItem(data) {
			peixeJSON('ajax-dbo-menu.php?action=gerar-dd-item', data, '', false);
			$('input[name^="item-pagina"]:checked').each(function(){
				$(this).attr('checked', false);
			});
		}

		$(document).ready(function(){

			$('.dd').nestable({ /* config options */ });

			$(document).on('click', '.trigger-serialize', function(){
				console.log($('.dd').nestable('serialize'));
			});

			$(document).on('click', '.dd-tipo', function(){
				clicado = $(this);
				if(clicado.hasClass('open')){
					clicado.removeClass('open').addClass('closed').closest('.dd-item').find('.dd-detalhes').slideUp();
				} else {
					clicado.removeClass('closed').addClass('open').closest('.dd-item').find('.dd-detalhes').slideDown();
				}
			});

			//atualizando os data-attributes no change
			$(document).on('change', '.dd-item input', function(){
				mudado = $(this);
				dd_item = mudado.closest('.dd-item');
				dd_item.data(mudado.attr('name'), mudado.val());
				updateDDItemTitulo(dd_item);
			});

			//excluindo os menus
			$(document).on('click', '.trigger-excluir-menu', function(e){
				e.preventDefault();
				var ans = confirm("Tem certeza que deseja excluir este item do menu?");
				if (ans==true) {
					clicado = $(this);
					clicado.closest('.dd-item').fadeOut(function(){
						$(this).remove();
					})
				} 
			});

			//enviando links personalizados ao menu
			$(document).on('click', '.trigger-adicionar-link', function(){
				clicado = $(this);
				url = clicado.closest('.content').find('input[name="url"]').val();
				titulo = clicado.closest('.content').find('input[name="titulo"]').val();
				if($.trim(titulo) != '' && $.trim(url) != ''){
					adicionarDDItem({
						tipo: 'link',
						titulo: titulo,
						url: url
					})
				}
				else {
					alert('Preencha um título e uma url para adicionar o item ao menu');
				}
			});

			//mostrando o formulário para inserir novo menu
			$(document).on('click', '#button-cadastrar-novo', function(){
				clicado = $(this);
				clicado.fadeOut('fast', function(){
					$('#button-voltar').fadeIn('fast');
				})
				$('#form-menu-update').fadeOut('fast', function(){
					$('#form-novo-menu').fadeIn('fast', function(){
						$('#input-novo-menu-nome').focus();
					});
				})
			});

			$(document).on('click', '#button-voltar', function(){
				clicado = $(this);
				clicado.fadeOut('fast', function(){
					$('#button-cadastrar-novo').fadeIn('fast');
				})
				$('#form-novo-menu').fadeOut('fast', function(){
					$('#form-menu-update').fadeIn('fast');
				})
			});			

		}) //doc.ready
	</script>
	<?
	return ob_get_clean();
}

?>
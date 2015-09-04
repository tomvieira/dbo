<? require('header.php') ?>
<?php require_once(DBO_PATH.'/core/dbo-ui.php'); ?>
<? require_once('auth.php') ?>
<?
	if(!hasPermission('painel-relatorios'))
	{
		setMessage("<div class='error'>Você não tem permissão para acessar o painel de relatórios</div>");
		header("Location: index.php");
	}
?>

<div class='row'>
	<div class='large-12 columns'>
		<h3>Relatórios</h3>
	</div>
</div><!-- row -->

<div class='row'>
	<div class='large-4 columns'>
		<ul class="side-nav" id="sub-nav-relatorios">
			<li class="divider"></li>
			<li><a href="#relatorio-avaliacoes">Relatório de Avaliações</a></li>
			<li class="divider"></li>
			<li><a href="#relatorio-servicos">Relatório Total de Serviços</a></li>
			<li class="divider"></li>
			<li><a href="#relatorio-servidores">Relatório Servidores e Prestadores</a></li>
			<li class="divider"></li>
		</ul>
	</div><!-- col -->
	<div class='large-8 columns'>
		
		<div class='row item-relatorio'>
			<div class='large-12 columns'>
				<div class="hide-for-small">
					<div class="helper arrow-left" style='position: relative; top: 1em;'>
						Selecione um relatório ao lado.
					</div>
				</div>
			</div><!-- col -->
		</div><!-- row -->
		

		<!-- relatorio de avaliações dos usuarios -->
		<div id="relatorio-avaliacoes" class="hidden item-relatorio">
			<form action="rel-avaliacoes.php" class="no-margin">
				<div class="row">
					<div class="large-12 columns item">
						<label for="">Local</label>
						<?php
							$obj = new equipamento();
							echo $obj->getFormElement('insert', 'local', array(
								'required' => false,
							));
						?>
					</div>
				</div>
				<div class='row'>
					<div class='large-6 columns'>
						<label>Data Inicial</label>
						<input type='date' name='data_i' value=""/>
					</div><!-- col -->
					<div class='large-6 columns'>
						<label>Data Final</label>
						<input type='date' name='data_f' value=""/>
					</div>
				</div>
				<div class='row'>
					<div class="large-6 columns">
						<ul style='padding-left: 1em;'>
							<li><a href="" class="trigger-filtro-relatorio" data_i="<?= date('Y-m-01') ?>" data_f="">Mês atual</a></li>
							<li><a href="" class="trigger-filtro-relatorio" data_i="<?= somaDataAMD(date('Y-m-01'), 0, -1, 0) ?>" data_f="<?= somaDataAMD(date('Y-m-31'), 0, -1, 0) ?>">Mês anterior</a></li>
							<li><a href="" class="trigger-filtro-relatorio" data_i="<?= somaDataAMD(date('Y-m').'-01', 0, -3, 0) ?>" data_f="">3 meses</a></li>
							<li><a href="" class="trigger-filtro-relatorio" data_i="<?= somaDataAMD(date('Y-m').'-01', 0, -6, 0) ?>" data_f="">6 meses</a></li>
							<li><a href="" class="trigger-filtro-relatorio" data_i="<?= somaDataAMD(date('Y-m').'-01', 0, -12, 0) ?>" data_f="">12 meses</a></li>
							<li><a href="" class="trigger-filtro-relatorio" data_i="<?= date('2013-09-01') ?>" data_f="">Total</a></li>
						</ul>
					</div><!-- col -->
					<div class='large-6 columns text-right'>
						<input type='submit' class="button radius" name='' value="Gerar Relatório"/>
					</div><!-- col -->
				</div><!-- row -->
			</form><!-- row -->
		</div>

		<div id="relatorio-servicos" class="hidden item-relatorio">
			<form action="rel-servicos.php" class="no-margin">
				<div class="row">
					<div class="large-12 columns item">
						<label for="">Local</label>
						<?php
							$obj = new equipamento();
							echo $obj->getFormElement('insert', 'local', array(
								'required' => false,
							));
						?>
					</div>
				</div>
				<div class='row'>
					<div class='large-6 columns'>
						<label>Data Inicial</label>
						<input type='date' name='data_i' value=""/>
					</div><!-- col -->
					<div class='large-6 columns'>
						<label>Data Final</label>
						<input type='date' name='data_f' value=""/>
					</div>
				</div>
				<div class='row'>
					<div class="large-6 columns">
						<ul style='padding-left: 1em;'>
							<li><a href="" class="trigger-filtro-relatorio" data_i="<?= date('Y-m-01') ?>" data_f="">Mês atual</a></li>
							<li><a href="" class="trigger-filtro-relatorio" data_i="<?= somaDataAMD(date('Y-m-01'), 0, -1, 0) ?>" data_f="<?= somaDataAMD(date('Y-m-31'), 0, -1, 0) ?>">Mês anterior</a></li>
							<li><a href="" class="trigger-filtro-relatorio" data_i="<?= somaDataAMD(date('Y-m').'-01', 0, -3, 0) ?>" data_f="">3 meses</a></li>
							<li><a href="" class="trigger-filtro-relatorio" data_i="<?= somaDataAMD(date('Y-m').'-01', 0, -6, 0) ?>" data_f="">6 meses</a></li>
							<li><a href="" class="trigger-filtro-relatorio" data_i="<?= somaDataAMD(date('Y-m').'-01', 0, -12, 0) ?>" data_f="">12 meses</a></li>
							<li><a href="" class="trigger-filtro-relatorio" data_i="<?= date('2013-09-01') ?>" data_f="">Total</a></li>
						</ul>
					</div><!-- col -->
					<div class='large-6 columns text-right'>
						<input type='submit' class="button radius" name='' value="Gerar Relatório"/>
					</div><!-- col -->
				</div><!-- row -->
				
			</form><!-- row -->
		</div>

		<!-- relatório de servidores e prestadores de servicço -->
		<div id="relatorio-servidores" class="hidden item-relatorio">
			<form action="rel-servidores.php" class="no-margin" id="form-servidores">

				<?
					$servidores = new pessoa("WHERE id IN (".implode(',', getIdsServidores()).") ORDER BY nome");
					$prestadores = new pessoa("WHERE id IN (".implode(',', getIdsPrestadores()).") ORDER BY nome");
				?>

				<div class="row">
					<div class="large-12 columns">
						<label for="">Servidor / Prestador de Serviço</label>
						<select name="pessoa">
							<option value="">...</option>
							<?
								if($servidores->size())
								{
									?>
									<optgroup label="Servidores">
										<?
											do {
												?>
												<option value="<?= $servidores->id ?>"><?= $servidores->nome ?></option>
												<?
											}while($servidores->fetch());
										?>
									</optgroup>
									<?
								}
								if($servidores->size())
								{
									?>
									<optgroup label="Prestadores de Serviço">
										<?
											do {
												?>
												<option value="<?= $prestadores->id ?>"><?= $prestadores->nome ?></option>
												<?
											}while($prestadores->fetch());
										?>
									</optgroup>
									<?
								}
							?>
						</select>
					</div>
				</div>

				<div class="row">
					<div class="large-8 columns text-right">
						<label for="" class="form-height-fix">Mostrar comentários dos usuários?</label>
					</div>
					<div class="large-4 columns">
						<span class="form-height-fix margin-bottom">
							<input type="radio" name="mostrar_comentarios" id="servidores-mostrar_comentarios-1" value="1" checked/><label for="servidores-mostrar_comentarios-1">Sim</label>
							<input type="radio" name="mostrar_comentarios" id="servidores-mostrar_comentarios-0" value="0"/><label for="servidores-mostrar_comentarios-0">Não</label>
						</span>
					</div>
				</div>
				
				<div class="row">
					<div class="large-8 columns text-right">
						<label for="" class="form-height-fix">Mostrar lista de serviços?</label>
					</div>
					<div class="large-4 columns">
						<span class="form-height-fix margin-bottom">
							<input type="radio" name="mostrar_servicos" id="servidores-mostrar_servicos-1" value="1"/><label for="servidores-mostrar_servicos-1">Sim</label>
							<input type="radio" name="mostrar_servicos" id="servidores-mostrar_servicos-0" value="0" checked/><label for="servidores-mostrar_servicos-0">Não</label>
						</span>
					</div>
				</div>

				<div class="row">
					<div class="large-8 columns text-right">
						<label for="" class="form-height-fix">Mostrar descrição completa dos serviços?</label>
					</div>
					<div class="large-4 columns">
						<span class="form-height-fix margin-bottom">
							<input type="radio" name="mostrar_descricao_completa" id="servidores-mostrar_descricao_completa-1" value="1"/><label for="servidores-mostrar_descricao_completa-1">Sim</label>
							<input type="radio" name="mostrar_descricao_completa" id="servidores-mostrar_descricao_completa-0" value="0" checked/><label for="servidores-mostrar_descricao_completa-0">Não</label>
						</span>
					</div>
				</div>

				<div class="row">
					<div class="large-8 columns text-right">
						<label for="" class="form-height-fix">Mostrar grupo de servidores por serviço?</label>
					</div>
					<div class="large-4 columns">
						<span class="form-height-fix margin-bottom">
							<input type="radio" name="mostrar_grupo_servidores" id="servidores-mostrar_grupo_servidores-1" value="1"/><label for="servidores-mostrar_grupo_servidores-1">Sim</label>
							<input type="radio" name="mostrar_grupo_servidores" id="servidores-mostrar_grupo_servidores-0" value="0" checked/><label for="servidores-mostrar_grupo_servidores-0">Não</label>
						</span>
					</div>
				</div>

				<div class='row'>
					<div class='large-6 columns'>
						<label>Data Inicial</label>
						<input type='date' name='data_i' value=""/>
					</div><!-- col -->
					<div class='large-6 columns'>
						<label>Data Final</label>
						<input type='date' name='data_f' value=""/>
					</div>
				</div>
				<div class='row'>
					<div class="large-6 columns">
						<ul style='padding-left: 1em;'>
							<li><a href="" class="trigger-filtro-relatorio" data_i="<?= date('Y-m-01') ?>" data_f="">Mês atual</a></li>
							<li><a href="" class="trigger-filtro-relatorio" data_i="<?= somaDataAMD(date('Y-m-01'), 0, -1, 0) ?>" data_f="<?= somaDataAMD(date('Y-m-31'), 0, -1, 0) ?>">Mês anterior</a></li>
							<li><a href="" class="trigger-filtro-relatorio" data_i="<?= somaDataAMD(date('Y-m').'-01', 0, -3, 0) ?>" data_f="">3 meses</a></li>
							<li><a href="" class="trigger-filtro-relatorio" data_i="<?= somaDataAMD(date('Y-m').'-01', 0, -6, 0) ?>" data_f="">6 meses</a></li>
							<li><a href="" class="trigger-filtro-relatorio" data_i="<?= somaDataAMD(date('Y-m').'-01', 0, -12, 0) ?>" data_f="">12 meses</a></li>
							<li><a href="" class="trigger-filtro-relatorio" data_i="<?= date('2013-09-01') ?>" data_f="">Total</a></li>
						</ul>
					</div><!-- col -->
					<div class='large-6 columns text-right'>
						<input type='submit' class="button radius" name='' value="Gerar Relatório"/>
					</div><!-- col -->
				</div><!-- row -->
			</form><!-- row -->
		</div>

	</div><!-- col -->
</div><!-- row -->

<script>
	$(document).ready(function(){

		activeMainNav('sistema');

		$(document).on('click', '.trigger-filtro-relatorio', function(e){
			e.preventDefault();
			$(this).closest('form').find('input[name="data_i"]').val($(this).attr('data_i'));
			$(this).closest('form').find('input[name="data_f"]').val($(this).attr('data_f'));
		});

		$('#sub-nav-relatorios a').on('click', function(e){
			e.preventDefault();
			var clicado = $(this);
			$('#sub-nav-relatorios li.active').removeClass('active');
			clicado.closest('li').addClass('active');
			$('.item-relatorio:visible').fadeOut('fast', function(){
				$(clicado.attr('href')).fadeIn('fast');
			})

		});

		$(document).on('submit', '.item-relatorio form', function(){

			var page = $(this).attr('action')+"?"+$(this).serialize();

			$.colorbox({
				href:page,
				iframe: true,
				width: 1000,
				height: '98%',
				overlayClose: false,
				escKey: false,
				fixed: true
			});

			return false;			
		});

	}) //doc.ready
</script>

<? require('footer.php') ?>
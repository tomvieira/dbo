<?

require_once('header.php');
require_once('auth.php');

?>

<?
	if(EXIBIR_INFORMACOES_REQUISICAO)
	{
		include('informacoes-requisicao.php');
	}
?>

<div id='requisicao' style="<?= ((EXIBIR_INFORMACOES_REQUISICAO)?('display: none;'):('')) ?>">
	<form method="POST" action="ajax-requisicao.php" id='form-requisicao'>

		<div class='row'>
			<div class='large-12 columns'>
				<h3>Dados do requisitante</h3>
			</div>
		</div><!-- row -->

		<div class='row'>
			<div class='large-12 columns'>
				<label>Nome do requisitante</label>
				<?php
					if(PERMITIR_REQUISICOES_PARA_TERCEIROS === false)
					{
						?>
						<span class="fake-input ok"><?= $_pes->nome ?></span>
						<?php
					}
					else
					{
						?>
						<div class='row collapse'>
							<div class='small-9 large-10 columns'><input type='text' name='nome_requisitante' value="<?= htmlSpecialChars($_pes->nome) ?>" class="required ok" readonly/></div>
							<div class='small-3 large-2 columns'><input type='button' name='' tabindex='-1' value="Alterar" class="nome-clearer button postfix radius"/></div>
						</div>				
						<?php
					}
				?>
			</div>
		</div><!-- row -->
		
		<div class='row'>
			<div class='large-6 columns'>
				<label>E-mail do requisitante</label>
				<?php
					if(PERMITIR_REQUISICOES_PARA_TERCEIROS === false)
					{
						?>
						<span class="fake-input ok"><?= $_pes->email ?></span>						
						<?php
					}
					else
					{
						?>
						<input type='email' name='email_requisitante' value="<?= $_pes->email ?>" class='required ok'/>
						<?php
					}
				?>
			</div>

			<div class='large-6 columns'>
				<label>Telefone do requisitante</label>
				<input type='text' name='telefone_requisitante' value="<?= $_pes->telefone ?>" class="required <?= ((strlen(trim($_pes->telefone)))?('ok'):('')) ?>" <?= ((strlen(trim($_pes->telefone)))?(''):('autofocus')) ?> />
			</div>
		</div><!-- row -->

		<div class='row'>
			<div class='large-12 columns'>
				<h3>Serviços requisitados</h3>
			</div>
		</div><!-- row -->


		<div id="wrapper-locais">
			
			<div class="item-local" number='1'>
				<div class='tag-local'><span>1</span></div>
				<div class='row'>
					<div class='large-12 columns'>
						<span id='helper-local' class="helper arrow-bottom hide-for-small">Selecione o local onde os serviços devem ser executados, ou o local de trabalho do requisitante. Em seguida, inclua os serviços, separadamente.</span>
						<label>Local</label>
						<div class='row collapse'>
							<div class='small-9 large-10 columns'><input type='text' name='aux_local[1]' <?= ((strlen(trim($_pes->telefone)))?('autofocus'):('')) ?> value="" class="required aux-local" id='first-focus' placeholder='Digite algumas letras para procurar...'/></div>
							<div class='small-3 large-2 columns'><input type='button' name='' tabindex='-1' value="Alterar" class="local-clearer button disabled postfix radius"/></div>
						</div><!-- row -->
						<input type='hidden' name='local[1]' value=""/>
					</div>
				</div><!-- row -->

				<div class="wrapper-servicos">
					<div class="item-servico" number='1'>
						<div class='row'>
							<div class='large-4 columns' id='tipo-servico-helped' style='display: none;'>
								<label>Tipo de serviço a ser executado</label>
								<select name="tipo_servico[1][1]" class="required">
									<option value='-1'>Selecione...</option>
									<?
										$obj = new tipo_servico('WHERE inativo = 0 ORDER BY nome');
										do {
											?>
											<option value="<?= $obj->id ?>" <?= (($obj->solicitar_patrimonio)?("data-solicitar_patrimonio='true'"):('')) ?>><?= $obj->nome ?></option>
											<?
										}while($obj->fetch());
									?>
								</select>
								<div class="helper arrow-top hide-for-small" id='helper-servico'>Selecione um tipo de serviço na lista acima.</div>
							</div><!-- col -->
							<div class='large-8 columns' id='descricao-servico-helped' style="display: none;">
								<label>Descrição do serviço</label>
								<textarea name='descricao[1][1]' rows='1' class="required helped"></textarea>
								<div class="helper-patrimonio" style="display: none; padding-bottom: 1em;"><div class="helper arrow-top">Não se esqueça de digitar o <span class="color alert"><strong><u>número do patrimônio</u></strong></span>. Ex: "Patrimônio: 12345"</div></div>
								<div class="helper arrow-top hide-for-small" id='helper-descricao'>Descreva detalhadamente sua requisição para o tipo de serviço selecionado. <strong><u>Importante:</u></strong> descreva somente 1 serviço por vez. Você deve incluir diferentes tipo de serviço separadamente.</div>
							</div><!-- col -->
						</div><!-- row -->
					</div><!-- item-servico (1)(1) -->

				
				</div><!-- wrapper-servicos -->

				<div class='row wrapper-novo-servico' style="display: none;">
					<div class='large-7 columns hide-for-small'>
						<div class="helper arrow-right" id='helper-novo-servico'>Caso precise de <em>outro serviço</em> no mesmo local, ou queira requisitar serviços em <em>outro local</em>, utilize uma das opções ao lado...</div>
					</div>
					<div class='large-5 columns text-right margin-bottom'>
						<a href='#' class="trigger-novo-servico add">Adicionar mais um serviço neste local <i class="fa fa-plus-circle"></i></a>
					</div><!-- col -->
				</div><!-- row -->

			</div><!-- item-local (1) -->

		
		</div><!-- wrapper-locais -->

		<div class='row wrapper-novo-local' style='display: none;'>
			<div class='large-12 columns text-right margin-bottom'>
				<a href='#' class="trigger-novo-local add">Adicionar mais um local <i class="fa fa-plus-circle"></i></a>
			</div>
		</div><!-- row -->

		<div class='row wrapper-enviar-requisicao' style="display: none;">
			<div class='large-6 large-offset-1 columns hide-for-small'>
				<div class="helper arrow-right tar" id='helper-enviar'>...caso contrário, pressione o botão "<em>Enviar Requisição</em>"</div>
			</div>
			<div class='large-5 columns text-right'>
				<input type='button' name='' default-value="Enviar Requisição" value="Enviar Requisição" default-value='Enviar Requisição' class="submitter button radius"/>
			</div><!-- col -->
		</div><!-- row -->

		<input type='hidden' name='submit_token' value="<?= time().rand(1,1000) ?>"/>
		<input type='hidden' name='action' value="save-requisicao"/>
	</form>
</div><!-- #requisicao -->

<a name='fim-da-pagina' id="fim-da-pagina"></a>

<script type="text/javascript" charset="utf-8">

	var REGEX_PATRIMONIO = '<?= addSlashes(REGEX_PATRIMONIO); ?>';

	var timer_regex;

	function addNovoServico(clicado) {

		var index_local = parseInt(clicado.closest('.item-local').attr('number'));
		var new_index_servico = parseInt(clicado.closest('.item-local').find('.wrapper-servicos').find('.item-servico').last().attr('number'))+1;

		string_retorno  = "";
		string_retorno += "<div class='item-servico' number='"+new_index_servico+"'>";
		string_retorno += "	<div class='row' style='position: relative;'>";
		string_retorno += "		<a href='#' class='button-delete trigger-delete-servico' title='Remover este serviço'>Remover</a>";
		string_retorno += "		<div class='large-4 columns'>";
		string_retorno += "				<select name='tipo_servico["+index_local+"]["+new_index_servico+"]' class='required'>";
		string_retorno += "					<option value='-1'>Selecione...</option>";

		<?                                                                                                                   
			$obj = new tipo_servico('ORDER BY nome');                                                                            
			do {                                                                                                                 
			?>                                                                                                                   
				string_retorno += "<option value='<?= $obj->id ?>' <?= (($obj->solicitar_patrimonio)?("data-solicitar_patrimonio='true'"):('')) ?>><?= $obj->nome ?></option>";      
			<?                                                                                                                   
			}while($obj->fetch());                                                                                               
		?>                                                                                                                   

		string_retorno += "				</select>";
		string_retorno += "		</div><!-- col -->";
		string_retorno += "		<div class='large-8 columns'>";
		string_retorno += "			<div class='input'>";
		string_retorno += "					<textarea name='descricao["+index_local+"]["+new_index_servico+"]' rows='1' class='required helped'></textarea>";
		string_retorno += '					<div class="helper-patrimonio" style="display: none; padding-bottom: 1em;"><div class="helper arrow-top">Não se esqueça de digitar o <span class="color alert"><strong><u>número do patrimônio</u></strong></span>. Ex: "Patrimônio: 12345"</div></div>';
		string_retorno += "			</div>";
		string_retorno += "			</div>";
		string_retorno += "		</div><!-- col -->";
		string_retorno += "	</div><!-- row -->";
		string_retorno += "</div><!-- item-servico ("+index_local+")("+new_index_servico+") -->";


		clicado.closest('.item-local').find('.wrapper-servicos').append(string_retorno);
	}

	function addNovoLocal() {

		var new_index_local = parseInt($('#wrapper-locais').find('.item-local').last().attr('number'))+1;

		string_retorno  = "<div class='item-local' number='"+new_index_local+"'>";
		string_retorno += "	<div class='tag-local'><span>"+new_index_local+"</span></div>";
		string_retorno += "	<div class='row' style='position: relative;'>";
		string_retorno += "		<a href='#' class='button-delete trigger-delete-local' title='Remover este serviço'>Remover</a>";
		string_retorno += "		<div class='large-12 columns'>";
		string_retorno += "			<label>Local do serviço a ser executado</label>";
		string_retorno += "			<div class='row collapse'>";
		string_retorno += "				<div class='small-9 large-10 columns'>";
		string_retorno += "					<input type='text' name='aux_local["+new_index_local+"]' value='' class='required aux-local' placeholder='Digite algumas letras para procurar...'/>";
		string_retorno += "				</div>";
		string_retorno += "				<div class='small-3 large-2 columns'>";
		string_retorno += "					<input type='button' name='' tabindex='-1' value='Alterar' class='local-clearer button disabled postfix radius'/>";
		string_retorno += "				</div>";
		string_retorno += "			</div>";
		string_retorno += "			<input type='hidden' name='local["+new_index_local+"]' value=''/>";
		string_retorno += "		</div><!-- item -->";
		string_retorno += "	</div><!-- row -->";
		string_retorno += "	<div class='wrapper-servicos'>";
		string_retorno += "		<div class='item-servico' number='1'>";
		string_retorno += "			<div class='row'>";
		string_retorno += "				<div class='large-4 columns'>";
		string_retorno += "					<label>Tipo de serviço a ser executado</label>";
		string_retorno += "						<select name='tipo_servico["+new_index_local+"][1]' class='required'>";
		string_retorno += "							<option value='-1'>Selecione...</option>";

		<?
			$obj = new tipo_servico('ORDER BY nome');
			do {
				?>
				string_retorno += "<option value='<?= $obj->id ?>' <?= (($obj->solicitar_patrimonio)?("data-solicitar_patrimonio='true'"):('')) ?>><?= $obj->nome ?></option>";
				<?
			}while($obj->fetch());
		?>

		string_retorno += "						</select>";
		string_retorno += "				</div><!-- col -->";
		string_retorno += "				<div class='large-8 columns'>";
		string_retorno += "						<label>Descrição do serviço</label>";
		string_retorno += "						<div class='input'>";
		string_retorno += "							<textarea name='descricao["+new_index_local+"][1]' rows='1' class='required helped'></textarea>";
		string_retorno += '							<div class="helper-patrimonio" style="display: none; padding-bottom: 1em;"><div class="helper arrow-top">Não se esqueça de digitar o <span class="color alert"><strong><u>número do patrimônio</u></strong></span>. Ex: "Patrimônio: 12345"</div></div>';
		string_retorno += "						</div>";
		string_retorno += "				</div><!-- col -->";
		string_retorno += "			</div><!-- row -->";
		string_retorno += "		</div><!-- item-servico -->";
		string_retorno += "	</div><!-- wrapper-servicos -->";
		string_retorno += "	<div class='row wrapper-novo-servico'>";
		string_retorno += "		<div class='large-12 columns text-right'>";
		string_retorno += "			<a href='#' class='trigger-novo-servico add'>Adicionar mais um serviço neste local <i class=\"fa fa-plus-circle\"></i></a>";
		string_retorno += "		</div><!-- col -->";
		string_retorno += "	</div>";
		string_retorno += "</div><!-- item-local -->";

		$('#wrapper-locais').append(string_retorno);

		$('input[name="aux_local\\['+new_index_local+'\\]"]').focus();

		$(window).scrollTo('#fim-da-pagina', 1000);

	}

	function makeMulti() {
		$('.tag-local').fadeIn('fast');
		$('.item-local').addClass('item-local-multi');
	}

	function checkPatrimonio(focado) {
		wrapper = focado.closest('.item-servico');
		if(wrapper.find('select option:selected').data('solicitar_patrimonio') == true){
			var re = new RegExp(REGEX_PATRIMONIO, 'mi');
			if(re.test(focado.val())){
				wrapper.find('.helper-patrimonio').slideUp();
			}
			else {
				wrapper.find('.helper-patrimonio').slideDown();
			}
		}
	}

	$(document).on('click', '.trigger-delete-local', function(e){
		e.preventDefault();
		var ans = confirm("Tem certeza que deseja remover este local e seus serviços?");
		if (ans==true) {
			$(this).closest('.item-local').fadeOut(function(){ $(this).remove(); })
		} else {
			return false;
		}
	});

	$(document).on('click', '.trigger-delete-servico', function(e){
		e.preventDefault();
		var ans = confirm("Tem certeza que deseja remover este serviço?");
		if (ans==true) {
			$(this).closest('.item-servico').fadeOut(function(){ $(this).remove(); })
		} else {
			return false;			
		}
	});

	$(document).on('focus', '.aux-local', function(){
		$(this).autocomplete({
			source: function(request, response){
				$.get("ajax-locais.php", {term:request.term}, function(data){
					response($.map(data, function(item) {
						return {
							label: item.local,
							value: item.id
						}
					}))
				}, "json");
			},
			minLength: 2,
			dataType: "json",
			cache: false,
			focus: function(event, ui) {
				return false;
			},
			change: function (event, ui){
				if(!ui.item){
					$(this).val('');
				}
			},
			delay: 1,
			select: function(event, ui) {
				if(ui.item.value != '-1'){
					this.value = ui.item.label;
					$(this).attr('readonly', 'readonly');
					$(this).removeClass('error');
					$(this).addClass('ok');
					$(this).closest('.item-local').find('input[name^=local]').val(ui.item.value);
					$(this).closest('.item-local').find('.local-clearer').removeClass('disabled');
					$('#helper-local').fadeOut(250, function(){ $(this).remove(); });
					$('#tipo-servico-helped:not(visible)').fadeIn(250);
				}
				else {
					this.value = '';
					$(this).closest('.item-local').find('input[name^=local]').val('');
				}
				return false;
			}
		});
	})

	$(document).on('focus', 'input[name="nome_requisitante"]', function(){
		$(this).autocomplete({
			source: function(request, response){
				$.get("ajax-requisitantes.php", {term:request.term}, function(data){
					response($.map(data, function(item) {
						return {
							label: item.nome,
							nome: item.nome,
							email: item.email,
							telefone: item.telefone,
							value: item.id
						}
					}))
				}, "json");
			},
			minLength: 3,
			dataType: "json",
			cache: false,
			focus: function(event, ui) {
				return false;
			},
			delay: 1,
			select: function(event, ui) {
				if(ui.item.value != '-1'){
					$(this).val(ui.item.nome).attr('readonly', 'readonly').removeClass('error').addClass('ok').closest('.row').find('.nome-clearer').removeClass('disabled');
					$('input[name="email_requisitante"]').val(ui.item.email).addClass('ok');
					if($.trim(ui.item.telefone).length > 1){
						$('input[name="telefone_requisitante"]').val(ui.item.telefone).addClass('ok');
						focus = $('#first-focus');
					}
					else {
						focus = $('input[name="telefone_requisitante"]');
					}
					focus.focus();
				}
				return false;
			}
		});
	})

	$(document).on('click', '.nome-clearer:not(.disabled)', function(){
		$(this).addClass('disabled');
		$('input[name="nome_requisitante"]').removeClass('ok').removeAttr('readonly').val('').focus();;
		$('input[name="email_requisitante"]').removeClass('ok').val('');
		$('input[name="telefone_requisitante"]').removeClass('ok').val('');
	})

	$(document).ready(function(){
		$('input[name^="aux_local"]').val('');
		$('input[name^="local"]').val('');
	}) //doc.ready

	$(document).on('click', '.trigger-disclaimer', function(){
		$('#disclaimer').fadeOut('fast', function(){
			$('#requisicao').fadeIn('fast');
		})
	});

	$(document).on('change', 'select[name="tipo_servico\\[1\\]\\[1\\]"]', function(){
		if($(this).val() != '-1'){
			$('#helper-servico').fadeOut(250, function(){ $(this).remove() });
			$('#descricao-servico-helped:not(visible)').fadeIn(250);
		}
	})

	$(document).on('focus', '#requisicao textarea[name^=descricao]', function(){
		$(this).autosize();
	})

	$('#form-requisicao').submit(function(){
		
		var error = false;
		var submitter = $(this).find('.submitter');

		//tratando campos obrigatorios
		$(this).find('input.required, textarea.required').each(function(){
			if($.trim($(this).val()) == ''){
				$(this).addClass('error');
				error = true;
			}
		})
		$(this).find('select.required').each(function(){
			if($.trim($(this).val()) == '-1'){
				$(this).addClass('error');
				error = true;
			}
		})
		
		//checando para ver se é bem móvel
		var serv = $('#form-requisicao select[name=tipo_servico]').find('option:selected');

		//se tiver erro, não submita e avisa o usuário.
		if(error){
			alert('Atenção:\n\npreencha todos os campos obrigatórios');
			$(submitter).val($(submitter).attr('default-value')).removeAttr('disabled').removeClass('disabled');
			return false;
		}
		//submit propriamente dito!
		else {
			var ans = confirm("Tem certeza que deseja enviar sua requisição?");
			if (ans==true) {
				peixePost(
					$(this).attr('action'),
					$(this).serialize(),
					function(data) {
						console.log(data);
						var result = $.parseJSON(data);
						if(result.message){
							setPeixeMessage(result.message);
							showPeixeMessage();
						}
						if(result.html){
							$(result.html).each(function(){
								$(this.selector).html(this.content);
							})
						}
						if(result.redirect){
							window.location = 'acompanhamento.php?sucesso=1&servicos='+result.redirect;
						}
					}
				)
				return false;			
			} else {
				$(submitter).val($(submitter).attr('default-value')).removeAttr('disabled').removeClass('disabled');
				return false;
			}
		}
	}) //form requisicao submit

	$(document).on('change, keyup', '#requisicao textarea[name^="descricao"]', function(){
		mudado = $(this);
		checkDescricao();
		clearTimeout(timer_regex);
		timer_regex = setTimeout(function(){
			checkPatrimonio(mudado);
		}, 500);
	})

	$(document).on('change', '#form-requisicao .error-group.error', function(){
		$('#form-requisicao .error-group').removeClass('error');
	})

	$(document).on('click', '.trigger-novo-local', function(e){
		e.preventDefault();
		$('#helper-novo-servico').fadeOut(250, function(){ $(this).remove() });
		$('#helper-enviar').fadeOut(250, function(){ $(this).remove() });
	})

	$(document).on('click', '.trigger-novo-servico', function(e){
		e.preventDefault();
		$('#helper-novo-servico').fadeOut(250, function(){ $(this).remove() });
		$('#helper-enviar').fadeOut(250, function(){ $(this).remove() });
		addNovoServico($(this));
	})

	$(document).on('click', '.trigger-novo-local', function(e){
		e.preventDefault();
		$('#helper-novo-servico').fadeOut(250, function(){ $(this).remove() });
		$('#helper-enviar').fadeOut(250, function(){ $(this).remove() });
		addNovoLocal();
		makeMulti();
	})

	activeMainNav('nova-requisicao');

</script>
<? require_once('footer.php'); ?>
<? require_once('header.php'); ?>
<? require_once('auth.php'); ?>

<style>
	html, body { padding: 0; }
	.ui-autocomplete { max-width: 65% !important; }
</style>

<div class="row">
	<div class="large-12 columns">
		<h3>Duplicar equipamento</h3>
	</div>
</div>


<form method="post" action="ajax-equipamento-duplicar-actions.php" id='form-duplicar' class="no-margin">
<?
	$eq = new equipamento(dboescape($_GET['id']));
	$loc = new local($eq->local);
	if($eq->size())
	{
		$tipo = new tipo_equipamento($eq->tipo_equipamento);
		$marca = new equipamento_marca($eq->equipamento_marca);
		?>
		<div id="wrapper-equipamento">
			
			<div class="row">
				<div class="large-3 columns">
				<?
					if(strlen($eq->foto))
					{
						?>
						<p>
							<img src='<?= DBO_URL ?>/upload/images/t_<?= $eq->foto ?>' class="th"/>
							<input type='hidden' name='foto' value="<?= $eq->foto ?>"/>
						</p>
						<?
					}		
				?>
					<span><label>Tipo</label></span>
					<p><?= $tipo->nome ?></p>
					<input type='hidden' name='tipo_equipamento' value="<?= $tipo->id ?>"/>

					<span><label>Marca</label></span>
					<p><?= $marca->nome ?></p>
					<input type='hidden' name='equipamento_marca' value="<?= $marca->id ?>"/>

					<span><label>Modelo</label></span>
					<p><?= $eq->modelo ?></p>
					<input type='hidden' name='modelo' value="<?= $eq->modelo ?>"/>

				</div><!-- col -->
				<div class="large-9 columns">
									
					<div class="row">
						<div class="large-6 columns item">
							<label>Código da Etiqueta</label>
							<input type='text' name='codigo' value="" class="" autofocus/>
							<div class="helper arrow-top" style="z-index: 1000; position: absolute; top: 68px;">Digite o código para o novo item que você está cadastrando</div>
						</div>
						<div class="large-6 columns">
							<label>Número de Série</label>
							<input type='text' name='numero_serie' value=""/>
						</div>
					</div>
					
					<div class="row">
						<div class="large-12 columns item item-local">
							<label>Local</label>
							<div class='row collapse'>
								<div class='small-9 large-10 columns'><input type='text' name='aux_local' readonly value="<?= htmlSpecialChars($loc->getSmartLocal()) ?>" class="required ok aux-local" placeholder='Digite algumas letras para procurar...'/></div>
								<div class='small-3 large-2 columns'><input type='button' name='' tabindex='-1' value="Alterar" class="local-clearer button postfix radius"/></div>
							</div><!-- row -->
							<input type='hidden' name='local' value="<?= $loc->id ?>"/>
						</div>
					</div>
					
					<div class="row">
						<div class="large-12 columns">
							<label>Detalhe do Local</label>
							<input type='text' name='local_detalhe' value="<?= $eq->local_detalhe ?>"/>
						</div>
					</div>

					<div class="row">
						<div class="large-6 columns">
							<label>Patrimônio</label>
							<input type='text' name='patrimonio' value=""/>
						</div>
						<div class="large-6 columns">
							<label>A.I.</label>
							<input type='text' name='ai' value=""/>
						</div>
					</div>
					
					<div class="row">
						<div class="large-12 columns">
							<label>Observações</label>
							<textarea name='observacao' rows='5'><?= htmlSpecialChars($eq->observacao) ?></textarea>
						</div>
					</div>
					
					<div class="row">
						<div class="large-6 columns">
							<label for="">Custo</label>
							<input type="text" name="custo" id="" value="<?= (($eq->custo != null)?(reais($eq->custo, true)):('')) ?>" class="price text-right"/>
						</div>
						<div class="large-6 columns item">
							<label for="">Status</label>
							<select name="status" required>
								<option value="">...</option>
								<?= $eq->getMulti('status', 'select', $eq->status) ?>
							</select>
						</div>
					</div>
					
						
					<div class="row">
						<div class="large-12 columns text-right">
							<input type='button' name='' value="Duplicar" class="submitter button radius no-margin"/>
						</div>
					</div>
					
				</div>
			</div>
		</div><!-- wrapper-equipamento -->
		<?
	}
?>
	<input type="hidden" name="vinculo_material" id="" value="<?= $eq->vinculo_material ?>"/>
</form>

<script type="text/javascript" charset="utf-8">

	function resetSubmitter(){
		$('.submitter').removeAttr('disabled');		
	}

	$(document).ready(function(){
		
		$('#form-duplicar').on('submit', function(){

			var form = $(this);

			//validar
			if(form.find('input[name=local]').val() == '' || form.find('select[name="status"]').val() == ''){
				alert('Erro: Preencha todos os campos obrigatórios');
				resetSubmitter();
				return false;
			}
			else if(
				$.trim(form.find('input[name="codigo"]').val()) == ''	&&
				$.trim(form.find('input[name="patrimonio"]').val()) == ''	&&
				$.trim(form.find('input[name="ai"]').val()) == ''
			){
				alert('Erro: Você precisa preencher pelo menos um dos seguintes campos:\n\n- Código\n- Patrimônio\n- A.I.');
				resetSubmitter();
				return false;
			}
			else {
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
						if(result.sucesso){
							//nao pode ser o primeiro no depois do <body>
							parent.location.reload();
						}
						resetSubmitter();
					}
				)
			}
			return false;
		})

		$('input[name=codigo]').on('blur', function(){
			$(this).closest('.item').find('.helper:visible').fadeOut(function(){ $(this).css('display', 'none !important') });
		})

		$('input[name=codigo]').focus();

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
					}
					else {
						this.value = '';
						$(this).closest('.item-local').find('input[name^=local]').val('');
					}
					return false;
				}
			});
		})

	}) //doc.ready
</script>

<? require_once('footer.php'); ?>
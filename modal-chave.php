<? require_once('lib/includes.php'); ?>
<? require_once('auth.php'); ?>
<a class="close-reveal-modal">&#215;</a>
<?
	$chave = new chave(str_pad(intval($_GET['numero_chave']), 5, 0, STR_PAD_LEFT));
	if($chave->size())
	{
		$loc = new local("WHERE id = '".$chave->local."'");
		?>
		<form method="post" action="ajax-chave-actions.php" class="no-margin" id="form-modal-chave">
			<div class="row full">
				<div class="large-5 columns">
					<div class="numero-chave"><i class="fa-key"></i> <?= $chave->numero ?></div>
				</div>
				<div class="large-7 columns">
					<h3 class="no-margin"><?= $loc->getSmartLocal(); ?></h3>
				</div>
			</div>

			<div class="row full">
				<div class="large-12 columns">
					<hr>
				</div>
			</div>

			<div class="row full">
				<div class="large-5 columns">
					<label for="">Status da chave</label>
					<span class="tag-status status-<?= makeSlug($chave->getStatus()) ?> radius"><?= $chave->getStatus(); ?></span>
				</div>
				<div class="large-<?= ((hasPermission('setar-horarios'))?('4'):('7')) ?> columns">
					<label for="modal-chave-pessoa">Retirada por</label>
					<?
						if($chave->estaDisponivel())
						{
							?>
							<input type="search" name="pessoa" id="modal-chave-pessoa" value="" placeholder="Digite algumas letras do nome da pessoa..." class="no-margin buscar-pessoa-chave-autocomplete" data-numero_chave="<?= $chave->numero ?>" data-target="#lista-pessoas-chave-modal"/>
							<?
						}
						else
						{
							$mov = $chave->getUltimaMovimentacao();
							$pessoa_que_retirou = new pessoa($mov->retirada_por);
							?>
							<input type="text" value="<?= htmlSpecialChars($pessoa_que_retirou->nome) ?>" readonly/>
							<?
						}
					?>
				</div>
				<?
					if(hasPermission('setar-horarios'))
					{
						?>
						<div class="large-3 columns">
							<label for="modal-chave-data_hora">Data e horário</label>
							<input type="text" name="data_hora" value="" id="modal-chave-data_hora" class="timepick"/>
						</div>
						<?
					}
				?>
			</div>

			<div class="row full">
				<div class="large-12 columns">
					<hr>
				</div>
			</div>

			<div class="row full">
				<div class="large-5 columns">
					<div class="row collapse margin-bottom">
						<div class="large-5 columns">
							<div class="avatar">
								<img src="<?= $_pes->getFoto(); ?>" class="round margin-bottom" style="display: block;"/>
								<div class="text-center"><strong><?= $_pes->getShortName(); ?></strong></div>
							</div>				
						</div>
						<div class="large-2 columns text-center">
							<i class="fa-arrow-<?= (($chave->estaDisponivel())?('right'):('left')) ?> seta seta-<?= (($chave->estaDisponivel())?('saida'):('entrada')) ?>"></i>
						</div>
						<div class="large-5 columns">
							<div class="avatar">
								<img src="<?= (($pessoa_que_retirou)?($pessoa_que_retirou->getFoto()):('data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH/C1hNUCBEYXRhWE1QPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS4zLWMwMTEgNjYuMTQ1NjYxLCAyMDEyLzAyLzA2LTE0OjU2OjI3ICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M2IChXaW5kb3dzKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpGMDc2QTJDNEU3MTAxMUUyQUI3OEY2NjRGREVCMkFGNyIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpGMDc2QTJDNUU3MTAxMUUyQUI3OEY2NjRGREVCMkFGNyI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkYwNzZBMkMyRTcxMDExRTJBQjc4RjY2NEZERUIyQUY3IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkYwNzZBMkMzRTcxMDExRTJBQjc4RjY2NEZERUIyQUY3Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+Af/+/fz7+vn49/b19PPy8fDv7u3s6+rp6Ofm5eTj4uHg397d3Nva2djX1tXU09LR0M/OzczLysnIx8bFxMPCwcC/vr28u7q5uLe2tbSzsrGwr66trKuqqainpqWko6KhoJ+enZybmpmYl5aVlJOSkZCPjo2Mi4qJiIeGhYSDgoGAf359fHt6eXh3dnV0c3JxcG9ubWxramloZ2ZlZGNiYWBfXl1cW1pZWFdWVVRTUlFQT05NTEtKSUhHRkVEQ0JBQD8+PTw7Ojk4NzY1NDMyMTAvLi0sKyopKCcmJSQjIiEgHx4dHBsaGRgXFhUUExIREA8ODQwLCgkIBwYFBAMCAQAAIfkEAQAAAAAsAAAAAAEAAQAAAgJEAQA7')) ?>" width="150" height="150" class="round margin-bottom" id="modal-chave-foto"/>
								<div class="text-center"><strong id="modal-chave-apelido"><?= (($pessoa_que_retirou)?($pessoa_que_retirou->getShortName()):('')) ?></strong></div>
							</div>	
						</div>
					</div>
					<?
						if($chave->estaDisponivel())
						{
							?>
							<a href="" class="button large expand radius no-margin secondary disabled" id="modal-chave-submit">Confirmar<br />Retirada da Chave</a>
							<?
						}
						else
						{
							?>
							<a href="" class="button large expand radius no-margin primary" id="modal-chave-submit">Confirmar<br />Devolução da Chave</a>
							<?
						}
					?>
				</div>
				<div class="large-7 columns">
					<ul class="lista-pessoas-chave scrollbar" id="lista-pessoas-chave-modal">
						<?
							if($chave->estaDisponivel())
							{
								$pessoa = $chave->getPessoasAutorizadasDefault();
								if($pessoa->size())
								{
									do {
										?>
										<li class="autorizada" data-apelido="<?= $pessoa->getShortName() ?>" data-foto="<?= $pessoa->getFoto() ?>" data-id="<?= $pessoa->id ?>">
											<i class="fa-unlock-alt"></i>
											<img class="foto" src="<?= $pessoa->getFoto() ?>" alt="">
											<span class="nome"><?= $pessoa->nome ?></span>
										</li>
										<?
									}while($pessoa->fetch());
								}
							}
							else
							{
								?>
								<div class="mensagem-indisponivel text-center padding-top-3">
									<p>Está chave está retirada há</p>
									<h1><strong><?= ago($mov->data_hora, false) ?>,</strong></h1>
									<p>desde <?= dboDate('l, d --- F, H:i', strtotime($mov->data_hora)) ?></p>
								</div>
								<?
							}
						?>
					</ul>
				</div>
			</div>
			<?= CSRFInput(); ?>
			<input type="hidden" name="retirada_por" id="modal-chave-retirada_por" value=""/>
			<input type="hidden" name="numero_chave" value="<?= $chave->numero ?>"/>
			
		</form>
		<?
	}
	else
	{
		?>
		<h1 class="text-center">Erro: a chave número <strong><?= $_GET['numero_chave'] ?></strong> não existe</h1>
		<?
	}
?>
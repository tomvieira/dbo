/* variaveis de controle do sistema */

var desktop_notifications = false;
var scroll_top = 0;
var win = $(window);
var requisicao = {
	alteracoes_pendentes: false,
	alteracoes_relevantes: false,
	last: {
		enviar_email_1: false,
		enviar_email_0: false,
		status: false
	},
	status: {
		novo: 0,
		aguardando_aprovacao_diretoria: 1,
		aprovado: 2,
		atribuido: 3,
		aguardando_requisitante: 4,
		em_andamento: 5,
		concluido: 6,
		nao_aprovado: 7,
		cancelado: 8,
		avaliado: 9
	},
	//tipo um construtor
	start: function(){
		this.alteracoes_pendentes = $('#form-modal-requisicao').serialize();
		this.alteracoes_relevantes = false;	
		this.last.enviar_email_0 = false;
		this.last.enviar_email_1 = false;
		this.last.status = $('#modal-requisicao-status').data('last-value');
		this.adjustLayout();
		//formatando o campo de preco
		$('.custo-requisicao').priceFormat({
			prefix: '',
			centsSeparator: ',',
			thousandsSeparator: '.'
		});
		//colocando autosize nas textareas
		$('#modal-gerenciar-requisicao textarea').autosize();
	},
	//forca o envio do email para o requisitante
	forceFeedback: function() {
		this.last.enviar_email_1 = $('#enviar-email-1:checked').length;
		this.last.enviar_email_0 = $('#enviar-email-0:checked').length;
		$('#enviar-email-1').prop('checked', true);
		$('#enviar-email-0').prop('checked', false).prop('disabled', true);
	},
	//desforça o envio do email para o requisitante
	unforceFeedback: function() {
		$('#enviar-email-1').prop('checked', this.last.enviar_email_1);
		$('#enviar-email-0').prop('checked', this.last.enviar_email_0).prop('disabled', false);
	},
	//da uma sacodidela no campo para chamar atenção do usuário
	nudgeFeedback: function() {
		nudge('#modal-requisicao-wrapper-enviar_email');
	},
	//retorna se selecionou ou não enviar o feedback
	feedbackSelected: function() {
		if($('#enviar-email-1:checked').length) {
			return 'sim';
		}
		else if($('#enviar-email-0:checked').length) {
			return 'não';
		}
		else {
			return false;
		}
	},
	//acerta o layout para mobild
	adjustLayout: function() {
		$('#modal-gerenciar-requisicao .wrapper-lista-responsaveis').clone().appendTo('#responsaveis-atribuidos-small');
	},
	/*alterarLocal: function() {
		$('#modal-requisicao-local_aux').prop('readonly', false).val('').focus();
		$('#modal-requisicao-local').val('');
	},*/
	//funcao que retorna se o local está em branco
	localEmBranco: function() {
		if($('#modal-requisicao-local').val() <= 0){
			return true;
		}
		else {
			return false;
		}
	},
	//sacode o local para o usuário
	nudgeLocal: function() {
		nudge('#modal-requisicao-wrapper-local');
	}
};

function nudge(elem) {
	$(elem).addClass('shake shake-constant shake-hard');
	setTimeout(function(){
		$(elem).removeClass('shake shake-constant shake-hard');
	}, 1000);
}

/* funcoes de uso geral */

function datepickerInit() {
	$('.datepick').each(function(){
		$(this).datepicker();
	})
}

function maskInit() {
	$('[mask]').each(function(){
		$(this).mask($(this).attr('mask'));
	});
}

function delayFormInit() {
	setTimeout(function(){
		formInit();
	}, 500);
}

function formInit() {
	peixeInit();
	datepickerInit();
	maskInit();
	peixeAjaxFileUploadInit();
	$('textarea.autosize').autosize();
	$('input.price').priceFormat({
		prefix: '',
		centsSeparator: ',',
		thousandsSeparator: '.'
	});
	$('.datetimepick').each(function(){
		$(this).datetimepicker({
			hourMin: 8,
			hourMax: 18,
			dateFormat: 'dd/mm/yy',
			timeFormat: 'HH:mm',
			stepHour: 1,
			showMinute: false,
			hour: 17,
			hourGrid: 5
		});
	});
}

/*function showDetalhesRequisicao() {
	$('#form-requisicao .detalhes').slideDown(250);
}*/

function setOpcao(opcao, valor, tipo) {
	if(typeof tipo == 'undefined'){
		console.log(opcao+valor);
	}
	else {
		console.log(opcao+valor+tipo);
	}
}

function sidebarInit() {}

/*function showDesktopNotification(id, icon, title, description, url) {
	if(window.webkitNotifications){
		if(desktop_notifications){
			if(window.webkitNotifications.checkPermission() == 0){
				width = 960;
				height = 750;
				y = parseInt((window.screen.availHeight-height)/2)-60;
				x = parseInt((window.screen.availWidth-width)/2);
				var notification = window.webkitNotifications.createNotification(
					icon,
					title,
					description
				);
				notification.onclick = function(){ window.open(url, "FCFAR Notification", "width="+width+", height="+height+", top="+y+", left="+x); }
				notification.show();
			}
		}
	}
	return false;
}*/

function hideDetalhesRequisicao() {
	$('#form-requisicao .detalhes').slideUp(250).find('input').each(function(){ $(this).val(''); $(this).removeClass('error'); });
}

function checkDescricao() {
    if($('#requisicao textarea[name=descricao\\[1\\]\\[1\\]]').val().length > 10){
		$('#helper-descricao').fadeOut(250, function(){ $(this).remove(); });
		if($('.wrapper-novo-servico:hidden').length){
			setTimeout(function(){
				$('.wrapper-novo-servico:hidden, .wrapper-novo-local:hidden, .wrapper-enviar-requisicao:hidden').fadeIn(250);
			}, 250);
		}
    }
}

function checkResponsaveisAtribuidos() {
	if($('#responsaveis-atribuidos input[type="checkbox"]:checked').length > 0){
		$('span.tag-servidor.tag-nenhum').hide();
	}
	else {
		$('span.tag-servidor.tag-nenhum').show();
	}
}


/*function setIcon(quantidade) {
	if(gerenciar_servicos){
		if(quantidade == null){
			quantidade = 0;
		}
		else {
			quantidade = $(quantidade).text();
		}
		if(quantidade > 10){
			quantidade = 10;
		}
		$('head link[rel="icon"]').attr('href', 'images/notification-icons/'+quantidade+'.png');
	}
}*/

/*function showDesktopNovosChamados(foo) {
	$(foo).toArray();
	for (var key in foo) {
		if (foo.hasOwnProperty(key)) {
			id = 'sti-suporte-servico-avisado-'+foo[key].id;
			descricao = foo[key].descricao.replace(/(\r\n|\n|\r)/gm," ");
			area = foo[key].nome_area;
			url = "modal-requisicao.php?&id="+foo[key].requisicao+"&item="+foo[key].id+"&outside=1";
			if(!sessionStorage.getItem(id)){
				showDesktopNotification(id, 'images/desktop-notification-novo-servico.png', 'Novo chamado - '+area, descricao.substr(0, 75)+((descricao.length > 75)?(' ...'):('')), url);
				sessionStorage.setItem(id, 1);
			}
		}
	}
}*/

/*function getNotifications() {
	//usar peixePost() com o peixelaranja JSFW
	$.get(
		'ajax-notifications.php',
		function(data) {
			var result = $.parseJSON(data);
			$('[data-notifications="pendente"]').html(result.pendente);
			$('[data-notifications="novo"]').html(result.novo);
			$('[data-notifications="finalizado"]').html(result.finalizado);
			$('[data-notifications="aguardando"]').html(result.aguardando);
			$('[data-notifications="feedback"]').html(result.feedback);
			$('[data-notifications="usuario"]').html(result.usuario_aguardando+result.usuario_concluido);
			$('[data-notifications="calendar"]').html(result.calendar_avisado+result.calendar_atrasado);
			if(result.novos_chamados && desktop_notifications){
				showDesktopNovosChamados(result.novos_chamados);
			}
			if(result.logout){
				$('head link[rel="icon"]').attr('href', 'images/notification-icons/off.png');
			} else {
				setIcon(result.pendente);
			}
		}
	)
	return false;
}*/

function getNotifications() {
	console.log('chamando a getCentralNotifications da central...');
	getCentralNotifications();
}

function gerenciarRequisicao(id, id_item) {
	modal = $('#modal-gerenciar-requisicao');
	//se já estiver aberto, soh recarrega o conteudo do modal.
	if(modal.hasClass('open')){
		modal.load('modal-requisicao-novo.php?id_requisicao='+id+'&id_servico='+id_item, function(){
			formInit();
			requisicao.start();
		})
	}
	else {
		$('#modal-gerenciar-requisicao').foundation('reveal', 'open', {
			url: 'modal-requisicao-novo.php',
			data: {
				id_requisicao: id,
				id_servico: id_item
			},
			animation: 'none',
			closeOnEsc: false,
			closeOnBackgroundClick: false,
			success: function(){
				setTimeout(function(){
					formInit();
					requisicao.start();
					if(win.width() < 750){
						scroll_top = document.querySelector('body').scrollTop;
						win.scrollTo('#modal-gerenciar-requisicao');
					}
					//sidebarInit();
				}, 250);					
			}
		});
	}
	/*if(typeof dev !== 'undefined' && dev == true){
	}else {
		$.colorbox({
			href:"modal-requisicao.php?&id="+id+"&item="+id_item,
			iframe: true,
			width: 1000,
			height: '98%',
			overlayClose: false,
			escKey: false,
			fixed: true
		});
	}*/
}

function closeModalRequisicao() {
	var alteracoes = false;
	if(requisicao.alteracoes_pendentes != $('#form-modal-requisicao').serialize()){
		var ans = confirm("Tem certeza que deseja fechar esta requisição sem salvar suas alterações?");
		if (ans!=true) {
			alteracoes = true;
		} 
	}
	if(!alteracoes){
		$('#modal-gerenciar-requisicao').foundation('reveal', 'close').removeClass('shown');
		if(win.width() < 750){
			win.scrollTo(scroll_top);
		}
	}
}

function reloadEquipamentosRelacionados() {
	peixeJSON($('#form-relacionar-equipamento').data('url-reload'), '', '', false);
	if(typeof window['reloadList'] == 'function'){
		reloadList();
	}
	return false;
}

function triggerProcurarEquipamentoRelacionado() {
	if($('#form-relacionar-equipamento').is(':visible')){
		$('#form-relacionar-equipamento').hide();
		$('#tag-nenhum-equipamento-relacionado').fadeIn('fast');
	}
	else {
		$('#tag-nenhum-equipamento-relacionado').hide();
		$('#form-relacionar-equipamento').fadeIn('fast', function(){
			if(peixeMediaQuery() != 'small'){
				$(this).find('input').focus();
			}
		})
	}
}

$(document).ready(function(){

	$(document).on('change', 'form input.error, form select.error, form textarea.error', function(){ $(this).removeClass('error'); })

	$(document).on('click', '.local-clearer:not(.disabled)', function(){
		$(this).addClass('disabled');
		$(this).closest('.item-local').find('.dica-local').fadeIn(250);
		$(this).closest('.item-local').find('.aux-local').removeClass('ok').removeAttr('readonly').val('').focus();
		$(this).closest('.item-local').find('input[name^=local]').val('');
	})

	/*$(document).on('change', '#form-requisicao select[name=tipo_servico]', function(){
		var option = $(this).find("option:selected");
		if($(option).attr('bem_movel') == '1'){
			showDetalhesRequisicao();
		} else {
			hideDetalhesRequisicao();	
		}
	})*/

	//impedindo o scroll do body quando o mouse está em cima do modal
	$('.scrollable').on('DOMMouseScroll mousewheel', function(ev) {
		if(win.width() > 750){
			var $this = $(this),
				scrollTop = this.scrollTop,
				scrollHeight = this.scrollHeight,
				height = $this.height(),
				delta = ev.originalEvent.wheelDelta,
				up = delta > 0;

			var prevent = function() {
				ev.stopPropagation();
				ev.preventDefault();
				ev.returnValue = false;
				return false;
			}
			
			if (!up && -delta > scrollHeight - height - scrollTop) {
				// Scrolling down, but this will take us past the bottom.
				$this.scrollTop(scrollHeight);
				return prevent();
			} else if (up && delta > scrollTop) {
				// Scrolling up, but this will take us past the top.
				$this.scrollTop(0);
				return prevent();
			}
		}
	});

	//trocando a lista do modal
	$(document).on('click', '.seletor-responsaveis span:not(.active)', function(){
		clicado = $(this);
		clicado.closest('div').find('span.active').removeClass('active');
		clicado.addClass('active');
		$(clicado.attr('data-hide')+':visible').fadeOut(100, function(){
			$(clicado.attr('data-show')+':hidden').fadeIn(100);
		})
	});

	//tratando os clicks na seleção de responsáveis
	$(document).on('click', '.lista-responsaveis .servidor-badge', function(){
		clicado = $(this);
		li = clicado.closest('li');
		req_status = $('#modal-requisicao-status');

		if(li.hasClass('active')){
			//desatribuindo alguém.
			li.removeClass('active');
			$('#responsaveis-atribuidos input[value="'+clicado.data('id')+'"]').prop('checked', false).trigger('change');
			if($('#responsaveis-atribuidos input:checked').length == 0){
				if(req_status.val() != requisicao.last.status){
					req_status.val(requisicao.last.status).trigger('change');
				}
			}
		}
		else {
			//atribuindo alguem
			li.addClass('active');
			$('#responsaveis-atribuidos input[value="'+clicado.data('id')+'"]').prop('checked', true).trigger('change');
			if(req_status.val() == requisicao.status.novo || req_status.val() == requisicao.status.aprovado){
				req_status.val(requisicao.status.atribuido).trigger('change');
			}
		}
	});

	//controlando a exibição da tag de nenhum responsavel atribuido
	$(document).on('change', '#responsaveis-atribuidos input', function(){
		checkResponsaveisAtribuidos();
	});

	//controlando alterações nos campos da requisição, para avisar ao cidadao na hora de fechar o modal.
	$(document).on('change', '#modal-gerenciar-requisicao input[name!="enviar_email"], #modal-gerenciar-requisicao select, #modal-gerenciar-requisicao textarea', function(e){
		//se mudou um campo importante, para o usuário, marcar como relevante.
		if(e.target.name == 'status' || e.target.name == 'prioridade' || e.target.name == 'comentario'){
			requisicao.alteracoes_relevantes = true;
		}
	});

	//tratando as alterações de status, espeficicamente
	$(document).on('change', '#modal-requisicao-status', function(){
		mudado = $(this);
		if(
			mudado.val() == requisicao.status.aguardando_aprovacao_diretoria ||
			mudado.val() == requisicao.status.aprovado ||
			mudado.val() == requisicao.status.atribuido ||
			mudado.val() == requisicao.status.concluido ||
			mudado.val() == requisicao.status.aguardando_requisitante
		)
		{
			requisicao.forceFeedback();			
		}
		else {
			requisicao.unforceFeedback();
		}
	});

	//controla a alteração de local em locais genericos
	$(document).on('focus', '.localpick', function(){
		focado = $(this);
		input = $(focado.data('target'));

		focado.autocomplete({
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
					focado.val('');
				}
			},
			delay: 1,
			select: function(event, ui) {
				if(ui.item.value != '-1'){
					this.value = ui.item.label;
					if(focado.data('readonly')){
						focado.prop('readonly', true);
					}
					input.val(ui.item.value);
				}
				else {
					this.value = '';
					input.val('');
				}
				return false;
			}
		});
	})

	$(document).on('click', '.trigger-alterar-local', function(){
		clicado = $(this);
		aux = $(clicado.data('target'));
		input = $(aux.data('target'));
		
		//reseta os valores dos locais
		aux.val('').prop('readonly', false).removeClass('ok').focus();
		input.val('');
	});

	//controla a busca dos materiais no modal
	$(document).on('focus', '#estoque_material_aux', function(){
		$(this).autocomplete({
			source: function(request, response){
				$.get("ajax-procurar-material.php", {term:request.term}, function(data){
					response($.map(data, function(item) {
						return {
							label: item.nome,
							value: item.id,
							unidade_medida: item.unidade_medida,
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
					$('#estoque_material').val('');
					$('#estoque_unidade_aux').html('');
				}
			},
			delay: 1,
			select: function(event, ui) {
				if(ui.item.value != ''){
					this.value = ui.item.label;
					$('#estoque_material').val(ui.item.value);
					$('#estoque_unidade_aux').html(ui.item.unidade_medida);
				}
				else {
					this.value = '';
					$('#estoque_material').val('');
					$('#estoque_unidade_aux').html('');
				}
				setTimeout(function(){
					$('#estoque_quantidade').focus();
				}, 25);
				return false;
			}
		});
	})

	//controla a busca dos equipamentos
	$(document).on('focus', '#estoque_equipamento_aux', function(){
		$(this).autocomplete({
			source: function(request, response){
				$.get("ajax-procurar-equipamento.php", {term:request.term}, function(data){
					response($.map(data, function(item) {
						return {
							label: item.nome,
							value: item.id,
							material_id: item.material_id,
							material_nome: item.material_nome,
							unidade_medida: item.unidade_medida,
							indisponivel: item.indisponivel
						}
					}))
				}, "json");
			},
			minLength: 1,
			dataType: "json",
			cache: false,
			focus: function(event, ui) {
				return false;
			},
			change: function (event, ui){
				if(!ui.item){
					$(this).val('').removeClass('ok');
					$('#estoque_equipamento').val('');
					$('#estoque_material').val('');
					$('#estoque_material_aux').val('').removeClass('ok').prop('readonly', false);
					$('#estoque_quantidade').val('').removeClass('ok').prop('readonly', false);
					$('#estoque_unidade_aux').html('');
				}
			},
			delay: 1,
			select: function(event, ui) {
				//não vinculado com material
				if(ui.item.material_id == ''){
					alert('Permissão negada:\n\n'+ui.item.label+'\n\nEste equipamento não está vinculado ao estoque.\n\nVocê precisa vinculá-lo a um tipo de material no estoque antes de atribuí-lo a uma requisição.');
				}
				else if(ui.item.indisponivel){
					alert('Permissão negada:\n\n'+ui.item.label+'\n\nEste equipamento não pode ser atribuído à requisição porque não está disponível no estoque.');
				}
				//tudo ok
				else if(ui.item.value != ''){
					$(this).val(ui.item.label).addClass('ok');
					$('#estoque_equipamento').val(ui.item.value);
					$('#estoque_material').val(ui.item.material_id);
					$('#estoque_material_aux').val(ui.item.material_nome).addClass('ok').prop('readonly', true);
					$('#estoque_quantidade').val(1).addClass('ok').prop('readonly', true);
					$('#estoque_unidade_aux').html(ui.item.unidade_medida);
					blur_me = $(this);
				}
				//nao selecionou nada
				else {
					$(this).val('').removeClass('ok');
					$('#estoque_equipamento').val('');
					$('#estoque_material').val('');
					$('#estoque_material_aux').val('').removeClass('ok').prop('readonly', false);
					$('#estoque_quantidade').val('').removeClass('ok').prop('readonly', false);
					$('#estoque_unidade_aux').html('');
					blur_me = $(this)
				}
				setTimeout(function(){
					if(typeof blur_me == 'object'){
						blur_me.blur();
					}
				}, 25);
				return false;
			}
		});
	})

	//controla a busca dos equipamentos no dashboard
	$(document).on('focus', '#localizar_equipamento', function(){
		$(this).autocomplete({
			source: function(request, response){
				$.get("ajax-procurar-equipamento.php", {term:request.term}, function(data){
					response($.map(data, function(item) {
						return {
							label: item.nome,
							value: item.id,
							modal_url: item.modal_url
						}
					}))
				}, "json");
			},
			minLength: 1,
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
				if(ui.item.value != ''){
					console.log(ui.item.modal_url);
					openColorBoxModal(ui.item.modal_url);
				}
				$(this).val('');
				return false;
			}
		});
	})

	//controla o relacionamento de equipamentos no modal de requisição
	$(document).on('focus', '#relacionar-equipamento', function(){
		$(this).autocomplete({
			source: function(request, response){
				$.get("ajax-procurar-equipamento.php", {term:request.term}, function(data){
					response($.map(data, function(item) {
						return {
							label: item.nome,
							value: item.id
						}
					}))
				}, "json");
			},
			minLength: 1,
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
				if(ui.item.value != ''){
					peixeJSON($('#form-relacionar-equipamento').data('url'), { equipamento_id: ui.item.value }, '', false);
				}
				$(this).val('');
				return false;
			}
		});
	})

	//submissão do formulário de requisicao
	$(document).on('submit', '#form-modal-requisicao', function(){
		form = $(this);
		error = false;
		status = $('#modal-requisicao-status').val();

		//fazendo todas as validações para poder salvar a requisição
		if(requisicao.alteracoes_relevantes && requisicao.feedbackSelected() === false){
			setPeixeMessage('<div class="error">Erro: Selecione uma opção.</div>');
			showPeixeMessage();
			requisicao.nudgeFeedback();
			error = true;
		}
		else if(requisicao.localEmBranco()){
			setPeixeMessage('<div class="error">Erro: Selecione um local.</div>');
			showPeixeMessage();
			requisicao.nudgeLocal();
			error = true;
		}
		//estatus de aguardando, mas o usuário não está recebendo comentário...
		else if(
			requisicao.alteracoes_relevantes &&
			(
				status == requisicao.status.aguardando_aprovacao_diretoria || 
				status == requisicao.status.aguardando_requisitante ||
				status == requisicao.status.cancelado ||
				status == requisicao.status.nao_aprovado
			) && 
			$.trim($('#modal-requisicao-comentario').val()) == '')
		{
			var ans = confirm("Você não escreveu um comentário para esta alteração. Deseja salvar assim mesmo? (não recomendado)");
			if (ans==true) {
				error = false;
			} else {
				error = true;
				nudge('#wrapper-modal-requisicao-comentario');
			}
		}

		//se nada deu errado, submeter.
		if(!error){
			peixeJSON(form.attr('action'), form.serialize(), function(){ reloadList(); }, true);
		}
		return false;
	});

	//atribuindo materiais de estoque à servicos
	$(document).on('click', '.trigger-atribuir-material', function(){
		clicado = $(this);
		peixeJSON(clicado.data('url'), {
			material_id: $('#estoque_material').val(),
			material_nome: $('#estoque_material_aux').val(),
			equipamento_id: $('#estoque_equipamento').val(),
			equipamento_nome: $('#estoque_equipamento_aux').val(),
			quantidade: $('#estoque_quantidade').val(),
			custo_unitario: $('#estoque_custo_unitario').val()
		}, '', false);
		return false;
	});

	//impedindo a submissão no enter nos campos de estoque
	$(document).on('keyup keypress', '#wrapper-controles-estoque input, #wrapper-tabela-materiais-utilizados', function(e){
	var code = e.keyCode || e.which; 
		if (code  == 13) {               
			e.preventDefault();
			return false;
		}		
	});
	
	//duplicando equipamento
	$(document).on('click', '.trigger-duplicar-equipamento', function(e){
		e.preventDefault();
		$.colorbox({
			href:"equipamento-duplicar.php?dbo_modal=1&id="+$(this).attr('href'),
			iframe: true,
			width: 900,
			height: 675,
			maxWidth: '100%',
			maxHeight: '100%',
			overlayClose: false,
			fixed: true
		});
	})

	//resetando o valor dos preços no blur
	$(document).on('blur', '.price', function(){
		foo = $(this);
		if(foo.val() == '0,00'){
			foo.val('');
		}
	});

	//mostrando os campos para cadastro de custo adicional
	$(document).on('click', 'a.trigger-custo-adicional', function(e){
		e.preventDefault();
		$(this).hide();
		$('#form-custo-adicional').show().find('input:first').focus();
		$('#estoque-helpers').fadeOut('fast');
	});

	$(document).on('click', '.trigger-incluir-custo-adicional', function(){
		clicado = $(this);
		clicado.removeClass('trigger-incluir-custo-adicional').addClass('trigger-waiting');
		peixeJSON(clicado.data('url'), { descricao: $('#custo_adicional_descricao').val(), custo: $('#custo_adicional_valor').val() }, '', false);
		return false;
	});

	//controle de campos exibidos no cadastro de estoque
	$(document).ready(function(){
		$(document).on('change', '#form-cadastro-estoque select[name="operacao"]', function(){
			mudado = $(this);
			if(mudado.val() == 'saida'){
				$('#form-cadastro-estoque input[name="custo_unitario"]').val('').removeProp('required').closest('.item').fadeOut();
			}
			else {
				$('#form-cadastro-estoque input[name="custo_unitario"]').prop('required', true).closest('.item').fadeIn();
			}
		});
	}) //doc.ready

	//modal das operações de equipamentos relacionados
	$(document).on('click', '.trigger-modal-equipamentos-relacionados', function(){
		$.colorbox({ 
			href: $(this).data('url'),
			initialWidth: 100,
			initialHeight: 100,
			maxWidth: '100%',
			scrolling: false,
			onComplete: function(){
				formInit();
			}
		});
	});

	//tratando a exibição do local dos equipamentos relacionados
	$(document).on('change', '#situacao-equipamento-relacionado', function(){
		mudado = $(this);
		selecionado = $(mudado.find('option:selected'));
		if(selecionado.data('show')){
			$('#wrapper-local-equipamento-relacionado').slideDown(function(){
				$.colorbox.resize();
			});
		}
		else {
			$('#wrapper-local-equipamento-relacionado').slideUp(function(){
				$.colorbox.resize();
			});
		}
		if(selecionado.data('solicitar-responsavel')){
			$('#wrapper-responsavel-assistencia_tecnica').peixeRequire().slideDown(function(){
				$.colorbox.resize();
			});
		}
		else {
			$('#wrapper-responsavel-assistencia_tecnica').peixeUnrequire().slideUp(function(){
				$.colorbox.resize();
			});
		}
		$('#local-situacao-aux').val(selecionado.data('local_aux'));
		$('#local-situacao').val(selecionado.data('local'));
	});

	//mostrando os detalhes do relacionamento do equipamento
	$(document).on('click', '.trigger-detalhes-equipamentos-relacionados', function(){
		clicado = $(this);
		if(clicado.hasClass('loaded')){
			if(clicado.hasClass('active')){
				clicado.removeClass('active').next('tr').hide();
			}
			else {
				clicado.addClass('active').next('tr').fadeIn();
			}
		}
		else {
			clicado.addClass('active loaded');
			peixePost(clicado.data('url'),{},function(data) {
					result = $.parseJSON(data);
					$(result.tabela).insertAfter(clicado).fadeIn();
				}
			)
		}
		return false;
	});

	$(document).on('click', '.trigger-gerenciar-requisicao-for-medium-up', function(){
		if(peixeMediaQuery() != 'small'){
			clicado = $(this);
			gerenciarRequisicao(clicado.data('requisicao_id'), clicado.data('requisicao_item_id'));
		}
	});

	/* script que roda as notificações */

	/*setInterval(function(){
		getNotifications()
	}, 60000);*/

}) //doc.ready


//função para mostrar e esconder as mensagens de erro/etc.

var peixe_message_timer;
var wrapper_peixe_message;

function showPeixeMessage() {
	clearTimeout(peixe_message_timer);
	wrapper_peixe_message = $('div.wrapper-message');
	if(wrapper_peixe_message.text() != '') {
		peixe_message_timer = setTimeout(function(){
			slidePeixeMessageDown();
			peixe_message_timer = setTimeout(function(){
				slidePeixeMessageUp();
			}, 4500);
		}, 300);
	}
}

function slidePeixeMessageDown() {
	clearTimeout(peixe_message_timer);
	wrapper_peixe_message.removeClass('closed');
}

function slidePeixeMessageUp() {
	clearTimeout(peixe_message_timer);
	wrapper_peixe_message.addClass('closed');
	peixe_message_timer = setTimeout(function(){
		removePeixeMessage();
	}, 1500);
}

function peixeJSON(action, args, callback, log, method) {
	if(typeof method == 'undefined' || method == 'post'){
		function_name = 'peixePost';
	}
	else {
		function_name = 'peixeGet';
	}
	window[function_name](
		action,
		args,
		function(data) {
			//mostrando log
			if(log == true){
				console.log(data);
			}
			var result = $.parseJSON(data);
			if(result.message){
				setPeixeMessage(result.message);
				showPeixeMessage();
			}
			if(result.reload){
				var html = '';
				peixeGet(document.URL, function(d) {
					html = $.parseHTML(d);
					result.reload.forEach(function(value) {
						peixeReload(value, html);
					});
				})
			}
			if(result.html){
				for(var key in result.html)
				{
					$(key).fadeHtml(result.html[key]);
				}
			}
			if(result.callback){
				result.callback.forEach(function(value) {
					if (typeof window[value] == 'function') {
						window[value]();
					}
				});
			}
			if(result.redirect){
				window.location = result.redirect;
			}
			if(result.eval){
				eval(result.eval)
			}
			//tratando o callback customizado
			if(typeof callback == 'function'){
				callback(result);
			}
			else if(typeof window[callback] == 'function'){
				window[callback]();
			}
			else if(typeof callback == 'string'){
				eval(callback);
			}
		}
	)
	return false;
}

function removePeixeMessage() {
	clearTimeout(peixe_message_timer);
	wrapper_peixe_message.addClass('no-transition').addClass('closed').html('').removeClass('no-transition');
}

//funcão para setar a mensagem
function setPeixeMessage(message) {
	$('div.wrapper-message').html(message);
}

function peixeReload(item, html, callback){
	content = $(html).find(item).html();
	if(typeof content === 'undefined'){
		$(item).fadeOut(function(){
			$(this).remove();
		});
	}
	else {
		if($(item).is(':visible')){
			$(item).fadeHtml(content, callback);
		}
		else {
			$(item).html(content, callback);
		}
	}
}

function showPeixeLoader() {
	$('.peixe-ajax-loader').fadeIn('fast');
	$('.peixe-screen-freezer').show();
}

(function($) {

	//function que funciona como o .html() do jQuery, mas com um efeito de fade
	$.fn.fadeHtml = function(content, callback) {
		return this.each(function() {
			$(this).fadeTo('fast', 0, function(){
				$(this).html(content);
				$(this).fadeTo('fast', 1, callback);
			});
		});
	}

	//função para remover determinados inputs do Foundation Abide
	$.fn.peixeUnrequire = function(callback) {
		var size = this.length-1;
		return this.each(function(i) {
			if(this.nodeName != 'INPUT' && this.nodeName != 'SELECT' && this.nodeName != 'TEXTAREA'){
				$(this).find('[required]').removeAttr('required').removeClass('required').attr('maybe-required', '');
			}
			else {
				$(this).removeAttr('required').removeClass('required').attr('maybe-required', '');
			}
			if(size == i){
				if(typeof callback == 'function'){
					callback.call(this);
				}
			}
		});
	}

	//função para adicionar determinados inputs no Foundation Abide
	$.fn.peixeRequire = function(callback) {
		var size = this.length-1;
		return this.each(function(i) {
			if(this.nodeName != 'INPUT' && this.nodeName != 'SELECT' && this.nodeName != 'TEXTAREA'){
				$(this).find('[maybe-required]').removeAttr('maybe-required').addClass('required').attr('required', '');
			}
			else {
				$(this).removeAttr('maybe-required').addClass('required').attr('required', '');
			}
			if(size == i){
				if(typeof callback == 'function'){
					callback.call(this);
				}
			}
		});
	}

	//funcao para equalizar a altura dos elementos
	$.fn.peixeEqualizeHeights = function(callback){

		var size = this.length-1;
		var height = 0, reset = "auto";
  
		return this
			.css("height", reset)
			.each(function() {
				height = Math.max(height, this.offsetHeight);
			})
			.css("height", height)
			.each(function(i) {
				var h = this.offsetHeight;
				if (h > height) {
					$(this).css("height", height - (h - height));
				};
				if(size == i){
					if(typeof callback == 'function'){
						callback.call(this);
					}
				}
			});
	}

})(jQuery);	

//mostra o loader de AJAX
function hidePeixeLoader() {
	$('.peixe-ajax-loader').delay(200).fadeOut('fast');
	$('.peixe-screen-freezer').hide();
}

//funciona igual ao .post() de jQuery, mas com loader
function peixePost(url, args, callback) {
	showPeixeLoader();
	$.post(url,args,callback).complete(function(){ hidePeixeLoader() });
}

//funciona igual ao .get() de jQuery, mas com loader
function peixeGet(url, args, callback) {
	showPeixeLoader();
	$.get(url,args,callback).complete(function(){ hidePeixeLoader() });
}

function peixeAddRequiredBullets() {
	//colocando * nos requireds
	$('form .required, form [required]').closest('.item').find('label:not(:has(.bullet-required)):first').append(' <span class="bullet-required">*</span>');
}

//funcoes para tratamento de upload de arquivos com ajax. O markup está setado para foundation

// Function that will allow us to know if Ajax uploads are supported
function supportAjaxUploadWithProgress() {
	return supportFileAPI() && supportAjaxUploadProgressEvents() && supportFormData();

	// Is the File API supported?
	function supportFileAPI() {
		var fi = document.createElement('INPUT');
		fi.type = 'file';
		return 'files' in fi;
	};

	// Are progress events supported?
	function supportAjaxUploadProgressEvents() {
		var xhr = new XMLHttpRequest();
		return !! (xhr && ('upload' in xhr) && ('onprogress' in xhr.upload));
	};

	// Is FormData supported?
	function supportFormData() {
		return !! window.FormData;
	}
}

function peixeAjaxFileUploadSingleFile(input_id, action) {

	//console.log(action);
	input = $('#'+input_id);
	var formData = new FormData();

	// FormData only has the file
	formData.append('peixe_ajax_file_upload_file', input[0].files[0]);

	// Code common to both variants
	peixeAjaxFileUploadSendXHRequest(formData, action, input_id);
}

// Once the FormData instance is ready and we know
// where to send the data, the code is the same
// for both variants of this technique
function peixeAjaxFileUploadSendXHRequest(formData, uri, input_id) {
	// Get an XMLHttpRequest instance
	var xhr = new XMLHttpRequest();

	// Set up events
	xhr.upload.addEventListener('loadstart', peixeAjaxFileUploadonloadstartHandler, false);
	xhr.upload.addEventListener('progress', peixeAjaxFileUploadonprogressHandler, false);
	xhr.upload.addEventListener('load', peixeAjaxFileUploadonloadHandler, false);
	xhr.upload.input_id = input_id;
	xhr.addEventListener('readystatechange', peixeAjaxFileUploadonreadystatechangeHandler, false);
	xhr.input_id = input_id;

	// Set up request
	xhr.open('POST', uri, true);

	// Fire!
	xhr.send(formData);
}

// Handle the start of the transmission
function peixeAjaxFileUploadonloadstartHandler(evt) {
	var container = $("#"+evt.target.input_id).next('.peixe-ajax-upload-status');
	//upload started
	container.prev('input[type="file"]').hide();
	container.find('.upload-progress').show();
	container.find('.upload-sending').show();
}

// Handle the end of the transmission
function peixeAjaxFileUploadonloadHandler(evt) {
	var container = $("#"+evt.target.input_id).next('.peixe-ajax-upload-status');
	//upload ended
}

// Handle the progress
function peixeAjaxFileUploadonprogressHandler(evt) {
	var container = $("#"+evt.target.input_id).next('.peixe-ajax-upload-status');
	var progress_bar = container.find('.progress .meter');
	var percent = evt.loaded/evt.total*100;
	progress_bar.css('width', percent+'%');
}

// Handle the response from the server
function peixeAjaxFileUploadonreadystatechangeHandler(evt) {
	var status, text, readyState;
	var container = $("#"+evt.target.input_id).next('.peixe-ajax-upload-status');

	$("#"+evt.target.input_id).trigger('uploadStart', {});

	try {
		readyState = evt.target.readyState;
		text = evt.target.responseText;
		status = evt.target.status;
	}
	catch(e) {
		return;
	}

	if (readyState == 4 && status == '200' && evt.target.responseText) {
		//console.log(evt.target.responseText);
		var data = $.parseJSON(evt.target.responseText);
		if(data.error){
			//erro de qualquer natureza na hora do upload
			container.find('.upload-progress').fadeOut();
			container.find('.upload-sending').fadeOut(function(){
				container.find('.upload-error').fadeIn().find('span').text(data.error);
			});
			$("#"+evt.target.input_id).trigger('uploadCanceled', {});
		}
		else {
			//tudo ok, disparar evento
			//var event = new CustomEvent('upload-done', { 'detail': { 'old_file_name': data.old_name, 'new_file_name': data.name } });
			//$("#"+evt.target.input_id)[0].dispatchEvent(event);
			$("#"+evt.target.input_id).trigger('uploadDone', { 'old_file_name': data.old_name, 'new_file_name': data.name, data: data });

			container.find('input[type="text"]').val(data.name);
			container.find('.upload-sending').hide();
			container.find('.upload-success').show();
			container.find('.upload-success').fadeOut(function(){
				container.find('.upload-progress').fadeOut(function(){
					container.find('.upload-file-placeholder').fadeIn().find('.uploaded-file').text(data.old_name);
				})
			})
		}
	}
}

//reset the ajax file upload
function peixeAjaxFileUploadRetry(input_id) {
	var input = $('#'+input_id);
	var container = input.next('.peixe-ajax-upload-status');

	//reseta todo o campo de upload
	container.find('.upload-progress:visible').hide();
	container.find('.upload-sending:visible').hide();
	container.find('.upload-success:visible').hide();
	container.find('.upload-error:visible').hide();
	container.find('.upload-file-placeholder:visible').hide();
	container.find('input[type="text"]').val('');
	input.val('').fadeIn();
}

function peixeAjaxFileUploadInit() {
	$('[peixe-ajax-file-upload]').each(function(){
		var foo = $(this);
		//verifica se a função já não rodou para este elemento...
		if(!foo.hasClass('peixe-ajax-file-upload-ready')){
			foo.addClass('peixe-ajax-file-upload-ready');
			var required = ((foo.prop('required'))?('required'):(''));
			var name = foo.attr('name');
			var aux_name = name+"_aux";

			//acertando o nome do input para aux
			foo.attr('name', aux_name);

			//inserindo toda o container do upload
			$('<div class="peixe-ajax-upload-status"><input type="text" name="'+name+'" value="" style="display: none;" '+required+'/><div class="upload-progress progress radius" style="display: none;"><span class="meter" style="width: 0%;"></span></div><div class="upload-sending font-14 margin-bottom" style="display: none;"><i class="fa-spinner fa-spin"></i> <span>Enviando...</span></div><div class="upload-success font-14 margin-bottom" style="display: none;"><i class="fa-check"></i> <span>Sucesso!</span></div><div class="upload-error font-14 alert-box radius alert" style="display: none;"><i class="fa-refresh pointer trigger-peixe-ajax-upload-retry right" title="Tentar novamente..."></i> <span>Erro ao enviar o arquivo</span></div><div class="upload-file-placeholder font-14 alert-box radius" style="display: none;"><i class="fa-file-text-o"></i> <span class="uploaded-file">nome-do-arquivo.php</span> <a href="#" style="color: #fff;" class="margin-bottom trigger-peixe-ajax-upload-remove" title="Remover este arquivo"><i class="fa-close right"></i></a></div></div>').insertAfter(foo);

		}
	})
}

function peixeMediaQuery() {
	if(window.innerWidth < 768){
		return 'small';
	}
	else {
		return 'large';
	}
}

function peixeInit() {
	peixeAddRequiredBullets();
	peixeAjaxFileUploadInit();
}

$(document).ready(function(){

	peixeInit();

	//mostra mensagens
	showPeixeMessage();

	// tratando mensagens
	$(document).on('click', 'div.wrapper-message', function(){
		removePeixeMessage();
	});

	$(document).on('mouseenter', 'div.wrapper-message', function(){
		clearTimeout(peixe_message_timer);
	});

	$(document).on('mouseleave', 'div.wrapper-message', function(){
		setTimeout(function(){
			slidePeixeMessageUp();
		}, 3000);
	});

	//botao de submit que só funciona com javascript, e impede dupla submissão
	$(document).on('click', 'form .submitter', function(e){
		e.preventDefault();
		$(this).closest('form').submit();
	})

	//tira o destaque de erro dos inputs, quando mudados
	/*$(document).on('focus', 'form .error', function(){
		$(this).removeClass('error');
	})*/
	$(document).on('focus', '.item.validation-error input, .item.validation-error select, .item.validation-error textarea', function(){
		$(this).closest('.item.validation-error').removeClass('validation-error');
	});

	//fazendo upload por ajax no onChange dos inputs file
	$(document).on('change', '[peixe-ajax-file-upload]', function(){
		input = $(this);
		action = ((typeof input.data('action') != 'undefined')?(input.data('action')):('peixe-ajax-file-upload.php'));
		peixeAjaxFileUploadSingleFile(input.attr('id'), action);
	});

	//resetando o formulário quando dá algum erro de upload
	$(document).on('click', '.trigger-peixe-ajax-upload-retry', function(){
		peixeAjaxFileUploadRetry($(this).closest('.peixe-ajax-upload-status').prev('input[type="file"]').attr('id'));
	});

	//resetando o formulário quando o usuário remover o arquivo uploadedado
	$(document).on('click', '.trigger-peixe-ajax-upload-remove', function(e){
		e.preventDefault();
		var ans = confirm("Tem certeza que deseja remover este arquivo?");
		if (ans==true) {
			peixeAjaxFileUploadRetry($(this).closest('.peixe-ajax-upload-status').prev('input[type="file"]').attr('id'));
		} 
	});

	//confirmando uma ação, em um click em um link por exemplo.
	$(document).on('click', '.peixe-confirm', function(e){
		//e.preventDefault();
		clicado = $(this);
		var ans = confirm(clicado.data('confirm').replace(/\\n/g, '\n'));
		if (ans==true) {
			return true;
		} else {
			return false;
		}
	});

	$(document).on('click', '.stop-propagation', function(e){
		e.stopPropagation();
	});

	$(document).on('submit', 'form.peixe-json', function(){
		form = $(this);
		error = false;
		if(typeof form.data('confirm') != 'undefined' && $.trim(form.data('confirm')) != ''){
			var ans = confirm(form.data('confirm').replace(/\\n/g, '\n'));
			if (ans==true) {
				error = false;
			} else {
				error = true;
			}
		}
		if(!error){
			peixeJSON(form.attr('action'), form.serialize(), '', ((typeof form.attr('peixe-log') != 'undefined')?(true):(false)));
		}
		return false;
	});

	$(document).on('click', 'a.peixe-json', function(e){
		e.preventDefault();
		clicado = $(this);
		error = false;
		if(typeof clicado.data('confirm') != 'undefined' && $.trim(clicado.data('confirm')) != ''){
			var ans = confirm(clicado.data('confirm').replace(/\\n/g, '\n'));
			if (ans==true) {
				error = false;
			} else {
				error = true;
			}
		}
		if(!error){
			peixeJSON(clicado.attr('href'), '', ((typeof clicado.data('callback') != 'undefined')?(clicado.data('callback')):('')), ((typeof clicado.attr('peixe-log') != 'undefined')?(true):(false)), 'get');
		}
		return false;
	});

	//controlando seções de conteudo (tipo accordion)
	$(document).on('click', '.trigger-peixe-section', function(){
		clicado = $(this);
		section = clicado.closest('.peixe-section');
		if(section.hasClass('open')){
			section.removeClass('open').addClass('closed');
		} else {
			section.removeClass('closed').addClass('open');
		}
	});

	//controlando os small toggles
	$(document).on('click', '.trigger-small-toggle', function(){
		clicado = $(this);
		section = clicado.closest('.section-small-toggle');
		if(section.hasClass('shown')){
			section.removeClass('shown').addClass('closed');
		} else {
			section.removeClass('closed').addClass('shown');
		}
	});

	//colcando ajax loader e screen freezer
	$('body:not(:has(.peixe-ajax-loader))').prepend('<div class="peixe-ajax-loader"><i class="fa-spinner fa-spin"></i> <span>Carregando...</span></div>');
	$('body:not(:has(.peixe-screen-freezer))').prepend('<div class="peixe-screen-freezer"></div>');
	$('body:not(:has(.wrapper-message))').prepend('<div class="wrapper-message closed"></div>');

}) //doc.ready
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
	$('form .required, form [required]').closest('.item').find('label:not(:has(.bullet-required))').append(' <span class="bullet-required">*</span>');
}

function peixeInit() {
	peixeAddRequiredBullets();
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
	$(document).on('click', 'form .submitter', function(){
		$(this).closest('form').submit();
	})

	//tira o destaque de erro dos inputs, quando mudados
	/*$(document).on('focus', 'form .error', function(){
		$(this).removeClass('error');
	})*/
	$(document).on('focus', '.item.validation-error input, .item.validation-error select, .item.validation-error textarea', function(){
		$(this).closest('.item.validation-error').removeClass('validation-error');
	});

	//colcando ajax loader e screen freezer
	$('body:not(:has(.peixe-ajax-loader))').prepend('<div class="peixe-ajax-loader">Carregando...</div>');
	$('body:not(:has(.peixe-screen-freezer))').prepend('<div class="peixe-screen-freezer"></div>');
	$('body:not(:has(.wrapper-message))').prepend('<div class="wrapper-message closed"></div>');

}) //doc.ready
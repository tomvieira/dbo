/* handler for the message setTimeout */
var message_timer;

function showDboMessage() {
	showPeixeMessage();
}

function setMessage(message) {
	$('div.wrapper-message').html(message);
}

function activeMainNav(menu) {
	$('#menu-'+menu).addClass('active');
}

function openDboModal(url, tamanho, callback) {
	if(typeof tamanho == 'undefined'){
		tamanho = 'medium';
	}
	$('#modal-dbo-'+tamanho).foundation('reveal', 'open', {
		url: url,
		success: function(){
			setTimeout(function(){
				if(typeof callback == 'function'){
					callback(result);
				}
				else if(typeof window[callback] == 'function'){
					window[callback]();
				}
				else if(typeof callback == 'string'){
					eval(callback);
				}
			}, 200);
		}
	})
}

function openColorBoxModal(url, width, height) {
	var width = ((typeof width != 'undefined')?(width):(1000));
	var height = ((typeof height != 'undefined')?(height):('98%'));
	height = (($(window).width() < 810)?('90%'):(height));
	$.colorbox({
		href: url,
		iframe: true,
		width: width,
		height: height,
		maxWidth: '100%',
		maxHeight: '100%',
		overlayClose: false,
		escKey: false,
		fixed: true
	});
}

$(document).ready(function(){
	//fade nas mensagens

	showDboMessage();

	/* modals */
	$(document).on('click', '[rel="modal"]', function(e){
		e.preventDefault();
		e.stopPropagation();
		clicado = $(this);
		openColorBoxModal(clicado.attr('href'), ((typeof clicado.attr('data-width') != 'undefined')?(clicado.attr('data-width')):(1000)), ((typeof clicado.attr('data-height') != 'undefined')?(clicado.attr('data-height')):('98%')));
	});

	$(document).on('click', '[rel^="lightbox"]', function(e){
		e.preventDefault();
		clicado = $(this);
		$.colorbox({
			href:clicado.attr('href'),
			fixed: true,
			maxWidth: '95%',
			maxHeight: '95%'
		});
	});

	//change password
	$(document).on('click', '.trigger-change-password', function(e){
		e.preventDefault();
		$('#modal-change-password').foundation('reveal', 'open', {
			url: 'modal-dbo-change-password.php'
		});
	});

	//abrindo modais com trigger
	$(document).on('click', '.trigger-dbo-modal', function(e){
		e.preventDefault();
		clicado = $(this);
		openDboModal(clicado.data('url'), clicado.data('tamanho'), clicado.data('callback'));
	});

	$(document).on('click', '.trigger-colorbox-modal', function(e){
		e.preventDefault();
		clicado = $(this);
		openColorBoxModal(clicado.data('url'), clicado.data('width'), clicado.data('height'));
	});

});

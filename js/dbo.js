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

$(document).ready(function(){
	//fade nas mensagens

	showDboMessage();

	/* modals */
	$(document).on('click', '[rel="modal"]', function(e){
		e.preventDefault();
		e.stopPropagation();
		clicado = $(this);
		var w = ((typeof clicado.attr('data-width') != 'undefined')?(clicado.attr('data-width')):(1000));
		var h = ((typeof clicado.attr('data-height') != 'undefined')?(clicado.attr('data-height')):('98%'));
		h = (($(window).width() < 810)?('90%'):(h));
		console.log($(document).width());
		$.colorbox({
			href:clicado.attr('href'),
			iframe: true,
			width: w,
			height: h,
			maxWidth: '100%',
			maxHeight: '100%',
			overlayClose: false,
			escKey: false,
			fixed: true
		});
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

});

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

	//password change
	$(document).on('click', ".trigger-user-box", function(e){
		e.preventDefault();
		$(".user-box").fadeToggle('fast');
	})

	$(document).on('submit', "#form-change-password", function(){
		$.post(
			$(this).attr('action'),
			$(this).serialize(),
			function(data) {
				var result = $.parseJSON(data);
				if(result.message){
					setMessage(result.message);
					showDboMessage();
				}
				if(result.success){
					$(".user-box").fadeToggle('fast');
					$("#form-change-password input[type=password]").val('');
				}
			}
		)
		return false;
	})

});

<?

	require_once('local-defines.php');

	/* variaveis globais */

	$global_ids_servidores = getIdsServidores();
	$global_ids_prestadores = getIdsPrestadores();

	/* procurar usar estes objetos no sistema, em locais especificos */
	$_pes = new pessoa(loggedUser());
	$_opt = new opcao();
	$_pessoa = new pessoa();
	$_local = new local();
	$_serv = new requisicao_item();
	$_conf = new config(1);
	$_manut = new equipamento_manutencao();
	$_calendar = new calendar();
	$_sistema = new sistema();
	$_sistema->loadContext();

	/* definindo contexto do sistema e notificacoes */
	$aux = $_opt->get('contexto_areas', $_pes->id);
	$_system['contexto_areas'] = (($aux)?(explode("-", $aux)):(false));
	$aux = $_opt->get('notificacoes_areas', $_pes->id);
	$_system['notificacoes_areas'] = (($aux)?(explode("-", $aux)):(false));
	$_system['desktop_notifications'] = $_opt->get('desktop_notifications', $pes->id);

	$_STATUS_NAMES = array(
		STATUS_NOVO => $_serv->getValue('status', STATUS_NOVO),
		STATUS_AGUARDANDO_APROVACAO_DIRETORIA => $_serv->getValue('status', STATUS_AGUARDANDO_APROVACAO_DIRETORIA),
		STATUS_APROVADO => $_serv->getValue('status', STATUS_APROVADO),
		STATUS_ATRIBUIDO => $_serv->getValue('status', STATUS_ATRIBUIDO),
		STATUS_AGUARDANDO_REQUISITANTE => $_serv->getValue('status', STATUS_AGUARDANDO_REQUISITANTE),
		STATUS_EM_ANDAMENTO => $_serv->getValue('status', STATUS_EM_ANDAMENTO),
		STATUS_CONCLUIDO => $_serv->getValue('status', STATUS_CONCLUIDO),
		STATUS_NAO_APROVADO => $_serv->getValue('status', STATUS_NAO_APROVADO),
		STATUS_CANCELADO => $_serv->getValue('status', STATUS_CANCELADO),
		STATUS_AVALIADO => $_serv->getValue('status', STATUS_AVALIADO)
	);
	
	define(STATUS_SERVICO_CONCLUIDO, 6);

	define(STATUS_EQUIPAMENTO_NO_ESTOQUE, 1);
	define(STATUS_EQUIPAMENTO_EM_USO, 2);
	define(STATUS_EQUIPAMENTO_EMPRESTADO, 3);
	define(STATUS_EQUIPAMENTO_CONSUMIDO, 4);
	define(STATUS_EQUIPAMENTO_EM_MANUTENCAO, 5);
	define(STATUS_EQUIPAMENTO_NA_ASSISTENCIA_TECNICA, 6);
	define(STATUS_EQUIPAMENTO_NAO_LOCALIZADO, 7);
	define(STATUS_EQUIPAMENTO_BAIXA, 8);

	define(SITE_URL, str_replace("/dbo", "", DBO_URL));

	/* ------------------------------------------------------------------------------------------------------ */
	/* --- FUNCOES ------------------------------------------------------------------------------------------ */
	/* ------------------------------------------------------------------------------------------------------ */

	function reais($num, $cifrao = false)
	{
		return (($cifrao)?('R$ '):('')).number_format($num, 2, ',', '.');
	}

	function dashBoard()
	{
		global $_pes;
		global $_system;

		ob_start();
		$sql = "SELECT * FROM categoria_servico WHERE id IN (SELECT categoria_servico FROM pessoa_categoria_servico WHERE pessoa = '".$_pes->id."') ORDER BY nome";
		$res = dboQuery($sql);
		if(dboAffectedRows())
		{
			while($lin = dboFetchObject($res))
			{
				$minhas_areas[] = $lin;
			}
		}
		$area = new categoria_servico("ORDER BY nome");
		if($area->size())
		{
			do {
				$areas[] = clone $area;
			}while($area->fetch());
		}
		?>
		<div id="dashboard">
			<div class="row">
				<div class="large-12 columns">
					<h4>Dashboard</h4>

					<div class="row">
						<div class="small-4 columns"><img src="<?= $_pes->getFoto(); ?>" class="th"/></div>
						<div class="small-8 columns">
							<h5><?= $_pes->nome; ?></h5>
							<?
								if(sizeof($minhas_areas))
								{
									foreach($minhas_areas as $key => $value)
									{
										?><span class="label radius"><?= $value->nome ?></span> <?
									}
								}
							?>
						</div>
					</div>
					
					<hr>
					
					<label>Localizar equipamento</label>
					<div class="row collapse">
						<div class="large-12 columns"><input type="text" name="localizar_equipamento" value="" placeholder="Digite código, AI ou patrimônio..." class="no-margin" id="localizar_equipamento"/></div>
					</div>

					<hr>
					<h3>Preferências</h3>

					<form method="post" action="ajax-dashboard-actions.php" class="no-margin form-dashboard" id="form-atualizar-preferencias">

						<div id="wrapper-desktop-notifications">

							<div class="row" id="switch-desktop-notifications">
								<div class="small-9 columns"><label>Notificações de desktop <i class="help icon" title="Mostra notificações no seu desktop quando uma nova requisição chega ao sistema. Atualmente, este recurso só funciona no Google Chrome."></i></label></div>
								<div class="small-3 columns">
									<div class="switch tiny round">
										<input id="desktop-notifications-on" name="desktop_notifications" value="0" type="radio" <?= ((!$_system['desktop_notifications'])?('checked'):('')) ?>>
										<label for="desktop-notifications-on" onclick="">Off</label>

										<input id="desktop-notifications-off" name="desktop_notifications" value="1" type="radio" <?= (($_system['desktop_notifications'])?('checked'):('')) ?>>
										<label for="desktop-notifications-off" onclick="">On</label>

										<span></span>
									</div>		
								</div>
							</div>

						</div>
						
						<label>Notificações <i class="icon help" title='Mostra somente os itens abaixo nas Notificações.'></i></label>
						<?
							if(sizeof($areas))
							{
								foreach($areas as $key => $value)
								{
									?><span style="white-space: nowrap;"><input type="checkbox" <?= ((@in_array($value->id, $_system['notificacoes_areas']))?('checked'):('')) ?> name="notificacoes[<?= $value->id ?>]" value="<?= $value->id ?>" id='input-notificacoes-<?= $value->id ?>' rel="<?= $value->id ?>"/><label for="input-notificacoes-<?= $value->id ?>"><?= $value->nome ?></label></span> <?
								}
							}						
						?><br /><br />	
						<label>Contexto do sistema <i class="icon help" title='Mostra somente os itens abaixo na seção "Consulta".'></i></label>
						<?
							if(sizeof($areas))
							{
								foreach($areas as $key => $value)
								{
									?><span style="white-space: nowrap;"><input type="checkbox" <?= ((@in_array($value->id, $_system['contexto_areas']))?('checked'):('')) ?> name="contexto[<?= $value->id ?>]" value="<?= $value->id ?>" id='input-contexto-<?= $value->id ?>' rel="<?= $value->id ?>"/><label for="input-contexto-<?= $value->id ?>"><?= $value->nome ?></label></span> <?
								}
							}						
						?>	
						<input type="hidden" name="action" value="update-preferencias"/>
						<div class="text-right"><input type="submit" name="" value="Salvar preferências" class="button radius"/></div>
					</form>
				
				</div>
			</div>
		</div>
		<script>
			$(document).ready(function(){

				desktop_notifications = <?= (($_system['desktop_notifications'])?('true'):('false')) ?>

				$('#dashboard').on('change', 'input[id^="input-notificacoes"]', function(){
					var clicado = $(this);
					if(clicado.prop('checked')){
						$('input[id^="input-contexto"][rel="'+clicado.attr('rel')+'"]').prop('checked', true);
					}
				});

				/*				if(desktop_notifications){
					console.log('ok');
					setTimeout(function(){
						showDesktopNotification('1', 'images/help.png', 'Novo Teste', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nesciunt voluptatem!', '');
					}, 3000);
				} */

				$('#dashboard').on('change', 'input[id^="input-contexto"]', function(){
					var clicado = $(this);
					if(!clicado.prop('checked')){
						$('input[id^="input-notificacoes"][rel="'+clicado.attr('rel')+'"]').prop('checked', false);
					}
				});

				$('#dashboard').on('change', 'input[id^="desktop-notifications"]', function(){
					if(window.webkitNotifications.checkPermission() != 0){
						window.webkitNotifications.requestPermission();
					}
				});

				$('#dashboard').on('submit', '.form-dashboard', function(){
					peixePost(
						$(this).attr('action'),
						$(this).serialize(),
						function(data) {
							var result = $.parseJSON(data);
							if(result.message){
								setPeixeMessage(result.message);
								showPeixeMessage();
							}
						}
					)
					return false;
				});
			}) //doc.ready		
		</script>
		<?
		return ob_get_clean();
	}

	function teste ($param)
	{
		return "<span style='color: red !important; font-weight: bold !important;'>".$param."</span>";
	}

	function updateTelefone($user, $telefone)
	{
		global $_pessoa;
		$sql = "UPDATE ".$_pessoa->__module_scheme->tabela." SET telefone = '".$telefone."' WHERE user = '".$user."'";
		dboQuery($sql);
	}

	function formataVariacao($var)
	{
		if($var < 0)
		{
			return "<span style='color: red;'>$var</span>";
		}
		return $var;
	}



	function moduloNumero($num)
	{
		return abs($num);
	}

	function trataNome($nome)
	{
		$nome = trim($nome);
		$partes = explode(" ", $nome);
		foreach($partes as $key => $value)
		{
			setlocale(LC_ALL, 'pt_BR');
			if(strlen($value))
			{
				if(strlen($value) > 2)
				{
					$nome_final[] = ucfirst(trim(deepLower($value)));
				}
				else
				{
					$nome_final[] = trim(deepLower($value));
				}
			}
		}
		return @implode(" ", $nome_final);
	}

	function deepLower($texto){ 
		//Letras minúsculas com acentos 
		$texto = strtr($texto, " 
		ĄĆĘŁŃÓŚŹŻABCDEFGHIJKLMNOPRSTUWYZQ 
		XVЁЙЦУКЕНГШЩЗХЪФЫВАПРОЛДЖЭЯЧСМИТЬБЮ 
		ÂÀÁÄÃÊÈÉËÎÍÌÏÔÕÒÓÖÛÙÚÜÇ 
		", " 
		ąćęłńóśźżabcdefghijklmnoprstuwyzq 
		xvёйцукенгшщзхъфывапролджэячсмитьбю 
		âàáäãêèéëîíìïôõòóöûùúüç 
		"); 
		return strtolower($texto); 
	} 

	function getIdsServidores()
	{
		$ids = array();
		$sql = "SELECT pessoa FROM pessoa_perfil WHERE perfil = ".ID_PERFIL_SERVIDOR." OR perfil = ".ID_PERFIL_ESTAGIARIO; 
		$res = dboQuery($sql);
		if(dboAffectedRows())
		{
			while($lin = dboFetchObject($res))
			{
				$ids[] = $lin->pessoa;
			}
		}
		return $ids;
	}

	function getIdsPrestadores()
	{
		$ids = array();
		$sql = "SELECT pessoa FROM pessoa_perfil WHERE perfil = ".ID_PERFIL_PRESTADOR_SERVICO; 
		$res = dboQuery($sql);
		if(dboAffectedRows())
		{
			while($lin = dboFetchObject($res))
			{
				$ids[] = $lin->pessoa;
			}
		}
		return $ids;
	}

	function getIdsServidoresAtribuidos()
	{
		$ids = array();
		$sql = "
			SELECT 
				pp.pessoa AS pessoa
			FROM 
				pessoa_perfil pp,
				requisicao_item_servidor ris,
				requisicao_item ri
			WHERE
				(
					pp.perfil = ".ID_PERFIL_SERVIDOR." OR
					pp.perfil = ".ID_PERFIL_ESTAGIARIO."
				) AND
				pp.pessoa = ris.servidor AND
				ris.item = ri.id AND
				ri.inativo = 0 AND
				ri.status IN (".STATUS_ATRIBUIDO.",".STATUS_EM_ANDAMENTO.", ".STATUS_AGUARDANDO_REQUISITANTE.", ".STATUS_AGUARDANDO_APROVACAO_DIRETORIA.")
		"; //perfil 3 = servidor  - status 6 = concluido
		$res = dboQuery($sql);
		if(dboAffectedRows())
		{
			while($lin = dboFetchObject($res))
			{
				$ids[$lin->pessoa] = $lin->pessoa;
			}
		}
		return $ids;
	}

	function getIdsAtribuidos()
	{
		$ids = array();
		$sql = "
			SELECT 
				pp.pessoa AS pessoa
			FROM 
				pessoa_perfil pp,
				requisicao_item_servidor ris,
				requisicao_item ri
			WHERE 
				(
					pp.perfil = ".ID_PERFIL_SERVIDOR." OR
					pp.perfil = ".ID_PERFIL_PRESTADOR_SERVICO."
				) AND
				pp.pessoa = ris.servidor AND
				ris.item = ri.id AND
				ri.inativo = 0 AND
				ri.status IN (".STATUS_ATRIBUIDO.",".STATUS_EM_ANDAMENTO.", ".STATUS_AGUARDANDO_REQUISITANTE.", ".STATUS_AGUARDANDO_APROVACAO_DIRETORIA.")
		"; //perfil 3 = servidor  - status 6 = concluido
		$res = dboQuery($sql);
		if(dboAffectedRows())
		{
			while($lin = dboFetchObject($res))
			{
				$ids[$lin->pessoa] = $lin->pessoa;
			}
		}
		return $ids;
	}

	function getIdsPrestadoresAtribuidos()
	{
		$ids = array();
		$sql = "
			SELECT 
				pp.pessoa AS pessoa
			FROM 
				pessoa_perfil pp,
				requisicao_item_servidor ris,
				requisicao_item ri
			WHERE 
				pp.perfil = ".ID_PERFIL_PRESTADOR_SERVICO." AND
				pp.pessoa = ris.servidor AND
				ris.item = ri.id AND
				ri.inativo = 0 AND
				ri.status IN (".STATUS_ATRIBUIDO.",".STATUS_EM_ANDAMENTO.", ".STATUS_AGUARDANDO_REQUISITANTE.", ".STATUS_AGUARDANDO_APROVACAO_DIRETORIA.")
		"; //perfil 3 = servidor  - status 6 = concluido
		$res = dboQuery($sql);
		if(dboAffectedRows())
		{
			while($lin = dboFetchObject($res))
			{
				$ids[$lin->pessoa] = $lin->pessoa;
			}
		}
		return $ids;
	}

	function getIdsServidoresNaoAtribuidos()
	{
		$ids = array();
		$atribuidos = getIdsServidoresAtribuidos();
		$sql = "
			SELECT 
				pessoa 
			FROM 
				pessoa_perfil 
			WHERE 
				( perfil = ".ID_PERFIL_SERVIDOR." OR perfil = ".ID_PERFIL_ESTAGIARIO." ) ".((sizeof($atribuidos))?("AND pessoa NOT IN (".implode(',', $atribuidos).")"):(''))."
			"; //perfil 3 = servidor  - status 6 = concluido
		$res = dboQuery($sql);
		if(dboAffectedRows())
		{
			while($lin = dboFetchObject($res))
			{
				$ids[$lin->pessoa] = $lin->pessoa;
			}
			return $ids;
		}
	}

	function getIdsPrestadoresNaoAtribuidos()
	{
		$ids = array();
		$atribuidos = getIdsPrestadoresAtribuidos();
		$sql = "
			SELECT 
				pessoa 
			FROM 
				pessoa_perfil 
			WHERE 
				perfil = ".ID_PERFIL_PRESTADOR_SERVICO." ".((sizeof($atribuidos))?("AND pessoa NOT IN (".implode(',', $atribuidos).")"):(''))."
			"; //perfil 3 = servidor  - status 6 = concluido
		$res = dboQuery($sql);
		if(dboAffectedRows())
		{
			while($lin = dboFetchObject($res))
			{
				$ids[$lin->pessoa] = $lin->pessoa;
			}
			return $ids;
		}
	}

	function criticalError($msg)
	{
		echo "<h1>".$msg."</h1>";
		exit();
	}

	function disparaEmail($slug, $params = array())
	{
		global $_conf;
		global $global_ids_servidores;

		extract($params);

		$m = new siteMailer($slug);
		$to = array();

		//pegando e-mails dos administradores, se houver a palavra administrador na slug.
		if(strstr($slug, 'administrador'))
		{
			$admins = getUsersPerfil('Administrador');
			$emails_admins = array();
			foreach($admins as $key => $value)
			{
				//verifica se a pessoa quer receber emails administrativos
				if(pessoaHasPermission($value, 'receber-emails-administrativos'))
				{
					$pes = new pessoa($value);
					$emails_admins[$pes->email] = $pes->email;
				}
			}
		}

		/* instancia uma requisição, se houver */
		if($id_requisicao)
		{
			$req = new requisicao($id_requisicao);
			$serv = new requisicao_item("WHERE requisicao = '".$req->id."' ORDER BY numero");
		}

		/* instanciando servico, se houver */
		if($id_servico)
		{
			$serv = new requisicao_item($id_servico);
			$loc = new local($serv->local);
			$req = new requisicao($serv->requisicao);
			$hist = new historico("WHERE requisicao_item = '".$serv->id."' ORDER BY data DESC LIMIT 1");
		}

		/* instanciando avaliacao, se houver */
		if($id_avaliacao)
		{
			$aval = new avaliacao($id_avaliacao);
			$serv = new requisicao_item($aval->requisicao_item);
			$loc = new local($serv->local);
			$req = new requisicao($serv->requisicao);
			$hist = new historico("WHERE requisicao_item = '".$serv->id."' ORDER BY data DESC LIMIT 1");
		}

		/* instancia o servidor, se houver */
		if($id_servidor)
		{
			$pes = new servidor($id_servidor);
			$m->nome_servidor = $pes->getShortName();
		}

		if($slug == 'administrador-nova-requisicao')
		{
			//pegando o email dos servidores que tem a permissão "gerenciar-servicos"
			foreach($global_ids_servidores as $key => $value)
			{
				//verifica se a pessoa quer receber emails administrativos
				if(pessoaHasPermission($value, 'gerenciar-servicos'))
				{
					$pes = new pessoa($value);
					$emails_gerentes[$pes->email] = $pes->email;
				}
			}

			$emails_gerentes = array_merge($emails_gerentes, $emails_admins);

			$to = $emails_gerentes;
			//gerando a lista de servicos para o email
			do {
				$loc = new local($serv->local);
				$message .= "
				
					<h3>Serviço nº ".$req->id."/".$serv->numero."</h3>

					<ul>
						<li>Tipo de Serviço: <strong>".$serv->___tipo_servico___nome."</strong></li>
						<li>Local: <strong>".$loc->getSmartLocal()."</strong></li>
						<li>Descrição: <strong>".$serv->descricao."</strong></li>
						<li>Prioridade: <strong>".$serv->getValue('prioridade', $serv->prioridade)."</strong></li>
						<li>Link Administrativo: <strong><a href='".$serv->getPermalinkModal()."'>Clique Aqui</a></strong></li>
						<li>Link de Acompanhamento: <strong><a href='".$serv->getPermalink()."'>Clique Aqui</a></strong></li>
					</ul>

				";
			}while($serv->fetch());
			$m->lista_servicos = $message;
		}
		elseif($slug == 'usuario-servico-aprovado')
		{
			$to[] = $req->email_requisitante;
		}
		elseif($slug == 'usuario-servico-atribuido')
		{
			$to[] = $req->email_requisitante;
		}
		elseif($slug == 'usuario-servico-atualizado')
		{
			$to[] = $req->email_requisitante;
		}
		elseif($slug == 'usuario-servico-aguardando-requisitante')
		{
			$to[] = $req->email_requisitante;
		}
		elseif($slug == 'usuario-servico-concluido')
		{
			$to[] = $req->email_requisitante;
		}
		if($slug == 'administrador-servico-avaliado')
		{
			$to = $emails_admins;
			if($aval->qualidade < 3 || $aval->tempo < 3 || $aval->feedback < 3)
			{
				//alertando caso alguma nota seja menor que 3
				$m->subject = "ALERTA - ".$m->subject;
			}
		}
		if($slug == 'servidor-nova-atribuicao')
		{
			$to[] = $pes->email;
		}

		//alocando dados da seção e outros
		$m->nome_sistema = SYSTEM_NAME;
		$m->genero_secao_minusculo = (($_conf->genero == 'f')?('a'):('o'));
		$m->genero_secao_maiusculo = strtoupper($m->genero_secao_minusculo);
		$m->telefone_secao = $_conf->telefone;
		$m->nome_secao = $_conf->nome_secao;
		$m->nome_curto_secao = $_conf->nome_curto_secao;
		$m->nome_atribuidor = $nome_atribuidor;
		$m->link_sistema = SITE_URL;

		//alocando dados da requisicao
		if($req)
		{
			$m->numero_requisicao = $req->id;
			$m->nome_requisitante = $req->nome_requisitante;
			$m->telefone_requisitante = $req->telefone_requisitante;
			$m->email_requisitante = $req->email_requisitante;
		}

		//alocando dados do servico
		if($serv)
		{
			$m->numero_servico = $serv->numero;
			$m->servico_local = $loc->getSmartLocal();
			$m->servico_tipo = $serv->_tipo_servico->nome;
			$m->servico_descricao = $serv->descricao;
			$m->servico_status = $serv->getValue('status', $serv->status);
			$m->link_acompanhamento_servico = '<a href="'.$serv->getPermalink().'" target="_blank">'.$serv->getPermalink().'</a>';
			$m->link_gerenciar_servico = '<a href="'.$serv->getPermalinkModal().'" target="_blank">'.$serv->getPermalinkModal().'</a>';
		}

		//alocando dados do historico
		if($hist)
		{
			$m->servico_data_ultimo_historico = dboDate("d/m/Y H:i", strtotime($hist->data));
			$m->servico_ultimo_historico = ((strlen(trim($hist->comentario)))?(trim($hist->comentario)):('<span style="color: #999;">(sem comentários)</span>'));
		}

		//alocando dados da avaliacao
		if($aval)
		{
			$m->link_avaliacao_servico = '<a href="'.$aval->getPermalink().'" target="_blank">'.$aval->getPermalink().'</a>';
			$m->avaliacao_servico = $aval->getResumo();
		}

		//mandando o email para todos os destinatarios
		$m->to = $to;
		$m->send();
		//echo $m->preview();

		return true;
	}

	function disparaEmails($params)
	{
		/*
			params:

			contexto            -> avaliacao_realizada : o usuário realizou a avaliacao (avisar administradores) 
			                    -> finalizado_servidor : o servidor finalizou a requisição, e agora o administrador deve concluir.

			id_requisicao       -> id da requisicao
			id_servico          -> id do item da requisicao
			id_avaliacao        -> id da avaliacao realizada pelo servidor
			status              -> mudança para este estatus
		*/

		/* pegando as configurações do sistema */
		$conf = new config(1);

		/* pegando os e-mails dos administradores */
		$admins = getUsersPerfil('Administrador');
		$emails_admins = array();
		foreach($admins as $key => $value)
		{
			$pes = new pessoa($value);
			$emails_admins[] = $pes->email;
		}

		/* transforma os indices dos arrays em variaveis */
		extract($params);

		/* instancia uma requisição, se houver */
		if($id_requisicao)
		{
			$req = new requisicao($id_requisicao);
			$serv = new requisicao_item("WHERE requisicao = '".$req->id."' ORDER BY numero");
		}

		/* instanciando servico, se houver */
		if($id_servico)
		{
			$serv = new requisicao_item($id_servico);
		}

		/* instanciando avaliacao, se houver */
		if($id_avaliacao)
		{
			$aval = new avaliacao($id_avaliacao); 
		}

		/* nova requisição, informar os administradores */
		if(isset($status) && $status == STATUS_NOVO)
		{
			/* pegando e-mails dos administradores */
			$mail_to = $emails_admins;

			$subject = SYSTEM_NAME." - Nova requisição - nro. ".$req->id." - ".$req->nome_requisitante;

			$message = "
				<h1>Nova Requisição</h1>

				<h2 style='text-decoration: underline;'>Informações do Requisitante</h2>

				<ul>
					<li>Nome do requisitante: <strong>".$req->nome_requisitante."</strong></li>
					<li>Telefone do requisitante: <strong>".$req->telefone_requisitante."</strong></li>
					<li>E-mail do requisitante: <strong>".$req->email_requisitante."</strong></li>
				</ul>

				<h2 style='text-decoration: underline;'>Informações dos Serviços Requisitados</h2>
			";

			do {
				$loc = new local($serv->local);
				$message .= "
				
					<h3>Serviço nº ".$req->id."/".$serv->numero."</h3>

					<ul>
						<li>Local: <strong>".$loc->getSmartLocal()."</strong></li>
						<li>Tipo de Serviço: <strong>".$serv->___tipo_servico___nome."</strong></li>
						<li>Descrição: <strong>".$serv->descricao."</strong></li>
						<li>Prioridade: <strong>".$serv->getValue('prioridade', $serv->prioridade)."</strong></li>
						<li>Link de Acompanhamento: <strong><a href='".DBO_URL."/../servico-user-view.php?&id=".$serv->id."&token=".$serv->token."'>Clique Aqui</a></strong></li>
					</ul>

				";
			}while($serv->fetch());

			/* mandando email para todos os destinatarios */
			foreach($mail_to as $key => $to)
			{
				mail($to, $subject, $message, "From: ".$conf->from_name." <".$conf->from_mail.">\r\nMIME-Version: 1.0\r\nContent-type: text/html; charset=UTF-8\r\n", "-r ".$conf->from_mail);
			}
		}

		/* servico foi aprovado */
		if(isset($status) && $status == STATUS_APROVADO)
		{
			$req = new requisicao($serv->requisicao);
			$loc = new local($serv->local);

			$mail_to[] = $req->email_requisitante;

			$subject = $conf->nome_curto_secao." - Serviço aprovado - n ".$serv->requisicao."/".$serv->numero;

			$message = "
				<p>Caro usuário,</p>

				<p>".(($conf->genero == 'f')?('A'):('O'))." ".$conf->nome_secao." aprovou uma solicitação de serviço feita por você. Em breve o serviço será atribuido a um servidor para realização.</p>

				<p>O serviço pedido é o seguinte:</p>

				<ul>
					<li>Local: <strong>".$loc->getSmartLocal()."</strong></li>
					<li>Tipo de Serviço: <strong>".$serv->___tipo_servico___nome."</strong></li>
					<li>Descrição: <strong>".$serv->descricao."</strong></li>
				</ul>

				<p>Para acompanhar o status de andamento e o histórico de eventos relacionados a este serviço, acesse o link a seguir:</p>

				<p><a href='".DBO_URL."/../servico-user-view.php?&id=".$serv->id."&token=".$serv->token."'>".DBO_URL."/../servico-user-view.php?&id=".$serv->id."&token=".$serv->token."</a></p>
			";
			
			$message .= $conf->mail_footer;

			/* mandando email para todos os destinatarios */
			foreach($mail_to as $key => $to)
			{
				mail($to, $subject, $message, "From: ".$conf->from_name." <".$conf->from_mail.">\r\nMIME-Version: 1.0\r\nContent-type: text/html; charset=UTF-8\r\n", "-r ".$conf->from_mail);
			}
		}

		/* servico foi aprovado */
		if(isset($status) && $status == STATUS_ATRIBUIDO)
		{
			$req = new requisicao($serv->requisicao);
			$loc = new local($serv->local);

			$mail_to[] = $req->email_requisitante;

			$subject = $conf->nome_curto_secao." - Serviço aprovado e atribuído - n ".$serv->requisicao."/".$serv->numero;

			$message = "
				<p>Caro usuário,</p>

				<p>".(($conf->genero == 'f')?('A'):('O'))." ".$conf->nome_secao." aprovou uma solicitação de serviço feita por você, e atribuiu a um servidor para realização.</p>

				<p>O serviço pedido é o seguinte:</p>

				<ul>
					<li>Local: <strong>".$loc->getSmartLocal()."</strong></li>
					<li>Tipo de Serviço: <strong>".$serv->___tipo_servico___nome."</strong></li>
					<li>Descrição: <strong>".$serv->descricao."</strong></li>
				</ul>

				<p>Para acompanhar o status de andamento e o histórico de eventos relacionados a este serviço, acesse o link a seguir:</p>

				<p><a href='".DBO_URL."/../servico-user-view.php?&id=".$serv->id."&token=".$serv->token."'>".DBO_URL."/../servico-user-view.php?&id=".$serv->id."&token=".$serv->token."</a></p>
			";
			
			$message .= $conf->mail_footer;

			/* mandando email para todos os destinatarios */
			foreach($mail_to as $key => $to)
			{
				mail($to, $subject, $message, "From: ".$conf->from_name." <".$conf->from_mail.">\r\nMIME-Version: 1.0\r\nContent-type: text/html; charset=UTF-8\r\n", "-r ".$conf->from_mail);
			}
		}

		/* servico aguardando o requisitante */
		if(isset($status) && $status == STATUS_AGUARDANDO_APROVACAO_DIRETORIA)
		{
			$req = new requisicao($serv->requisicao);
			$loc = new local($serv->local);

			/* pegando ultimo historico do tipo "aguardando aprovacao da diretoria" */
			$hist = new historico("WHERE requisicao_item = '".$serv->id."' AND status = '".STATUS_AGUARDANDO_APROVACAO_DIRETORIA."' ORDER BY data DESC LIMIT 1");

			$mail_to[] = $req->email_requisitante;

			$subject = $conf->nome_curto_secao." - Serviço aguardando - n ".$serv->requisicao."/".$serv->numero;

			$message = "
				<p>Caro usuário,</p>

				<p>O serviço a seguir está aguardando para continuar a execução:</p>

				<ul>
					<li>Local: <strong>".$loc->getSmartLocal()."</strong></li>
					<li>Tipo de Serviço: <strong>".$serv->___tipo_servico___nome."</strong></li>
					<li>Descrição: <strong>".$serv->descricao."</strong></li>
				</ul>

				".((strlen(trim($hist->comentario)))?('<p>O motivo da espera é o seguinte:</p><p style="display: block; padding: .5em 1em; background: #eee; border: 1px solid #ddd;"><strong>'.date("d/m/Y H:i", strtotime($hist->data)).' - '.$hist->comentario.'</strong></p>'):(''))."

				<p>Para acompanhar o status de andamento e o histórico de eventos relacionados a este serviço, acesse o link a seguir:</p>

				<p><a href='".DBO_URL."/../servico-user-view.php?&id=".$serv->id."&token=".$serv->token."'>".DBO_URL."/../servico-user-view.php?&id=".$serv->id."&token=".$serv->token."</a></p>
			";

			$message .= $conf->mail_footer;

			/* mandando email para todos os destinatarios */
			foreach($mail_to as $key => $to)
			{
				mail($to, $subject, $message, "From: ".$conf->from_name." <".$conf->from_mail.">\r\nMIME-Version: 1.0\r\nContent-type: text/html; charset=UTF-8\r\n", "-r ".$conf->from_mail);
			}
		}

		/* servico aguardando o requisitante */
		if(isset($status) && $status == STATUS_AGUARDANDO_REQUISITANTE)
		{
			$req = new requisicao($serv->requisicao);
			$loc = new local($serv->local);

			/* pegando ultimo historico do tipo "aguardando requisitante" */
			$hist = new historico("WHERE requisicao_item = '".$serv->id."' AND status = '".STATUS_AGUARDANDO_REQUISITANTE."' ORDER BY data DESC LIMIT 1");

			$mail_to[] = $req->email_requisitante;

			$subject = "IMPORTANTE - Serviço aguardando seu contato - n ".$serv->requisicao."/".$serv->numero;

			$message = "
				<p>Caro usuário,</p>

				<p>".(($conf->genero == 'f')?('A'):('O'))." ".$conf->nome_secao." espera o <strong style='color: red; text-decoration: underline;'>seu retorno</strong> sobre uma solicitação de serviço.</p>

				<p>O serviço pedido é o seguinte:</p>

				<ul>
					<li>Local: <strong>".$loc->getSmartLocal()."</strong></li>
					<li>Tipo de Serviço: <strong>".$serv->___tipo_servico___nome."</strong></li>
					<li>Descrição: <strong>".$serv->descricao."</strong></li>
				</ul>
				
				".((strlen(trim($hist->comentario)))?('<p>Estamos aguardando seu contato pelo seguinte motivo, segundo informações fornecidas pelo servidor responsável pela execução:</p><p style="display: block; padding: .5em 1em; background: #eee; border: 1px solid #ddd;"><strong>'.date("d/m/Y H:i", strtotime($hist->data)).' - '.$hist->comentario.'</strong></p>'):(''))."

				<p><strong style='color: red;'><u>Importante:</u></strong><br /><br />Para que o serviço tenha prosseguimento, você deve, <u><strong>obrigatoriamente</strong></u>, entrar em contato com ".(($conf->genero == 'f')?('a'):('o'))." ".$conf->nome_secao.", informando <u><strong>data e horário</strong></u> para retorno do servidor responsável.</p>

				<p>".(($conf->genero == 'f')?('A'):('O'))." ".$conf->nome_secao." aguarda seu retorno.</p>

				<p>Para acompanhar o status de andamento e o histórico de eventos relacionados a este serviço, acesse o link a seguir:</p>

				<p><a href='".DBO_URL."/../servico-user-view.php?&id=".$serv->id."&token=".$serv->token."'>".DBO_URL."/../servico-user-view.php?&id=".$serv->id."&token=".$serv->token."</a></p>
			";
			
			$message .= $conf->mail_footer;

			/* mandando email para todos os destinatarios */
			foreach($mail_to as $key => $to)
			{
				mail($to, $subject, $message, "From: ".$conf->from_name." <".$conf->from_mail.">\r\nMIME-Version: 1.0\r\nContent-type: text/html; charset=UTF-8\r\n", "-r ".$conf->from_mail);
			}
		}

		/* requisicao foi concluida, informar o requisitante para realizar a avaliacao */		
		if(isset($status) && $status == STATUS_CONCLUIDO)
		{
			$req = new requisicao($serv->requisicao);
			$aval = new avaliacao("WHERE requisicao_item = '".$serv->id."'");

			$mail_to[] = $req->email_requisitante;

			$subject = $conf->nome_curto_secao." - Serviço concluído - n ".$serv->requisicao."/".$serv->numero;

			$message = "
				<p>Caro usuário,</p>

				<p>".(($conf->genero == 'f')?('A'):('O'))." ".$conf->nome_secao." acaba de finalizar um serviço aberto por você. <strong>Você precisa realizar a avaliação</strong> do serviço.</p>

				<p>Para realizá-la, acesse o link a seguir. São necessários <strong>apenas alguns clicks</strong>.</p>

				<p><a href='".DBO_URL."/../servico-user-view.php?&id=".$serv->id."&token=".$serv->token."&token_avaliacao=".$aval->token."'>".DBO_URL."/../servico-user-view.php?&id=".$serv->id."&token=".$serv->token."&token_avaliacao=".$aval->token."</a></p>
			";
			
			$message .= $conf->mail_footer;

			/* mandando email para todos os destinatarios */
			foreach($mail_to as $key => $to)
			{
				mail($to, $subject, $message, "From: ".$conf->from_name." <".$conf->from_mail.">\r\nMIME-Version: 1.0\r\nContent-type: text/html; charset=UTF-8\r\n", "-r ".$conf->from_mail);
			}
		}

		/* o servidor fez uma avaliação de serviço, informar os administradores */
		if(isset($contexto) && $contexto == 'avaliacao_realizada')
		{
			$serv = new requisicao_item($aval->requisicao_item);

			/* pegando e-mails dos administradores */
			$mail_to = $emails_admins;

			$subject = SYSTEM_NAME." - Serviço avaliado - n ".$serv->requisicao."/".$serv->numero;

			$message = "
				<h1>Nova Avaliação</h1>

				<p>O serviço número ".$serv->requisicao."/".$serv->numero." foi avaliado pelo usuário.</p>

				<p>Para visualizar a avaliação, acesse o seguinte link:</p>

				<p><a href='".DBO_URL."/../servico-user-view.php?&id=".$serv->id."&token=".$serv->token."&token_avaliacao=".$aval->token."'>".DBO_URL."/../servico-user-view.php?&id=".$serv->id."&token=".$serv->token."&token_avaliacao=".$aval->token."</a></p>

			";

			/* mandando email para todos os destinatarios */
			foreach($mail_to as $key => $to)
			{
				mail($to, $subject, $message, "From: ".$conf->from_name." <".$conf->from_mail.">\r\nMIME-Version: 1.0\r\nContent-type: text/html; charset=UTF-8\r\n", "-r ".$conf->from_mail);
			}
		}

		return true;
	}

	/* ------------------------------------------------------------------------------------------------------------------ */
	/* ------------------------------------------------------------------------------------------------------------------ */
	/* ---- Pacote de Funções de Data ----------------------------------------------------------------------------------- */
	/* ------------------------------------------------------------------------------------------------------------------ */
	/* ------------------------------------------------------------------------------------------------------------------ */

	// Find the Number of Days in a Month
	// Month is between 1 and 12
	function numeroDeDiasNoMes($year, $month)
	{
		$days_in_the_month = array (31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		if ($month != 2) return $days_in_the_month[$month - 1];
		// or Check for Leap Year (February)
		return (checkdate($month, 29, $year)) ? 29 : 28;
	}

	function validaData($date)
	{
		// Split the date into its components.
		list($year, $month, $day) = explode("-", $date);
		// 4 Digit Year Check
		if ($year < 1900 || $year > 2050) { return false; }
		// Day Check
		$days_in_month = numeroDeDiasNoMes($year, $month);
		if ($day+0 > $days_in_month+0) { return false; }
		// otherwise...
		return true;
	}

	// Month is between 1 and 12
	function beginning_diaDaSemanaDoAno($year, $month)
	{
		// Return Values: (0=Sunday, 1=Monday,...,6=Saturday)
		// mktime function expects month as 1-12
		return date("w", mktime(1, 0, 0, $month, 1, $year));
	}

	function diaDaSemanaDoAno($date)
	{
		// Split the date into its components.
		list($year, $month, $day) = explode("-", $date);
		// Return Values: (0=Sunday, 1=Monday,...,6=Saturday)
		return date("w", mktime(1, 0, 0, $month, $day, $year));
	}

	function weekday_occurrence($date)
	{
		// Split the date into its components.
		list($year, $month, $day) = explode("-", $date);
		return floor(($day-1)/7)+1;
	}

	// $xst - 1=1st, 2=2nd, 3=3rd, 4=4th, etc.
	// $weekday - weekday value (0=Sunday, 1=Monday,...,6=Saturday)
	// Month is between 1 and 12
	function xst_diaDaSemanaDoAno($xst, $weekday, $year, $month)
	{
		$days_in_month = numeroDeDiasNoMes($year, $month);
		$beginning_weekday = beginning_diaDaSemanaDoAno($year, $month);
		
		// Find 1st occurence of the specified weekday.
		$weekday_difference = $weekday - $beginning_weekday;
		// Add 7 if the weekday value is less than the starting weekday value.
		if ($weekday_difference < 0) { $weekday_difference += 7; }
		
		// Add the number of days, to the 1st, required to move to the specified day.
		$first_date = date('Y-m-d', mktime(1, 0, 0, $month, 1 + $weekday_difference, $year));
		// Add 7 for each week in the month (1=1st, 2=2nd, etc).
		$date = somaDataAMD($first_date,0,0,($xst-1)*7);
		
		// Split the date into its components.
		list($new_year, $new_month, $new_day) = explode("-", $date);
		// If the date is beyond the current month then set $date equal to nothing.
		if ($new_month != $month) { $date = ''; }
		return $date;
	}

	// The date of the Sunday before the specified date.
	// Returns the date in 'Y-m-d' format.
	function sunday_before_date($date)
	{
		// Split the date into its components.
		list($year, $month, $day) = explode("-", $date);
		// Find the current day of the week as a single digit.
		// Range from "0" (Sunday) to "6" (Saturday)
		$day_of_the_week = date("w", mktime(1, 0, 0, $month, $day, $year));
		// Subtract the day of the week for Sunday from the specified
		// day and reformat into YYYY-MM-DD format.
		return date('Y-m-d', mktime(1, 0, 0, $month, $day - $day_of_the_week, $year));
	}

	// The date of the Monday before the specified date.
	// Returns the date in 'Y-m-d' format.
	function monday_before_date($date)
	{
		// Split the date into its components.
		list($year, $month, $day) = explode("-", $date);
		// Find the current day of the week as a single digit.
		// Range from "0" (Sunday) to "6" (Saturday)
		$day_of_the_week = date("w", mktime(1, 0, 0, $month, $day, $year));
		// If Sunday, subtract 6 days to get to Monday.
		if ($day_of_the_week == 0) {
			return date('Y-m-d', mktime(1, 0, 0, $month, $day - 6, $year));
			// Else If Monday, return that day.
		} elseif ($day_of_the_week == 1) {
			return date('Y-m-d', mktime(1, 0, 0, $month, $day, $year));
			// Else, subtract the day of the week to get to Sunday
			// and then add one to get to Monday.
		} else {
			return date('Y-m-d', mktime(1, 0, 0, $month, $day - $day_of_the_week + 1, $year));
		}
	}

	function somaDataAMD($date, $delta_years = 0, $delta_months = 0, $delta_days = 0)
	{
		// delta_years adjustment:
		// Use this to adjust for next and previous years.
		// Add the $delta_years to the current year and make the new date.
		
		if ($delta_years != 0) {
			// Split the date into its components.
			list($year, $month, $day) = explode("-", $date);
			// Careful to check for leap year effects!
			if ($month == 2 && $day == 29) {
				// Check the number of days in the month/year, with the day set to 1.
				$tmp_date = date("Y-m", mktime(1, 0, 0, $month, 1, $year + $delta_years));
				list($new_year, $new_month) = explode("-", $tmp_date);
				$days_in_month = numeroDeDiasNoMes($new_year, $new_month);
				// Lower the day value if it exceeds the number of days in the new month/year.
				if ($days_in_month < $day) { $day = $days_in_month; }
				$date = $new_year . '-' . $month . '-' . $day;
			} else {
				$new_year = $year + $delta_years;
				$date = sprintf("%04d-%02d-%02d", $new_year, $month, $day);
			}
		}
		
		// delta_months adjustment:
		// Use this to adjust for next and previous months.
		// Note: This DOES NOT subtract 30 days! 
		// Use $delta_days for that type of calculation.
		// Add the $delta_months to the current month and make the new date.
		
		if ($delta_months != 0) {
		// Split the date into its components.
		list($year, $month, $day) = explode("-", $date);
		// Calculate New Month and Year
		$new_year = $year;
		$new_month = $month + $delta_months;
		if ($delta_months < -840 || $delta_months > 840) { $new_month = $month; } // Bad Delta
		if ($delta_months > 0) { // Adding Months
			while ($new_month > 12) { // Adjust so $new_month is between 1 and 12.
				$new_year++;
				$new_month -= 12;
			}
		} elseif ($delta_months < 0) { // Subtracting Months
			while ($new_month < 1) { // Adjust so $new_month is between 1 and 12.
				$new_year--;
				$new_month += 12;
			}
		}
		// Careful to check for number of days in the new month!
		$days_in_month = numeroDeDiasNoMes($new_year, $new_month);
		// Lower the day value if it exceeds the number of days in the new month/year.
		if ($days_in_month < $day) { $day = $days_in_month; }
			$date = sprintf("%04d-%02d-%02d", $new_year, $new_month, $day);
		}
		
		// delta_days adjustment:
		// Use this to adjust for next and previous days.
		// Add the $delta_days to the current day and make the new date.
		
		if ($delta_days != 0) {
			// Split the date into its components.
			list($year, $month, $day) = explode("-", $date);
			// Create New Date
			$date = date("Y-m-d", mktime(1, 0, 0, $month, $day, $year) + $delta_days*24*60*60);
		}
		
		// Check Valid Date, Use for TroubleShooting
		//list($year, $month, $day) = explode("-", $date);
		//$valid = checkdate($month, $day, $year);
		//if (!$valid)	return "Error, function somaDataAMD: Could not process valid date!";
		
		return $date;
	}

	// Returns week number for the specified date, 
	// depending on the week numbering setting(s).
	function week_number($year, $month, $day)
	{
	// Make Adjustment if Week Starts on Weekday Sunday.
	// ISO Weeks Start on Monday. We will consider the 
	// Sunday before as part of the following ISO week.
		if (WEEK_START == 0) { $day++; } // Add one to get to Monday.
		
		$timestamp = mktime(1, 0, 0, $month, $day, $year);
		$week = "";
		$week = strftime("%V", $timestamp); // ISO Weeks start on Mondays
		if ($week == "") {
			// %V not implemented on older versions of PHP and on Win32 machines.
			$week = ISOWeek($year, $month, $day);
		}
		
		return $week + 0;
	}

	function ISOWeek($y, $m, $d) 
	{
		$week = strftime("%W", mktime(0, 0, 0, $m, $d, $y));
		$dow0101 = getdate(mktime(0, 0, 0, 1, 1, $y));
		$next0101 = getdate(mktime(0, 0, 0, 1, 1, $y+1));
		
		if ($dow0101["wday"] > 1 && $dow0101["wday"] < 5) { $week++; }
		if ($next0101["wday"] > 1 && $next0101["wday"] < 5 && $week == 53) { $week = 1; }
		if ($week == 0) { $week = ISOWeek($y-1,12,31); }
		
		return substr("00" . $week, -2); 
	} 

	// Return the full month name
	// Month is between 1 and 12
	function month_name($month)
	{
		switch($month) {
			case 0: return "Mês deve ser entre 1-12!";
			case 1: return "Janeiro";
			case 2: return "Fevereiro";
			case 3: return "Março";
			case 4: return "Abril";
			case 5: return "Maio";
			case 6: return "Junho";
			case 7: return "Julho";
			case 8: return "Agosto";
			case 9: return "Setembro";
			case 10: return "Outubro";
			case 11: return "Novembro";
			case 12: return "Dezembro";
		}
		return "unknown-month($m)";
	}

	// Return the abbreviated month name
	// Month is between 1 and 12
	function month_short_name($month)
	{
		switch($month) {
			case 0: return "Mês deve ser entre 1-12!";
			case 1: return "Jan";
			case 2: return "Fev";
			case 3: return "Mar";
			case 4: return "Abr";
			case 5: return "Mai";
			case 6: return "Jun";
			case 7: return "Jul";
			case 8: return "Ago";
			case 9: return "Set";
			case 10: return "Out";
			case 11: return "Nov";
			case 12: return "Dez";
		}
		return "unknown-month($m)";
	}

	// Return the full weekday name
	// $weekday_value - weekday (0=Sunday,...,6=Saturday)
	function weekday_name($weekday_value)
	{
		switch($weekday_value) {
			case 0: return "Domingo";
			case 1: return "Segunda-feira";
			case 2: return "Terça-feira";
			case 3: return "Quarta-feira";
			case 4: return "Quinta-feira";
			case 5: return "Sexta-feira";
			case 6: return "Sábado";
		}
		return "unknown-weekday($w)";
	}

	// Return the abbreviated weekday name
	// $weekday_value - weekday (0=Sunday,...,6=Saturday)
	function weekday_short_name($weekday_value)
	{
		switch($weekday_value) {
			case 0: return "Dom";
			case 1: return "Seg";
			case 2: return "Ter";
			case 3: return "Qua";
			case 4: return "Qui";
			case 5: return "Sex";
			case 6: return "Sab";
		}
		return "unknown-weekday($w)";
	}

	// Return the occurence name
	// $occurence, 0-31
	function occurence_name($occurence)
	{
		if ($occurence < 0) { return "occurence must be great than zero"; }
		switch($occurence) {
			case 0: return "None";
			case 1: return "1st";
			case 2: return "2nd";
			case 3: return "3rd";
			case 21: return "21st";
			case 22: return "22nd";
			case 23: return "23rd";
			case 31: return "31st";
		}
		return $occurence."th";
	}

	// Return the full weekday index array used to 
	// determine what day of the week to start with
	// to display the month view and month nav view.
	// $w - weekday (0=Sunday, 1=Monday)
	function weekday_index_array ($w)
	{
		switch($w) {
			case 0: return array (0,1,2,3,4,5,6); // Start with Sunday
			case 1: return array (1,2,3,4,5,6,0); // Start with Monday
		}
		return array (0,1,2,3,4,5,6); // Default - Start with Sunday
	}

	// Param: $date format, 'YYYY-MM-DD'
	// Returns formatted date string.
	function dataPorExtenso($date)
	{
		list($year, $month, $day) = explode("-",$date);
		return sprintf("%d",$day)." de ".month_name($month)." de ".$year;
	}

	// Param: $date format, 'YYYY-MM-DD'
	// Returns formatted date string.
	function short_date_format($date)
	{
		list($year, $month, $day) = explode("-",$date);
		return month_short_name($month) . ' ' . sprintf("%d",$day) . ', ' . $year;
	}

	// Param: $time, 'hh:mm' format
	function format_time_to_ampm($time, $add_leading_zeros = false)
	{
		list ($hour, $min) = explode(":", $time);
		// To Cater for the AM PM Hour display
		if (DEFINE_AM_PM) {
			if ($hour > 12 ) { $hour = $hour - 12; $ampm = "PM"; 
			} elseif ($hour == 12) { $ampm="PM"; } else { $ampm="AM"; }
		}
		if ($add_leading_zeros) {
			$time = sprintf("%02d:%02d", $hour, $min) . $ampm;
		} else {
			$time = sprintf("%d:%02d", $hour, $min) . $ampm;
		}
		return $time;
	}

	// Calculate the number of days an event spans.
	// This function assumes that the dates do exist!
	// $start_date - YYYY-MM-DD
	// $end_date - YYYY-MM-DD
	function diasPassados($start_date, $end_date)
	{
		list($year, $month, $day) = explode("-", $start_date);
		$start_time_stamp = @mktime(1, 0, 0, $month, $day, $year);
		list($year, $month, $day) = explode("-", $end_date);
		$end_time_stamp = @mktime(1, 0, 0, $month, $day, $year);
		return round(($end_time_stamp - $start_time_stamp)/(24*60*60));
	}

	/* ------------------------------------------------------------------------------------------------------------------ */
	/* ------------------------------------------------------------------------------------------------------------------ */
	/* -- / Pacote de Funções de Data ----------------------------------------------------------------------------------- */
	/* ------------------------------------------------------------------------------------------------------------------ */
	/* ------------------------------------------------------------------------------------------------------------------ */

	function makeShortRequisicaoItemLink($lin)
	{
		return "<a target='_blank' title='".$lin->nome_requisitante.": \n".trim($lin->descricao)."' href='".DBO_URL."/../servico-user-view.php?&id=".$lin->requisicao_item."&token=".$lin->requisicao_item_token."'>".$lin->requisicao."/".$lin->numero."</a>";
	}

	function getPercent($valor, $total, $formato = '')
	{
		$percent = $valor/$total*100;

		if($formato == '')
		{
			$retorno = number_format($percent, 0, '', '');
			return (($retorno > 0)?($retorno."%"):(''));
		}
	}

	/* override de campos nos formularios */
	function field_equipamento_manutencao_equipamento_manutencao_periodica($tipo, $obj)
	{
		if($tipo == "insert" || $tipo == "update")
		{
			/* pegando somente as manutenções programadas dos tipos de equipamento do equipamento ativo */

			$id_equipamento = (($tipo == 'insert')?($obj->__fixos[equipamento]):($obj->equipamento));

			$sql = "
				SELECT
					eq_man_per.*
				FROM
					equipamento_manutencao_periodica eq_man_per,
					equipamento eq
				WHERE
					eq.id = '".$id_equipamento."' AND
					eq_man_per.tipo_equipamento = eq.tipo_equipamento
				ORDER BY nome;
			";
			$manut = new equipamento_manutencao_periodica();
			$manut->query($sql);
			
			ob_start();
			?>
			<select name="equipamento_manutencao_periodica" class="required" data-name="Selecione a Manutenção Periódica">
				<option value="-1"></option>
				<?
					if($manut->size())
					{
						do {
							?><option value="<?= $manut->id ?>" <?= (($manut->id == $obj->equipamento_manutencao_periodica)?("selected"):("")) ?>><?= $manut->getSmartNome(); ?></option><?
						}while($manut->fetch());
					}
				?>
			</select>
			<script>
				$(document).ready(function(){
					var target = $('select[name="equipamento_manutencao_periodica"]').closest('.item');
					var trigger = $('input[name="eh_periodica"]:checked');
					if(trigger.val() == 0 || typeof trigger.val() == 'undefined'){
						target.hide();
					}
					$(document).on('click', 'input[name="eh_periodica"]', function(){
						if($(this).val() == 1){
							target.fadeIn();
						} else {
							target.fadeOut().find('select').val('-1');
						}
					});
				}) //doc.ready
			</script>
			<?
			$retorno = ob_get_clean();
		}
		return $retorno;
	}

	function validation_equipamento_manutencao($tipo, $obj)
	{
		ob_start();
		?>
		<script>
			function validation_equipamento_manutencao(form) {
				return true;
			}
		</script>
		<?
		$ob_result = ob_get_clean();
		return $ob_result;
	}

	function criaAgendamentosIniciais($obj)
	{
		if(is_array($_POST['aux_data_agendada']))
		{
			foreach($_POST['aux_data_agendada'] as $key => $value)
			{
				if(strlen(trim($value)))
				{
					$ag = new equipamento_manutencao();
					$ag->equipamento = $obj->id;
					$ag->eh_periodica = 1;
					$ag->equipamento_manutencao_periodica = $key;
					$ag->data_agendada = dataSQL(dboescape($_POST['aux_data_agendada'][$key]));
					$ag->observacao = dboescape($_POST['aux_observacao'][$key]);
					$ag->save();
				}
			}		
		}
		return true;
	}

	class calendar
	{
		function drawCalendar($month,$year,$params) {

			global $dbo;

			// Create array containing abbreviations of days of week.
			$daysOfWeek = array('Dom','Seg','Ter','Qua','Qui','Sex','Sab');

			// What is the first day of the month in question?
			$firstDayOfMonth = mktime(0,0,0,$month,1,$year);

			// How many days does this month contain?
			$numberDays = date('t',$firstDayOfMonth);

			// Retrieve some information about the first day of the
			// month in question.
			$dateComponents = getdate($firstDayOfMonth);

			// What is the name of the month in question?
			$monthName = $dateComponents['month'];

			// What is the index value (0-6) of the first day of the
			// month in question.
			$dayOfWeek = $dateComponents['wday'];

			// Create the table tag opener and day headers

			$calendar = "<div class='wrapper-calendar'>";

			ob_start();
			?>
			<div class="row">
				<div class="large-6 columns text-left center-for-small">
					<ul class="button-group radius">
						<li><a href="" data-dropdown="drop-mes" class="button dropdown small secondary"><?= month_name($month) ?></a></li>				
						<li><a href="" data-dropdown="drop-ano" class="button dropdown small secondary"><?= $year ?></a></li>
					</ul>
					<ul id="drop-mes" class="f-dropdown" data-dropdown-content>
					<?
						for($mes = 1; $mes <= 12; $mes++)
						{
							$mes = str_pad($mes, 2, '0', STR_PAD_LEFT);
							?>
							<li><a class="trigger-calendar-nav" href="<?= $dbo->keepUrl('calendar_date='.$year."-".$mes."-01") ?>" data-month='<?= $mes ?>'><?= month_name($mes) ?></a></li>
							<?
						}			
					?>
					</ul>
					<ul id="drop-ano" class="f-dropdown" data-dropdown-content>
					<?
						$min = date('Y')-2;
						$max = date('Y')+2;
						for($ano = $min; $ano <= $max; $ano++)
						{
							?>
							<li><a class="trigger-calendar-nav" href="<?= $dbo->keepUrl('calendar_date='.$ano."-".$month."-01") ?>" data-year="<?= $ano ?>"><?= $ano ?></a></li>
							<?
						}
					?>
					</ul>
				</div><!-- col -->
				<div class="large-6 columns text-right center-for-small">
					<ul class="button-group radius right">
						<li><a href="<?= $dbo->keepUrl('calendar_date='.somaDataAMD($year."-".$month."-01", 0, -1, 0)) ?>" class="button small secondary trigger-calendar-nav">&#9664;</a></li>
						<li><a href="<?= $dbo->keepUrl('calendar_date='.date('Y-m-d')) ?>" class="button small secondary trigger-calendar-nav">Hoje</a></li>
						<li><a href="<?= $dbo->keepUrl('calendar_date='.somaDataAMD($year."-".$month."-01", 0, 1, 0)) ?>" class="button small secondary trigger-calendar-nav">&#9654;</a></li>
					</ul>
				</div><!-- col -->
			</div><!-- row -->
			<?
			$calendar .= ob_get_clean();

			$calendar .= "<table class='calendar' id='calendar' data-active-url=\"".$dbo->keepUrl()."\">";
			$calendar .= "<thead><tr>";

			// Create the calendar headers

			foreach($daysOfWeek as $day) {
				$calendar .= "<th class='header'>$day</th>";
			} 

			// Create the rest of the calendar

			// Initiate the day counter, starting with the 1st.

			$currentDay = 1;

			$calendar .= "</tr></thead><tbody><tr>";

			// The variable $dayOfWeek is used to
			// ensure that the calendar
			// display consists of exactly 7 columns.

			if ($dayOfWeek > 0) { 
				$calendar .= "<td colspan='$dayOfWeek'>&nbsp;</td>"; 
			}
			
			$month = str_pad($month, 2, "0", STR_PAD_LEFT);
		  
			while ($currentDay <= $numberDays) {

				// Seventh column (Saturday) reached. Start a new row.

				if ($dayOfWeek == 7) {

					$dayOfWeek = 0;
					$calendar .= "</tr><tr>";

				}
				
				$currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
				
				$date = "$year-$month-$currentDayRel";

				$calendar .= "<td data-day-of-week='".weekday_short_name($dayOfWeek)."' data-day-of-month='".$currentDay."' class='day ".((is_array($_SESSION[sysId()]['active_days']))?(((in_array($date, array_keys($_SESSION[sysId()]['active_days'])))?('active'):(''))):(''))." ".(($date == date('Y-m-d'))?('today'):(''))."' rel='$date' id=\"day-".$date."\"><div data-day-of-week='".weekday_short_name($dayOfWeek)."' data-day-of-month='".$currentDay."'>";
				if(is_array($params->data_set[$date]))
				{
					foreach($params->data_set[$date] as $info)
					{
						$calendar .= $info[function_name]($info[data]);
					}
				}
				$calendar .= "</div></td>";

				// Increment counters
		 
				$currentDay++;
				$dayOfWeek++;

			}
			
			

			// Complete the row of the last week in month, if necessary

			if ($dayOfWeek != 7) { 
			
				$remainingDays = 7 - $dayOfWeek;
				$calendar .= "<td colspan='$remainingDays'>&nbsp;</td>"; 

			}
			
			$calendar .= "</tr>";
			$calendar .= "</tbody>";
			$calendar .= "</table>";
			$calendar .= "</div><!-- wrapper-calendar -->";

			ob_start();
			?>
			<script>
				$(document).ready(function(){
					$(document).on('click', '.trigger-calendar-nav', function(e){
						e.preventDefault();
						//usar peixePost() com o peixelaranja JSFW
						peixeGet(
							$(this).attr('href'),
							function(data) {
								var result = $.parseHTML(data);
								$('.wrapper-calendar').fadeHtml($(result).find('.wrapper-calendar').html(), function(){
								});
							}
						)
						return false;
					});
				}) //doc.ready
			</script>
			<?
			$calendar .= ob_get_clean();
			return $calendar;
		}

		function getNotifications()
		{
			global $_serv;
			global $_manut;
			$this->total_atrasado = $_serv->getTotalAtrasado()+$_manut->getTotalAtrasado();
			$this->total_avisado = $_serv->getTotalAvisado()+$_manut->getTotalAvisado();
		}

		function getNotificationTag($tipo)
		{
			if(!isset($this->total_atrasado))
			{
				$this->getNotifications();
			}
			if($tipo == 'calendar_atrasado' && $this->total_atrasado > 0)
			{
				$mensagem = (($this->total_atrasado > 1)?($this->total_atrasado." serviços precisam ser executados"):($this->total_atrasado." serviço precisa ser executado"));
				return "<span class='notification calendar-atrasado help' title='".$mensagem."'>".$this->total_atrasado."</span>";
			}
			if($tipo == 'calendar_avisado' && $this->total_avisado > 0)
			{
				$mensagem = (($this->total_avisado > 1)?($this->total_avisado." serviços agendados para os próximos ".DIAS_DE_AVISO." dias"):($this->total_avisado." serviço agendado para os próximos ".DIAS_DE_AVISO." dias"));
				return "<span class='notification calendar-avisado help' title='".$mensagem."'>".$this->total_avisado."</span>";
			}
			return '';
		}
	}

	function getStatusAgendamentoServico($obj)
	{
		if(!intval($obj->data_agendada))
		{
			return "nao-agendado";
		}
		$hoje = date("Y-m-d");
		if(diasPassados($hoje, $obj->data_agendada) < 0)
		{
			$status = 'atrasado';
		} 
		if(diasPassados($hoje, $obj->data_agendada) == 0)
		{
			$status = ((ALERTAR_SERVICOS_DO_DIA)?('atrasado'):('avisado'));
		} 
		if(diasPassados($hoje, $obj->data_agendada) > 0)
		{
			$status = 'avisado';
		} 
		if(diasPassados($hoje, $obj->data_agendada) > DIAS_DE_AVISO)
		{
			$status = 'programado';
		}
		if($obj->status == STATUS_CONCLUIDO || $obj->status == STATUS_AVALIADO)
		{
			$status = 'realizado';
		}
		return $status;
	}

	function getItemCalendarServico($obj)
	{
		$status = getStatusAgendamentoServico($obj);
		ob_start();
		?>
		<span title="<?= htmlSpecialChars($obj->___requisicao___nome_requisitante).":\n".htmlSpecialChars($obj->descricao) ?>" data-id-requisicao="<?= $obj->requisicao ?>" data-id-servico="<?= $obj->id ?>" class="tag-status status-<?= $status ?> radius pointer trigger-requisicao"><?= $obj->requisicao."/".$obj->numero ?></span>
		<?
		return ob_get_clean();
	}

	function getItemCalendarManutencao($obj)
	{
		$hoje = date("Y-m-d");
		if(diasPassados($hoje, $obj->data_agendada) <= 0)
		{
			$status = 'atrasado';
		} 
		if(diasPassados($hoje, $obj->data_agendada) > 0)
		{
			$status = 'avisado';
		} 
		if(diasPassados($hoje, $obj->data_agendada) > DIAS_DE_AVISO)
		{
			$status = 'programado';
		}
		if(strlen(trim($obj->data_realizada)))
		{
			$status = 'realizado';
		}
		$equip = new equipamento($obj->equipamento);
		$local = new local($equip->local);
		if($obj->eh_periodica)
		{
			$period = new equipamento_manutencao_periodica($obj->equipamento_manutencao_periodica);
			$nome_period = " - ".$period->getSmartNome();
		}
		$title = strtoupper($obj->tipo_equipamento).$nome_period."\nLocal: ".$local->getSmartLocal().(($equip->local_detalhe)?(" (".$equip->local_detalhe.")"):(''));
		ob_start();
		?>
		<a class="tag-status status-<?= $status ?> radius icon" rel="modal" href='dbo_admin.php?dbo_mod=equipamento_manutencao&dbo_fixo=<?= $obj->encodeFixos('equipamento='.$obj->equipamento) ?>&dbo_update=<?= $obj->id ?>&dbo_modal_no_fixos=1&dbo_return_function=closeCalendarModal' data-height='790px' data-width='900px' title="<?= $title ?>"><img src="<?= DBO_URL ?>/../images/icon-tipo-equipamento-<?= makeSlug($obj->tipo_equipamento) ?>.png"/></a>
		<?
		return ob_get_clean();
	}

	function closeCalendarModal($tipo, $obj)
	{
		?>
		<script>
			parent.reloadList();
			parent.setPeixeMessage("<div class='success'>Sucesso!</div>");
			parent.showPeixeMessage();
			parent.$.fn.colorbox.close();
		</script>
		<?
		exit();
	}

	function form_equipamento_manutencao_prepend($tipo, $obj)
	{
		if($tipo == 'update' && ($_GET['dbo_modal'] || $_GET['dbo_modal_no_fixos']))
		{
			ob_start();
			$equip = new equipamento($obj->equipamento);
			$local = new local($equip->local);
			?>
			<div class="row">
				<div class="large-12 columns">
					<div class="panel radius">
						<h5>Equipamento</h5>
						<div class="row">
							<div class="large-3 columns"><p><?= $equip->___tipo_equipamento___nome; ?></p></div><!-- col -->
							<div class="large-9 columns"><p><strong>Local:</strong> <?= $local->getSmartLocal(); ?> <?= ((strlen(trim($equip->local_detalhe)))?("(".$equip->local_detalhe.")"):('')) ?></p></div><!-- col -->
						</div><!-- row -->
						<div class="row">
							<div class="large-3 small-6 columns">
								<?
									$marca = $equip->___equipamento_marca___nome;
								?>
								<p class="large-no-margin">
									<strong>Marca</strong>
									<?= ((strlen(trim($marca)))?("<br />".$marca):('<br />-')) ?>
								</p>
							</div><!-- col -->
							<div class="large-3 small-6 columns">
								<p class="large-no-margin">
									<strong>Modelo</strong>
									<?= ((strlen(trim($equip->modelo)))?("<br />".$equip->modelo):('<br />-')) ?>
								</p>
							</div><!-- col -->
							<div class="large-3 small-6 columns">
								<p class="no-margin">
									<strong>Patrimônio / AI</strong>
									<?= ((strlen(trim($equip->patrimonio)))?("<br />".$equip->patrimonio):(((strlen(trim($equip->ai)))?("<br />".$equip->ai):('<br />-')))) ?>
								</p>
							</div><!-- col -->
							<div class="large-3 small-6 columns">
								<p class="no-margin">
									<strong>Número de Série</strong>
									<?= ((strlen(trim($equip->numero_serie)))?("<br />".$equip->numero_serie):('<br />-')) ?>
								</p>
							</div><!-- col -->
						</div><!-- row -->
						
					</div>
					<h4>Dados da Manutenção</h4>
				</div><!-- col -->
			</div><!-- row -->
			<?
			return ob_get_clean();
		}
	}

	function form_equipamento_manutencao_append($tipo, $obj)
	{
		if($tipo == 'update')
		{
			ob_start();
			if($obj->eh_periodica)
			{
				$manut = new equipamento_manutencao_periodica($obj->equipamento_manutencao_periodica);
				$postfix = $manut->valor." ".$manut->getNomePeríodo($manut->periodo, $manut->valor);
				$Y = (($manut->periodo == 'Y')?($manut->valor):(0));
				$m = (($manut->periodo == 'm')?($manut->valor):(0));
				$d = (($manut->periodo == 'd')?($manut->valor):(0));
			}
			?>
			<div class="wrapper-nova-manutencao hidden">
				<div class="row">
					<div class="large-12 columns">
						<h4>Deseja agendar a próxima manutenção?</h4>
					</div><!-- col -->
				</div><!-- row -->
				<div class="row">
					<div class="large-3 columns hide-for-small">
						<div class="helper arrow-right text-center">Deixe a data em branco para ignorar o agendamento</div>
					</div><!-- col -->
					<div class="large-3 columns">
						<label>Próxima Manutenção</label>
						<div class="row collapse">
							<div class="large-<?= (($postfix)?('7'):('12')) ?> columns"><input type="text" name="nova_data_agendada" class="datepick" value=""/></div>
							<?
								if($postfix)
								{
									?>
									<div class="large-5 columns"><span class="postfix radius pointer" onClick="suggestData(this)" data-data="<?= date('d/m/Y', strtotime(somaDataAMD(date('Y-m-d'), $Y, $m, $d))) ?>"><?= $postfix ?></span></div>
									<input type="hidden" name="nova_eh_periodica" value="1"/>
									<input type="hidden" name="nova_equipamento_manutencao_periodica" value="<?= $manut->id ?>"/>
									<?
								}
							?>
						</div><!-- row -->
					</div><!-- col -->
					<div class="large-6 columns">
						<label>Observações</label>
						<input type="text" name="nova_observacao" value=""/>
					</div><!-- col -->
				</div><!-- row -->
			</div>
			<script>
				function suggestData(clicado) {
					clicado = $(clicado);
					clicado.closest('.row').find('input[name="nova_data_agendada"]').val(clicado.attr('data-data'));
				}

				$(document).ready(function(){
					$(document).on('change', 'input[name="data_realizada"]', function(){
						if($(this).val() != ''){
							$('.wrapper-nova-manutencao').fadeIn(function(){
								$(this).find('input[name="nova_data_agendada"]').closest('.row').find('[data-data]').trigger('click');
							});
						}
						else {
							$('.wrapper-nova-manutencao').fadeOut().find('input[name="nova_data_agendada"]').val('');
						}
					});
				}) //doc.ready
			</script>
			<?
			return ob_get_clean();
		}
	}

	function field_equipamento_manutencao_data_realizada($tipo, $obj)
	{
		if($tipo == 'update')
		{
			ob_start();
			$rand = rand(1,1000);
			?>
			<div class="row collapse">
				<div class="large-7 columns">
					<input type="text" name="data_realizada" class="datepick" value="<?= (($tipo == 'update' && strlen(trim($obj->data_realizada)))?(date('d/m/Y H:i', strtotime($obj->data_realizada))):('')) ?>"/>
				</div>
				<div class="large-5 columns">
					<span class="postfix radius pointer" data-data='<?= date('d/m/Y', strtotime(date('Y-m-d'))) ?>' onClick="suggestData<?= $rand ?>(this)">hoje</span>
					<script>
						function suggestData<?= $rand; ?>(clicado) {
							clicado = $(clicado);
							clicado.closest('.row').find('input[name="data_realizada"]').val(clicado.attr('data-data')).trigger('change');
						}
					</script>
				</div><!-- col -->
			</div><!-- row -->
			
			<?
			return ob_get_clean();
		}
	}

	function tipoEquipamentoTotal($obj)
	{
		$sql = "SELECT COUNT(*) AS total FROM equipamento WHERE tipo_equipamento = '".$obj->id."' AND STATUS <> ".STATUS_EQUIPAMENTO_BAIXA;
		$res = dboQuery($sql);
		$lin = dboFetchObject($res);
		return $obj->nome.(($lin->total)?(" <span class='help' title='Total de equipamentos cadastrados'>(".$lin->total.")</span>"):(''));
	}

	function criaMovimentacao($obj)
	{
		if(strlen(trim($_POST['movimentacao_data'])) && strlen(trim($_POST['movimentacao_justificativa'])))
		{
			$mov = new equipamento_movimentacao();
			$mov->created_by = loggedUser();
			$mov->created_on = $mov->now();
			$mov->equipamento = $obj->id;
			$mov->data = dataSQL($_POST['movimentacao_data']);
			$mov->responsavel = $_POST['movimentacao_responsavel'];
			$mov->justificativa = $_POST['movimentacao_justificativa'];
			$mov->local_destino = $obj->local;
			$mov->local_detalhe = $obj->local_detalhe;
			$mov->status = $obj->status;
			$mov->save();
		}
	}

	function relacionaEquipamentoRequisicaoItem($tipo, $obj_equipamento)
	{
		if($_GET['requisicao_item_id'])
		{
			$serv = new requisicao_item($_GET['requisicao_item_id']);
			if($serv->size())
			{
				$rel = new requisicao_item_equipamento();
				$rel->requisicao_item = $serv->id;
				$rel->equipamento = $obj_equipamento->id;
				$rel->situacao = $rel->null();
				$rel->data = dboNow();
				$rel->created_by = loggedUser();
				$rel->save();
			}
		}
	}

	function extraiPatrimonios($term)
	{
		$patrimonios = array();

		preg_match_all('#'.REGEX_PATRIMONIO.'#is', $term, $matches);
		if(is_array($matches) && sizeof($matches))
		{
			foreach($matches[0] as $key => $value)
			{
				$foo = '';
				$foo = preg_replace('#[,;eo]#is', '', $matches[1][$key]); //remove todos os lixos do meio de uma sequencia de numeros
				$foo = preg_replace('#\s\s+#is', ' ', $foo); //remove espaços duplicados
				$parts = explode(' ', $foo); //separa a lista de patrimonios por espaço
				if(sizeof($parts))
				{
					foreach($parts as $num_pat)
					{
						$patrimonios[$num_pat] = $num_pat;
					}
				}
			}
		}
		return $patrimonios;
	}

?>
<?

require_once('lib/includes.php');

/* primeiro de tudo, checar se a avaliacao é válida */
$aval = new avaliacao("WHERE id = '".addslashes($_POST['id'])."' AND token = '".addslashes($_POST['token'])."'");
if(!$aval->size())
{
	$json_result['error'] = 1;
	$json_result['message'] = "ERRO: A avaliação não existe.";
	echo json_encode($json_result);
	exit();
}

/* verificando se o usuário digitou tudo mesmo que precisava */
if(
	!strlen($_POST['servico_realizado']) ||
	!strlen($_POST['qualidade']) ||
	!strlen($_POST['tempo']) ||
	!strlen($_POST['feedback'])
)
{
	$json_result['error'] = 1;
	$json_result['message'] = "ERRO: Assinale todos os campos.";
	echo json_encode($json_result);
	exit();
}

/* processamento da avaliacao */

/* verificando se o usuário não tentou enviar 2x a avaliacao */
if(!checkSubmitToken())
{
	$json_result['error'] = 1;
	$json_result['message'] = "Tentativa de avaliação duplicada. Clique somente 1 vez no botão.";
	echo json_encode($json_result);
	exit();
}

/* salvando dados da avaliacao em si */
$aval->servico_realizado = $_POST['servico_realizado'];
$aval->qualidade = $_POST['qualidade'];
$aval->tempo = $_POST['tempo'];
$aval->qualidade = $_POST['qualidade'];
$aval->feedback = $_POST['feedback'];
$aval->comentario = $_POST['comentario'];
$aval->respondido_em = $aval->now();
$aval->ip_requisitante = $_SERVER['REMOTE_ADDR'];
$aval->created_by = $_pes->id;
$aval->update();

/* disparando e-mails que a avaliacao está finalizada */
$params = array(
	'id_avaliacao' => $aval->id,
	'contexto' => 'avaliacao_realizada',
);
//disparaEmails($params);
disparaEmail('administrador-servico-avaliado', $params);

/* salvando dados do item da requisição - update do status  para avaliado (9) */
$serv = new requisicao_item($aval->requisicao_item);
$serv->status = STATUS_AVALIADO; //avaliado
$serv->updated_on = $serv->now();
$serv->update();

/* salvando um histórico com os dados que o usuário enviou */

/* mensagem para o historico */
$message = "
	Serviço realizado: ".(($_POST['servico_realizado'] == 'nao')?('Não'):('Sim'))."<br />
	Qualidade: ".$_POST['qualidade']."<br />
	Tempo: ".$_POST['tempo']."<br />
	Feedback: ".$_POST['feedback']."
	".((strlen($_POST['comentario']))?("<br />Comentário: ".$_POST['comentario']):(''))."
";

/* pegando ultimo historico para dados */
$last_hist = new historico("WHERE requisicao_item = '".$serv->id."' ORDER BY id DESC LIMIT 1");

/* montando objeto do historico e salvando */
$hist = new historico();
$hist->requisicao_item = $serv->id;
$hist->created_by = ((loggedUser())?(loggedUser()):($serv->___requisicao___id_requisitante));
$hist->data = $hist->now();
$hist->prioridade = $last_hist->prioridade;
$hist->status = STATUS_AVALIADO;
$hist->comentario = $message;
$hist->save();

/* Mensagem de sucesso e informações */
$json_result['success'] = 1;
$json_result['id_avaliacao'] = $aval->id;
$json_result['id_requisicao_item'] = $serv->id;
$json_result['message'] = "Sucesso. Obrigado!";

echo json_encode($json_result);

?>
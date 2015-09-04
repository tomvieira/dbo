<?

/*
To use the ajax interface, just create a custom button with DBOMaker and use the class='ajax-button' on the <a> element.
The response from this file must be in JSON format.
Currently, the implemented functions are:

- message: shows a message on successful request
- html: inserts data in the parent page using jQuery '.html()' function

Example:

----------------------------------------------------------------------------------------
$json_result['message'] = "<div class='success'>This is a successful message!</div>";
$json_result['html'][0]['selector'] = '#wrapper-titulo h1';
$json_result['html'][0]['content'] = 'Total';
$json_result['html'][1]['selector'] = '#wrapper-titulo span';
$json_result['html'][1]['content'] = 'Insanity!';

echo json_encode($json_result);
----------------------------------------------------------------------------------------

The above example will return a Success message on the parent page, and replace the System name and description with the
"Total Insanity!" sentence.

*/

include('lib/includes.php');
include('auth.php');

$error = array();

//implementar validação server-side..

/* setando na seção um token para impedir a resubmissão dos arquivos */

/* checando se todos os dados do requisitante estão ok */
if(
	!strlen(trim($_POST['nome_requisitante'])) ||
	!strlen(trim($_POST['email_requisitante'])) ||
	!strlen(trim($_POST['telefone_requisitante']))
)
{
	$error[] = "Dados do requisitante inconsistentes";
}

if(sizeof($error) == 0 && checkSubmitToken())
{
	//deu tudo certo... primeiro passo: criar a requisicao

	//criando a requisicao
	$req = new requisicao();
	$req->created_by = loggedUser();
	$req->created_on = $req->now();
	$req->id_requisitante = loggedUser();
	$req->nome_requisitante = trim($_POST['nome_requisitante']);
	$req->email_requisitante = trim($_POST['email_requisitante']);
	$req->telefone_requisitante = trim($_POST['telefone_requisitante']);
	/* atualiza o telefone da pessoa */
	updateTelefone(dboescape($_POST['email_requisitante']), dboescape($_POST['telefone_requisitante']));
	$req->data = $req->now();
	$req->navegador = $_SERVER['HTTP_USER_AGENT'];
	if($req->save())
	{
		//se criou a requisicao certinho, criar os itens.

		$count_item = 0; //contador de itens de requisicao
		
		$servicos_criados = array(); //este vetor vai guardar os itens criados para serem exibidos na tela de acompanhamento

		foreach($_POST['local'] as $key_local => $id_local)
		{
			//para cada local, iterar todos os servicos.
			foreach($_POST['tipo_servico'][$key_local] as $key_servico => $id_tipo_servico)
			{

				/* instanciando o tipo de serviço para pegar a prioridade */
				$serv = new tipo_servico($id_tipo_servico);

				$count_item++;

				$item = new requisicao_item();
				$item->requisicao = $req->id;
				$item->created_by = loggedUser();
				$item->created_on = $item->now();
				$item->numero = $count_item;
				$item->local = $id_local;
				$item->dias_feedback = (($serv->dias_feedback > 0)?($serv->dias_feedback):(0));
				$item->tipo_servico = $id_tipo_servico;
				$item->descricao = $_POST['descricao'][$key_local][$key_servico];
				$item->prioridade = $serv->prioridade;
				$item->status = 0;
				$item->token = generatePassword();
				$item->save();

				//verificando se existem patrimonios no texto, para vinculo com o item de requisicao.
				if(COLETAR_PATRIMONIOS)
				{
					$pats = extraiPatrimonios($item->descricao);
					if(sizeof($pats))
					{
						$item->relacionaEquipamentos($pats);
					}
				}

				//criando o histórico inicial para cada item
				$hist = new historico();
				$hist->requisicao_item = $item->id;
				$hist->created_by = loggedUser();
				$hist->data = $hist->now();
				$hist->prioridade = $item->prioridade;
				$hist->status = $item->status;
				$hist->save();

				$servicos_criados[] = $item->id;
			}
		}

		$params = array(
			'id_requisicao' => $req->id,
			'status' => STATUS_NOVO
		);
		//disparaEmails($params);
		disparaEmail('administrador-nova-requisicao', array('id_requisicao' => $req->id));
		//tudo cadastrado!
		setMessage("<div class='success'>Requisição criada com sucesso!</div>");
		$json_result['redirect'] = implode('-', $servicos_criados); //implodindo todos os ids dos itens criados
	}
	else
	{
		//se não criou a requisicao, tem algo muito errado!
		$json_result['message'] = "<div class='error'>Erro ao criar a requisição. Contate a DTI (4651)</div>";
	}
}

echo json_encode($json_result);

?>
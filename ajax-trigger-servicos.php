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


if($_GET['acao'] == 'iniciar')
{
	if(hasPermission('iniciar-servicos'))
	{
		$serv = new requisicao_item($_GET['id']);
		if($serv->size())
		{
			$serv->status = STATUS_EM_ANDAMENTO;
			$serv->update();

			$hist = new historico();
			$hist->requisicao_item = $serv->id;
			$hist->created_by = loggedUser();
			$hist->data = $hist->now();
			$hist->prioridade = $serv->prioridade;
			$hist->status = $serv->status;
			$hist->comentario = ((strlen(trim($_GET['horario'])))?("Horário aproximado da chegada do servidor: ".strtolower($_GET['horario'])):(''));
			$hist->save();

			/* disparando e-mails */
			$params = array(
				'id_servico' => $_GET['id'], 
				'status' => STATUS_EM_ANDAMENTO
			);

			disparaEmails($params);
			$json_result['message'] = "<div class='success'>Serviço ".$serv->requisicao."/".$serv->numero." iniciado com sucesso</div>";
			$json_result['success'] = 1;
		}
		else
		{
			$json_result['message'] = "<div class='error'>O serviço requisitado não existe</div>";
		}
	}
}

if($_GET['acao'] == 'finalizar')
{
	if(hasPermission('finalizar-servicos'))
	{
		$serv = new requisicao_item($_GET['id']);
		if($serv->size())
		{
			$serv->finalizado_servidor = 1;
			$serv->update();

			/* disparando e-mails */
			$params = array(
				'id_servico' => $_GET['id'], 
				'contexto' => 'finalizado_servidor'
			);
			disparaEmails($params);
			$json_result['message'] = "<div class='success'>Serviço ".$serv->requisicao."/".$serv->numero." finalizado com sucesso</div>";
			$json_result['success'] = 1;
		}
		else
		{
			$json_result['message'] = "<div class='error'>O serviço requisitado não existe</div>";
		}
	}
}

echo json_encode($json_result);

?>
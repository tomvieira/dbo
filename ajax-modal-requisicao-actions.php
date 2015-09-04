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

//validações... verifica se o usuário tem permissão de gerenciar servicos
if(!hasPermission('gerenciar-servicos'))
{
	criticalError('Você não tem permissão para gerenciar servicos');
}

foreach($_POST['tipo_servico'] as $id_item => $tipo_servico)
{
	//carrega o item para dar update de status, prioridade, e flag de finalizado pelo servidor
	$item = new requisicao_item($id_item);
	$item->status = $_POST['status'][$item->id];
	$item->prioridade = $_POST['prioridade'][$item->id];
	$item->finalizado_servidor = $_POST['finalizado_servidor'][$item->id];
	$item->tipo_servico = $tipo_servico;
	$item->data_agendada = ((strlen(trim($_POST['data_agendada'][$item->id])))?(dataSql($_POST['data_agendada'][$item->id])):($item->null()));
	if(isset($_POST['dias_feedback'][$item->id]))
	{
		$item->dias_feedback = $_POST['dias_feedback'][$item->id];
	}
	$item->observacao = $_POST['observacao'][$item->id];
	$item->updated_on = $item->now();
	$item->update(); //salvou o item...

	//...agora atribuir os servidores: primeiro deletar todos os servidores atribuidos
	$sql = "DELETE FROM requisicao_item_servidor WHERE item = '".addslashes($item->id)."'";
	dboQuery($sql);

	//e insere os novos que foram selecionados
	if(is_array($_POST['servidor'][$item->id]))
	{
		foreach($_POST['servidor'][$item->id] as $key => $id_servidor)
		{
			$sql = "INSERT INTO requisicao_item_servidor (item, servidor) VALUES ('".addslashes($item->id)."', '".addslashes($id_servidor)."')";
			dboQuery($sql); //servidores atribuidos...
		}
	}

	//...agora atribuir estoque (implementar)

	/*
	

		LOGICA DO ESTOQUE AQUI

	
	*/

	//...e finalmente salvar o historico.
	//soh salva o historico se mudou o status, prioridade ou se foi adiconado um comentário.
	//então primeiro deve-se pegar o último historico adicionado no item para fins de comparacao:
	$last_hist = new historico("WHERE requisicao_item = '".addslashes($item->id)."' ORDER BY data DESC LIMIT 1");
	if($last_hist->size())
	{
		if(
			$item->status != $last_hist->status ||             //mudou o status
			$item->prioridade != $last_hist->prioridade ||     //mudou a prioridade
			strlen(trim($_POST['comentario'][$item->id])) > 0  //o supervisor adicionou um comentario
		)
		{
			$hist = new historico();
			$hist->requisicao_item = $item->id;
			$hist->created_by = loggedUser();
			$hist->data = $hist->now();
			$hist->prioridade = $item->prioridade;
			$hist->status = $item->status;
			$hist->comentario = $_POST['comentario'][$item->id];
			$hist->save();

			/* se o item for concluido, enviar o email de avaliacao para o usuario */
			if($item->status == STATUS_CONCLUIDO && $item->status != $last_hist->status)
			{
				$item->createAvaliacao();
			}

			/* sempre que ocorre uma mudança de historico, as pessoas envolvidas devem ser informadas por e-mail */
			$params = array(
				'status' => $item->status,
				'id_servico' => $item->id
			);
			disparaEmails($params);
		}
	}
}

$json_result['message'] = "<div class='success'>Requisição atualizada com sucesso!</div>";
$json_result['reload'] = '#wrapper-requisicao';
$json_result['reload_list'] = 1;

echo json_encode($json_result);

?>
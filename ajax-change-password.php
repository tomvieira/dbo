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

$pes = new dbo('pessoa');
$pes->id = loggedUser();
$pes->load();

if(trim($pes->pass) == '')
{
	$json_result['message'] = "<div class='error'>Impossível alterar a senha: Seu usuário é integrado ao e-mail da FCFAR</div>";
}
elseif($pes->pass != $_POST['current_pass'])
{
	$json_result['message'] = "<div class='error'>Erro: Senha atual inválida</div>";
}
elseif(trim($_POST['new_pass']) == '')
{
	$json_result['message'] = "<div class='error'>Erro: Nova senha inválida</div>";
}
elseif($_POST['new_pass'] != $_POST['new_pass_check'])
{
	$json_result['message'] = "<div class='error'>Erro: As senhas não conferem</div>";
}
else
{
	$pes->pass = trim($_POST['new_pass']);
	$pes->update();
	$json_result['message'] = "<div class='success'>Senha alterada com sucesso!</div>";
	$json_result['success'] = "1";
}

echo json_encode($json_result);

?>
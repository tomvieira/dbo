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
require_once('auth.php');

$error = false;

/* verifica se está tudo ok com o codigo */

/*if(!is_numeric($_POST['codigo']))
{
	$error = "Erro: O código digitado não é um número";
}*/

$inv = new equipamento("WHERE codigo = '".trim(intval($_POST['codigo']))."'");
if($inv->size())
{
	$error = 'Erro: O código digitado já está cadastrado no banco de dados';
}

if(!$error)
{
	$inv = new equipamento();
	$inv->smartSet($_POST);
	$inv->custo = ((strlen(trim($_POST['custo'])))?(priceSQL($_POST['custo'])):($inv->null()));
	$inv->local = $_POST['local'];
	$inv->tipo_equipamento = $_POST['tipo_equipamento'];
	$inv->equipamento_marca = $_POST['equipamento_marca'];
	$inv->modelo = $_POST['modelo'];
	$inv->numero_serie = $_POST['numero_serie'];
	$inv->local_detalhe = $_POST['local_detalhe'];
	$inv->observacao = $_POST['observacao'];
	$inv->foto = $_POST['foto'];
	$inv->patrimonio = ((intval($_POST['patrimonio'])>0)?(intval($_POST['patrimonio'])):($inv->null()));
	$inv->ai = ((intval($_POST['ai'])>0)?(intval($_POST['ai'])):($inv->null()));
	$inv->codigo = ((strlen(trim($_POST['codigo'])))?($_POST['codigo']):($inv->null()));
	$inv->created_by = loggedUser();
	$inv->created_on = $inv->now();
	if($_POST['vinculo_material'] > 0)
	{
		$inv->vinculo_material = $_POST['vinculo_material'];
	}
	$inv->save();

	setMessage("<div class='success'>Item duplicado com sucesso!</div>");
	$json_result['sucesso'] = $inv->id;
}
else
{
	$json_result['message'] = "<div class='error'>".$error."</div>";
}

echo json_encode($json_result);

?>
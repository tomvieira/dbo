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

if($_POST['action'] == 'update-preferencias')
{
	/* notificacoes-desktop */
	$_opt->set('desktop_notifications', dboescape($_POST['desktop_notifications']), $_pes->id);

	/* salvando notificacoes */
	if(sizeof($_POST['notificacoes']))
	{
		$_opt->set('notificacoes_areas', implode("-", $_POST['notificacoes']), $_pes->id);
	}
	else
	{
		$_opt->delete('notificacoes_areas', $_pes->id);
	}

	/* salvando contexto */
	if(sizeof($_POST['contexto']))
	{
		$_opt->set('contexto_areas', implode("-", $_POST['contexto']), $_pes->id);
	}
	else
	{
		$_opt->delete('contexto_areas', $_pes->id);
	}
	
	$json_result['message'] = "<div class='success'>Sucesso. Recarregue o sistema (F5) para ver as alterações.</div>";
}

echo json_encode($json_result);

?>
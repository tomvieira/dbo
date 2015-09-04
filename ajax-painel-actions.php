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

$painel = new painel();

$json_result = array();

//painel de requisicoes

if(isset($_GET['status']))
{
	$painel->setParam('status', addslashes($_GET['status']));
}

if(isset($_GET['show_all']))
{
	$painel->setParam('show_all', (($_GET['show_all'] == 0)?(false):(true)));
}

if(isset($_GET['term']))
{
	$painel->setParam('term', addslashes($_GET['term']));
}

if($_GET['show_list'])
{
	$item = new requisicao_item();
	ob_start();
	$item->getPainelList($painel->getParams());
	$ob_result = ob_get_clean();
	$json_result['list'] = $ob_result;
}

//painel de materiais

if(isset($_GET['painel_material_modulo']))
{
	$painel->setParam('painel_material_modulo', addslashes($_GET['painel_material_modulo']));
}

if(isset($_GET['painel_material_order_by']))
{
	$painel->setParam('painel_material_order_by', $_GET['painel_material_order_by']);
}

if(isset($_GET['painel_material_hide_list_equipamentos']))
{
	$painel->clearParamMulti('painel_material_show_list_equipamentos', $_GET['painel_material_hide_list_equipamentos']);
}

if(isset($_GET['painel_material_hide_list_manutencao']))
{
	$painel->clearParamMulti('painel_material_show_list_manutencao', $_GET['painel_material_hide_list_manutencao']);
}

if(isset($_GET['painel_material_set_list_movimentacao']))
{
	$painel->setParamMulti('painel_material_show_list_movimentacao', $_GET['painel_material_set_list_movimentacao']);
}

if(isset($_GET['painel_material_hide_list_movimentacao']))
{
	$painel->clearParamMulti('painel_material_show_list_movimentacao', $_GET['painel_material_hide_list_movimentacao']);
}

if(isset($_GET['painel_material_set_list_equipamentos']))
{
	$painel->setParamMulti('painel_material_show_list_equipamentos', $_GET['painel_material_set_list_equipamentos']);
}

if(isset($_GET['painel_material_set_list_manutencao']))
{
	$painel->setParamMulti('painel_material_show_list_manutencao', $_GET['painel_material_set_list_manutencao']);
}

if(isset($_GET['painel_material_show_list']))
{
	$modulo = $painel->getParam('painel_material_modulo');
	if($modulo == 'manutencao')
	{
		$json_result['list'] = equipamento::getPainelListManutencao($painel->getParams());
	}
	else
	{
		$json_result['list'] = $modulo::getPainelList($painel->getParams());
	}
}

if(isset($_GET['painel_material_show_list_equipamentos']))
{
	$painel->setParamMulti('painel_material_show_list_equipamentos', $_GET['painel_material_show_list_equipamentos']);
	ob_start();
	?>
	<tr class="sublist">
		<td colspan="10">
			<?
				echo equipamento::getPainelListDetalhe($_GET['painel_material_show_list_equipamentos'], $painel->getParams());
			?>
		</td>
	</tr>
	<?
	$json_result['detalhe'] = ob_get_clean();
}

if(isset($_GET['painel_material_show_list_manutencao']))
{
	$painel->setParamMulti('painel_material_show_list_manutencao', $_GET['painel_material_show_list_manutencao']);
	ob_start();
	?>
	<tr class="sublist">
		<td colspan="10">
			<?
				echo equipamento::getPainelListManutencaoDetalhe($_GET['painel_material_show_list_manutencao'], $painel->getParams());
			?>
		</td>
	</tr>
	<?
	$json_result['detalhe'] = ob_get_clean();
}

if(isset($_GET['painel_material_show_list_movimentacao']))
{
	$painel->setParamMulti('painel_material_show_list_movimentacao', $_GET['painel_material_show_list_movimentacao']);
	ob_start();
	?>
	<tr class="sublist">
		<td colspan="10">
			<?
				echo material::getPainelListDetalhe($_GET['painel_material_show_list_movimentacao'], $painel->getParams());
			?>
		</td>
	</tr>
	<?
	$json_result['detalhe'] = ob_get_clean();
}


#######################################################################################################################

echo json_encode($json_result);

?>
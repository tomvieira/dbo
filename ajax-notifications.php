<?

require_once('lib/includes.php');

/* 
	Este aquivo pega o total de serviços que necessitam da atenção do administrador
	Os tipos de serviço são:
	- Novos: Com status "novo" (0)
	- Finalizados: Setados com finalizado pelo servidor, com STATUS menor que 6 (diferente de avaliado, concluido, cancelado e não aprovado)
	- Aguardando: Requisições com "aguardando" (1)
	- Pendente: Novo + Finalizado
*/

if(!isset($_serv->total_novo))
{
	$_serv->getNotifications();
}

/* administrador */
$json_result[novo] = $_serv->getNotificationTag('novo');
$json_result[aguardando] = $_serv->getNotificationTag('aguardando');
$json_result[finalizado] = $_serv->getNotificationTag('finalizado');
$json_result[pendente] = $_serv->getNotificationTag('pendente');
$json_result[feedback] = $_serv->getNotificationTag('feedback_atrasado');

/* usuario */
$json_result[usuario_aguardando] = $_serv->getNotificationTag('usuario_aguardando');
$json_result[usuario_concluido] = $_serv->getNotificationTag('usuario_concluido');

/* calendario */
$json_result[calendar_atrasado] = $_calendar->getNotificationTag('calendar_atrasado');
$json_result[calendar_avisado] = $_calendar->getNotificationTag('calendar_avisado');

/* desktop notifications */
$json_result[novos_chamados] = $_serv->getNovosChamados();

/* logout */
if(!$_pes->size())
{
	$json_result[logout] = 1;
}

echo json_encode($json_result);

?>
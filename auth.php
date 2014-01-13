<?

require_once('lib/includes.php');

$pes = new dbo('pessoa');
$pes->id = loggedUser();
$pes->load();

if(!loggedUser() || !$pes->size())
{
	header("Location: login.php?dbo_redirect=".base64_encode(fullUrl()));
	exit();
}
?>
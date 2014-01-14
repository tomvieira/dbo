<?
require_once('lib/includes.php');

if(!loggedUser())
{
	header("Location: login.php?dbo_redirect=".base64_encode(fullUrl()));
	exit();
}
?>
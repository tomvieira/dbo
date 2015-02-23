<?
	if(!file_exists('custom-footer.php'))
	{
		?>
		</div><!-- main-wrap -->
		<script>$(document).foundation();</script>
		<?= getMessage(); ?>
		<? dboFooter(); ?>
		<? $hooks->do_action('footer'); ?>
		</body>
		</html>
		<?
	}
	else
	{
		include('custom-footer.php');
	}
?>

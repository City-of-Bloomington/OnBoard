<div id="panel-one">
<?php
	if (userHasRole('Administrator')) {
		include APPLICATION_HOME.'/templates/html/partials/panel-widgets/Administrator.inc';
	}
?>
</div>

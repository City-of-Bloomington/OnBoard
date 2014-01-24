<div id="panel-one">
<?php
	global $USER_ROLE;
	if ($USER_ROLE == 'Administrator') {
		include __DIR__.'/panel-widgets/Admin.inc';
	}
?>
</div>

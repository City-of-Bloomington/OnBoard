<div id="panel-one">
<?php
	echo $this->includeBlocks('panel-one');

	if (userHasRole('Clerk')) {
		echo "
		<div>
			<h1>Clerk</h1>
			<ul><li><a href=\"".BASE_URL."/tags\">Tags</a></li>
			</ul>
		</div>
		";
	}

	if (userHasRole('Administrator')) {
		include APPLICATION_HOME.'/templates/html/partials/panel-widgets/Administrator.inc';
	}
?>
</div>

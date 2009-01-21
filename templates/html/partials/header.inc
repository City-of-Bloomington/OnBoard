<?php $startTime = microtime(1); ?>
<head>
	<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
	<meta http-equiv="Content-Script-Type" content="text/javascript" />
	<link rel="SHORTCUT ICON" href="/favicon.ico" />
	<style type="text/css" media="screen">
		@import url('/skins/origin-external/reset.css');
		@import url('/skins/origin-external/layouts/<?php echo $this->layout; ?>.css');
		@import url('/skins/origin-external/screen.css');
	</style>
	<style type="text/css" media="print">
		@import url('/skins/origin-external/reset.css');
		@import url('/skins/origin-external/layouts/<?php echo $this->layout; ?>.css');
		@import url('/skins/origin-external/print.css');
	</style>
	<script type="text/javascript" src="<?php echo BASE_URL; ?>/js/functions.js"></script>

	<!-- Load the YUI toolkit -->
	<script type="text/javascript" src="<?php echo BASE_URL; ?>/js/yui/build/utilities/utilities.js"></script>
	<script type="text/javascript" src="<?php echo BASE_URL; ?>/js/yui/build/yahoo-dom-event/yahoo-dom-event.js"></script>
	<script type="text/javascript" src="<?php echo BASE_URL; ?>/js/yui/build/element/element-min.js"></script>
	<script type="text/javascript" src="<?php echo BASE_URL; ?>/js/yui/build/datasource/datasource-min.js"></script>
	<script type="text/javascript" src="<?php echo BASE_URL; ?>/js/yui/build/datatable/datatable-min.js"></script>
	<script type="text/javascript" src="<?php echo BASE_URL; ?>/js/yui/build/calendar/calendar-min.js"></script>
	<script type="text/javascript" src="<?php echo BASE_URL; ?>/js/yui/build/autocomplete/autocomplete-min.js"></script>
	<style type="text/css" media="screen">
		@import url('<?php echo BASE_URL; ?>/js/yui/build/assets/skins/sam/skin.css');
		@import url('<?php echo BASE_URL; ?>/js/yui/build/calendar/assets/skins/sam/calendar.css');
		@import url('<?php echo BASE_URL; ?>/js/yui/build/datatable/assets/skins/sam/datatable.css');
		@import url('<?php echo BASE_URL; ?>/build/autocomplete/assets/skins/sam/autocomplete.css');
	</style>

	<title>Boards &amp; Commissions - City of Bloomington, Indiana</title>
</head>

<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html>
<?php $lang = strtolower(substr(LOCALE, 0, 2)); ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?= $lang; ?>" lang="<?= $lang; ?>">
<?php
	$this->_include('partials/head.inc');
?>
<body class="full-width">
	<?php
        if (!isset($_REQUEST['popup'])) {
            $this->_include('partials/header.inc');
        }
	?>
	<main>
        <div id="content-panel" class="panel">
            <?php
                $this->_include('errorMessages.inc');
                echo $this->includeBlocks();
            ?>
        </div>
	</main>
	<?php $this->_include('partials/footer.inc'); ?>
</body>
</html>
<?php
	$this->_include('partials/garbageCollection.inc');
?>

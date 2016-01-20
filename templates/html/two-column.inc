<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<?php
  $this->layout = 'two-column';
  $this->_include('/partials/head.inc');
?>
<body>
    <?= $this->_include('/partials/header.inc'); ?>
    <main class="fn1-main">
        <div class="fn1-main-container">
            <aside class="onboard-contextInfo">
                <?= $this->includeBlocks('contextInfo'); ?>
            </aside>
            <div class="onboard-content">
            <?php
                include BLOSSOM.'/errorMessages.php';
                echo $this->includeBlocks();
            ?>
            </div>
        </div>
    </main>
    <?= $this->_include('/partials/footer.inc'); ?>
</body>
</html>
<?= $this->_include('partials/garbageCollection.inc'); ?>

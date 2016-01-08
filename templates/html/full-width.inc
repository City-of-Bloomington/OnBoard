<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<?php
  $this->layout = 'full-width';
  include __DIR__.'/partials/head.inc';
?>
<body>
  <?php
    if (!isset($_REQUEST['popup'])) {
      include __DIR__.'/partials/header.inc';
    }
  ?>
  <main class="fn1-main">
    <div class="fn1-main-container">
      <?php
        include BLOSSOM.'/errorMessages.php';

        echo $this->includeBlocks();
      ?>
    </div>
  </main>
  <?php
    include __DIR__.'/partials/footer.inc';
  ?>
</body>
</html>
<?php
  include __DIR__.'/partials/garbageCollection.inc';
?>

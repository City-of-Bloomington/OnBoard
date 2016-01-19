<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<?php
  $this->layout = 'three-column';
  include __DIR__.'/partials/head.inc';
?>
<body>
  <?php
    include __DIR__.'/partials/header.inc';
  ?>
  <main>
    <?php
      include __DIR__.'/partials/panel-one.inc';
      include __DIR__.'/partials/panel-two.inc';
    ?>
    <div id="content-panel">
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

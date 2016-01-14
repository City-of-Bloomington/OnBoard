<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<?php
    $this->layout = 'full-width';
    $this->_include('partials/head.inc');
?>
<body>
    <?php
        if (!isset($_REQUEST['popup'])) {
            $this->_include('partials/header.inc');
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
        $this->_include('partials/footer.inc');
    ?>
</body>
</html>
<?php
    $this->_include('partials/garbageCollection.inc');
?>

<header  class="fn1-siteHeader">
  <div   class="fn1-siteHeader-container">
    <div class="fn1-site-title">
      <h1><a href="<?php echo BASE_URI; ?>"><?php echo APPLICATION_NAME; ?></a></h1>
      <div class="fn1-site-location">
        <a href="<?php echo BASE_URI; ?>">City of Bloomington, John Hamilton, Mayor</a>
      </div>
    </div>

    <div class="fn1-site-utilityBar">
    <?php
        $this->addToAsset('scripts', FN1_URL.'/js/dropdown.js');
        include __DIR__.'/panel-widgets/user_menu.inc';
        include __DIR__.'/panel-widgets/admin_menu.inc';
    ?>
    </div>
  </div>
  <?php
    include __DIR__.'/menubar.inc';
  ?>
</header>

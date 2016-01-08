<?php
use Blossom\Classes\View;
?>
<header class="fn1-siteHeader">
  <div class="fn1-siteHeader-container">
    <div class="fn1-site-title">
      <h1><a href="<?php echo BASE_URI; ?>"><?php echo APPLICATION_NAME; ?></a></h1>
      <div class="fn1-site-location">
        <a href="<?php echo BASE_URI; ?>">City of Bloomington, Mark Kruzan, Mayor</a>
      </div>
    </div>

    <div class="fn1-site-utilityBar">
      <?php
      $this->addToAsset('scripts', FN1_URL.'/js/dropdown.js');
      ?>
      <nav class="fn1-dropdown fn1-siteMenu">
        <button class="fn1-dropdown-launcher" aria-haspopup="true" aria-expanded="false">Admin</button>
        <div class="fn1-dropdown-links">
          <?php
            $li = '<a href="%s" class="fn1-dropdown-link">%s</a>';

            if (isset($_SESSION['USER'])) {
              echo sprintf($li, BASE_URI.'/login/logout', $this->translate('logout'));

              $name = View::escape($_SESSION['USER']->getFullname());
              echo sprintf('<li>'.sprintf($this->translate('messages.signed_in_as'), $name).'</li>');
            }
            else {
              $return_url = View::escape($_SERVER['REQUEST_URI']);
              echo sprintf($li, BASE_URI.'/login?return_url='.$return_url, $this->translate('login'));
            }
          ?>
        </div>
      </nav>
    </div>
  </div>
  <?php
    include __DIR__.'/menubar.inc';
  ?>
</header>

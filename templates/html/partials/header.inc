<header>
    <h1><a href="<?php echo BASE_URI; ?>"><?php echo self::escape(APPLICATION_NAME); ?></a></h1>
    <div class="utilityBar">
    <?php
        $this->addToAsset('scripts', BASE_URI.'/js/menus.js');

        $this->_include('/partials/panel-widgets/user_menu.inc');
        $this->_include('/partials/panel-widgets/admin_menu.inc');
    ?>
    </div>
    <?php $this->_include('partials/nav1.inc'); ?>
</header>

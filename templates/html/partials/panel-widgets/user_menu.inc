<?php
/**
 * @copyright 2016-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
if (isset($_SESSION['USER'])) {
    $name = self::escape($_SESSION['USER']->getFullname());
    $links = [['url'=> parent::generateUri('login.logout'), 'label'=>$this->_('logout')]];

    $helper = $this->getHelper('dropdown');
    echo $helper->dropdown($links, $name, 'user_menu');
}
else {
    $return_url = self::escape($_SERVER['REQUEST_URI']);
    $login      = parent::generateUri('login.index');
    echo "<nav><a href=\"$login?return_url=$return_url\">{$this->_('login')}</a></nav>";
}

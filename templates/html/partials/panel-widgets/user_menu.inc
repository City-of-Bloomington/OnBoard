<?php
/**
 * @copyright 2016-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
if (isset($_SESSION['USER'])) {
    $name = self::escape($_SESSION['USER']->getFullname());
    $links = [['url'=> BASE_URI.'/login/logout', 'label'=>$this->_('logout')]];

    $helper = $this->getHelper('dropdown');
    echo $helper->dropdown($links, $name, 'user_menu');
}
else {
    $return_url = self::escape($_SERVER['REQUEST_URI']);
    echo "<nav><a href=\"".BASE_URI."/login?return_url=$return_url\">{$this->_('login')}</a></nav>";
}

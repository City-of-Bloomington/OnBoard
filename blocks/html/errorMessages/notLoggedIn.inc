<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
$loginUrl = BASE_URI.'/login?return_url='.$_SERVER['REQUEST_URI'];
$h = $this->template->getHelper('buttonLink');
$button = $h->buttonLink(
    BASE_URI.'/login?return_url='.$_SERVER['REQUEST_URI'],
    $this->_('login'),
    'login'
);

?>
<p><?= $this->_('notLoggedIn', 'errors'); ?></p>
<p><?= $button; ?></p>

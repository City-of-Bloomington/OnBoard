<?php
/**
 * @copyright 2018-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);

$h = $this->template->getHelper('buttonLink');
$button = $h->buttonLink(
    parent::generateUri('login.index').'?return_url='.$_SERVER['REQUEST_URI'],
    $this->_('login'),
    'login'
);

?>
<p><?= $this->_('notLoggedIn', 'errors'); ?></p>
<p><?= $button; ?></p>

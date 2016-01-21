<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
if (isset($_SESSION['USER'])) {
    $name = self::escape($_SESSION['USER']->getFullname());
    echo "
    <nav        class=\"fn1-dropdown fn1-siteMenu\">
        <button class=\"fn1-dropdown-launcher\" aria-haspopup=\"true\" aria-expanded=\"false\">
            $name
        </button>
        <div    class=\"fn1-dropdown-links\">
            <a href=\"".BASE_URI."/login/logout\" class=\"fn1-dropdown-link\">{$this->_('logout')}</a>
        </div>
    </nav>
    ";
}
else {
    $return_url = self::escape($_SERVER['REQUEST_URI']);
    echo "<a href=\"".BASE_URI."/login?return_url=$return_url\">{$this->_('login')}</a>";
}

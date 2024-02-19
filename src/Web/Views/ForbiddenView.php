<?php
/**
 * @copyright 2016-2021 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Web\Views;

use Web\View;

class ForbiddenView extends View
{
    public function __construct()
    {
        header('HTTP/1.1 403 Forbidden', true, 403);

        parent::__construct();
        $this->vars['errorMessages'][] = isset($_SESSION['USER'])
            ? 'noAccessAllowed'
            : 'notLoggedIn';
    }

    public function render(): string
    {
        return $this->twig->render('html/403.twig');
    }
}

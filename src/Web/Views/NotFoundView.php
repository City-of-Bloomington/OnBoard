<?php
/**
 * @copyright 2016-2021 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Web\Views;

use Web\View;

class NotFoundView extends View
{
    public function __construct()
    {
        header('HTTP/1.1 404 Not Found', true, 404);

        parent::__construct();
    }

    public function render(): string
    {
        return $this->twig->render('html/404.twig');
    }
}

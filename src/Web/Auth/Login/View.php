<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Auth\Login;

class View extends \Web\View
{
    public function render(): string
    {
        return $this->twig->render('html/login.twig');
    }
}

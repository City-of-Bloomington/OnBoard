<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Auth\Login;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['return_url'])) {
            $_SESSION['return_url'] = $_REQUEST['return_url'];
        }

        if (empty($_SESSION['return_url'])) {
            $_SESSION['return_url'] = BASE_URL;
        }

        return new View();
    }
}

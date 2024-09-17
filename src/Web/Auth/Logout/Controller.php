<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Auth\Logout;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $return_url = !empty($_SESSION['return_url'])
                           ? $_SESSION['return_url']
                           : (!empty($_REQUEST['return_url']) ? $_REQUEST['return_url'] : BASE_URL);

        session_destroy();
        header('Location: '.$return_url);
        exit();
    }
}

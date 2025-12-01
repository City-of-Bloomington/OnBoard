<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Auth\Logout;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $_SESSION = [];
        header('Location: '.BASE_URL);
        exit();
    }
}

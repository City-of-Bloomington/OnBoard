<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Profile\Info;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        return new View($_SESSION['USER']);
    }
}

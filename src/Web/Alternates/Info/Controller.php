<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Alternates\Info;

use Application\Models\Alternate;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        try { $alternate = new Alternate($params['id']); }
        catch (\Exception $e) {
            $_SESSION['errorMessages'][] = $e->getMessage();
            return new \Web\Views\NotFoundView();
        }

        return new View($alternate);
    }
}

<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Notifications\Definitions\Info;

use Application\Models\Notifications\Definition;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['definition_id'])) {
            try { $d = new Definition($_REQUEST['definition_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (!isset($d)) { return new \Web\Views\NotFoundView(); }

        return new View($d);
    }
}

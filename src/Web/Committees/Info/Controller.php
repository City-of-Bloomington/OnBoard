<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Info;

use Application\Models\Committee;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($params['id'])) {
            try {
                $c = new Committee($params['id']);
                switch ($this->outputFormat) {
                    case 'json':
                        return new \Web\Views\JSONView($c->toArray());
                    break;

                    default:
                        return new View($c);
                }
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        return new \Web\Views\NotFoundView();
    }
}

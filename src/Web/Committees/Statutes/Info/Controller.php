<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Statutes\Info;

use Application\Models\Committee;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($params['id'])) {
            try {
                $committee = new Committee($params['id']);
                $statutes = [];
                foreach ($committee->getStatutes() as $s) {
                    $statutes[] = $s;
                }
                return new View($committee, $statutes);
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        return new \Web\Views\NotFoundView();
    }
}

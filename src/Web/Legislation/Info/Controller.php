<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Legislation\Info;

use Application\Models\Legislation\Legislation;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($params['id'])) {
            try { $legislation = new Legislation($params['id']); }
            catch (\Exception $e) { $_SESSION['errorMesssages'][] = $e->getMessage(); }
        }

        if (isset($legislation)) {
            $committee = $legislation->getCommittee();
            switch ($this->outputFormat) {
                case 'json':
                break;

                default:
                    return new View($legislation);
            }
        }

        return new \Web\Views\NotFoundView();
    }
}

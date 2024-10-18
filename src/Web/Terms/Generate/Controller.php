<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Terms\Generate;

use Application\Models\Term;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($params['id'])) {
            try { $term = new Term($params['id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (empty($_REQUEST['direction'])) { $_REQUEST['direction'] = 'next'; }
        $gen  =   $_REQUEST['direction'] == 'next' ? 'generateNextTerm' : 'generatePreviousTerm';

        if (isset($term)) {
            $newTerm = $term->$gen();
            try { $newTerm->save(); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }

            $url = \Web\View::generateUrl('seats.view', ['id'=>$term->getSeat_id()]);
            header("Location: $url");
            exit();
        }

        return new \Web\Views\NotFoundView();
    }
}

<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Terms\Delete;

use Application\Models\Term;
use Application\Models\TermTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['term_id'])) {
            try {
                $term = new Term($_REQUEST['term_id']);
                $seat = $term->getSeat();
                $url  = \Web\View::generateUrl('seats.view', ['seat_id'=>$seat->getId()]);

                TermTable::delete($term);
                header("Location: $url");
                exit();

            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }
        header('Location: '.View::generateUrl('committees.index'));
        exit();
    }
}

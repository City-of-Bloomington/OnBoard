<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\People\Merge\Candidates;

use Application\Models\Person;
use Application\Models\PeopleTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (   isset($_POST['target_id'])
            && isset($_POST['person_id']) && is_array($_POST['person_id']) && count($_POST['person_id'])>1) {

            try {
                $target = new Person($_POST['target_id']);
                foreach ($_POST['person_id'] as $id) { $people[] = new Person($id); }

                foreach ($people as $p) {
                    if ($p->getId() != $target->getId()) {
                        $target->mergeFrom($p);
                    }
                }
                $_SESSION['errorMessages'][] = 'success';
                $url = \Web\View::generateUrl('people.view', ['person_id'=>$target->getId()]);
                header("Location: $url");
                exit();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }

        }

        $people = [];
        $table  = new PeopleTable();
        $list   = null;
        $search = self::prepareSearch();
        if (isset($_GET['firstname'])) {
            $list  = $table->search($search, 'lastname');
            foreach ($list as $p) { $people[] = $p; }
        }

        $dup = [];
        $res = $table->duplicatesByName();
        foreach ($res as $row) { $dup[] = $row; }

        return new View($people, $search, $dup);
    }

    private static function prepareSearch(): array
    {
        $search = [];
        $fields = ['firstname', 'lastname', 'email'];
        foreach ($fields as $f) {
            if (!empty($_GET[$f])) { $search[$f] = $_GET[$f]; }
        }
        return $search;
    }
}

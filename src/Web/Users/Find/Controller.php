<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Users\Find;

use Application\Models\PeopleTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $search = self::prepareSearch();
        $people = new PeopleTable();
        $users  = [];

        switch ($this->outputFormat) {
            case 'csv':
                $users = $people->search($search);
                $data  = [];
                foreach ($users as $u) {
                    $data[] = [
                        'id'         => $u->getId(),
                        'username'   => $u->getUsername(),
                        'firstname'  => $u->getFirstname(),
                        'lastname'   => $u->getLastname(),
                        'email'      => $u->getEmail(),
                        'department' => $u->getDepartment(),
                        'role'       => $u->getRole()
                    ];
                }
                return new \Web\Views\CSVView('Users', $data);
            break;

            default:
                $page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
                $list = $people->search($search, 'lastname', true);
                $list->setCurrentPageNumber($page);
                $list->setItemCountPerPage(parent::ITEMS_PER_PAGE);
                foreach ($list as $u) { $users[] = $u; }


                return new View($users,
                                $search,
                                $list->getTotalItemCount(),
                                parent::ITEMS_PER_PAGE,
                                $page);
        }
    }

    private static function prepareSearch(): array
    {
        $q = ['user_account'=>true];

        $fields = ['firstname', 'lastname', 'email', 'username', 'department_id', 'role'];
        foreach ($fields as $f) {
            if (!empty($_GET[$f])) { $q[$f] = $_GET[$f]; }
        }
        return $q;
    }
}

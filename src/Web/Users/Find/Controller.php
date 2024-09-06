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
        $_GET['user_account'] = true;

        $people = new PeopleTable();

        switch ($this->outputFormat) {
            case 'csv':
                $users = $people->search($_GET);
                $data  = [];
                foreach ($users as $u) {
                    $columns = ['id', 'username', 'firstname','lastname', 'email', 'department', 'role'];
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
                $page  = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
                $users = $people->search($_GET, null, true);
                $users->setCurrentPageNumber($page);
                $users->setItemCountPerPage(parent::ITEMS_PER_PAGE);

                return new View($users,
                                $users->getTotalItemCount(),
                                parent::ITEMS_PER_PAGE,
                                $page);
        }
    }
}

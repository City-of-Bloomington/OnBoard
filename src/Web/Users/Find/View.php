<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Users\Find;

use Application\Models\DepartmentTable;
use Application\Models\Person;

class View extends \Web\View
{
    public function __construct(array $users, array $search, int $total, int $itemsPerPage, int $currentPage)
    {
        parent::__construct();

        $csv = parent::current_url();
        $csv->format = 'csv';

        $this->vars = [
            'users'                 => $users,
            'total'                 => $total,
            'itemsPerPage'          => $itemsPerPage,
            'currentPage'           => $currentPage,
            'firstname'             => $_GET['firstname'    ] ?? null,
            'lastname'              => $_GET['lastname'     ] ?? null,
            'username'              => $_GET['username'     ] ?? null,
            'email'                 => $_GET['email'        ] ?? null,
            'department_id'         => $_GET['department_id'] ?? null,
            'role'                  => $_GET['role'         ] ?? null,
            'departments'           => self::department_options(),
            'roles'                 => self::role_options(),
            'actionLinks' => [
                ['url'   => parent::generateUri('users.add'),
                 'label' => parent::_('create_account'),
                 'class' => 'add'],
                ['url'   => $csv->__toString(),
                 'label' => 'csv',
                 'class' => 'download'],
            ]
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/users/findForm.twig', $this->vars);
    }

    private static function department_options(): array
    {
        $opts  = [['value'=>'']];
        $table = new DepartmentTable();
        foreach ($table->find() as $d) {
            $opts[] = ['value'=>$d->getId(), 'label'=>$d->getName()];
        }
        return $opts;
    }

    private static function role_options(): array
    {
        global $ACL;
        $opts = [['value'=>'']];
        foreach ($ACL->getRoles() as $r) {
            $opts[] = ['value'=>$r];
        }
        return $opts;
    }
}

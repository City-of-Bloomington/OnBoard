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

        $this->vars = [
            'users'         => $users,
            'total'         => $total,
            'itemsPerPage'  => $itemsPerPage,
            'currentPage'   => $currentPage,
            'search'        => $search,
            'departments'   => self::department_options(),
            'roles'         => self::role_options(),
            'actionLinks'   => self::actionLinks($search),
            'return_url'    => parent::generateUrl('users.index').'?'.http_build_query($search)
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/users/findForm.twig', $this->vars);
    }

    private static function actionLinks(array $search): array
    {
        $p   = array_merge($search, ['format'=>'csv']);
        $csv = parent::generateUri('users.index').'?'.http_build_query($p);

        return [
            ['url'   => parent::generateUri('users.add'),
             'label' => parent::_('create_account'),
             'class' => 'add'],
            ['url'   => $csv,
             'label' => 'csv',
             'class' => 'download'],
        ];
    }

    private static function department_options(): array
    {
        $o = [['value'=>'']];
        $t = new DepartmentTable();
        $l = $t->find();
        foreach ($l['rows'] as $d) { $o[] = ['value'=>$d->getId(), 'label'=>$d->getName()]; }
        return $o;
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

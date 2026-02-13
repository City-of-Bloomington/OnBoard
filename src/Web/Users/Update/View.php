<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Users\Update;

use Application\Models\Person;
use Application\Models\DepartmentTable;

class View extends \Web\View
{
    public function __construct(Person $user)
    {
        parent::__construct();

        $this->vars = [
            'user'        => $user,
            'departments' => self::department_options(),
            'roles'       => self::role_options()
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/users/updateForm.twig', $this->vars);
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

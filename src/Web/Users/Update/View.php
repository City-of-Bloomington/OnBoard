<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
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
            'user' => $user,
            'departments'           => self::department_options(),
            'roles'                 => self::role_options(),
            'authenticationMethods' => self::authentication_options(),
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/users/updateForm.twig', $this->vars);
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

    private static function authentication_options(): array
    {
        $opts = [['value'=>'']];
        foreach (Person::getAuthenticationMethods() as $m) {
            $opts[] = ['value'=>$m];
        }
        return $opts;
    }
}

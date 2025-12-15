<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Users\Add;

use Application\Models\Email;
use Application\Models\Person;
use Application\Models\PeopleTable;
use Web\Ldap;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $person = new Person();
        $ldap   = null;

        if (isset($_POST['username'])) {
            try {
                $ldap = new Ldap($_POST['username']);
                $p    = self::existingPerson($ldap);
                if ($p) { $person = $p; }
            }
            catch (\Exception $e) { }

            $person->handleUpdateUserAccount($_POST);
            if ($ldap) { self::populateFromLdap($person, $ldap); }

            try {
                $person->save();
                if (!empty($_POST['email'])) { $person->saveEmail($_POST['email']); }
                elseif ($ldap)               { $person->saveEmail($ldap->getEmail()); }

                header('Location: '.\Web\View::generateUrl('users.index'));
                exit();
            }
            catch (\Exception $e) {
                $_SESSION['errorMessages'][] = $e->getMessage();
            }
        }

        return new \Web\Users\Update\View($person);
    }

    private static function populateFromLdap(Person &$p, Ldap &$l)
    {
        if (!$p->getFirstname() && $l->getFirstname()) { $p->setFirstname($l->getFirstname()); }
        if (!$p->getLastname()  && $l->getLastname() ) { $p->setLastname ($l->getLastname() ); }
    }

    private static function existingPerson(Ldap $ldap): ?Person
    {
        $t = new PeopleTable();
        $r = $t->find(['username'=>$ldap->getUsername()]);
        if (count($r['rows'])) { return $r['rows'][0]; }

        $r = $t->find(['email'=>$ldap->getEmail()]);
        if (count($r['rows'])) { return $r['rows'][0]; }

        return null;
    }
}

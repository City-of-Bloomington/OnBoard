<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Laminas\Paginator $this->people
 */
declare (strict_types=1);
use Application\Models\Person;

$userCanViewContactInfo = Person::isAllowed('people', 'viewContactInfo');

$people = [];
foreach ($this->people as $p) {
    $person = [
        'id' => $p->getId(),
        'firstname' => $p->getFirstname(),
        'lastname' => $p->getLastname()
    ];
    if ($userCanViewContactInfo) {
        $person['email'] = $p->getEmail();
        $person['phone'] = $p->getPhone();
    }
    $people[] = $person;
}
echo json_encode($people, JSON_PRETTY_PRINT);

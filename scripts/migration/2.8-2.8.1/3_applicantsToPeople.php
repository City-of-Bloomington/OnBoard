<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
include '../../../src/Web/bootstrap.php';

use Application\Models\Email;
use Application\Models\Person;
use Application\Models\Phone;
use Web\Database;

$db  = Database::getConnection();
$pdo = $db->getDriver()->getConnection()->getResource();

echo "Delete applicants without any application or files\n";
$sql = "delete a
        from applicants          a
        left join applications  ap on a.id=ap.applicant_id
        left join applicantFiles f on a.id=f.applicant_id
        where ap.id is null
          and  f.id is null";
$pdo->query($sql);

echo "Associate applicants and people using email address\n";
$sql = "update people      p
        join people_emails e on e.person_id=p.id
        join applicants    a on e.email=a.email
        join applications ap on a.id=ap.applicant_id
        set ap.person_id=p.id";
$pdo->query($sql);

echo "Associate applicants and people using phone number\n";
$sql = "update applicants   a
        join people_phones pp on pp.number=a.phone
        join people         p on pp.person_id=p.id
        join applications  ap on ap.applicant_id=a.id
        set ap.person_id=p.id";
$pdo->query($sql);

echo "Associate applicants by firstname and lastname\n";
$sql = "update applicants  a
        join applications ap on ap.applicant_id=a.id
        join people        p on a.firstname=p.firstname and a.lastname=p.lastname
        set ap.person_id=p.id
        where ap.person_id is null";
$pdo->query($sql);

echo "Associate applicants by address\n";
$sql = "update applicants  a
        join applications ap on ap.applicant_id=a.id
        join people        p on a.address=p.address
        set ap.person_id=p.id
        where ap.person_id is null";
$pdo->query($sql);


/**
 * Any applicants left over have no match.
 * Create full people records for each applicant
 */
echo "Creating new person records\n";
$app = $pdo->prepare("update applications   set person_id=? where applicant_id=?");
$fil = $pdo->prepare("update applicantFiles set person_id=? where applicant_id=?");

$sql = "select distinct a.*
        from applicants a
        join applications ap on a.id=ap.applicant_id
        where ap.person_id is null
        order by a.lastname, a.firstname";
$result = $pdo->query($sql);
foreach ($result->fetchAll(\PDO::FETCH_ASSOC) as $row) {
    echo "$row[firstname] $row[lastname] $row[email] $row[phone]\n";
    $person = new Person([
        'firstname'  => $row['firstname' ],
        'lastname'   => $row['lastname'  ],
        'address'    => $row['address'   ],
        'city'       => $row['city'      ],
        'zip'        => $row['zip'       ],
        'citylimits' => $row['citylimits'],
        'occupation' => $row['occupation']
    ]);
    $person->save();

    $params = [$person->getId(), $row['id']];
    $app->execute($params);
    $fil->execute($params);

    if ($row['email']) { $person->saveEmail($row['email']); }
    if ($row['phone']) { $person->savePhone($row['phone']); }
}

echo "Update applicantFiles with person_id\n";
$sql = "update applicantFiles f
        set f.person_id=(
            select distinct(ap.person_id)
            from applicants a
            join applications ap on ap.applicant_id=a.id
            where a.id=f.applicant_id)
        where f.person_id is null";
$pdo->query($sql);

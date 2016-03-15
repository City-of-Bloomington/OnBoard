<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\CommitteeTable;
use Blossom\Classes\Url;

$table = new CommitteeTable();
$list  = $table->find();
$title = $this->_(['liaison', 'liaisons', count($list)]);

$helper = $this->template->getHelper('buttonLink');
$uri = new Url(Url::current_url());
$uri->format = 'csv';
$csvButton = $helper->buttonLink($uri, 'csv', 'download');
echo "
<section>
    <header>
        <h1>$title</h1>
        <div class=\"tools\">$csvButton</div>
    </header>
    <table class=\"fn1-table\">
";
    foreach ($list as $committee) {
        $committeeUri  = BASE_URI.'/committees/info?committee_id='.$committee->getId();
        $committeeName = self::escape($committee->getName());

        $departments = '';
        foreach ($committee->getDepartments() as $department) {
            $departments.= '<li>'.self::escape($department->getName()).'</li>';
        }
        if ($departments) {
            $departments = "<ul>$departments</ul>";
        }

        $liasons = $committee->getLiaisonPeople();

        if (count($liasons)) {
            foreach ($committee->getLiaisonPeople() as $person) {
                $name  = self::escape($person->getFullname());
                $email = self::escape($person->getEmail());
                $phone = self::escape($person->getPhone());

                $uri = BASE_URI.'/people/view?person_id='.$person->getId();
                echo "
                <tr><td><a href=\"$committeeUri\">$committeeName</a></td>
                    <td>$departments</td>
                    <td><a href=\"$uri\">$name</a></td>
                    <td>$email</td>
                    <td>$phone</td>
                </tr>
                ";
            }
        }
        else {
            echo "
            <tr><td><a href=\"$committeeUri\">$committeeName<a></td>
                <td>$departments</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            ";
        }
    }
echo "
    </table>
</section>
";
<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Zend\Db\ResultSet $this->committees
 */
namespace Application\Models;

use Application\Templates\Helpers\ButtonLink;

$userCanEdit = Person::isAllowed('committees', 'edit');
?>
<section     class="fn1-uiBlock">
    <header  class="fn1-uiBlock-header">
        <div class="fn1-uiBlock-headerTools">
        <?php
            $helper = $this->template->getHelper('buttonLink');
            if ($userCanEdit) {
                echo $helper->buttonLink(
                    BASE_URI.'/committees/update',
                    $this->_('committee_add'),
                    'add'
                );
            }
        ?>
        </div>
    </header>
    <table class="fn1-table">
        <thead>
            <tr><th></th>
                <th><?= $this->_(['seat', 'seats', 2]); ?></th>
                <th><?= $this->_(['vacancy', 'vacancies', 2]); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php
            foreach ($this->committees as $c) {
                $name = self::escape($c->getName());
                $url = BASE_URI.'/committees/members?committee_id='.$c->getId();

                $seatCount = count($c->getSeats());
                if ($seatCount === 0) { $seatCount = ''; }

                $vacancies = $c->getVacancyCount();
                if ($vacancies === 0) { $vacancies = ''; }

                echo "
                <tr><td><a href=\"$url\">$name</a></td>
                    <td>$seatCount</td>
                    <td>$vacancies</td>
                </tr>
                ";
            }
        ?>
        </tbody>
    </table>
</section>

<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array     $this->list      An array of Report objects
 * @param Committee $this->committee
 */
declare (strict_types=1);
use Application\Models\Person;
?>
<section>
    <header>
        <div class="tools">
        <?php
            $userCanEdit   = Person::isAllowed('reports', 'update');
            $userCanDelete = Person::isAllowed('reports', 'delete');
            if ($userCanEdit && $this->committee) {
                $helper = $this->template->getHelper('buttonLink');
                echo $helper->buttonLink(
                    BASE_URI.'/reports/update?committee_id='.$this->committee->getId(),
                    $this->_('report_add'),
                    'add'
                );
            }
        ?>
        </div>
    </header>
    <div>
        <table>
        <?php
            foreach ($this->list as $r) {
                $date  = $r->getReportDate(DATE_FORMAT);
                $title = parent::escape($r->getTitle());

                $editButton = '';
                if ($userCanEdit) {
                    $editButton = $helper->buttonLink(
                        BASE_URI.'/reports/update?id='.$r->getId(),
                        $this->_('report_edit'),
                        'edit'
                    );
                }

                $url   = BASE_URI.'/reports/download?id='.$r->getId();
                $title = "<a href=\"$url\">$title</a>";

                echo "
                <tr><th>$date</th>
                    <td>$title</td>
                    <td>$editButton</td>
                </tr>
                ";
            }
        ?>
        </table>
    </div>
</section>

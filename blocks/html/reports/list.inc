<?php
/**
 * @copyright 2017-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
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
                    parent::generateUri('reports.update').'?committee_id='.$this->committee->getId(),
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
            $helper = $this->template->getHelper('dropdown');
            foreach ($this->list as $r) {
                $id      = $r->getId();
                $date    = $r->getReportDate(DATE_FORMAT);
                $title   = parent::escape($r->getTitle());
                $actions = [];

                if ($userCanEdit) {
                    $actions[] = [
                        'url'   => parent::generateUri('reports.update')."?report_id=$id",
                        'label' => $this->_('report_edit')
                    ];
                }
                if ($userCanDelete) {
                    $actions[] = [
                        'url'   => parent::generateUri('reports.delete')."?report_id=$id",
                        'label' => $this->_('report_delete')
                    ];
                }
                $dropdown = $actions
                        ? $helper->dropdown($actions, $this->_('more'), "report_$id")
                        : '';

                $url   = parent::generateUri('reports.download')."?report_id=$id";
                $title = "<a href=\"$url\">$title</a>";

                echo "
                <tr><th>$date</th>
                    <td>$title</td>
                    <td>$dropdown</td>
                </tr>
                ";
            }
        ?>
        </table>
    </div>
</section>

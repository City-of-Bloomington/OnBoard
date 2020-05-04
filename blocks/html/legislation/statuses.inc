<?php
/**
 * @copyright 2017-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param array $this->statuses
 */
declare (strict_types=1);
use Application\Models\Person;
?>
<section>
    <header>
        <h1><?= $this->_(['legislationStatus', 'legislationStatuses', count($this->statuses)]); ?></h1>
        <div class="tools">
        <?php
            $userCanEdit   = Person::isAllowed('legislationStatuses', 'update');
            $userCanDelete = Person::isAllowed('legislationStatuses', 'delete');
            if ($userCanEdit) {
                $helper = $this->template->getHelper('buttonLink');
                echo $helper->buttonLink(
                    parent::generateUri('legislationStatuses.update'),
                    $this->_('legislationStatus_add'),
                    'add'
                );
            }
        ?>
        </div>
    </header>
    <table>
        <thead>
            <tr><th></th>
                <th><?= $this->_('status'); ?></th>
                <th><?= $this->_('active'); ?></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
    <?php
        foreach ($this->statuses as $s) {
            $status_id    = $s->getId();
            $editButton   = '';
            $deleteButton = '';
            if ($userCanEdit) {
                $editButton = $helper->buttonLink(
                    parent::generateUri('legislationStatuses.update')."?id=$status_id",
                    $this->_('legislationStatus_edit'),
                    'edit'
                );
            }
            if ($userCanDelete) {
                $deleteButton = $helper->buttonLink(
                    parent::generateUri('legislationStatuses.delete')."?id=$status_id",
                    $this->_('legislationStatus_delete'),
                    'delete'
                );
            }
            $name   = parent::escape($s->getName());
            $active = $s->getActive();
            echo "
            <tr><td>$editButton</td>
                <td>$name</td>
                <td>$active</td>
                <td>$deleteButton</td>
            </tr>
            ";
        }
    ?>
        </tbody>
    </table>
</section>

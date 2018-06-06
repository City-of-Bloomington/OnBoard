<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
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
                    BASE_URI.'/legislationStatuses/update',
                    $this->_('legislationStatus_add'),
                    'add'
                );
            }
        ?>
        </div>
    </header>
    <table>
    <?php
        foreach ($this->statuses as $s) {
            $editButton   = '';
            $deleteButton = '';
            if ($userCanEdit) {
                $editButton = $helper->buttonLink(
                    BASE_URI.'/legislationStatuses/update?id='.$s->getId(),
                    $this->_('legislationStatus_edit'),
                    'edit'
                );
            }
            if ($userCanDelete) {
                $deleteButton = $helper->buttonLink(
                    BASE_URI.'/legislationStatuses/delete?id='.$s->getId(),
                    $this->_('legislationStatus_delete'),
                    'delete'
                );
            }
            $name = parent::escape($s->getName());
            echo "
            <tr><td>$editButton</td>
                <td>$name</td>
                <td>$deleteButton</td>
            </tr>
            ";
        }
    ?>
    </table>
</section>

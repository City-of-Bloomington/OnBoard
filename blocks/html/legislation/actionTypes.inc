<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array $this->types An arra of Legislation Action Type objects
 */
use Application\Models\Person;
?>
<section>
    <header>
        <h1><?= $this->_(['legislationActionType', 'legislationActionTypes', count($this->types)]); ?></h1>
        <div class="tools">
        <?php
            $userCanEdit = Person::isAllowed('legislationActionTypes', 'update');
            $h = $this->template->getHelper('buttonLink');
            if ($userCanEdit) {
                echo $h->buttonLink(
                    BASE_URI.'/legislationActionTypes/update',
                    $this->_('legislationActionType_add'),
                    'add'
                );
            }
        ?>
        </div>
    </header>
    <table>
    <?php
        $editButton = '';
        foreach ($this->types as $t) {
            if ($userCanEdit) {
                $editButton = $h->buttonLink(
                    BASE_URI.'/legislationActionTypes/update?id='.$t->getId(),
                    $this->_('legislationActionType_edit'),
                    'edit'
                );
            }
            $name     = parent::escape($t->getName());
            $ordering = $t->getOrdering();

            echo "
            <tr><td>$editButton</td>
                <td>$name</td>
                <td>$ordering</td>
            </tr>
            ";
        }
    ?>
    </table>
</section>

<?php
/**
 * @copyright 2017-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param array $this->types An arra of Legislation Type objects
 */
declare (strict_types=1);
use Application\Models\Person;
?>
<section>
    <header>
        <h1><?= $this->_(['legislationType', 'legislationTypes', count($this->types)]); ?></h1>
        <div class="tools">
        <?php
            $userCanEdit = Person::isAllowed('legislationTypes', 'update');
            $h = $this->template->getHelper('buttonLink');
            if ($userCanEdit) {
                echo $h->buttonLink(
                    parent::generateUri('legislationTypes.update'),
                    $this->_('legislationType_add'),
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
                    parent::generateUri('legislationTypes.update').'?id='.$t->getId(),
                    $this->_('legislationType_edit'),
                    'edit'
                );
            }
            $name    = parent::escape($t->getName());
            $subtype = $t->isSubtype() ? $this->_('subtype') : '';

            echo "
            <tr><td>$editButton</td>
                <td>$name</td>
                <td>$subtype</td>
            </tr>
            ";
        }
    ?>
    </table>
</section>

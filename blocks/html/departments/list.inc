<?php
/**
 * @copyright 2016-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Zend\Db\Result $this->departments
 * @param boolean        $this->disableButtons
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;

$helper = $this->template->getHelper('buttonLink');
?>
<section>
    <header>
        <h2><?= $this->_(['department', 'departments', count($this->departments)]); ?></h2>
        <div class="tools">
        <?php
            $userCanEdit = (!$this->disableButtons && Person::isAllowed('departments', 'update'));
            if ($userCanEdit) {
                echo $helper->buttonLink(
                    BASE_URI.'/departments/update',
                    $this->_('department_add'),
                    'add'
                );
            }
        ?>
        </div>
    </header>

    <table>
    <?php
        $editButton = '';
        foreach ($this->departments as $d) {
            $name = self::escape($d->getName());

            if ($userCanEdit) {
                $editButton = $helper->buttonLink(
                    BASE_URI.'/departments/update?department_id='.$d->getId(),
                    $this->_('department_edit'),
                    'edit'
                );
            }

            echo "
            <tr><td>$editButton</td>
                <td>$name</td>
            </tr>
            ";
        }
    ?>
    </table>
</section>

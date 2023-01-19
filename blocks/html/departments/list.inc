<?php
/**
 * @copyright 2016-2023 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Laminas\Db\Result $this->departments
 * @param boolean           $this->disableButtons
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
                    parent::generateUri('departments.update'),
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
            $url  = parent::generateUri('departments.view').'?department_id='.$d->getId();
            $name = self::escape($d->getName());

            if ($userCanEdit) {
                $editButton = $helper->buttonLink(
                    parent::generateUri('departments.update').'?department_id='.$d->getId(),
                    $this->_('department_edit'),
                    'edit'
                );
            }

            echo "
            <tr><td>$editButton</td>
                <td><a href=\"$url\">$name</a></td>
            </tr>
            ";
        }
    ?>
    </table>
</section>

<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Zend\Db\Result $this->departments
 */
use Application\Models\Person;

$helper = $this->template->getHelper('buttonLink');
?>
<section     class="fn1-uiBlock">
    <header  class="fn1-uiBlock-header"><h1><?= $this->_(['department', 'departments', count($this->departments)]); ?></h1>
        <div class="fn1-uiBlock-headerTools">
        <?php
            if (Person::isAllowed('departments', 'add')) {
                echo $helper->buttonLink(
                    BASE_URI.'/departments/update',
                    $this->_('department_add'),
                    'add'
                );
            }
        ?>
        </div>
    </header>

    <table class="fn1-table">
    <?php
        foreach ($this->departments as $d) {
            $name  = self::escape($d->getName());
            $email = self::escape($d->getEmail());
            $phone = self::escape($d->getPhone());

            if (Person::isAllowed('departments', 'edit')) {
                $editButton = $helper->buttonLink(
                    BASE_URI.'/departments/update?department_id='.$d->getId(),
                    $this->_('department_edit'),
                    'edit'
                );
            }

            echo "
            <tr><td>$name</td>
                <td>$email</td>
                <td>$phone</td>
            </tr>
            ";
        }
    ?>
    </table>
</section>
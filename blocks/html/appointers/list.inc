<?php
/**
 * @copyright 2009-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Zend\Db\ResultSet $this->appointers
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
use Web\View;
?>
<section>
    <header>
        <h1><?=$this->translate(['appointer','appointers',2]); ?></h1>
        <div class="tools">
        <?php
            $helper = $this->template->getHelper('buttonLink');
            if (Person::isAllowed('appointers')) {
                echo $helper->buttonLink(
                    BASE_URI.'/appointers/update',
                    $this->translate('add_appointer'),
                    'add'
                );
            }
        ?>
        </div>
    </header>
    <table>
    <?php
        foreach ($this->appointers as $appointer) {
            $editButton = '';
            if (Person::isAllowed('appointers')) {
                $editButton = $helper->buttonLink(
                    BASE_URI.'/appointers/update?appointer_id='.$appointer->getId(),
                    $this->translate('edit'),
                    'edit'
                );
            }

            $name = View::escape($appointer->getName());
            echo "
            <tr><td>$editButton</td>
                <td>$name</td>
            </tr>
            ";
        }
    ?>
    </table>
</section>

<?php
/**
 * @copyright 2009-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Zend\Db\ResultSet $this->races
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
?>
<section>
    <header>
        <h1><?= $this->translate(['race','races', count($this->races)]); ?></h1>
        <div class="tools">
        <?php
            $helper = $this->template->getHelper('buttonLink');
            if (Person::isAllowed('races', 'add')) {
                echo $helper->buttonLink(
                    BASE_URI.'/races/update',
                    $this->translate('race_add'),
                    'add'
                );
            }
        ?>
        </div>
    </header>
	<table>
    <?php
        foreach ($this->races as $race) {
            $editButton = '';
            if (Person::isAllowed('races', 'edit')) {
                $editButton = $helper->buttonLink(
                    BASE_URI.'/races/update?race_id='.$race->getId(),
                    $this->translate('race_edit'),
                    'edit'
                );
            }

            $name = parent::escape($race->getName());
            echo "
            <tr><td>$editButton</td>
                <td>$name</td>
            </tr>
            ";
        }
    ?>
	</table>
</section>

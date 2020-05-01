<?php
/**
 * @copyright 2009-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Laminas\Db\ResultSet $this->races
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
?>
<section>
    <header>
        <h1><?= $this->translate(['race','races', count($this->races)]); ?></h1>
        <div class="tools">
        <?php
            $helper      = $this->template->getHelper('buttonLink');
            $userCanEdit = Person::isAllowed('races', 'update');
            if ($userCanEdit) {
                echo $helper->buttonLink(
                    parent::generateUri('races.update'),
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
            if ($userCanEdit) {
                $editButton = $helper->buttonLink(
                    parent::generateUri('races.update').'?race_id='.$race->getId(),
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

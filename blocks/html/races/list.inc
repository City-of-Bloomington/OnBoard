<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Zend\Db\ResultSet $this->races
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
use Blossom\Classes\View;
?>
<section     class="fn1-uiBlock">
    <header  class="fn1-uiBlock-header"><h1><?= $this->translate(['race','races',2]); ?></h1>
        <div class="fn1-uiBlock-headerTools">
        <?php
            if (Person::isAllowed('races')) {
                $helper = $this->template->getHelper('buttonLink');
                echo $helper->buttonLink(
                    BASE_URI.'/races/update',
                    $this->translate('add_race'),
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
				if (Person::isAllowed('races')) {
					$editButton = $helper->buttonLink(
						BASE_URI.'/races/update?race_id='.$race->getId(),
						$this->translate('edit'),
						'edit',
						ButtonLink::SIZE_ICON
					);
				}

				$name = View::escape($race->getName());
				echo "
				<tr><td>$editButton</td>
					<td>$name</td>
				</tr>
				";
			}
		?>
	</table>
</section>
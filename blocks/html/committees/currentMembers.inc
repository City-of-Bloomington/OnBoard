<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Committee $this->committee
 */
use Blossom\Classes\Url;
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
?>
<div>
	<h2><?= $this->_(['current_member', 'current_members', 2]); ?></h2>
	<table>
		<thead>
			<tr><th><?= $this->_('name'); ?></th>
				<th><?= $this->_('appointed_by'); ?></th>
				<th colspan="2"><?= $this->_(['term','term', 1]); ?></th>
				<th colspan="2"><?= $this->_(['office','offices', 2]); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
			$currentUrl = new Url($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
			$helper = $this->template->getHelper('buttonLink');

            $committee_id = $this->committee->getId();

            $userCanEditTerms   = Person::isAllowed('terms',   'edit');
            $userCanEditOffices = Person::isAllowed('offices', 'edit');

            foreach ($this->committee->getCurrentTerms() as $term) {
                $editTermButton = '';
                $addTermButton  = '';
                if ($userCanEditTerms) {
                    $editTermButton = $helper->buttonLink(
                        BASE_URI."/terms/update?term_id={$term->getId()};return_url=$currentUrl",
                        $this->translate('edit'),
                        'edit',
                        ButtonLink::SIZE_ICON
                    );
                }

                $addOfficeButton  = '';
                $editOfficeButton = '';
                if ($userCanEditOffices) {
                    $addOfficeButton = $helper->buttonLink(
                        BASE_URI."/offices/update?committee_id={$this->committee->getId()};person_id={$term->getPerson_id()}",
                        $this->translate('add_office'),
                        'add'
                    );
                }

                $offices = array();
                foreach ($term->getPerson()->getOffices($this->committee, date('Y-m-d')) as $office) {
                    $editOfficeButton = '';
                    if ($userCanEditOffices) {
                        $editOfficeButton = $helper->buttonLink(
                            BASE_URI.'/offices/update?office_id='.$office->getId(),
                            $this->translate('edit_office'),
                            'edit'
                        );
                    }
                    $offices[] = self::escape($office->getTitle());
                }
                $offices = implode(',',$offices);

                $appointer = self::escape($term->getAppointer());
                $fullname  = self::escape($term->getPerson()->getFullname());
                echo "
                <tr><td><a href=\"{$term->getPerson()->getURL()}\">$fullname</a></td>
                    <td>$appointer</td>
                    <td>{$term->getTerm_start(DATE_FORMAT)} - {$term->getTerm_end(DATE_FORMAT)}
                        $editTermButton
                    </td>
                    <td>$addTermButton</td>
                    <td>$offices</td>
                    <td>$addOfficeButton $editOfficeButton</td>
                </tr>
                ";
            }

            foreach ($this->committee->getSeats(time()) as $seat) {

                //--------------------------------------------------------
                // Show all the vacancies
                //--------------------------------------------------------
                $numOpenTerms = $seat->getMaxCurrentTerms() - count($seat->getCurrentTerms());
                if ($numOpenTerms > 0) {
                    $appointer = self::escape($seat->getAppointer());

                    $addTermButton = '';
                    if ($userCanEditTerms) {
                        $addTermButton = $helper->buttonLink(
                            BASE_URI."/terms/update?seat_id={$seat->getId()};return_url=$currentUrl",
                            $this->translate('add_term'),
                            'add'
                        );
                    }
                    for ($i=0; $i<$numOpenTerms; $i++) {
                        echo "
                        <tr><td>( {$this->_('vacancy')} )</td>
                            <td>$appointer</td>
                            <td>$addTermButton</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        ";
                    }
                }
            }
		?>
		</tbody>
	</table>
    <?php
        if ($userCanEditTerms && $this->committee->getType() === 'open') {
            echo '<div>';
            echo $helper->buttonLink(
                BASE_URI."/terms/update?committee_id=$committee_id;return_url=$currentUrl",
                $this->_('add_term'),
                'add'
            );
            echo '</div>';
        }
        
        echo "
        <div>
            <a href=\"{$this->committee->getUri()};members=past\">
                {$this->_('view_past_members')}
            </a>
        </div>
        ";
    ?>
</div>

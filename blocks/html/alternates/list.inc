<?php
/**
 * @copyright 2022 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Laminas\Db\Result $this->alternates
 */
declare (strict_types=1);
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
use Web\Block;
?>
<section class="alternatesList">
    <table>
    <?php
        $actions = new Block('alternates/partials/actions.inc');

        foreach ($this->alternates as $alternate) {
            $alternate_id = $alternate->getId();
            $person_id    = $alternate->getPerson_id();
            $name         = self::escape($alternate->getPerson()->getFullname());

            $moreLinks = '';
            if (!$this->disableButtons) {
                $actions->alternate = $alternate;
                $moreLinks          = $actions->render('html', $this->template);
            }
            $uri = parent::generateUri('people.view')."?person_id=$person_id";
            echo "
            <tr><td><a href=\"$uri\">$name</a></td>
                <td>{$alternate->getStartDate(DATE_FORMAT)} - {$alternate->getEndDate(DATE_FORMAT)}</td>
                <td>$moreLinks</td>
            </tr>
            ";
        }
    ?>
    </table>
    <?php
        $helper = $this->template->getHelper('buttonLink');
        if (!$this->disableButtons && Person::isAllowed('alternates', 'update')) {
            $uri = null;
            if ($this->term) {
                $term_id = $this->term->getId();
                $uri     = parent::generateUri('alternates.update')."?term_id=$term_id";
                echo $helper->buttonLink($uri, $this->_('alternate_add'), 'add');
            }
            elseif ($this->seat && $this->seat->getType() === 'open') {
                $seat_id = $this->seat->getId();
                $uri     = parent::generateUri('alternates.update')."?seat_id=$seat_id".$this->seat->getId();
                echo $helper->buttonLink($uri, $this->_('alternate_add'), 'add');
            }
            elseif ($this->committee) {
                $committee_id = $this->committee->getId();
                $uri          = parent::generateUri('alternates.update')."?committee_id=$committee_id";
                echo $helper->buttonLink($uri, $this->_('alternate_add'), 'add');
            }
        }
    ?>
</section>

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
use Web\Url;
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
        if (!$this->disableButtons && Person::isAllowed('alternates', 'update')) {
            $p = ['return_url' => Url::current_url(BASE_HOST)];
            if ($this->term) {
                $p['term_id'] = $this->term->getId();
                $uri = parent::generateUri('alternates.update').'?'.http_build_query($p, '', ';');
                echo "<a href=\"$uri\" class=\"add button\">{$this->_('alternate_add')}</a>";
            }
            elseif ($this->seat && $this->seat->getType() === 'open') {
                $p['seat_id'] = $this->seat->getId();
                $uri = parent::generateUri('alternates.update').'?'.http_build_query($p, '', ';');
                echo "<a href=\"$uri\" class=\"add button\">{$this->_('alternate_add')}</a>";
            }
            elseif ($this->committee) {
                $p['committee_id'] = $this->committee->getId();
                $uri = parent::generateUri('alternates.update').'?'.http_build_query($p, '', ';');
                echo "<a href=\"$uri\" class=\"add button\">{$this->_('alternate_add')}</a>";
            }
        }
    ?>
</section>

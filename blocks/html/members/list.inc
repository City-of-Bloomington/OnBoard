<?php
/**
 * @copyright 2016-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Laminas\Db\Result $this->members
 *
 * @param Committee $this->committee      (optional)
 * @param Seat      $this->seat           (optional)
 * @param string    $this->title          (optional)
 * @param boolean   $this->disableButtons (optional)
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
use Web\Block;
?>
<section class="membersList">
    <?php
        if ($this->title) { echo "<header><h1>{$this->title}</h1></header>"; }
    ?>
    <table>
    <?php
        $actions = new Block('members/partials/actions.inc');

        foreach ($this->members as $member) {
            $member_id = $member->getId();
            $person_id = $member->getPerson_id();
            $name      = self::escape($member->getPerson()->getFullname());

            $titles  = [];
            $offices = $member->getPerson()->getOffices($member->getCommittee(), date('Y-m-d'));
            foreach ($offices as $o) { $titles[] = self::escape($o->getTitle()); }
            $titles  = count($titles)
                     ? ' <span class="offices">'.implode(', ', $titles).'</span>'
                     : '';

            $moreLinks = '';
            if (!$this->disableButtons) {
                $actions->member  = $member;
                $actions->offices = $offices;
                $moreLinks        = $actions->render('html', $this->template);
            }
            $uri = parent::generateUri('people.view')."?person_id=$person_id";
            echo "
            <tr><td><a href=\"$uri\">$name</a>$titles</td>
                <td>{$member->getStartDate(DATE_FORMAT)} - {$member->getEndDate(DATE_FORMAT)}</td>
                <td>$moreLinks</td>
            </tr>
            ";
        }
    ?>
    </table>
    <?php
        $helper = $this->template->getHelper('buttonLink');

        if (!$this->disableButtons) {
            $uri = null;
            $userCanUpdate  = Person::isAllowed('members', 'update' );
            $userCanAppoint = Person::isAllowed('members', 'appoint');
            if ($this->term) {
                $term_id = $this->term->getId();
                if ($userCanUpdate) {
                    echo $helper->buttonLink(
                        parent::generateUri('members.update')."?term_id=$term_id",
                        $this->_('member_add'),
                        'add'
                    );
                }
            }
            elseif ($this->seat && $this->seat->getType() === 'open') {
                $seat_id = $this->seat->getId();
                if ($userCanUpdate) {
                    $uri = parent::generateUri('members.update')."?seat_id=$seat_id";
                }
                elseif ($userCanAppoint) {
                    $uri = parent::generateUri('members.appoint')."?seat_id=$seat_id";
                }

                if (isset($uri)) {
                    echo $helper->buttonLink($uri, $this->_('member_add'), 'add');
                }
            }
            elseif ($this->committee) {
                $committee_id = $this->committee->getId();
                if ($userCanUpdate) {
                    $uri = parent::generateUri('members.update')."?committee_id=$committee_id";
                }
                elseif ($userCanAppoint) {
                    $uri = parent::generateUri('members.appoint')."?committee_id=$committee_id";
                }

                if (isset($uri)) {
                    echo $helper->buttonLink($uri, $this->_('member_add'), 'add');
                }

                if ($this->committee->hasVacancy()) {
                    echo $helper->buttonLink(
                        parent::generateUri('applicants.apply')."?committee_id=$committee_id",
                        $this->_('apply'),
                        'apply'
                    );
                }
            }
        }
    ?>
</section>

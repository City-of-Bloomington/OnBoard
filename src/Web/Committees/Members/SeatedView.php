<?php
/**
 * A view of commiitee members based on Seats and Terms
 *
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Members;

use Application\Models\Committee;
use Application\Models\Term;
use Web\View;

class SeatedView extends View
{
    public function __construct(Committee $committee, array $seat_data)
    {
        parent::__construct();

        $url = parent::generateUri('committees.members', ['committee_id'=>$committee->getId()]);
        $links = [
            'download' => [
                'url'   => $url.'?format=csv',
                'label' => 'CSV Export',
                'class' => 'download'
            ]
        ];

        if (isset($_SESSION['USER'])) {
            $this->createActionLinks($committee, $seat_data);
        }

        $this->vars = [
            'committee'   => $committee,
            'seat_data'   => $seat_data,
            'actionLinks' => $links,
            'search'      => ['current'=>true] // Needed for side-nav partial
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/committees/seated_members.twig', $this->vars);
    }

    /**
     * Adds an actionLinks array to each row of seat_data
     *
     * Does permission checks for all possible actions for each row of seat_data
     */
    private function createActionLinks(Committee $committee, array &$seat_data)
    {
        $userCanEditAlternates = parent::isAllowed('alternates', 'update');
        $userCanAppointMembers = parent::isAllowed('members',    'appoint');
        $userCanEditOffices    = parent::isAllowed('offices',    'update');
        $alternates            = $committee->allowsAlternates();

        foreach ($seat_data as $i=>$row) {
            $actions = [];

            if ($alternates && $userCanEditAlternates) {
                if ($row['alternate_person_id']) {
                    $actions[] = [
                        'url'   => parent::generateUri('alternates.update', ['alternate_id'=>$row['alternate_id']]),
                        'label' => $this->_('alternate_edit'),
                        'class' => 'edit'
                    ];
                }
                else {
                    $actions[] = [
                        'url'   => parent::generateUri('alternates.add'),
                        'label' => $this->_('alternate_add'),
                        'class' => 'add'
                    ];
                }
            }
            if ($userCanAppointMembers) {
                if ($row['member_person_id']) {
                    if (!empty($row['term_id'])) {
                        $uri = $row['seat_type'] === 'termed'
                                ? parent::generateUri('members.appoint')."?term_id=$row[term_id]"
                                : parent::generateUri('members.appoint')."?seat_id=$row[seat_id]";
                        $actions[] = ['url'=>$uri, 'label'=>$this->_('member_add')];

                        if ($row['seat_type'] == 'termed') {
                            $t = new Term($row['term_id']);
                            $n = $t->getNextTerm();

                            if ($n->isVacant()) {
                                $uri = parent::generateUri('members.reappoint', ['member_id'=>$row['member_id']]);
                                $actions[] = ['url'=>$uri, 'label'=>$this->_('member_continue')];
                            }
                        }
                    }

                    if (!$row['member_endDate'] || strtotime($row['member_endDate']) > time()) {
                        $uri = parent::generateUri('members.resign', ['member_id'=>$row['member_id']]);
                        $actions[] = ['url'=>$uri, 'label'=>$this->_('member_end')];
                    }
                }
                else {
                    if (!empty($row['term_id'])) {
                        $uri = $row['seat_type'] === 'termed'
                                ? parent::generateUri('members.appoint')."?term_id=$row[term_id]"
                                : parent::generateUri('members.appoint')."?seat_id=$row[seat_id]";
                        $actions[] = ['url'=>$uri, 'label'=>$this->_('member_add')];
                    }
                }
            }
            if ($userCanEditOffices && $row['member_person_id']) {
                $actions[] = [
                    'url'   => parent::generateUri('offices.add')."?committee_id={$committee->getId()};person_id=$row[member_person_id]",
                    'label' => $this->_('office_add'),
                    'class' => 'add'
                ];

                if ($row['offices']) {
                    foreach (explode(',',$row['offices']) as $o) {
                        list($office_id, $office_title) = explode('|', $o);
                        $actions[] = [
                            'url'   => parent::generateUri('offices.update', ['office_id'=>$office_id]),
                            'label' => "{$this->_('edit')} $office_title",
                            'class' => 'add'
                        ];
                    }
                }

            }

            $seat_data[$i]['actionLinks'] = $actions;
        }
    }
}

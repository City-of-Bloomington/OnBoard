<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Members\Appoint;

use Application\Models\ApplicationTable;
use Application\Models\Committee;
use Application\Models\Member;
use Application\Models\Seat;

class View extends \Web\View
{
    public function __construct(Member $member, string $return_url)
    {
        parent::__construct();

        $committee = $member->getCommittee();
        $seat      = $member->getSeat();

        $this->vars = [
            'committee'     => $committee,
            'member'        => $member,
            'requirements'  => $seat ? $seat->getRequirements()   : null,
            'applications'  => self::applications($committee),
            'return_url'    => $return_url
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/members/updateForm.twig', $this->vars);
    }

    public static function applications(Committee $c): array
    {
        $data = [];

        if (parent::isAllowed('applications', 'view')) {
            $tab  = new ApplicationTable();
            $apps = $tab->find(['current'=>time(), 'committee_id'=>$c->getId()], 'created desc');
            foreach ($apps['rows'] as $a) {
                $data[] = [
                    'id'           => $a->getId(),
                    'person_id'    => $a->getPerson_id(),
                    'person'       => $a->getPerson()->getFullname(),
                    'created'      => $a->getCreated(DATE_FORMAT)
                ];
            }
        }

        return $data;
    }
}

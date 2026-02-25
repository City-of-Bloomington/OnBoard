<?php
/**
 * @copyright 2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Meetings\Notice;

use Application\Models\Meeting;

class View extends \Web\View
{
    public function __construct(Meeting $m)
    {
        parent::__construct();

        $this->vars = [
            'meeting'     => $m,
            'committee'   => $m->getCommittee(),
            'attendance'  => $m->getAttendance(),
            'breadcrumbs' => self::breadcrumbs($m)
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/meetings/notice.twig', $this->vars);
    }

    private static function breadcrumbs(Meeting $m): array
    {
        $committee_id = $m->getCommittee_id();
        $committee    = $m->getCommittee()->getName();
        $meetings     = parent::_(['meeting', 'meetings', 10]);
        return [
            $committee  => parent::generateUri('committees.info',     ['committee_id'=>$committee_id]),
            $meetings   => parent::generateUri('committees.meetings', ['committee_id'=>$committee_id]),
            $m->getStart('F j, Y g:ia') => parent::generateUri('meetings.view', ['meeting_id'=>$m->getId()]),
            parent::_('notice') => null
        ];
    }
}

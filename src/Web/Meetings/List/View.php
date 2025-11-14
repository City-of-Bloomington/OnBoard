<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Meetings\List;

use Application\Models\Committee;
use Application\Models\CommitteeTable;
use Application\Models\MeetingFile;
use Application\Models\MeetingTable;
use Application\Models\Notifications\DefinitionTable;

class View extends \Web\View
{
    public function __construct(array     $meetings,
                                array     $search,
                                string    $sort,
                                int   $totalItemCount,
                                int   $currentPage,
                                int   $itemsPerPage,
                                ?Committee $committee)
    {
        parent::__construct();

        $this->vars = [
            'meetings'     => $meetings,
            'committee'    => $committee,
            'search'       => $search,
            'committees'   => self::committees(),
            'fileTypes'    => self::fileTypes(),
            'years'        => self::years($search),
            'sorts'        => self::sorts(),
            'total'        => $totalItemCount,
            'itemsPerPage' => $itemsPerPage,
            'currentPage'  => $currentPage,
            'sort'         => $sort,
            'actionLinks'  => $committee ? self::actionLinks($committee, $search) : []
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/meetings/list.twig', $this->vars);
    }

    private static function actionLinks(Committee $c, array $search)
    {
        $url   = parent::generateUrl('committees.meetings', ['committee_id'=>$c->getId()]);
        $ret   = $url.'?'.http_build_query($search);
        $event = DefinitionTable::MEETINGFILE_NOTICE;
        $links = \Web\Notifications\View::actionLinksForSubscriptions($event, $c->getId(), $ret);

        if (parent::isAllowed('committees', 'meetingsync')) {
            $links[] = [
                'url'   => parent::generateUri('committees.meetingsync', ['committee_id'=>$c->getId()]),
                'label' => _('sync'),
                'class' => 'autorenew'
            ];
        }

        return $links;
    }

    /**
     * Returns an array of options in the format expected by the forms macros
     *
     * @see templates/html/macros/forms.twig
     */
    private static function years(array $search): array
    {
        $q     = !empty($search['committee_id']) ? ['committee_id'=>$search['committee_id']] : [];
        $table = new MeetingTable();
        $years = array_keys($table->years($q));

        if (!in_array($search['year'], $years)) { array_unshift($years, $search['year']); }

        $options = [];
        foreach ($years as $v) { $options[] = ['value' => $v]; }
        return $options;
    }

    /**
     * Returns an array of options in the format expected by the forms macros
     *
     * @see templates/html/macros/forms.twig
     */
    private static function fileTypes(): array
    {
        $o = [['value'=>'']];
        foreach (MeetingFile::$types as $t) { $o[] = ['value'=>$t]; }
        return $o;
    }

    /**
     * Returns an array of options in the format expected by the forms macros
     *
     * @see templates/html/macros/forms.twig
     */
    private static function sorts(): array
    {
        return [
            ['value'=>'asc',  'label'=>parent::translate('sort_date_asc' )],
            ['value'=>'desc', 'label'=>parent::translate('sort_date_desc')]
        ];
    }

    /**
     * Returns an array of options in the format expected by the forms macros
     *
     * @see templates/html/macros/forms.twig
     */
    private static function committees(): array
    {
        $o = [['value'=>'']];
        $t = new CommitteeTable();
        $l = $t->find();
        foreach ($l as $c) { $o[] = ['value'=>$c->getId(), 'label'=>$c->getName()]; }
        return $o;
    }
}

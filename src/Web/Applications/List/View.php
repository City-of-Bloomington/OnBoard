<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Applications\List;

use Application\Models\CommitteeTable;

class View extends \Web\View
{
    public function __construct(array $applications, array $search, int $total, int $itemsPerPage, int $currentPage)
    {
        parent::__construct();

        $status = !empty($search['current']) ? 'current' : (!empty($search['archived']) ? 'archived' : null);

        $this->vars = [
            'applications' => $applications,
            'firstname'    => $search['firstname'   ] ?? '',
            'lastname'     => $search['lastname'    ] ?? '',
            'email'        => $search['email'       ] ?? '',
            'committee_id' => $search['committee_id'] ?? null,
            'status'       => $status,
            'total'        => $total,
            'itemsPerPage' => $itemsPerPage,
            'currentPage'  => $currentPage,
            'committees'   => self::committees(),
            'statuses'     => self::statuses()
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/applications/find.twig', $this->vars);
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

    /**
     * Returns an array of options in the format expected by the forms macros
     *
     * @see templates/html/macros/forms.twig
     */
    private static function statuses(): array
    {
        return [
            ['value'=>'current' ],
            ['value'=>'archived']
        ];
    }
}

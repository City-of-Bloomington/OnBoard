<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Applicants\List;

use Application\Models\CommitteeTable;

class View extends \Web\View
{
    public function __construct(array $applicants, array $search, int $total, int $itemsPerPage, int $currentPage)
    {
        parent::__construct();

        $this->vars = [
            'applicants'   => self::applicant_data($applicants),
            'total'        => $total,
            'itemsPerPage' => $itemsPerPage,
            'currentPage'  => $currentPage,
            'search'       => $search,
            'committees'   => self::committees()
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/applicants/list.twig', $this->vars);
    }

    private static function applicant_data(array $applicants): array
    {
        $out = [];
        foreach ($applicants as $a) {
            $out[] = [
                'id'    => $a->getId(),
                'name'  => "{$a->getFirstname()} {$a->getLastname()}",
                'email' => $a->getEmail(),
                'phone' => $a->getPhone()
            ];
        }
        return $out;
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

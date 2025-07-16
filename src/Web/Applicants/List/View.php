<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Applicants\List;

class View extends \Web\View
{
    public function __construct(array $applicants, array $search, int $total, int $itemsPerPage, int $currentPage)
    {
        parent::__construct();

        $this->vars = [
            'applicants'  => self::applicant_data($applicants),
            'total'       => $total,
            'itemsPerPage'=> $itemsPerPage,
            'currentPage' => $currentPage,
            'firstname'   => $search['firstname'] ?? '',
            'lastname'    => $search['lastname' ] ?? '',
            'email'       => $search['email'    ] ?? '',
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
}

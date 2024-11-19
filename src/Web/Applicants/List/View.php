<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Applicants\List;

class View extends \Web\View
{
    public function __construct(array $applicants)
    {
        parent::__construct();

        $this->vars = [
            'applicants' => $this->applicant_data($applicants)
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/applicants/list.twig', $this->vars);
    }

    private function applicant_data(array $applicants): array
    {
        $canDelete = parent::isAllowed('applicants', 'delete');

        $out = [];
        foreach ($applicants as $a) {
            $links = [];
            if ($canDelete) {
                $links[] = [
                    'url'   => parent::generateUri('applicants.delete', ['id'=>$a->getId()]),
                    'label' => $this->_('applicant_delete'),
                    'class' => 'delete'
                ];
            }
            $out[] = [
                'id'          => $a->getId(),
                'name'        => "{$a->getFirstname()} {$a->getLastname()}",
                'email'       => $a->getEmail(),
                'phone'       => $a->getPhone(),
                'actionLinks' => $links
            ];
        }
        return $out;
    }
}

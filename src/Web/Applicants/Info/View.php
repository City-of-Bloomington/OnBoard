<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Applicants\Info;

use Application\Models\Applicant;

class View extends \Web\View
{
    public function __construct(Applicant $applicant)
    {
        parent::__construct();

        $this->vars = [
            'applicant'   => $applicant,
            'actionLinks' => $this->actionLinks($applicant)
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/applicants/info.twig', $this->vars);
    }

    private function actionLinks(Applicant $applicant): array
    {
        $out = [];
        if (parent::isAllowed('applicants', 'update')) {
            $out[] = [
                'url'   => parent::generateUri('applicants.update').'?applicant_id='.$applicant->getId(),
                'label' => _('applicant_edit'),
                'class' => 'edit'
            ];
        }
        return $out;
    }
}

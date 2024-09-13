<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Applicants\Apply;

use Application\Models\Applicant;

class Success extends \Web\View
{
    public function __construct(Applicant $applicant)
    {
        parent::__construct();

        $this->vars = [
            'applicant' => $applicant
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/applicants/success.twig', $this->vars);
    }
}

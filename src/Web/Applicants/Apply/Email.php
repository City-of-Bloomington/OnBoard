<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Applicants\Apply;

use Application\Models\Application;

class Email extends \Web\View
{
    public function __construct(Application $application)
    {
        parent::__construct();

        $this->vars = [
            'person'       => $application->getApplicant()->getFullname(),
            'committee'    => $application->getCommittee()->getName(),
            'committee_id' => $application->getCommittee_id()
        ];
    }

    public function render(): string
    {
        return $this->twig->render($this->outputFormat.'/liaisons/applicationNotification.twig', $this->vars);
    }
}

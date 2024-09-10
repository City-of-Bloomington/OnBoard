<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Offices\Update;

use Application\Models\Office;
use Application\Models\Committee;

class View extends \Web\View
{
    public function __construct(Office $office)
    {
        parent::__construct();

        $this->vars = [
            'office'    => $office,
            'committee' => $office->getCommittee(),
            'offices'   => self::offices($office->getCommittee())
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/offices/updateForm.twig', $this->vars);
    }

    private static function offices(Committee $committee): array
    {
        $offices = [];
        foreach ($committee->getOffices() as $o) { $offices[] = $o; }
        return $offices;
    }
}

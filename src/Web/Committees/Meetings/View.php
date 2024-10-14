<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Meetings;

use Application\Models\Committee;

class View extends \Web\View
{
    public function __construct(array $meetings, Committee $committee)
    {
        parent::__construct();

        $this->vars = [
            'meetings'  => $meetings,
            'committee' => $committee
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/committees/meetings.twig', $this->vars);
    }
}

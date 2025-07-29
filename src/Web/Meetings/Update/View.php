<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Meetings\Update;

use Application\Models\Meeting;

class View extends \Web\View
{
    public function __construct(Meeting $m, string $return_url)
    {
        parent::__construct();

        $this->vars = [
            'meeting'    => $m,
            'return_url' => $return_url,
            'committee'  => $m->getCommittee()
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/meetings/updateForm.twig', $this->vars);
    }
}

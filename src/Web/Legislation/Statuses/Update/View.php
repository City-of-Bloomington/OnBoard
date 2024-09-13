<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Legislation\Statuses\Update;

use Application\Models\Legislation\Status;

class View extends \Web\View
{
    public function __construct(Status $status)
    {
        parent::__construct();

        $this->vars = [
            'status' => $status
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/legislation/statuses/updateForm.twig', $this->vars);
    }
}

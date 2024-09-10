<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Legislation\Years;

use Application\Models\Committee;

class View extends \Web\View
{
    public function __construct(array $years, ?Committee $committee=null)
    {
        parent::__construct();

        $this->vars = [
            'years'     => $years,
            'committee' => $committee
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/legislation/years.twig', $this->vars);
    }
}

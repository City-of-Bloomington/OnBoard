<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Legislation\Types\Update;

use Application\Models\Legislation\Type;

class View extends \Web\View
{
    public function __construct(Type $type)
    {
        parent::__construct();

        $this->vars = [
            'type'  => $type,
            'title' => $type->getId() ? parent::_('legislationType_edit') : parent::_('legislationType_add')
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/legislation/types/updateForm.twig', $this->vars);
    }
}

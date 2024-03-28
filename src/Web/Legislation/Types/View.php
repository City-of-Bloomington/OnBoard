<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Web\Legislation\Types;

use Web\View as BaseView;

class View extends BaseView
{
    private $types;
    private $isUpdate;

    public function __construct($types, $isUpdate = false)
    {
        parent::__construct();
        $this->types = $types;
        $this->isUpdate = $isUpdate;
    }

    public function render(): string
    {
        $template = $this->isUpdate ? 'updateTypeForm.twig' : 'typesList.twig';
        return $this->twig->render("html/legislation/types/{$template}", [
            'types' => $this->types,
            'isUpdate' => $this->isUpdate
        ]);
    }
}

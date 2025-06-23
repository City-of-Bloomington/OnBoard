<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Legislation\Actions\Update;

use Application\Models\Legislation\Action;

class View extends \Web\View
{
    public function __construct(Action $action)
    {
        parent::__construct();

        $this->vars = [
            'action'    => $action,
            'outcomes'  => self::outcomes(),
            'committee' => $action->getLegislation()->getCommittee()
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/legislation/actions/updateForm.twig', $this->vars);
    }

    private static function outcomes(): array
    {
        $out = [['value'=>'']];
        foreach (Action::$outcomes as $c) { $out[] = ['value'=>$c]; }
        return $out;
    }
}

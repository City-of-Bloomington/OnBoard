<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Info;

use Application\Models\Committee;

class View extends \Web\View
{
    public function __construct(Committee $committee)
    {
        parent::__construct();
        $committee_id = (int)$committee->getId();

        $this->vars = [
            'committee'    => $committee,
            'actionLinks'  => $this->actionLinks ($committee_id),
        ];
    }

    public function render(): string
    {
        return $this->twig->render("{$this->outputFormat}/committees/info.twig", $this->vars);
    }

    private function actionLinks(int $committee_id): array
    {
        $links = [];
        if (parent::isAllowed('committees', 'update')) {
            $links[] = [
                'url'   => parent::generateUri('committees.update', ['id'=>$committee_id]),
                'label' => parent::_('edit'),
                'class' => 'edit'
            ];
        }
        if (parent::isAllowed('committees', 'end')) {
            $links[] = [
                'url'    => parent::generateUri('committees.end', ['id'=>$committee_id]),
                'label'  => parent::_('committee_end'),
                'class'  => 'delete'
            ];
        }
        return $links;
    }
}
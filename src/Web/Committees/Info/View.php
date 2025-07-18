<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
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

        $this->vars = [
            'committee'    => $committee,
            'actionLinks'  => self::actionLinks($committee),
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/committees/info.twig', $this->vars);
    }

    private static function actionLinks(Committee $c): array
    {
        $links = [];
        if (parent::isAllowed('committees', 'update')) {
            $links[] = [
                'url'   => parent::generateUri('committees.update', ['committee_id'=>$c->getId()]),
                'label' => parent::_('edit'),
                'class' => 'edit'
            ];
        }
        if (parent::isAllowed('committees', 'end') && !$c->getEndDate()) {
            $links[] = [
                'url'    => parent::generateUri('committees.end', ['committee_id'=>$c->getId()]),
                'label'  => parent::_('committee_end'),
                'class'  => 'delete'
            ];
        }
        return $links;
    }
}

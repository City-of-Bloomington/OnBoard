<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Races\List;

class View extends \Web\View
{
    public function __construct(array $races)
    {
        parent::__construct();

        $this->vars = [
            'races'       => self::race_data($races),
            'actionLinks' => self::actionLinks()
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/races/list.twig', $this->vars);
    }

    private static function race_data(array $races)
    {
        $data  = [];
        foreach ($races as $r) {
            $links = [];
            if (parent::isAllowed('races', 'update')) {
                $links = [[
                    'url'   => parent::generateUri('races.update', ['id' => $r->getId()]),
                    'label' => parent::_('race_edit'),
                    'class' => 'edit'
                ]];
            }
            $data[] = [
                'id'    => $r->getId(),
                'name'  => $r->getName(),
                'links' => $links
            ];
        }
        return $data;
    }

    private static function actionLinks()
    {
        if (parent::isAllowed('races', 'add')) {
            return [[
                'url'   => parent::generateUri('races.add'),
                'label' => parent::_('race_add'),
                'class' => 'add'
            ]];
        }
        return [];
    }
}

<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Settings\Index;

class View extends \Web\View
{
    public function __construct()
    {
        parent::__construct();

        $this->vars = [
            'links' => $this->links()
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/settings/index.twig', $this->vars);
    }

    private function links(): array
    {
        $links  = [];
        $routes = [
            'departments'=> 'department',
            'appointers' => 'appointer',
            'users'      => 'user',
            'legislationTypes'       => 'legislationType',
            'legislationActionTypes' => 'legislationActionType',
            'legislationStatuses'    => 'legislationStatus',
        ];
        foreach ($routes as $plural=>$single) {
            if (parent::isAllowed($plural, 'index')) {
                $links[] = [
                    'url'   => parent::generateUri("$plural.index"),
                    'label' => $this->_([$single, $plural, 10])
                ];
            }
        }

        if (parent::isAllowed('notifications', 'index')) {
            $links[] = [
                'url' => parent::generateUri('notifications.index'),
                'label' => $this->_(['notification_definition', 'notification_definitions', 10])
            ];
        }
        if (parent::isAllowed('people.merge', 'index')) {
            $links[] = [
                'url' => parent::generateUri('people.merge.index'),
                'label' => $this->_('merge_people')
            ];
        }
        return $links;
    }
}

<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Notifications\Definitions\List;

class View extends \Web\View
{
    public function __construct(array $definitions)
    {
        parent::__construct();

        $this->vars = [
            'definitions' => $definitions,
            'actionLinks' => self::actionLinks()
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/notifications/definitions/list.twig', $this->vars);
    }

    private static function actionLinks(): array
    {
        $links = [];
        if (parent::isAllowed('notifications.definitions', 'add')) {
            $links[] = [
                'url'   => parent::generateUri('notifications.definitions.add'),
                'label' => parent::_('notification_definition_add'),
                'class' => 'add'
            ];
        }
        return $links;
    }
}

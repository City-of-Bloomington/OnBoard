<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Notifications\List;

class View extends \Web\View
{
    public function __construct(array $notifications)
    {
        parent::__construct();

        $this->vars = [
            'notifications' => $notifications,
            'actionLinks'   => self::actionLinks()
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/notifications/list.twig', $this->vars);
    }

    private static function actionLinks(): array
    {
        $links = [];
        if (parent::isAllowed('notifications', 'add')) {
            $links[] = [
                'url'   => parent::generateUri('notifications.add'),
                'label' => parent::_('notification_add'),
                'class' => 'add'
            ];
        }
        return $links;
    }
}

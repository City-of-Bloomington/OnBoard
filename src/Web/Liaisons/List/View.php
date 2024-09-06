<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Liaisons\List;

use Application\Models\Liaison;
use Web\Url;

class View extends \Web\View
{
    public function __construct(array $data)
    {
        parent::__construct();

        $this->vars = [
            'data'        => $data,
            'actionLinks' => self::actionLinks()
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/liaisons/list.twig', $this->vars);
    }

    private function actionLinks(): array
    {
        $out = [];

        foreach (Liaison::$types as $t) {
            $out[] = [
                'url'   => parent::generateUri('liaisons.index')."?type=$t",
                'label' => _($t)
            ];
        }

        if (parent::isAllowed('people', 'viewContactInfo')) {
            $uri = new Url(Url::current_url(BASE_HOST));
            $uri->format = 'csv';
            $out[] = [
                'url'   => $uri->__toString(),
                'label' => 'csv',
                'class' => 'download'
            ];

            $uri->format = 'email';
            $out[] = [
                'url'   => $uri->__toString(),
                'label' => 'email',
                'class' => 'download'
            ];
        }

        return $out;
    }
}

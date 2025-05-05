<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
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
        self::prepare_data($data);

        $this->vars = [
            'data'        => $data,
            'actionLinks' => self::actionLinks()
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/liaisons/list.twig', $this->vars);
    }

    private static function prepare_data(array &$data)
    {
        foreach ($data as $i=>$d) {
            try {
                if ($d['username']) {
                    $u = new \Web\Ldap($d['username']);
                    $data[$i]['status'] = 'Staff';
                }
                else {
                    $data[$i]['status'] = 'Non-staff';
                }
            }
            catch (\Exception $e) {
                $data[$i]['status'] = 'Non-staff';
            }
        }
    }

    private function actionLinks(): array
    {
        $out = [];
        if (parent::isAllowed('people', 'viewContactInfo')) {
            $uri = new Url(Url::current_url(BASE_HOST));
            $uri->format = 'csv';
            $out[] = [
                'url'   => $uri->__toString(),
                'label' => 'CSV Export',
                'class' => 'download'
            ];

            $uri->format = 'email';
            $out[] = [
                'url'   => $uri->__toString(),
                'label' => 'Liaison Email List',
                'class' => 'mail'
            ];
        }

        return $out;
    }
}

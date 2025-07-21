<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\People\Add;

use Application\Models\Person;
use Web\People\Update\View as UpdateView;

class View extends \Web\View
{
    public function __construct(string $return_url)
    {
        parent::__construct();

        $this->vars = [
            'firstname'  => $_REQUEST['firstname' ] ?? '',
            'lastname'   => $_REQUEST['lastname'  ] ?? '',
            'address'    => $_REQUEST['address'   ] ?? '',
            'city'       => $_REQUEST['city'      ] ?? '',
            'state'      => $_REQUEST['state'     ] ?? '',
            'zip'        => $_REQUEST['zip'       ] ?? '',
            'citylimits' => $_REQUEST['citylimits'] ?? '',
            'occupation' => $_REQUEST['occupation'] ?? '',
            'website'    => $_REQUEST['website'   ] ?? '',
            'email'      => $_REQUEST['email'     ] ?? '',
            'phone'      => $_REQUEST['phone'     ] ?? '',
            'states'     => UpdateView::states(),
            'yesno'      => UpdateView::yesno(),
            'callback'   => isset($_REQUEST['callback']),
            'return_url' => $return_url
        ];

        // Preserve any extra parameters passed in
        $params = [];
        foreach ($_REQUEST as $key=>$value) {
            if (!in_array($key, array_keys($this->vars))) { $params[$key] = $value; }
        }
        $this->vars['additional_params'] = $params;
    }

    public function render(): string
    {
        return $this->twig->render('html/people/addForm.twig', $this->vars);
    }
}

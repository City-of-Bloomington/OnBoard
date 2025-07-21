<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Profile\Phones\Add;

use Application\Models\Phone;

class View extends \Web\View
{
    public function __construct(Phone $phone, string $return_url)
    {
        parent::__construct();

        $this->vars = [
            'phone'      => $phone,
            'return_url' => $return_url
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/people/phones/updateForm.twig', $this->vars);
    }
}

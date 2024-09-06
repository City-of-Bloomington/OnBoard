<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Liaisons\Update;

use Application\Models\Liaison;

class View extends \Web\View
{
    public function __construct(Liaison $liaison)
    {
        parent::__construct();

        $this->vars = [
            'liaison'   => $liaison,
            'committee' => $liaison->getCommittee(),
            'types'     => self::liaison_types()
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/liaisons/updateForm.twig', $this->vars);
    }

    private static function liaison_types(): array
    {
        $options = [];
        foreach (Liaison::$types as $t) {
            $options[] = ['value'=>$t];
        }
        return $options;
    }
}

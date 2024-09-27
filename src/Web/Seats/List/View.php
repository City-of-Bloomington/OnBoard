<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Seats\List;

class View extends \Web\View
{
    public function __construct(array $data)
    {
        parent::__construct();

        $this->vars = [
            'data' => $data
        ];

        if (parent::isAllowed('people', 'viewContactInfo')) {
            $this->vars['actionLinks'] = [['url' => parent::generateUri('seats.index').'?format=csv', 'label' => 'CSV Export']];
        }
    }

    public function render(): string
    {
        return $this->twig->render($this->outputFormat.'/seats/list.twig', $this->vars);
    }
}

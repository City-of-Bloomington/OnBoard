<?php
/**
 * A view of commiitee members based on Seats and Terms
 *
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Members;

use Application\Models\Committee;
use Web\View;

class SeatedView extends View
{
    public function __construct(Committee $committee, array $seat_data)
    {
        parent::__construct();

        $url = parent::current_url();
        $url->format = 'csv';
        $links = [
            'download' => [
                'url'   => $url,
                'label' => 'csv'
            ]
        ];

        $this->vars = [
            'committee'   => $committee,
            'seat_data'   => $seat_data,
            'actionLinks' => $links
        ];
    }

    public function render(): string
    {
        return $this->twig->render("{$this->outputFormat}/committees/seated_members.twig", $this->vars);
    }
}

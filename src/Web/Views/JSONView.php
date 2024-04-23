<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Views;

class JSONView extends \Web\View
{
    public function __construct(public array $data) { }

    public function render(): string
    {
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($data, JSON_PRETTY_PRINT);
    }
}

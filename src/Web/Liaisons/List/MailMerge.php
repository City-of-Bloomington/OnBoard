<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @see LiaisonTable::$dataFields
 */
declare (strict_types=1);
namespace Web\Liaisons\List;

class MailMerge extends \Web\View
{
    public function __construct(private array $data)
    {
        // Do not call parent::__construct
        // This does not use Twig templating
    }

    public function render(): string
    {
        $people = [];
        foreach ($this->data as $row) {
            $people[$row['person_id']] = isset($row['email'])
                ? "$row[firstname] $row[lastname] <$row[email]>"
                : "$row[firstname] $row[lastname]";
        }
        return implode(",\n", $people);
    }
}

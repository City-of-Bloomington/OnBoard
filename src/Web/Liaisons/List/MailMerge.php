<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
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
        $this->outputFormat = 'txt';
    }

    public function render(): string
    {
        $people = [];
        foreach ($this->data as $row) {
            $people[$row['person_id']] = "$row[firstname] $row[lastname] <$row[email]>";
        }
        return implode(",\n", $people);
    }
}

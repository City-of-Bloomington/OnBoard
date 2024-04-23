<?php
/**
 * Output arbitrary data as a CSV file
 *
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Views;

class CSVView extends \Web\View
{
    public $data = [];

    public function __construct(string $filename, array $data)
    {
        header("Content-Disposition: attachment; filename=\"$filename.csv\"");
        if ($data) {
            $this->data = array_merge([array_keys($data[0])], $data);
        }
    }

    public function render(): string
    {
        $out = fopen('php://output', 'w');
        foreach ($this->data as $row) {
            fputcsv($out, $row);
        }
        fclose($out);
        return '';
    }
}

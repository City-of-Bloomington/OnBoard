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

    public function __construct(string $title, array $data)
    {
        // Do not call parent::__construct
        // This does not use Twig templating

        $this->outputFormat = 'csv';
        $filename = APPLICATION_NAME."-{$title}-".date('Ymd');

        header("Content-Disposition: attachment; filename=\"$filename.csv\"");
        // Use the array keys as the first row for column names
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

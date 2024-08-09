<?php
/**
 * Output arbitrary data as a CSV file
 *
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Seats;

class CSVView extends \Web\View
{
    public $data = [];

    public function __construct(string $filename, array $data)
    {
        // Do not call parent::__construct
        // This does not use Twig templating

        $this->outputFormat = 'csv';

        header("Content-Disposition: attachment; filename=\"$filename.csv\"");

        $userCanViewContactInfo = parent::isAllowed('people', 'viewContactInfo');
        $fields = [
            'Committee','Seat Code', 'Seat Name','Appointer','Firstname','Lastname',
            'Term Start','Term End','Appointment Start', 'Appointment End'
        ];
        if ($userCanViewContactInfo) {
            $fields[] = 'Email';
            $fields[] = 'Address';
            $fields[] = 'City';
            $fields[] = 'State';
            $fields[] = 'Zip';
        }
        echo implode(',', $fields)."\n";

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

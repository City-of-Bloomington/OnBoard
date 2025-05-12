<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Seats\View;

use Application\Models\Seat;
use Application\Models\Term;

class CSVView extends \Web\View
{
    public $data = [];

    public function __construct(Seat $seat)
    {
        // Do not call parent::__construct
        // This does not use Twig templating

        $this->outputFormat = 'csv';
        $title    = $seat->getCode() ?? $seat->getName();
        $filename = APPLICATION_NAME."-{$title}-".date('Ymd');

        header("Content-Disposition: attachment; filename=\"$filename.csv\"");

        $data = $seat->getType() == 'termed'
                ? self::term_data  ($seat)
                : self::member_data($seat);

        // Use the array keys as the first row for column names
        if ($data) {
            $this->data = array_merge([array_keys($data[0])], $data);
        }
    }

    public function render(): string
    {
        $out = fopen('php://output', 'w');
        foreach ($this->data as $row) { fputcsv($out, $row); }
        fclose($out);

        return '';
    }

    private static function term_data(Seat $seat): array
    {
        $terms = [];
        foreach ($seat->getTerms() as $t) {
            $term = [
                'term_id'    => $t->getId(),
                'term_start' => $t->getStartDate(),
                'term_end'   => $t->getEndDate()
            ];
            foreach (self::member_data($t) as $m) {
                $terms[] = $term + $m;
            }
        }
        return $terms;
    }

    private static function member_data(Seat|Term $s): array
    {
        $members = [];

        foreach ($s->getMembers() as $m) {
            $members[] = [
                'member_id'   => $m->getId(),
                'person_id'   => $m->getPerson_id(),
                'name'        => $m->getPerson()->getFullname(),
                'startDate'   => $m->getStartDate(),
                'endDate'     => $m->getEndDate()
            ];
        }
        return $members;
    }
}

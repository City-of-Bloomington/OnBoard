<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Meetings;

use Application\Models\Committee;
use Application\Models\MeetingTable;

class View extends \Web\View
{
    public function __construct(array     $meetings,
                                Committee $committee,
                                array     $years,
                                int       $year,
                                \DateTime $start,
                                \DateTime $end)
    {
        parent::__construct();

        $this->vars = [
            'meetings'  => $meetings,
            'committee' => $committee,
            'years'     => self::yearOptions($years, $year),
            'year'      => $year,
            'start'     => $start,
            'end'       => $end
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/committees/meetings.twig', $this->vars);
    }

    /**
     * Prepare an options array for the available years
     */
    private static function yearOptions(array $years, int $year): array
    {
        if (!in_array($year, $years)) { array_unshift($years, $year); }

        $options = [];
        foreach ($years as $v) { $options[] = ['value' => $v]; }
        return $options;
    }
}

<?php
/**
 * @copyright 2014-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Web\Templates\Helpers;

use Web\Helper;
use Web\View;

class DatePicker extends Helper
{
    public function datePicker($fieldname, $id, $timestamp=null)
    {
        $date = '';
        if ($timestamp) {
            $date = date(DATE_FORMAT, $timestamp);
        }

        $help = View::translateDateString(DATE_FORMAT);
        $size = strlen($help);

        return "
        <input name=\"$fieldname\" id=\"$id\" value=\"$date\"
            type=\"date\" placeholder=\"$help\" />
        ";
    }
}

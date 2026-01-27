<?php
/**
 * @copyright 2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Applications;

class Validators
{
    /**
     * @return string[]  An array of validator class names
     */
    public static function find(): array
    {
        $out = [];
        foreach (scandir(__DIR__.'/Validators') as $f) {
            if ($f[0] != '.') {
                $path  = pathinfo($f);
                $class = get_called_class()."\\".$path['filename'];
                $out[] = $class;
            }
        }
        return $out;
    }
}

<?php
/**
 * @copyright 2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Applications\Validators;

use Application\Models\Address;
use Application\Models\Application;
use Application\Applications\Validator;
use Web\ArcGIS;

class CityLimits implements Validator
{
    public const NAME = 'City Limits';

    public function __invoke(Application $a): ?bool
    {
        $p = $a->getPerson()->getAddress(Address::TYPE_HOME);
        if ($p && $p->getX() && $p->getY()) {
            global $ARCGIS;

            $arcgis = new ArcGIS($ARCGIS);
            return $arcgis->inCityLimits($p->getX(), $p->getY());
        }
        return null;
    }
}

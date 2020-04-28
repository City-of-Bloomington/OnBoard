<?php
/**
 * Provides markup for button links
 *
 * @copyright 2014-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Templates\Helpers;

use Web\Helper;

class ButtonLink extends Helper
{
	public function buttonLink($url, $label, $type=null, array $additionalAttributes=[])
	{
        $attrs = '';
        foreach ($additionalAttributes as $key=>$value) {
            $attrs.= " $key=\"$value\"";
        }
        return "<a href=\"$url\" class=\"button $type\" $attrs>$label</a>";
	}
}

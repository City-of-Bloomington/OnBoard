<?php
/**
 * Provides markup for button links
 *
 * @copyright 2014-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Templates\Helpers;

use Blossom\Classes\Template;

class ButtonLink
{
	private $template;

	const SIZE_BUTTON = 'button';
	const SIZE_ICON   = 'icon';

	public static $types = [
		'add'      => 'fa fa-plus',
		'edit'     => 'fa fa-pencil',
		'delete'   => 'fa fa-times',
		'cancel'   => 'fa fa-times',
		'download' => 'fa fa-download'
	];

	public function __construct(Template $template)
	{
		$this->template = $template;
	}

	public function buttonLink($url, $label, $type=null, $size=self::SIZE_BUTTON)
	{
        if ($type) {
            $a = $size === self::SIZE_BUTTON
                ? '<a href="%s" class="fn1-btn"><i class="%s"></i> %s</a>'
                : '<a href="%s" class="fn1-btn %s" ><i class="hidden-label">%s</i></a>';
            $class = array_key_exists($type, self::$types) ? self::$types[$type] : $type;
            return sprintf($a, $url, $class, $label);
        }
        else {
            $a = '<a href="%s" class="fn1-btn">%s</a>';
            return sprintf($a, $url, $label);
        }
	}
}

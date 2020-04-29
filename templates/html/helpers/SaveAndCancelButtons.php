<?php
/**
 * @copyright 2013-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Web\Templates\Helpers;

use Web\Helper;
use Web\Template;

class SaveAndCancelButtons extends Helper
{
	public function saveAndCancelButtons($cancelURL, $onclick=null)
	{
		$buttons = "
		<button type=\"submit\" class=\"fn1-btn\"><i class=\"fa fa-save\"></i>
			{$this->template->_('save')}
		</button>
		<a class=\"fn1-btn\" href=\"$cancelURL\" $onclick>
			{$this->template->_('cancel')}
		</a>
		";
		return $buttons;
	}
}

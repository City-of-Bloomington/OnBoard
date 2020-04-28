<?php
/**
 * @copyright 2013-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Templates\Helpers;

use Web\Template;

class SaveAndCancelButtons
{
	private $template;

	public function __construct(Template $template)
	{
		$this->template = $template;
	}

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

<?php
/**
 * @copyright 2013-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Templates\Helpers;

use Blossom\Classes\Template;

class SaveAndCancelButtons
{
	private $template;

	public function __construct(Template $template)
	{
		$this->template = $template;
	}

	public function saveAndCancelButtons($cancelURL)
	{
		$buttons = "
		<button type=\"submit\"><span class=\"fa fa-save\"></span>
			{$this->template->_('labels.save')}
		</button>
		<a class=\"btn\" href=\"$cancelURL\"><span class=\"fa fa-undo\"></span>
			{$this->template->_('labels.cancel')}
		</a>
		";
		return $buttons;
	}
}

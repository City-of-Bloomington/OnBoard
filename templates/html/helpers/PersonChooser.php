<?php
/**
 * Renders the form fields for choosing a person
 *
 * Since we want to always choose an existing person record, or add a
 * person to the system, all person_id input fields should be the same.
 *
 * This form will render HTML for a link to the person choosing process.
 * Once the user has selected the person from the system, the person choosing
 * process will return to the current URL, including |person_id=xxx| in the URL.
 *
 * Progressive Enhancement notes:
 * If the user does not have javascript, the process should still work using
 * page redirection to the href provided.  The people search will return the user
 * to the current url once they've chosen a person.
 *
 * @copyright 2013-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Templates\Helpers;

use Application\Models\Person;
use Blossom\Classes\Template;
use Blossom\Classes\Url;
use Blossom\Classes\View;

class PersonChooser
{
	private $template;

	public function __construct(Template $template)
	{
		$this->template = $template;
	}

	/**
	 * @param string $fieldname The name of the person field
	 * @param string $fieldId   The ID of the person field
	 * @param Person $person The currently selected Person object
	 * @return string
	 */
	public function personChooser($fieldname, $fieldId, Person $person=null)
	{
		$this->template->addToAsset('scripts', BASE_URI.'/js/people/chooser.js');

		$id   = '';
		$name = '';
		if ($person) {
			$id   = $person->getId();
			$name = View::escape($person->getFullname());
		}
		$return_url = new Url(Url::current_url(BASE_HOST));
		if (isset($return_url->callback_field)) { unset($return_url->callback_field); }

		$personChooser = BASE_URI."/people?callback_field=$fieldname;return_url=$return_url";

		$html = "
		<input type=\"hidden\" name=\"{$fieldname}\" id=\"{$fieldId}\" value=\"$id\" />
		<span id=\"{$fieldId}-name\">$name</span>
		<a class=\"button person\"
			href=\"$personChooser\"
			onclick=\"PERSON_CHOOSER.open(event, '$fieldId');\">
			{$this->template->_('person_change')}
		</a>
		";
		return $html;
	}
}

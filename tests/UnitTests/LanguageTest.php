<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Blossom\Classes\Template;

require_once '../../configuration.inc';

class LanguageTest extends PHPUnit_Framework_TestCase
{
	public function testTranslationsAreSetup()
	{
		$template = new Template();
		$name = $template->_('labels.name', 'labels.names', 1);
		$this->assertEquals('Name', $name);
	}

	public function testMenuBarTranslations()
	{
		$template = new Template();

		$routes = [
			'appointer'=>'appointers',
			'committee'=>'committees',
			'requirement'=>'requirements',
			'topic'=>'topics',
			'topicType'=>'topicTypes',
			'race'=>'races',
			'tag'=>'tags',
			'person'=>'people',
			'user'=>'users'
		];
		foreach ($routes as $singular=>$plural) {
			$label = $template->_(["labels.$singular", "labels.$plural", 2]);
			$this->assertEquals(ucfirst($plural), $label);
		}
	}
}

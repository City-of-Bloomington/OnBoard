<?php
/**
 * Defines the overall page layout
 *
 * The template collects all the blocks from the controller
 *
 * @copyright 2006-2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Template extends View
{
	private $filename;
	public $outputFormat = 'html';
	public $blocks = array();

	/**
	 * @param string $filename
	 * @param string $outputFormat
	 * @param array $vars
	 */
	public function __construct($filename='default',$outputFormat='html',array $vars=null)
	{
		$this->filename = $filename;
		$this->outputFormat = preg_replace('/[^a-zA-Z]/','',$outputFormat);

		// Make sure the output format exists
		if (!is_file(APPLICATION_HOME."/templates/{$this->outputFormat}/{$this->filename}.inc")) {
			$this->filename = 'default';
			$this->outputFormat = 'html';
		}

		if (count($vars)) {
			foreach ($vars as $name=>$value) {
				$this->vars[$name] = $value;
			}
		}
	}

	/**
	 * Returns all the rendered content of the template
	 *
	 * Template files must include a call to $this->includeBlocks(),
	 * when they're ready for content
	 *
	 * @return string
	 */
	public function render()
	{
		ob_start();
		include APPLICATION_HOME."/templates/{$this->outputFormat}/{$this->filename}.inc";
		return ob_get_clean();
	}

	/**
	 * Callback function for template files
	 *
	 * Renders blocks for the main content area, unless $panel is given.  If $panel is given
	 * it will render any blocks that the controllers have assigned to that panel.
	 *
	 * Template files make calls to this function to render all the blocks that the controller
	 * has loaded for this Template.  Controllers will populate the blocks array with content.
	 * If a template file can render content in a panel that is not the main content panel,
	 * the template file will need to include the panel's name in the includeBlocks() call.
	 *
	 * $this->blocks is a multi-dimensional array.  The top level elements, non-array elements
	 * are for the default, main content area.  Other panels will be arrays in $this->blocks with
	 * the panel name as the key.
	 *
	 * Panels are nothing but a name on a div, the $panel string can be whatever the template
	 * author thinks makes sense.  Controllers are expected to know what the template authors
	 * have written.
	 *
	 * $this->blocks[] = "main content block one";
	 * $this->blocks[] = "main content block two";
	 * $this->blocks['panel-one'][] = "left sidebar block one";
	 * $this->blocks['panel-one'][] = "left sidebar block two";
	 * $this->blocks['panel-two'][] = "right sidebar block one";
	 *
	 * @param string $panel
	 * @return string
	 */
	private function includeBlocks($panel=null)
	{
		ob_start();
		if ($panel) {
			// Render any blocks for the given panel
			if (isset($this->blocks[$panel]) && is_array($this->blocks[$panel])) {
				foreach ($this->blocks[$panel] as $block) {
					echo $block->render($this->outputFormat);
				}
			}

		}
		else {
			// Render only the blocks for the main content area
			foreach ($this->blocks as $block) {
				if (!is_array($block)) {
					echo $block->render($this->outputFormat);
				}
			}
		}
		return ob_get_clean();
	}
}

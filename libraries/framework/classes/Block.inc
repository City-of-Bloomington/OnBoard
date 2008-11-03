<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 *
 * Represents a block of main content in a template
 */
class Block extends View
{
	private $filename;

	public function __construct($filename,array $vars=null)
	{
		$this->filename = $filename;
		if (count($vars))
		{
			foreach($vars as $name=>$value) { $this->vars[$name] = $value; }
		}
	}

	public function render($outputFormat='html')
	{
		$block = "/blocks/$outputFormat/{$this->filename}";

		if (file_exists(APPLICATION_HOME.$block))
		{
			ob_start();
			include APPLICATION_HOME.$block;
			return ob_get_clean();
		}
		elseif(file_exists(FRAMEWORK.$block))
		{
			ob_start();
			include FRAMEWORK.$block;
			return ob_get_clean();
		}
		else { throw new Exception('unknownBlock'); }
	}
}

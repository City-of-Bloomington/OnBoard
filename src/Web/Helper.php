<?php
/**
 * @copyright 2014-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Web;

abstract class Helper
{
    protected $template;

    public function __construct(Template $template)
    {
        $this->template = $template;
    }
}

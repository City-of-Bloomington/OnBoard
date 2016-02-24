<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Templates\Helpers;

use Blossom\Classes\Helper;

class Field extends Helper
{
    public function input(array $params)
    {
        $class = ['fn1-input-field'];

        $required = '';
        if (!empty($params['required']) && $params['required']) {
            $required = 'required="true"';
            $class[]  = 'required';
        }

        $attr = '';
        if (!empty(  $params['attr'])) {
            foreach ($params['attr'] as $key=>$value) {
                $attr.= "$key=\"$value\"";
            }
        }

        $type = !empty($params['type']) ? "type=\"$params[type]\"" : '';

        $class = implode(' ', $class);

        $value = !empty($params['value']) ? $params['value'] : '';

        return "
        <dl class=\"$class\">
            <dt><label for=\"$params[id]\">$params[label]</label></dt>
            <dd><input name=\"$params[name]\" id=\"$params[id]\" $type value=\"$value\" $required  $attr /></dd>
        </dl>
        ";
    }
}
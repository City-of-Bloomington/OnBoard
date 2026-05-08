<?php
/**
 * @copyright 2016-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;

use Application\Database;

class Site
{
    public static $labels = ['applyForm_help'];

    public static function getContent($label)
    {
        if (in_array($label, self::$labels)) {
            $result = Database::query('select content from siteContent where label=?', [$label]);
            if (count($result)) {
                return $result[0]['content'];
            }
        }
    }

    public static function saveContent(array $post)
    {
        if (!empty($post['label']) && in_array($post['label'], self::$labels)) {

            $sql = is_null(self::getContent($post['label']))
                ? 'insert siteContent set content=?, label=?'
                : 'update siteContent set content=? where label=?';
            Database::execute($sql, [$post['content'], $post['label']]);
        }
    }
}

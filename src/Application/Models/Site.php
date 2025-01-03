<?php
/**
 * @copyright 2016-2025 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;

use Web\Database;

class Site
{
    public static $labels = ['applyForm_help', 'application_received'];

    public static function getContent($label)
    {
        if (in_array($label, self::$labels)) {
            $db = Database::getConnection();
            $result = $db->query('select content from siteContent where label=?')->execute([$label]);
            if (count($result)) {
                $row = $result->current();
                return $row['content'];
            }
        }
    }

    public static function saveContent(array $post)
    {
        if (!empty($post['label']) && in_array($post['label'], self::$labels)) {

            $sql = is_null(self::getContent($post['label']))
                ? 'insert siteContent set content=?, label=?'
                : 'update siteContent set content=? where label=?';

            $db = Database::getConnection();
            $db->query($sql, [$post['content'], $post['label']]);
        }
    }
}

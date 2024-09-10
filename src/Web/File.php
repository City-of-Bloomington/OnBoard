<?php
/**
 * Utility class for file upload functions
 *
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web;

class File
{
    /**
     * Return the max size upload allowed in PHP ini
     *
     * This returns both a human readable size string as well as the raw
     * number of bytes.
     */
    public static function maxUpload(): array
    {
        $upload_max_size  = ini_get('upload_max_filesize');
        $post_max_size    = ini_get('post_max_size');
        $upload_max_bytes = self::bytes($upload_max_size);
        $post_max_bytes   = self::bytes(  $post_max_size);

        if ($upload_max_bytes < $post_max_bytes) {
            $maxSize  = $upload_max_size;
            $maxBytes = $upload_max_bytes;
        }
        else {
            $maxSize  = $post_max_size;
            $maxBytes = $post_max_bytes;
        }
        return [$maxSize, $maxBytes];
    }

    public static function bytes(string $size): int
    {
        switch (substr($size, -1)) {
            case 'M': return (int)$size * 1048576;
            case 'K': return (int)$size * 1024;
            case 'G': return (int)$size * 1073741824;
            default:  return (int)$size;
        }
    }
}

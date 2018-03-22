<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
use Scripts\Ckan\Ckan;
use Blossom\Classes\Database;

include realpath(__DIR__.'/../../bootstrap.inc');

if (file_exists(SITE_HOME.'/ckan_config.inc')) {
    include __DIR__.'/Ckan.php';
    $config = include SITE_HOME.'/ckan_config.inc';
    if ($config['ckan_url']) {

        $ckan = new Ckan($config);
        $dir  = SITE_HOME.'/ckan';
        if (!is_dir($dir)) { mkdir($dir, 0775); }

        foreach ($config['resources'] as $resource_id=>$onboard) {
            $file = $dir."/$onboard[filename]";
            $url  = BASE_URL.$onboard['uri'];

            file_put_contents($file, file_get_contents($url));
            $ckan->upload_resource($resource_id, $file);
            unlink($file);
        }
    }
    else {
        echo "Missing CKAN url\n";
    }
}
else {
    echo "No config file\n";
}

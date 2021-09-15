<?php
/**
 * @copyright 2021 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Search;

use Application\Models\File;

use Solarium\Core\Client\Adapter\Curl;
use Solarium\Client;
use Solarium\Core\Query\Result\ResultInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Solr
{
    public const DATETIME_FORMAT = 'Y-m-d\TH:i:s\Z';
    
    private $client;

    /**
     * @param array $config  Endpoint config for Solarium Client
     *  [
     *      'scheme'   => 'https',
     *      'host'     => 'localhost',
     *      'port'     => 443,
     *      'core'     => 'testcore',
     *      'username' => 'user',
     *      'password' => 'pass'
     *  ]
     */
    public function __construct(array $config)
    {
        $curl         = new Curl();
        $curl->setTimeout(10);
        $this->client = new Client($curl,
                                   new EventDispatcher(),
                                   ['endpoint'=>['solr'=>$config]]);
    }

    public function getClient(): Client { return $this->client; }

    public static function filterControlCharacters(string $data): string
    {
        return preg_replace('@[\x00-\x08\x0B\x0C\x0E-\x1F]@', ' ', $data);
    }

    public function query(): ResultInterface
    {
        $query = $this->client->createQuery(Client::QUERY_SELECT);
        return   $this->client->execute($query);
    }

    /**
     * Clears all OnBoard data from the Solr core
     */
    public function purge(): ResultInterface
    {
        $delete = $this->client->createUpdate();
        $delete->addDeleteQuery('index_id:'.APPLICATION_NAME);
        $delete->addCommit();
        return $this->client->update($delete);
    }

    public function add(File $file): ResultInterface
    {
        $fields = $this->prepareIndexFields($file);
        $update = $this->client->createUpdate();
        $doc    = $update->createDocument($fields);
        $update->addDocuments([$doc]);
        $update->addCommit();
        return $this->client->update($update);
    }

    public function prepareIndexFields(File $file): array
    {
        $data = $file->getSolrFields();
        
        $utc     = new \DateTimeZone('UTC');
        $date    = new \DateTime($data['date']);
        $changed = new \DateTime($data['changed']);
        $date   ->setTimezone($utc);
        $changed->setTimezone($utc);

        return [
            'site'             => BASE_URL,
            'id'               => APPLICATION_NAME."-$data[type]-$data[id]",
            'index_id'         => APPLICATION_NAME,
            'ss_search_api_id' => "$data[type]-$data[id]",
            'ss_type'          => $data['type' ],
            'ss_title'         => $data['title'],
            'ss_url'           => $data['url'  ],
            'ds_date'          => $date   ->format(self::DATETIME_FORMAT),
            'ds_changed'       => $changed->format(self::DATETIME_FORMAT),
            'tm_X3b_en_aggregated_field' => self::filterControlCharacters($data['text'])
        ];
    }
}

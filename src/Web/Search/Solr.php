<?php
/**
 * @copyright 2021-2023 City of Bloomington, Indiana
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
    public static $FACETS        = ['index_id', 'ss_type', 'ss_board'];

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
        $this->client = new Client($curl,
                                   new EventDispatcher(),
                                   ['endpoint'=>['solr'=>$config]]);
    }

    public function getClient(): Client { return $this->client; }
    public function setTimeout(int $s)  { $this->client->getAdapter()->setTimeout($s); }

    /**
     * @param string $search
     * @param int    $itemsPerPage
     * @param int    $currentPage   Current page number starting from 1
     * @param array  $filters
     */
    public function query(string $search,
                             int $itemsPerPage,
                             int $currentPage,
                          ?array $filters=null): ResultInterface
    {
        $search = self::cleanInput($search);

        $query  = $this->client->createSelect();
        $dismax = $query->getEDisMax();

        $query->getHighlighting();

        $query->setQuery ($search);
        $query->setFields('id,site,index_id,ss_type,ss_board,ss_url,ss_title,ss_summary,score');
        $query->setStart ($currentPage - 1); // Solr pagination starts at 0
        $query->setRows  ($itemsPerPage);

        $dismax->setQueryFields('ss_title^2 ss_summary^2 tm_X3b_en_aggregated_field');

        // This filters out old documents from search results
        // It declares a curve from 1 to zero where it hits zero at $M.
        // So, documents older than $m will not show up in search results at all.
        // $a and $b control the shape of the curve.
        // Units are all in milliseconds
        $numYears = 1;
        $m = 3.16E-11 * $numYears;
        $a = 1; $b = 1;
        $dismax->setBoostFunctionsMult("recip(ms(NOW,ds_changed),$m,$a,$b)");
//         $dismax->setBoostFunctionsMult("if(eq(ss_type,'news'),0.1,1) recip(ms(NOW,ds_changed),1,1000,1000)");

        $facets = $query->getFacetSet();
        foreach (self::$FACETS as $f) {
            $facets->createFacetField($f)->setField($f);

            if (!empty($filters[$f])) {
                $query->createFilterQuery($f)
                      ->setQuery(sprintf('%s:"%s"', $f, self::cleanInput($filters[$f])));
            }
        }

        $req = $this->client->createRequest($query);
        return $this->client->execute($query);
    }

    /**
     * Clears all OnBoard data from the Solr core
     */
    public function purge(): ResultInterface
    {
        $delete = $this->client->createUpdate();
        $delete->addDeleteQuery('index_id:'.APPLICATION_NAME);
        $delete->addCommit();
        $delete->addOptimize();
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

    /**
     * Delete a single file from the index
     */
    public function delete(File $file): ResultInterface
    {
        $fields = $this->prepareIndexFields($file);
        $delete = $this->client->createUpdate();
        $delete->addDeleteQuery('id:'.$fields['id']);
        $delete->addCommit();
        $delete->addOptimize();
        return $this->client->update($delete);
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
            'ss_board'         => $data['committee'],
            'ss_type'          => $data['type' ],
            'ss_title'         => $data['title'],
            'ss_url'           => $data['url'  ],
            'its_year'         => $date   ->format('Y'),
            'ds_date'          => $date   ->format(self::DATETIME_FORMAT),
            'ds_changed'       => $changed->format(self::DATETIME_FORMAT),
            'tm_X3b_en_aggregated_field' => self::filterControlCharacters($data['text'])
        ];
    }

    public static function filterControlCharacters(string $data): string
    {
        return preg_replace('@[\x00-\x08\x0B\x0C\x0E-\x1F]@', ' ', $data);
    }

    private static function cleanInput(string $search): string
    {
        return str_replace(['"', "'"], '', $search);
    }
}

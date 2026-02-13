<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web;

class ArcGIS
{
    private $token;
    private $portal;
    private $server;

    public const REFERER = 'https://'.BASE_HOST;
    public const LAYER_ADDRESS           = '/Energov/EnergovData/MapServer/0';
    public const LAYER_PARCEL            = '/Energov/EnergovData/MapServer/1';
    public const LAYER_MUNICPAL_BOUNDARY = '/Boundaries/CityMunicipalBoundary/MapServer/0';
    public const LOCATOR                 = '/Locators/CityCountyLocator12_2_24/GeocodeServer';

    public function __construct(array $config)
    {
        $this->portal = $config['portal'].'/sharing/rest';
        $this->server = $config['server'].'/rest/services';

        $this->token = $this->token($config);
        if (!$this->token) {
            throw new \Exception('Could not authenticate to portal');
        }
    }

    /**
     * Look up an address by the fulladdress string
     */
    public function address(string $fulladdress): ?array
    {
        $url = $this->server.self::LAYER_ADDRESS.'/query?'.http_build_query([
            'where'          => "upper(fulladdress)=upper('$fulladdress')",
            'outFields'      => 'fulladdress,zip,lat,lon,xcoord,ycoord,jurisdiction',
            'f'              => 'json',
            'returnGeometry' => 'false',
            'token'          => $this->token
        ], '', '&');

        $json = self::json_query($url);
        if ($json && !empty($json['features'])) {
            $out = [];
            foreach ($json['features'] as $f) { $out[] = $f['attributes']; }
            return $out;
        }
        return null;
    }


    /**
     * May return more than one parcel for given coordinates
     *
     * @param string $resource  Path to resource in REST service
     * @param int    $x         State Plane X
     * @param int    $y         State Plane y
     * @return array  An array of parcels
     */
    public function parcels(int $x, int $y): ?array
    {
        $url = $this->server.self::LAYER_PARCEL.'/query?'.http_build_query([
            'geometryType'   => 'esriGeometryPoint',
            'geometry'       => "$x,$y",
            'spatialRel'     => 'esriSpatialRelWithin',
            'outFields'      => 'OBJECTID,pin_18,tax_10,owner,legal_desc',
            'f'              => 'json',
            'returnGeometry' => 'false',
            'token'          => $this->token
        ], '', '&');

        $json = self::json_query($url);
        if ($json && !empty($json['features'])) {
            $out = [];
            foreach ($json['features'] as $f) { $out[] = $f['attributes']; }
            return $out;
        }
        return null;
    }

    /**
     * @param int    $x         State Plane X
     * @param int    $y         State Plane y
     */
    public function inCityLimits(int $x, int $y): bool
    {
        $url = $this->server.self::LAYER_MUNICPAL_BOUNDARY.'/query?'.http_build_query([
            'geometryType'   => 'esriGeometryPoint',
            'geometry'       => "$x,$y",
            'spatialRel'     => 'esriSpatialRelWithin',
            'outFields'      => 'objectid,muni_name,muni_type',
            'f'              => 'json',
            'returnGeometry' => 'false',
            'token'          => $this->token
        ], '', '&');
        $json = self::json_query($url);
        return ($json && !empty($json['features']));
    }

    public function suggest(string $text): ?array
    {
        $url = $this->server.self::LOCATOR.'/suggest?'.http_build_query([
            'text' => $text,
            'f'    => 'json'
        ], '', '&');

        $json = self::json_query($url);
        return $json['suggestions'] ?? null;
    }

    public function findAddressCandidates(string $SingleLine, ?string $magicKey=null): ?array
    {
        $p   = ['SingleLine'=>$SingleLine,'f'=>'json'];
        if ($magicKey) { $p['magicKey'] = $magicKey; }

        $url = $this->server.self::LOCATOR.'/findAddressCandidates?'.http_build_query($p, '', '&');

        $json = self::json_query($url);
        return $json['candidates'] ?? null;
    }


    public function query(string $resource, array $params): array
    {
        $params['f']              = 'json';
        $params['returnGeometry'] = false;
        $params['token']          = $this->token;

        $url = $this->server.$resource.'/query?'.http_build_query($params, '', '&');
        $res = $this->get($url);
        if ($res) {
            $json = json_decode($res, true);
            return $json;
        }
        return [];
    }

    public function metadata(string $resource): ?array
    {
        $params = [
            'f'     => 'json',
            'token' => $this->token
        ];
        $url = $this->server.$resource.'?'.http_build_query($params, '', '&');
        $res = $this->get($url);
        if ($res) {
            $json = json_decode($res, true);
            return $json;
        }
        return null;
    }

    private function token(array $config): ?string
    {
        $res = self::post($this->portal.'/generateToken', [
            'username'  => $config['user'],
            'password'  => $config['pass'],
            'client'    => 'referer',
            'referer'   => self::REFERER,
            'f'         => 'json'
        ]);
        if ($res) {
            $json = json_decode($res, true);
            if (!empty($json['token'])) {
                return $json['token'];
            }
        }
        return null;
    }

    public static function json_query($url): ?array
    {
        $res = self::get($url);
        if ($res) {
            $json = json_decode($res, true);
            if ($json) { return $json; }
        }
        return null;
    }

    private static function get(string $url): ?string
	{
		$request = curl_init($url);
		curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($request, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($request, CURLOPT_HTTPHEADER, ['Referer: '.self::REFERER]);
		$res     = curl_exec($request);
		return $res ? $res : null;
	}

	private static function post(string $url, array $params)
    {
		$request = curl_init($url);
		curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($request, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($request, CURLOPT_POST,           true);
        curl_setopt($request, CURLOPT_POSTFIELDS, http_build_query($params, '', '&'));
		$res     = curl_exec($request);
		return $res ? $res : null;
    }
}

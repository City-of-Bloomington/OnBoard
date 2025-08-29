<?php
/**
 * @copyright 2006-2025 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Web;

use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Extra\Markdown\MarkdownExtension;
use Twig\Extra\Markdown\MichelfMarkdown;
use Twig\Extra\Markdown\MarkdownRuntime;
use Twig\RuntimeLoader\RuntimeLoaderInterface;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;
use Twig\TwigTest;

abstract class View
{
    protected $vars         = [];
    protected $twig;
    public    $outputFormat = 'html';

    abstract public function render();

    /**
     * Configures the gettext translations
     */
    public function __construct(array $vars=null)
    {
        // Twig templates
        $this->outputFormat = !empty($_REQUEST['format']) ? $_REQUEST['format'] : 'html';
        $tpl = [];

        if (defined('THEME')) {
            $dir = SITE_HOME.'/Themes/'.THEME;

            // Twig Templates
            if (is_dir ( "$dir/templates")) {
                $tpl[] = "$dir/templates";
            }
        }

        // Twig Templates
        $tpl[]      = APPLICATION_HOME.'/templates';
        $loader     = new FilesystemLoader($tpl);
        $this->twig = new Environment($loader, ['cache'            => false,
                                                'strict_variables' => true,
                                                'debug'            => true]);
        global $ROUTE;
        $this->twig->addGlobal('APPLICATION_NAME', APPLICATION_NAME);
        $this->twig->addGlobal('VERSION',          VERSION);
        $this->twig->addGlobal('BASE_URL',         BASE_URL);
        $this->twig->addGlobal('BASE_URI',         BASE_URI);
        $this->twig->addGlobal('USWDS_URL',        USWDS_URL);
        $this->twig->addGlobal('REQUEST_URI',      $_SERVER['REQUEST_URI']);
        $this->twig->addGlobal('ROUTE_NAME',       $ROUTE ? $ROUTE->name : null);
        $this->twig->addGlobal('DATE_FORMAT',      DATE_FORMAT);
        $this->twig->addGlobal('TIME_FORMAT',      TIME_FORMAT);
        $this->twig->addGlobal('DATETIME_FORMAT',  DATETIME_FORMAT);
        $this->twig->addGlobal('LANG',             strtolower(substr(LOCALE, 0, 2)));

        if (isset($_SESSION['USER'])) {
            $this->twig->addGlobal('USER', $_SESSION['USER']);
        }
        if (isset($_SESSION['errorMessages'])) {
            $this->twig->addGlobal('ERROR_MESSAGES', $_SESSION['errorMessages']);
            unset($_SESSION['errorMessages']);
        }
        $this->twig->addExtension(new DebugExtension());
        $this->twig->addExtension(new MarkdownExtension());

        $this->twig->addFunction(new TwigFunction('_'  ,         [$this, 'translate'  ]));
        $this->twig->addFunction(new TwigFunction('uri',         [$this, 'generateUri']));
        $this->twig->addFunction(new TwigFunction('url',         [$this, 'generateUrl']));
        $this->twig->addFunction(new TwigFunction('isAllowed',   [$this, 'isAllowed'  ]));
        $this->twig->addFunction(new TwigFunction('current_url', [$this, 'current_url']));

        $this->twig->addRuntimeLoader(new class implements RuntimeLoaderInterface {
            public function load($class) {
                if (MarkdownRuntime::class === $class) {
                    return new MarkdownRuntime(new MichelfMarkdown());
                }
            }
        });
    }

    /**
     * Magic Method for setting object properties
     *
     * @param string $key
     * @param mixed $value
     */
    public function __set($key,$value) {
        $this->vars[$key] = $value;
    }
    /**
     * Magic method for getting object properties
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        if (isset($this->vars[$key])) {
            return $this->vars[$key];
        }
        return null;
    }

    /**
     * @param string $key
     * @return boolean
     */
    public function __isset($key) {
        return array_key_exists($key,$this->vars);
    }

    /**
     * Cleans strings for output
     *
     * There are more bad characters than htmlspecialchars deals with.  We just want
     * to add in some other characters to clean.  While here, we might as well
     * have it trim out the whitespace too.
     *
     * @param  array|string $input
     * @param  int          $quotes Optional, the desired constant to use for the htmlspecidalchars call
     * @return string
     */
    public static function escape($input, $quotes=ENT_QUOTES)
    {
        if ($input) {
            if (is_array($input)) {
                foreach ($input as $key=>$value) {
                    $input[$key] = self::escape($value,$quotes);
                }
            }
            else {
                $input = htmlspecialchars(trim($input), $quotes, 'UTF-8');
            }
        }

        return $input;
    }

    /**
     * Reverses the escaping done by View::escape()
     *
     * @param array|string $input
     * @return string
     */
    public static function unescape($input)
    {
        if (is_array($input)) {
            foreach ($input as $key=>$value) {
                $input[$key] = self::unescape($value);
            }
        }
        else {
            $input = htmlspecialchars_decode(trim($input), ENT_QUOTES);
        }
        return $input;
    }

    /**
     * Returns the gettext translation of msgid
     *
     * The default domain is "labels".  Any other text domains must be passed
     * in the second parameter.
     *
     * For entries in the PO that are plurals, you must pass msgid as an array
     * $this->translate( ['msgid', 'msgid_plural', $num] )
     *
     * @param mixed $msgid String or Array
     * @param string $domain Alternate domain
     * @return string
     */
    public static function translate($msgid, $domain=null)
    {
        if (is_array($msgid)) {
            return $domain
                ? dngettext($domain, $msgid[0], $msgid[1], $msgid[2])
                : ngettext (         $msgid[0], $msgid[1], $msgid[2]);
        }
        else {
            return $domain
                ? dgettext($domain, $msgid)
                : gettext (         $msgid);
        }
    }

    /**
     * Alias of $this->translate()
     */
    public static function _($msgid, $domain=null)
    {
        return self::translate($msgid, $domain);
    }

    public static $supportedDateFormatStrings = [
        'm', 'n', 'd', 'j', 'Y', 'H', 'g', 'i', 's', 'a'
    ];

    /**
     * Converts the PHP date format string syntax into something for humans
     *
     * @param string $format
     * @return string
     */
    public static function translateDateString($format)
    {
        return str_replace(
            self::$supportedDateFormatStrings,
            ['mm', 'mm', 'dd', 'dd', 'yyyy', 'hh', 'hh', 'mm', 'ss', 'am'],
            $format
        );
    }

    public static function convertDateFormat($format, $syntax)
    {
        $languages = [
            'mysql'  => ['%m', '%c', '%d', '%e', '%Y', '%H', '%l', '%i', '%s', '%p'],
            'jquery' => ['mm', 'm',  'dd', 'd',  'yy', 'HH', 'h',  'mm', 'ss', 'a' ]
        ];

        if (array_key_exists($syntax, $languages)) {
            return str_replace(
                self::$supportedDateFormatStrings,
                $languages[$syntax],
                $format
            );
        }
    }

    /**
     * Creates a URI for a named route
     *
     * This imports the $ROUTES global variable and calls the
     * generate function on it.
     *
     * @see https://github.com/auraphp/Aura.Router/tree/2.x
     * @param string $route_name
     * @param array $params
     * @return string
     */
    public static function generateUri($route_name, $params=[])
    {
        global $ROUTES;
        $helper = $ROUTES->newRouteHelper();
        return $helper($route_name, $params);
    }
    public static function generateUrl($route_name, $params=[])
    {
        return "https://".BASE_HOST.self::generateUri($route_name, $params);
    }
    public static function current_url(): Url
    {
        return new Url(Url::current_url(BASE_HOST));
    }

    public static function isAllowed(string $resource, ?string $action=null): bool
    {
        global $ACL;
        $role = 'Anonymous';
        if (isset  ($_SESSION['USER']) && $_SESSION['USER']->getRole()) {
            $role = $_SESSION['USER']->getRole();
        }
        return $ACL->isAllowed($role, $resource, $action);
    }
}

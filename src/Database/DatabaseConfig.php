<?php
/**
 * Synga Inheritance Finder
 * @author      Roy Pouls
 * @copytright  2016 Roy Pouls / Synga (http://www.synga.nl)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/synga-nl/inheritance-finder
 */

namespace Synga\InheritanceFinder\Database;


use Synga\InheritanceFinder\ConfigInterface;

class DatabaseConfig implements ConfigInterface
{
    private $host;

    private $port;

    private $username;

    private $password;
}
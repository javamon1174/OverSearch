<?php
/**
 * Overwarch crawler from blizzard
 *
 * 오버워치 전적 검색
 *
 * Created on 2016. 11.
 * @package      Javamon/Oversearch
 * @subpackage   Processor, Model, View
 * @category     parser
 * @license      http://opensource.org/licenses/MIT
 * @author       javamon <javamon1174@gmail.com>
 * @link         http://javamon.be
 * @version      1.2.1
 */
namespace Javamon\OverSearch;

use Javamon\OverSearch\Config as Config;
use Javamon\OverSearch\Processor\SearchProcesser as SearchProcesser;

//service modes
// error_reporting(0);

//develop mode
error_reporting(E_ALL);
// error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', -1);

require_once __DIR__.'/vendor/autoload.php';

$SearchProcesser = new SearchProcesser();
$SearchProcesser->SearchProcesser();

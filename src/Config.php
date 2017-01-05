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

use \Monolog\Logger as Logger;
use Monolog\Handler\StreamHandler;

trait Config
{
    /**
     * @access private
     * @var String $host : maria_db connection host
     */
    // private $host = '';

    /**
     * @access private
     * @var String $user : maria_db connection username
     */
    // private $user = '';

    /**
     * @access private
     * @var String $password : maria_db connection password
     */
    // private $password = '';

    /**
     * @access protected
     * @var String $url : parse to blizzard url
     */
    protected $url = '';

    /**
     * @access protected
     * @var String $user_tag : blizzard battle tag
     */
    protected $user_tag = '';

    /**
     * this charset is recommended to assign a charset in maria_db
     * @access protected
     * @var String $user_tag : global charset
     */
    protected $charset = 'utf8';

    /**
     * @access protected
     * @var Array $processer : Array Kinds of processer
     */
    protected $processer = [
                            'Summary',
                            'DataAnaly',
                            'Frequency',
                            'Heroes',
                           ];

   /**
    * @access private
    * @var String $database : maria_db use database name
    */
   private $database = 'oversearch';

   /**
    * @access private
    * @var Array $tables : maria_db use database tables
    */
   private $tables = array(
       0 => 'Summary',
       1 => 'Frequency',
       2 => 'Heroes',
   );

   /**
    * @access private
    * @var Array $query_tables : matched maria_db use database tables
    */
   private $query_tables = array(
       0 => 'tb_summary',
       1 => 'tb_frequency',
       2 => 'tb_heroes',
       3 => 'tb_time',
   );

   /**
    * load loger
    * @access public
    */
    public function loadingLogger() {
        //  // create a log channel
        // $log = new Logger('oversearch');
        //  // oversearch.log 파일에 로그 생성. 로그 레벨은 WARNING
        // $log->pushHandler(new StreamHandler('../oversearch.log', Logger::WARNING));
        //  // add records to the log
        // $log->addInfo('Info log');
        // //  $log->addDebug('Debug log');
        // //  $log->addError('Error log');
    }

    /**
     * (project init) Specify the default setting values ​​for the project (php.ini)
     * @access public
     * @return Array $result : init result
     */
     public function ConfigInit()
     {
         $this->loadingLogger();
         $ini_set = function () {
             $ini_set_result = ini_set('default_charset', 'utf-8');
             //Please add here ini_set
             if ($ini_set_result === "UTF-8")
                 return true;
             else
                 return false;
             // if ($ini_set_result === "UTF-8") { return true; } else { return false; }
         };
         $result[] = $ini_set();
         $result[] = date_default_timezone_set('Asia/Seoul');
         $error['set_error'] = 'ini setting error, check for config class';
         if (!($result == true)) return $error;
     }

     public function __construct() { }
}

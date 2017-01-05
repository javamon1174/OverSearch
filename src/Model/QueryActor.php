<?php
/**
 * Overwarch crawler from blizzard
 *
 * 오버워치 전적 검색
 *
 * Created on 2016. 11.
 * @package      Javamon/Oversearch/Model
 * @category     parser
 * @license      http://opensource.org/licenses/MIT
 * @author       javamon <javamon1174@gmail.com>
 * @link         http://javamon.be
 * @version      1.2.1
 */
namespace Javamon\OverSearch\Model;

use Javamon\OverSearch\Config as Config;
use \PDO;

 class QueryActor
 {
     use Config;

     private $db_resouce;

     public function __construct() { }

     /**
      * connect to maria database
      *
      * @access default
      * @return object $db_resouce : database resouce
     */
     function dbConnect()
     {
         $parentDir = dirname(dirname(dirname(__DIR__ . '.../'))).'/DbConfig.php';
         require $parentDir;

         try {
             $dsn = "mysql:host=".$db_config["host"].";dbname=$this->database";
             $options = array(
                 PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES '.$this->charset,
                 PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                 PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                 // PDO::ATTR_EMULATE_PREPARES   => false,
                 //Please add here mysql setting
             );
             $this->db_resouce = new PDO($dsn, $db_config["user"], $db_config["password"], $options);
             return true;
         } catch (PDOException $e) {
             echo 'Connection failed: ' . $e->getMessage();
         }
     }

     /**
      * PDO Insert query execution
      *
      * @access default
      * @param Array $query : sql query
      * @param Array $data  : query_result_data
      * @return Array $result : Query execution result
     */
     function PdoInsertCommit($query, $data = array())
     {
         $this->dbConnect();

         try {
             $this->db_resouce->beginTransaction();
             $prepared_query = $this->db_resouce->prepare($query);
             $commit_result = $prepared_query->execute($data);
             $this->PdoResouceRemove();
             return $commit_result;
         } catch (PDOException $e) {
             $this->db_resouce->rollBack();
             $this->PdoResouceRemove();
             return false;
         }
     }

     /**
      * PDO Select query execution
      *
      * @access default
      * @param Array $query : sql query
      * @param Array $data  : query_result_data
      * @return Array $query_result_data : returns data of Query execution result
     */
     function PdoSelectCommit($query, $data = array())
     {
         $this->dbConnect();
         try {
             $prepared_query = $this->db_resouce->prepare($query);
             $commit_result = $prepared_query->execute($data);
             $this->PdoResouceRemove();
             return $prepared_query->fetchAll(PDO::FETCH_ASSOC);
         } catch (PDOException $e) {
             $this->db_resouce->rollBack();
             $this->PdoResouceRemove();
             return false;
         }
     }

     /**
      * PDO Delete query execution
      *
      * @access default
      * @param Array $query : sql query
      * @param Array $data  : query_result_data
      * @return Array $result : Query execution result
     */
     function PdoDeleteCommit($query, $data = array())
     {
         $this->dbConnect();
         try
         {
             $this->db_resouce->beginTransaction();

             foreach ($query as $key => $table_query)
             {
                 $prepared_query = $this->db_resouce->prepare($table_query);
                 $commit_result[] = $prepared_query->execute($data);
             }

             $commit_result = $this->db_resouce->commit();
             if(!$commit_result)
                 $this->db_resouce->rollBack();

             $this->PdoResouceRemove();
             return true;
         }
         catch(PDOException $ex)
         {
             $this->db_resouce->rollBack();
             $this->PdoResouceRemove();
             return $ex->getMessage();
         }
     }

     /**
      * PDO Resouce Remover
      *
      * @access private
      * @param obejct $pdo_reousce : database resouce
      * @return Bollean true
     */
     private function PdoResouceRemove()
     {
         unset($this->db_resouce);
         return true;
     }
 }

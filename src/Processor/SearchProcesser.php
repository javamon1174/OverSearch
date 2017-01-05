<?php
/**
 * Overwarch crawler from blizzard
 *
 * 오버워치 전적 검색
 *
 * Created on 2016. 11.
 * @package      Javamon/Oversearch/Processor
 * @category     parser
 * @license      http://opensource.org/licenses/MIT
 * @author       javamon <javamon1174@gmail.com>
 * @link         http://javamon.be
 * @version      1.2.1
 */
namespace Javamon\OverSearch\Processor;

use Javamon\OverSearch\Config as Config;
use Javamon\OverSearch\Processor\FrequencyProcessor as FrequencyProcessor;
use Javamon\OverSearch\Processor\StatisticsProcessor as StatisticsProcessor;
use Javamon\OverSearch\Processor\SummaryProcessor as SummaryProcessor;
use Javamon\OverSearch\Model\QueryActor as QueryActor;
use Javamon\OverSearch\Model\QueryFilter as QueryFilter;
use Javamon\OverSearch\View\Data2JsonViewer as Data2JsonViewer;
// use Javamon\OverSearch\Processor as Processor;
// use Javamon\OverSearch\Model as Model;
// use Javamon\OverSearch\View as View;

class SearchProcesser
{
    use Config;

    /**
     * @access protected
     * @var Object $db_resouce : maria db resouce variable
     */
    protected $db_resouce;

    /**
     * @access protected
     * @var Object $viewer : instance of viewer class
     */
    protected $viewer;

    /**
     * @access protected
     * @var Object $query_filter : instance of query filter class
     */
    protected $query_filter;

    /**
     * @access protected
     * @var Object $query_actor : instance of query actor class
     */
    protected $query_actor;


    /**
     * @access protected
     * @var String $user_idx : defined user index
     */
    protected $user_idx;

    /**
     * @access private
     * @var String $patal_error : patal error msg
     */
    private $patal_error = array();

    /**
     * @access private
     * @var String $update_flag : user update flag
     */
    private $update_flag;

    /**
     * @access private
     * @var Boolean $parse_flag : data parse flag(blizzard to db)
     */
    private $parse_flag;

    /**
     * OverSearch project initailize
     * @access public
     * @todo passing from blizzard
     */
    public function init()
    {
        $this->ConfigInit();
        $this->SetUserNameData2Var();

        $this->SetDatabaseClassObj();
        $this->SetViewerClassObj();

        $temp_flag = filter_input(INPUT_GET, 'renew');
        $this->update_flag = (!empty($temp_flag));
        return true;
    }


    /**
     * Process-oriented handle of OverSearch project
     * @access public
     * @return String $parsed_user_all_data : draw user data
     */
    public function SearchProcesser()
    {
        $this->init();
        $user_check = $this->GetUserIdx();

        if ($user_check && !$this->update_flag)
        {
            $data = $this->UserDataLoad();
        }

        elseif ($user_check && $this->update_flag)
        {
            $this->DeleteUserData();
            $parse = $this->DataToDbFromBlizzard();

            if (!$parse)
                return false;

            $data = $this->UserDataLoad();
        }

        else
        {
            $parse = $this->DataToDbFromBlizzard();
            if (!$parse)
                return false;

            $data = $this->UserDataLoad();
        }
        $this->viewer->Data2JsonViewer($data, null);
    }

    /**
     * Delete user data in database for renew
     * @access public
     * @return Array $commit : commit result
     */
    public function DeleteUserData()
    {
        $query_data = array(
                            ':user_idx' => $this->user_idx,
                           );

        $query = $this->query_filter->QueryInit('DeleteUserData');


        $commit = $this->query_actor->PdoDeleteCommit($query, $query_data);
        return $commit;
    }

    /**
     * Resouce Remover for package
     * @access public
     */
    public function RemoveResouce($obj)
    {
        unset($obj);
    }

    public function __construct() { }

    /**
     * Verify that the record about the user in the database
     * @access protected
     * @param   String $query_data : battle tag
     * @return  Bollean $commit_result : When there is a data in database => true or false
     */
    protected function IsUserDataCheck()
    {
        $query_data[':user_name'] = USERNAME;
        $query = $this->query_filter->QueryInit('CheckUserQuery');
        $commit = $this->query_actor->PdoSelectCommit($query,$query_data);
        if (!empty($commit[0]['update_date']))
            return true;
        else
            return false;
    }

    /**
     * Sets the database resouce to variable
     * @access protected
     * @return  Obejct $this->query_filter  : model filter class instance
     * @return  Obejct $this->query_actor   : model actor class instance
     */
    protected function SetDatabaseClassObj()
    {
        $this->query_filter = $this->SearchFactory('QueryFilter');
        $this->query_actor = $this->SearchFactory('QueryActor');
    }

    /**
     * Sets viewer object to variable
     * @access protected
     */
    protected function SetViewerClassObj()
    {
        $this->viewer = $this->SearchFactory('Data2JsonViewer');
    }

    /**
     * This function returns an instance(factory)
     * @access protected
     * @param   String $class_name : class name
     * @return  Object $instance : instance
     */
    protected function SearchFactory($class_name = '')
    {
        if (strpos($class_name, 'Query') !== false)
            $class_name = 'Javamon\OverSearch\Model\\'.$class_name;

        if (strpos($class_name, 'Processor') !== false)
            $class_name = 'Javamon\OverSearch\Processor\\'.$class_name;

        if (strpos($class_name, 'View') !== false)
            $class_name = 'Javamon\OverSearch\View\\'.$class_name;
        return new $class_name;
    }

    /**
     * Returns replaced blizzard tag and define const.
     * @access protected
     * @param   String $username    : user_battle_tag
     * @return  Object EN_USERNAME  : user_urlencoding_battle_tag or die
     */
    protected function SetUserNameData2Var()
    {
        $tag = filter_input(INPUT_GET, 'tag');
        //test
        if (empty($tag))
            $tag = filter_input(INPUT_GET, 'user');

        $tag = urldecode($tag);
        $de_tag = str_replace("#", "-", $tag);
        if ($tag == "") {
            $this->patal_error['error_msg'] = 'battle tag has not been entered.';
            $this->viewer->Data2JsonViewer(null, $this->patal_error);
            $this->RemoveResouce($patal_error);
            return die;
        } else {
            (!defined('USERNAME')) ? define('USERNAME', $de_tag) : false;
            (!defined('DE_USERNAME')) ? define('DE_USERNAME', $de_tag) : false;
            (!defined('EN_USERNAME')) ? define('EN_USERNAME', urlencode($tag)) : false;
            return EN_USERNAME;
        }
    }

    /**
     * Returns Returns overwatch data from database.
     * @access private
     * @param   String $username    : user_battle_tag
     * @return  Int    $index       : user index in database
     */
    private function GetUserIdx()
    {
        $query = $this->query_filter->QueryInit('GetUserIndex');
        $commit = $this->query_actor->PdoSelectCommit($query,array(':user_name' => USERNAME));

        if (array_key_exists(0, $commit))
        {
            //user index setting
            $this->user_idx = $commit[0]['user_idx'];
            $this->query_filter->user_idx = $this->user_idx;
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Obtains overwatch data for the use from database.
     * @access private
     * @return  Array $data : overwatch data of user
     */
    private function UserDataLoad()
    {

        foreach ($this->tables as $key => $table)
        {
            $query[$table] = $this->query_filter->QueryInit('Select'.$table);
            $commit[$table] = $this->query_actor->PdoSelectCommit($query[$table], array(
                ':user_idx' => $this->user_idx,
            ));
                $UserData[$table] = $commit[$table];
        }
        $this->RemoveResouce($commit);
        $UserData['Heroes'] = $this->DataChangeFormat($UserData['Heroes']);
        return $UserData;
    }

    /**
     * Check for data validation.
     * @access private
     * @param   String  $user_summary_data  : Analy data in summary data
     * @return  Boolean $result             : check result
     */
    private function IsDataCheck($user_summary_data)
    {
        if($user_summary_data[':analy'] == 'bug')
            return false;
        else
            return true;
    }

    /**
     * Collect all the parsed data.
     * @access private
     * @return  Array Boolean $commit_result : commit result array
     */
    private function DataToDbFromBlizzard()
    {
        if($this->parse_flag)
        return true;

        $parse_data_from_blizzard = $this->GetDataFromBlizzard();
        $explode_data_from_blizzard = explode('column gutter-12@sm gutter-18@md' , $parse_data_from_blizzard);

        //Heroes Summary Processer
        $summary_processor = $this->SearchFactory('SummaryProcessor');
        $temp_explode_data = $explode_data_from_blizzard[0].$explode_data_from_blizzard[2];
        $user_summary_data = $summary_processor->SummayData2Array($temp_explode_data);
        if(!$this->IsDataCheck($user_summary_data))
            return false;
        $data_processing_result['summary'] = $this->InsertSummaryData($user_summary_data);
        $this->RemoveResouce($temp_explode_data);
        $this->RemoveResouce($summary_processor);

        //get user index Number
        $this->GetUserIdx();

        //to top
        $pattern = '/<h3 class=\"card-heading\">.*<\/p><\/div><\/div>/';
        preg_match_all($pattern, $explode_data_from_blizzard[2], $matches, PREG_OFFSET_CAPTURE, 3);
        $explode_hero_play_time = explode('svg>' , $matches[0][0][0]);
        $explode_hero_play_time = $explode_hero_play_time[7];

        //Heroes Frequency processer
        $frequency_processor = $this->SearchFactory('FrequencyProcessor');
        $hero_play_time_data = $frequency_processor->TimeData2Array($explode_hero_play_time);
        $hero_play_time_data_count = $frequency_processor->hero_time_count;

        $data_processing_result['Frequency'] = $this->InsertFrequencyData($hero_play_time_data, $hero_play_time_data_count);
        $this->RemoveResouce($frequency_processor);
        $this->RemoveResouce($hero_play_time_data);
        $this->RemoveResouce($hero_play_time_data_count);


        //Heroes Statistics processer
        $statistics_processer = $this->SearchFactory('StatisticsProcessor');
        $statistics_data = $statistics_processer->DetailData2Array($parse_data_from_blizzard);
        $statistics_data_count = $statistics_processer->hero_statistics_count;
        $data_processing_result['Statistics'] = $this->InsertStatisticsData($statistics_data, $statistics_data_count);

        $this->RemoveResouce($statistics_processer);
        $this->RemoveResouce($statistics_data);

        //get query for updating date
        $data_processing_result['Time'] = $this->InsertTimeData();

        $this->parse_flag = true;
        return $data_processing_result;
    }

    /**
     * Insert Summary data parsed from blizzard.
     * @access private
     * @param   Array   $summary_data       : summary data
     * @param   Integer $summary_data_count : summary data count
     * @return  Boolean $commit_result      : commit result
     */
    private function InsertSummaryData($user_summary_data)
    {
        $query = $this->query_filter->QueryInit('InsertSummaryQuery');
        $commit = $this->query_actor->PdoInsertCommit($query, $user_summary_data);
        return $commit;
    }

    /**
     * Insert Frequency data parsed from blizzard.
     * @access private
     * @param   Array   $hero_play_time_data        : Frequency data
     * @param   Integer $hero_play_time_data_count  : Frequency data count
     * @return  Boolean $commit_result              : commit result
     */
    private function InsertFrequencyData($hero_play_time_data, $hero_play_time_data_count = 0)
    {
        $query = $this->query_filter->QueryInit('InsertFrequencyQuery', $hero_play_time_data_count);
        $commit = $this->query_actor->PdoInsertCommit($query, $hero_play_time_data);
        return $commit;
    }

    /**
     * Insert Statistics data parsed from blizzard.
     * @access private
     * @param   Array   $hero_statistics_data        : Statistics data
     * @param   Integer $hero_statistics_data_count  : Statistics data count
     * @return  Boolean $commit_result               : commit result
     */
    private function InsertStatisticsData($hero_statistics_data, $hero_statistics_data_count = 0)
    {
        $query = $this->query_filter->QueryInit('InsertStatisticsQuery', $hero_statistics_data_count);
        $commit = $this->query_actor->PdoInsertCommit($query, $hero_statistics_data);
        return $commit;
    }

    /**
     * Insert TimeData data parsed from blizzard.
     * @access private
     * @param   Array   $insert_time_data   : TimeData data
     * @return  Boolean $commit_result      : commit result
     */
    private function InsertTimeData()
    {
        $insert_time_data = array(
            ':update_date' => date("Y-m-d H:i:s",time()),
            ':user_idx' => $this->user_idx,
        );
        $query = $this->query_filter->QueryInit('InsertTimeQuery');
        $commit = $this->query_actor->PdoInsertCommit($query, $insert_time_data);
        return $commit;
    }

    /**
     * Get Data From Blizzard.
     * @access private
     * @param   String  $url                    : blizzard url
     * @return  String  $blizzard_parse_data    : data from blizzard
     */
    private function GetDataFromBlizzard()
    {
        $url = "https://playoverwatch.com/ko-kr/career/pc/kr/".urlencode(DE_USERNAME);
        // $url = "https://playoverwatch.com/ko-kr/career/pc/kr/%EC%B0%A8%EC%B0%A8-3997";
        try
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $blizzard_parse_data = curl_exec($ch);
            curl_close($ch);
            return $blizzard_parse_data;
        }
        catch (Exception $e)
        {
             "<hr /><center><div style='width:500px;border: 3px solid #FFEEFF; padding: 3px; background-color: #FFDDFF;
                  font-family: verdana; font-size: 10px'><b>cURL Error</b><br>".$e."</div></center>";
            die;
        }
    }

    /**
     * Data Change Format.
     * @access private
     * @param   Array  $data                : data
     * @return  Array  $user_heroes_data    : Changed data
     */
    private function DataChangeFormat($data = array())
    {
        $user_heroes_data = array();

        foreach ($data as $key => $heroes_value)
        {
            $user_heroes_data[$heroes_value['hero']][$heroes_value['category']][]
                = $heroes_value['title']."|".$heroes_value['value'];
        }
        return $user_heroes_data;
    }
}

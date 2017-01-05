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

class StatisticsProcessor extends SearchProcesser
{
    use Config;

    public  $hero_statistics_count;
    private $index = 0;
    private $hero_data = array();
    private $hero_statistics_data = array();

    public function StatisticsProcesser()
    {
        $this->setUserNameData2Var();
        $this->setDatabaseResouce();
    }

    public function __construct() { }

    /**
     * data of overwatch to make array data from blizzard
     * @access protected
     * @param   String  $data_from_blizzard     : data from blizzard
     * @return  Array   $hero_statistics_data   : User Heroes Data
     */
    protected function DetailData2Array($data_from_blizzard) {
        $hero_data = array();
        $value = "";

        $hero_stat = $this->DataExplodeFromBlizzard($data_from_blizzard);

        if (empty($this->hero))
            $this->GetHeroName($hero_stat[0]); array_shift($hero_stat);

        foreach ($hero_stat as $row)
        {
          $hero = $this->hero[$this->index];
          $hero_data[$hero][] = $this->GetStatValue($row);
          $this->index++;
        }
        array_pop($hero_data);

        $heroes = array_keys($hero_data);
        $heroes_count = count($heroes);
        for ($i=0; $i < $heroes_count ; $i++) {
          $this->GetCategoryQueryData($hero_data[$heroes[$i]][0], $heroes[$i]);
        }
        $this->RemoveResouce($data_from_blizzard);
        $this->RemoveResouce($hero_stat);
        $this->RemoveResouce($hero_data);

        $this->hero_statistics_count = (count($this->hero_statistics_data)/4);

        return $this->hero_statistics_data;
    }

    /**
     * data of overwatch to make array data from blizzard
     * @access private
     * @param   String  $data_from_blizzard     : data from blizzard
     * @return  String  $ex_data_from_blizzard  : exploded data from blizzard
     */
    private function DataExplodeFromBlizzard($data_from_blizzard)
    {
        $statistics = explode('row column gutter-18@md' , $data_from_blizzard);
        $statistics = explode('content-box max-width-container hero-comparison-section' , $statistics[2]);  //$statistics[1] normal game
        $statistics = $statistics[0];
        $hero_stat = explode('row gutter-18@md' , $statistics);
        return $hero_stat;
    }

     //needs to refactoring => generator
    private function GetCategoryQueryData($hero_data, $hero) {
        $value = "";
        $hero_data_keys = array_keys($hero_data);
        foreach ($hero_data_keys as $title) {
          $title = strip_tags($title);

          switch ($title) {
            //전투
            case '단독 처치': $category = "전투"; break;
            case '임무 기여 처치': $category = "전투"; break;
            case '준 피해': $category = "전투"; break;
            case '처치': $category = "전투"; break;
            case '환경 요소로 처치': $category = "전투"; break;
            case '동시 처치': $category = "전투"; break;
            case '결정타': $category = "전투"; break;
            case '근접 공격 결정타': $category = "전투"; break;
            case '치명타': $category = "전투"; break;
            case '명중률': $category = "전투"; break;
            case '치명타 명중률': $category = "전투"; break;
            case '목숨당 처치': $category = "전투"; break;
            case '발사': $category = "전투"; break;
            case '명중': $category = "전투"; break;
            case '포탑 파괴': $category = "전투"; break;
            case '분당 치명타': $category = "전투"; break;
            //죽음
            case '죽음': $category = "죽음"; break;
            case '환경 요소로 죽음': $category = "죽음"; break;
            //경기 보상
            case '칭찬 카드': $category = "경기 보상"; break;
            case '메달 획득': $category = "경기 보상"; break;
            case '메달 - 금': $category = "경기 보상"; break;
            case '메달 - 은': $category = "경기 보상"; break;
            case '메달 - 동': $category = "경기 보상"; break;
            //지원
            case '치유': $category = "지원"; break;
            case '처치 시야 지원': $category = "지원"; break;
            case '순간이동기 파괴': $category = "지원"; break;
            case '자가 치유': $category = "지원"; break;
            case '처치 시야 지원 - 평균': $category = "지원"; break;
            case '처치 시야 지원 - 한 게임 최고기록': $category = "지원"; break;
            //최고
            case '처치 - 한 게임 최고기록': $category = "최고"; break;
            case '결정타 - 한 게임 최고기록': $category = "최고"; break;
            case '준 피해 - 한 게임 최고기록': $category = "최고"; break;
            case '치유 - 한 게임 최고기록': $category = "최고"; break;
            case '방어형 도움 - 한 게임 최고기록': $category = "최고"; break;
            case '공격형 도움 - 한 게임 최고기록': $category = "최고"; break;
            case '임무 기여 처치 - 한 게임 최고기록': $category = "최고"; break;
            case '임무 기여 시간 - 한 게임 최고기록': $category = "최고"; break;
            case '동시 처치 - 최고기록': $category = "최고"; break;
            case '단독 처치 - 한 게임 최고기록': $category = "최고"; break;
            case '폭주 시간 - 한 게임 최고기록': $category = "최고"; break;
            case '근접 공격 결정타 - 한 게임 최고기록': $category = "최고"; break;
            case '준 피해 - 한 목숨 최고기록': $category = "최고"; break;
            case '치명타 - 한 게임 최고기록': $category = "최고"; break;
            case '치명타 - 한 목숨 최고기록': $category = "최고"; break;
            case '처치 - 한 목숨 최고기록': $category = "최고"; break;
            case '연속 처치 - 최고기록': $category = "최고"; break;
            case '치유 - 한 목숨 최고기록': $category = "최고"; break;
            case '자가 치유 - 한 게임 최고기록': $category = "최고"; break;
            case '명중률 - 한 게임 최고기록': $category = "최고"; break;
            //게임
            case '승리한 게임': $category = "게임"; break;
            case '폭주 시간': $category = "게임"; break;
            case '임무 기여 시간': $category = "게임"; break;
            case '플레이 시간': $category = "게임"; break;
            case '치른 게임': $category = "게임"; break;
            case '승률': $category = "게임"; break;
            case '무승부 게임': $category = "게임"; break;
            case '패배한 게임': $category = "게임"; break;
            //평균
            case '폭주 시간 - 평균': $category = "평균"; break;
            case '단독 처치 - 평균': $category = "평균"; break;
            case '임무 기여 시간 - 평균': $category = "평균"; break;
            case '임무 기여 처치 - 평균': $category = "평균"; break;
            case '치유 - 평균': $category = "평균"; break;
            case '결정타 - 평균': $category = "평균"; break;
            case '죽음 - 평균': $category = "평균"; break;
            case '준 피해 - 평균': $category = "평균"; break;
            case '처치 - 평균': $category = "평균"; break;
            case '근접 공격 결정타 - 평균': $category = "평균"; break;
            case '자가 치유 - 평균': $category = "평균"; break;
            //기타
            case '방어형 도움': $category = "기타"; break;
            case '방어형 도움 - 평균': $category = "기타"; break;
            case '공격형 도움': $category = "기타"; break;
            case '공격형 도움 - 평균': $category = "기타"; break;
            //스킬
            default: $category = "스킬"; break;
          }
          $this->hero_statistics_data[] = $hero;
          $this->hero_statistics_data[] = $category;
          $this->hero_statistics_data[] = $title;
          $this->hero_statistics_data[] = $hero_data[$title];
        }
        $tmp_result = (!empty($this->hero_statistics_data));
        return $tmp_result;
    }

    /**
     * Get Hero name
     * @access private
     * @param   Array   $stat     : Parsed hero partial data
     * @return  String  $hero     : hero name
     */
    private function GetHeroName($stat) {
        $pattern = '/option-id="[_A-Za-z0-9.+ TorbjörnLúcio:]+">/';
        preg_match_all($pattern, $stat, $hero_name);
        foreach ($hero_name as $row) {
          $row = str_replace("option-id=\"", "", $row);
          $row = str_replace("Torbjörn", "Torbjoern", $row);
          $row = str_replace("Soldier: 76", "Soldier76", $row);
          $row = str_replace("Lúcio", "Lucio", $row);
          $this->hero = str_replace("\">", "", $row);
          }
        return true;
    }

    /**
     * Get hero stat value
     * @access private
     * @param   Array   $stat          : Parsed hero partial data
     * @return  String  $stat_data     : hero stat value
     */
    private function GetStatValue($stat) {
        $pattern = '/<td>[ㄱ-ㅎ|가-힣|a-z|A-Z| -]+<\/td><td>[0-9 +, %.: 시간% ]{1,20}<\/td>/';
        preg_match_all($pattern, $stat, $value, PREG_OFFSET_CAPTURE, 3);
        // $keywords = preg_split("/<\/td><td>/", ($value[0][0][0]));
        foreach ($value[0] as $stat_value) {
          $split = explode ("</td><td>", $stat_value[0]);
          $title = strip_tags($split[0]);
          $value = strip_tags($split[1]);
          $stat_data[$title] = $value;
        }
        return $stat_data;
    }
}

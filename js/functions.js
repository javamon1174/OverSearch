//onload
var data_check;
$( document ).ready(function() {
  handler();
});

//functions
function Frequency_set() {
  var most = $( "#frequency_inner_data");
  most = most[0];
  most = most.firstElementChild.firstElementChild.firstElementChild.innerHTML;
  most = get_hero_id(most);
  most = most[2];
  var url = "https://d1u1mce87gyfbn.cloudfront.net/hero/"+most+"/background-story.jpg";
  $('#background_form').css("background-image", "url("+url+")");
}

function renewal_handle() {
    alert("전적을 새로 받아옵니다.");
    var user = getQueryParams();
    var user_tag = decodeURI(user.user);
    var tag = user_tag.replace("#", "-");
    var result = draw_re_data(tag);
    if(result !== null)
    location.reload();
}

function getQueryParams() {

  var qs = document.location.search;
  qs = qs.split("+").join(" ");
  var params = {},
      tokens,
      re = /[?&]?([^=]+)=([^&]*)/g;

  while (tokens = re.exec(qs)) {
      params[decodeURIComponent(tokens[1])] = decodeURIComponent(tokens[2]);
  }

  return params;
}

function handler() {
    var user = getQueryParams();
    var user_tag = decodeURI(user.user);
    var tag = user_tag.replace("#", "-");
      all_data = draw_data(tag);
      if (all_data !== null) {
          data_to_contents(all_data);
          Frequency_set();
      } else {
          alert("블리자드에서 검색이 되질 않습니다. 배틀태그를 확인해주세요.");
      }
}

function draw_re_data(tag) {
    var data_result = "";
    var url = encodeURI("search.php?tag="+tag+"&renew=true");
    $.ajax({
       type:"POST",
       url:url,
       cache: false,
       async : false,
       success : function(data) {
           try {
               data_result = jQuery.parseJSON( data );
           } catch (e) {
               console.log('draw_re_data : exception issue.');
           }
       },
       error : function(xhr, status, error) {
           console.log(xhr);
       }
    });
    return data_result;
}

function draw_data(tag) {
  var data_result = "";
  var url = encodeURI("search.php?tag="+tag);
  $.ajax({
       type:"POST",
       url:url,
       cache: false,
       async : false,
       success : function(data) {
         try {
             data_result = jQuery.parseJSON( data );
         } catch (e) {
             console.log('draw_data : exception issue.');
             alert("한국서버 아이디인지 확인해주세요. 메인페이지로 이동합니다.");
             window.location.href = "/";
         }
       },
       error : function(xhr, status, error) {
           console.log(xhr);
           alert("한국서버 아이디인지 확인해주세요. 메인페이지로 이동합니다.");
           window.location.href = "/";
       }
   });
   return data_result;
}

function data_to_contents(data) {
  // Summary
  // $("#icon").attr("src", data.Summary[0]["icon"]);
  $("#icon").attr("src", data.Summary[0].icon);
  // $("#username_tag").text(data.Summary[0]["user_name"]);
  $("#username_tag").text(data.Summary[0].user_name);
  $("#user_level").text(data.Summary[0].level);
  $("#com_grade").text(data.Summary[0].com_grade);
  $("#recent_date").text(data.Summary[0].update_date);
  $("#grade").text(data.Summary[0].analy);
  $("#avg_contributions_kill").text(data.Summary[0].avg_contributions_kill);
  $("#avg_contributions_time").text(data.Summary[0].avg_contributions_time);
  $("#avg_damage").text(data.Summary[0].avg_damage);
  $("#avg_death").text(data.Summary[0].avg_death);
  $("#avg_kill").text(data.Summary[0].avg_kill);
  $("#avg_heal").text(data.Summary[0].avg_heal);
  $("#avg_solo_kill").text(data.Summary[0].avg_solo_kill);
  if(data.Summary[0].level_img !== "")
  {
      $("#level_img").attr("src", data.Summary[0].level_img);
      $("#level_img").attr("style", "margin-top:-10px;height:24px;opacity:0.5;"+
                                    "background-color:#ffffff;border-radius:7px;");
  }

  // hero pick
  for (var i = 0; i < data.Frequency.length; i++) {
    // delete data.Frequency[i]["user_idx"];
    // delete data.Frequency[i]["freq_index"];
    var inner_data = "<tr>";
    for(var key in data.Frequency[i]) {
      var hero_id = get_hero_id(data.Frequency[i][key]);
      if(hero_id[1]) {
        if(i < 3) {
          inner_data = inner_data+"<td style=\"border-color:#8C8C8C;color:#FF00DD;\"><a class=\"none_a\" href=\"#"+ hero_id[0] +"\">"+data.Frequency[i][key]+"</a></td>";
        } else {
          inner_data = inner_data+"<td style=\"border-color:#8C8C8C;\"><a class=\"none_a\" href=\"#"+ hero_id[0] +"\">"+data.Frequency[i][key]+"</a></td>";
        }
      } else {
        if(i < 3) {
          inner_data = inner_data+"<td style=\"border-color:#8C8C8C;color:#FF00DD;\">"+data.Frequency[i][key]+"</td>";
        } else {
          inner_data = inner_data+"<td style=\"border-color:#8C8C8C;\">"+data.Frequency[i][key]+"</td>";
        }
      }
    }
    inner_data = inner_data+"</tr>";
    $("#frequency_inner_data").append(inner_data);
  }
  var temp_heroes = "";
  for(var hero in data.Heroes) {
    // var switcher = 0;
    temp_heroes = temp_heroes + "<hr id=\"item_hr\" style=\"margin-top:-13px;\"><h3 id=\""+hero+"\"style=\"color:white;\"><a class=\"none_a\" href=\"#top\">" + hero + "</a></h3><br>";
    for(var cate in data.Heroes[hero]) {
      temp_heroes = temp_heroes + "<h4 style=\"margin-left:3%;text-align:left;font-weight: bold;\">" + cate + "</h4>";
      temp_heroes = temp_heroes +"<div class=\"contents\">";
      for(var title in data.Heroes[hero][cate]) {
        var temp = data.Heroes[hero][cate][title].split("|");
        temp_heroes = temp_heroes + "<div class=\"item\">" +"<div class=\"title\">"+ temp[0]+"</div>"+"<div class=\"spec\">"+ temp[1]+"</div></div>";
        // temp_heroes = temp_heroes + "<div class=\"item\">" +data.Heroes[hero][cate][title] + "</div>";
      }
      if(cate == data.Heroes[hero][data.Heroes[hero].length]) {
        temp_heroes = temp_heroes +"</div>";
      } else {
        temp_heroes = temp_heroes +"</div><hr id=\"item_hr\">";
      }
    }
    temp_heroes = temp_heroes + "</div>";
  }
  $("#heroes_inner_data").append(temp_heroes);
}

function get_hero_id(name) {
  var return_name = "";
  var return_bool = true;
  switch (name) {
    //신규 챔프가 생겼을시 챔프에 따른 case문만 추가하면 확장됨.
    case "리퍼":
      return_name = "Reaper";
      return_url = "reaper";
      break;

    case "트레이서":
      return_name = "Tracer";
      return_url = "tracer";
      break;

    case "한조":
      return_name = "Hanzo";
      return_url = "hanzo";
      break;

    case "토르비욘":
      return_name = "Torbjoern";
      return_url = "torbjorn";
      break;

    case "라인하르트":
      return_name = "Reinhardt";
      return_url = "reinhardt";
      break;

    case "파라":
      return_name = "Pharah";
      return_url = "pharah";
      break;

    case "윈스턴":
      return_name = "Winston";
      return_url = "winston";
      break;

    case "위도우메이커":
      return_name = "Widowmaker";
      return_url = "widowmaker";
      break;

    case "바스티온":
      return_name = "Bastion";
      return_url = "bastion";
      break;

    case "시메트라":
      return_name = "Symmetra";
      return_url = "symmetra";
      break;

    case "젠야타":
      return_name = "Zenyatta";
      return_url = "zenyatta";
      break;

    case "겐지":
      return_name = "Genji";
      return_url = "genji";
      break;

    case "로드호그":
      return_name = "Roadhog";
      return_url = "roadhog";
      break;

    case "맥크리":
      return_name = "McCree";
      return_url = "mccree";
      break;

    case "정크랫":
      return_name = "Junkrat";
      return_url = "junkrat";
      break;

    case "자리야":
      return_name = "Zarya";
      return_url = "zarya";
      break;

    case "솔저: 76":
      return_name = "Soldier76";
      return_url = "soldier-76";
      break;

    case "루시우":
      return_name = "Lucio";
      return_url = "lucio";
      break;

    case "D.Va":
      return_name = "D.Va";
      return_url = "dva";
      break;

    case "메이":
      return_name = "Mei";
      return_url = "mei";
      break;

    case "아나":
      return_name = "Ana";
      return_url = "ana";
      break;

    case "메르시":
      return_name = "Mercy";
      return_url = "mercy";
      break;

    case "솜브라":
      return_name = "Sombra";
      return_url = "sombra";
      break;

    default :
      return_name = name;
      return_bool = false;
      return_url = false;
  }
  var return_value = [return_name, return_bool, return_url];
  return return_value;
}

function validateForm() {
    var input = document.forms.myForm.user.value;
    var chk = input.match(/(.)+#[0-9]{4,5}/g);
    if (input === null || input === "" || chk != document.forms.myForm.user.value || chk === "" || chk === null) {
        alert("입력한 배틀태그를 확인해주세요.");
        return false;
    } else {
      document.forms.myForm.submit();
      return true;
    }
}

$(document).ready(function() {
    $('#favorites').on('click', function(e) {
        var user = getQueryParams();
        var user_tag = decodeURI(user.user);
        var bookmarkURL = window.location.href;
        var bookmarkTitle = "오버서치-"+user_tag;
        var triggerDefault = false;

        if (window.sidebar && window.sidebar.addPanel) {
            // Firefox version < 23
            window.sidebar.addPanel(bookmarkTitle, bookmarkURL, '');
        } else if ((window.sidebar && (navigator.userAgent.toLowerCase().indexOf('firefox') > -1)) || (window.opera && window.print)) {
            // Firefox version >= 23 and Opera Hotlist
            var $this = $(this);
            $this.attr('href', bookmarkURL);
            $this.attr('title', bookmarkTitle);
            $this.attr('rel', 'sidebar');
            $this.off(e);
            triggerDefault = true;
        } else if (window.external && ('AddFavorite' in window.external)) {
            // IE Favorite
            window.external.AddFavorite(bookmarkURL, bookmarkTitle);
        } else {
            // WebKit - Safari/Chrome
            alert((navigator.userAgent.toLowerCase().indexOf('mac') != -1 ? 'Cmd' : 'Ctrl') + '+D 키를 눌러 즐겨찾기에 등록하실 수 있습니다.');
        }
        return triggerDefault;
    });
});
//터치 기능 없앰(확대 X)
document.documentElement.addEventListener('touchstart', function (event) {
  if (event.touches.length > 1) {
    event.preventDefault();
  }
}, false);

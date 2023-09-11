<?php
require_once "function/function.php";
require_once "function/settings.php";

echo banner();
echo banner2();
enterlist:
echo "\n\n [$BL+$WH]$BL Enter your list $WH($DEF eg:$YL list.txt$WH )$GR >> $WH";
$listname = trim(fgets(STDIN));
if(empty($listname) || !file_exists($listname)) {
 echo " [!] Your Fucking list not found [!]".PHP_EOL;
 goto enterlist;
}
$lists = array_unique(explode("\n",str_replace("\r","",file_get_contents($listname))));
enterCountry:
echo "
     $GR>>>$BL COUNTRY $GR<<<$WH
 [$BL 1$WH ]$YL USA$WH     [$BL 2 $WH]$YL CANADA$WH
      [$BL 99 $WH]$YL EXIT$GR
 >> $WH";
$ct = trim(fgets(STDIN));
if($ct == 1) {
 $country = "us";
}else if($ct == 2) {
    $country = "ca";
}else if($ct == 99) {
    exit("\n\n [!] Thanks for Using [!]\n\n");
}else{
    echo "\n\n [!] Country not found [!]".PHP_EOL;
    goto enterCountry;
}

$total = count($lists);
$live = 0;
$die = 0;
$failed = 0;
$limit = 0;
$unknown = 0;
$no = 0;
echo PHP_EOL.PHP_EOL;
foreach ($lists as $list) {
    $no++;

    // EXPLODE
    if(strpos($list, "|") !== false) list($phone, $pass) = explode("|", $list);
    else if(strpos($list, ":") !== false) list($phone, $pass) = explode(":", $list);
    else $phone = $list;
    if(empty($phone)) continue;
    $api = "http://darkxcode.com/validator/carrier?phone=$list&country=$country&proxy=$Proxies&proxyPWD=$proxy_pwd";
    // CURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    $x = curl_exec($ch);
    curl_close($ch);
    $js  = json_decode($x, TRUE);
    $msg           = $js['data']['msg'];
    $code_pohne    = $js['data']['info']['code_phone'];
    $phone_country = $js['data']['info']['country'];
    $city          = $js['data']['info']['city'];
    $carrier       = $js['data']['info']['carrier'];

    if(strpos($x, '"status":"live"')){
        $live++;
        save_file("result/$carrier.txt","$list");
        echo "[$RD$no$DEF/$GR$total$DEF]$GR LIVE$DEF =>$BL $phone$DEF | [$YL CODE$DEF: $MG$code_pohne$DEF ] | [$YL COUNTRY$DEF: $MG$phone_country$DEF ] | [$YL CITY$DEF: $MG$city$DEF ] | [$YL CARRIER$DEF: $MG$carrier$DEF ] | [$YL MSG$DEF: $MG$msg$DEF ] | BY$CY DARKXCODE$DEF (DEMO)".PHP_EOL;
    }else if (strpos($x, '"status":"die"')){
        $die++;
        save_file("result/die.txt","$list");
        echo "[$RD$no$DEF/$GR$total$DEF]$RD DIE$DEF =>$BL $phone$DEF | [$YL MSG$DEF: $MG$msg$DEF ] | BY$CY DARKXCODE$DEF (DEMO)".PHP_EOL;
    }else if (strpos($x, '"status":"failed"')){
        $failed++;
        save_file("result/failed.txt","$list");
        echo "[$RD$no$DEF/$GR$total$DEF]$WH FAILED$DEF =>$BL $phone$DEF | [$YL MSG$DEF: $MG$msg$DEF ] | BY$CY DARKXCODE$DEF (DEMO)".PHP_EOL;
    }else if (strpos($x, '"status":"Too Many Requests"')){
        $limit++;
        save_file("result/limit.txt","$list");
        echo "[$RD$no$DEF/$GR$total$DEF]$BL LIMIT$DEF =>$BL $phone$DEF | [$YL MSG$DEF: $MG$msg$DEF ] | BY$CY DARKXCODE$DEF (DEMO)".PHP_EOL;
    }else{
        $unknown++;
        save_file("result/unknown.txt","$list");
        echo "[$RD$no$DEF/$GR$total$DEF]$YL UNKNOWN$DEF =>$BL $phone$DEF | BY$CY DARKXCODE$DEF (DEMO)".PHP_EOL;
    }

}
//============> END

echo PHP_EOL;
echo "================[DONE]================".PHP_EOL;
echo " DATE          : ".$date.PHP_EOL;
echo " LIVE          : ".$live.PHP_EOL;
echo " DIE           : ".$die.PHP_EOL;
echo " FAILED        : ".$failed.PHP_EOL;
echo " LIMIT         : ".$limit.PHP_EOL;
echo " UNKNOWN       : ".$unknown.PHP_EOL;
echo " TOTAL         : ".$total.PHP_EOL;
echo "======================================".PHP_EOL;
echo "[+] RATIO VALID => $GR".round(RatioCheck($live, $total))."%$DEF".PHP_EOL.PHP_EOL;
echo "[!] NOTE : CHECK AGAIN FILE 'unknown.txt' [!]".PHP_EOL;
echo "This file '".$listname."'".PHP_EOL;
echo "File saved in folder 'result/' ".PHP_EOL.PHP_EOL;


// ==========> FUNCTION

function collorLine($col){
    $data = array(
        "GR" => "\e[32;1m",
        "RD" => "\e[31;1m",
        "BL" => "\e[34;1m",
        "YL" => "\e[33;1m",
        "CY" => "\e[36;1m",
        "MG" => "\e[35;1m",
        "WH" => "\e[37;1m",
        "DEF" => "\e[0m"
    );
    $collor = $data[$col];
    return $collor;
}
?>
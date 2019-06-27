<?php

class fuzzification{
    private $tableDays = 0; //attributes
    private $amountOfClassifying = 0; //intervals
    private $valueOfThreshold = 0;
    private $selectRate = '';
    //loadData return:
    public $loadData = array();
    //selectIntervalPoints return:
    public $tabPointsX = array();
    public $tabPointsY = array();
    public $countPoints = 0;
    //systemInformation return:
    public $tabDec = array(); //renderTab->array
    //buildFuzziTree return:
    public $fuzziData = array(); //build fuzzyTree => $tabDec->fuzzification->$fuzziData
    public $hBTreeArray = array();
    public $chooseAttValue;
    public $chooseAttValueInterval = array();
    public $multiChooseAttValue = array();
    public $multiArray = array();
    private $choosedAttributed = [];

    private $flowersData = [];
    public $minRule = 0;
    public $maxRule = 0;


    public function __construct($tableDays, $amountOfClassifying, $valueOfThreshold, $selectRate)

    {
        $this->tableDays = $tableDays;
        $this->amountOfClassifying = $amountOfClassifying;
        $this->valueOfThreshold = $valueOfThreshold;
        $this->selectRate = $selectRate;
    }

    public function loadData($file){
        require_once('csvimporter.php');
        $importer = new CsvImporter($file, true);
        $data = $importer->get();
        //parser
        $isNumeric=0;
        $firstKeyNumeric=0;

        foreach($data as $key => $value){
            if(is_numeric($data[$key]["data"]))
                $isNumeric++;
        }

        foreach($data as $key => $value){
            if(is_numeric($data[$key]["data"]))
            {
                break;
            }
            else
            {
                $firstKeyNumeric++;
            }
        }
        $parseData = [];
        for($firstKeyNumeric;$firstKeyNumeric<=$isNumeric;$firstKeyNumeric++)
        {
            array_push($this->loadData, str_replace(',','.',$data[$firstKeyNumeric][$this->selectRate]));
        }
        return count($this->loadData);
    }

    public function loadDataPackagesFromNBP(){
        $startDate = "2012";
        $currentYear = date("Y");
        $currentDate = date("Y-m-d");
        for($startDate;$startDate<$currentYear;$startDate++){
            $url="http://api.nbp.pl/api/exchangerates/rates/a/".$this->selectRate."/".$startDate."-01-01/".$startDate."-12-31?format=json";
            $this->loadPackageData($url);
            //echo $startDate."-01-01 => ".$startDate."-12-31 <br>";
        }
        $url="http://api.nbp.pl/api/exchangerates/rates/a/".$this->selectRate."/".$currentYear."-01-01/".$currentDate."?format=json";
        $this->loadPackageData($url);
    }

    public function loadPackageData($url){
        $json = file_get_contents($url);
        $json_decode = json_decode($json, true);
        $tabsOfTab=$json_decode['rates'];
        foreach($tabsOfTab as $tab => $row) {
            array_push($this->loadData, $row['mid']);
        }
    }

    public function uploadFile($postName, $name){
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES[$postName]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        // Check if image file is a actual image or fake image
        // Allow certain file formats
        if($imageFileType != "csv" && $imageFileType != "txt") {
            echo "Check whats do you send me ;)";
            $uploadOk = 0;
        }
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
            // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES[$postName]["tmp_name"], $target_dir.$name)) {
                //echo "The file ". basename( $_FILES[$postName]["name"]). " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }

    public function loadNBPTabTest(){
        $url="http://api.nbp.pl/api/exchangerates/rates/a/".($this->selectRate)."/last/".($this->tableDays+1)."?format=json";
        $this->loadPackageData($url);
    }

    public function systemInformation() {
        //loadData->tabDec
        $lengthLoadData=count($this->loadData);
        $j=0;
        $restFromSharing=1;
        for($i=0;$i<$lengthLoadData+$j;$i++){
            if($i/($this->tableDays+1)==$restFromSharing)
            {
                $j=$this->tableDays+$j;
                $restFromSharing++;
            }
            $this->tabDec[$i]=$this->loadData[$i-$j];
            //echo $this->tabDec[$i].'  ';
        }
    }

    public function selectIntervalPoints() {

        $lastElem=max($this->loadData);
        $firstElem=min($this->loadData);
        //echo $lastElem;
        //echo $firstElem;
        $centerElem=($lastElem+$firstElem)/2;
        $centerPoints = (($this->amountOfClassifying)-2)*3; // center intervals without $firstAndLastPoints.
        //echo $centerPoints;
        $firstAndLastPoints=4; // first and last intervals
        $countPoints=$centerPoints+$firstAndLastPoints; // count all intervals
        $lengthOneInterval=($lastElem-$firstElem)/($countPoints-1);
        $interval=$firstElem; //start intervals
        for($i=0;$i<=$countPoints;$i++){
            $this->tabPointsX[$i]=$interval;
            //echo $tabPointsX[$i].' # ';
            if($i%3==0){
                $this->tabPointsY[$i]=1;
            }
            else{
                $this->tabPointsY[$i]=0;
            }
            $interval=$interval+$lengthOneInterval;
        }
        $this->tabPointsY[0]=1;
        $this->tabPointsY[1]=1;
        $this->tabPointsY[$countPoints]=1;
        $this->tabPointsY[$countPoints-1]=1;
        $this->tabPointsY[$countPoints-2]=1;
//		print_r ($this->tabPointsX);
        //echo "</br>";
//		print_r ($this->tabPointsY);
        //echo "</br>";
        // Defined compartments x,y, now I start to determine the straight line passing through 2 points
        $this->countPoints=$countPoints;

        return ['tabPointsX'=>$this->tabPointsX, 'tabPointsY'=>$this->tabPointsY, 'countPoints'=>$countPoints, 'minRule'=>$firstElem, 'maxRule'=>$lastElem];
    }

    public function compareDataWithIntervals($data=[]) {

        if(!empty($data)){
            $this->tabPointsX = $data['tabPointsX'];
            $this->tabPointsY = $data['tabPointsY'];
            $this->countPoints = $data['countPoints'];
            $this->minRule = $data['minRule'];
            $this->maxRule = $data['maxRule'];
        }

        $data=$this->tabDec;
        //var_dump($data);
        //$data[0]=4.24; // test value
        $lengthLoadData=count($this->tabDec);
        $this->fuzziData = array_fill(0, $lengthLoadData, 0);
        $countPoints = $this->countPoints;
        $tabPointsX = $this->tabPointsX;
        //var_dump ($tabPointsX);
        $tabPointsY = $this->tabPointsY;
        //var_dump ($tabPointsY);
        //echo $countPoints;
        for($countData=0;$countData<$lengthLoadData;$countData++) {
            //var_dump ($this->fuzziData[$countData]);
            //exit();
            $this->fuzziData[$countData] = array_fill(0, $this->amountOfClassifying, 0);
            for ($i = 0; $i < $countPoints; $i++) {
                //echo "<br> data => ".$data[$countData]."<br> tabPointsX i => ".$tabPointsX[$i]."<br> tabPointsX i + 1 => ".$tabPointsX[$i + 1];
                if (($data[$countData] >= $tabPointsX[$i]) && ($data[$countData] < $tabPointsX[$i + 1])) {
                    if ($i == $countPoints - 2) {
                        //3,79
                        $x1 = $tabPointsX[$i];
                        $x2 = $tabPointsX[$i + 1];
                        $y1 = $tabPointsY[$i];
                        $y2 = $tabPointsY[$i + 1];
                        $this->determinationOfStraightLine($x1, $y1, $x2, $y2, $data[$countData], $countData, $i - 1);
                        continue;
                    }
                    if (($i < $countPoints - 2) && (($tabPointsY[$i] == 1) && ($tabPointsY[$i + 1] == 1) && ($tabPointsY[$i + 2] == 0))) {
                        //3,33
                        $x1 = $tabPointsX[$i];
                        $x2 = $tabPointsX[$i + 1];
                        $y1 = $tabPointsY[$i];
                        $y2 = $tabPointsY[$i + 1];
                        $this->determinationOfStraightLine($x1, $y1, $x2, $y2, $data[$countData], $countData, $i);
                        continue;
                    }
                    if (($tabPointsY[$i] == 0) && ($tabPointsY[$i + 1] == 1) && ($tabPointsY[$i + 2] == 0)) {
                        //3,44
                        $x1 = $tabPointsX[$i - 1];
                        $x2 = $tabPointsX[$i + 1];
                        $y1 = 0;
                        $y2 = 1;
                        $this->determinationOfStraightLine($x1, $y1, $x2, $y2, $data[$countData], $countData, $i);
                        continue;
                    }
                    if (($tabPointsY[$i] == 1) && ($tabPointsY[$i + 1] == 0) && ($tabPointsY[$i + 2] == 0)) {
                        //3,51
                        $x1 = $tabPointsX[$i];
                        $x2 = $tabPointsX[$i + 2];
                        $y1 = $tabPointsY[$i];
                        $y2 = $tabPointsY[$i + 2];
                        $this->determinationOfStraightLine($x1, $y1, $x2, $y2, $data[$countData], $countData, $i);
                        continue;
                    }
                    if (($tabPointsY[$i] == 0) && ($tabPointsY[$i + 1] == 0) && ($tabPointsY[$i + 2] == 1)) {
                        //3,58
                        $x1 = $tabPointsX[$i - 1];
                        $x2 = $tabPointsX[$i + 1];
                        $y1 = $tabPointsY[$i - 1];
                        $y2 = $tabPointsY[$i + 1];
                        $this->determinationOfStraightLine($x1, $y1, $x2, $y2, $data[$countData], $countData, $i - 1);
                        $x1 = $tabPointsX[$i];
                        $x2 = $tabPointsX[$i + 2];
                        $y1 = $tabPointsY[$i];
                        $y2 = $tabPointsY[$i + 2];
                        $this->determinationOfStraightLine($x1, $y1, $x2, $y2, $data[$countData], $countData, $i);
                        continue;
                    }

                    if (($tabPointsY[$i] == 0) && ($tabPointsY[$i + 1] == 1) && ($tabPointsY[$i + 2] == 1)) {
                        //3,72
                        $x1 = $tabPointsX[$i];
                        $x2 = $tabPointsX[$i + 1];
                        $y1 = $tabPointsY[$i];
                        $y2 = $tabPointsY[$i + 1];
                        $this->determinationOfStraightLine($x1, $y1, $x2, $y2, $data[$countData], $countData, $i);
                        $x1 = $tabPointsX[$i - 1];
                        $x2 = $tabPointsX[$i + 1];
                        $y1 = $tabPointsY[$i - 1];
                        $y2 = 0;
                        $this->determinationOfStraightLine($x1, $y1, $x2, $y2, $data[$countData], $countData, $i - 2);
                        continue;
                    }

                    if (($tabPointsY[$i] == 1) && ($tabPointsY[$i + 1] == 0) && ($tabPointsY[$i + 2] == 1) && ($tabPointsY[$i + 3] == 0)) {
                        //3,38
                        $x1 = $tabPointsX[$i];
                        $x2 = $tabPointsX[$i + 1];
                        $y1 = $tabPointsY[$i];
                        $y2 = $tabPointsY[$i + 1];
                        $this->determinationOfStraightLine($x1, $y1, $x2, $y2, $data[$countData], $countData, $i);
                        $x1 = $tabPointsX[$i];
                        $x2 = $tabPointsX[$i + 2];
                        if ($i == 1) {
                            $y1 = 0;
                        } else {
                            $y1 = $tabPointsY[$i];
                        }
                        $y2 = $tabPointsY[$i + 2];
                        $this->determinationOfStraightLine($x1, $y1, $x2, $y2, $data[$countData], $countData, $i + 1);
                        continue;
                    }

                    if (($tabPointsY[$i] == 1) && ($tabPointsY[$i + 1] == 0) && ($tabPointsY[$i + 2] == 1) && ($tabPointsY[$i + 3] == 1)) {
                        //3,67
                        $x1 = $tabPointsX[$i];
                        $x2 = $tabPointsX[$i + 2];
                        $y1 = $tabPointsY[$i];
                        $y2 = 0;
                        $this->determinationOfStraightLine($x1, $y1, $x2, $y2, $data[$countData], $countData, $i - 1);
                        continue;
                    }
                }
            }
            //exit();
        }
    }

    public function determinationOfStraightLine($x1,$y1,$x2,$y2,$data,$countData,$i) {

        $a=($y1-$y2)/($x1-$x2);
        $b=$y1-($x1*$a);
        $y=($a*$data)+$b;

        if($y<=1)
        {
            $interval=round($i/2, 0, PHP_ROUND_HALF_DOWN);
            $this->buildFuzziTree($countData,$interval,$y);
        }

    }

    public function sortFuzziData($data){
        $i=1;
        $Rdata = [];

        for($d=1; $d<=$this->tableDays+1;$d++)
            $Rdata[$d]=[];

        foreach ($data as $dzien) {
            $Rdata[$i++][] = $dzien;

            if($i > $this->tableDays+1)
                $i=1;
        }

        return $Rdata;

    }

    public function buildFuzziTree($countData,$i,$value) {

        $this->fuzziData[$countData][$i]=$value;
    }

    public function returnFuzziData() {
//    	var_dump ($this->fuzziData);
    }

    public function sumValuesFromAttribute($data,$interval){
        $countData=count($data);
        $suma=0;

        for($i=0;$i<$countData;$i++)
        {
            $suma=$suma+$data[$i][$interval];
        }
        return $suma;
    }


    private $temp_data_atributes = [];

    public function sumAndMultiplicationValuesFromAttributes($data,$attribute,$attributeDec,$intervalAtt,$intervalDec){
        $countData = count($this->fuzziData)/($this->tableDays+1);

        $suma = 0;

        for ($i = 0; $i < $countData; $i++) {
            $value = $data[$attribute][$i][$intervalAtt] * $data[$attributeDec][$i][$intervalDec];
            $this->temp_data_atributes[$attribute][$i][$intervalAtt] = $value;
            $suma = $suma + $value;
        }
        return $suma;
    }

    public function hAn($data){
        $suma=0;
        $days=$this->tableDays+1;
        $hAn=0;
        $n=count($data);

        for($interval=0;$interval<$this->amountOfClassifying;$interval++)
        {
            $m=$this->sumValuesFromAttribute($data,$interval);
            //var_dump('m=>(int -> '.$interval,$m);
            if($m>0)
                $suma=$suma+($m*log($m,2));
            //var_dump('suma:'.$suma);
        }

        $hAn=$n*log($n,2)-$suma;

        //echo ($hAn);
        //echo '<br>';
        return $hAn;
    }

    public function hBAn($data,$attribute,$attributeParrent){
        //sumy iloczynów par = atr.
        $suma=0;
        $days=$this->tableDays+1;
        $n=count($this->fuzziData)/$days;
        for($interval=0;$interval<$this->amountOfClassifying;$interval++)
        {

            for($intervalDec=0;$intervalDec<$this->amountOfClassifying;$intervalDec++)
            {
                $m=$this->sumAndMultiplicationValuesFromAttributes($data,$attribute,$attributeParrent,$interval,$intervalDec);
                if($m>0)
                {
                    $suma=$suma+(float)$m*log($m,2);
                }
            }
        }
        $hBAn=$n*log($n,2)-$suma;

        return $hBAn;
    }

    public function multiplicationValuesByIntervalChooseAtt($fullData, $attr){
        $data = [];

        // Count intervals -> eg. 4 from a5
        for($intervalOfChooseAttValue=0;$intervalOfChooseAttValue<$this->amountOfClassifying;$intervalOfChooseAttValue++) {
            //var_dump($intervalOfChooseAttValue);

            // $intervalOfChooseAttValue = 0;
            // Count att -> eg. 5 + 1 dec
            for($attribute=1;$attribute<=$this->tableDays+1;$attribute++) {

                // count intervals from att
                for($interval=0;$interval<$this->amountOfClassifying;$interval++)
                {
                    $countData = count($this->fuzziData)/($this->tableDays+1);
                    for ($i = 0; $i < $countData; $i++) {

                        if(!isset($fullData[$attribute][$i][$interval])){

                            // var_dump($i, $interval);
                            // var_dump($fullData);
                            exit('problem multiplicationValuesByIntervalChooseAtt');
                        }

                        $value = $fullData[$attribute][$i][$interval] * $fullData[$attr][$i][$intervalOfChooseAttValue];
                        $data[$intervalOfChooseAttValue][$attribute][$i][$interval] = $value;
                    }

                }
            }
        }

        return $data;
    }

    public function multiplicationDecision($dataOfAttr,$intervalOfAttr,$dataOfDecision){

        $chooseDecisionTab = [];
        $countData = count($this->fuzziData)/($this->tableDays+1);

        for($intervalOfDec=0;$intervalOfDec<$this->amountOfClassifying;$intervalOfDec++)
        {
            $chooseDecisionTab[$intervalOfDec] = 0;
            for ($i = 0; $i < $countData; $i++) {

                $value = $dataOfAttr[$i][$intervalOfAttr] * $dataOfDecision[$i][$intervalOfDec];
                $chooseDecisionTab[$intervalOfDec] = $chooseDecisionTab[$intervalOfDec] + $value;

            }
            $chooseDecisionTab[$intervalOfDec] = ($chooseDecisionTab[$intervalOfDec])/$countData;
        }
        //var_dump($chooseDecisionTab);
        return $chooseDecisionTab;

    }

    public function normalizeValues($data){
        $suma = 0;
        foreach($data as $decision){
            $suma=$suma+$decision;
        }
        foreach($data as &$decision){
            if($suma!=0 OR $decision!=0) {
                $decision = $decision / $suma;
            }
        }
        return $data;
    }

    public function firstHBTree($sortFuzziData, $hB){
        //An : hB + hAn - hBAn
        // $sortFuzziData=$this->sortFuzziData($this->fuzziData);
        // $hB=$this->hAn($sortFuzziData[$this->tableDays+1]);

        for($attribute=1;$attribute<=$this->tableDays;$attribute++) {
            $hAn=$this->hAn($sortFuzziData[$attribute]); // sum column
            $hBAn=$this->hBAn($sortFuzziData, $attribute,$this->tableDays+1); // sum multiplication attrs
            $this->hBTreeArray[$attribute]=$hB+$hAn-$hBAn;
        }

        //var_dump ($this->hBTreeArray);
        $this->chooseAttValue = $this -> chooseTheLargestAttributte($this->hBTreeArray);

        return $this->chooseAttValue;

    }

    public function multiHBTree($sortFuzziData, $hB, $chooseInt, $chooseAtt, $prevAttrs=[]){

        if(!isset($prevAttrs[$chooseAtt]))
            $prevAttrs[$chooseAtt] = $chooseInt;

        $dataMultiplicationByIntervalsChoosenAtWithInterval = $this->multiplicationValuesByIntervalChooseAtt($sortFuzziData, $chooseAtt);
        $this->hBTreeArray = [];

        foreach ($dataMultiplicationByIntervalsChoosenAtWithInterval as $intervalNumber => $dataMultiplicationByIntervalsChoosenAtt) {

            $this->hBTreeArray[$intervalNumber] = [];
            for($attribute=1;$attribute<=$this->tableDays;$attribute++) {

                if(!in_array($attribute, array_keys($prevAttrs))) // We omit the attributes already selected
                {
                    $hAn=$this->hAn($dataMultiplicationByIntervalsChoosenAtt[$attribute]); // suma kolumny
                    $hBAn=$this->hBAn($dataMultiplicationByIntervalsChoosenAtt, $attribute,$this->tableDays+1); // sum multiplication attrs
                    $this->hBTreeArray[$intervalNumber][$attribute] = $hB + $hAn - $hBAn;
                }
            }
        }

        if(count($prevAttrs)==$this->tableDays){

            $decision=$this->multiplicationDecision($dataMultiplicationByIntervalsChoosenAtWithInterval[current($prevAttrs)][$chooseAtt],$chooseInt,$sortFuzziData[$this->tableDays+1]);
            arsort($decision);
            $decision=$this->normalizeValues($decision);
            $decWin=array_keys($decision, current($decision))[0];
            if($decWin>0)
            {
                $this->flowersData[] = ['prevAttrs'=>$prevAttrs,'decisions'=>$decision,'decision0'=>$decWin,'decision1'=>array_keys($decision, next($decision))[0]];
            }
            return ;
        } else {
            $nextArgument = array_keys($this->hBTreeArray[$chooseInt], max($this->hBTreeArray[$chooseInt]))[0];
            $threshold = $this->countThresholdAtt($dataMultiplicationByIntervalsChoosenAtWithInterval[current($prevAttrs)][$nextArgument],$chooseInt);
            if($threshold>$this->valueOfThreshold)
            {
                for($interval=0;$interval<$this->amountOfClassifying;$interval++) {
                    $this->multiHBTree($dataMultiplicationByIntervalsChoosenAtWithInterval[$chooseInt], $hB, $interval, $nextArgument, $prevAttrs);
                }
            }
            else{
                $decision=$this->multiplicationDecision($dataMultiplicationByIntervalsChoosenAtWithInterval[current($prevAttrs)][$chooseAtt],$chooseInt,$sortFuzziData[$this->tableDays+1]);
                arsort($decision);
                $decision=$this->normalizeValues($decision);
                $decWin=array_keys($decision, current($decision))[0];
                if($decWin>0)
                {
                    $this->flowersData[] = ['prevAttrs'=>$prevAttrs,'decisions'=>$decision,'decision0'=>$decWin,'decision1'=>array_keys($decision, next($decision))[0]];
                }
            }
        }
        file_put_contents('dramat.txt', print_r($this->flowersData, true));
    }

    public function chooseTheLargestAttributte($tree){
        $maxValue = -9999;
        foreach($tree as $attribute => $value)
        {
            if($tree[$attribute]>$maxValue)
            {
                $maxValue=$tree[$attribute];
                $iMax=$attribute;
            }
        }
        return $iMax;
    }

    public function multiChooseTheLargestAttributeFromInterval($tree,$interval){
        $maxValue = -9999;
        foreach($tree as $attribute => $value)
        {
            if($tree[$attribute]>$maxValue)
            {
                $maxValue=$tree[$attribute];
                $iMax=$attribute;
            }
        }
        $this->multiChooseAttValue[$interval]=$iMax;
    }

    public function countThresholdAtt($data,$interval){

        $sumaValues = 0;
        foreach($data as $value){
            $sumaValues = $sumaValues + $value[$interval];
        }
        $threshold = 0;
        $threshold = $sumaValues/count($data);
        return $threshold;
    }


    public function go(){

        $sortFuzziData=$this->sortFuzziData($this->fuzziData);
        $hB=$this->hAn($sortFuzziData[$this->tableDays+1]);
        $firstArgument = $this->firstHBTree($sortFuzziData, $hB);

        for($interval=0;$interval<$this->amountOfClassifying;$interval++) {
            $this->multiHBTree($sortFuzziData, $hB, $interval, $firstArgument, []);
        }
        return ($this->flowersData);

    }

    public function classification($flowersData){

        $classificationTab = [];
        $probabilityOfDecision = 0;
        //load Data
        $sortFuzziData=$this->sortFuzziData($this->fuzziData);
        file_put_contents('classfNewTab.txt', print_r($sortFuzziData, true));
        for($i=0;$i<count($this->fuzziData)/($this->tableDays+1);$i++)
        {
            foreach ($flowersData as $flower) {
                $valueClass = 1;
                $max = 0;
                //echo " <br>------------------------DECISION RULE------------------------------<br>";

                foreach ($flower['prevAttrs'] as $attribute => $interval) {
                    $valueClass = $valueClass * $sortFuzziData[$attribute][$i][$interval];
                }

                if ($valueClass > $max) {
                    $max = $valueClass;
                    $decision = $flower['decision0'];
                    $decisionRule = $flower['prevAttrs'];
                    $probabilityOfDecision = current ($flower['decisions'])*100;
                }
                else{
                }
            }
            if(isset($decisionRule) && isset($decision)){
                $classificationTab[] = ['decisionRule' => $decisionRule, 'decision' => $decision, 'decisionForHuman' => $this->resultForView($decision), 'probabilityOfDecision' => (int)$probabilityOfDecision, 'rowTab' => $i];
            }else{
                $classificationTab[] = ['decisionRule' => '', 'decision' => NULL, 'rowTab' => $i];
            }

        }
        file_put_contents('classification.txt', print_r($classificationTab, true));

        return $classificationTab;
    }

    public function resultForView($decision){
        $valueOfInterval = ($this->maxRule - $this->minRule)/$this->amountOfClassifying;
        $valueOfDecision = $this->minRule;
        for ($i=0;$i<=$decision;$i++){
            $valueOfDecision = $valueOfDecision + $valueOfInterval;
        }
        $resultsTab = ['minRule' => $valueOfDecision-$valueOfInterval, 'maxRule' => $valueOfDecision];
        return $resultsTab;
    }

    public function changeIntervalsToHumanVoid($array){
        foreach ($array as $att => $int){
            $int=$this->resultForView($int);
        }
        return $array;
    }

}

?>

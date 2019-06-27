<?php
require_once 'back\configPlatform.php';
require_once 'back\classification.php';
//ini smarty to creating a template system
require_once 'Smarty.class.php';
$smarty = new Smarty;
$smarty->display('header.tpl');
// ---------------------------------------------------
if(isset($_POST["att"]) && isset($_POST["int"]) && isset($_POST["threshold"]) && isset($_POST["selectSource"]) && isset($_POST["selectRate"])){
	if ($_POST["selectSource"] == 'csv') {
		$selectRate = '1'.strtoupper($_POST["selectRate"]);
	}
	else{
		$selectRate = $_POST["selectRate"];
	}
	$fuzzifcation = new fuzzification($_POST["att"], $_POST["int"], $_POST["threshold"], $selectRate);
	if ($_POST["selectSource"] == 'nbp') {
		$fuzzifcation->loadDataPackagesFromNBP();
	} else {
		$fuzzifcation->uploadFile('tabTrain','tabTrain.csv');
		$url_file_csv = "uploads/tabTrain.csv";
		$fuzzifcation->loadData($url_file_csv);
	}
	$fuzzifcation->systemInformation();
	$clone = $fuzzifcation->selectIntervalPoints();
	$fuzzifcation->compareDataWithIntervals();
	$flowersData = $fuzzifcation->go();
	$classification = new fuzzification($_POST["att"], $_POST["int"], $_POST["threshold"], $selectRate);
	if ($_POST["selectSource"] == 'nbp') {
		$classification->loadNBPTabTest();
	} else {
		$classification->uploadFile('tabTest','tabTest.csv');
		$url_file_csv = "uploads/tabTest.csv";
		$countLoadData=$classification->loadData($url_file_csv);
		if($countLoadData<$_POST["att"]){
			$smarty->display('badAttPost.tpl');
            exit();
		}
	}
	$classification->systemInformation();
	$classification->compareDataWithIntervals($clone);
	$resultsTab=$classification->classification($flowersData);
	if(!isset($resultsTab[0]['decision']))
	{
		echo $resultsTab[0]['decision'];
		$smarty->display('badDecision.tpl');
		exit();
	}
	else{
			$attWithInterval = end($resultsTab)['decisionRule'];
			$smarty->assign('arrayAtt',$attWithInterval);
			$smarty->assign('resultsTab',$resultsTab);
			$smarty->assign('decisionLastDay',end($resultsTab)['decision']);
			//echo $classification -> renderTab();
			$minRule=end($resultsTab)['decisionForHuman']['minRule'];
			$smarty->assign('minRule',$minRule);
			$maxRule=end($resultsTab)['decisionForHuman']['maxRule'];
			$smarty->assign('maxRule',$maxRule);
			$smarty->assign('countDays', $_POST["att"]);
			$smarty->display('tabResults.tpl');
	}
}
else{
    $smarty->display('index.tpl');
}
$smarty->display('footer.tpl');
?>

<?php
/* Smarty version 3.1.33, created on 2019-06-11 18:46:59
  from 'C:\xampp\htdocs\templates\index.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_5cffdb03f22606_88289525',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'df1d40a0bf0365f1e3ef5a39dc9c4a565e75319c' => 
    array (
      0 => 'C:\\xampp\\htdocs\\templates\\index.tpl',
      1 => 1560271618,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5cffdb03f22606_88289525 (Smarty_Internal_Template $_smarty_tpl) {
?><button style="display: none;" onclick='window.location.href="index.php";' id="back">Wróć</button>
<form action="index.php" method="post" enctype="multipart/form-data">
	<span id="preloader" style="display:none;"><img src="preloader.gif"><br><br>To może potrwać chwilę.</span>
	<div id="startHome">
		<h1>Co chcesz uzyskać?</h1>
		<select id="selectSource" name="selectSource" required>
			<option disabled selected value> -- wybierz wartość z listy --</option>
			<option id="optionNbp" value="nbp">Sprawdź mi kurs na jutro!</option>
			<option id="optionCsv" value="csv">Pozwól mi wgrać własną bazę wiedzy i zarządzać nią</option>
		</select>
		<div style="display: none;" id="csv">
			<br>Tablica testowa:<br>
			<input id="tabTest" type="file" name="tabTest">
			<br>Tablica treningowa:<br>
			<input id="tabTrain" type="file" name="tabTrain">
		</div>
		<div id="settings" style="display: none;">
			<fieldset>
				Wybierz walutę:<br>
				<select name="selectRate" required>
					<option id="typeRate" value="usd">USD</option>
					<option id="typeRate" value="eur">EURO</option>
					<option id="typeRate" value="uah">UAH</option>
				</select>
				<h2>Ustal parametry algorytmu</h2>
				<i>Zwiększenie liczby analizowanych dni bądź rozpiętości przedziałów znacząco wydłuża czas oczekiwania na decyzje.</i>
				<br><br><br>Podaj liczbę dni analizowanych kursów:<br>
				<input type="number" name="att" value="5" min="3" max="25" required><br>
				Zadeklaruj rozpiętość przedziałów do oszacowania:<br>
				<input type="number" name="int" value="7" min="3" max="15" required><br>
				Próg przydatności analizowanych dni:<br>
				<input type="number" name="threshold" value="0.01" min="0.00001" max="0.1" step="any" required><br>
				<button id="confirm" type="submit">Przejdź dalej!</button>
			</fieldset>
		</div>
	</div>
</form>
<?php echo '<script'; ?>
>
	$("#selectSource").change(function () {

		if ($(this).val() == 'csv') {
			$('#csv').show();
		} else {
			$('#csv').hide();
		}

		if ($(this).val()) {
			$('#settings').show();
		} else {
			$('#csv').hide();
		}

	});

	$("form").submit(function (event) {
		$('#startHome').hide();
		$('#preloader').show();
		$('#back').show();
	});
<?php echo '</script'; ?>
>
<?php }
}

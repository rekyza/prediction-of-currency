<?php
/* Smarty version 3.1.33, created on 2019-06-11 19:30:58
  from 'C:\xampp\htdocs\templates\tabResults.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_5cffe5524f8566_85192396',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '848fee35280798723f958123e2097a612937ab83' => 
    array (
      0 => 'C:\\xampp\\htdocs\\templates\\tabResults.tpl',
      1 => 1560274177,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5cffe5524f8566_85192396 (Smarty_Internal_Template $_smarty_tpl) {
?><button style="display: block;" onclick='window.location.href="index.php";' id="back">Wróć</button>
<div id="diagram">
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['arrayAtt']->value, 'value');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value) {
$__foreach_value_0_saved = $_smarty_tpl->tpl_vars['value'];
?>
    <div class="circle">Dzień <?php echo $_smarty_tpl->tpl_vars['value']->key;?>

        <div class="circleInt">
        <?php if ($_smarty_tpl->tpl_vars['value']->value == $_smarty_tpl->tpl_vars['decisionLastDay']->value) {?>Interwał ten sam<?php }?>
        <?php if ($_smarty_tpl->tpl_vars['value']->value > $_smarty_tpl->tpl_vars['decisionLastDay']->value) {?>Interwał wyższy<?php }?>
        <?php if ($_smarty_tpl->tpl_vars['value']->value < $_smarty_tpl->tpl_vars['decisionLastDay']->value) {?>Interwał niższy<?php }?>
        </div>
    <?php
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_0_saved;
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['arrayAtt']->value, 'value');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value) {
$__foreach_value_1_saved = $_smarty_tpl->tpl_vars['value'];
?>
    </div>
    <?php
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_1_saved;
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

</div>
<div id="legenda">
    <h5>Legenda<fieldset> Wykres porównywany jest z decyzją dnia jutrzejszego.<br>
        Kolejność wyświetlenia dni zachowana jest od najważniejszego dnia reguły.<br>
        Wskazane interwały to porównanie przedziału z przedziałem jutrzejszej decyzji.</fieldset></h5>
    <h3 style="color: #63a89d;">Kurs w dniu jutrzejszym powinien mieścić się w przedziale<br></h3> <?php echo $_smarty_tpl->tpl_vars['minRule']->value;?>
 => <?php echo $_smarty_tpl->tpl_vars['maxRule']->value;?>

</div>
<table class="tabResults">
    <tr>
        <th>Dzień</th>
        <th>Prawdopodobieństwo uznania decyzji</th>
        <th>Decyzja</th>
    </tr>
    <tr>
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['resultsTab']->value, 'arrayTab');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['arrayTab']->key => $_smarty_tpl->tpl_vars['arrayTab']->value) {
$__foreach_arrayTab_2_saved = $_smarty_tpl->tpl_vars['arrayTab'];
?>
    <tr>
        <td><h3><?php echo $_smarty_tpl->tpl_vars['arrayTab']->key+$_smarty_tpl->tpl_vars['countDays']->value+1;?>
</h3></td>
        <td>
        <?php echo $_smarty_tpl->tpl_vars['arrayTab']->value['probabilityOfDecision'];?>
%
        </td>
        <?php ob_start();
echo $_smarty_tpl->tpl_vars['arrayTab']->value['decisionForHuman']['maxRule'];
$_prefixVariable1 = ob_get_clean();
if ('' != $_prefixVariable1) {?><td><?php echo $_smarty_tpl->tpl_vars['arrayTab']->value['decisionForHuman']['minRule'];?>
 => <?php echo $_smarty_tpl->tpl_vars['arrayTab']->value['decisionForHuman']['maxRule'];?>
</td><?php }?>
    </tr>
    <?php
$_smarty_tpl->tpl_vars['arrayTab'] = $__foreach_arrayTab_2_saved;
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    </tr>
</table>
<?php }
}

<?php
/* Smarty version 3.1.33, created on 2019-06-09 18:33:00
  from 'C:\xampp\htdocs\templates\result.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_5cfd34bc077339_21568066',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'a58ce2bb6d28f99a078c38ecf01d6e23066d10a3' => 
    array (
      0 => 'C:\\xampp\\htdocs\\templates\\result.tpl',
      1 => 1560097467,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5cfd34bc077339_21568066 (Smarty_Internal_Template $_smarty_tpl) {
?><button style="display: block;" onclick='window.location.href="index.php";' id="back">Wróć</button>
<div id="diagram">
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['arrayAtt']->value, 'value');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value) {
$__foreach_value_1_saved = $_smarty_tpl->tpl_vars['value'];
?>
        <div class="circle">A<?php echo $_smarty_tpl->tpl_vars['value']->key;?>

    <?php
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_1_saved;
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['arrayAtt']->value, 'value');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value) {
$__foreach_value_2_saved = $_smarty_tpl->tpl_vars['value'];
?>
        </div>
    <?php
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_2_saved;
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

</div>
<h3>Kurs w dniu jutrzejszym powinien mieścić się w przedziale<br></h3> <?php echo $_smarty_tpl->tpl_vars['minRule']->value;?>
 => <?php echo $_smarty_tpl->tpl_vars['maxRule']->value;?>

<?php }
}

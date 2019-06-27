<button style="display: block;" onclick='window.location.href="index.php";' id="back">Wróć</button>
<div id="diagram">
    {foreach $arrayAtt as $value}
        <div class="circle">A{$value@key}
    {/foreach}
    {foreach $arrayAtt as $value}
        </div>
    {/foreach}

</div>
<h3>Kurs w dniu jutrzejszym powinien mieścić się w przedziale<br></h3> {$minRule} => {$maxRule}

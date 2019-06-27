<button style="display: block;" onclick='window.location.href="index.php";' id="back">Wróć</button>
<div id="diagram">
    {foreach $arrayAtt as $value}
    <div class="circle">Dzień {$value@key}
        <div class="circleInt">
        {if $value==$decisionLastDay}Interwał ten sam{/if}
        {if $value>$decisionLastDay}Interwał wyższy{/if}
        {if $value<$decisionLastDay}Interwał niższy{/if}
        </div>
    {/foreach}
    {foreach $arrayAtt as $value}
    </div>
    {/foreach}

</div>
<div id="legenda">
    <h5>Legenda<fieldset> Wykres porównywany jest z decyzją dnia jutrzejszego.<br>
        Kolejność wyświetlenia dni zachowana jest od najważniejszego dnia reguły.<br>
        Wskazane interwały to porównanie przedziału z przedziałem jutrzejszej decyzji.</fieldset></h5>
    <h3 style="color: #63a89d;">Kurs w dniu jutrzejszym powinien mieścić się w przedziale<br></h3> {$minRule} => {$maxRule}
</div>
<table class="tabResults">
    <tr>
        <th>Dzień</th>
        <th>Prawdopodobieństwo uznania decyzji</th>
        <th>Decyzja</th>
    </tr>
    <tr>
        {foreach $resultsTab as $arrayTab}
    <tr>
        <td><h3>{$arrayTab@key+$countDays+1}</h3></td>
        <td>
        {$arrayTab['probabilityOfDecision']}%
        </td>
        {if "" != {$arrayTab['decisionForHuman']['maxRule']}}<td>{$arrayTab['decisionForHuman']['minRule']} => {$arrayTab['decisionForHuman']['maxRule']}</td>{/if}
    </tr>
    {/foreach}
    </tr>
</table>

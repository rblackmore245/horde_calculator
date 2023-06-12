<?php
$plots = [];
$totalinvested = 0;
$brokeeven = false;
setlocale(LC_MONETARY, "en_US");

$plots = createPlot($plots, 500, 201);
$plots = createPlot($plots, 1000, 255);
$plots = createPlot($plots, 100, 174);
$plots = createPlot($plots, 100, 178);
$plots = createPlot($plots, 100, 199);
///
$plots = createPlot($plots, 500, 196);
$plots = createPlot($plots, 100, 146);
$plots = createPlot($plots, 100, 146);
$plots = createPlot($plots, 100, 157);

$takeprofit = 15; // how much in percent to re-invest
$dailyearnings = 0.1;
$pot = 0;
$totalwithdrawn = 0;
$numplots = 0;
$buyplot = 100;
$buyplotdays = 200;
$numberofdays = 0;
$html = "";
while ($dailyearnings > 0) {
    $dailyearnings = 0;
    $dailyreinvest = 0;
    $dailywithdrawal = 0;
    $date = date("Y-m-d", strtotime("+$numberofdays day", time()));

    foreach ($plots as $key => &$value) {
        if ($value["duration"] == 0) {
            unset($plots[$key]);
        } else {
            $earning = ($value["amount"] / 100) * 1;
            $dailyearnings += $earning;
            $reinvest = ($earning / 100) * $takeprofit; // work out re-invest amount
            $withdraw = ($earning / 100) * (100 - $takeprofit); // work out withdrawal amount
            $dailyreinvest += $reinvest;
            $dailywithdrawal += $withdraw;
            $value["duration"]--;
        }
    }

    $weeklyearnings = $weeklyearnings + $dailywithdrawal;
    $monthlyearnings = $monthlyearnings + $dailywithdrawal;
    if ($numberofdays % 29 == 0) {
        // echo "$date - Monthly Earnings: $$monthlyearnings<BR>";
        $monthlyearnings = 0;
    }
    if ($numberofdays % 7 == 0) {
        //  echo "$date - Weekly Earnings: $$weeklyearnings<BR>";
        $weeklyearnings = 0;
    }

    $pot += $dailyreinvest;

    if ($numplots <= 10) {
        if ($pot > $buyplot) {
            //echo "$date - Earned enough to compound Buy new Plot<BR>";
            $html .= " <tr>
                <td colspan='5'><div align='center' class='roi'>$date - Earned enough to compound Buy new Plot</div>   </td>
            </tr>";
            $plots = compoundCreatePlot($plots, $buyplot, $buyplotdays);
            $numplots++;
            $pot = number_format($pot - $buyplot, 2);
        }
    } else {
        $takeprofit = 0;
    }
    $totalwithdrawn += $dailywithdrawal;
    $html .= "  <tr>
        <td><div align='center'>$date</div></td>
        <td><div align='center'>$$dailyearnings</div></td>
        <td><div align='center'>$$dailyreinvest ( $takeprofit% )</div></td>
        <td><div align='center'>$$dailywithdrawal</div></td>
        <td><div align='center'>$pot</div></td>
    </tr>";
    if ($brokeeven === false && $totalwithdrawn > $totalinvested) {
        $html .= " <tr>
            <td colspan='5'><div align='center' class='roi'>$date - Withdrawn: $$totalwithdrawn  Invested: $$totalinvested 100% ROI 🎉🎉🎉🎉</div>   </td>
        </tr>";
        $brokeeven = true;
    }
    $numberofdays++;
}
$html .= "<BR><BR>TOTAL Withdrawn: $totalwithdrawn<BR><BR>";
function compoundCreatePlot($plots, $amount, $duration)
{
    $uuid = rand(0, 999999);
    $plots[$uuid]["amount"] = $amount;
    $plots[$uuid]["duration"] = $duration;
    return $plots;
}

function createPlot($plots, $amount, $duration)
{
    global $totalinvested;
    $totalinvested += $amount;
    $uuid = rand(0, 999999);
    $plots[$uuid]["amount"] = $amount;
    $plots[$uuid]["duration"] = $duration;
    return $plots;
}
$roi = ($totalwithdrawn * 100) / $totalinvested;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Horde Calculator</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" media="screen and (min-width: 930px)" href="desktop.css?<?php echo time(); ?>">
<link rel="stylesheet" media="screen and (max-width: 930px)" href="mobile.css?<?php echo time(); ?>">
</head>
<style>
 
</style>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bakbak+One&family=Jaldi:wght@400;700&family=Montserrat:ital,wght@0,100;0,200;0,300;0,500;0,700;0,900;1,100;1,200;1,300;1,500;1,900&family=Nunito+Sans:ital,wght@0,200;0,400;0,600;1,400;1,600&family=Poppins:ital,wght@0,100;0,300;0,400;0,500;0,600;1,200;1,300;1,400;1,500;1,800&display=swap" rel="stylesheet">
<body>
<div class="backgrounddiv"> </div>  
<div class="dialog_html">
<div class="top_stats">
<div class="input_fields">
  <div class="field_input">
    <div class="field_label">Re-Invest</div>
    <input type="text" name="re-invest" id="re-invest">
  </div>
</div>  
  <div class="stat_item">
    <div class="statlable"> 
    Total Invested:
  </div>
  <div class="statvalue"> 
    $<?php echo number_format($totalinvested); ?>
  </div>
  </div>

  <div class="stat_item">
    <div class="statlable"> 
    Total Return:
  </div>
  <div class="statvalue"> 
    $<?php echo number_format($totalwithdrawn); ?> - ( <?php echo number_format(
     $roi,
     2
 ); ?>% )
  </div>
  </div>
</div>
  <table class="htmltable" border="0" cellpadding="3">
  <tr>
    <td width="124"><div align="center">Date</div></td>
    <td width="143"><div align="center">Daily Earnings</div></td>
    <td width="134"><div align="center">Re-Invest</div></td>
    <td width="213"><div align="center">Taken Profit </div></td>
    <td width="133"><div align="center">Rolling Re-invest pot </div></td>
  </tr>
<?php echo $html; ?>
</table>
</div>  
</body>
</html>

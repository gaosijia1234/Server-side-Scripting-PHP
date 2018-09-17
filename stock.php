<html lang="en">
<?php

$searchPlaceholder =  $_GET["searchBox"];

?>

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="Normalize.css">
    <title>Stock Search</title>
    <script>
        function clearText(){
            document.forms["searchForm"]["clear"].value = "";
        }
        function validateForm() {
            var x = document.forms["searchForm"]["searchBox"].value;
            if (x == ""){
                alert("Please enter Name or Symbol");
                return false;
            }
        }
    </script>
</head>

<body>
<h1><i>Stock Search</i></h1>
<hr>
<form name="searchForm" method="get" action="stock.php">
    Company Name or Symbol: <input type="text" name="searchBox" value="<?php echo $searchPlaceholder; ?>">
    <br><br>
    <input value="Search" name="submit" type="submit" onclick="return validateForm()">
    <input value="Clear" name="reset" type="reset" onclick="clearText()">
    <br><br>
    <a href="https://ihsmarkit.com/products/digital.html">Powered by Markit on Demand</a>
</form>

<?php
// decoding from the website json data
$contents = file_get_contents("http://dev.markitondemand.com/MODApis/Api/v2/Quote/json?symbol=".$_GET["searchBox"]);?><?php
$contents = json_decode($contents, true);

// if clciking on submit button
if($_GET["submit"]) {

// echo the table
echo "<table class='table-stylized'>";
    foreach($contents as $key => $value) {

        $roundedValue = round($value, 2);

        echo '<tr>';

        // associate roundedValue with a marker representing the price change trend of the stock.
        if ($roundedValue < 0) {
            $img = "https://upload.wikimedia.org/wikipedia/commons/thumb/c/c0/Red_Triangle.svg/300px-Red_Triangle.svg.png";
        } else {
            $img = "https://upload.wikimedia.org/wikipedia/commons/thumb/8/8b/Green_Arrow_Up_Darker.svg/1200px-Green_Arrow_Up_Darker.svg.png";
        }

        // define lastPrice for change YTD
        if ($key == "LastPrice"){
            $lastPrice = $value;
        }

        if ($key == "Message" && $value = "No symbol matches found for grnerg. Try another symbol such as MSFT or AAPL, or use the Lookup API."){
            echo "<div>There is no stock information available</div>";
        }
        else {
            if ($key !== "Status") {
                switch ($key) {
                    case "Change":
                        echo '<td class="table-key" width="20%"><strong>' . $key . '</strong></td>';
                        echo '<td class="table-value" width="30%">' . $roundedValue . " " . "<img src=$img width='14px;'" . '</td>';
                        break;
                    case "ChangePercent":
                        echo '<td class="table-key" width="20%"><strong>' . $key . '</strong></td>';
                        echo '<td class="table-value" width="30%">' . $roundedValue . "%" . "<img src=$img width='14px;'" . '</td>';
                        break;
                case "Timestamp":
                    $date = date('Y-m-d g:ia', strtotime($value));

                    echo '<td class="table-key" width="20%"><strong>' . $key . '</strong></td>';
                    echo '<td class="table-value" width="30%">' . $date .'</td>';
                    break;

                    case "MarketCap":
                        echo '<td class="table-key" width="20%"><strong>' . $key . '</strong></td>';
                        echo '<td class="table-value" width="30%">' . round($value / 1000000000, 2) . "B" . '</td>';
                        break;
                    case "Volume":
                        echo '<td class="table-key" width="20%"><strong>' . $key . '</strong></td>';
                        echo '<td class="table-value" width="30%">' . number_format($value) . '</td>';
                        break;
                    case "ChangeYTD":
                        $changeYtd = $lastPrice - $value;
                        $changeYtd = round($changeYtd, 2);
                        if ($changeYtd < 0) {
                            $theValue = "(" . $changeYtd . ")";
                        } else
                            $theValue = $changeYtd;
                        if ($changeYtd < 0) {
                            $img = "https://upload.wikimedia.org/wikipedia/commons/thumb/c/c0/Red_Triangle.svg/300px-Red_Triangle.svg.png";
                        } else {
                            $img = "https://upload.wikimedia.org/wikipedia/commons/thumb/8/8b/Green_Arrow_Up_Darker.svg/1200px-Green_Arrow_Up_Darker.svg.png";
                        }
                        echo '<td class="table-key" width="20%"><strong>' . $key . '</strong></td>';
                        echo '<td class="table-key" width="20%" style="text-align: center">' . $theValue . "<img src=$img width='14px;'" . '</td>';
                        break;
                    case "ChangePercentYTD":
                        echo '<td class="table-key" width="20%"><strong>' . $key . '</strong></td>';
                        echo '<td class="table-value" width="30%">' . $roundedValue . "%" . "<img src=$img width='14px;'" . '</td>';
                        break;
                    default:
                        echo '<td class="table-key" width="20%"><strong>' . $key . '</strong></td>';
                        echo '<td class="table-value" width="30%">' . $value . '</td>';
                        break;
                }
            }
        }
        echo '</tr>';
    }
}
echo "</table>";
?>
</body>
</html>
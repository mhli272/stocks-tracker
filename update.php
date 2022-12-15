<?php 
    if (!isset($_GET['stock'])) {
		$error = "Please provide a stock to search for.";
	}else{
        $host = "303.itpwebdev.com";
        $user = "mhli_dbuser";
        $pass;
        $db = "mhli_stocks";

        // Establish MySQL Connection
        $mysqli = new mysqli($host, $user, $pass, $db);

        // Check for MySQL Connection Errors
        if ( $mysqli->connect_errno ) {
            echo $$mysqli->connect_error;
            exit();
        }

        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, 'https://www.alphavantage.co/query?function=TIME_SERIES_INTRADAY&symbol='.$_GET['stock'].'&interval=5min&apikey=');
        $json = curl_exec($c);
        curl_close($c);
        $stock_numbers = json_decode($json,true);
        $stock_news = null;
        $date = null;
        if(array_key_exists("Error Message", $stock_numbers)){
            $error = "This symbol does not exist. Try searching for another symbol.";
        }else{
            $date = array_key_first($stock_numbers["Time Series (5min)"]); 
            $price = $stock_numbers["Time Series (5min)"][$date]["4. close"];
            $symbol = strtoupper($stock_numbers["Meta Data"]["2. Symbol"]);
            $date = substr(array_key_first($stock_numbers["Time Series (5min)"]), 0, 10);
            
            //first insert into date to get date id if it exists
            $sql = "INSERT INTO dates (date)
            SELECT * FROM (SELECT '$date' AS date) AS temp
            WHERE NOT EXISTS (
                SELECT id FROM dates WHERE date = '$date'
            ) LIMIT 1;";

            
            $result = $mysqli->query($sql);

            if (!$result) {
                echo $mysqli->error;
                $mysqli->close();
                exit();
            }

            //Next, update the stock data
            $sql = "UPDATE stocks, symbols SET stocks.price = $price WHERE symbols.symbol = '$symbol' AND symbols.id = stocks.symbols_id;";
            
            $result = $mysqli->query($sql);
            
            if (!$result) {
                echo $mysqli->error;
                $mysqli->close();
                exit();
            }

            $sql = "UPDATE stocks, dates SET stocks.date_id = dates.id WHERE dates.date='$date' AND stocks.price=$price;";

            $result = $mysqli->query($sql);
    
            if (!$result) {
                echo $mysqli->error;
                $mysqli->close();
                exit();
            }

            $c = curl_init();
            curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($c, CURLOPT_URL, 'https://api.marketaux.com/v1/news/all?symbols='.$_GET['stock'].'&api_token=');
            $json = curl_exec($c);
            curl_close($c);
            $stock_news = json_decode($json, true);
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Update Stocks</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="main.css">
    </head>
    <body>
        <div class="container">
            <div class="row">
                <h1 class="col-12 my-4 text-center text-weight-bold">Update Stocks <?php echo strtoupper($stock_numbers["Meta Data"]["2. Symbol"]); ?></h1>
            </div>
        </div>
        <div class="container">
            <?php if (isset($error) && trim($error) != '') : ?>
                <div class="text-danger font-italic font-weight-bold">
                    <?php echo $error;?>
                </div>
            <?php else: ?>
                <div class="row mb-4">
                    <h4 class="col-4 text-center"><?php echo "Stock Name: ".$symbol?></h4>
                    <h4 class="col-4 text-center"><?php echo "Closing price: ".$price?></h4>
                    <h4 class="col-4 text-center"><?php echo "Date: ".$date?></h4>
                </div>
                <div class="row mb-4">
                    <p class="col-12">Stock data has been updated.</p>
                </div>
                <div class="row mb-4">
                    <h3 class="col-12">News: </h3>
                </div>
                <?php if($stock_news['meta']['found'] == 0): ?>
                    <div class="row">
                        <?php echo "There is no news for this stock." ?>
                    </div>
                <?php else: ?>
                    <?php for($i = 0; $i < $stock_news['meta']['limit']; $i++): ?>
                        <div class="row">
                            <div class="col-6">
                                <?php
                                    echo '<a href="'.$stock_news['data'][$i]['url'].'">'.$stock_news['data'][$i]['title'].'</a>';
                                    echo "<br>";
                                    echo $stock_news['data'][$i]['description'];
                                    echo "<br>";
                                ?>
                            </div>
                            <div class="col-6">
                                <?php
                                    echo '<img src="'.$stock_news['data'][$i]['image_url'].'" alt="news image">';
                                    echo "<br>";
                                ?>
                            </div>
                        </div>
                    <?php endfor?>
                <?php endif; ?>
            <?php endif; ?>
            <div class="row mt-4 mb-4">
                <div class="col-2">
                    <a href="index.php" role="button" class="btn btn-primary">Back to Home</a>
                </div>
                <div class="col-2">
                    <a href="saved_stocks.php" role="button" class="btn btn-primary">View Saved Stocks</a>
                </div>
            </div>
        </div>
    </body>
</html>
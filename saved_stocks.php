<?php 
	
	// Define Credentials
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

    //Fetch all saved stocks
    $sql = "SELECT symbols.symbol AS symbol, stocks.price AS price, dates.date AS date FROM stocks LEFT JOIN symbols ON symbols.id=stocks.symbols_id LEFT JOIN dates ON dates.id=stocks.date_id;";
    
    $results = $mysqli->query($sql);

    if (!$results) {
        echo $mysqli->error;
        $mysqli->close();
        exit();
    }

    $sql = "SELECT AVG(price) AS price FROM stocks;";
    $avg = $mysqli->query($sql);

    if (!$results) {
        echo $mysqli->error;
        $mysqli->close();
        exit();
    }
    
	$mysqli->close();
	$c = curl_init();
	curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($c, CURLOPT_URL, 'https://api.marketaux.com/v1/news/all?api_token=');
	$json = curl_exec($c);
	curl_close($c);
    $stock_news = json_decode($json, true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Saved Stocks</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="main.css">
</head>
<body>
	<div class="container">
		<div class="row">
			<h1 class="col-12 my-4">Saved Stocks</h1>
		</div>
	</div> 
	<div class="container">
        <div class="row">
            <?php if($results->num_rows != 0): ?>
                <p>The average prices of the stocks that you have saved is: <?php echo $avg->fetch_assoc()['price']; ?></p>
            <?php endif; ?>
        </div>
		<div class="row">
			<div class="col-6">
				<table class="table table-hover table-responsive mt-2">
					<thead>
						<tr>
							<th>Symbol</th>
							<th>Price</th>
							<th>Date</th>
                            <th></th>
                            <th></th>
						</tr>
					</thead>
					<tbody>
						<?php while ($row = $results->fetch_assoc()): ?>
							<tr>
								<td>
									<?php echo $row['symbol']; ?>
								</td>
								<td>
									<?php echo $row['price']; ?>
								</td>
								<td>
									<?php echo $row['date']; ?>
								</td>
                                <td>
                                    <a href="update.php?stock=<?php echo $row['symbol']; ?>" class="btn btn-outline-primary">
                                        Update
                                    </a>
                                </td>
								<td>
                                    <a href="delete.php?stock=<?php echo $row['symbol']; ?>" class="btn btn-outline-danger">
                                        Remove
                                    </a>
								</td>
							</tr>
						<?php endwhile; ?>
					</tbody>
				</table>
			</div>
            <div class="col-6">
                <div class="row">
                    <h3 class="col-12">News: </h3>
                </div>
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
            </div>
		</div>
		<div class="row mt-4 mb-4">
			<div class="col-12">
				<a href="index.php" role="button" class="btn btn-primary">Back to Home</a>
			</div>
		</div>
	</div>
</body>
</html>
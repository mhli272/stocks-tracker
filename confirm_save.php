<?php 
	if ( !isset($_GET['symbol'])) {
		$error = "Please fill out all required fields.";
	} else {
		// Define Credentials
		$host = "303.itpwebdev.com";
		$user = "mhli_dbuser";
		$pass;
		$db = "mhli_stocks";

		// 1. Establish MySQL Connection
		$mysqli = new mysqli($host, $user, $pass, $db);

		// Check for MySQL Connection Errors
		if ($mysqli->connect_errno) {
			echo $mysqli->connect_error;
			exit();
		}
        
        $price = $_GET['price'];
        $date = $_GET['date'];
        $symbol = $_GET['symbol'];

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
        
        //Next, insert into symbol table
        $sql = "INSERT INTO symbols (symbol)
        SELECT * FROM (SELECT '$symbol' AS symbol) AS temp
        WHERE NOT EXISTS (
            SELECT id FROM symbols WHERE symbol = '$symbol'
        ) LIMIT 1;";

        $result = $mysqli->query($sql);

		if (!$result) {
			echo $mysqli->error;
			$mysqli->close();
			exit();
		}

		$sql = "SELECT id AS symbols_id FROM symbols WHERE symbol='$symbol';";

		$result = $mysqli->query($sql);

		if (!$result) {
			echo $mysqli->error;
			$mysqli->close();
			exit();
		}

		$symbols_id = $result->fetch_assoc()['symbols_id'];

		$sql = "SELECT id AS date_id FROM dates WHERE date='$date';";

		$result = $mysqli->query($sql);

		if (!$result) {
			echo $mysqli->error;
			$mysqli->close();
			exit();
		}

		$date_id = $result->fetch_assoc()['date_id'];

        
        //insert the whole stock
        $sql = "INSERT INTO stocks (symbols_id, price, date_id) VALUES ($symbols_id, $price, $date_id)";

        $result = $mysqli->query($sql);

		if (!$result) {
			echo $mysqli->error;
			$mysqli->close();
			exit();
		}

		$mysqli->close();
	}
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
			<h1 class="col-12 mt-4">Confirm Save</h1>
		</div>
	</div>
    <div class="container">
        <div class="row mt-4">
            <div class="col-12">

                <?php if ( isset($error) && !empty($error)) : ?>
                    <div class="text-danger font-italic">
                        Please fill out all required fields.
                    </div>
                <?php else : ?>
                    <p><span class="font-italic"><?php echo $symbol ?></span> was successfully added.</p>
                <?php endif; ?>
            </div>
        </div>
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
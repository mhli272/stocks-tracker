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
        
        $symbol = $_GET['stock'];
        $sql = "DELETE FROM stocks
            WHERE 
            (SELECT id FROM symbols WHERE symbol = '$symbol')=symbols_id";

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
        <title>Update Stocks</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="main.css">
    </head>
    <body>
        <div class="container">
            <div class="row">
                <h1 class="col-12 mt-4">Confirm Delete</h1>
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
                        <p><span class="font-italic"><?php echo $symbol ?></span> was successfully deleted.</p>
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
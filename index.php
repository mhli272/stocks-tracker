<?php 
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
		<title>Stocks Tracker</title>
		<style>
			#title{
				margin-top: 10%;
				font-weight: bolder;
				font-size: 60px;
			}
		</style>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
		<link rel="stylesheet" href="main.css">
	</head>
	<body>
		<div class="container">
			<div class="row">
				<h1 id="title" class="col-12 mb-4 text-center">Stocks Tracker</h1>
			</div>
			<div class="row">
				<p class="col-1"></p>
				<p class="col-10 mb-4 text-center">Stocks Tracker equips you with the tools you need to adequetely your hobbies of making money through stocks. Search up the stocks that you are interested in to find its current price and news regarding it. You can then choose to add the stock to a saved list that gives you quick access to your favorite stocks!</p>
			</div>
		</div> 
		<div class="container mb-4">
			<form action="search_results.php" method="GET">
				<div class="form-group row">
					<label for="stock-sym" class="col-sm-3 col-form-label text-sm-right font-weight-bold">Search Stock:</label>
					<div class="col-sm-7">
						<input type="text" class="form-control" id="stock-sym" name="stock">
					</div>
					<p class="col-3"></p>
					<p class="col-9 pt-1">Give the 3-4 letter symbol of a stock and press enter to get the details of that stock.</p>
				</div> 
				<div class="row my-4">
					<div class="col-sm-5">
					</div>
					<div class="col-sm-2">
						<a href="saved_stocks.php" role="button" class="btn btn-primary">View Saved Stocks</a>
					</div>
				</div>
			</form>
		</div> 
		<div class="row">
			<div class="col-12">
				<div class="row">
					<div class="col-3">
					</div>
					<h3 class="col-9">News: </h3>
                </div>
				<?php for($i = 0; $i < $stock_news['meta']['limit']; $i++): ?>
					<div class="row">
						<div class="col-3 text-center">
						</div>
						<div class="col-3">
							<?php
								echo '<a href="'.$stock_news['data'][$i]['url'].'">'.$stock_news['data'][$i]['title'].'</a>';
								echo "<br>";
								echo $stock_news['data'][$i]['description'];
								echo "<br>";
							?>
						</div>
						<div class="col-3">
							<?php
								echo '<img src="'.$stock_news['data'][$i]['image_url'].'" alt="news image">';
								echo "<br>";
							?>
						</div>
					</div>
				<?php endfor?>
			</div>
		</div>
	</body>
</html>
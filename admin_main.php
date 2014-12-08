<?php 
session_start();
include_once 'require/config.php';
include $header_loc;

$current_url = base64_encode($url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
?>

<div class="container">
	<h1 class="row title">Books</h2>
	<div class="row">
		<div class="books col-md-6">
			<!-- Search by title form -->
			<form class="row search" method="post" action="functions/search.php">
				<h5>Search by title:</h5>
				<div class="input-group search-bar">
					<input type="text" name="title" class="form-control" placeholder="title"></input>
					<span class="input-group-btn">
						<button class="btn btn-default">
							<span class="glyphicon glyphicon-search"></span>
						</button>
					</span>
				</div>
				<input type="hidden" name="type" value="search">
				<input type="hidden" name="return_url" value="<?php echo $current_url; ?>">
			</form>
			
			<!-- Button trigger modal -->
			<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#advanceSearchModal">
				Advance search
			</button>
	
			<!-- Modal -->
			<div class="modal fade" id="advanceSearchModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			  <div class="modal-dialog">
			    <div class="modal-content">
			      <div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			        <h4 class="modal-title" id="myModalLabel">Advane search</h4>
			      </div>
			      <form action="functions/search.php" method="post" id="advance">
			      	<div class="modal-body">
			      		<div class="form-group">
			      		    <label>Authors</label>
			      		    <input type="text" name="author" class="form-control" placeholder="">
			      		</div>
			      		<div class="form-group">
			      		    <label>Publisher</label>
			      		    <input type="text" name="publisher" class="form-control" placeholder="">
			      		</div>
			      		<div class="form-group">
			      		    <label>Title</label>
			      		    <input type="text" name="title" class="form-control" placeholder="">
			      		</div>
			      		<div class="form-group">
			      		    <label>Subject</label>
			      		    <input type="text" name="subject" class="form-control" placeholder="">
			      		</div>
						<input type="hidden" name="type" value="advance">
						<input type="hidden" name="return_url" value="<?php echo $current_url; ?>">
						<label>Sort by:</label>
			      		<select name="sort_by" form="advance">
						  	<option value="year">year</option>
						  	<option value="socre">score</option>
						</select>
						<label>Search by:</label>
						<select name="search_by" form="advance">
						 	<option value="and">and</option>
						 	<option value="or">or</option>
						</select>
			      	</div>
			      	<div class="modal-footer">
			      	  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			      	  <button type="submit" class="btn btn-primary">Search</button>
			      	</div>
			      </form>
			    </div>
			  </div>
			</div>
	
			<!-- Display books -->
			<?php
			if (isset($_SESSION['search_title'])) {
				$query = "select * FROM books where title like '%".$_SESSION['search_title']."%'";
			} else if (isset($_SESSION['advance_search'])) {
				$search = $_SESSION['advance_search'];
				if ($search['sort_by'] == 'year') {
					$query = "select ISBN, title, authors, publisher, year, price, subject, copies, format, keywords, avg(score) from (select * from books left outer join feedback on books.ISBN=feedback.book ) search_book where(authors like '%".$search['author']."%' ".$search['search_by']." publisher like'%".$search['publisher']."%' ".$search['search_by']." title like'%".$search['title']."%' ".$search['search_by']." subject like '%".$search['subject']."%') group by ISBN order by year desc;";
					// echo $query;
				} else {
					$query = "select ISBN, title, authors, publisher, year, price, subject, copies, format, keywords, avg(score) from (select * from books left outer join feedback on books.ISBN=feedback.book ) search_book where(authors like '%".$search['author']."%' ".$search['search_by']." publisher like'%".$search['publisher']."%' ".$search['search_by']." title like'%".$search['title']."%' ".$search['search_by']." subject like '%".$search['subject']."%') group by ISBN order by avg(score) desc;";
				}
			} else {
				$query = "select * FROM books";
			}
			$result = $mysqli->query($query);
		
			if ($result) {
				while ($book = $result->fetch_object()) {
			?>
			<div class="book">
				<h3><a href="single_book.php?ISBN=<?php echo $book->ISBN; ?>"><?php echo $book->title; ?></a></h3>
				<h4><?php echo $book->authors; ?></h4>
				<div class="row">
					<div class="col-md-8">
						<p>
							<span>Published by <?php echo $book->publisher; ?></span><br>	
							<span>Quantity Available: <strong><?php echo $book->copies; ?></strong></span><br>
							<span>Year: <?php echo $book->year; ?></span><br>
							<span>Format: <?php echo $book->format; ?></span><br>
						</p>
						
						<?php 
						if ($book->subject) {
							echo '<p>Subject: '.$book->subject.'</p>';
						}
						?></div>
					<div class="col-md-4">
						<p class="price"><span>Price:</span> $<?php echo $book->price; ?></p>
					</div>
				</div>
				<?php 
				if ($book->keywords) {
					echo '<p>Item Description: '.$book->keywords.'</p>';
				}
				 ?>
			</div>
			<?php
				}
			}
			?>
		</div>
</div>
<?php include $footer_loc; ?>
<?php 
session_start();
include_once("db.php");

if (!isset($_SESSION["books"])) {
	$_SESSION["books"] = array();
}

// Empty cart by distroyng current session
if (isset($_GET["emptycart"]) && $_GET["emptycart"] == 1) {
	$return_url = base64_decode($GET["return_url"]);
	session_destroy();
	header('Location:'.$return_url);
}

if (isset($_POST['type']) && $_POST["type"] == 'add') {

	// Get all the post info
	$ISBN = filter_var($_POST["ISBN"], FILTER_SANITIZE_STRING);
	$product_qty = filter_var($_POST["product_qty"], FILTER_SANITIZE_NUMBER_INT);
	$return_url = base64_decode($_POST["return_url"]);
	
	// Query the book info
	$query = "SELECT title, price FROM books WHERE ISBN='" .$ISBN. "' LIMIT 1";
	$results = $mysqli->query($query);

	if ($results) {
		$obj = $results->fetch_object();
		// Prepare array for the session variable
		$new_book = array(array('title'=>$obj->title, 'ISBN'=>$ISBN, 'qty'=>$product_qty, 'price'=>$obj->price));

		if (isset($_SESSION["books"])) {
			$found = false;
			$product = array();

			foreach ($_SESSION["books"] as $cart_itm) {
				if ($cart_itm["ISBN"] == $ISBN) {
					$product[] = array('title'=>$cart_itm["title"], 'ISBN'=>$cart_itm["ISBN"], 'qty'=>$product_qty, 'price'=>$cart_itm["price"]);
					$found = true;
				} else {
					$product[] = array('title'=>$cart_itm["title"], 'ISBN'=>$cart_itm["ISBN"], 'qty'=>$cart_itm["qty"], 'price'=>$cart_itm["price"]);
				}
			}

			if (!$found) {
				$_SESSION["books"] = array_merge($product, $new_book);
			} else {
				$_SESSION["books"] = $product;
			}

		}
	}

	// var_dump($_SESSION["books"]);
	header('Location:'.$return_url);
}

if (isset($_GET["removep"]) && isset($_GET["return_url"]) && isset($_SESSION["books"])) {
	$ISBN = $_GET["removep"];
	$return_url = base64_decode($_GET["return_url"]);
	$product = array();

	foreach ($_SESSION["books"] as $cart_itm) {
		if ($cart_itm["ISBN"] != $ISBN) {
			$product[] = array('title'=>$cart_itm["title"], 'ISBN'=>$cart_itm["ISBN"], 'qty'=>$cart_itm["qty"], 'price'=>$cart_itm["price"]);
		}

		$_SESSION["books"] = $product;
	}

	header('Location:'.$return_url);
}

 ?>
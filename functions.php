<?php
/*
 * Contains all the functions used in the website.
 */
 
if(!session_id()) session_start();

$_SESSION['prefixes'] = array("folder" => "f", "pen organizer" => "po", "planner" => "p");
$_SESSION['tableNames'] = array("f" => "folder", "po" => "pen_organizers", "p" => "planners");
if(!$_SESSION['cart']) $_SESSION['cart'] = array();
if(!$_SESSION['catalog']) $_SESSION['catalog'] = array();

/*
 * Returns true if there is an agent in the database with agent_id = $id.
 * Returns false otherwise.
 */
function isAgentId($id) {
	if(!is_numeric($id)) return false;
	include("db_connection.php");
	
	$agent = $db->query("SELECT * FROM sales_agent WHERE agent_id=$id")->fetchAll(PDO::FETCH_ASSOC);

	return $agent != null;
}

/* 
 * Returns an array containing items.
 * An item's values can be accessed using the following keys:
 * "item_id", "item_name", "srp", "item_type", "personalization", "features", and "dimensions_or_slots"
 * 
 * If $item_name is not empty nor null, the items returned must have an item name equal to $item_name
 * If $category is not empty nor null, the items returned must have an item_type equal to $_SESSION['prefixes'][$category]
 * 
 */
function getItemCatalog($item_name, $category) {
	include("db_connection.php");
	
	if($category == "null") $category = "";
	if(($item_name == "") && ($category == "")) return array();
	
	$queryString = "SELECT item_id, item_name, srp, item_type, personalization_length FROM item";
	$cases = array();
	
	if($category != "") {
		$category = strtolower($category);
		$fieldName = $_SESSION['prefixes'][$category] . "_item_id";
		
		$queryString .= ", " . $_SESSION['tableNames'][$_SESSION['prefixes'][$category]];
		$cases[] = "item.item_id = $fieldName";
	}

	if($item_name != "") {
		$item_name = strtolower($item_name);
		$cases[] = "lower(item.item_name) = \"$item_name\"";
	}
	$queryString .= " WHERE " . implode(" AND ", $cases);
	
	$catalog = $db->query($queryString)->fetchAll(PDO::FETCH_ASSOC);
	
	foreach($catalog as $key => $log) {
		$id = $log["item_id"];
		
		$catalog[$key]["features"] = getFeatures($id);
		$catalog[$key]["dimensions_or_slots"] = getDimensionsOrSlots($id, $log["item_type"]);
	}
	
	return $catalog;
}

/*
 * Returns an array containing either the dimensions or the number of slots the item with item_id equal to $id has.
 * If $item_type is equal to "po", the number of slots are returned.
 * Otherwise, the item's dimenstions will be returned.
 * 
 * $item_type must be the type of the item with item_id equal to $id.
 */
function getDimensionsOrSlots($id, $item_type) {
	include("db_connection.php");
	
	$table = $_SESSION['tableNames'][$item_type];
	$fieldName = $item_type . "_item_id";
	$arr = array();
	
	if($item_type == "po") {
		$query = $db->query("SELECT $table.slots FROM $table WHERE $table.$fieldName = $id")->fetchAll(PDO::FETCH_ASSOC);
		
		$arr[] = $query[0]["slots"];
	}
	else {
		$length = $item_type . "_length";
		$width = $item_type . "_width";
		$height = $item_type . "_height";
		
		$query = $db->query("SELECT $length, $width, $height FROM $table WHERE $table.$fieldName = $id")->fetchAll(PDO::FETCH_ASSOC);
		
		$arr[] = $query[0][$length];
		$arr[] = $query[0][$width];
		$arr[] = $query[0][$height];
	}
	
	return $arr;
}

/*
 * Returns the features of the item with item_id equal to $id.
 */
function getFeatures($id) {
	include("db_connection.php");
	
	$featureQuery = $db->query("SELECT feature.feature FROM feature WHERE feature.item_id = $id")->fetchAll(PDO::FETCH_ASSOC);
	
	$features = array();
	foreach($featureQuery as $featureLog) {
		$features[] = $featureLog["feature"];
	}
	return $features;
}

/*
 * Returns all transactions which satisfy the following conditions
 * If $orderNumber is not empty, the transaction must have an order_number equal to $orderNumber.
 * If $firstAgent is not empty, the transaction must have an agent whose first_name is equal to $firstAgent.
 * If $lastAgent is not empty, the transaction must have an agent whose first_name is equal to $lastAgent.
 * If $firstCustomer is not empty, the transaction must have an agent whose first_name is equal to $firstCustomer.
 * If $lastCustomer is not empty, the transaction must have an agent whose first_name is equal to $lastCustomer.
 * If $dateType is not empty:
 * 		If $dateType is equal to "year", the transaction must have been made in the year specified by $date.
 * 		If $dateType is equal to "month", the transaction must have been made in the month and year specified by $date.
 * 		If $dateType is equal to "week", the transaction must have been made in the week and year specified by $date.
 * 		If $dateType is equal to "day", the transaction must have been made on the exact day specified by $date.
 * Returns null if all parameters are equal to null
 */
function getTransactions($orderNumber, $firstAgent, $lastAgent, $firstCustomer, $lastCustomer, $dateType, $date) {
	if(!$orderNumber AND !$firstAgent AND !$lastAgent AND !$firstCustomer AND !$lastCustomer AND !$dateType) return null;
	
	include("db_connection.php");
	
	$queryString = "SELECT order_form.date_ordered \"date\", CONCAT(sales_agent.first_name, \" \", sales_agent.last_name) \"agent name\",
	CONCAT(customer.first_name, \" \", customer.last_name) \"customer name\", order_form.order_no \"order number\", order_form.total_amount \"total amount\"
	FROM order_form, sales_agent, customer";
	
	$cases = array();
	$cases[] = "order_form.customer_id = customer.customer_id";
	$cases[] = "order_form.agent_id = sales_agent.agent_id";
	if($orderNumber != "") $cases[] = "order_form.order_no = \"$orderNumber\"";
	if($firstAgent != "") $cases[] = "sales_agent.first_name = \"$firstAgent\"";
	if($lastAgent != "") $cases[] = "sales_agent.last_name = \"$lastAgent\"";
	if($firstCustomer != "") $cases[] = "customer.first_name = \"$firstCustomer\"";
	if($lastCustomer != "") $cases[] = "customer.last_name = \"$lastCustomer\"";
	
	if($dateType != "") {
		if($dateType == "day") $cases[] = "order_form.date_ordered = \"$date\"";
		else {
			$cases[] = "YEAR(order_form.date_ordered) = \"" . substr($date, 0, 4) . "\"";
			if($dateType == "week")
				$cases[] = "WEEK(order_form.date_ordered) = \"" . substr($date, 6) . "\"";
			else if($dateType == "month")
				$cases[] = "MONTH(order_form.date_ordered) = \"" . substr($date, 5) . "\"";
		}
	}
	
	if(count($cases) != 0) $queryString .= " WHERE ";
	$queryString .= implode(" AND ", $cases);

	return $db->query($queryString)->fetchAll(PDO::FETCH_ASSOC);
}

/*
 * Returns an array of all the available colors for all entries in stock.
 */
function getColors() {
	include("db_connection.php");
	$colors = array();
	
	$query = $db->query("SELECT DISTINCT color FROM stock")->fetchAll(PDO::FETCH_ASSOC);
	
	foreach($query as $log) {
		$colors[] = $log["color"];
	}
	
	return $colors;
}

/*
 * Adds an item to the cart.
 * This item will have the values of the only item in $_SESSION["catalog"].
 * Also, item will have a quantity of $quantity, a color of $color, a personalization of $personalization, and a discount percentage of $discountCode
 */
function addToCart($quantity, $color, $personalization, $discountCode) {
	include("db_connection.php");
	
	if(!isset($_SESSION["cart"])) $_SESSION["cart"] = array();
	$item = $_SESSION["catalog"][0];
	
	if($personalization == "") $personalization = "(none)";
	if($discountCode == 0) $discountCode = 0;
	
	$arr = array();
	$arr["item_id"] = $item["item_id"];
	$arr["item_name"] = $item["item_name"];
	$arr["personalization"] = $personalization;
	$arr["color"] = $color;
	$arr["qty"] = $quantity;
	$arr["srp"] = $item["srp"];
	$arr["discount"] = $discountCode;
	$arr["total"] = $arr["qty"]*$arr["srp"]*(1 - $arr["discount"]/100);
	
	$_SESSION["cart"][] = $arr;
}

/*
 * Returns the total amount of all the items in $_SESSION["cart"].
 */
function getTotalAmount() {
	$amount = 0;
	
	foreach($_SESSION["cart"] as $log) {
		$amount += $log["total"];
	}
	
	return $amount;
}

/*
 * Returns the value in the text box with name equal to $name.
 */
function getInput($name) {
	return trim(filter_input(INPUT_POST, $name, FILTER_SANITIZE_STRING));
}

/*
 * Affects four different tables in the database.
 * First, creates and order_form with the values coming from the parameters, the system date, and $_SESSION["agent_id"].
 * Next, creates order_items from the items in $_SESSION["cart"].
 * Next, a recipient is added to the recipient table.
 * Next, the stock quantity is reduced for each order item added.
 * Finally, the cart is emptied.
 */
function submitOrder($firstCustomer, $lastCustomer, $sched, $gift, $recipients, $HN, $S, $C) {
	include("db_connection.php");
	
	//Creates order_form
	$sched = explode("T", $sched);
	
	try{
		$results = $db->prepare("INSERT INTO order_form VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$results->bindValue(1, NULL);
		$results->bindValue(2, getCustomerId($firstCustomer, $lastCustomer));
		$results->bindValue(3, $_SESSION['agent_id']);
		$results->bindValue(4, getTotalAmount());
		$results->bindValue(5, date("Y-m-d"));
		$results->bindValue(6, $sched[0]);
		$results->bindValue(7, $sched[1]);
		$results->bindValue(8, $HN);
		$results->bindValue(9, $S);
		$results->bindValue(10, $C);
		$results->bindValue(11, $gift);
		$results->execute();
	}
	catch(Exception $e) {}
	
	//Creates order_item's
	$order_number = $db->query("SELECT LAST_INSERT_ID()")->fetchAll(PDO::FETCH_ASSOC);
	$order_number = $order_number[0]["LAST_INSERT_ID()"];
	
	foreach($_SESSION['cart'] as $item) {
		try{
			$results = $db->prepare("INSERT INTO order_item VALUES (?, ?, ?, ?, ?, ?)");
			$results->bindValue(1, $order_number);
			$results->bindValue(2, $item["item_id"]);
			$results->bindValue(3, $item["qty"]);
			$results->bindValue(4, $item["discount"]);
			$results->bindValue(5, $item["total"]);
			$results->bindValue(6, $item["personalization"]);
			
			$results->execute();
		}
		catch(Exception $e) {}
	}
	
	//Adds recipients
	$recipients = explode(",", $recipients);
	foreach($recipients as $recipient) {
		try{
			$results = $db->prepare("INSERT INTO recipient VALUES (?, ?)");
			$results->bindValue(1, trim($recipient));
			$results->bindValue(2, $order_number);
			
			$results->execute();
		}
		catch(Exception $e) {}
	}
	
	//Updates the inventory
	foreach($_SESSION['cart'] as $item) {
		try{
			$results = $db->prepare("UPDATE stock SET quantity = (quantity - ?) WHERE item_id = ? AND color = ?");
			$results->bindValue(1, $item["qty"]);
			$results->bindValue(2, $item["item_id"]);
			$results->bindValue(3, $item["color"]);
			
			$results->execute();
		}
		catch(Exception $e) {}
	}
	
	//Empties cart
	$_SESSION['cart'] = array();
}

/*
 * Returns the category $item_type corresponds to.
 * Values are based on the contents of $_SESSION.
 */
function getCategory($item_type) {
	foreach($_SESSION["prefixes"] as $key => $value) {
		if($item_type == $value) {
			return $key;
		}
	}
}

/*
 * Returns the customer_id of the customer whose first name is $firstName and whose last name is $lastName.
 * If the customer does not exist yet. The customer is added to the database and the customer_id of the newly added customer is returned.
 */
function getCustomerId($firstName, $lastName) {
	include("db_connection.php");
	
	$queryString = "SELECT customer_id FROM customer WHERE first_name = '$firstName' AND last_name = '$lastName'";
	$query = $db->query($queryString)->fetchAll(PDO::FETCH_ASSOC);
	
	if($query == null) {
		addCustomer($firstName, $lastName);
		return getCustomerId($firstName, $lastName);
	}
	
	return $query[0]["customer_id"];
}

/*
 * Adds a customer whose first_name is $firstName and whose last_name is $lastName to the database.
 */
function addCustomer($firstName, $lastName) {
	include("db_connection.php");
	
	$db->prepare("INSERT INTO customer VALUES(NULL, '$firstName', '$lastName', '" . $_SESSION['agent_id'] . "')")->execute();
}

  /*
  * Obtains item names and quantity of each color.
  *
  * @return array containing item_name, color, quantity
  */
  function inventory() {
    include("db_connection.php");

    $items = ("SELECT item_name, color, quantity FROM item
               JOIN stock ON item.item_id = stock.item_id
               ORDER BY item_name");

    $inventory = $db->query($items);

    return $inventory->fetchAll(PDO::FETCH_ASSOC);
  }

  /*
  * Obtains item names, sum of quantity of units sold, and sum of amount earned for a given date, week, month, or year.
  * Returns null if all parameters are equal to null
  *
  * @param $date date in the format YYYY-MM-DD
  * @param $week week in the format YYYY-WW
  * @param $month month in the format YYYY-MM
  * @param $year year in the format YYYY
  * @return array containing item_name, sum of quantity as units_sold, sum of total_amount as earnings
  */
  function sales($date, $week, $month, $year) {
    if(!$date AND !$week AND !$month AND !$year) return null;
	
	include("db_connection.php");

    if ($date != NULL) {
      $query = ("SELECT item_name, SUM(quantity) AS 'units_sold', SUM(order_item.total_amount) AS 'earnings' FROM item
                 JOIN order_item ON item.item_id = order_item.item_id
                 JOIN order_form ON order_item.order_no = order_form.order_no
                 WHERE date_ordered = '" . $date . "'
                 GROUP BY item_name");
    } elseif ($week != NULL) {
      $input = explode("-", date("Y-m-d", strtotime($week)));
      $first = date('Y-m-d', mktime(0, 0, 0, $input[1], $input[2] - 1, $input[0]));
      $last = date('Y-m-d', mktime(0, 0, 0, $input[1], $input[2] + 5, $input[0]));

      $query = ("SELECT item_name, SUM(quantity) AS 'units_sold', SUM(order_item.total_amount) AS 'earnings' FROM item
                 JOIN order_item ON item.item_id = order_item.item_id
                 JOIN order_form ON order_item.order_no = order_form.order_no
                 WHERE date_ordered >= '" . $first . "' && date_ordered <= '" . $last . "'
                 GROUP BY item_name");
    } elseif ($month != NULL) {
      $input = explode("-", $month);
      $first = date('Y-m-d', mktime(0, 0, 0, $input[1], 1, $input[0]));
      $last = date('Y-m-d', mktime(0, 0, 0, $input[1] + 1, 0, $input[0]));

      $query = ("SELECT item_name, SUM(quantity) AS 'units_sold', SUM(order_item.total_amount) AS 'earnings' FROM item
                 JOIN order_item ON item.item_id = order_item.item_id
                 JOIN order_form ON order_item.order_no = order_form.order_no
                 WHERE date_ordered >= '" . $first . "' && date_ordered <= '" . $last . "'
                 GROUP BY item_name");
    } else {
      $query = ("SELECT item_name, SUM(quantity) AS 'units_sold', SUM(order_item.total_amount) AS 'earnings' FROM item
                 JOIN order_item ON item.item_id = order_item.item_id
                 JOIN order_form ON order_item.order_no = order_form.order_no
                 WHERE date_ordered LIKE '" . $year . "%'
                 GROUP BY item_name");
    }

    $sales = $db->query($query);

    return $sales->fetchAll(PDO::FETCH_ASSOC);
  }

  /*
  * Obtains item names and count of customers for a given date, week, month, or year.
  *
  * @param $item name of the item searched for
  * @param $date date in the format YYYY-MM-DD
  * @param $week week in the format YYYY-WW
  * @param $month month in the format YYYY-MM
  * @param $year year in the format YYYY
  * @return array containing item_name and count of distinct customer_id as count
  */
  function customerCount($item, $date, $week, $month, $year) {
    include("db_connection.php");

    if ($date != NULL) {
      $query = ("SELECT item_name, COUNT(DISTINCT customer_id) AS 'count' FROM item
                 JOIN order_item ON item.item_id = order_item.item_id
                 JOIN order_form ON order_item.order_no = order_form.order_no
                 WHERE item_name = '" . $item . "'
                 && date_ordered = '" . $date . "'");
    } elseif ($week != NULL) {
      $input = explode("-", date("Y-m-d", strtotime($week)));
      $first = date('Y-m-d', mktime(0, 0, 0, $input[1], $input[2] - 1, $input[0]));
      $last = date('Y-m-d', mktime(0, 0, 0, $input[1], $input[2] + 5, $input[0]));

      $query = ("SELECT item_name, COUNT(DISTINCT customer_id) AS 'count' FROM item
                 JOIN order_item ON item.item_id = order_item.item_id
                 JOIN order_form ON order_item.order_no = order_form.order_no
                 WHERE item_name = '" . $item . "'
                 && date_ordered >= '" . $first . "' && date_ordered <= '" . $last . "'");
    } elseif ($month != NULL) {
      $input = explode("-", $month);
      $first = date('Y-m-d', mktime(0, 0, 0, $input[1], 1, $input[0]));
      $last = date('Y-m-d', mktime(0, 0, 0, $input[1] + 1, 0, $input[0]));

      $query = ("SELECT item_name, COUNT(DISTINCT customer_id) AS 'count' FROM item
                 JOIN order_item ON item.item_id = order_item.item_id
                 JOIN order_form ON order_item.order_no = order_form.order_no
                 WHERE item_name = '" . $item . "'
                 && date_ordered >= '" . $first . "' && date_ordered <= '" . $last . "'");
    } else {
      $query = ("SELECT item_name, COUNT(DISTINCT customer_id) AS 'count' FROM item
                 JOIN order_item ON item.item_id = order_item.item_id
                 JOIN order_form ON order_item.order_no = order_form.order_no
                 WHERE item_name = '" . $item . "'
                 && date_ordered LIKE '" . $year . "%'");
    }

    $count = $db->query($query);

    return $count->fetchAll(PDO::FETCH_ASSOC);
  }


  /*
  * Obtains distinct customer names and sales agent names for a given date, week, month, or year.
  * Returns null if all parameters are equal to null
  *
  * @param $date date in the format YYYY-MM-DD
  * @param $week week in the format YYYY-WW
  * @param $month month in the format YYYY-MM
  * @param $year year in the format YYYY
  * @return array containing distinct customer_name and agent_name
  */
  function customer($date, $week, $month, $year) {
	if(!$date AND !$week AND !$month AND !$year) return null;
	  
    include("db_connection.php");

    if ($date != NULL) {
      $query = ("SELECT DISTINCT(CONCAT(customer.first_name, ' ', customer.last_name)) AS 'customer_name',
                 CONCAT(sales_agent.first_name, ' ', sales_agent.last_name) AS 'agent_name' FROM customer
                 LEFT JOIN order_form ON customer.customer_id = order_form.customer_id
                 LEFT JOIN sales_agent ON order_form.agent_id = sales_agent.agent_id
                 WHERE date_ordered = '" . $date . "'");
    } elseif ($week != NULL) {
      $input = explode("-", date("Y-m-d", strtotime($week)));
      $first = date('Y-m-d', mktime(0, 0, 0, $input[1], $input[2] - 1, $input[0]));
      $last = date('Y-m-d', mktime(0, 0, 0, $input[1], $input[2] + 5, $input[0]));

      $query = ("SELECT DISTINCT(CONCAT(customer.first_name, ' ', customer.last_name)) AS 'customer_name',
                 CONCAT(sales_agent.first_name, ' ', sales_agent.last_name) AS 'agent_name' FROM customer
                 LEFT JOIN order_form ON customer.customer_id = order_form.customer_id
                 LEFT JOIN sales_agent ON order_form.agent_id = sales_agent.agent_id
                 WHERE date_ordered >= '" . $first . "' && date_ordered <= '" . $last . "'");
    } elseif ($month != NULL) {
      $input = explode("-", $month);
      $first = date('Y-m-d', mktime(0, 0, 0, $input[1], 1, $input[0]));
      $last = date('Y-m-d', mktime(0, 0, 0, $input[1] + 1, 0, $input[0]));

      $query = ("SELECT DISTINCT(CONCAT(customer.first_name, ' ', customer.last_name)) AS 'customer_name',
                 CONCAT(sales_agent.first_name, ' ', sales_agent.last_name) AS 'agent_name' FROM customer
                 LEFT JOIN order_form ON customer.customer_id = order_form.customer_id
                 LEFT JOIN sales_agent ON order_form.agent_id = sales_agent.agent_id
                 WHERE date_ordered >= '" . $first . "' && date_ordered <= '" . $last . "'");
    } else {
      $query = ("SELECT DISTINCT(CONCAT(customer.first_name, ' ', customer.last_name)) AS 'customer_name',
                 CONCAT(sales_agent.first_name, ' ', sales_agent.last_name) AS 'agent_name' FROM customer
                 LEFT JOIN order_form ON customer.customer_id = order_form.customer_id
                 LEFT JOIN sales_agent ON order_form.agent_id = sales_agent.agent_id
                 WHERE date_ordered LIKE '" . $year . "%'");
    }

    $customer = $db->query($query);

    return $customer->fetchAll(PDO::FETCH_ASSOC);
  }

  /*
  * Obtains distinct customer names, count of order numbers, and sum of amount earned for a given date, week, month, or year.
  *
  * @param $customer name of the customer searched for
  * @param $date date in the format YYYY-MM-DD
  * @param $week week in the format YYYY-WW
  * @param $month month in the format YYYY-MM
  * @param $year year in the format YYYY
  * @return array containing distinct customer_name, count of order_no as orders_made, sum of total_amount as total_amount
  */
  function orders($customer, $date, $week, $month, $year) {
    include("db_connection.php");

    if ($date != NULL) {
      $query = ("SELECT DISTINCT(CONCAT(customer.first_name, ' ', customer.last_name)) AS 'customer_name',
                 COUNT(order_no) AS 'orders_made',
                 SUM(total_amount) AS 'total_amount' FROM customer
                 LEFT JOIN order_form ON customer.customer_id = order_form.customer_id
                 LEFT JOIN sales_agent ON order_form.agent_id = sales_agent.agent_id
                 WHERE CONCAT(customer.first_name, ' ', customer.last_name) = '" . $customer . "'
                 && date_ordered = '" . $date . "'");
    } elseif ($week != NULL) {
      $input = explode("-", date("Y-m-d", strtotime($week)));
      $first = date('Y-m-d', mktime(0, 0, 0, $input[1], $input[2] - 1, $input[0]));
      $last = date('Y-m-d', mktime(0, 0, 0, $input[1], $input[2] + 5, $input[0]));

      $query = ("SELECT DISTINCT(CONCAT(customer.first_name, ' ', customer.last_name)) AS 'customer_name',
                 COUNT(order_no) AS 'orders_made',
                 SUM(total_amount) AS 'total_amount' FROM customer
                 LEFT JOIN order_form ON customer.customer_id = order_form.customer_id
                 LEFT JOIN sales_agent ON order_form.agent_id = sales_agent.agent_id
                 WHERE CONCAT(customer.first_name, ' ', customer.last_name) = '" . $customer . "'
                 && date_ordered >= '" . $first . "' && date_ordered <= '" . $last . "'");
    } elseif ($month != NULL) {
      $input = explode("-", $month);
      $first = date('Y-m-d', mktime(0, 0, 0, $input[1], 1, $input[0]));
      $last = date('Y-m-d', mktime(0, 0, 0, $input[1] + 1, 0, $input[0]));

      $query = ("SELECT DISTINCT(CONCAT(customer.first_name, ' ', customer.last_name)) AS 'customer_name',
                 COUNT(order_no) AS 'orders_made',
                 SUM(total_amount) AS 'total_amount' FROM customer
                 LEFT JOIN order_form ON customer.customer_id = order_form.customer_id
                 LEFT JOIN sales_agent ON order_form.agent_id = sales_agent.agent_id
                 WHERE CONCAT(customer.first_name, ' ', customer.last_name) = '" . $customer . "'
                 && date_ordered >= '" . $first . "' && date_ordered <= '" . $last . "'");
    } else {
      $query = ("SELECT DISTINCT(CONCAT(customer.first_name, ' ', customer.last_name)) AS 'customer_name',
                 COUNT(order_no) AS 'orders_made',
                 SUM(total_amount) AS 'total_amount' FROM customer
                 LEFT JOIN order_form ON customer.customer_id = order_form.customer_id
                 LEFT JOIN sales_agent ON order_form.agent_id = sales_agent.agent_id
                 WHERE CONCAT(customer.first_name, ' ', customer.last_name) = '" . $customer . "'
                 && date_ordered LIKE '" . $year . "%'");
    }

    $orders = $db->query($query);

    return $orders->fetchAll(PDO::FETCH_ASSOC);
  }

  /*
  * Obtains distinct sales agent names for a given date, week, month, or year.
  * Returns null if all parameters are equal to null
  *
  * @param $date date in the format YYYY-MM-DD
  * @param $week week in the format YYYY-WW
  * @param $month month in the format YYYY-MM
  * @param $year year in the format YYYY
  * @return array containing distinct agent_name
  */
  function agent($date, $week, $month, $year) {
	if(!$date AND !$week AND !$month AND !$year) return null;
	
    include("db_connection.php");

    if ($date != NULL) {
      $query = ("SELECT DISTINCT(CONCAT(sales_agent.first_name, ' ', sales_agent.last_name)) AS 'agent_name' FROM sales_agent
                 JOIN order_form ON  sales_agent.agent_id = order_form.agent_id
                 JOIN customer ON order_form.customer_id = customer.customer_id
                 WHERE date_ordered = '" . $date . "'");
    } elseif ($week != NULL) {
      $input = explode("-", date("Y-m-d", strtotime($week)));
      $first = date('Y-m-d', mktime(0, 0, 0, $input[1], $input[2] - 1, $input[0]));
      $last = date('Y-m-d', mktime(0, 0, 0, $input[1], $input[2] + 5, $input[0]));

      $query = ("SELECT DISTINCT(CONCAT(sales_agent.first_name, ' ', sales_agent.last_name)) AS 'agent_name' FROM sales_agent
                 JOIN order_form ON  sales_agent.agent_id = order_form.agent_id
                 JOIN customer ON order_form.customer_id = customer.customer_id
                 WHERE date_ordered >= '" . $first . "' && date_ordered <= '" . $last . "'");
    } elseif ($month != NULL) {
      $input = explode("-", $month);
      $first = date('Y-m-d', mktime(0, 0, 0, $input[1], 1, $input[0]));
      $last = date('Y-m-d', mktime(0, 0, 0, $input[1] + 1, 0, $input[0]));

      $query = ("SELECT DISTINCT(CONCAT(sales_agent.first_name, ' ', sales_agent.last_name)) AS 'agent_name' FROM sales_agent
                 JOIN order_form ON  sales_agent.agent_id = order_form.agent_id
                 JOIN customer ON order_form.customer_id = customer.customer_id
                 WHERE date_ordered >= '" . $first . "' && date_ordered <= '" . $last . "'");
    } else {
      $query = ("SELECT DISTINCT(CONCAT(sales_agent.first_name, ' ', sales_agent.last_name)) AS 'agent_name' FROM sales_agent
                 JOIN order_form ON  sales_agent.agent_id = order_form.agent_id
                 JOIN customer ON order_form.customer_id = customer.customer_id
                 WHERE date_ordered LIKE '" . $year . "%'");
    }

    $agent = $db->query($query);

    return $agent->fetchAll(PDO::FETCH_ASSOC);
  }

  /*
  * Obtains distinct sales agent names, count of distinct customer ids, count of order numbers, and sum of amount earned for a given date, week, month, or year.
  *
  * @param $agent name of the agent searched for
  * @param $date date in the format YYYY-MM-DD
  * @param $week week in the format YYYY-WW
  * @param $month month in the format YYYY-MM
  * @param $year year in the format YYYY
  * @return array containing distinct agent_name, count of distinct customer_id as customer_count, count of order_no as orders, sum of total_amount as earnings
  */
  function numCustomer($agent, $date, $week, $month, $year) {
    include("db_connection.php");

    if ($date != NULL) {
      $query = ("SELECT DISTINCT(CONCAT(sales_agent.first_name, ' ', sales_agent.last_name)) AS 'agent_name',
                 COUNT(DISTINCT(order_form.customer_id)) AS 'customer_count',
                 COUNT(order_no) AS 'orders',
                 SUM(total_amount) AS 'earnings' FROM sales_agent
                 JOIN order_form ON  sales_agent.agent_id = order_form.agent_id
                 JOIN customer ON order_form.customer_id = customer.customer_id
                 WHERE CONCAT(sales_agent.first_name, ' ', sales_agent.last_name) = '" . $agent . "'
                 && date_ordered = '" . $date . "'");
    } elseif ($week != NULL) {
      $input = explode("-", date("Y-m-d", strtotime($week)));
      $first = date('Y-m-d', mktime(0, 0, 0, $input[1], $input[2] - 1, $input[0]));
      $last = date('Y-m-d', mktime(0, 0, 0, $input[1], $input[2] + 5, $input[0]));

      $query = ("SELECT DISTINCT(CONCAT(sales_agent.first_name, ' ', sales_agent.last_name)) AS 'agent_name',
                 COUNT(DISTINCT(order_form.customer_id)) AS 'customer_count',
                 COUNT(order_no) AS 'orders',
                 SUM(total_amount) AS 'earnings' FROM sales_agent
                 JOIN order_form ON  sales_agent.agent_id = order_form.agent_id
                 JOIN customer ON order_form.customer_id = customer.customer_id
                 WHERE CONCAT(sales_agent.first_name, ' ', sales_agent.last_name) = '" . $agent . "'
                 && date_ordered >= '" . $first . "' && date_ordered <= '" . $last . "'");
    } elseif ($month != NULL) {
      $input = explode("-", $month);
      $first = date('Y-m-d', mktime(0, 0, 0, $input[1], 1, $input[0]));
      $last = date('Y-m-d', mktime(0, 0, 0, $input[1] + 1, 0, $input[0]));

      $query = ("SELECT DISTINCT(CONCAT(sales_agent.first_name, ' ', sales_agent.last_name)) AS 'agent_name',
                 COUNT(DISTINCT(order_form.customer_id)) AS 'customer_count',
                 COUNT(order_no) AS 'orders',
                 SUM(total_amount) AS 'earnings' FROM sales_agent
                 JOIN order_form ON  sales_agent.agent_id = order_form.agent_id
                 JOIN customer ON order_form.customer_id = customer.customer_id
                 WHERE CONCAT(sales_agent.first_name, ' ', sales_agent.last_name) = '" . $agent . "'
                 && date_ordered >= '" . $first . "' && date_ordered <= '" . $last . "'");
    } else {
      $query = ("SELECT DISTINCT(CONCAT(sales_agent.first_name, ' ', sales_agent.last_name)) AS 'agent_name',
                 COUNT(DISTINCT(order_form.customer_id)) AS 'customer_count',
                 COUNT(order_no) AS 'orders',
                 SUM(total_amount) AS 'earnings' FROM sales_agent
                 JOIN order_form ON  sales_agent.agent_id = order_form.agent_id
                 JOIN customer ON order_form.customer_id = customer.customer_id
                 WHERE CONCAT(sales_agent.first_name, ' ', sales_agent.last_name) = '" . $agent . "'
                 && date_ordered LIKE '" . $year . "%'");
    }

    $num_cusomter = $db->query($query);

    return $num_cusomter->fetchAll(PDO::FETCH_ASSOC);
  }
?>
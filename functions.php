<?php
  function firstName($fname) {
    include("db_connection.php");

    $sql = 'INSERT INTO users(name) VALUES (?)';

    try {
      $results = $db->prepare($sql);
      $results->bindValue(1, $fname, PDO::PARAM_STR);
      $results->execute();
    } catch (Exception $e) {
      echo "Error: " . $e->getMessage() . "<br />";
      return false;
    }
    return true;
  }

  function test() {
    include("db_connection.php");

    $inventory = $db->query("SELECT * FROM customer WHERE customer_id = 30");

    return $inventory->fetchAll(PDO::FETCH_ASSOC);
  }

  function inventory() {
    include("db_connection.php");

    $items = ("SELECT * FROM item
               JOIN stock ON item.item_id = stock.item_id
               ORDER BY item_name");

    $inventory = $db->query($items);

    return $inventory->fetchAll(PDO::FETCH_ASSOC);
  }

  /*WHERE date_ordered = '" . $date . "'
    $date*/
  function sales() {
    include("db_connection.php");

    $query = ("SELECT item_name, SUM(quantity) AS 'units_sold', SUM(order_item.total_amount) AS 'earnings' FROM item
               JOIN order_item ON item.item_id = order_item.item_id
               JOIN order_form ON order_item.order_no = order_form.order_no
               GROUP BY item_name");

    $sales = $db->query($query);

    return $sales->fetchAll(PDO::FETCH_ASSOC);
  }

  function customerCount($item) {
    include("db_connection.php");

    $query = ("SELECT item_name, count(distinct customer_id) as 'count' from item
               join order_item on item.item_id = order_item.item_id
               join order_form on order_item.order_no = order_form.order_no
               where item_name = '" . $item . "'");

    $count = $db->query($query);

    return $count->fetchAll(PDO::FETCH_ASSOC);
  }

  function customer() {
    include("db_connection.php");

    $query = ("SELECT DISTINCT(customer_name) AS 'customer_name', agent_name FROM customer
               LEFT JOIN order_form ON customer.customer_id = order_form.customer_id
               LEFT JOIN sales_agent ON order_form.agent_id = sales_agent.agent_id");

    $customer = $db->query($query);

    return $customer->fetchAll(PDO::FETCH_ASSOC);
  }

  function orders($customer) {
    include("db_connection.php");

    $query = ("SELECT DISTINCT(customer_name), COUNT(order_no) AS 'orders_made', SUM(total_amount) AS 'total_amount' FROM customer
               LEFT JOIN order_form ON customer.customer_id = order_form.customer_id
               LEFT JOIN sales_agent ON order_form.agent_id = sales_agent.agent_id
               WHERE customer_name = '" . $customer . "'");

    $orders = $db->query($query);

    return $orders->fetchAll(PDO::FETCH_ASSOC);
  }

  function agent() {
    include("db_connection.php");

    $query = ("SELECT DISTINCT(agent_name) FROM sales_agent
               JOIN order_form ON  sales_agent.agent_id = order_form.agent_id
               JOIN customer ON order_form.customer_id = customer.customer_id");

    $agent = $db->query($query);

    return $agent->fetchAll(PDO::FETCH_ASSOC);
  }

  function num_customer($agent) {
    include("db_connection.php");

    $query = ("SELECT DISTINCT(agent_name), COUNT(DISTINCT(order_form.customer_id)) AS 'customer_count',
               COUNT(DISTINCT(order_form.customer_id)) AS 'orders',
               SUM(total_amount) AS 'earnings' FROM sales_agent
               JOIN order_form ON  sales_agent.agent_id = order_form.agent_id
               JOIN customer ON order_form.customer_id = customer.customer_id
               WHERE agent_name = '" . $agent . "'");

    $num_cusomter = $db->query($query);

    return $num_cusomter->fetchAll(PDO::FETCH_ASSOC);
  }
?>

<?php
  function inventory() {
    include("db_connection.php");

    $items = ("SELECT * FROM item
               JOIN stock ON item.item_id = stock.item_id
               ORDER BY item_name");

    $inventory = $db->query($items);

    return $inventory->fetchAll(PDO::FETCH_ASSOC);
  }

  function sales($date, $week, $month, $year) {
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

  function customerCount($item, $date, $week, $month, $year) {
    include("db_connection.php");

    if ($date != NULL) {
      $query = ("SELECT item_name, count(distinct customer_id) AS 'count' FROM item
                 JOIN order_item ON item.item_id = order_item.item_id
                 JOIN order_form ON order_item.order_no = order_form.order_no
                 WHERE item_name = '" . $item . "'
                 && date_ordered = '" . $date . "'");
    } elseif ($week != NULL) {
      $input = explode("-", date("Y-m-d", strtotime($week)));
      $first = date('Y-m-d', mktime(0, 0, 0, $input[1], $input[2] - 1, $input[0]));
      $last = date('Y-m-d', mktime(0, 0, 0, $input[1], $input[2] + 5, $input[0]));

      $query = ("SELECT item_name, count(distinct customer_id) AS 'count' FROM item
                 JOIN order_item ON item.item_id = order_item.item_id
                 JOIN order_form ON order_item.order_no = order_form.order_no
                 WHERE item_name = '" . $item . "'
                 && date_ordered >= '" . $first . "' && date_ordered <= '" . $last . "'");
    } elseif ($month != NULL) {
      $input = explode("-", $month);
      $first = date('Y-m-d', mktime(0, 0, 0, $input[1], 1, $input[0]));
      $last = date('Y-m-d', mktime(0, 0, 0, $input[1] + 1, 0, $input[0]));

      $query = ("SELECT item_name, count(distinct customer_id) AS 'count' FROM item
                 JOIN order_item ON item.item_id = order_item.item_id
                 JOIN order_form ON order_item.order_no = order_form.order_no
                 WHERE item_name = '" . $item . "'
                 && date_ordered >= '" . $first . "' && date_ordered <= '" . $last . "'");
    } else {
      $query = ("SELECT item_name, count(distinct customer_id) AS 'count' FROM item
                 JOIN order_item ON item.item_id = order_item.item_id
                 JOIN order_form ON order_item.order_no = order_form.order_no
                 WHERE item_name = '" . $item . "'
                 && date_ordered LIKE '" . $year . "%'");
    }

    $count = $db->query($query);

    return $count->fetchAll(PDO::FETCH_ASSOC);
  }

  function customer($date, $week, $month, $year) {
    include("db_connection.php");

    if ($date != NULL) {
      $query = ("SELECT DISTINCT(customer_name) AS 'customer_name', agent_name FROM customer
                 LEFT JOIN order_form ON customer.customer_id = order_form.customer_id
                 LEFT JOIN sales_agent ON order_form.agent_id = sales_agent.agent_id
                 WHERE date_ordered = '" . $date . "'");
    } elseif ($week != NULL) {
      $input = explode("-", date("Y-m-d", strtotime($week)));
      $first = date('Y-m-d', mktime(0, 0, 0, $input[1], $input[2] - 1, $input[0]));
      $last = date('Y-m-d', mktime(0, 0, 0, $input[1], $input[2] + 5, $input[0]));

      $query = ("SELECT DISTINCT(customer_name) AS 'customer_name', agent_name FROM customer
                 LEFT JOIN order_form ON customer.customer_id = order_form.customer_id
                 LEFT JOIN sales_agent ON order_form.agent_id = sales_agent.agent_id
                 WHERE date_ordered >= '" . $first . "' && date_ordered <= '" . $last . "'");
    } elseif ($month != NULL) {
      $input = explode("-", $month);
      $first = date('Y-m-d', mktime(0, 0, 0, $input[1], 1, $input[0]));
      $last = date('Y-m-d', mktime(0, 0, 0, $input[1] + 1, 0, $input[0]));

      $query = ("SELECT DISTINCT(customer_name) AS 'customer_name', agent_name FROM customer
                 LEFT JOIN order_form ON customer.customer_id = order_form.customer_id
                 LEFT JOIN sales_agent ON order_form.agent_id = sales_agent.agent_id
                 WHERE date_ordered >= '" . $first . "' && date_ordered <= '" . $last . "'");
    } else {
      $query = ("SELECT DISTINCT(customer_name) AS 'customer_name', agent_name FROM customer
                 LEFT JOIN order_form ON customer.customer_id = order_form.customer_id
                 LEFT JOIN sales_agent ON order_form.agent_id = sales_agent.agent_id
                 WHERE date_ordered LIKE '" . $year . "%'");
    }

    $customer = $db->query($query);

    return $customer->fetchAll(PDO::FETCH_ASSOC);
  }

  function orders($customer, $date, $week, $month, $year) {
    include("db_connection.php");

    if ($date != NULL) {
      $query = ("SELECT DISTINCT(customer_name), COUNT(order_no) AS 'orders_made', SUM(total_amount) AS 'total_amount' FROM customer
                 LEFT JOIN order_form ON customer.customer_id = order_form.customer_id
                 LEFT JOIN sales_agent ON order_form.agent_id = sales_agent.agent_id
                 WHERE customer_name = '" . $customer . "'
                 && date_ordered = '" . $date . "'");
    } elseif ($week != NULL) {
      $input = explode("-", date("Y-m-d", strtotime($week)));
      $first = date('Y-m-d', mktime(0, 0, 0, $input[1], $input[2] - 1, $input[0]));
      $last = date('Y-m-d', mktime(0, 0, 0, $input[1], $input[2] + 5, $input[0]));

      $query = ("SELECT DISTINCT(customer_name), COUNT(order_no) AS 'orders_made', SUM(total_amount) AS 'total_amount' FROM customer
                 LEFT JOIN order_form ON customer.customer_id = order_form.customer_id
                 LEFT JOIN sales_agent ON order_form.agent_id = sales_agent.agent_id
                 WHERE customer_name = '" . $customer . "'
                 && date_ordered >= '" . $first . "' && date_ordered <= '" . $last . "'");
    } elseif ($month != NULL) {
      $input = explode("-", $month);
      $first = date('Y-m-d', mktime(0, 0, 0, $input[1], 1, $input[0]));
      $last = date('Y-m-d', mktime(0, 0, 0, $input[1] + 1, 0, $input[0]));

      $query = ("SELECT DISTINCT(customer_name), COUNT(order_no) AS 'orders_made', SUM(total_amount) AS 'total_amount' FROM customer
                 LEFT JOIN order_form ON customer.customer_id = order_form.customer_id
                 LEFT JOIN sales_agent ON order_form.agent_id = sales_agent.agent_id
                 WHERE customer_name = '" . $customer . "'
                 && date_ordered >= '" . $first . "' && date_ordered <= '" . $last . "'");
    } else {
      $query = ("SELECT DISTINCT(customer_name), COUNT(order_no) AS 'orders_made', SUM(total_amount) AS 'total_amount' FROM customer
                 LEFT JOIN order_form ON customer.customer_id = order_form.customer_id
                 LEFT JOIN sales_agent ON order_form.agent_id = sales_agent.agent_id
                 WHERE customer_name = '" . $customer . "'
                 && date_ordered LIKE '" . $year . "%'");
    }

    $orders = $db->query($query);

    return $orders->fetchAll(PDO::FETCH_ASSOC);
  }

  function agent() {
    include("db_connection.php");

    if ($date != NULL) {
      $query = ("SELECT DISTINCT(agent_name) FROM sales_agent
                 JOIN order_form ON  sales_agent.agent_id = order_form.agent_id
                 JOIN customer ON order_form.customer_id = customer.customer_id
                 WHERE date_ordered = '" . $date . "'");
    } elseif ($week != NULL) {
      $input = explode("-", date("Y-m-d", strtotime($week)));
      $first = date('Y-m-d', mktime(0, 0, 0, $input[1], $input[2] - 1, $input[0]));
      $last = date('Y-m-d', mktime(0, 0, 0, $input[1], $input[2] + 5, $input[0]));

      $query = ("SELECT DISTINCT(agent_name) FROM sales_agent
                 JOIN order_form ON  sales_agent.agent_id = order_form.agent_id
                 JOIN customer ON order_form.customer_id = customer.customer_id
                 WHERE date_ordered >= '" . $first . "' && date_ordered <= '" . $last . "'");
    } elseif ($month != NULL) {
      $input = explode("-", $month);
      $first = date('Y-m-d', mktime(0, 0, 0, $input[1], 1, $input[0]));
      $last = date('Y-m-d', mktime(0, 0, 0, $input[1] + 1, 0, $input[0]));

      $query = ("SELECT DISTINCT(agent_name) FROM sales_agent
                 JOIN order_form ON  sales_agent.agent_id = order_form.agent_id
                 JOIN customer ON order_form.customer_id = customer.customer_id
                 WHERE date_ordered >= '" . $first . "' && date_ordered <= '" . $last . "'");
    } else {
      $query = ("SELECT DISTINCT(agent_name) FROM sales_agent
                 JOIN order_form ON  sales_agent.agent_id = order_form.agent_id
                 JOIN customer ON order_form.customer_id = customer.customer_id
                 WHERE date_ordered LIKE '" . $year . "%'");
    }

    $agent = $db->query($query);

    return $agent->fetchAll(PDO::FETCH_ASSOC);
  }

  function num_customer($agent) {
    include("db_connection.php");

    if ($date != NULL) {
      $query = ("SELECT DISTINCT(agent_name), COUNT(DISTINCT(order_form.customer_id)) AS 'customer_count',
                 COUNT(DISTINCT(order_form.customer_id)) AS 'orders',
                 SUM(total_amount) AS 'earnings' FROM sales_agent
                 JOIN order_form ON  sales_agent.agent_id = order_form.agent_id
                 JOIN customer ON order_form.customer_id = customer.customer_id
                 WHERE agent_name = '" . $agent . "'
                 && date_ordered = '" . $date . "'");
    } elseif ($week != NULL) {
      $input = explode("-", date("Y-m-d", strtotime($week)));
      $first = date('Y-m-d', mktime(0, 0, 0, $input[1], $input[2] - 1, $input[0]));
      $last = date('Y-m-d', mktime(0, 0, 0, $input[1], $input[2] + 5, $input[0]));

      $query = ("SELECT DISTINCT(agent_name), COUNT(DISTINCT(order_form.customer_id)) AS 'customer_count',
                 COUNT(DISTINCT(order_form.customer_id)) AS 'orders',
                 SUM(total_amount) AS 'earnings' FROM sales_agent
                 JOIN order_form ON  sales_agent.agent_id = order_form.agent_id
                 JOIN customer ON order_form.customer_id = customer.customer_id
                 WHERE agent_name = '" . $agent . "'
                 && date_ordered >= '" . $first . "' && date_ordered <= '" . $last . "'");
    } elseif ($month != NULL) {
      $input = explode("-", $month);
      $first = date('Y-m-d', mktime(0, 0, 0, $input[1], 1, $input[0]));
      $last = date('Y-m-d', mktime(0, 0, 0, $input[1] + 1, 0, $input[0]));

      $query = ("SELECT DISTINCT(agent_name), COUNT(DISTINCT(order_form.customer_id)) AS 'customer_count',
                 COUNT(DISTINCT(order_form.customer_id)) AS 'orders',
                 SUM(total_amount) AS 'earnings' FROM sales_agent
                 JOIN order_form ON  sales_agent.agent_id = order_form.agent_id
                 JOIN customer ON order_form.customer_id = customer.customer_id
                 WHERE agent_name = '" . $agent . "'
                 && date_ordered >= '" . $first . "' && date_ordered <= '" . $last . "'");
    } else {
      $query = ("SELECT DISTINCT(agent_name), COUNT(DISTINCT(order_form.customer_id)) AS 'customer_count',
                 COUNT(DISTINCT(order_form.customer_id)) AS 'orders',
                 SUM(total_amount) AS 'earnings' FROM sales_agent
                 JOIN order_form ON  sales_agent.agent_id = order_form.agent_id
                 JOIN customer ON order_form.customer_id = customer.customer_id
                 WHERE agent_name = '" . $agent . "'
                 && date_ordered LIKE '" . $year . "%'");
    }

    $num_cusomter = $db->query($query);

    return $num_cusomter->fetchAll(PDO::FETCH_ASSOC);
  }
?>

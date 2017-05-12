CREATE DATABASE jamaldb;
USE jamaldb;

CREATE TABLE item (
  item_id         INT NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY,
  item_name       VARCHAR(255) NOT NULL,
  item_type       VARCHAR(255) NOT NULL,
  srp             INT NOT NULL,

  CHECK(item_type in ('f', 'po', 'p'))
);

CREATE TABLE customer (
  customer_id     INT NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY,
  first_name   	  VARCHAR(255) NOT NULL,
  last_name		    VARCHAR(255) NOT NULL
);

CREATE TABLE sales_agent (
  agent_id        INT NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY,
  first_name   	  VARCHAR(255) NOT NULL,
  last_name		  VARCHAR(255) NOT NULL
);

CREATE TABLE order_form (
  order_no        INT NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY,
  customer_id     INT NOT NULL,
  agent_id        INT NOT NULL,
  total_amount    INT NOT NULL,
  date_ordered    DATE NOT NULL,
  delivery_date   DATE NOT NULL,
  delivery_time   TIME NOT NULL,
  address_number  VARCHAR(255) NOT NULL,
  address_street  VARCHAR(255) NOT NULL,
  address_city    VARCHAR(255) NOT NULL,
  is_gift         BOOLEAN NOT NULL,

  FOREIGN KEY(customer_id) REFERENCES customer(customer_id),
  FOREIGN KEY(agent_id) REFERENCES sales_agent(agent_id)
);

CREATE TABLE order_item (
  order_no        INT NOT NULL,
  item_id         INT NOT NULL,
  quantity        INT NOT NULL,
  discount        INT NOT NULL,
  total_amount    INT NOT NULL,
  personalization VARCHAR(255),

  FOREIGN KEY(order_no) REFERENCES order_form(order_no),
  FOREIGN KEY(item_id) REFERENCES item(item_id)
);

CREATE TABLE folder (
  f_item_id       INT NOT NULL,
  f_length        VARCHAR(255) NOT NULL,
  f_width         VARCHAR(255) NOT NULL,
  f_height        VARCHAR(255) NOT NULL,

  FOREIGN KEY(f_item_id) REFERENCES item(item_id)
);

CREATE TABLE pen_organizers (
  po_item_id      INT NOT NULL,
  slots           INT NOT NULL,

  FOREIGN KEY(po_item_id) REFERENCES item(item_id)
);

CREATE TABLE planners (
  p_item_id       INT NOT NULL,
  p_length        VARCHAR(255) NOT NULL,
  p_width         VARCHAR(255) NOT NULL,
  p_height        VARCHAR(255) NOT NULL,

  FOREIGN KEY(p_item_id) REFERENCES item(item_id)
);

CREATE TABLE recipient (
  recipient_name  VARCHAR(255) NOT NULL PRIMARY KEY,
  order_no        INT NOT NULL,

  FOREIGN KEY(order_no) REFERENCES order_form(order_no)
);

CREATE TABLE feature (
  item_id         INT NOT NULL,
  feature         VARCHAR(255) NOT NULL,

  FOREIGN KEY(item_id) REFERENCES item(item_id)
);

CREATE TABLE stock (
  item_id         INT NOT NULL,
  quantity        INT NOT NULL,
  color           VARCHAR(255) NOT NULL,

  FOREIGN KEY(item_id) REFERENCES item(item_id)
);

CREATE TABLE personalization (
  item_id         INT NOT NULL,
  personalization INT NOT NULL,

  FOREIGN KEY(item_id) REFERENCES item(item_id)
);
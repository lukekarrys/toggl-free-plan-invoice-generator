# Setup Instructions

## includes/config/setup.php

### Mandatory

- Define `BASE_URL`
- Define `TOGGL_API_URL`
- Define `TOGGL_API_TOKEN`

### Optional

To activate the ability to add arbitrary amounts to any invoice:

- Define `ALLOW_EXTRAS` as `true`
- Define `DB_CONN`, `DB_USER`, `DB_PASS`
- Create MySQL database by running:
`CREATE TABLE extra_items (AUTONUMBER INT NOT NULL AUTO_INCREMENT PRIMARY KEY, CLIENT INT, PROJECT INT, DESCRIPTION TEXT, TOTAL DECIMAL(19,4));`

To add contact info to invoice:
- Define `DISPLAY_CONTACT` as `true`
- Define `CONTACT_` constants appropriately


## includes/config/clients.php

### Define defaults

Define default values array:
```
$default_clients_values = array(
  "preHourly" => 500, // An hourly wage
  "postHourly" => 1000, // An hourly wage
  "hourlyChange" => "9/1/2011", // A date, all taks before this date will use preHourly, all after will use postHourly
  "dontRound" => false // Whether to round task times to the nearest quarter hour
);
```

### Add clients

Clients is an associative array where each key is the client ID (defined by Toggl) and the value is an associative array idential to `$$default_clients_values`.

For example:

```
$clients = array(
  55555 => array(
    "preHourly" => $default_clients_values["preHourly"],        // Use default
    "postHourly" => 750,                                        // Override default
    "hourlyChange" => "11/11/2011",                             // Override default
    "dontRound" => true                                         // Override default
  ),
  77777 => $default_clients_values                              // Use all defaults
);
```

## Styling

- `styles/s_style.css` holds all the screen styles
- `styles/p_style.css` holds all the print styles
- `images/logo.png` is the screen logo
- `images/logo-print.png` is the print logo


## Printing

I use Chrome or Safari to print the page and then OS X to save the page as a PDF instead of actualling printing it. Pretty simple.
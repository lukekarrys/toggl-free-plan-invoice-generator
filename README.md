# Setup Instructions

## public/includes/config/setup.php

- `mv public/includes/config/setup-sample.php public/includes/config/setup.php`

### Mandatory

- Define `BASE_URL`
- Define `TOGGL_API_TOKEN`

### Optional

To activate the ability to add arbitrary amounts to any invoice:

- Define `ALLOW_EXTRAS` as `true`
- Define `DB_CONN`, `DB_USER`, `DB_PASS`
- Create MySQL database by running:
`CREATE TABLE extra_items (AUTONUMBER INT NOT NULL AUTO_INCREMENT PRIMARY KEY, CLIENT INT, PROJECT INT, DESCRIPTION TEXT, TOTAL DECIMAL(19,4));`

To add contact info to invoice:

- Define `DISPLAY_CONTACT` as `true`
- Define `CONTACT_*` constants appropriately


## public/includes/config/clients.php

- `mv public/includes/config/clients-sample.php public/includes/config/clients.php`

### Define defaults

Define default values array:

```
$default_clients_values = array(
  "preHourly" => 500, // An hourly wage
  "postHourly" => 1000, // An hourly wage
  "hourlyChange" => "9/1/2011", // A date, all tasks before this date will use preHourly, all after will use postHourly
  "dontRound" => false // Whether to round task times to the nearest quarter hour
);
```

### Add clients

`$clients` is an associative array where each key is the client ID (defined by Toggl) and the value is an associative array whose keys are identical to `$default_clients_values`. Use `$default_clients_values["KEY"]` to use a defaul client value for one key but override others.

For example:

```
$clients = array(
  00000 => array(
    "preHourly" => 2000,                                   // Override default
    "postHourly" => 5000,                                  // Override default
    "hourlyChange" => "1/1/2013",                          // Override default
    "dontRound" => $default_clients_values["dontRound"],   // Use default
  )
);
```

*Note: If a client is omitted from the `$clients` array but exists in Toggl, it will automatically be included with all the defaults.*

## Password Protection

- `mv public/.htaccess-sample public/.htaccess`
- `mv public/.htpasswd-sample public/.htpasswd`

You will need to edit `AuthUserFile` in the `.htaccess` file to point to where the `.htpasswd` file will be on your server. It needs to be an absolute path. Then you can use an [htpasswd generator site](http://www.htaccesstools.com/htpasswd-generator/) to generate an entry to be pasted into your `.htpasswd` file.

## Clients and Projects

A client will only show up in the clients dropdown if it has at least 1 non-archived project.

## Styling

- `styles/s_style.css` holds all the screen styles
- `styles/p_style.css` holds all the print styles
- `images/logo.png` is the screen logo
- `images/logo-print.png` is the print logo


## Printing

I use Chrome or Safari to print the page and then OS X to save the page as a PDF instead of actualling printing it. Pretty simple.
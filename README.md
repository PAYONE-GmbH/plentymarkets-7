#  BS Payone plugin for plentymarkets 7

## Plugin description and requirements

See the [plugin description](./meta/documents/user_guide_de.md) for the plentymarkets marketplace.

## Requirements

The plugin has been developed for the Ceres template. The plugins IO and Ceres 
are required and have to be active.

## Installation

A github account es required to be able to install the plugin. In your plentymarkets backend navigate to 
"Plugins -> GIT". Now click "New plugin" int the bottom left corner:

![Add new plugin](./meta/images/plugin_from_git.png)

A popup will prompt you to enter your github credentials:

![credential form](./meta/images/git_credentials.png)
 
It is recommended to use an account which has only been created 
for the plugin installation. After adding the plugin it will show up in the pugin overview. The provisioning process is 
the same as for plugins installed from the plentymarkets marketplace.

## Configuration

Set up the payone account and configure the payments method in the plentymarkets backend:

* Navigate to "Plugins"

* Double click on the payone pluging:

![Plugin list](./meta/images/plugin_list.png)

* Enter settings in each configuration tab:

![config](./meta/images/config.png)

* Save changes

### Setting up event actions 

To fully integrate the plugin event actions have to be set up for capture, refund and returns. The event actions 
"Payone | Capture order" and "Payone | Refund order" have to be set up according to your workflow.

### Show clearing data on order success page

To add a text to the order success page on how to fullfill the paymen set up the payone payment containers.
The "Payone Order Confirmation Page Payment Data" container needs to be added to the 
 "Order confirmation: Additional payment information" block.
 
### Integrate payment methods into the checkout
 
To fully integrate the plugin the template container "Payone Checkout JS" has to be added to 
"Script loader: After scripts loaded".

## Tests

To run the unit tests execute 

```phpunit --exclude-group online```

To run tests agains the Payone API, set up your merchant credentials in the phpunit.ini file (see phpunit.ini.dist as 
reference for the field names).

Run ```phpunit```

## Changelog

See the [changelog](./CHANGELOG.md).
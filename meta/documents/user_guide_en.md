<div class="alert alert-warning" role="alert">
   The PAYONE plugin has been developed for use with the online store Ceres and only works with its structure or other template plugins. The plugins Ceres and IO have to be activated so that the Payone plugin can be used.
</div>
![Payone Banner][https://www.psg-projektmanagement.de/wp-content/uploads/2020/08/payone-aktion.jpg]

# PAYONE Payment for plentymarkets

The plentymarkets PAYONE plugin offers you access to international as well as local payment methods. In addition, the plugin offers the advantages of an integrated risk management system, automated refunds and swift processing of returns.

The plugin currently offers the following payment methods:

* Secure Invoice
* Visa & MasterCard (incl. Maestro)
* American Express - Integration of your existing acceptance contract
* SEPA direct debit
* giropay - online bank transfer Germany
* Sofortüberweisung - Online bank transfer international
* Transfer - Cash in advance & Invoice & Cash on Delivery
* PayPal - Integration of your PayPal account
* Amazon Pay - Integration of your Amazon Pay account

## First steps

The use requires a PAYONE account as well as PAYONE access data. If you are not a PAYONE customer yet and thus do not have a PAYONE account, please contact:

PSG Projektmanagement GmbH <br>
Meergässle 4 <br>
89180 Berghülen <br>
Phone: 07344-9592588 <br>
E-mail: plenty@psg-projektmanagement.de <br>
Website: http://www.psg-projektmanagement.de <br>
Or use the following registration form: <br>
https://www.psg-projektmanagement.de/payone-plentymarkets/

<div class="alert alert-warning" role="alert">
   You can only get a PAYONE account via the partner mentioned above. In order not to compromise a smooth process, please do not contact PAYONE directly.
</div>

Upon receipt of your access data, log in to the PAYONE merchant interface and carry out the following settings.

##### Carrying out settings in the PAYONE merchant interface

1. Go to **Configuration » Payment portals**.
2. Open the **Extended Tab** of the payment portal belonging to your online store.
3. In the field **TransactionStatusURL**, enter a URL following the pattern **DOMAIN/payment/payone/status**. Replace **DOMAIN** with the URL of your online store.
4. For the option **Method hash calculation**, select the option **md5 or sha2-384 (during migration)**.
5. **Save** the settings.

In your plentymarkets back end, activate the payment method once in the **Setup » Orders » Payment » Methods** menu. More information on carrying out this setting is available on the <strong><a href="https://knowledge.plentymarkets.com/en/payment/managing-payment-methods#20" target="_blank">Managing payment methods</a></strong> page of the manual.

In addition, make sure that the payment method is included among the Permitted payment methods in the <strong><a href="https://knowledge.plentymarkets.com/en/crm/managing-contacts#15" target="_blank">customer classes</a></strong> and that it is not listed among the Blocked payment methods in the <strong><a href="https://knowledge.plentymarkets.com/en/order-processing/fulfilment/preparing-the-shipment#1000" target="_blank">shipping profiles</a></strong>.

## Setting up the plugin

The following steps for setting up the plentymarkets PAYONE plugin are carried out in the plugin overview of your plentymarkets system. Proceed as described below to set up the plugin.

##### Carrying out the basic settings

1. Go to **Plugin » Plugin overview**.
2. In the plugin list, click on the name of the plugin **PAYONE**. <br >
→ The detail view of the plugin  opens.
3. Open the menu entry **Configuration**.
4. Click on **Basic settings**. Carry out the settings according to the information provided in table 1.
5. **Save** the settings.

<table>
<caption>Tab. 1: Carrying out the basic settings</caption>
   <thead>
      <th>
         Setting
      </th>
      <th>
         Explanation
      </th>
   </thead>
   <tbody>
      <tr>
         <td>
            <b>Merchant ID</b>
         </td>
         <td>
            Enter the customer number as received after the registration process with PAYONE.
         </td>
      </tr>
      <tr>
         <td>
            <b>Portal ID</b>
         </td>
         <td>
         Enter the payment portal ID as received after the registration process with PAYONE.
         </td>
      </tr>
      <tr>
         <td>
            <b>Account ID</b>
         </td>
         <td>
         Enter the account ID as received after the registration process with PAYONE.
         </td>
      </tr>
      <tr>
         <td>
            <b>Key</b>
         </td>
         <td>
            Enter the key as received after the registration process with PAYONE.
         </td>
      </tr>
      <tr>
         <td>
            <b>Mode</b>
         </td>
         <td>
            Select one of the options <strong>Test</strong> and <strong>Live</strong>. We recommend using the test mode while setting up the plugin. The test mode ensures that the payment method is not available in your online store during the setup process. After completing the setup of the plugin, select the live mode so that the payment method becomes visible in your online store.
         </td>
      </tr>
      <tr>
          <td>
              <b>Authorisation method</b>
          </td>
          <td>
             <strong>Preauthorisation:</strong> Select this option if payment collection should only be prepared for the customer. The definite collection of payment has to be effected by an event procedure that should be triggered by booking outgoing items (see table 5). <br />
            <strong>Authorisation</strong>: Select this option if the payment should be collected from the customer immediately. Incoming payment is then booked directly after the customer has completed the checkout process in the online store.
      </tr>
   </tbody>
</table>

### Setting up payment methods

In the following, you select the PAYONE payment methods that should be available for your customers in the online store. In addition, you carry out more detailed settings for the selected payment methods. Proceed as described below.


##### Setting up payment methods:

1. Open the menu entry **Configuration** in the detail view of the plugin.
2. Click on the menu entry of the payment method. Carry out the settings according to the information provided in table 2.
5. **Save** the settings.

<table>
<caption>Tab. 2: Setting up payment methods</caption>
   <thead>
      <th>
         Setting
      </th>
      <th>
         Explanation
      </th>
   </thead>
   <tbody>
      <tr>
         <td>
            <b>Active</b>
         </td>
         <td>
            Select the option <strong>Yes</strong> to activate the payment method and thus offer it in the online store.<br /> Select the option <strong>No</strong> if the payment method should be deactivated and thus not be available.
         </td>
      </tr>
      <tr>
         <td>
            <b>Name</b>
         </td>
         <td>
            Enter a name for the payment method. This name is visible for your customers in the online store.
         </td>
      </tr>
      <tr>
         <td>
            <b>Description</b>
         </td>
         <td>
          Enter a description for the payment method. This text is visible for your customers in the online store.
         </td>
      </tr>
      <tr>
         <td>
            <b>Minimum order value</b>
         </td>
         <td>
            Enter a minimum order value for which the payment method should be available in the online store.
         </td>
      </tr>
      <tr>
         <td>
            <b>Maximum order value</b>
         </td>
         <td>
            Enter a maximum order value for which the payment method should be available in the online store. The payment method is not available for orders exceeding this value.
         </td>
      </tr>
      <tr>
          <td>
              <b>Allowed countries of delivery</b>
          </td>
          <td>
            Enter the countries of delivery (separated by a comma) for which the payment method should be available.
         </td>
      </tr>
   </tbody>
</table>

**Notes about credit card:** The payment method **Credit card** requires particular attention. Additional settings are necessary for this payment method. The settings are described in table 3.

<table>
<caption>Tab. 3: Setting up the payment method Credit card</caption>
   <thead>
      <th>
         Setting
      </th>
      <th>
         Explanation
      </th>
   </thead>
   <tbody>
      <tr>
         <td>
            <b>Minimum card validity in days</b>
         </td>
         <td>
            Enter the required minimum card validity in days that a credit card must have to be accepted for the payment method.
         </td>
      </tr>
      <tr>
         <td>
            <b>Credit card fields default style</b>
         </td>
         <td>
            Default settings are saved for the colour, font size and font used for the data fields into which customers enter their credit card data in the online store. The values are editable.
         </td>
      </tr>
      <tr>
         <td>
            <b>Credit card fields height in px</b>
         </td>
         <td>
            The height of the data fields in px is set by default. The value is editable.
         </td>
      </tr>
      <tr>
         <td>
            <b>Credit card fields width in px</b>
         </td>
         <td>
            The width of the data fields in px is set by default. The value is editable.
         </td>
      </tr>
      <tr>
         <td>
              <b>Allowed card types</b>
          </td>
          <td>
            Activate the card types that should be accepted for the payment method.
         </td>
      </tr>
   </tbody>
</table>

**Notes about Secure Invoice**: You need a separate **Portal ID** and **Key** for the payment method **Secure Invoice**. This Portal-ID and this key have to be deposited in the plugin while configuring it. You get the Portal ID and the Key via the PAYONE Merchant Interface in the **Configuration » Payment portals** menu.

**Notes about Amazon Pay**: In order to use the payment method **Amazon Pay** you have to link your Amazon account with Payone. To do so, contact the <strong><a href="https://www.payone.com/kontakt/" target="_blank">Payone support</a></strong>.
When setting up the payment method **Amazon Pay**, you can activate the setting **Test-Environment** additionally to the settings described in table 2. This setting enables you to test the payment method first with test purchases. You cannot generate orders or turnover with these, they are solely for testing the payment method. Deactivate the setting **Test-Environment** to activate Amazon Pay.
Note that you cannot use Amazon Pay if you have already integrated **PayPal** in Mode 1. In this mode, PayPal overwrites the entire checkout with the PayPal-Wall so that the widgtes Amazon Pay needs are not available.

## Adjusting the Ceres checkout

In the next step, an adjustment to your Ceres checkout settings is necessary. These adjustments need to be made so that customers using PAYONE for payment processes can enter their date of birth (for secure invoice only) correctly.

<div class="alert alert-warning" role="alert">
  Note: Make sure to carefully carry out the settings described below. Otherwise, your customers will not be able to complete the payment process using PAYONE in your online store!
</div>

##### Adjusting the Ceres checkout for secure invoice:

1. Go to **Plugins » Plugin overview**. <br >
→ The plugin overview opens.
2. Click on **Ceres**. <br >
→ The plugin opens.
3. Click on **Configuration** in the directory tree.
4. Click on the **Checkout and My account** tab.
5. Open the **Show invoice address fields in the address form (DE)** area.
6. Use the check box to activate the option **Date of birth**.
7. **Save** the settings. <br /> A field for entering the date of birth is now displayed for your customers in the checkout area.

## Linking template containers

You have multiple options to integrate the payment method PAYONE into your online store. For this purpose, the plentymarkets system offers containers at relevant places which can be filled with content to meet your needs.

##### Linking template containers:

1. Go to Plugins » Plugin set overview.
2. Open the plugin set you want to edit.
3. Open the Settings of the Payone plugin.
4. Click on Container links.
5. From the drop-down list, select the data provider you want to link.
6. Select the container you want to link the data provider to. Pay attention to the information provided in table 4.
7. Repeat steps 5 and 6 for all data providers you want to link.
8. Save the settings.

<table>
<caption>Tab. 4: Linking template containers</caption>
   <thead>
      <th>
         Content
      </th>
      <th>
         Explanation
      </th>
   </thead>
   <tbody>
      <tr>
         <td>
            <b>Payone Order Confirmation Page Payment Data</b>
         </td>
         <td>
            Link this content to the container Order confirmation: Additional payment information to display the PAYONE payment methods on the order confirmation page in the online store.
         </td>
      </tr>
      <tr>
         <td>
            <b>Payone Checkout JS</b>
         </td>
         <td>
            Link this content to the container Script loader: After script loaded to display the PAYONE payment methods during the checkout process in the online store.
         </td>
      </tr>
   </tbody>
</table>

## Sending an automatic shipping confirmation to PAYONE

Set up an event procedure to send an automatic shipping confirmation to PAYONE as soon as you have shipped the order.

**Note:** Sending up the following event procedure is mandatory if you have selected the option **Preauthorisation** as **Authorisation method** (see table 1). This event procedure is not necessary and cannot be used if you have selected the option **Authorisation**.

##### Setting up an event procedure:

1. Go to **System » Orders » Events**.
2. Click on **Add event procedure**. <br >
→ The **Create new event procedure** window opens.
3. Enter a name.
4. Select the event according to table 5.
5. **Save** the settings. <br >
→ The event procedure is created.
6. Carry out the further settings according to table 5.
7. Place a check mark next to the option **Active**.
8. **Save** the settings. <br >
→ The event procedure is saved.

<table>
<caption>
   Tab. 5: Event procedure for sending an automatic shipping confirmation to PAYONE
</caption>
   <thead>
    </tr>
      <th>
         Setting
      </th>
      <th>
         Option
      </th>
      <th>
         Selection
      </th>
    </tr>
   </thead>
   <tbody>
      <tr>
         <td><strong>Event</strong></td>
         <td>Select the event after which an automatic shipping confirmation should be sent, e.g. <strong>Order change > Outgoing items booked</strong></td>
         <td></td>
      </tr>
      <tr>
         <td><strong>Filter 1</strong></td>
         <td><strong>Order > Payment method</strong></td>
         <td><strong>Plugin: PAYONE</strong>
      </tr>
      <tr>
        <td><strong>Procedure</strong></td>
        <td><strong>Plugins > Payone | Refund order</strong></td>
        <td></td>
      </tr>
    </tbody>
</table>

## Automatically refunding PAYONE payments

Set up an event procedure to automatically refund a PAYONE payment.

##### Setting up an event procedure:

1. Go to **System » Orders » Events**.
2. Click on **Add event procedure**. <br >
→ The **Create new event procedure** window opens.
3. Enter a name.
4. Select the event according to table 6.
5. **Save** the settings. <br >
→ The event procedure is created.
6. Carry out the further settings according to table 6.
7. Place a check mark next to the option **Active**.
8. **Save** the settings. <br >
→ The event procedure is saved.

<table>
<caption>
   Tab. 6: Event procedure for automatically refunding PAYONE payments
</caption>
   <thead>
    </tr>
      <th>
         Setting
      </th>
      <th>
         Option
      </th>
      <th>
         Selection
      </th>
    </tr>
   </thead>
   <tbody>
      <tr>
         <td><strong>Event</strong></td>
         <td>Select the event to trigger a refund.</td>
         <td></td>
      </tr>
      <tr>
         <td><strong>Filter 1</strong></td>
         <td><strong>Order > Payment method</strong></td>
         <td><strong>Plugin: PAYONE</strong>
      </tr>
      <tr>
        <td><strong>Procedure</strong></td>
        <td><strong>Plugins > Payone | Refund order</strong></td>
        <td></td>
      </tr>
    </tbody>
</table>

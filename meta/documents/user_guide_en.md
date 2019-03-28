<div class="alert alert-warning" role="alert">
   The Payone plugin has been developed for use with the online store Ceres and only works with its structure or other template plugins. The plugins Ceres and IO have to be activated so that the Payone plugin can be used.
</div>

# PAYONE Payment for plentymarkets 7

The plentymarkets Payone plugin offers you access to international as well as local payment methods. In addition, the plugin offers the advantages of an integrated risk management system, automated refunds and swift processing of returns.

The plugin currently offers the following payment methods:

* Invoice
* Secure invoice
* Paydirekt
* Payolution Payment in installments
* PayPal
* RatePay Payment in installments
* Sofortüberweisung
* Cash in advance
* Credit card
* Cash on Delivery
* Debit

## First steps

**Note: Before using the plugin, you have to register for a PAYONE account. [Click here to access the registration form](https://www.psg-projektmanagement.de/bs-payone/).**

First of all, activate the payment method once in the **System » System Settings » Orders » Payment » Methods** menu. More information on carrying out this setting is available on the <strong><a href="https://knowledge.plentymarkets.com/en/payment/managing-payment-methods#20" target="_blank">Managing payment methods</a></strong> page of the manual.

In addition, make sure that the payment method is included among the Permitted payment methods in the <strong><a href="https://knowledge.plentymarkets.com/en/crm/managing-contacts#15" target="_blank">customer classes</a></strong> and that it is not listed among the Blocked payment methods in the <strong><a href="https://knowledge.plentymarkets.com/en/order-processing/fulfilment/preparing-the-shipment#1000" target="_blank">shipping profiles</a></strong>.

## Setting up the plugin

The following steps for setting up the plentymarkets Payone plugin are carried out in the plugin overview of your plentymarkets system. Proceed as described below to set up the plugin.

##### Carrying out the basic settings

1. Go to **Plugin » Plugin overview**.
2. In the plugin list, click on the name of the plugin **BS Payone**.
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
            Enter the customer number as received after the registration process with Payone.
         </td>
      </tr>
      <tr>
         <td>
            <b>Portal ID</b>
         </td>
         <td>
         Enter the payment portal ID as received after the registration process with Payone.
         </td>
      </tr>
      <tr>
         <td>
            <b>Account ID</b>
         </td>
         <td>
         Enter the account ID as received after the registration process with Payone.
         </td>
      </tr>
      <tr>
         <td>
            <b>Key</b>
         </td>
         <td>
            Enter the key as received after the registration process with Payone.
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
              <b>Method for payment registration</b>
          </td>
          <td>
            Select when and how orders with the payment method Payone are marked as paid in your plentymarkets system.<br /> <strong>Manually/Event procedure</strong>: Select this option if you wish to manually book incoming payment or to set up an event procedure booking incoming payment. <br />
            <strong>Directly after authorisation:</strong>: Select this option if incoming payment should be booked directly after completion of the checkout process in the online store and successful authorisation by Payone.
         </td>
      </tr>
   </tbody>
</table>

### Setting up payment methods

In the following, you select the Payone payment methods that should be available for your customers in the online store. In addition, you carry out more detailed settings for the selected payment methods. Proceed as described below.


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

**Note:** The payment method Die Zahlungsart **Credit card** requires particular attention. Additional settings are necessary for this payment method. The settings are described in table 3.

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

## Linking template containers

You have multiple options to integrate the payment method Payone into your online store. For this purpose, the plentymarkets system offers containers at relevant places which can be filled with content to meet your needs.

##### Linking template containers:

1. Go to **CMS » Container links**.
2. Select the content that should be linked.
3. Select one or more containers in which the previously selected content should be displayed. Pay attention to the information provided in table 4.
4. **Save** the settings.<br /> → The content is linked to the containers

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
            Link this content to the container Script loader: After script loaded to
         </td>
      </tr>
      <tr>
         <td>
            <b>Payone Checkout JS</b>
         </td>
         <td>
            Linkt his content to the container Script loader: After script loaded to
         </td>
      </tr>
   </tbody>
</table>

## Automatically refunding Payone payments

Set up an event procedure to automatically refund a Payone payment.

##### Setting up an event procedure:

1. Go to **System » Orders » Events**.
2. Click on **Add event procedure**.
→ The **Create new event procedure** window opens.
3. Enter a name.
4. Select the event according to table 5.
5. **Save** the settings.
→ The event procedure is created.
6. Carry out the further settings according to table 5.
7. Place a check mark next to the option **Active**.
8. **Save** the settings.
→ The event procedure is saved.

<table>
<caption>
   Tab. 5: Event procedure for automatically refunding Payone payments
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

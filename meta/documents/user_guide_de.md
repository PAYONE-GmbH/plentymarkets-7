<div class="alert alert-warning" role="alert">
   Das BS Payone Plugin ist für die Nutzung mit dem Webshop Ceres entwickelt und funktioniert nur mit dessen Logikstruktur oder anderen Template-Plugins. Zur Nutzung des BS Payone Plugins müssen die plugins IO und Ceres aktiviert sein.
</div>

# BS PAYONE Payment für plentymarkets 7

Das plentymarkets BS Payone Plugin bietet Ihnen Zugang zu internationalen und lokalen Zahlungsarten. Gleichzeitig haben Sie Zugriff auf ein integriertes Risikomanagement, automatisierte Gutschriften und schnelle Retourenabwicklung.

Aktuell beinhaltet das plentymarkets BS Payone Plugin die folgenden Zahlungsarten:

* Rechnung
* Gesicherte Rechnung
* Paydirekt
* Payolution Ratenzahlung
* PayPal
* RatePay Ratenzahlung
* Sofortüberweisung
* Vorkasse
* Kreditkarte
* Nachnahme
* Lastschrift

## Erste Schritte

**Hinweis: Bevor Sie das Plugin nutzen, müssen Sie ein PAYONE Konto beantragen. [Hier geht es zum Anmeldeformular](https://www.psg-projektmanagement.de/bs-payone/).**

Zuerst aktivieren Sie die Zahlungsart einmalig im Menü **System » Systemeinstellungen » Aufträge » Zahlung » Zahlungsarten**. Weitere Informationen dazu finden Sie auf der Handbuchseite <strong><a href="https://knowledge.plentymarkets.com/payment/zahlungsarten-verwalten#20" target="_blank"> Zahlungsarten verwalten </a></strong>.

Stellen Sie zudem sicher, dass die Zahlungsart unter dem Punkt **Erlaubte Zahlungsarten** in den <strong><a href="https://knowledge.plentymarkets.com/crm/kontakte-verwalten#15" target="_blank">Kundenklassen</a></strong> vorhanden ist und nicht im Bereich **Gesperrte Zahlungsarten** in den <strong><a href="https://knowledge.plentymarkets.com/auftragsabwicklung/fulfillment/versand-vorbereiten#1000" target="_blank">Versandprofilen</a></strong> aufgeführt ist.

## Einrichtung des Plugins

Die folgenden Einrichtungsschritte für das plentymarkets Payone Plugins erfolgen direkt in der Plugin-Übersicht. Gehen Sie dazu wie im Folgenden beschrieben vor.

### Grundeinstellungen vornehmen

1. Öffnen Sie das Menü **Plugins » Plugin-Übersicht**.
2. Klicken Sie in der Liste der Plugins auf den Namen des Plugins **BS Payone**.
→ Die Detailansicht des Plugins öffnet sich.
3. Öffnen Sie den Menüpunkt **Konfiguration**.
4. Klicken Sie auf **Grundeinstellungen**. Nehmen Sie die Einstellungen anhand der Informationen in Tabelle 1 vor.
5. **Speichern** Sie die Einstellungen.

<table>
<caption>Tab. 1: Grundeinstellungen vornehmen</caption>
   <thead>
      <th>
         Einstellung
      </th>
      <th>
         Erläuterung
      </th>
   </thead>
   <tbody>
      <tr>
         <td>
            <b>Merchant ID</b>
         </td>
         <td>
            Geben Sie hier die Kundennummer ein, die Sie bei der Registrierung bei Payone erhalten haben.
         </td>
      </tr>
      <tr>
         <td>
            <b>Portal ID</b>
         </td>
         <td>
            Geben Sie hier die ID des Zahlungsportals ein, die Sie bei der Registrierung bei Payone erhalten haben.
         </td>
      </tr>
      <tr>
         <td>
            <b>Account ID</b>
         </td>
         <td>
            Geben Sie hier die Account-ID ein, die Sie bei der Registrierung bei Payone erhalten haben.
         </td>
      </tr>
      <tr>
         <td>
            <b>Schlüssel</b>
         </td>
         <td>
            Geben Sie hier den Schlüssel ein, den Sie bei der Registrierung bei Payone erhalten haben.
         </td>
      </tr>
      <tr>
         <td>
            <b>Modus</b>
         </td>
         <td>
            Wahlen Sie zwischen den Optionen <strong>Test</strong> und <strong>Live</strong>. Wir empfehlen, während der Einrichtung des Plugins den Testmodus zu wählen. Währenddessen ist die Zahlungsart noch nicht in Ihrem Webshop verfügbar. Nach erfolgter Einrichtung wechseln Sie in den Livemodus und machen somit die Zahlungsart in Ihrem Webshop sichtbar.
         </td>
      </tr>
      <tr>
          <td>
              <b>Autorisierungsmethode</b>
          </td>
          <td>
            <strong>Preautorisierung:</strong>: Wählen Sie diese Option, wenn Sie für Payone-Zahlungen manuell oder per Ereignisaktion den Zahlungseingang buchen wollen.<br />
            <strong>Autorisierung:</strong>: Wählen Sie diese Option, wenn der Zahlungseingang direkt nach dem Kaufabschluss im Webshop und der Überprüfung durch Payone in Ihrem plentymarkets System gebucht werden soll.
         </td>
      </tr>
   </tbody>
</table>

### Zahlungsarten einrichten

Im Folgenden legen Sie fest, welche Payone-Zahlungsarten Ihren Kunden im Webshop zur Verfügung stehen sollen. Außerdem nehmen Sie für die festgelegten Zahlungsarten genauere Einstellungen vor. Gehen Sie dazu wie im Folgenden beschrieben vor.

1. Öffnen Sie den Menüpunkt **Konfiguration** in der Detailansicht des Plugins.
2. Klicken Sie auf den Menüpunkt der Zahlungsart. Nehmen Sie die Einstellungen anhand der Informationen in Tabelle 2 vor.
5. **Speichern** Sie die Einstellungen.

<table>
<caption>Tab. 2: Zahlungsarten einrichten</caption>
   <thead>
      <th>
         Einstellung
      </th>
      <th>
         Erläuterung
      </th>
   </thead>
   <tbody>
      <tr>
         <td>
            <b>Aktiv</b>
         </td>
         <td>
            Wählen Sie die Option <strong>Ja</strong>, um die Zahlungsart zu aktivieren und somit im Webshop anzubieten.<br /> Wählen Sie die Option <strong>Nein</strong>, wenn die Zahlungsart deaktiviert werden und damit nicht zur Verfügung stehen soll.
         </td>
      </tr>
      <tr>
         <td>
            <b>Name</b>
         </td>
         <td>
            Geben Sie einen Namen für die Zahlungsart ein. Dieser Name ist für Ihre Kunden im Webshop sichtbar.
         </td>
      </tr>
      <tr>
         <td>
            <b>Beschreibung</b>
         </td>
         <td>
            Geben Sie einen Beschreibungstext für die Zahlungsart ein. Dieser Text ist für Ihre Kunden im Webshop sichtbar.
         </td>
      </tr>
      <tr>
         <td>
            <b>Minimaler Bestellwert</b>
         </td>
         <td>
            Geben Sie einen minimalen Bestellwert ein, ab dem die Zahlungsart im Webshop verfügbar sein soll.
         </td>
      </tr>
      <tr>
         <td>
            <b>Maximaler Bestellwert</b>
         </td>
         <td>
            Geben Sie einen maximalen Bestellwert ein, bis zu dem die Zahlungsart im Webshop verfügbar sein soll. Wird dieser Wert überschritten, ist die Zahlungsart nicht mehr verfügbar.
         </td>
      </tr>
      <tr>
          <td>
              <b>Erlaubte Lieferländer</b>
          </td>
          <td>
            Geben Sie kommasepariert die Lieferländer aus, für die die Zahlungsart verfügbar sein soll.
         </td>
      </tr>
   </tbody>
</table>

Für die Zahlungsart **Kreditkarte** können Sie zusätzliche Einstellungen vornehmen. Diese Einstellungen werden in Tabelle 3 beschrieben.

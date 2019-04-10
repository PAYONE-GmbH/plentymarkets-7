<div class="alert alert-warning" role="alert">
   Das BS PAYONE Plugin ist für die Nutzung mit dem Webshop Ceres entwickelt und funktioniert nur mit dessen Logikstruktur oder anderen Template-Plugins. Zur Nutzung des BS PAYONE Plugins müssen die Plugins IO und Ceres aktiviert sein.
</div>

# BS PAYONE Payment für plentymarkets 7

Das plentymarkets BS PAYONE Plugin bietet Ihnen Zugang zu internationalen und lokalen Zahlungsarten. Gleichzeitig haben Sie Zugriff auf ein integriertes Risikomanagement, automatisierte Gutschriften und schnelle Retourenabwicklung.

Aktuell beinhaltet das Plugin die folgenden Zahlungsarten:

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

**Hinweis: Die Nutzung dieses Plugins erfordert einen Freischaltcode, den Sie über die PSG Projektmanagement GmbH erhalten können. Unter bestimmten Voraussetzungen können Sie in Verbindung mit dieser Zahlart über die PSG Projektmanagement GmbH in den Genuss eines attraktiven Förderprogramms kommen. [Hier geht es zum Anmeldeformular](https://www.psg-projektmanagement.de/bs-payone/).**

Nach Erhalt der Zugangsdaten loggen Sie sich im BS PAYONE Merchant Interface ein und nehmen die folgenden Einstellungen vor.

##### Einstellungen im PAYONE Merchant Interface vornehmen:

1. Öffnen Sie das Menü **Konfiguration » Zahlungsportale**.
2. Öffnen Sie das Tab **Erweitert** des Zahlungsportals Ihres Shops.
3. Tragen Sie im Feld **TransactionStatusURL** eine URL nach dem Schema **DOMAIN/payment/payone/status** ein. Den Platzhalter **DOMAIN** durch die URL zu Ihrem Webshop ersetzen.
4. Wählen Sie als **Verfahren Hashwert-Prüfung** die Option **md5 oder sha2-384 (für Migration)**.
5. **Speichern** Sie die Einstellungen.

Aktivieren Sie die gewünschten BS PAYONE-Zahlungsarten in Ihrem plentymarkets Backend einmalig im Menü **System » Systemeinstellungen » Aufträge » Zahlung » Zahlungsarten**. Weitere Informationen dazu finden Sie auf der Handbuchseite <strong><a href="https://knowledge.plentymarkets.com/payment/zahlungsarten-verwalten#20" target="_blank"> Zahlungsarten verwalten </a></strong>.

Stellen Sie zudem sicher, dass die Zahlungsart unter dem Punkt **Erlaubte Zahlungsarten** in den <strong><a href="https://knowledge.plentymarkets.com/crm/kontakte-verwalten#15" target="_blank">Kundenklassen</a></strong> vorhanden ist und nicht im Bereich **Gesperrte Zahlungsarten** in den <strong><a href="https://knowledge.plentymarkets.com/auftragsabwicklung/fulfillment/versand-vorbereiten#1000" target="_blank">Versandprofilen</a></strong> aufgeführt ist.

## Einrichtung des Plugins

Die folgenden Einrichtungsschritte für das plentymarkets BS PAYONE Plugins erfolgen direkt in der Plugin-Übersicht Ihres plentymarkets Systems. Gehen Sie dazu wie im Folgenden beschrieben vor.

##### Grundeinstellungen vornehmen:

1. Öffnen Sie das Menü **Plugins » Plugin-Übersicht**.
2. Klicken Sie in der Liste der Plugins auf den Namen des Plugins **BS BS PAYONE**.
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
            Geben Sie hier die Kundennummer ein, die Sie bei der Registrierung bei BS PAYONE erhalten haben.
         </td>
      </tr>
      <tr>
         <td>
            <b>Portal ID</b>
         </td>
         <td>
            Geben Sie hier die ID des Zahlungsportals ein, die Sie bei der Registrierung bei BS PAYONE erhalten haben.
         </td>
      </tr>
      <tr>
         <td>
            <b>Account ID</b>
         </td>
         <td>
            Geben Sie hier die Account-ID ein, die Sie bei der Registrierung bei BS PAYONE erhalten haben.
         </td>
      </tr>
      <tr>
         <td>
            <b>Schlüssel</b>
         </td>
         <td>
            Geben Sie hier den Schlüssel ein, den Sie bei der Registrierung bei BS PAYONE erhalten haben.
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
              <b>Art der Autorisierung</b>
          </td>
          <td>
             <strong>Vorautorisierung</strong>: Wählen Sie diese Option, wenn der Zahlungseinzug beim Käufer nur vorgemerkt werden soll. Der Zahlungseinzug erfolgt dann durch eine Ereignisaktion, die beim Warenausgang ausgelöst werden muss (siehe Tabelle 5). <br />
            <strong>Autorisierung:</strong>: Wählen Sie diese Option, wenn der Zahlungseinzug beim Käufer sofort stattfinden soll. Der Zahlungseingang wird somit direkt nach dem Kaufabschluss im Webshop in Ihrem plentymarkets System gebucht.
         </td>
      </tr>
   </tbody>
</table>

### Zahlungsarten einrichten

Im Folgenden legen Sie fest, welche BS PAYONE-Zahlungsarten Ihren Kunden im Webshop zur Verfügung stehen sollen. Außerdem nehmen Sie für die festgelegten Zahlungsarten genauere Einstellungen vor. Gehen Sie dazu wie im Folgenden beschrieben vor.


##### Zahlungsarten einrichten:

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

**Hinweis**: Die Zahlungsart **Kreditkarte** stellt einen Sonderfall dar. Für diese Zahlungsart sind zusätzliche Einstellungen notwendig. Diese Einstellungen werden in Tabelle 3 beschrieben.

<table>
<caption>Tab. 3: Zahlungsart Kreditkarte einrichten</caption>
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
            <b>Minimale Kartengültigkeit in Tagen</b>
         </td>
         <td>
            Geben Sie die erforderliche minimale Kartengültigkeit an, über die eine Kreditkarte verfügen muss, um für die Zahlungsart akzeptiert zu werden.
         </td>
      </tr>
      <tr>
         <td>
            <b>Kreditkartenfelder Default Style</b>
         </td>
         <td>
            Standardmäßig ist hier voreingestellt, in welcher Farbe, Schriftgröße und Schriftart die Eingabefelder für die Kreditkartendaten für Kunden im Webshop angezeigt werden. Die Werte sind editierbar.
         </td>
      </tr>
      <tr>
         <td>
            <b>Kreditkartenfelder Höhe in px</b>
         </td>
         <td>
            Standardmäßig ist hier die Höhe der Eingabefelder in px voreingestellt. Der Wert ist editierbar.
         </td>
      </tr>
      <tr>
         <td>
            <b>Kreditkartenfelder Breite in px</b>
         </td>
         <td>
            Standardmäßig ist hier die Breite der Eingabefelder in px voreingestellt. Der Wert ist editierbar.
         </td>
      </tr>
      <tr>
         <td>
              <b>Erlaubte Kartentypen</b>
          </td>
          <td>
            Aktivieren Sie die Kartentypen, die für die Zahlungsart akzeptiert werden sollen.
         </td>
      </tr>
   </tbody>
</table>

## Template-Container verknüpfen

Für die Zahlungsart BS PAYONE stehen Ihnen verschiedene Möglichkeiten zur Verfügung, um sie in Ihrem Webshop einzubinden.
Hierfür sind in den Templates in plentymarkets an relevanten Stellen Container hinterlegt, mit denen zur Individualisierung Contents verknüpft werden.

##### Container verknüpfen:

1. Öffnen Sie das Menü **CMS » Container-Verknüpfungen**.
2. Wählen Sie den gewünschten Content, der verknüpft werden soll.
3. Wählen Sie einen oder mehrere Container, in denen der zuvor gewählte Content dargestellt werden soll. Beachten Sie dazu die Erläuterungen in Tabelle 4.
4. **Speichern** Sie die Einstellungen.<br /> → Die Contents sind mit den Containern verknüpft.

<table>
<caption>Tab. 4: Container verknüpfen</caption>
   <thead>
      <th>
         Content
      </th>
      <th>
         Erläuterung
      </th>
   </thead>
   <tbody>
      <tr>
         <td>
            <b>Payone Order Confirmation Page Payment Data</b>
         </td>
         <td>
            Verknüpfen Sie diesen Content mit dem Container Order confirmation: Additional payment information, um die BS PAYONE-Zahlungsarten auf der Bestellbestätigungsseite im Webshop anzuzeigen.
         </td>
      </tr>
      <tr>
         <td>
            <b>Payone Checkout JS</b>
         </td>
         <td>
            Verknüpfen Sie diesen Content mit dem Container Script loader: After script loaded, um die BS PAYONE-Zahlungsarten während der Kaufabwicklung im Webshop anzuzeigen.
         </td>
      </tr>
   </tbody>
</table>

## Automatische Versandbestätigung an BS PAYONE senden

Richten Sie eine Ereignisaktion ein, um eine automatische Versandbestätigung an BS PAYONE zu senden, sobald Sie den Auftrag versendet haben.

**Hinweis:** Die Einrichtung dieser Ereignisaktion ist zwingend notwendig, wenn als **Art der Autorisierung** die Option **Vorautorisierung** gewählt wurde (siehe Tabelle 1). Haben Sie die Option **Autorisierung** gewählt, ist diese Ereignisaktion nicht nutzbar und nicht notwendig.

##### Ereignisaktion einrichten:

1. Öffnen Sie das Menü **System » Aufträge » Ereignisaktionen**.
2. Klicken Sie auf **Ereignisaktion hinzufügen**.
→ Das Fenster **Neue Ereignisaktion erstellen** wird geöffnet.
3. Geben Sie einen Namen ein.
4. Wählen Sie das Ereignis gemäß Tabelle 5.
5. **Speichern** Sie die Einstellungen.
→ Die Ereignisaktion wird angelegt.
6. Nehmen Sie die weiteren Einstellungen gemäß Tabelle 5 vor.
7. Setzen Sie ein Häkchen bei **Aktiv**.
8. **Speichern** Sie die Einstellungen.
→ Die Ereignisaktion wird gespeichert.

<table>
<caption>
   Tab. 5: Ereignisaktion zum Senden einer automatischen Versandbestätigung an BS PAYONE
</caption>
   <thead>
    </tr>
      <th>
         Einstellung
      </th>
      <th>
         Option
      </th>
      <th>
         Auswahl
      </th>
    </tr>
   </thead>
   <tbody>
      <tr>
         <td><strong>Ereignis</strong></td>
         <td>Das Ereignis wählen, nach dem die Versandbestätigung automatisch versendet werden soll, beispielsweise <strong>Auftragsänderung > Warenausgang gebucht</strong> </td>
         <td></td>
      </tr>
      <tr>
         <td><strong>Filter 1</strong></td>
         <td><strong>Auftrag > Zahlungsart</strong></td>
         <td><strong>Plugin: PAYONE</strong>
      </tr>
      <tr>
        <td><strong>Aktion</strong></td>
        <td><strong>Plugin > BS PAYONE | Versandbestätigung senden</strong></td>
        <td></td>
      </tr>
    </tbody>
</table>

## BS PAYONE-Zahlung automatisch zurückzahlen

Richten Sie eine Ereignisaktion ein, um die Rückzahlung einer Zahlung über BS PAYONE zu automatisieren.

##### Ereignisaktion einrichten:

1. Öffnen Sie das Menü **System » Aufträge » Ereignisaktionen**.
2. Klicken Sie auf **Ereignisaktion hinzufügen**.
→ Das Fenster **Neue Ereignisaktion erstellen** wird geöffnet.
3. Geben Sie einen Namen ein.
4. Wählen Sie das Ereignis gemäß Tabelle 6.
5. **Speichern** Sie die Einstellungen.
→ Die Ereignisaktion wird angelegt.
6. Nehmen Sie die weiteren Einstellungen gemäß Tabelle 6 vor.
7. Setzen Sie ein Häkchen bei **Aktiv**.
8. **Speichern** Sie die Einstellungen.
→ Die Ereignisaktion wird gespeichert.

<table>
<caption>
   Tab. 6: Ereignisaktion zur automatischen Rückzahlung der BS PAYONE-Zahlung
</caption>
   <thead>
    </tr>
      <th>
         Einstellung
      </th>
      <th>
         Option
      </th>
      <th>
         Auswahl
      </th>
    </tr>
   </thead>
   <tbody>
      <tr>
         <td><strong>Ereignis</strong></td>
         <td>Das Ereignis wählen, nach dem eine Rückzahlung erfolgen soll.</td>
         <td></td>
      </tr>
      <tr>
         <td><strong>Filter 1</strong></td>
         <td><strong>Auftrag > Zahlungsart</strong></td>
         <td><strong>Plugin: PAYONE</strong>
      </tr>
      <tr>
        <td><strong>Aktion</strong></td>
        <td><strong>Plugin > BS PAYONE | Rückerstattung senden</strong></td>
        <td></td>
      </tr>
    </tbody>
</table>

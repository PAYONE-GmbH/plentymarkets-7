<div class="alert alert-warning" role="alert">
   Das PAYONE Plugin ist für die Nutzung mit dem Webshop Ceres entwickelt und funktioniert nur mit dessen Logikstruktur oder anderen Template-Plugins. Zur Nutzung des PAYONE Plugins müssen die Plugins IO und Ceres aktiviert sein.
</div>
![Payone Banner][https://www.psg-projektmanagement.de/wp-content/uploads/2020/08/payone-aktion.jpg]

# PAYONE Payment für plentymarkets

Das plentymarkets PAYONE Plugin bietet Zugang zu internationalen und lokalen Zahlungsarten. Gleichzeitig hast du Zugriff auf ein integriertes Risikomanagement, automatisierte Gutschriften und schnelle Retourenabwicklung.

Aktuell beinhaltet das Plugin die folgenden Zahlungsarten:

* Gesicherter Rechnungskauf
* Visa & MasterCard (inkl. Maestro)
* American Express - Anbindung Ihres bestehenden Akzeptanzvertrages
* SEPA Lastschrift
* giropay - Online-Überweisung Deutschland
* Sofortüberweisung - Online-Überweisung international
* Überweisung - Vorkasse & Rechnung & Nachnahme
* PayPal - Anbindung Ihres PayPal-Accounts
* Amazon Pay - Anbindung Ihres Amazon Pay-Accounts

## Erste Schritte

Für die Nutzung benötigst du einen PAYONE Account und die PAYONE Zugangsdaten. Wenn du noch kein PAYONE Kunde bist und demnach keinen PAYONE Account besitzt, wende dich bitte an:

PSG Projektmanagement GmbH <br>
Meergässle 4 <br>
89180 Berghülen <br>
Telefon: 07344-9592588 <br>
E-Mail: plenty@psg-projektmanagement.de <br>
Internet: http://www.psg-projektmanagement.de <br>
Oder nutze folgendes Anmeldeformular <br>
https://www.psg-projektmanagement.de/payone-plentymarkets/

<div class="alert alert-warning" role="alert">
    Einen PAYONE-Account erhältst du nur über den oben genannten Partner. Bitte wende dich nicht direkt an die PAYONE, um den reibungslosen Ablauf nicht zu gefährden.
</div>

Nach Erhalt der Zugangsdaten loggst du dich im PAYONE Merchant Interface ein und nimmst die folgenden Einstellungen vor.

##### Einstellungen im PAYONE Merchant Interface vornehmen:

1. Öffne das Menü **Konfiguration » Zahlungsportale**.
2. Öffne das Tab **Erweitert** des Zahlungsportals deines Shops.
3. Trage im Feld **TransactionStatusURL** eine URL nach dem Schema **DOMAIN/payment/payone/status** ein. Den Platzhalter **DOMAIN** durch die URL zu deinem Webshop ersetzen.
4. Wähle als **Verfahren Hashwert-Prüfung** die Option **md5 oder sha2-384 (für Migration)**.
5. **Speichere** die Einstellungen.

Aktiviere die gewünschten PAYONE-Zahlungsarten in deinem plentymarkets Backend einmalig im Menü **Einrichtung » Aufträge » Zahlung » Zahlungsarten**. Weitere Informationen dazu findest du auf der Handbuchseite <strong><a href="https://knowledge.plentymarkets.com/payment/zahlungsarten-verwalten#20" target="_blank"> Zahlungsarten verwalten </a></strong>.

Stelle zudem sicher, dass die Zahlungsart unter dem Punkt **Erlaubte Zahlungsarten** in den <strong><a href="https://knowledge.plentymarkets.com/crm/kontakte-verwalten#15" target="_blank">Kundenklassen</a></strong> vorhanden ist und nicht im Bereich **Gesperrte Zahlungsarten** in den <strong><a href="https://knowledge.plentymarkets.com/auftragsabwicklung/fulfillment/versand-vorbereiten#1000" target="_blank">Versandprofilen</a></strong> aufgeführt ist.

## Einrichtung des Plugins

Die folgenden Einrichtungsschritte für das plentymarkets PAYONE Plugin erfolgen direkt in der Plugin-Übersicht deines plentymarkets-Systems. Gehe dazu wie im Folgenden beschrieben vor.

##### Grundeinstellungen vornehmen:

1. Öffne das Menü **Plugins » Plugin-Übersicht**.
2. Klicke in der Liste der Plugins auf den Namen des Plugins **PAYONE**.
→ Die Detailansicht des Plugins öffnet sich.
3. Öffne den Menüpunkt **Konfiguration**.
4. Klicke auf **Grundeinstellungen**. Nimm die Einstellungen anhand der Informationen in Tabelle 1 vor.
5. **Speichere** die Einstellungen.

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
            Gib hier die Kundennummer ein, die du bei der Registrierung bei PAYONE erhalten hast.
         </td>
      </tr>
      <tr>
         <td>
            <b>Portal ID</b>
         </td>
         <td>
            Gib hier die ID des Zahlungsportals ein, die du bei der Registrierung bei PAYONE erhalten hast.
         </td>
      </tr>
      <tr>
         <td>
            <b>Account ID</b>
         </td>
         <td>
            Gib hier die Account-ID ein, die du bei der Registrierung bei PAYONE erhalten hast.
         </td>
      </tr>
      <tr>
         <td>
            <b>Schlüssel</b>
         </td>
         <td>
            Gib hier den Schlüssel ein, den du bei der Registrierung bei PAYONE erhalten hast.
         </td>
      </tr>
      <tr>
         <td>
            <b>Modus</b>
         </td>
         <td>
            Wähle zwischen den Optionen <strong>Test</strong> und <strong>Live</strong>. Wir empfehlen, während der Einrichtung des Plugins den Testmodus zu wählen. Währenddessen ist die Zahlungsart noch nicht in deinem Webshop verfügbar. Nach erfolgter Einrichtung wechsele in den Livemodus und mach somit die Zahlungsart in deinem Webshop sichtbar.
         </td>
      </tr>
      <tr>
          <td>
              <b>Art der Autorisierung</b>
          </td>
          <td>
             <strong>Vorautorisierung</strong>: Wähle diese Option, wenn der Zahlungseinzug beim Käufer nur vorgemerkt werden soll. Der Zahlungseinzug erfolgt dann durch eine Ereignisaktion, die beim Warenausgang ausgelöst werden muss (siehe Tabelle 5). <br />
            <strong>Autorisierung:</strong>: Wähle diese Option, wenn der Zahlungseinzug beim Käufer sofort stattfinden soll. Der Zahlungseingang wird somit direkt nach dem Kaufabschluss im Webshop in deinem plentymarkets-System gebucht.
         </td>
      </tr>
   </tbody>
</table>

### Zahlungsarten einrichten

Im Folgenden legst du fest, welche PAYONE-Zahlungsarten deinen Kunden im Webshop zur Verfügung stehen sollen. Außerdem nimmst du für die festgelegten Zahlungsarten genauere Einstellungen vor. Gehe dazu wie im Folgenden beschrieben vor.


##### Zahlungsarten einrichten:

1. Öffne den Menüpunkt **Konfiguration** in der Detailansicht des Plugins.
2. Klicke auf den Menüpunkt der Zahlungsart. Nimm die Einstellungen anhand der Informationen in Tabelle 2 vor.
5. **Speichere** die Einstellungen.

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
            Wähle die Option <strong>Ja</strong>, um die Zahlungsart zu aktivieren und somit im Webshop anzubieten.<br /> Wähle die Option <strong>Nein</strong>, wenn die Zahlungsart deaktiviert werden und damit nicht zur Verfügung stehen soll.
         </td>
      </tr>
      <tr>
         <td>
            <b>Name</b>
         </td>
         <td>
            Gib einen Namen für die Zahlungsart ein. Dieser Name ist für deine Kunden im Webshop sichtbar.
         </td>
      </tr>
      <tr>
         <td>
            <b>Beschreibung</b>
         </td>
         <td>
            Gib einen Beschreibungstext für die Zahlungsart ein. Dieser Text ist für deine Kunden im Webshop sichtbar.
         </td>
      </tr>
      <tr>
         <td>
            <b>Minimaler Bestellwert</b>
         </td>
         <td>
            Gib einen minimalen Bestellwert ein, ab dem die Zahlungsart im Webshop verfügbar sein soll.
         </td>
      </tr>
      <tr>
         <td>
            <b>Maximaler Bestellwert</b>
         </td>
         <td>
            Gib einen maximalen Bestellwert ein, bis zu dem die Zahlungsart im Webshop verfügbar sein soll. Wird dieser Wert überschritten, ist die Zahlungsart nicht mehr verfügbar.
         </td>
      </tr>
      <tr>
          <td>
              <b>Erlaubte Lieferländer</b>
          </td>
          <td>
            Gib kommasepariert die Lieferländer aus, für die die Zahlungsart verfügbar sein soll.
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
            Gib die erforderliche minimale Kartengültigkeit an, über die eine Kreditkarte verfügen muss, um für die Zahlungsart akzeptiert zu werden.
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
            Aktiviere die Kartentypen, die für die Zahlungsart akzeptiert werden sollen.
         </td>
      </tr>
   </tbody>
</table>

**Hinweis**: Für die Zahlungsart **Gesicherter Rechnungskauf** sind eine eigene **Portal-ID** sowie ein eigener **Schlüssel** notwendig. Diese Portal-ID und dieser Schlüssel müssen im Plugin unter **Gesicherte Rechnung** bei der Konfiguration separat hinterlegt werden. Die Portal-ID und den Schlüssel erhältst du über das PAYONE Merchant Interface über das Menü **Konfiguration » Zahlungsportale**.

**Hinweis**: Um die Zahlungsart **Amazon Pay** nutzen zu können, musst du dein Amazon-Konto mit Payone verknüpfen. Wende dich dafür an den <strong><a href="https://www.payone.com/kontakt/" target="_blank">Payone Support</a></strong>.
Bei der Einrichtung der Zahlungsart **Amazon Pay** kannst du zusätzlich zu den in Tabelle 2 beschriebenen Einstellungen noch die Einstellung **Testumgebung** aktivieren. Diese ermöglicht es dir die Zahlungsart zunächst auszutesten und Testkäufe zu tätigen. Mit diesen lassen sich keine Aufträge und kein Umsatz generieren. Deaktiviere die Einstellung **Testumgebung**, um Amazon Pay aktiv zu schalten.

## Ceres-Checkout anpassen

Als Nächstes ist eine Anpassung im Ceres Checkout notwendig, damit deine Kunden bei Bezahlvorgängen mit PAYONE ihr Geburtsdatum (nur für den gesicherten Rechnungskauf) korrekt eingeben können.

<div class="alert alert-warning" role="alert">
  Hinweis: Nimm die im Folgenden beschriebene Einstellung unbedingt sorgfältig vor, da deine Kunden andernfalls den Bezahlvorgang mit PAYONE nicht abschließen können!
</div>

##### Ceres-Checkout anpassen für den gesicherten Rechnungskauf:

1. Öffne das Menü **Plugins » Plugin-Übersicht**. <br >
→ Die Plugin-Übersicht wird geöffnet.
2. Klicke auf **Ceres**. <br >
→ Das Plugin wird geöffnet.
3. Klicke im Verzeichnisbaum auf **Konfiguration**.
4. Wechsele in das Tab **Kaufabwicklung und Mein Konto.**
5. Klappe den Bereich **Rechnungsadressfelder im Adressformular anzeigen (DE)** auf.
6. Aktiviere über die Checkbox die Option **Geburtsdatum**.
7. **Speichere** die Einstellungen. <br /> Im Checkout wird deinen Kunden nun ein Feld zur Eingabe des Geburtsdatums angezeigt.

## Template-Container verknüpfen

Für die Zahlungsart PAYONE stehen dir verschiedene Möglichkeiten zur Verfügung, um sie in deinem Webshop einzubinden.
Hierfür sind in den Templates in plentymarkets an relevanten Stellen Container hinterlegt, mit denen zur Individualisierung Contents verknüpft werden.

##### Container verknüpfen:

1. Öffne das Menü **Plugins » Plugin-Set-Übersicht**.
2. Öffne das Plugin-Set, das du bearbeiten möchtest.
3. Öffne die **Einstellungen** des Payone-Plugins.
4. Klicke auf **Container-Verknüpfungen**.
5. Wähle aus der Dropdown-Liste den Datenanbieter aus, den du verknüpfen möchtest.
6. Wähle den Container aus, mit dem du den Datenanbieter verknüpfen möchtest. Beachte dazu die Erläuterungen in Tabelle 4.
7. Wiederhole Schritte 5 und 6 für alle Datenanbieter, die due verknüpfen möchtest.
8. **Speichere** die Einstellungen.

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
            Verknüpfe diesen Content mit dem Container Order confirmation: Additional payment information, um die PAYONE-Zahlungsarten auf der Bestellbestätigungsseite im Webshop anzuzeigen.
         </td>
      </tr>
      <tr>
         <td>
            <b>Payone Checkout JS</b>
         </td>
         <td>
            Verknüpfe diesen Content mit dem Container Script loader: After script loaded, um die PAYONE-Zahlungsarten während der Kaufabwicklung im Webshop anzuzeigen.
         </td>
      </tr>
   </tbody>
</table>

## Automatische Versandbestätigung an PAYONE senden

Richte eine Ereignisaktion ein, um eine automatische Versandbestätigung an PAYONE zu senden, sobald du den Auftrag versendet hast.

**Hinweis:** Die Einrichtung dieser Ereignisaktion ist zwingend notwendig, wenn als **Art der Autorisierung** die Option **Vorautorisierung** gewählt wurde (siehe Tabelle 1). Hast du die Option **Autorisierung** gewählt, ist diese Ereignisaktion nicht nutzbar und nicht notwendig.

##### Ereignisaktion einrichten:

1. Öffne das Menü **System » Aufträge » Ereignisaktionen**.
2. Klicke auf **Ereignisaktion hinzufügen**. <br >
→ Das Fenster **Neue Ereignisaktion erstellen** wird geöffnet.
3. Gib einen Namen ein.
4. Wähle das Ereignis gemäß Tabelle 5.
5. **Speichere** die Einstellungen. <br >
→ Die Ereignisaktion wird angelegt.
6. Nimm die weiteren Einstellungen gemäß Tabelle 5 vor.
7. Setze ein Häkchen bei **Aktiv**.
8. **Speichere** die Einstellungen. <br >
→ Die Ereignisaktion wird gespeichert.

<table>
<caption>
   Tab. 5: Ereignisaktion zum Senden einer automatischen Versandbestätigung an PAYONE
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
        <td><strong>Plugin > PAYONE | Versandbestätigung senden</strong></td>
        <td></td>
      </tr>
    </tbody>
</table>

## PAYONE-Zahlung automatisch zurückzahlen

Richte eine Ereignisaktion ein, um die Rückzahlung einer Zahlung über PAYONE zu automatisieren.

##### Ereignisaktion einrichten:

1. Öffne das Menü **System » Aufträge » Ereignisaktionen**.
2. Klicke auf **Ereignisaktion hinzufügen**. <br >
→ Das Fenster **Neue Ereignisaktion erstellen** wird geöffnet.
3. Gib einen Namen ein.
4. Wähle das Ereignis gemäß Tabelle 6.
5. **Speichere** die Einstellungen. <br >
→ Die Ereignisaktion wird angelegt.
6. Nimm die weiteren Einstellungen gemäß Tabelle 6 vor.
7. Setze ein Häkchen bei **Aktiv**.
8. **Speichere** die Einstellungen. <br >
→ Die Ereignisaktion wird gespeichert.

<table>
<caption>
   Tab. 6: Ereignisaktion zur automatischen Rückzahlung der PAYONE-Zahlung
</caption>
   <thead>
    <tr>
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
        <td><strong>Plugin > PAYONE | Rückerstattung senden</strong></td>
        <td></td>
      </tr>
    </tbody>
</table>

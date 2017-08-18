<?php
/*
d 	Tag des Monats, 2-stellig mit f�hrender Null 	01 bis 31
D 	Wochentag, gek�rzt auf drei Buchstaben 	Mon bis Sun
j 	Tag des Monats ohne f�hrende Nullen 	1 bis 31
l (kleines 'L') 	Ausgeschriebener Wochentag 	Sunday bis Saturday
N 	Numerische Repr�sentation des Wochentages gem�� ISO-8601 (in PHP 5.1.0 hinzugef�gt) 	1 (f�r Montag) bis 7 (f�r Sonntag)
S 	Anhang der englischen Aufz�hlung f�r einen Monatstag, zwei Zeichen 	st, nd, rd oder th. Zur Verwendung mit j empfohlen.
w 	Numerischer Tag einer Woche 	0 (f�r Sonntag) bis 6 (f�r Samstag)
z 	Der Tag des Jahres (von 0 beginnend) 	0 bis 365
Woche 	--- 	---
W 	ISO-8601 Wochennummer des Jahres, die Woche beginnt am Montag (hinzugef�gt in PHP 4.1.0) 	Beispiel: 42 (die 42. Woche im Jahr)
Monat 	--- 	---
F 	Monat als ganzes Wort, wie January oder March 	January bis December
m 	Monat als Zahl, mit f�hrenden Nullen 	01 bis 12
M 	Monatsname mit drei Buchstaben 	Jan bis Dec
n 	Monatszahl, ohne f�hrende Nullen 	1 bis 12
t 	Anzahl der Tage des angegebenen Monats 	28 bis 31
Jahr 	--- 	---
L 	Schaltjahr oder nicht 	1 f�r ein Schaltjahr, ansonsten 0
o 	Jahreszahl gem�� ISO-8601. Dies ergibt den gleichen Wert wie Y, au�er wenn die ISO-Kalenderwoche (W) zum vorhergehenden oder n�chsten Jahr geh�rt, wobei dann jenes Jahr verwendet wird (in PHP 5.1.0 hinzugef�gt). 	Beispiele: 1999 oder 2003
Y 	Vierstellige Jahreszahl 	Beispiele: 1999 oder 2003
y 	Jahreszahl, zweistellig 	Beispiele: 99 oder 03
Uhrzeit 	--- 	---
a 	Kleingeschrieben: Ante meridiem (Vormittag) und Post meridiem (Nachmittag) 	am oder pm
A 	Gro�geschrieben: Ante meridiem (Vormittag) und Post meridiem (Nachmittag) 	AM oder PM
B 	Swatch-Internet-Zeit 	000 bis 999
g 	Stunde im 12-Stunden-Format, ohne f�hrende Nullen 	1 bis 12
G 	Stunde im 24-Stunden-Format, ohne f�hrende Nullen 	0 bis 23
h 	Stunde im 12-Stunden-Format, mit f�hrenden Nullen 	01 bis 12
H 	Stunde im 24-Stunden-Format, mit f�hrenden Nullen 	00 bis 23
i 	Minuten, mit f�hrenden Nullen 	00 bis 59
s 	Sekunden, mit f�hrenden Nullen 	00 bis 59
u 	Mikrosekunden (hinzugef�gt in PHP 5.2.2). Beachten Sie, dass date() immer die Ausgabe 000000 erzeugen wird, da es einen Integer als Parameter erh�lt, wohingegen DateTime::format() Mikrosekunden unterst�tzt, wenn DateTime mit Mikrosekunden erzeugt wurde. 	Beispiel: 654321
Zeitzone 	--- 	---
e 	Zeitzonen-Bezeichner (hinzugef�gt in PHP 5.1.0) 	Beispiele: UTC, GMT, Atlantic/Azores
I (gro�es 'i') 	F�llt ein Datum in die Sommerzeit 	1 bei Sommerzeit, ansonsten 0.
O 	Zeitunterschied zur Greenwich time (GMT) in Stunden 	Beispiel: +0200
P 	Zeitunterschied zur Greenwich time (GMT) in Stunden mit Doppelpunkt zwischen Stunden und Minuten (hinzugef�gt in PHP 5.1.3) 	Beispiel: +02:00
T 	Abk�rzung der Zeitzone 	Beispiele: EST, MDT ...
Z 	Offset der Zeitzone in Sekunden. Der Offset f�r Zeitzonen westlich von UTC ist immer negativ und f�r Zeitzonen �stlich von UTC immer positiv. 	-43200 bis 50400
Vollst�ndige(s) Datum/Uhrzeit 	--- 	---
c 	ISO 8601 Datum (hinzugef�gt in PHP 5) 	2004-02-12T15:19:21+00:00
r 	Gem�� � RFC 2822 formatiertes Datum 	Beispiel: Thu, 21 Dec 2000 16:01:07 +0200
U 	Sekunden seit Beginn der UNIX-Epoche (January 1 1970 00:00:00 GMT) 	Siehe auch time()
*/

/*
date_default_timezone_set('Europe/Berlin');
echo date("z");
echo PHP_EOL;
$dt = new DateTime("2017-07-31 00:00:00", new DateTimeZone("Europe/Berlin"));
echo "Start: ", $dt->format("Y-m-d H:i:s P"), PHP_EOL;
$dt->modify("+95 days");
echo "End:   ", $dt->format("Y-m-d H:i:s P"), PHP_EOL;
*/


date_default_timezone_set('Europe/Berlin');
$start="01.08.2017 00:00:00";
$end="01.04.2018 23:59:59";


$dt = new DateTime($start, new DateTimeZone("Europe/Berlin"));

$currdate=$start;
while ($dt->getTimestamp()<strtotime($end)) {
	echo $dt->format("Y-m-d H:i:s"),PHP_EOL;
	$dt->modify("+1 days");
} 






?>
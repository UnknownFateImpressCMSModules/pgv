=======================================================
	PhpGedView

	Versjon 3.2
	Copyright 2004 John Finlay og andre

	Denne og annen informasjon kan du finne online p�
	http://www.PhpGedView.net

	# $Id: readme-norsk.txt,v 1.1 2004/12/03 14:00:28 eikland Exp $
=======================================================

INNHOLD
	 1. LISENS
	 2. INTRODUKSJON
	 3. HURTIG-INSTALLASJON
	 4. INSTALLATION
	 5. OPPDATERING
	 6. OPPDATERING AV GEDCOM
	 7. STILER (THEMES) (se i filen readme.txt)
	 8. MULTIMEDIA OBJEKT
	 9. RSS FEED (se i filen readme.txt)
	10. DATABASE TABELL LAYOUT (se i filen readme.txt)
	11. MANUELL KONFIGURASJON (se i filen readme.txt)
	12. SIKKERHET / PERSONVERN
	13. SPR�K (se i filen readme.txt)
	14. IKKE-STANDARD GEDCOM KODER (se i filen readme.txt)
	15. EKSTRA SPR�KFILER (se i filen readme.txt)
	16. OVERF�RING FRA SQL TIL INDEX MODUS OG MOTSATT (se i filen readme.txt)

-------------------------------------------------------
1. LISENS

PhpGedView: Genealogy Viewer (Vising av slektshistorie)
Copyright (C) 2002 til 2004  John Finlay og andre

Dette programmet er gratis; du kan gi det bort til andre og/eller endre
det i henhold til betingelsen i GNU General Public License som er utgitt
av Free Software Foundation; enten versjon 2 eller senere.

Dette programmet er distribuert i h�p om at det kan v�re til nytte,
men UTEN NOEN FORM FOR GARANTI; dvs. brukes p� eget ansvar.

Se i filen GPL.txt for mer detaljert lisensinformasjon.

-------------------------------------------------------
2. INTRODUKSJON

Programmet PhpGedView viser slektsinformasjon dynamisk fra en GEDCOM 5.5 fil
i HTML form.  Alt du trenger � gj�re er � legge til en GEDCOM fil og s� vil
PhpGedView gj�re resten.  Da vil du kunne "lage" slekts- og etterslekttre,
unders�ke slektskap, se p� slektskalender, se p� lister og s�ke i slektsdataene.
Fordi alt er online, s� kan du lett dele familiehistorien din med resten av
familien din uansett hvor de m�tte befinne seg i verden.
For mer informasjon og for � se nettsider som bruker phpGedView, bes�k
http://www.PhpGedView.net/

PhpGedView er testet p� installasjoner av PHP 4.1.0 og h�yere, men for best
mulig sikkerhet b�r du bruke PhpGedView med PHP versjon 4.3.x eller h�yere.

Under konfigureringen vil du bli spurt om du vil bruke Database eller Indeks
for lagring av dataene.  Du kan bytte fra Indeks eller Database modus p� et
hvilket som helst tidspunkt. Du m� imidlertid importere Gedcomen(e) p� nytt.

PhpGedView er et �pen Kilde program som har blitt lagd av personer fra mange land
og som frivillig har gitt av sin tid og kompetanse.  All vedlikehold, brukerst�tte
og videreutvikling er avhengig av den tid utviklere og oversettere er villig til
� bruke (ofte p� bekostning av arbeid, fritid/avkobling og familie).  
Utenom bidrag som vi mottar fra brukere, s� mottar utviklerne og oversetterene
ingen kompensasjon for den tiden de bruker med � jobbe med programmet.
Det er for tiden heller ingen fond eller sponsorere som st�tter prosjektet.

-------------------------------------------------------
3. HURTIG INSTALLASJON

F�lg instruksjonene i denne delen for � installere PhpGedView dersom du er kjent
med programmet eller er kjent med installing av andre PHP nettprogram.

1. Last opp (upload) filene til nettjeneren (webserver) din
2. Sett skrive-rettigheter til filen config.php og katalogen "index". (chmod 777)
   (For optimal sikkerhet, b�r du flytte katalogen "index" til en plassering
   "utenfor" nettomr�det ditt.)
3. Start nettleseren din og angi internett-adressen til katalogen du lastet opp
   PhpGedView-filene dine (http://www.dintjener.no/PhpGedView/)
4. Fyll ut konfigurasjon-innstillingene dine.  Dersom du flyttet katalogen index,
   husk da p� � spesifisere den riktige plasseringen til denne her.
   Deretter lagrer du konfigurasjon-valgene dine.
5. Oppgi s� standard administrative bruker (deg)
6. Logg deg inn som denne brukeren og last opp gedcom filen(e) dine
7. Lagre gedcom konfigurasjons-valgene
8. Importer gedcom-filen

Tilleggs-muligheter
9.  Dersom du �nsker � bruke muligheten for � endre spr�kfilene, m� du gi
    skrive-rettigheter til alle filene i katalogen ./languages
10. Dersom du �nsker � laste opp media-filer (bilder) ved � bruke delen
    Last opp media-filer i admin-menyen, s� m� du gi skrive-rettigheter til
	katalogene ./media og ./media/thumbs
11. Dersom du �nsker � redigere gedcom filen(e) dine online, s� m� gi bruker(e)
	rettigheter til � gj�re endringer i gedcom-filen. (I brukermeny)
12. Dersom du �nsker � bruke sikkerhetskopi-funksjonen i oppgraderings-delen
    i PhpGedView, m� du enten sette skrive-rettigheter p� hoved-katalogen til
	PhpGedView eller lage en sikkethetskopi-katalog med skrive-rettigheter.
	Skrive-rettighetene for hoved-katalogen til PhpGedView b�r endres med en
	gang du har opprettet en egen katalog for sikkerhetskopi med skrive-rettighet.
13. P� grunn av hensyn til sikkerhet b�r du sette rettighetene tilbake til Kun-lese
    (read-only) n�r du er ferdig med � endre eller oppdatere filer.

-------------------------------------------------------
4. INSTALLASJON

F�lg disse instruksjonene dersom du ikke er kjent med PhpGedView eller
installering av PHP program.

*A. Laste opp (overf�re) filer:
For � installere PhpGedView m� du overf�re filene til en katalog p� hjemmesiden
(internett-adressen) din ved hjelp av et FTP-program (finnes gratis p� internett).
Dersom du har begrenset plass p� hjemmesiden din, kan du spare plass p�
f�lgende m�ter:
1. Slett de Stilen (themes) i katalogen ./themes som du ikke vil bruke
3. Slett noen av spr�kfilene i katalogen ./languages som du ikke �nsker

*B. N�dvendige rettigheter for filene:
PhpGedView krever at lese (Read) rettigheter er satt for alle filer i rot-
katalogen til PhpGedView (standard).  Noen nettverter krever ogs� at rettigheten
Execute (chmod 755).  PhpGedView krever full skrive-rettigheter til katalogen
index (chmod 777 i de fleste verts-konfigurasjonene).  PhpGedView krever ogs�
at skrive-rettigheter (chmod 777) er midlertidig satt for filen config.php
(denne kan endres etter at du er ferdig med � konfigurere phpGedView).

Som en hjelp for � angi riktige rettigheter, s� er det en fil som heter
setpermissions.php og som ligger i rot-katalogen til phpGedView. Denne filen
vil pr�ve � sette rettigheten 777 til filen config.php, katalogen ./index,
og alle filene i katalogen ./index.
Fordi innstillingene hos ulike verter varierer med hensyn til � f� lov til �
la PHP program f� sette rettigheter til filer og kataloger, m� denne filen
kj�res manuelt f�r du starter konfigurasjonen av phpGedView.
Start nettleseren din og angi adressen til der du lastet opp phpGedView og s�
setpermissions.php (http://www.dintjener.no/PhpGedView/setpermissions.php)
Hvis du under installasjonen skulle f� problemer, sjekk rettighetene dine p� nytt.

Det er noen avanserte valg som krever at flere skrive-rettigheter blir satt.
Se punktene 9-13 i Hurtig installasjon over.

*C. Konfigurasjon av phpGedView
Start nettleseren din og angi adressen til katalogen til PhpGedView
(http://www.dintjener.no/PhpGedView/) for automatisk � begynne online
konfigurasjon-prosedyren.  Informasjon om hvert valg i konfigureringen kan
du f� ved � klikke p� sp�rsm�lstegnet (?) bak hver ledetekst.

Det f�rste valget i konfigureringen er � bestemme om du �nsker � bruke
Index filer eller en Database for � lagre slektsdataene dine n�r du skal
importere Gedcom-filen(e) din(e).
Velger du Index filer, vil gedcom-postene dine bli importert til en tekstfil
der dataene blir lagret i serier, og som vil bli plassert i katalogen
"index".
Velger du Database, vil gedcom-postene bli importert til en PEAR:DB st�ttet
database som for eksempel MySQL eller PostgreSQL.

Velger du en Database, m� dette nettjeneren di tilby dette. Database krever
at du er registert  i databasen med brukernavn og passord.
Du kan finne mer informasjon om du b�r velge index filer eller database i 
FAQs (OfteStilteSp�rsm�l)
(http://www.PhpGedView.net/faq.php)

Velger du Index filer trenger du ikke noe brukernavn eller passord for �
lagre dataene.

Du kan endre konfigurasjonen av PhpGedView n�r som helst ved � logge deg inn
som administrator og g� til admin-menyen og klikke p� valget "Konfigurasjon".

*D. Opprette Admin bruker
Etter at du har lagre konfigurasjons-valgene, vil du f� en ny side der du blir
bedt om � legge inn opplysninger om en administrativ bruker (administrator).
N�r du er ferdig med dette og lagret opplysningene, vil du f� opp en ny side
for "Sleksfiler og personvern".  Her kan du legge til gedcom-filer, endre
opplysninger om hver gedcom fil og importere en gedcom fil.

*E. Legge til Gedcom fil
For � legge til GEDCOM filer, kan du laste opp GEDCOM filen din ved � bruke
valget "Last opp Gedcom".  Alle filer som blir lastet opp ved hjelp av dette
valget, blir automatisk lagret i katalogen ./index.  Du kan ogs� laste opp
gedcom filen din manuelt ved � bruke et FTP-program.  De fleste nettverter
har satt en grense for hvor store filer som kan lastes opp fra et PHP-program
ut fra hensyn til sikkerhet. Derfor kan du bli tvunget til � bruke den
manuelle metoden.

*F. Konfigurasjon av Gedcom-filen
Etter at du har lastet opp gedcom filen din, vil du f� opp en side med ulike
valg for slektsfilen.  Her har du mange valgmuligheter i tillegg til at de er
delt opp som Standard/Avanserte/Meta-valg.  P� samme m�te som overalt ellers
i phpGedView, finner du ogs� her informasjon om hvert valg i konfigureringen
ved � klikke p� sp�rsm�lstegnet (?) bak hver ledetekst.

*G. Sjekke dataene i Gedcom filen
Etter at du har lagret konfigurasjons-innstillengen til gedcom filen, vil
PhpGedView sjekke gedcom filen for feil som kan rettes automatisk.  Dersom det
det blir funnet feil i filen som ikke kan rettes automatisk, vil du bli spurt
om hvordan du vil fortsette. Igjen, bruk hjelpetegnet ? for mer informasjon.

*H. Importere Gedcom
N� er du nesten ferdig.  Dette er siste skritt f�r du kan begynne � se p�
dataene dine.  Etter sjekken av Gedcom og eventuelle rettinger av feil, kan du
starte importeringen av gedcom-filen.  Under importen vil du se en linje som
forteller deg progresjonen og etterp� vil du se en tabell med hvilke type
poster som ble importert.  Da er du ferdig med installeringen og kan begynne
� bruk phpGedView!

*I. Slette en Gedcom
Fra siden "Slektsfiler og Personvern" kan du ogs� slette importerte Gedcom-data.
Slettingen vil bare gj�res i datalageret til phpGedView , men den originale
gedcom-filen som du lastet opp, vil ikke bli slettet (du kan med andre ord
importere den p� nytt.

*J. Forandre rettighetene til filen config.php
Av hensyn til sikkerhet b�r du sette rettighetene til filen config.php tilbake til
kun lesbar (read-only)(chmod 755) n�r du er ferdig med konfigureringen av
phpGedView. Skrivetilgang er bare n�dvendig dersom du �nsker � gj�re endringer
under valget Konfigurering av phpGedView i Admin-menyen.  Alle andre innstillinger
vil bli lagret i katalogen ./index.

*K. Forandre en stil (Theme)
Du kan forandre utseende og annen layout av PhpGedView ved � redigere en av de
vedlagte stilartene (dette krever imidlertid litt kunnskaper om CSS).
Se egen omtale av THEMES i readme.txt for mer informasjon.

*L. HTTP komprimering
Sider som blir lagd av PhpGedView kan bli store og bruke lang tid � bli vist p�
skjermen (avhengig av brukeres hastighet p� internettoppkoblingen / b�ndbredde).
Dette kan bedres ved � komprimere sidene f�r de blir sendt over til deg som
bruker ved hjelp av "gzip-kompresjon". Det kan f�re til at data som sendes
fra hjemmesiden kan bli redustert opptil 90% (testet til mellom 80% - 90%).
Dersom nettjeneren du bruker er drevet av Apache, kan dette settes opp veldig
enkelt ved � legge til f�lgende 2 linjer i php.ini filen:

php_flag zlib.output_compression On
php_value zlib.output_compression_level 5

Dersom du ikke har tilgang til filen php.ini, kan du lage en tekstfil med navnet
.htaccess (husk punktum) og skrive inn de 2 linjene der (eller legge dem til i
en eksisterende .htaccess fil) og s� laste filen opp til rot-katalogen til
PhpGedView.
PS: Dersom nettverten din bruker mod_gzip eller en annen metode for � komprimere,
kan opplasting av filen .htaccess skape problemer for siden din. Kompresjon vil
ikke ha noen effekt for nettlesere som ikke st�tter dette. Du kan teste om
komprimeringen virker ved � g� til nettstedet http://leknor.com/code/gziped.php


Dersom du trenger hjelp eller annen st�tte, bes�k nettstedet:
http://www.PhpGedView.net/support.php

-------------------------------------------------------
5. OPPDATERING

Gj�r f�lgende for � oppgradere fra v3.x.  De f�lgende punktene krever
at du er kjent med PhpGedView og at n�v�rende installasjon virker.

1. Lag en sikkerhetskopi av filen config.php og katalogen ./index.
   Dersom du har endret en stil eller spr�kfil, b�r ogs� disse tas kopi av.
2. Last opp (upload) alle nye filer i den nye versjonen med UNNTAK av filen
   config.php og katalogene ./index og ./media.  Dersom disse blir overskrevet,
   vil du miste alle innstillinger og registerte brukere.
3. Sett skrive-rettigheter for filen config.php og katalogen ./index.
4. Importer p� nytt Gedcom filene dine ved � g� til Admin->Slektsfiler og
   personvern->Importer
5. �pne (og eventuelt endre) og lagre filene konfigurasjon og personvern til
   gedcom filen(e) dine for � legge til de siste endringer.
6. Dersom du bruker en endret stil (theme), m� du oppdatere stilen din med de
   nye style-oppsett og variabler.
   
   PS1. Et utmerket verkt�y som kan hjelpe deg � oppdatere stiler er WinMerge
        (http://winmerge.sourceforge.net/).
   
   PS2. Endringer i stilartene kan du finne i Style Guide dokumentasjonen
        (http://www.PhpGedView.net/styleguide.php)

For oppgradering fra tidligere versjoner, les om dette i readme.txt.

-------------------------------------------------------
6. OPPDATERING AV GEDCOM

N�r du har gjort endringer i slektsdataene p� din lokale PC, er det ikke n�dvendig
� slette slektsdataene dine i PhpGedView og begynne p� nytt.  F�lg disse punktene
for � oppgradere en Gedcom som allerede er importert:

1. Bruk FTP eller en annen metode for � laste opp filen til hjemmesiden/nettstedet
   ditt og erstatt den gamle gedcom filen din med den nye.  Dersom du bruker valget
   i phpGedView for laste opp en gedcomfil, vil den gamle filen ikke ble slettet,
   men f� et nytt navn.
2. Importer Gedcom-filen p� nytt ved � velge Importer p� siden for Slektsfiler og
   personvern. Den nye Gedcom filen vil bli sjekket for feil f�r den blir importert.
3. phpGedView vil si i fra at en gedcomfil med det samme navnet allerede er
   importert og om du �nsker � erstatte de gamle dataene.  Klikk da p� knappen "Ja".
4. N�r importen er ferdig, vil de nye dataene v�re lagret og vil bli vist.
   
PS1. Dersom du eller andre registerte brukere har gjort endringer online, b�r du
     eksportere gedcomfilen fra phpGedView til slektsprogrammet ditt f�r du gj�r
	 de nye endringene. P� denne m�ten mister du ikke endringer som er gjort
	 online.
PS2. Dersom du bruker en database for � lagre dataene i phpGedView kan du bruke
	 Brother Keepers klonen GDBI for � vedlikeholde slektsopplysningene online.
	 (http://gdbi..sourceforge.net/).
	 P� denne m�ten vil phpGedView alltid v�re oppdatert og du kan hente ned
	 (eksportere) siste oppdaterte versjon til en hver tid til din lokale PC
	 om du skulle �nske dette.

-------------------------------------------------------
7. STILER (THEMES) 

Se i filen readme.txt

-------------------------------------------------------
8. MULTIMEDIA OBJEKT

GEDCOM 5.5 standarden st�tter multimedia filer av alle typer.  For �yeblikket
st�tter PhpGedView bare multimedia objekt som eksterne filer.  Multimedia
flettet inn i GEDCOM filen vil bli ignorert.  For � bruke multimedia i
PhpGedView, m� du ha en ekstern link til multimedia filene i GEDCOM filen din
til katalogen ./media.
(./media/bilde_meg.jpg  eller  ./media/thumb/passfoto_meg.jpg)

For � velge hvilket bilde som skal brukes i diagram, vil PhpGedView velge det
f�rste med _PRIM Y merket.  Dersom det ikke er noe _PRIM merke i media objekt
posten din, s� vil det f�rste objektet som blir funnet, brukt.  Du kan velge �
ikke vise et bilde i diagram for en bestemt person ved � sette _PRIM N p� dette
eller alle media objekt. De fleste slektsprogram vil gj�re dette automatisk
for deg.

Du kan finne alle referanser til bilder i filen din ved � �pne gedcom filen i
et tekstredigeringsprogram (f.eks. Notepad) og se etter ordene OBJE eller FILE.

PhpGedView har ogs� katalogen "media/thumbs" hvor du kan legge "passfoto" av
bildene dine for visning i lister og andre sider.  PhpGedView tillater at du
kan lage dine egne passfoto slik at du kan ha kunstnerisk kontroll over bildene
og � unng� installering av andre bildeprogram p� netter.
Lag en kopi av bildene og forminske dem til en passende passfoto-st�rrelse p� 
rundt 100px bred og last dem opp i katalogen "media/thumbs". Gi kopien det samme
navnet som originalen.
Passfoto kan lages for ikke-bilde media filer ogs�.  For � gj�re dette, s� lager
du et passfoto-bilde i enten gif, jpeg, png eller bmp format og gir dem det
samme navnet som media filen og med det samme fil-etternavnet (selv om filen er
et ikke-bilde slik som en PDF eller en AVI fil, s� gi passfotoet navn med PDF
eller AVI som fil-etternavn).

Det finnes en BildeModul (ImageModule) som er integreres helt med PGV og som
automatisk vil lage passfoto for deg hvis du bruker den for � laste opp filene
dine.  Den er ikke inkludert sammen med PGV filene fordi den krever at det
finnes visse bibliotek p� nettjeneren som ikke all verter har installert.
Men du burde f� den til � virke ved � f�lge de instruksjonene som f�lger med.
Du kan hente (download) ImageModule fra:
http://sourceforge.net/project/showfiles.php?group_id=55456&package_id=88140

Du kan konfigurere PhpGedView til � finne underkataloger i media katalogen din.
Underkatalogene m� ha samme navn som underkatalogene i mediafil-stiene peker til
i GEDCOM filen din.
For eksempel dersom du har f�lgende media-referanser i GEDCOM filen din:
C:\Bilder\Slekt\foto.jpg
C:\Bilder\Scan\scan1.jpg
scan2.jpg

Med mappeniv�er for media satt til 1, m� du lage f�lgende struktur p� katalogene:
media/Slekt/foto.jpg
media/Scan/scan1.jpg
media/scan2.jpg
media/thumbs/Slekt/foto.jpg
media/thumbs/Scan/scan1.jpg
media/thumbs/scan2.jpg

Med mappeniv�er for media satt til 2, m� du lage f�lgende struktur p� katalogene:
media/Bilder/Slekt/foto.jpg
media/Bilder/Scan/scan1.jpg
media/scan2.jpg
media/thumbs/Bilder/Slekt/foto.jpg
media/thumbs/Bilder/Scan/scan1.jpg
media/thumbs/scan2.jpg

-------------------------------------------------------
9. RSS FEED

Se i filen readme.txt

-------------------------------------------------------
10. DATABASE TABELL LAYOUT

Se i filen readme.txt

-------------------------------------------------------
11. MANUELL KONFIGURASJON

Se i filen readme.txt

-------------------------------------------------------
12. SIKKERHET / PERSONVERN

Selv om PhpGedView gir deg mulighet for � skjule detaljer om levende personer,
b�r du f�rst innhente tillatelse fra HVER levende person du �nsker � inkludere.
Det er mange mennesker som ikke �nsker at navnet deres skal knyttes en familie
p� et offentlig sted som internett er og deres �nsker b�r respekteres og holdes.
De fleste slektsprogram (familie-historie-program) gir deg valg for hvem som
skal bli inkludert i gedcom-filen din ved eksport.  Det aller sikreste valget
er � ikke inkludere levende personer i det hele tatt n�r du eksporterer slekts-
dataene til en GEDCOM fil.

Dersom du �nsker � sikre deg mot at GEDCOM filen din kan bli lastet ned fra
internett, b�r du plassere den et helt annet sted enn i en katalog p� nettjeneren
(hjemmesiden din) eller en virtuell vert og angi denne verdien i $GEDCOM
variabelen, slik at denne peker til den nye plasseringen.
For eksempel:
Dersom hjemme-katalogen din er noe lignende "/home/brukernavn" og rot-katalogen
til nettstedet ditt er "/home/brukernavn/public_html" og du har installert
PhpGedView i katalogen "public_html/PhpGedView", da kan du plassere GEDCOM filen
din p� rot-katalogen (p� samme niv� som katalogen "public_html").  Du kan da angi
stien til filen til "/home/brukernavn/gedcom.ged" ved � endre gedcom
konfigurasjons-filen.

Du kan ogs� manuelt angi plasseringen ved � endre "path" linjen i index/gedcoms.php:
	$gedarray["path"] = "../../gedcom.ged";
eller
	$gedarray["path"] = "/home/brukernavn/gedcom.ged";

Siden GEDCOM filen er plassert i en katalog p� utsiden av rot-katalogen til nett-
tjeneren din, vil nettjeneren din ikke kunne fullf�re �nsker om � nedlasting
(download).  Men, PhpGedView vil fremdeles kunne lese den og vise data fra den.

NB !!!
Til syvende og sist er DU ansvarlig for � garantere at opplysningene du har vist
ikke har brutt noen av reglene for personvern og DU kan bli holdt ansvarlig dersom
privat informasjon blir offentliggjort p� nettstedet du administrerer.

For flere valg om personvern, bes�k:
http://www.PhpGedView.net/privacy.php

-------------------------------------------------------
13. SPR�K

Se i filen readme.txt

-------------------------------------------------------
14. IKKE-STANDARD GEDCOM KODER

Se i filen readme.txt

-------------------------------------------------------
15. EKSTRA SPR�KFILER

Se i filen readme.txt

-------------------------------------------------------
16. OVERF�RING FRA SQL TIL INDEX MODUS OG MOTSATT

Se i filen readme.txt

===========================================================

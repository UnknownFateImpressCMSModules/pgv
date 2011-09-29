=======================================================
	PhpGedView

	Versjon 3.2
	Copyright 2004 John Finlay og andre

	Denne og annen informasjon kan du finne online på
	http://www.PhpGedView.net

	# $Id: readme-norsk.txt,v 1.1 2005/10/07 18:12:20 skenow Exp $
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
	13. SPRÅK (se i filen readme.txt)
	14. IKKE-STANDARD GEDCOM KODER (se i filen readme.txt)
	15. EKSTRA SPRÅKFILER (se i filen readme.txt)
	16. OVERFØRING FRA SQL TIL INDEX MODUS OG MOTSATT (se i filen readme.txt)

-------------------------------------------------------
1. LISENS

PhpGedView: Genealogy Viewer (Vising av slektshistorie)
Copyright (C) 2002 til 2004  John Finlay og andre

Dette programmet er gratis; du kan gi det bort til andre og/eller endre
det i henhold til betingelsen i GNU General Public License som er utgitt
av Free Software Foundation; enten versjon 2 eller senere.

Dette programmet er distribuert i håp om at det kan være til nytte,
men UTEN NOEN FORM FOR GARANTI; dvs. brukes på eget ansvar.

Se i filen GPL.txt for mer detaljert lisensinformasjon.

-------------------------------------------------------
2. INTRODUKSJON

Programmet PhpGedView viser slektsinformasjon dynamisk fra en GEDCOM 5.5 fil
i HTML form.  Alt du trenger å gjøre er å legge til en GEDCOM fil og så vil
PhpGedView gjøre resten.  Da vil du kunne "lage" slekts- og etterslekttre,
undersøke slektskap, se på slektskalender, se på lister og søke i slektsdataene.
Fordi alt er online, så kan du lett dele familiehistorien din med resten av
familien din uansett hvor de måtte befinne seg i verden.
For mer informasjon og for å se nettsider som bruker phpGedView, besøk
http://www.PhpGedView.net/

PhpGedView er testet på installasjoner av PHP 4.1.0 og høyere, men for best
mulig sikkerhet bør du bruke PhpGedView med PHP versjon 4.3.x eller høyere.

Under konfigureringen vil du bli spurt om du vil bruke Database eller Indeks
for lagring av dataene.  Du kan bytte fra Indeks eller Database modus på et
hvilket som helst tidspunkt. Du må imidlertid importere Gedcomen(e) på nytt.

PhpGedView er et Åpen Kilde program som har blitt lagd av personer fra mange land
og som frivillig har gitt av sin tid og kompetanse.  All vedlikehold, brukerstøtte
og videreutvikling er avhengig av den tid utviklere og oversettere er villig til
å bruke (ofte på bekostning av arbeid, fritid/avkobling og familie).  
Utenom bidrag som vi mottar fra brukere, så mottar utviklerne og oversetterene
ingen kompensasjon for den tiden de bruker med å jobbe med programmet.
Det er for tiden heller ingen fond eller sponsorere som støtter prosjektet.

-------------------------------------------------------
3. HURTIG INSTALLASJON

Følg instruksjonene i denne delen for å installere PhpGedView dersom du er kjent
med programmet eller er kjent med installing av andre PHP nettprogram.

1. Last opp (upload) filene til nettjeneren (webserver) din
2. Sett skrive-rettigheter til filen config.php og katalogen "index". (chmod 777)
   (For optimal sikkerhet, bør du flytte katalogen "index" til en plassering
   "utenfor" nettområdet ditt.)
3. Start nettleseren din og angi internett-adressen til katalogen du lastet opp
   PhpGedView-filene dine (http://www.dintjener.no/PhpGedView/)
4. Fyll ut konfigurasjon-innstillingene dine.  Dersom du flyttet katalogen index,
   husk da på å spesifisere den riktige plasseringen til denne her.
   Deretter lagrer du konfigurasjon-valgene dine.
5. Oppgi så standard administrative bruker (deg)
6. Logg deg inn som denne brukeren og last opp gedcom filen(e) dine
7. Lagre gedcom konfigurasjons-valgene
8. Importer gedcom-filen

Tilleggs-muligheter
9.  Dersom du ønsker å bruke muligheten for å endre språkfilene, må du gi
    skrive-rettigheter til alle filene i katalogen ./languages
10. Dersom du ønsker å laste opp media-filer (bilder) ved å bruke delen
    Last opp media-filer i admin-menyen, så må du gi skrive-rettigheter til
	katalogene ./media og ./media/thumbs
11. Dersom du ønsker å redigere gedcom filen(e) dine online, så må gi bruker(e)
	rettigheter til å gjøre endringer i gedcom-filen. (I brukermeny)
12. Dersom du ønsker å bruke sikkerhetskopi-funksjonen i oppgraderings-delen
    i PhpGedView, må du enten sette skrive-rettigheter på hoved-katalogen til
	PhpGedView eller lage en sikkethetskopi-katalog med skrive-rettigheter.
	Skrive-rettighetene for hoved-katalogen til PhpGedView bør endres med en
	gang du har opprettet en egen katalog for sikkerhetskopi med skrive-rettighet.
13. På grunn av hensyn til sikkerhet bør du sette rettighetene tilbake til Kun-lese
    (read-only) når du er ferdig med å endre eller oppdatere filer.

-------------------------------------------------------
4. INSTALLASJON

Følg disse instruksjonene dersom du ikke er kjent med PhpGedView eller
installering av PHP program.

*A. Laste opp (overføre) filer:
For å installere PhpGedView må du overføre filene til en katalog på hjemmesiden
(internett-adressen) din ved hjelp av et FTP-program (finnes gratis på internett).
Dersom du har begrenset plass på hjemmesiden din, kan du spare plass på
følgende måter:
1. Slett de Stilen (themes) i katalogen ./themes som du ikke vil bruke
3. Slett noen av språkfilene i katalogen ./languages som du ikke ønsker

*B. Nødvendige rettigheter for filene:
PhpGedView krever at lese (Read) rettigheter er satt for alle filer i rot-
katalogen til PhpGedView (standard).  Noen nettverter krever også at rettigheten
Execute (chmod 755).  PhpGedView krever full skrive-rettigheter til katalogen
index (chmod 777 i de fleste verts-konfigurasjonene).  PhpGedView krever også
at skrive-rettigheter (chmod 777) er midlertidig satt for filen config.php
(denne kan endres etter at du er ferdig med å konfigurere phpGedView).

Som en hjelp for å angi riktige rettigheter, så er det en fil som heter
setpermissions.php og som ligger i rot-katalogen til phpGedView. Denne filen
vil prøve å sette rettigheten 777 til filen config.php, katalogen ./index,
og alle filene i katalogen ./index.
Fordi innstillingene hos ulike verter varierer med hensyn til å få lov til å
la PHP program få sette rettigheter til filer og kataloger, må denne filen
kjøres manuelt før du starter konfigurasjonen av phpGedView.
Start nettleseren din og angi adressen til der du lastet opp phpGedView og så
setpermissions.php (http://www.dintjener.no/PhpGedView/setpermissions.php)
Hvis du under installasjonen skulle få problemer, sjekk rettighetene dine på nytt.

Det er noen avanserte valg som krever at flere skrive-rettigheter blir satt.
Se punktene 9-13 i Hurtig installasjon over.

*C. Konfigurasjon av phpGedView
Start nettleseren din og angi adressen til katalogen til PhpGedView
(http://www.dintjener.no/PhpGedView/) for automatisk å begynne online
konfigurasjon-prosedyren.  Informasjon om hvert valg i konfigureringen kan
du få ved å klikke på spørsmålstegnet (?) bak hver ledetekst.

Det første valget i konfigureringen er å bestemme om du ønsker å bruke
Index filer eller en Database for å lagre slektsdataene dine når du skal
importere Gedcom-filen(e) din(e).
Velger du Index filer, vil gedcom-postene dine bli importert til en tekstfil
der dataene blir lagret i serier, og som vil bli plassert i katalogen
"index".
Velger du Database, vil gedcom-postene bli importert til en PEAR:DB støttet
database som for eksempel MySQL eller PostgreSQL.

Velger du en Database, må dette nettjeneren di tilby dette. Database krever
at du er registert  i databasen med brukernavn og passord.
Du kan finne mer informasjon om du bør velge index filer eller database i 
FAQs (OfteStilteSpørsmål)
(http://www.PhpGedView.net/faq.php)

Velger du Index filer trenger du ikke noe brukernavn eller passord for å
lagre dataene.

Du kan endre konfigurasjonen av PhpGedView når som helst ved å logge deg inn
som administrator og gå til admin-menyen og klikke på valget "Konfigurasjon".

*D. Opprette Admin bruker
Etter at du har lagre konfigurasjons-valgene, vil du få en ny side der du blir
bedt om å legge inn opplysninger om en administrativ bruker (administrator).
Når du er ferdig med dette og lagret opplysningene, vil du få opp en ny side
for "Sleksfiler og personvern".  Her kan du legge til gedcom-filer, endre
opplysninger om hver gedcom fil og importere en gedcom fil.

*E. Legge til Gedcom fil
For å legge til GEDCOM filer, kan du laste opp GEDCOM filen din ved å bruke
valget "Last opp Gedcom".  Alle filer som blir lastet opp ved hjelp av dette
valget, blir automatisk lagret i katalogen ./index.  Du kan også laste opp
gedcom filen din manuelt ved å bruke et FTP-program.  De fleste nettverter
har satt en grense for hvor store filer som kan lastes opp fra et PHP-program
ut fra hensyn til sikkerhet. Derfor kan du bli tvunget til å bruke den
manuelle metoden.

*F. Konfigurasjon av Gedcom-filen
Etter at du har lastet opp gedcom filen din, vil du få opp en side med ulike
valg for slektsfilen.  Her har du mange valgmuligheter i tillegg til at de er
delt opp som Standard/Avanserte/Meta-valg.  På samme måte som overalt ellers
i phpGedView, finner du også her informasjon om hvert valg i konfigureringen
ved å klikke på spørsmålstegnet (?) bak hver ledetekst.

*G. Sjekke dataene i Gedcom filen
Etter at du har lagret konfigurasjons-innstillengen til gedcom filen, vil
PhpGedView sjekke gedcom filen for feil som kan rettes automatisk.  Dersom det
det blir funnet feil i filen som ikke kan rettes automatisk, vil du bli spurt
om hvordan du vil fortsette. Igjen, bruk hjelpetegnet ? for mer informasjon.

*H. Importere Gedcom
Nå er du nesten ferdig.  Dette er siste skritt før du kan begynne å se på
dataene dine.  Etter sjekken av Gedcom og eventuelle rettinger av feil, kan du
starte importeringen av gedcom-filen.  Under importen vil du se en linje som
forteller deg progresjonen og etterpå vil du se en tabell med hvilke type
poster som ble importert.  Da er du ferdig med installeringen og kan begynne
å bruk phpGedView!

*I. Slette en Gedcom
Fra siden "Slektsfiler og Personvern" kan du også slette importerte Gedcom-data.
Slettingen vil bare gjøres i datalageret til phpGedView , men den originale
gedcom-filen som du lastet opp, vil ikke bli slettet (du kan med andre ord
importere den på nytt.

*J. Forandre rettighetene til filen config.php
Av hensyn til sikkerhet bør du sette rettighetene til filen config.php tilbake til
kun lesbar (read-only)(chmod 755) når du er ferdig med konfigureringen av
phpGedView. Skrivetilgang er bare nødvendig dersom du ønsker å gjøre endringer
under valget Konfigurering av phpGedView i Admin-menyen.  Alle andre innstillinger
vil bli lagret i katalogen ./index.

*K. Forandre en stil (Theme)
Du kan forandre utseende og annen layout av PhpGedView ved å redigere en av de
vedlagte stilartene (dette krever imidlertid litt kunnskaper om CSS).
Se egen omtale av THEMES i readme.txt for mer informasjon.

*L. HTTP komprimering
Sider som blir lagd av PhpGedView kan bli store og bruke lang tid å bli vist på
skjermen (avhengig av brukeres hastighet på internettoppkoblingen / båndbredde).
Dette kan bedres ved å komprimere sidene før de blir sendt over til deg som
bruker ved hjelp av "gzip-kompresjon". Det kan føre til at data som sendes
fra hjemmesiden kan bli redustert opptil 90% (testet til mellom 80% - 90%).
Dersom nettjeneren du bruker er drevet av Apache, kan dette settes opp veldig
enkelt ved å legge til følgende 2 linjer i php.ini filen:

php_flag zlib.output_compression On
php_value zlib.output_compression_level 5

Dersom du ikke har tilgang til filen php.ini, kan du lage en tekstfil med navnet
.htaccess (husk punktum) og skrive inn de 2 linjene der (eller legge dem til i
en eksisterende .htaccess fil) og så laste filen opp til rot-katalogen til
PhpGedView.
PS: Dersom nettverten din bruker mod_gzip eller en annen metode for å komprimere,
kan opplasting av filen .htaccess skape problemer for siden din. Kompresjon vil
ikke ha noen effekt for nettlesere som ikke støtter dette. Du kan teste om
komprimeringen virker ved å gå til nettstedet http://leknor.com/code/gziped.php


Dersom du trenger hjelp eller annen støtte, besøk nettstedet:
http://www.PhpGedView.net/support.php

-------------------------------------------------------
5. OPPDATERING

Gjør følgende for å oppgradere fra v3.x.  De følgende punktene krever
at du er kjent med PhpGedView og at nåværende installasjon virker.

1. Lag en sikkerhetskopi av filen config.php og katalogen ./index.
   Dersom du har endret en stil eller språkfil, bør også disse tas kopi av.
2. Last opp (upload) alle nye filer i den nye versjonen med UNNTAK av filen
   config.php og katalogene ./index og ./media.  Dersom disse blir overskrevet,
   vil du miste alle innstillinger og registerte brukere.
3. Sett skrive-rettigheter for filen config.php og katalogen ./index.
4. Importer på nytt Gedcom filene dine ved å gå til Admin->Slektsfiler og
   personvern->Importer
5. Åpne (og eventuelt endre) og lagre filene konfigurasjon og personvern til
   gedcom filen(e) dine for å legge til de siste endringer.
6. Dersom du bruker en endret stil (theme), må du oppdatere stilen din med de
   nye style-oppsett og variabler.
   
   PS1. Et utmerket verktøy som kan hjelpe deg å oppdatere stiler er WinMerge
        (http://winmerge.sourceforge.net/).
   
   PS2. Endringer i stilartene kan du finne i Style Guide dokumentasjonen
        (http://www.PhpGedView.net/styleguide.php)

For oppgradering fra tidligere versjoner, les om dette i readme.txt.

-------------------------------------------------------
6. OPPDATERING AV GEDCOM

Når du har gjort endringer i slektsdataene på din lokale PC, er det ikke nødvendig
å slette slektsdataene dine i PhpGedView og begynne på nytt.  Følg disse punktene
for å oppgradere en Gedcom som allerede er importert:

1. Bruk FTP eller en annen metode for å laste opp filen til hjemmesiden/nettstedet
   ditt og erstatt den gamle gedcom filen din med den nye.  Dersom du bruker valget
   i phpGedView for laste opp en gedcomfil, vil den gamle filen ikke ble slettet,
   men få et nytt navn.
2. Importer Gedcom-filen på nytt ved å velge Importer på siden for Slektsfiler og
   personvern. Den nye Gedcom filen vil bli sjekket for feil før den blir importert.
3. phpGedView vil si i fra at en gedcomfil med det samme navnet allerede er
   importert og om du ønsker å erstatte de gamle dataene.  Klikk da på knappen "Ja".
4. Når importen er ferdig, vil de nye dataene være lagret og vil bli vist.
   
PS1. Dersom du eller andre registerte brukere har gjort endringer online, bør du
     eksportere gedcomfilen fra phpGedView til slektsprogrammet ditt før du gjør
	 de nye endringene. På denne måten mister du ikke endringer som er gjort
	 online.
PS2. Dersom du bruker en database for å lagre dataene i phpGedView kan du bruke
	 Brother Keepers klonen GDBI for å vedlikeholde slektsopplysningene online.
	 (http://gdbi..sourceforge.net/).
	 På denne måten vil phpGedView alltid være oppdatert og du kan hente ned
	 (eksportere) siste oppdaterte versjon til en hver tid til din lokale PC
	 om du skulle ønske dette.

-------------------------------------------------------
7. STILER (THEMES) 

Se i filen readme.txt

-------------------------------------------------------
8. MULTIMEDIA OBJEKT

GEDCOM 5.5 standarden støtter multimedia filer av alle typer.  For øyeblikket
støtter PhpGedView bare multimedia objekt som eksterne filer.  Multimedia
flettet inn i GEDCOM filen vil bli ignorert.  For å bruke multimedia i
PhpGedView, må du ha en ekstern link til multimedia filene i GEDCOM filen din
til katalogen ./media.
(./media/bilde_meg.jpg  eller  ./media/thumb/passfoto_meg.jpg)

For å velge hvilket bilde som skal brukes i diagram, vil PhpGedView velge det
første med _PRIM Y merket.  Dersom det ikke er noe _PRIM merke i media objekt
posten din, så vil det første objektet som blir funnet, brukt.  Du kan velge å
ikke vise et bilde i diagram for en bestemt person ved å sette _PRIM N på dette
eller alle media objekt. De fleste slektsprogram vil gjøre dette automatisk
for deg.

Du kan finne alle referanser til bilder i filen din ved å åpne gedcom filen i
et tekstredigeringsprogram (f.eks. Notepad) og se etter ordene OBJE eller FILE.

PhpGedView har også katalogen "media/thumbs" hvor du kan legge "passfoto" av
bildene dine for visning i lister og andre sider.  PhpGedView tillater at du
kan lage dine egne passfoto slik at du kan ha kunstnerisk kontroll over bildene
og å unngå installering av andre bildeprogram på netter.
Lag en kopi av bildene og forminske dem til en passende passfoto-størrelse på 
rundt 100px bred og last dem opp i katalogen "media/thumbs". Gi kopien det samme
navnet som originalen.
Passfoto kan lages for ikke-bilde media filer også.  For å gjøre dette, så lager
du et passfoto-bilde i enten gif, jpeg, png eller bmp format og gir dem det
samme navnet som media filen og med det samme fil-etternavnet (selv om filen er
et ikke-bilde slik som en PDF eller en AVI fil, så gi passfotoet navn med PDF
eller AVI som fil-etternavn).

Det finnes en BildeModul (ImageModule) som er integreres helt med PGV og som
automatisk vil lage passfoto for deg hvis du bruker den for å laste opp filene
dine.  Den er ikke inkludert sammen med PGV filene fordi den krever at det
finnes visse bibliotek på nettjeneren som ikke all verter har installert.
Men du burde få den til å virke ved å følge de instruksjonene som følger med.
Du kan hente (download) ImageModule fra:
http://sourceforge.net/project/showfiles.php?group_id=55456&package_id=88140

Du kan konfigurere PhpGedView til å finne underkataloger i media katalogen din.
Underkatalogene må ha samme navn som underkatalogene i mediafil-stiene peker til
i GEDCOM filen din.
For eksempel dersom du har følgende media-referanser i GEDCOM filen din:
C:\Bilder\Slekt\foto.jpg
C:\Bilder\Scan\scan1.jpg
scan2.jpg

Med mappenivåer for media satt til 1, må du lage følgende struktur på katalogene:
media/Slekt/foto.jpg
media/Scan/scan1.jpg
media/scan2.jpg
media/thumbs/Slekt/foto.jpg
media/thumbs/Scan/scan1.jpg
media/thumbs/scan2.jpg

Med mappenivåer for media satt til 2, må du lage følgende struktur på katalogene:
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

Selv om PhpGedView gir deg mulighet for å skjule detaljer om levende personer,
bør du først innhente tillatelse fra HVER levende person du ønsker å inkludere.
Det er mange mennesker som ikke ønsker at navnet deres skal knyttes en familie
på et offentlig sted som internett er og deres ønsker bør respekteres og holdes.
De fleste slektsprogram (familie-historie-program) gir deg valg for hvem som
skal bli inkludert i gedcom-filen din ved eksport.  Det aller sikreste valget
er å ikke inkludere levende personer i det hele tatt når du eksporterer slekts-
dataene til en GEDCOM fil.

Dersom du ønsker å sikre deg mot at GEDCOM filen din kan bli lastet ned fra
internett, bør du plassere den et helt annet sted enn i en katalog på nettjeneren
(hjemmesiden din) eller en virtuell vert og angi denne verdien i $GEDCOM
variabelen, slik at denne peker til den nye plasseringen.
For eksempel:
Dersom hjemme-katalogen din er noe lignende "/home/brukernavn" og rot-katalogen
til nettstedet ditt er "/home/brukernavn/public_html" og du har installert
PhpGedView i katalogen "public_html/PhpGedView", da kan du plassere GEDCOM filen
din på rot-katalogen (på samme nivå som katalogen "public_html").  Du kan da angi
stien til filen til "/home/brukernavn/gedcom.ged" ved å endre gedcom
konfigurasjons-filen.

Du kan også manuelt angi plasseringen ved å endre "path" linjen i index/gedcoms.php:
	$gedarray["path"] = "../../gedcom.ged";
eller
	$gedarray["path"] = "/home/brukernavn/gedcom.ged";

Siden GEDCOM filen er plassert i en katalog på utsiden av rot-katalogen til nett-
tjeneren din, vil nettjeneren din ikke kunne fullføre ønsker om å nedlasting
(download).  Men, PhpGedView vil fremdeles kunne lese den og vise data fra den.

NB !!!
Til syvende og sist er DU ansvarlig for å garantere at opplysningene du har vist
ikke har brutt noen av reglene for personvern og DU kan bli holdt ansvarlig dersom
privat informasjon blir offentliggjort på nettstedet du administrerer.

For flere valg om personvern, besøk:
http://www.PhpGedView.net/privacy.php

-------------------------------------------------------
13. SPRÅK

Se i filen readme.txt

-------------------------------------------------------
14. IKKE-STANDARD GEDCOM KODER

Se i filen readme.txt

-------------------------------------------------------
15. EKSTRA SPRÅKFILER

Se i filen readme.txt

-------------------------------------------------------
16. OVERFØRING FRA SQL TIL INDEX MODUS OG MOTSATT

Se i filen readme.txt

===========================================================

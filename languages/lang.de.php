<?php
/*=================================================
   charset=utf-8
   Projekt:        phpGedView
   Datei:        lang.de.php
   Autor:        John Finlay
   Übersetzung:        Bach Jürgen
                   Norgaz Kurt
                   Pluntke Peter
   Bemerkungen:        Deutsches Sprachen Modul für PhpGedView
   Aufzeichnung der Änderungen:
   15.11.2002        Datei erstellt durch Jürgen Bach
   08.04.2003        Inhalt angepasst an Inhalt der Datei lang.en.php v1.23
                   Anhand der Originalsprachdatei lang.en.php gänzlich neu ins Deutsche übersetzt
   18.05.2003        charset=utf-8 in den Header aufgenommen, um das Erkennen als UTF-8-Datei zu gewährleisten
   18.07.2003        fehlende Begriffe ergänzt,
                   doppeldeutige Begriffe optimiert,
                   Vereinheitlichung der Verbformen (Infinitiv statt Imperativ)
                   Vereinheitlichung der Anrede (Sie statt Du)
                   Ergänzung der fehlenden Abschnittsnamen
                   Sortierung an lang.en.php angepasst
   11.02.2004        Renamed from lang.ge.php to lang.de.php
   2005.02.19 "PhpGedView" and "GEDCOM" made consistent across all language files  G.Kroll (canajun2eh)
===================================================*/
# $Id: lang.de.php,v 1.1 2005/10/07 18:08:36 skenow Exp $
if (preg_match("/lang\...\.php$/", $_SERVER["PHP_SELF"])>0) {
        print "You cannot access a language file directly.";
        exit;
}

$pgv_lang["undo_all_confirm"]		= "Möchten Sie wirklich alle Änderung an dieser GEDCOM-Datei verwerfen?";
$pgv_lang["undo_all"]				= "Alle Änderungen verwerfen";
$pgv_lang["index_edit_advice"]           = "Markieren Sie den Namen eines Blockes und klicken Sie auf eines der Pfeil-Symbole, um den markierten Block in die gewünschte Richtung zu bewegen.";
$pgv_lang["index_edit_advice2"]          = "Sie müssen auf den Button <b>#pgv_lang[save]#</b> klicken, um die Änderungen zu übernehmen.";
$pgv_lang["continue_import2"]            = "Datumsimport fortsetzen";
$pgv_lang["importing_dates"]             = "Datumsangaben importieren";
$pgv_lang["changelog"]                           = "Änderungen in Version #VERSION#";
$pgv_lang["view_changelog"]                      = "Datei changelog.txt ansehen";
$pgv_lang["html_block_descr"]            = "Mit diesem standard HTML Block können Sie einen beliebigen Text auf Ihrer Seite einfügen.";
$pgv_lang["html_block_sample_part1"]	= "<p class=\"blockhc\"><b>Titel hier eingeben</b></p><br /><p>Klicken Sie auf Konfigurieren";
$pgv_lang["html_block_sample_part2"]     = "um diesen Text hier zu ändern.</p>";
$pgv_lang["html_block_name"]             = "HTML";
$pgv_lang["num_to_show"]                 = "Anzahl der anzuzeigenden Objekte";
$pgv_lang["days_to_show"]                        = "Anzahl der anzuzeigenden Tage";
$pgv_lang["before_or_after"]             = "Nummern vor oder hinter den Namen anzeigen?";
$pgv_lang["before"]                                      = "vor";
$pgv_lang["after"]					= "hinter";
$pgv_lang["config_block"]			= "Konfigurieren";
$pgv_lang["pls_note12"]				= "Bitte tragen Sie in diesem Feld ein, warum Sie einen Zugang beantragen und wie Sie mit Personen aus der Datenbank verwandt sind.";
$pgv_lang["enter_comments"]			= "Bitte geben Sie ihre Verwandtschaft zu den Daten im Kommentarfeld ein..";
$pgv_lang["comments"]				= "Kommentar";
$pgv_lang["none"]					= "keiner";
$pgv_lang["ahnentafel_report"]		= "Ahnentafel Bericht";
$pgv_lang["child-family"]			= "Eltern und Geschwister";
$pgv_lang["spouse-family"]			= "Ehepartner und Kinder";
$pgv_lang["direct-ancestors"]		= "Vorfahren in direkter Linie";
$pgv_lang["ancestors"]				= "Vorfahren in direkter Linie und deren Familien";
$pgv_lang["descendants"]			= "Nachfahren";
$pgv_lang["choose_relatives"]		= "Verwandte auswählen";
$pgv_lang["relatives_report"]		= "Verwandten Bericht";
$pgv_lang["total_living"]			= "Insgesamt lebend";
$pgv_lang["total_dead"]				= "Insgesamt verstorben";
$pgv_lang["total_not_born"]			= "Insgesamt noch nicht geboren";
$pgv_lang["remove_custom_tags"]		= "Benutzerdefinierte PGV Tags entfernen? (z.B. _PGVU, _THUM)";
$pgv_lang["download_zipped"]		= "GEDCOM-Datei als Zip-Datei herunterladen?";
$pgv_lang["cookie_login_help"]		= "Sie waren auf dieser Seite schon einmal angemeldet. Sie haben daher Zugriff auf private Daten und andere benutzerdefinierte Eigenschaften. Aus Sicherheitsgründen müssen Sie sich aber erneut anmelden, wenn Sie Konfigurationen vornehmen möchten.";
$pgv_lang["remember_me"]			= "Auf diesem Computer angemeldet bleiben?";
$pgv_lang["add_unlinked_person"]	= "Eine Person ohne Verknüpfung hinzufügen";
$pgv_lang["fams_with_surname"]		= "Familien mit dem Namen #surname#";
$pgv_lang["support_contact"]		= "Kontakt für technische Fragen";
$pgv_lang["genealogy_contact"]		= "Kontakt für genealogische Fragen";
$pgv_lang["continue_import"]		= "Ortsimport fortsetzen";
$pgv_lang["importing_places"]		= "Ortsangaben importieren";
$pgv_lang["common_upload_errors"]	= "Dieser Fehler bedeutet vermutlich, dass die Datei die Sie hochladen wollten, die Grenze ihres Hosts überschreitet. Die Standard-Grenze liegt in PHP bei 2MB. Sie können entweder den Support Ihres Hosts kontaktieren, und ihn bitten, die Grenze in der Datei php.ini zu erhöhen, oder Sie können die Datei per FTP hochladen. Benutzen Sie die Seite <a href=\"uploadgedcom.php?action=add_form\"><b>GEDCOM hinzufügen</b></a>, um eine Datei zu verwenden, die Sie per FTP hochgeladen haben.";
$pgv_lang["total_memory_usage"]		= "Speicherbedarf insgesamt:";
$pgv_lang["mothers_family_with"]	= "Familie der Mutter mit ";
$pgv_lang["fathers_family_with"]	= "Familie des Vaters mit ";
$pgv_lang["halfbrother"]			= "Halbbruder";
$pgv_lang["halfsister"]				= "Halbschwester";
$pgv_lang["family_timeline"]		= "Familie in der Lebensspannenanzeige darstellen";
$pgv_lang["children_timeline"]		= "Kinder in der Lebensspannenanzeige darstellen";
$pgv_lang["other"]					= "Übrige";
$pgv_lang["sort_by_marriage"]		= "Nach Hochzeitsdatum sortieren";
$pgv_lang["reorder_families"]		= "Familien neu ordnen";
$pgv_lang["indis_with_surname"]		= "Personen mit dem Nachnamen #surname#";
$pgv_lang["first_letter_fname"]		= "Wählen Sie einen Buchstaben aus, um Personen anzuzeigen, deren Vornamen mit diesem Buchstaben beginnt.";
$pgv_lang["import_marr_names"]		= "Heirats-Namen importieren";
$pgv_lang["marr_name_import_instr"]	= "Klicken Sie auf diesen Button, wenn PhpGedView die Heirats-Namen von weiblichen Personen in dieser GEDCOM-Datei ermitteln soll. Dann können Sie Ehefrauen auch nach ihren Heirats-Namen suchen oder auflisten lassen.";
$pgv_lang["calc_marr_names"]		= "Heirats-Namen ermitteln";
$pgv_lang["total_names"]			= "Namen insgesamt";

$pgv_lang["upload_file"]			= "Datei von ihrem Computer hochladen";
$pgv_lang["thumb_genned"]			= "Thumbnail wurde automatisch erstellt.";
$pgv_lang["thumbgen_error"]			= "Es kann kein Thumbnail erstellt werden für ";
$pgv_lang["generate_thumbnail"]		= "Thumbnail automatisch erstellen aus ";
$pgv_lang["no_upload"]				= "Multimedia-Dateien können nicht hochgeladen werden, weil Multimedia-Objekte deaktiviert wurden oder weil für das Verzeichnis keine Schreibrechte bestehen.";
$pgv_lang["top10_pageviews_nohits"]	= "Es können keine Treffer angezeigt werden.";
$pgv_lang["top10_pageviews_msg"]	= "Sie müssen in der GEDCOM-Konfiguration die Zähler zunächst aktivieren.";
$pgv_lang["review_changes_descr"]	= "Der \"offene Änderungen\"-Block zeigt Benutzern mit Editierrechten eine Liste der Datensätze die online geändert wurden und die noch kontrolliert und akzeptiert bzw. verworfen werden müssen.<br /><br />Wenn dieser Block aktiviert ist, erhalten Benutzer mit Editierrechten täglich eine Mail, die auf offene Änderungen hinweist.";
$pgv_lang["review_changes_block"]	= "Offene Änderungen";
$pgv_lang["review_changes_email"]	= "Erinnerungs E-Mails versenden?";
$pgv_lang["review_changes_email_freq"]	= "Erinnerungs E-Mails Häufigkeit (Tage)";
$pgv_lang["review_changes_subject"]	= "PhpGedView - Änderungen kontrollieren";
$pgv_lang["review_changes_body"]	= "An einer genealogischen Datenbank wurden Änderungen vorgenommen. Diese Änderungen müssen kontrolliert und akzeptiert werden, bevor sie für alle Nutzer sichtbar werden. Bitte klicken Sie auf die angegebene URL, um auf die PhpGedView-Seite zu gelangen und melden Sie sich an, um die Änderung zu kontrollieren. ";
$pgv_lang["show_spouses"]		= "Ehepartner anzeigen";
$pgv_lang["quick_update_title"] = "Schnelle Aktualisierung";
$pgv_lang["quick_update_instructions"] = "Auf dieser Seite können Sie schnell die Daten einer Person aktualisieren. Sie müssen nur die Daten eintragen, die neu sind oder von den Informationen in der Datenbank abweichen. Nachdem ihre Daten übermittelt wurden, müssen sie noch von einem Administrator kontrolliert und akzeptiert werden, bevor sie für alle Nutzer sichtbar werden.";
$pgv_lang["update_name"] = "Namen aktualisieren";
$pgv_lang["update_fact"] = "Ereignis aktualisieren";
$pgv_lang["update_fact_restricted"] = "Das Aktualisieren dieses Ereignisses ist eingeschränkt:";
$pgv_lang["update_photo"] = "Foto aktualisieren";
$pgv_lang["photo_replace"] = "Möchten Sie ein älteres Foto durch dieses ersetzen?";
$pgv_lang["select_fact"] = "Ein Ereignis auswählen...";
$pgv_lang["update_address"] = "Adresse aktualisieren";
$pgv_lang["add_new_chil"] = "Ein Kind hinzufügen";
$pgv_lang["top10_pageviews_descr"]	= "Dieser Block zeigt die 10 Datensätze an, die am häufigsten aufgerufen wurden. Sie müssen dazu in der GEDCOM-Konfiguration die Zähler zunächst aktivieren.";
$pgv_lang["top10_pageviews"]		= "Meist aufgerufene Einträge";
$pgv_lang["top10_pageviews_block"]		= "Meist aufgerufene Einträge";
$pgv_lang["user_default_tab"]		= "Standardregister zur Anzeige einer Personen-Informationsseite";
$pgv_lang["stepfamily"]				= "Stief-Familie";
$pgv_lang["stepdad"]				= "Stiefvater";
$pgv_lang["stepmom"]				= "Stiefmutter";
$pgv_lang["stepsister"]				= "Stiefschwester";
$pgv_lang["stepbrother"]			= "Stiefbruder";
$pgv_lang["max_upload_size"]		= "Maximale Größe zum Hochladen: ";
$pgv_lang["edit_fam"]				= "Familie editieren";
$pgv_lang["fams_charts"]			= "Optionen für Familie";
$pgv_lang["sort_by_birth"]			= "Nach Geburtstagen sortieren";
$pgv_lang["reorder_children"]		= "Kindern neu ordnen";
$pgv_lang["add_from_clipboard"]		= "Aus der Zwischenablage hinzufügen: ";
$pgv_lang["record_copied"]			= "Datensatz in die Zwischenablage kopiert";
$pgv_lang["copy"]					= "Kopieren";
$pgv_lang["cut"]					= "Ausschneiden";
$pgv_lang["indis_charts"]			= "Optionen für Personen";
$pgv_lang["edit_indi"] 				= "Personen editieren";
$pgv_lang["locked"]					= "nicht verändern!";
$pgv_lang["privacy"]				= "Datenschutz";

//-- GENERAL HELP MESSAGES
$pgv_lang["qm"]                                = "(?)";
$pgv_lang["qm_ah"]                        = "(?)";
$pgv_lang["page_help"]                        = "Hilfe";
$pgv_lang["help_for_this_page"]                = "Hilfe für diese Seite";
$pgv_lang["help_contents"]                = "Hilfe Inhalt";
$pgv_lang["show_context_help"]                = "Kontext-bezogene Hilfe anzeigen";
$pgv_lang["hide_context_help"]                = "Kontext-bezogene Hilfe verbergen";
$pgv_lang["sorry"]                        = "<b>Leider ist die Hilfe für dieses Thema noch nicht fertiggestellt.</b>";
$pgv_lang["help_not_exist"]                = "<b>Die Hilfe für diese Seite oder dieses Element ist noch nicht verfügbar.</b>";
$pgv_lang["var_not_exist"]			= "<span style=font-weight: bold>Diese Sprach-Variable existiert nicht. Bitte melden Sie dies als Fehler.</span>";
$pgv_lang["resolution"]                        = "Bildschirmauflösung";
$pgv_lang["menu"]                        = "Menü";
$pgv_lang["header"]                        = "Kopfbereich";
$pgv_lang["imageview"]                = "Image Viewer";

//-- CONFIG FILE MESSAGES
$pgv_lang["login_head"]                        = "PhpGedView Benutzer Anmeldung";
$pgv_lang["error_title"]                = "FEHLER: GEDCOM-Datei kann nicht geöffnet werden";
$pgv_lang["error_header"]                = "Die GEDCOM-Datei <b>#GEDCOM#</b> befindet sich nicht am angegebenen Ort.";
$pgv_lang["error_header_write"]        = "Die GEDCOM-Datei <b>#GEDCOM#</b> kann nicht gespeichert werden. Bitte prüfen Sie die Schreibrechte und Dateiattribute.";
$pgv_lang["for_support"]                = "Für Support wenden Sie sich an folgende Kontaktadresse:";
$pgv_lang["for_contact"]                = "Bei genealogischen Fragen wenden Sie sich an folgende Kontaktadresse:";
$pgv_lang["for_all_contact"]                = "Für Support sowie bei genealogischen Fragen wenden Sie sich an folgende Kontaktadresse:";
$pgv_lang["build_title"]                = "Index-Dateien werden erstellt";
$pgv_lang["build_error"]                = "Eine neue GEDCOM-Datei wurde gefunden.";
$pgv_lang["please_wait"]                = "Einen Moment bitte: Index Dateien werden neu erstellt.";
$pgv_lang["choose_gedcom"]                = "Eine GEDCOM-Datei auswählen";
$pgv_lang["username"]                        = "Benutzername";
$pgv_lang["invalid_username"]                = "Der Benutzername enthält unzulässige Zeichen";
$pgv_lang["fullname"]                        = "Kompletter Name";
$pgv_lang["password"]                        = "Passwort";
$pgv_lang["confirm"]                        = "Passwort bestätigen";
$pgv_lang["user_contact_method"]        = "Bevorzugte Kontaktaufnahme";
$pgv_lang["login"]                        = "Login";
$pgv_lang["login_aut"]                        = "Benutzereinstellungen bearbeiten";
$pgv_lang["logout"]                        = "Logout";
$pgv_lang["admin"]                        = "Verwalten";
$pgv_lang["logged_in_as"]                = "Angemeldet als";
$pgv_lang["my_pedigree"]                = "Mein Stammbaum";
$pgv_lang["my_indi"]                        = "Mein Datenblatt";
$pgv_lang["yes"]                        = "Ja";
$pgv_lang["no"]                                = "Nein";
$pgv_lang["add_gedcom"]                        = "GEDCOM-Datei hinzufügen";
$pgv_lang["no_support"]                        = "Ihr Browser unterstützt nicht alle Standards, die von dieser PhpGedView Website benutzt werden. Die meisten neueren Browser-Versionen unterstützen diese Funktionen. Bitte besorgen Sie sich eine neuere Version Ihres Browsers.";
$pgv_lang["change_theme"]                = "Theme ändern";
$pgv_lang["gedcom_downloadable"] 	= "Diese GEDCOM-Datei könnte über das Internet heruntergeladen werden!<br />Bitte lesen Sie im Bereich \"SECURITY\" der Datei <a href=\"readme.txt\"><b>readme.txt</b></a> nach, wie Sie dieses Problem lösen können.";

//-- INDEX (PEDIGREE_TREE) FILE MESSAGES
$pgv_lang["index_header"]                = "Stammbaum";
$pgv_lang["gen_ped_chart"]                = "Stammbaum für #PEDIGREE_GENERATIONS# Generationen";
$pgv_lang["generations"]                = "Generationen";
$pgv_lang["view"]                        = "Zeige";
$pgv_lang["fam_spouse"]                        = "Familie mit Ehepartner";
$pgv_lang["root_person"]                = "ID der Startperson";
$pgv_lang["hide_details"]                = "Details verbergen";
$pgv_lang["show_details"]                = "Details zeigen";
$pgv_lang["person_links"]                = "Links zu Diagrammen, Familien und nahen Verwandten dieser Person. Klicken Sie auf dieses Symbol, um die Seite mit dieser Person als Ausgangspunkt aufzurufen.";
$pgv_lang["zoom_box"]                        = "Zoom hinein/heraus";
$pgv_lang["portrait"]                        = "Hochformat";
$pgv_lang["landscape"]                        = "Querformat";
$pgv_lang["start_at_parents"]                = "Bei den Eltern beginnen";
$pgv_lang["charts"]                        = "Diagramme";
$pgv_lang["lists"]                        = "Listen";
$pgv_lang["welcome_page"]                = "Begrüßungs-Seite";
$pgv_lang["max_generation"]                = "Die maximale Anzahl von Generationen beträgt #PEDIGREE_GENERATIONS#.";
$pgv_lang["min_generation"]                = "Die minimale Anzahl von Generationen beträgt 3.";
$pgv_lang["box_width"] 					     = "Box-Breite";

//-- FUNCTIONS FILE MESSAGES
$pgv_lang["unable_to_find_family"]        = "Zu folgender ID kann keine Familie gefunden werden:";
$pgv_lang["unable_to_find_indi"]        = "Zu folgender ID kann keine Person gefunden werden:";
$pgv_lang["unable_to_find_record"]        = "Zu folgender ID kann kein Datensatz gefunden werden:";
$pgv_lang["unable_to_find_source"]        = "Zu folgender ID kann keine Quelle gefunden werden:";
$pgv_lang["unable_to_find_repo"]        = "Kann das Archiv zu folgender ID nicht finden:";
$pgv_lang["repo_name"]                        = "Archiv-Name:";
$pgv_lang["address"]                        = "Adresse:";
$pgv_lang["phone"]                        = "Telefon:";
$pgv_lang["source_name"]                = "Quellen-Name:";
$pgv_lang["title"]                        = "Titel";
$pgv_lang["author"]                        = "Autor:";
$pgv_lang["publication"]                = "Publikation:";
$pgv_lang["call_number"]                = "Telefon Nummer:";
$pgv_lang["living"]                        = "Lebt";
$pgv_lang["private"]                        = "Privat";
$pgv_lang["birth"]                        = "Geburt:";
$pgv_lang["death"]                        = "Tod:";
$pgv_lang["descend_chart"]                = "Nachfahrenbaum";
$pgv_lang["individual_list"]                = "Personen Liste";
$pgv_lang["family_list"]                = "Familien Liste";
$pgv_lang["source_list"]                = "Quellen Liste";
$pgv_lang["place_list"]                        = "Liste der Orte";
$pgv_lang["place_list_aft"] 		= "Liste der Orte in";
$pgv_lang["media_list"]                        = "Multimedia Liste";
$pgv_lang["search"]                        = "Suche";
$pgv_lang["clippings_cart"]                = "Ausschnitts-Sammelbehälter";
$pgv_lang["not_an_array"]                = "keine Reihe (Array)";
$pgv_lang["print_preview"]                = "Ausdruck-optimierte Version";
$pgv_lang["cancel_preview"]                = "Zurück zur normalen Ansicht";
$pgv_lang["change_lang"]                = "Sprache auswählen";
$pgv_lang["print"]                        = "Drucken";
$pgv_lang["total_queries"]                = "Gesamtanzahl der Datenanfragen an die Datenbank:";
$pgv_lang["total_privacy_checks"]		= "Gesamtanzahl der Datenschutz-Überprüfungen:";
$pgv_lang["back"]                        = "zurück";
$pgv_lang["privacy_list_indi_error"]        = "Aus Datenschutzgründen werden eine oder mehrere Personen verborgen.";
$pgv_lang["privacy_list_fam_error"]        = "Aus Datenschutzgründen werden eine oder mehrere Familien verborgen.";

//-- INDIVIDUAL FILE MESSAGES
$pgv_lang["aka"]                        = "auch bekannt als";
$pgv_lang["male"]                        = "männlich";
$pgv_lang["female"]                        = "weiblich";
$pgv_lang["temple"]                        = "HLT Tempel";
$pgv_lang["temple_code"]                = "HLT Tempel Code:";
$pgv_lang["status"]                        = "Status";
$pgv_lang["source"]                        = "Quelle";
$pgv_lang["citation"]                        = "Abschnitt:";
$pgv_lang["text"]                        = "Quellen-Text:";
$pgv_lang["note"]                        = "Notiz";
$pgv_lang["NN"]                                = "(unbekannt)";
$pgv_lang["PN"]                                = "(unbekannt)";
$pgv_lang["unrecognized_code"]                = "Unbekannter GEDCOM-Code";
$pgv_lang["unrecognized_code_msg"]        = "Ein Fehler ist aufgetreten, bitte melden Sie diesen an ";
$pgv_lang["indi_info"]                        = "Persönliche Informationen";
$pgv_lang["pedigree_chart"]                = "Stammbaum";
$pgv_lang["desc_chart2"]                = "Nachfahrenbaum";
$pgv_lang["family"]                        = "Familie";
$pgv_lang["family_with"]                = "Familie mit";
$pgv_lang["as_spouse"]                        = "Familiendaten als Ehepartner";
$pgv_lang["as_child"]                        = "Familiendaten als Kind";
$pgv_lang["view_gedcom"]                = "GEDCOM-Datensatz anzeigen";
$pgv_lang["add_to_cart"]                = "Datensatz dem Sammelbehälter hinzufügen";
$pgv_lang["still_living_error"]                = "Diese Person lebt noch oder hat keine Geburts- oder Sterbedaten. Alle Details lebender Personen sind verborgen.<br />Für weitere Informationen schreiben Sie an";
$pgv_lang["privacy_error"]                = "Details dieser Person sind vertraulich.<br />";
$pgv_lang["more_information"]                = "Für weitere Informationen wenden Sie sich an folgende E-Mail Adresse:";
$pgv_lang["name"]                        = "Name";
$pgv_lang["given_name"]                        = "Vorname:";
$pgv_lang["surname"]                        = "Nachname:";
$pgv_lang["suffix"]                        = "Namenszusatz:";
$pgv_lang["object_note"]                = "Objekt-Notiz:";
$pgv_lang["sex"]                        = "Geschlecht";
$pgv_lang["personal_facts"]                = "Persönliche Fakten und Details";
$pgv_lang["type"]                        = "Typ";
$pgv_lang["date"]                        = "Datum";
$pgv_lang["place_description"]                = "Ort / Beschreibung";
$pgv_lang["parents"]                        = "Eltern:";
$pgv_lang["siblings"]                        = "Geschwister";
$pgv_lang["father"]                        = "Vater";
$pgv_lang["mother"]                        = "Mutter";
$pgv_lang["relatives"]                        = "Direkte Verwandschaft";
$pgv_lang["child"]                        = "Kind";
$pgv_lang["spouse"]                        = "Ehepartner";
$pgv_lang["surnames"]                        = "Nachname";
$pgv_lang["adopted"]                        = "adoptiert";
$pgv_lang["foster"]                        = "Vormund";
$pgv_lang["sealing"]                        = "Siegelung";
$pgv_lang["challenged"]				= "Aufforderung";

$pgv_lang["disproved"]				= "Widerlegt";
$pgv_lang["link_as"]                        = "Diese Person mit bestehender Familie verknüpfen als ";
$pgv_lang["no_tab1"]                        = "Zu dieser Person gibt es keine Fakten.";
$pgv_lang["no_tab2"]                        = "Zu dieser Person gibt es keine Notizen.";
$pgv_lang["no_tab3"]                        = "Zu dieser Person gibt es keine Quellenangaben.";
$pgv_lang["no_tab4"]                        = "Zu dieser Person gibt es keine Multimedia-Objekte.";
$pgv_lang["no_tab5"]                        = "Zu dieser Person gibt es keine direkte Verwandschaft.";
$pgv_lang["no_tab6"]				= "Zu dieser Person gibt es keine Forschungs-Protokolle.";

//-- FAMILY FILE MESSAGES
$pgv_lang["family_info"]                = "Familien Informationen";
$pgv_lang["family_group_info"]                = "Informationen zur Familiengruppe";
$pgv_lang["husband"]                        = "Ehemann";
$pgv_lang["wife"]                        = "Ehefrau";
$pgv_lang["marriage"]                        = "Heirat:";
$pgv_lang["lds_sealing"]                = "HLT Siegelung:";
$pgv_lang["marriage_license"]                = "Ehe Erlaubnis:";
$pgv_lang["media_object"]                = "Multimedia Objekt";
$pgv_lang["children"]                        = "Kinder";
$pgv_lang["no_children"]                = "keine Kinder eingetragen";
$pgv_lang["parents_timeline"]                = "Ehepaar in<br />Lebensspannen-Ansicht zeigen";

//-- CLIPPINGS FILE MESSAGES
$pgv_lang["clip_cart"]                        = "Ausschnitts-Sammelbehälter";
$pgv_lang["clip_explaination"]                = "Der Stammbaum-Ausschnitts-Sammelbehälter ermöglicht es, &quot;Ausschnitte&quot; aus diesem Stammbaum zu markieren.<br />Diese können dann später in einer separaten GEDCOM-Datei zum sofortigen Download gebündelt werden.<br /><br />";
$pgv_lang["item_with_id"]                = "Ausschnitt von ID";
$pgv_lang["error_already"]                = "ist schon in Ihrem Ausschnitts-Sammelbehälter.";
$pgv_lang["which_links"]                = "Welche Verbindungen dieser Familie möchten Sie noch hinzuzufügen?";
$pgv_lang["just_family"]                = "Nur den Familiendatensatz dieser Familie hinzufügen.";
$pgv_lang["parents_and_family"]                = "Den Familiendatensatz und die Datensätze der Eltern dieser Familie hinzufügen.";
$pgv_lang["parents_and_child"]                = "Den Familiendatensatz, den der Eltern und die der Kinder dieser Familie hinzufügen.";
$pgv_lang["parents_desc"]                = "Den Familiendatensatz, die Datensätze der Eltern und die ihrer gesamten Nachkommen hinzufügen.";
$pgv_lang["continue"]                        = "weiter";
$pgv_lang["which_p_links"]                = "Welche Verbindungen dieser Person möchten Sie hinzufügen?";
$pgv_lang["just_person"]                = "Nur den Datensatz dieser Person hinzufügen.";
$pgv_lang["person_parents_sibs"]        = "Den Datensatz dieser Person, die seiner Eltern und seiner Geschwister hinzufügen.";
$pgv_lang["person_ancestors"]                = "Den Datensatz dieser Person und die seiner direkten Vorfahren hinzufügen.";
$pgv_lang["person_ancestor_fams"]        = "Den Datensatz dieser Person, die seiner direkten Vorfahren und die Datensätze derer Familien hinzufügen.";
$pgv_lang["person_spouse"]                = "Den Datensatz dieser Person, den seines Ehepartners und die seiner Kinder hinzufügen.";
$pgv_lang["person_desc"]                = "Den Datensatz dieser Person, den seines Ehepartners und die aller seiner Nachfahren hinzufügen.";
$pgv_lang["unable_to_open"]                = "Es ist nicht möglich, das Verzeichnis für den Ausschnitts-Sammelbehälter zum Schreiben zu öffnen.";
$pgv_lang["person_living"]                = "Diese Person ist am Leben. Persönliche Details werden nicht eingefügt.";
$pgv_lang["person_private"]                = "Details dieser Person sind vertraulich. Persönliche Details werden nicht eingefügt.";
$pgv_lang["family_private"]                = "Details dieser Familie sind vertraulich. Persönliche Details werden nicht eingefügt.";
$pgv_lang["download"]                        = "Klicken Sie mit der rechten Maustaste (beim Mac Control-Klick) auf den untenstehenden Link und wählen &quot;Ziel speichern unter&quot;, um die generierte GEDCOM-Datei auf Ihren eigenen Computer zu übertragen (Download).";
$pgv_lang["media_files"]                = "Multimedia-Dateien, welche zu den Datensätzen dieser GEDCOM-Datei gehören:";
$pgv_lang["cart_is_empty"]                = "Ihr Ausschnitts-Sammelbehälter ist leer.";
$pgv_lang["id"]                                = "Identifikationsnummer (ID)";
$pgv_lang["name_description"]                = "Name / Beschreibung";
$pgv_lang["remove"]                        = "Entfernen";
$pgv_lang["empty_cart"]                        = "Ausschnitts-Sammelbehälter leeren";
$pgv_lang["download_now"]                = "Jetzt downloaden";
$pgv_lang["indi_downloaded_from"]        = "Daten der Person wurden geladen von:";
$pgv_lang["family_downloaded_from"]        = "Daten der Familie wurden geladen von:";
$pgv_lang["source_downloaded_from"]        = "Daten der Quelle wurden geladen von:";

//-- PLACELIST FILE MESSAGES
$pgv_lang["connections"]                = "Orts-Verbindungen gefunden";
$pgv_lang["top_level"]                        = "zurück";
$pgv_lang["form"]                        = "Ortsangaben werden im folgenden Format ausgewertet:";
$pgv_lang["default_form"]                = "Stadt, Kreis, (Bundes)Land, Staat";
$pgv_lang["default_form_info"]                = "(Standardeinstellung)";
$pgv_lang["gedcom_form_info"]                = "(GEDCOM)";
$pgv_lang["unknown"]                        = "Unbekannt";
$pgv_lang["individuals"]                = "Personen";
$pgv_lang["view_records_in_place"]        = "Alle Datensätze für diesen Ort anzeigen";
$pgv_lang["place_list2"] 					   = "Ortsliste";
$pgv_lang["show_place_hierarchy"]		= "Orts-Hierarchie anzeigen";
$pgv_lang["show_place_list"]			= "Alle Orte in einer Liste anzeigen";
$pgv_lang["total_unic_places"]		= "Gesamtanzahl Orte";

//-- MEDIALIST FILE MESSAGES
$pgv_lang["multi_title"]                = "Liste der Multimedia Objekte";
$pgv_lang["media_found"]                = "Multimedia Objekt(e) gefunden";
$pgv_lang["view_person"]                = "Person zeigen";
$pgv_lang["view_family"]                = "Familie zeigen";
$pgv_lang["view_source"]                = "Quelle zeigen";
$pgv_lang["prev"]                        = "&lt; Vorhergehende Seite";
$pgv_lang["next"]                        = "Nächste Seite &gt;";
$pgv_lang["file_not_found"]                = "Datei nicht gefunden.";
$pgv_lang["medialist_show"]             = "Zeige";
$pgv_lang["per_page"]                   = "Objekte pro Seite";

//-- SEARCH FILE MESSAGES
$pgv_lang["search_gedcom"]                = "GEDCOM-Dateien durchsuchen";
$pgv_lang["enter_terms"]                = "Suchbegriffe eingeben";
$pgv_lang["soundex_search"]                = "Den Namen nach Aussprache suchen (Soundex-Methode):";
$pgv_lang["sources"]                        = "Quellen";
$pgv_lang["firstname_search"]                = "Vorname";
$pgv_lang["lastname_search"]                = "Nachname";
$pgv_lang["search_place"]                = "Ort";
$pgv_lang["search_year"]                = "Jahr";
$pgv_lang["no_results"]                        = "Keine Ergebnisse gefunden";
$pgv_lang["invalid_search_input"] 	= "Bitte geben Sie einen Vor-, Nachnamen oder einen Ort zusätzlich zur Jahresangabe an";
$pgv_lang["search_options"]			= "Such-Optionen";
$pgv_lang["search_geds"]			= "GEDCOMS in denen gesucht wird";
$pgv_lang["search_type"]			= "Such-Methode";
$pgv_lang["search_general"]			= "Normale Suche";
$pgv_lang["search_soundex"]			= "Soundex Suche";
$pgv_lang["search_inrecs"]			= "Suchen nach";
$pgv_lang["search_fams"]			= "Familien";
$pgv_lang["search_indis"]			= "Personen";
$pgv_lang["search_sources"]			= "Quellen";
$pgv_lang["search_more_chars"]      = "Bitte mehr als einen Buchstaben eingeben";
$pgv_lang["search_soundextype"]		= "Soundex Variante:";
$pgv_lang["search_russell"]			= "Russell";
$pgv_lang["search_DM"]				= "Daitch-Mokotoff";


//-- SOURCELIST FILE MESSAGES
$pgv_lang["sources_found"]                = "Quellen gefunden";
$pgv_lang["titles_found"]			= "Titel";
$pgv_lang["find_source"]			= "Quelle suchen";

//-- REPOLIST FILE MESSAGES
$pgv_lang["repo_list"]				= "Archiv Liste";
$pgv_lang["repos_found"]			= "gefundene Archive";
$pgv_lang["find_repository"]		= "Archive suchen";
$pgv_lang["total_repositories"]		= "Gesamtanzahl Archive";
$pgv_lang["repo_info"]				= "Archiv Informationen";
$pgv_lang["delete_repo"]			= "Archiv löschen";
$pgv_lang["other_repo_records"]		= "Datensätze, die auf dieses Archiv verweisen:";
$pgv_lang["create_repository"]		= "Archiv anlegen";
$pgv_lang["new_repo_created"]		= "neues Archiv erstellt";
$pgv_lang["paste_rid_into_field"]	= "Fügen Sie die folgende Archiv-ID ein, um auf dieses Archiv zu verweisen ";
$pgv_lang["confirm_delete_repo"]	= "Möchten Sie das Archiv wirklich aus der Datenbank löschen?";

//-- SOURCE FILE MESSAGES
$pgv_lang["source_info"]                = "Informationen zur Quelle";
$pgv_lang["other_records"]                = "Datensätze, die auf diese Quelle verweisen:";
$pgv_lang["people"]                        = "PERSONEN";
$pgv_lang["families"]                        = "FAMILIEN";
$pgv_lang["total_sources"]			= "Gesamtanzahl Quellen:";

//-- BUILDINDEX FILE MESSAGES
$pgv_lang["building_indi"]                        = "Erstelle Index-Dateien der Personen und Familien";
$pgv_lang["building_index"]                        = "Erstelle Liste der Index-Dateien";
$pgv_lang["invalid_gedformat"]        = "Format entspricht nicht dem GEDCOM 5.5 Standard";
$pgv_lang["importing_records"]                        = "Importiere Datensätze in Datenbank";
$pgv_lang["detected_change"]                        = "PhpGedView hat eine Änderung an der Datei <b>#GEDCOM#</b> erkannt. Daher müssen jetzt die Index-Dateien neu erstellt werden.";
$pgv_lang["please_be_patient"]                        = "BITTE UM ETWAS GEDULD";
$pgv_lang["reading_file"]                        = "GEDCOM-Datei wird gelesen";
$pgv_lang["flushing"]                                = "Speicherinhalt wird gelöscht";
$pgv_lang["found_record"]                        = "Datensatz gefunden";
$pgv_lang["exec_time"]                                = "Ausführungszeit:";
$pgv_lang["time_limit"]				= "Zeitbeschränkung:";
$pgv_lang["unable_to_create_index"]                = "Index-Datei kann nicht erstellt werden. Stellen Sie sicher, dass die Rechte zum Schreiben im PhpGedView-Verzeichnis gesetzt sind. Die Rechte können zurückgesetzt werden, sobald die Index-Dateien erstellt wurden.";
$pgv_lang["indi_complete"]                        = "Update der Personen-Index-Datei ist komplett.";
$pgv_lang["family_complete"]                        = "Update der Familien-Index-Datei ist komplett.";
$pgv_lang["source_complete"]                        = "Update der Quellen-Index-Datei ist komplett.";
$pgv_lang["tables_exist"]                        = "PhpGedView Tabellen sind schon in der Datenbank vorhanden";
$pgv_lang["you_may"]                                = "Sie können:";
$pgv_lang["drop_tables"]                        = "Löschen der aktuellen sql-Tabellen";
$pgv_lang["import_multiple"]                        = "Importieren und Weiterarbeiten mit mehreren GEDCOM-Dateien";
$pgv_lang["explain_options"]                        = "Wenn Sie beschliessen, die Tabellen zu löschen, werden <u>alle</u> Inhalte durch die dieser GEDCOM-Datei ersetzt.<br /><br />Wenn Sie sich für einen Import und die Weiterarbeit mit mehreren GEDCOM-Dateien entscheiden, wird PhpGedView alle Daten löschen, welche aus einer GEDCOM-Datei mit gleichem Namen importiert wurden. Diese Option erlaubt es Ihnen, mehrere GEDCOM-Dateien in den gleichen Tabellen zu speichern und sehr leicht zwischen diesen zu wechseln.<br /><br /><b>Bitte beachten Sie die Groß-/Kleinschreibung bei Dateinamen.</b> Denn <b>Test.GED</b> ist <u>nicht</u> das selbe wie <b>test.ged</b>.";
$pgv_lang["path_to_gedcom"]                        = "Geben Sie den Pfad zu Ihrer GEDCOM-Datei ein:";
$pgv_lang["gedcom_title"]                        = "Geben Sie einen Bezeichnung ein, welche die Daten in dieser GEDCOM-Datei beschreibt";
$pgv_lang["dataset_exists"]                        = "Eine GEDCOM-Datei mit diesem Dateinamen wurde bereits in die Datenbank importiert.";
$pgv_lang["changes_present"]		= "Die aktuelle GEDCOM-Datei enthält Änderungen die noch kontrolliert werden müssen. Wenn Sie mit dem Import fortfahren, werden diese Änderungen unmittelbar in die Datenbank eingefügt. Sie sollten die Änderungen kontrollieren, bevor Sie mit dem Importieren fortfahren.";
$pgv_lang["empty_dataset"]                        = "Möchten Sie den alten Datensatz löschen und durch diese neue Daten ersetzen?";
$pgv_lang["index_complete"]                        = "Index komplett.";
$pgv_lang["click_here_to_go_to_pedigree_tree"]        = "Hier klicken, um zum Stammbaum zu gelangen.";
$pgv_lang["updating_is_dead"]                        = "Aktualisiere den \"Lebt\"-Status von Person ";
$pgv_lang["import_complete"]                        = "Import komplett";
$pgv_lang["updating_family_names"]                = "Erneuere Familien Namen für \"FAM\"";
$pgv_lang["processed_for"]                        = "Datei bearbeitet";
$pgv_lang["run_tools"]                                = "Möchten Sie eines der folgenden Tools anwenden, bevor die GEDCOM-Datei importiert wird:";
$pgv_lang["addmedia"]                                = "Add Media Tool (fügt OBJE Medien hinzu)";
$pgv_lang["dateconvert"]                        = "Date Conversion Tool (wandelt das Datum um)";
$pgv_lang["xreftorin"]                                = "Convert XREF IDs to RIN number (wandelt XREF Nummern in RIN Nummern um)";
$pgv_lang["tools_readme"]                        = "Lesen Sie in der Datei #README.TXT# das Kapitel \"tools\" für weitere Informationen.";
$pgv_lang["sec"]                                = "s";
$pgv_lang["bytes_read"]                                = "Gelesene Bytes:";
$pgv_lang["created_indis"]                = "Die <i>Individuen</i>-Tabelle wurde erfolgreich erstellt.";
$pgv_lang["created_indis_fail"]        = "Die <i>Personen</i>-Tabelle kann nicht erstellt werden.";
$pgv_lang["created_fams"]                = "Die <i>Familien</i>-Tabelle wurde erfolgreich erstellt.";
$pgv_lang["created_fams_fail"]        = "Die <i>Familien</i>-Tabelle kann nicht erstellt werden.";
$pgv_lang["created_sources"]                = "Die <i>Quellen</i>-Tabelle wurde erfolgreich erstellt.";
$pgv_lang["created_sources_fail"]        = "Die <i>Quellen</i>-Tabelle kann nicht erstellt werden.";
$pgv_lang["created_other"]                = "Die <i>Sonstiges</i>-Tabelle wurde erfolgreich erstellt.";
$pgv_lang["created_other_fail"]        = "Die <i>Sonstiges</i>-Tabelle kann nicht erstellt werden.";
$pgv_lang["created_places"]                = "Die <i>Orts</i>-Tabelle wurde erfolgreich erstellt.";
$pgv_lang["created_places_fail"]        = "Die <i>Orts</i>-Tabelle kann nicht erstellt werden.";
$pgv_lang["import_progress"]        = "Import Fortschritt...";

//-- INDIVIDUAL AND FAMILYLIST FILE MESSAGES
$pgv_lang["total_fams"]                        = "Familien insgesamt";
$pgv_lang["total_indis"]                = "Personen insgesamt";
$pgv_lang["starts_with"]                = "Starte mit:";
$pgv_lang["person_list"]                = "Liste der Personen:";
$pgv_lang["paste_person"]                = "Person einfügen";
$pgv_lang["notes_sources_media"]        = "Notizen, Quellen und Multimedia-Dateien";
$pgv_lang["notes"]                        = "Notizen";
$pgv_lang["ssourcess"]                        = "Quellen";
$pgv_lang["media"]                        = "Multimedia";
$pgv_lang["name_contains"]                = "Name enthält:";
$pgv_lang["filter"]                        = "Filter";
$pgv_lang["find_individual"]                = "Person aussuchen";
$pgv_lang["find_familyid"]                = "Familie aussuchen";
$pgv_lang["find_sourceid"]                = "Quelle auswählen";
$pgv_lang["find_specialchar"]		= "Nach speziellen Zeichen suchen";
$pgv_lang["magnify"]				= "Vergrößern";
$pgv_lang["skip_surnames"]                = "Nachnamen Sprung-Liste";
$pgv_lang["show_surnames"]                = "Nachnamen anzeigen";
$pgv_lang["all"]                        = "ALLE";
$pgv_lang["hidden"]					= "Verborgen";
$pgv_lang["confidential"]			= "Vertraulich";

//-- TIMELINE FILE MESSAGES
$pgv_lang["age"]                        = "Alter";
$pgv_lang["timeline_title"]                = "PhpGedView - Anzeige der Lebensspanne";
$pgv_lang["timeline_chart"]                = "Lebenspannenanzeige";
$pgv_lang["remove_person"]                = "Person entfernen";
$pgv_lang["show_age"]                        = "Altersanker anzeigen";
$pgv_lang["add_another"]                = "Andere Person zur Ansicht hinzufügen:<br />Personen ID:";
$pgv_lang["find_id"]                        = "ID suchen";
$pgv_lang["show"]                        = "Zeigen";
$pgv_lang["year"]                        = "Jahr:";
$pgv_lang["timeline_instructions"]        = "In den meisten neueren Browsern, kann man die Rechtecke durch Klicken und gleichzeitiges Ziehen innerhalb der Ansicht bewegen.";
$pgv_lang["zoom_in"]                        = "Zoom +";
$pgv_lang["zoom_out"]                        = "Zoom -";

//-- MONTH NAMES
$pgv_lang["jan"]                        = "Januar";
$pgv_lang["feb"]                        = "Februar";
$pgv_lang["mar"]                        = "März";
$pgv_lang["apr"]                        = "April";
$pgv_lang["may"]                        = "Mai";
$pgv_lang["jun"]                        = "Juni";
$pgv_lang["jul"]                        = "Juli";
$pgv_lang["aug"]                        = "August";
$pgv_lang["sep"]                        = "September";
$pgv_lang["oct"]                        = "Oktober";
$pgv_lang["nov"]                        = "November";
$pgv_lang["dec"]                        = "Dezember";
$pgv_lang["abt"]                        = "um";
$pgv_lang["aft"]                        = "nach";
$pgv_lang["and"]                        = "und";
$pgv_lang["bef"]                        = "vor";
$pgv_lang["bet"]                        = "zwischen";
$pgv_lang["cal"]                        = "berechnet";
$pgv_lang["est"]                        = "angenommen";
$pgv_lang["from"]                        = "vom";
$pgv_lang["int"]                        = "interpretiert";
$pgv_lang["to"]                                = "bis";
$pgv_lang["cir"]                        = "ca.";
$pgv_lang["apx"]                        = "ungefähr";

//-- Admin File Messages
$pgv_lang["select_an_option"]                = "Wählen Sie bitte eine der unten aufgeführten Optionen:";
$pgv_lang["readme_documentation"]        = "README-Datei / Dokumentation";
$pgv_lang["view_readme"]			= "Datei readme.txt ansehen";
$pgv_lang["configuration"]                = "Konfiguration";
$pgv_lang["rebuild_indexes"]                = "Index-Dateien neu erstellen";
$pgv_lang["user_admin"]                        = "Benutzer-Verwaltung";
$pgv_lang["user_created"]                = "Benutzer wurde erfolgreich angelegt.";
$pgv_lang["user_create_error"]                = "Der Benutzer konnte nicht angelegt werden. Bitte nochmal versuchen.";
$pgv_lang["password_mismatch"]                = "Passwörter stimmen nicht überein.";
$pgv_lang["enter_username"]                = "Sie müssen einen Benutzernamen eingeben.";
$pgv_lang["enter_fullname"]                = "Sie müssen einen vollständigen Namen eingeben.";
$pgv_lang["enter_password"]                = "Sie müssen ein Passwort eingeben.";
$pgv_lang["confirm_password"]                = "Sie müssen das Passwort bestätigen.";
$pgv_lang["update_user"]                = "Benutzerdaten aktualisieren";
$pgv_lang["update_myaccount"]                = "Meine Benutzerdaten aktualisieren";
$pgv_lang["save"]                        = "Speichern";
$pgv_lang["delete"]                        = "Löschen";
$pgv_lang["edit"]                        = "Bearbeiten";
$pgv_lang["full_name"]                        = "Vollständiger Name";
$pgv_lang["visibleonline"]				= "Für andere Benutzer sichtbar, wenn online";
$pgv_lang["comment"]				= "Administrator Kommentar zum Benutzer";
$pgv_lang["comment_exp"]			= "Administrator Erinnerung am Datum";
$pgv_lang["editaccount"]				= "Benutzer darf seine Benutzerdaten ändern";
$pgv_lang["admin_gedcom"]				= "GEDCOM verwalten";
$pgv_lang["confirm_user_delete"]        = "Möchten Sie den Benutzer wirklich löschen";
$pgv_lang["create_user"]                = "Benutzer erstellen";
$pgv_lang["no_login"]                        = "Kann Benutzer nicht verifizieren.";
$pgv_lang["import_gedcom"]                = "Diese GEDCOM-Datei importieren";
$pgv_lang["duplicate_username"]         = "Doppelter Benutzername. Ein Benutzer mit dem gewählten Namen existiert bereits. Bitte wählen Sie einen anderen Benutzernamen.";
$pgv_lang["gedcomid"]                   = "Nummer des GEDCOM-Datensatzfeldes INDI dieser Person";
$pgv_lang["enter_gedcomid"]             = "Sie müssen eine GEDCOM-ID angeben.";
$pgv_lang["user_info"]                  = "Meine Benutzerdaten";
$pgv_lang["rootid"]                     = "Startperson für die Stammbaumdarstellung";
$pgv_lang["download_gedcom"]            = "GEDCOM-Datei von Ihrem Server herunterladen (download)";
$pgv_lang["upload_gedcom"]              = "GEDCOM-Datei auf Ihren Server laden (upload)";
$pgv_lang["add_new_gedcom"]                = "Eine neue GEDCOM-Datei erstellen";
$pgv_lang["gedcom_file"]                = "GEDCOM-Datei:";
$pgv_lang["enter_filename"]                = "Sie müssen einen GEDCOM-Dateinamen eingeben.";
$pgv_lang["file_not_exists"]        = "Eine Datei mit dem eingegebenen Namen existiert nicht.";
$pgv_lang["file_exists"]                = "Es gibt schon eine GEDCOM-Datei mit diesem Dateinamen. Bitte wählen Sie einen anderen Dateinamen oder löschen Sie die alte Datei.";
$pgv_lang["new_gedcom_title"]                = "Genealogische Daten aus [#GEDCOMFILE#]";
$pgv_lang["upload_error"]               = "Beim übertragen (upload) Ihrer Datei auf Ihren Server trat ein Fehler auf.";
$pgv_lang["upload_help"]                = "Wählen Sie Dateien auf Ihrem lokalen Computer aus, um diese auf Ihren Server zu übertragen (upload). Alle Dateien werden in folgendes Verzeichnis geladen:";
$pgv_lang["add_gedcom_instructions"]        = "Geben Sie einen Dateinamen für diese neue GEDCOM-Datei ein. Die neue GEDCOM-Datei wird im Index-Verzeichnis eingetragen: ";
$pgv_lang["file_success"]               = "Datei wurde erfolgreich auf Ihren Server übertragen (upload)";
$pgv_lang["file_too_big"]               = "Die zu übertragende Datei ist größer als erlaubt";
$pgv_lang["file_partial"]               = "Die Datei wurde nur teilweise hochgeladen. Bitte versuchen Sie es erneut.";
$pgv_lang["file_missing"]               = "Es kam keine Datei auf Ihrem Server an. Bitte erneut übertragen.";
$pgv_lang["manage_gedcoms"]             = "GEDCOM-Dateien und Datenschutz-Einstellungen verwalten";
$pgv_lang["research_log"]			= "Forschungs-Protokoll";
$pgv_lang["administration"]             = "Administration";
$pgv_lang["ansi_to_utf8"]                = "Soll diese ANSI-codierte GEDCOM-Datei in das UTF-8-Format konvertiert werden?";
$pgv_lang["utf8_to_ansi"]                = "Möchten Sie diese GEDCOM-Datei vom UTF-8 in das ANSI (ISO-8859-1)-Format konvertieren?";
$pgv_lang["user_manual"]                = "PhpGedView Benutzer Handbuch";
$pgv_lang["upgrade"]                        = "PhpGedView-Upgrade";
$pgv_lang["view_logs"]                        = "Log-Dateien einsehen";
$pgv_lang["logfile_content"]        = "Inhalt der Logdatei";
$pgv_lang["step1"]                        = "Schritt 1 von 4:";
$pgv_lang["step2"]                        = "Schritt 2 von 4:";
$pgv_lang["step3"]                        = "Schritt 3 von 4:";
$pgv_lang["step4"]                        = "Schritt 4 von 4:";
$pgv_lang["validate_gedcom"]                = "GEDCOM-Datei überprüfen";
$pgv_lang["img_admin_settings"]                = "Bild-Veränderungs-Konfiguration bearbeiten";
$pgv_lang["download_note"]                = "ANMERKUNG: Große GEDCOM-Dateien benötigen vor dem Download u.U. eine längere Berechnungszeit. Wenn ein PHP-Timeout auftritt bevor der Download fertig ist, ist die heruntergeladene Datei vermutlich nicht komplett.<br /><br />Prüfen Sie, ob die heruntergeladene Datei in der letzten Zeile <b>0&nbsp;TRLR</b> enthält, um den korrekten Download sicherzustellen. GEDCOM-Dateien sind Textdateien, die mit einem einfachen Texteditor geöffnet werden können, aber achten Sie darauf, die Datei nach dem Ansehen <u>nicht</u> zu speichern.<br /><br />Üblicherweise benötigt der Download etwa so lang wie der Import-Vorgang.";
$pgv_lang["pgv_registry"]                = "Andere PhpGedView-Seiten besuchen";
$pgv_lang["verify_upload_instructions"]	= "Wenn Sie fortfahren, wird die alte GEDCOM-Datei durch die neu hochgeladene Datei ersetzt und der Import-Prozess beginnt erneut. Wenn Sie abbrechen, bleibt die alte GEDCOM-Datei unverändert.";
$pgv_lang["cancel_upload"]				= "Upload abbrechen";
$pgv_lang["add_media_records"]		= "Multimedia-Datensätze hinzufügen";
$pgv_lang["manage_media_files"]		= "Multimedia-Dateien verwalten";
$pgv_lang["link_media_records"]		= "Multimedia-Dateien mit Personen verknüpfen";
$pgv_lang["add_media_button"]            = "Multimedia-Dateien hinzufügen";
$pgv_lang["phpinfo"]				= "PHP Informationen";
$pgv_lang["admin_info"]				= "Information";
$pgv_lang["admin_geds"]				= "Daten- und GEDCOM-Verwaltung";
$pgv_lang["admin_site"]				= "Webseiten-Verwaltung";

//-- Relationship chart messages
$pgv_lang["relationship_chart"]         = "Verwandtschaftsberechnung";
$pgv_lang["person1"]                    = "Person 1:";
$pgv_lang["person2"]                    = "Person 2:";
$pgv_lang["no_link_found"]              = "Keine (weitere) Verbindung zwischen den beiden Personen gefunden.";
$pgv_lang["sibling"]                    = "Geschwister";
$pgv_lang["follow_spouse"]              = "Überprüfe Verwandtschaft anhand der Ehen.";
$pgv_lang["timeout_error"]              = "Die maximal zulässige Ausführungszeit des Scriptes wurde überschritten, bevor ein Verwandtschaftsverhältnis gefunden werden konnte.";
$pgv_lang["son"]                        = "Sohn";
$pgv_lang["daughter"]                   = "Tochter";
$pgv_lang["brother"]                    = "Bruder";
$pgv_lang["sister"]                     = "Schwester";
$pgv_lang["relationship_to_me"]                = "Verwandtschaft mit mir";
$pgv_lang["rela_husb"]				= "Verwandtschaft mit dem Ehemann";
$pgv_lang["rela_wife"]				= "Verwandschaft mit der Ehefrau";
$pgv_lang["next_path"]                        = "Nächsten Pfad suchen";
$pgv_lang["show_path"]                        = "Pfad anzeigen";
$pgv_lang["line_up_generations"]        = "Personen gleicher Generation auf einer Höhe darstellen";
$pgv_lang["oldest_top"]             = "Älteste zuoberst";

//-- GEDCOM edit utility
$pgv_lang["check_delete"]                = "Möchten Sie dieses Ereignis wirklich löschen?";
$pgv_lang["access_denied"]                = "<b>Zugriff verweigert</b><br />Sie besitzen hierfür keine Erlaubnis.";
$pgv_lang["gedrec_deleted"]                = "GEDCOM-Datensatz erfolgreich gelöscht.";
$pgv_lang["gedcom_deleted"]                = "GEDCOM [#GED#] erfolgreich gelöscht.";
$pgv_lang["changes_exist"]                = "Die Änderungen an der GEDCOM-Datei wurden durchgeführt.";
$pgv_lang["accept_changes"]                = "Änderungen akzeptieren / verwerfen";
$pgv_lang["show_changes"]                = "Dieser Eintrag wurde geändert. Hier klicken, um die Änderungen zu sehen.";
$pgv_lang["hide_changes"]                = "Hier klicken, um die Änderungen zu verbergen.";
$pgv_lang["review_changes"]                = "GEDCOM-Änderung überprüfen";
$pgv_lang["undo_successful"]                = "erfolgreich gelöscht";
$pgv_lang["undo"]                        = "löschen";
$pgv_lang["view_change_diff"]                = "Änderungen ansehen";
$pgv_lang["changes_occurred"]                = "Bei dieser Person wurden folgende Änderungen vorgenommen:";
$pgv_lang["find_place"]                        = "Ort suchen";
$pgv_lang["close_window"]                = "Fenster schließen";
$pgv_lang["close_window_without_refresh"]        = "Fenster schließen ohne Neuladen";
$pgv_lang["place_contains"]             = "Ort enthält:";
$pgv_lang["accept_gedcom"]              = "Entscheiden Sie für jede Änderung, ob Sie sie akzeptieren oder löschen möchten.<br /><br />Um alle Änderungen auf einmal zu akzeptieren, klicken Sie <b>\"Alle Änderungen akzeptieren\"</b> in der Box darunter.<br />Weitere Informationen über eine Änderung erhalten Sie über den Link <b>\"Änderungen ansehen\"</b> oder klicken Sie <b>\"GEDCOM Datensatz ansehen\"</b>, um die veränderte GEDCOM-Datei anzusehen.";
$pgv_lang["ged_import"]                 = "Import";
$pgv_lang["now_import"]                 = "Sie sollten jetzt den Import der GEDCOM-Daten in PhpGedView starten, indem Sie auf den Import-Button klicken.";
$pgv_lang["add_fact"]                   = "neues Ereignis hinzufügen";
$pgv_lang["add"]                        = "hinzufügen";
$pgv_lang["custom_event"]               = "Benutzerdefiniertes Ereignis";
$pgv_lang["update_successful"]                = "Update erfolgreich";
$pgv_lang["add_child"]                  = "Kind hinzufügen";
$pgv_lang["add_child_to_family"]        = "Kind zu dieser Familie hinzufügen";
$pgv_lang["add_sibling"]                = "Einen Bruder oder eine Schwester hinzufügen";
$pgv_lang["add_son_daughter"]                = "Einen Sohn oder eine Tochter hinzufügen";
$pgv_lang["must_provide"]               = "Bitte eingeben:";
$pgv_lang["delete_person"]              = "Diese Person löschen";
$pgv_lang["confirm_delete_person"]      = "Möchten Sie diese Person wirklich aus der GEDCOM-Datei löschen?";
$pgv_lang["find_media"]                 = "Multimedia-Datei suchen";
$pgv_lang["set_link"]                   = "Link setzen";
$pgv_lang["add_source_lbl"]                = "Quelle hinzufügen";
$pgv_lang["add_source"]                 = "Eine neue Quelle hinzufügen";
$pgv_lang["add_note_lbl"]                = "Notiz hinzufügen";
$pgv_lang["add_note"]                   = "Eine neue Notiz hinzufügen";
$pgv_lang["add_media_lbl"]                = "Multimedia-Datei hinzufügen";
$pgv_lang["add_media"]                        = "Eine neue Multimedia-Datei hinzufügen";
$pgv_lang["delete_source"]              = "Diese Quelle löschen";
$pgv_lang["confirm_delete_source"]      = "Möchten Sie diese Quelle wirklich aus der GEDCOM-Datei löschen?";
$pgv_lang["add_husb"]                   = "Ehemann hinzufügen";
$pgv_lang["add_husb_to_family"]                = "Einen Ehemann zu dieser Familie hinzufügen";
$pgv_lang["add_wife"]                   = "Ehefrau hinzufügen";
$pgv_lang["add_wife_to_family"]                = "Eine Ehefrau zu dieser Familie hinzufügen";
$pgv_lang["find_family"]                = "Familie suchen";
$pgv_lang["find_fam_list"]                = "Familien-Liste suchen";
$pgv_lang["add_new_wife"]               = "Eine weitere Ehefrau hinzufügen";
$pgv_lang["add_new_husb"]               = "Einen weiteren Ehemann hinzufügen";
$pgv_lang["edit_name"]                        = "Namen editieren";
$pgv_lang["delete_name"]                = "Namen löschen";
$pgv_lang["no_temple"]                        = "Kein Tempel - Ordinanz zu Lebzeiten";
$pgv_lang["replace"]                        = "Datensatz ersetzen";
$pgv_lang["append"]                                = "Datensatz hinzufügen";
$pgv_lang["add_father"]                        = "Einen Vater hinzufügen";
$pgv_lang["add_mother"]                        = "Eine Mutter hinzufügen";
$pgv_lang["add_obje"]                        = "Ein neues Multimedia-Objekt hinzufügen";
$pgv_lang["no_changes"]                        = "Es liegen derzeit keine Änderungen zur Überprüfung vor.";
$pgv_lang["accept"]                                = "Akzeptieren";
$pgv_lang["accept_all"]                        = "Alle Änderungen akzeptieren";
$pgv_lang["accept_successful"]        = "Änderungen erfolgreich in die Datenbank übernommen";
$pgv_lang["edit_raw"]                        = "GEDCOM Rohdaten bearbeiten";
$pgv_lang["select_date"]                = "Datum auswählen";
$pgv_lang["create_source"]                = "Neue Quelle anlegen";
$pgv_lang["new_source_created"]        = "Neue Quelle erfolgreich erstellt.";
$pgv_lang["paste_id_into_field"]= "Fügen Sie die folgende Quellen-Identifikation in das jeweilige Eingabefeld ein, um auf diese Quelle zu verweisen: ";
$pgv_lang["add_name"]			= "Neuen Namen hinzufügen";
$pgv_lang["privacy_not_granted"]	= "Sie haben keinen Zugriff auf";
$pgv_lang["user_cannot_edit"]		= "Dieser Benutzername kann diese GEDCOM-Datei nicht editieren.";
$pgv_lang["gedcom_editing_disabled"]	= "Editieren dieser GEDCOM-Datei wurde vom Administrator deaktiviert.";
$pgv_lang["privacy_prevented_editing"]	= "Die Datenschutzeinstellungen verhindern, dass Sie diesen Datensatz bearbeiten können.";
$pgv_lang["add_asso"]				= "Einen neuen Partner hinzufügen";
$pgv_lang["edit_sex"]				= "Geschlecht ändern";

//-- calendar.php messages
$pgv_lang["on_this_day"]                = "An diesem Tag in Ihrer Chronik...";
$pgv_lang["in_this_month"]                = "In diesem Monat in Ihrer Chronik...";
$pgv_lang["in_this_year"]                = "In diesem Jahr in Ihrer Chronik...";
$pgv_lang["year_anniversary"]                = "#year_var#. Jahrestag";
$pgv_lang["today"]                        = "Heute";
$pgv_lang["day"]                        = "Tag:";
$pgv_lang["month"]                        = "Monat:";
$pgv_lang["showcal"]                        = "Anzeige der Ereignisse von:";
$pgv_lang["anniversary_calendar"]        = "Jahrestag Kalender";
$pgv_lang["sunday"]                        = "Sonntag";
$pgv_lang["monday"]                        = "Montag";
$pgv_lang["tuesday"]                        = "Dienstag";
$pgv_lang["wednesday"]                        = "Mittwoch";
$pgv_lang["thursday"]                        = "Donnerstag";
$pgv_lang["friday"]                        = "Freitag";
$pgv_lang["saturday"]                        = "Samstag";
$pgv_lang["viewday"]                        = "Tag anzeigen";
$pgv_lang["viewmonth"]                        = "Monat anzeigen";
$pgv_lang["viewyear"]                        = "Jahr anzeigen";
$pgv_lang["all_people"]                        = "Alle Personen";
$pgv_lang["living_only"]                = "Lebende Personen";
$pgv_lang["recent_events"]                = "Jüngere Ereignisse (&lt; 100 Jahre)";
$pgv_lang["day_not_set"]				= "Kein Tag angegeben";
$pgv_lang["year_error"]                        = "Daten vor 1970 werden leider nicht unterstützt.";

//-- upload media messages
$pgv_lang["upload"]					= "Upload";
$pgv_lang["upload_media"]                = "Multimedia-Dateien auf Server übertragen (upload)";
$pgv_lang["media_file"]                 = "Multimedia-Datei";
$pgv_lang["thumbnail"]                  = "Thumbnail";
$pgv_lang["upload_successful"]                = "Upload erfolgreich";

//-- user self registration module
//$pgv_lang["no_pw_or_account"]                = "Wenn Sie noch keinen Benutzerzugang haben, oder Ihr Passwort vergessen haben, klicken Sie einfach auf den <b>Login</b> Button";
$pgv_lang["lost_password"]                = "Haben Sie Ihr Passwort verloren?";
$pgv_lang["requestpassword"]            = "Passwort verloren! Neues beantragen";
$pgv_lang["no_account_yet"]                = "Haben Sie noch keine Benutzerdaten?";
$pgv_lang["requestaccount"]             = "Als neuer Benutzer anmelden";
$pgv_lang["register_info_01"]                = "Der Zugang zu privaten Daten auf dieser Webseite kann eingeschränkt sein wegen geltender Datenschutzbestimmungen. Viele Personen möchten nicht, dass ihre persönlichen Daten im Internet öffentlich zugänglich sind, da diese dann durch Identitäts-Diebstahl oder für Spam-Versandt missbraucht werden könnten.<br /><br />Um die privaten Daten zu sehen, müssen Sie einen Zugang zu dieser Webseite haben. Um einen Zugang zu bekommen, können Sie sich selbst registrieren, wobei Sie einige Fragen zu Ihrer Person beantworten müssen. Sobald der Administrator Ihre Angaben geprüft und die Registrierung aktiviert hat, können Sie sich auf der Weibseite einloggen und private Daten ansehen.<br /><br />Falls die Verwandschaftsüberprüfung eingeschaltet wurde, können Sie auch danach nur auf die privaten Daten ihrer eigenen näheren Verwandtschaft zugreifen. Der Administrator kann Ihnen auch Rechte zur Veränderung der vorhandenen Daten geben oder zum Eingeben neuer Daten.<br /><br />Bitte beachten Sie, dass Sie nur dann Zugriff auf die Daten erhalten, wenn Sie nachweisen können, dass Sie mit einer der Personen in der Datenbank verwandt sind. Falls dies nicht der Fall sein sollte, wäre es nutzlos, einen Antrag zu stellen, weil diesem wahrscheinlich nicht nachgegangen wird.<br /><br />Falls Sie weitere Unterstützung brauchen, klicken Sie bitte auf den unten stehenden Link und fragen Sie den Administrator per E-Mail um Rat.<br /><br />";
$pgv_lang["register_info_02"]                = "";
$pgv_lang["pls_note01"]                 = "Bitte beachten: Es wird zwischen Groß- und Kleinbuchstaben unterschieden!";
$pgv_lang["min6chars"]                  = "Das Passwort muss mindestens 6 Zeichen enthalten";
$pgv_lang["pls_note02"]                 = "Bitte beachten: Passwörter können aus Buchstaben und Zahlen und weiteren Zeichen bestehen.";
$pgv_lang["pls_note03"]                 = "Diese E-Mail Adresse wird vor Aktivierung Ihres Zuganges überprüft. Sie wird nicht auf der Webseite veröffentlicht. Sie werden eine Nachricht mit Ihren Registrierungsdaten an diese E-Mail Adresse erhalten.";
$pgv_lang["emailadress"]                = "E-Mail Adresse";
$pgv_lang["pls_note04"]                 = "Mit * markierte Felder sind Pflichtfelder.";
$pgv_lang["pls_note05"]                 = "Sie müssen eine gültige E-Mail-Adresse angeben, um die Bestätigungs-Mail zu erhalten.<br /><br />wenn Sie dieses Formular ausgefüllt haben und ihre Antwort überprüft wurde, werden Sie eine Bestätigungs-Mail an die hier angegebene Adresse erhalten. Mit der Bestätigungs-Mail können Sie Ihren Zugang aktivieren. Falls Sie den Zugang nicht binnen 7 Tagen aktivieren, wird er gelöscht (Sie müssen sich dann erst erneut registrieren).<br /><br />Um sich an dieser Seite einzuloggen benötigen Sie Ihren Benutzernamen und das Passwort.<br /><br />Falls bei der Registrierung Probleme auftreten, fragen Sie bitte den Webmaster um Hilfe.";

$pgv_lang["mail01_line01"]              = "Hallo #user_fullname# ...";
$pgv_lang["mail01_line02"]              = "Auf der Website ( #SERVER_NAME# ) wurde ein Benutzerantrag mit Ihrer E-Mail-Adresse ( #user_email# ) gestellt.";
$pgv_lang["mail01_line03"]              = "Informationen zu der Anfrage können Sie unter dem untenstehenden Link abrufen.";
$pgv_lang["mail01_line04"]              = "Bitte klicken Sie nun auf den folgenden Link und geben die dort geforderten Daten ein, um Ihre Anmeldung und Ihre E-Mail-Adresse zu bestätigen.";
$pgv_lang["mail01_line05"]              = "Falls Sie keinen Zugang beantragt haben, können Sie diese Mail einfach löschen.";
$pgv_lang["mail01_line06"]              = "Sie werden keine weitere Mail mehr erhalten, da ohne Verifizierung die angegebenen Daten nach einer Woche automatisch gelöscht werden.";
$pgv_lang["mail01_subject"]             = "Ihre Anmeldung bei #SERVER_NAME#";

$pgv_lang["mail02_line01"]              = "Hallo Administrator ...";
$pgv_lang["mail02_line02"]              = "Eine neuer Benutzer hat sich auf der Website ( #SERVER_NAME# ) angemeldet.";
$pgv_lang["mail02_line03"]              = "Der Benutzer hat eine E-Mail mit den zur Verifizierung notwendigen Informationen erhalten.";
$pgv_lang["mail02_line04"]              = "Sobald diese Verifikation erfolgt ist, werden Sie erneut benachrichtigt und aufgefordert, diese Person freizuschalten. Der Benutzer kann sich auf der Seite solange nicht anmelden, bis Sie seinen Zugang aktivieren.";
$pgv_lang["mail02_line04a"]			= "Sobald diese Verifikation erfolgt ist, werden Sie erneut benachrichtigt. Der Benutzer kann sich ab dem Zeitpunkt seiner Verifikation auf der Seite anmelden, ohne dass Sie weitere Schritte unternehmen müssen.";
$pgv_lang["mail02_subject"]             = "Neue Anmeldung bei #SERVER_NAME#";

$pgv_lang["hashcode"]                   = "Verifizierungscode:";
$pgv_lang["thankyou"]                   = "Hallo #user_fullname# ...<br />Danke für Ihre Anmeldung";
$pgv_lang["pls_note06"]                 = "Ihnen wird nun eine Bestätigungsmail an die Adresse ( #user_email# ) geschickt. Sie müssen den in der Mail enthaltenen Anweisungen folgen, um ihren Account zu aktivieren. Falls Sie innerhalb von 7 Tagen nicht reagieren, wird Ihre Anfrage automatisch abgewiesen. Sie müssen sich dann erneut anmelden.<br /><br />Wenn Sie den Anweisungen in der Bestätigungs-Mail gefolgt sind, muss der Administrator Ihren Account noch freischalten, bevor Sie ihn benutzen können.<br /><br />Um sich auf dieser Web-Seite anzumelden, benötigen Sie Ihren Benutzernamen und Ihr Passwort.<br /><br />";
$pgv_lang["pls_note06a"] 			= "Ihnen wird nun eine Bestätigungsmail an die Adresse ( #user_email# ) geschickt. Sie müssen den in der Mail enthaltenen Anweisungen folgen, um ihren Account zu aktivieren. Falls Sie innerhalb von 7 Tagen nicht reagieren, wird Ihre Anfrage automatisch abgewiesen. Sie müssen sich dann erneut anmelden.<br /><br />Wenn Sie den Anweisungen in der Bestätigungs-Mail gefolgt sind, können Sie sich sofort auf dieser Web-Seite anmelden. Dazu benötigen Sie Ihren Benutzernamen und Ihr Passwort.<br /><br />";

$pgv_lang["registernew"]                = "Verifizierung der neuen Benutzerdaten";
$pgv_lang["user_verify"]                = "Benutzer Verifizierung";
$pgv_lang["send"]                       = "Absenden";

$pgv_lang["pls_note07"]                 = "Bitte geben Sie nun Ihren Benutzernamen, Ihr Passwort und den Verifizierungscode ein, den Sie per E-Mail erhalten haben, um Ihre Anmeldung zu bestätigen.";
$pgv_lang["pls_note08"]                 = "Die Angaben für den Benutzer #user_name# wurden überprüft.";

$pgv_lang["mail03_line01"]              = "Hallo Administrator ...";
$pgv_lang["mail03_line02"]              = "#newuser[username]# ( #newuser[fullname]# ) hat die Registrierungsdaten verifiziert.";
$pgv_lang["mail03_line03"]              = "Bitte klicken Sie jetzt auf den unten stehenden Link und loggen sich ein. Sie müssen die Daten dieses Benutzers editieren, damit dieser sich einloggen kann.";
$pgv_lang["mail03_line03a"]			= "Der Benutzer kann sich jetzt anmelden; Sie müssen nichts weiter unternehmen.";
$pgv_lang["mail03_subject"]             = "Neue Verifizierung bei #SERVER_NAME#";

$pgv_lang["pls_note09"]                 = "Sie haben Ihren Antrag auf Benutzer-Registrierung bestätigt.";
$pgv_lang["pls_note10"]                 = "Der Administrator wurde benachrichtigt. Sobald dieser Ihren Account freigegeben hat, können Sie sich mit Ihrem Benutzernamen und Ihrem Passwort einloggen.";
$pgv_lang["pls_note10a"]			= "Sie können sich jetzt mit ihrem Benutzernamen und Passwort anmelden.";
$pgv_lang["data_incorrect"]             = "Die Daten waren falsch, bitte versuchen Sie es erneut.";
$pgv_lang["user_not_found"]                = "Die eingegebenen Informationen waren falsch. Bitte versuchen Sie es erneut.";

$pgv_lang["lost_pw_reset"]              = "Passwort neu anfordern";

$pgv_lang["pls_note11"]                 = "Um ein neues Passwort zu beantragen, geben Sie bitte Ihren Benutzernamen und die E-Mail-Adresse Ihres Zugangs ein.<br /><br />Wir werden Ihnen eine spezielle URL zumailen, die einen Bestätigungs-Code für Ihren Zugang enthält. Auf der Web-Seite dieser URL können Sie Ihr Passwort ändern und sich anmelden. Aus Sicherheitsgründen dürfen Sie diesen Bestätigungs-Code niemandem mitteilen, auch nicht dem Administrator dieser Seite (wir werden Sie nie danach fragen!).<br /><br />Falls Sie Hilfe benötigen, schreiben Sie bitte eine Anfrage an den Administrator.";
$pgv_lang["enter_email"]                = "Sie müssen eine E-Mail-Adresse angeben.";

$pgv_lang["mail04_line01"]              = "Hallo #user_fullname#...";
$pgv_lang["mail04_line02"]              = "Für ihren Benutzernamen wurde ein neues Passwort angefordert.";
$pgv_lang["mail04_line03"]              = "Empfehlung:";
$pgv_lang["mail04_line04"]              = "Bitte klicken Sie jetzt auf den unten stehenden Link oder kopieren Sie ihn in die Adresszeile Ihres Browsers, loggen sich mit dem neuen Passwort ein und ändern Sie es umgehend aus Datenschutzgründen.";
$pgv_lang["mail04_subject"]                = "Datenanforderung bei #SERVER_NAME#";

$pgv_lang["pwreqinfo"]                        = "Hallo...<br /><br />Das neue Passwort wurde an die von Ihnen angegebene E-Mail-Adresse (#user[email]#) versandt.<br /><br />Sie sollten die E-Mail bald in Ihrem Postfach finden.<br /><br />Hinweis:<br />Sie sollten sich baldmöglichst mit dem neuen Passwort anmelden und Ihr Passwort aus Datenschutzgründen gleich ändern.";

$pgv_lang["editowndata"]                = "Eigene Benutzerdaten bearbeiten";
$pgv_lang["savedata"]                   = "Geänderte Daten speichern";
$pgv_lang["datachanged"]                = "Benutzerdaten wurden geändert";
$pgv_lang["datachanged_name"]           = "Möglicherweise müssen Sie sich mit Ihrem neuen Benutzernamen neu anmelden.";
$pgv_lang["myuserdata"]                 = "Meine Benutzerdaten";
$pgv_lang["verified"]                   = "Benutzer hat sich selber verifiziert";
$pgv_lang["verified_by_admin"]          = "Benutzer wurde durch den Administrator freigegeben";
$pgv_lang["user_theme"]                 = "Mein Theme";
$pgv_lang["mgv"]                        = "MeinGedView";
$pgv_lang["mygedview"]                  = "Mein GedView Portal";
$pgv_lang["passwordlength"]             = "Das Passwort muss mindestens 6 Zeichen lang sein.";
$pgv_lang["admin_approved"]                = "Ihr Zugang für #SERVER_NAME# wurde freigegeben";
$pgv_lang["you_may_login"]                = " vom Administrator dieser Seite. Mit dem folgenden Link können Sie sich jetzt auf der PhpGedView Seite einloggen:";
$pgv_lang["welcome_text_auth_mode_1"]        =        "<center><b>Willkommen auf dieser Genealogie-Webseite</b></center><br /><br />Den Zugriff auf diese Seite erhält jeder Benutzer, der einen Zugang beantragt hat.<br /><br />Wenn Sie bereits einen Benutzerzugang haben, können Sie sich auf dieser Seite einloggen. Wenn Sie noch keinen Zugang besitzen, können Sie diesen beantragen, indem Sie auf den entsprechenden Link klicken.<br /><br />Sobald Ihre Angaben überprüft wurden, wird der Administrator Ihren Zugang freischalten. Sie werden dann eine Bestätigungsungsmail erhalten.";
$pgv_lang["welcome_text_auth_mode_2"]        =        "<center><b>Willkommen auf dieser Genealogie-Webseite</b></center><br /><br /><br />Der Zugriff auf diese Seite ist nur <u>autorisierten</u> Benutzern erlaubt.<br /><br />Wenn Sie bereits einen Benutzerzugang haben, können Sie sich auf dieser Seite einloggen. Wenn Sie noch keinen Zugang besitzen, können Sie diesen beantragen, indem Sie auf den entsprechenden Link klicken.<br /><br />Sobald Ihre Angaben überprüft wurden, wird der Administrator Ihrem Antrag zustimmen (oder ihn ablehnen). Sie werden eine E-Mail mit dem Antragsergebnis erhalten.";
$pgv_lang["welcome_text_auth_mode_3"]        =        "<center><b>Willkommen auf dieser Genealogie-Webseite</b></center><br /><br />Den Zugriff auf diese Seite erhalten <u>nur Familienmitglieder</u>.<br /><br />Wenn Sie bereits einen Benutzerzugang haben, können Sie sich auf dieser Seite einloggen. Wenn Sie noch keinen Zugang besitzen, können Sie diesen beantragen, indem Sie auf den entsprechenden Link klicken.<br /><br />Sobald Ihre Angaben überprüft wurden, wird der Administrator Ihrem Antrag zustimmen (oder ihn ablehnen).<br />Sie werden eine E-Mail mit dem Antragsergebnis erhalten.";
$pgv_lang["welcome_text_cust_head"]                =        "<center><b>Willkommen auf dieser Genealogie-Webseite</b></center><br /><br />Der Zugriff ist Benutzern vorbehalten, die einen Benutzernamen und ein Passwort für diese Webseite haben.<br />";

//-- mygedview page
$pgv_lang["welcome"]                    = "Willkommen";
$pgv_lang["upcoming_events"]                = "Bevorstehende Ereignisse";
$pgv_lang["living_or_all"]			= "Nur Ereignisse von lebenden Personen anzeigen?";
$pgv_lang["no_events_living"]		= "Für die nächsten #pgv_lang[global_num1]# Tage stehen keine Ereignisse für lebende Personen bevor.";
$pgv_lang["no_events_living1"]		= "Für morgen stehen keine Ereignisse für lebende Personen bevor.";
$pgv_lang["no_events_all"]			= "In den nächsten #pgv_lang[global_num1]# Tagen stehen keine Ereignisse bevor.";
$pgv_lang["no_events_all1"]			= "Für morgen stehen keine Ereignisse bevor.";
$pgv_lang["no_events_privacy"]		= "Für die nächsten #pgv_lang[global_num1]# Tage stehen zwar Ereignisse bevor aber aus Datenschutz-Gründen können diese nicht angezeigt werden.";
$pgv_lang["no_events_privacy1"]		= "Für morgen stehen zwar Ereignisse bevor aber aus Datenschutz-Gründen können diese nicht angezeigt werden.";
$pgv_lang["none_today_living"]		= "Am heutigen Tag gibt es keine Ereignisse für lebende Personen.";
$pgv_lang["none_today_all"]			= "Am heutigen Tag gibt es keine Ereignisse.";
$pgv_lang["none_today_privacy"]		= "Am heutigen Tag gibt es zwar Ereignisse aber aus Datenschutz-Gründen können diese nicht angezeigt werden.";
$pgv_lang["chat"]                       = "Chat";
$pgv_lang["users_logged_in"]                = "Angemeldete Benutzer";
$pgv_lang["anon_user"]				= "1 anonymer angemeldeter Benutzer";
$pgv_lang["anon_users"]				= "#pgv_lang[global_num1]# anonyme angemeldete Benutzer";
$pgv_lang["login_user"]				= "1 angemeldeter Benutzer";
$pgv_lang["login_users"]			= "#pgv_lang[global_num1]# angemeldete Benutzer";
$pgv_lang["no_login_users"]			= "Keine angemeldeten oder anonymen Benutzer";
$pgv_lang["message"]                        = "Nachricht senden";
$pgv_lang["my_messages"]                = "Meine Nachrichten";
$pgv_lang["date_created"]                = "gesendet:";
$pgv_lang["message_from"]                = "E-Mail Adresse:";
$pgv_lang["message_from_name"]                = "Ihr Name:";
$pgv_lang["message_to"]                        = "An:";
$pgv_lang["message_subject"]                = "Betreff:";
$pgv_lang["message_body"]                = "Text:";
$pgv_lang["no_to_user"]                 = "Bitte Empfänger angeben.";
$pgv_lang["provide_email"]              = "Bitte geben Sie die E-Mail-Adresse an, damit wir Ihre Nachricht beantworten können, andernfalls ist eine Antwort nicht möglich. Ihre E-Mail-Adresse wird ausschließlich zur Beantwortung Ihrer Anfrage genutzt.";
$pgv_lang["reply"]                      = "Antwort";
$pgv_lang["message_deleted"]                = "Nachricht gelöscht";
$pgv_lang["message_sent"]               = "Nachricht gesendet";
$pgv_lang["reset"]                      = "Reset";
$pgv_lang["site_default"]               = "Grundeinstellung";
$pgv_lang["mygedview_desc"]             = "Dieses Portal ermöglicht Ihnen das Anlegen von Lesezeichen zu bevorzugten Personen, Überwachen bevorstehender Ereignisse und die Zusammenarbeit mit anderen PhpGedView Benutzern.";
$pgv_lang["no_messages"]                = "Keine neuen Nachrichten.";
$pgv_lang["clicking_ok"]                = "Durch Klick auf OK, können Sie im sich dann öffnenden Fenster #user[fullname]# kontaktieren.";
$pgv_lang["my_favorites"]               = "Meine Lesezeichen";
$pgv_lang["no_favorites"]               = "Sie haben noch keine Lesezeichen gesetzt. Dies können Sie tun, indem Sie in der Detailansicht einer Person auf den <b>Lesezeichen hinzufügen</b>-Button klicken, oder indem Sie die folgende ID-Box benutzen.";
$pgv_lang["add_to_my_favorites"]        = "Lesezeichen hinzufügen";
$pgv_lang["gedcom_favorites"]                = "Stammbaum-Lesezeichen";
$pgv_lang["no_gedcom_favorites"]        = "Derzeit sind keine Lesezeichen angelegt. Der Administrator kann Lesezeichen einrichten, die Ihnen hier angezeigt werden.";
$pgv_lang["confirm_fav_remove"]                = "Möchten Sie dieses Lesezeichen wirklich löschen?";
$pgv_lang["invalid_email"]              = "Bitte geben Sie eine gültige E-Mail-Adresse ein.";
$pgv_lang["enter_subject"]              = "Bitte geben Sie einen Betreff ein.";
$pgv_lang["enter_body"]                 = "Bitte geben Sie vor dem Senden einen Text ein.";
$pgv_lang["confirm_message_delete"]        = "Möchten Sie diese Nachricht wirklich löschen? Sie kann anschließend nicht wiederhergestellt werden.";
$pgv_lang["message_email1"]                = "Die folgende Nachricht wurde an Ihr PhpGedView Benutzer-Postfach gesendet von ";
$pgv_lang["message_email2"]                = "Sie haben die folgende Nachricht an einen PhpGedView Benutzer gesendet:";
$pgv_lang["message_email3"]                = "Sie haben die folgende Nachricht an einen PhpGedView Administrator gesendet:";
$pgv_lang["viewing_url"]                = "Diese Nachricht wurde gesendet als die folgende Seite aufgerufen wurde: ";
$pgv_lang["messaging2_help"]                = "Wenn Sie diese Nachricht senden, erhalten Sie eine Kopie an die von Ihnen angegebene Adresse.";
$pgv_lang["random_picture"]                = "Zufällig ausgewähltes Bild";
$pgv_lang["message_instructions"]        = "<b>Bitte beachten:</b> Private Informationen von lebenden Personen werden nur Familienangehörigen und nahen Freunden zugänglich gemacht. Bevor Sie irgendwelche persönlichen Daten ansehen können, müssen Sie Ihren Verwandtschaftsgrad belegen. Es kann auch vorkommen, dass bestimmte Daten von bereits verstorbenen Personen privat sind. Dies kann der Fall sein, wenn nicht genügend Informationen vorhanden sind, um sicher zu belegen, ob die Person noch lebt oder verstorben ist.<br /><br />Bevor Sie Fragen stellen, überprüfen Sie bitte, dass Sie über die von Ihnen gesuchte Person fragen, indem Sie Orte, Zeitangaben und Verwandte prüfen. Falls Sie Änderungen der genealogischen Daten übermitteln, geben Sie bitte auch die Quelle an, von der Sie Ihre Informationen bezogen haben.<br /><br />";
$pgv_lang["sending_to"]                        = "Diese Nachricht wird an #TO_USER# gesendet";
$pgv_lang["preferred_lang"]                 = "Dieser Nutzer bevorzugt Nachrichten in #USERLANG#";
$pgv_lang["gedcom_created_using"]        = "Diese GEDCOM-Datei wurde erstellt mit <b>#SOFTWARE# #VERSION#</b>";
$pgv_lang["gedcom_created_on"]                = "Diese GEDCOM-Datei wurde am <b>#DATE#</b> erstellt";
$pgv_lang["gedcom_created_on2"]        = " am <b>#DATE#</b>";
$pgv_lang["gedcom_stats"]                = "GEDCOM-Statistiken";
$pgv_lang["stat_individuals"]                = "Personen, ";
$pgv_lang["stat_families"]                = "Familien, ";
$pgv_lang["stat_sources"]                = "Quellen, ";
$pgv_lang["stat_other"]                        = "Andere Datensätze";
$pgv_lang["customize_page"]                = "Mein GedView Portal anpassen";
$pgv_lang["customize_gedcom_page"]        = "Stammbaum Begrüßungs-Seite anpassen";
$pgv_lang["upcoming_events_block"]        = "Bevorstehende Ereignisse";
$pgv_lang["upcoming_events_descr"]        = "Der Block \"Bevorstehende Ereignisse\" zeigt eine Liste von Daten aus der Datenbank an, die sich in den kommenden 30 Tagen jähren.<br /><br />Auf der Mein GedView-Seite des Benutzers zeigt dieser Block nur lebende Personen, auf der Stammbaum-Begrüßungs-Seite zeigt er Daten aller Personen.";
$pgv_lang["todays_events_block"]        = "An diesem Tag";
$pgv_lang["todays_events_descr"]        = "Der Block \"An diesem Tag in Ihrer Chronik...\" zeigt eine Liste von Ereignissen in der Datenbank an, die sich heute jähren.<br /><br />Wenn keine Ereignisse stattfanden, wird der Block nicht angezeigt. Auf der Mein GedView-Seite des Benutzers zeigt dieser Block nur lebende Personen, auf der Stammbaum-Begrüßungs-Seite zeigt er Daten aller Personen.";
$pgv_lang["logged_in_users_block"]        = "Angemeldete Benutzer";
$pgv_lang["logged_in_users_descr"]        = "Der Block \"Angemeldete Benutzer\" zeigt eine Liste der Personen, die derzeit auf dieser Seite angemeldet sind.";
$pgv_lang["user_messages_block"]        = "Benutzer-Nachrichten";
$pgv_lang["user_messages_descr"]        = "Der Block \"Benutzer-Nachrichten\" zeigt eine Liste von Nachrichten an, die der aktuelle Benutzer erhalten hat.";
$pgv_lang["user_favorites_block"]        = "Benutzer Lesezeichen";
$pgv_lang["user_favorites_descr"]        = "Der \"Lesezeichen\"-Block zeigt dem Benutzer eine Liste der von ihm als wichtig angesehenen Personen in der Datenbank an, deren Daten er so schneller wieder auffinden kann.";
$pgv_lang["welcome_block"]                = "Willkommen";
$pgv_lang["welcome_descr"]                = "Der \"Benutzer Willkommen\"-Block zeigt den Benutzer, das aktuelle Datum und die Uhrzeit, Links, um die Benutzereinstellungen zu ändern und zu seiner eigenen Stammbaumansicht und einen Link zur Anpassung seiner Mein GedView-Seite.";
$pgv_lang["random_media_block"]                = "Zufälliges Bild";
$pgv_lang["random_media_descr"]                = "Der Block \"Zufälliges Bild\" wählt bei jedem Portalaufruf zufällig ein Bild aus der Datenbank aus und zeigt es an.";
$pgv_lang["gedcom_block"]                = "GEDCOM-Willkommen";
$pgv_lang["gedcom_descr"]                = "Der Block \"GEDCOM-Willkommen\" ist equivalent zum \"Benutzer Willkommen\"-Block indem er dem Benutzer den Titel der aktuellen Datei, sowie Datum und Uhrzeit anzeigt.";
$pgv_lang["gedcom_favorites_block"]        = "GEDCOM-Lesezeichen";
$pgv_lang["gedcom_favorites_descr"]        = "Der Block \"GEDCOM-Lesezeichen\" gibt dem Administrator die Möglichkeit, wichtige Personen der Datenbank als Lesezeichen einzutragen, so dass die Besucher sie einfach finden können.";
$pgv_lang["gedcom_stats_block"]                = "GEDCOM-Statistik";
$pgv_lang["gedcom_stats_descr"]                = "Der \"GEDCOM-Statistik\"-Block zeigt dem Besucher einige Basis-Informationen über die GEDCOM-Datei, z.B. wann sie erstellt wurde und wie viele Personen sie umfasst.<br /><br />Es kann auch eine Liste der am häufigsten vorkommenden Namen angezeigt werden. Zu dieser Liste können Namen hinzugefügt oder daraus Namen unterdrückt werden. Der Häufigkeitswert, ab wann ein Name in dieser Liste auftaucht, kann in der GEDCOM-Konfiguration eingestellt werden.";
$pgv_lang["gedcom_stats_show_surnames"]	= "Häufig vorkommende Nachnamen anzeigen?";
$pgv_lang["portal_config_intructions"]        = "Sie können die Seite Ihren Wünschen anpassen, indem Sie die Positionen der einzelnen Blöcke angeben.<br /><br />Die Seite ist die beiden Bereiche <b>Hauptbereich</b> und <b>Rechts</b> aufgeteilt. Die Blöcke im <b>Hauptbereich</b> erscheinen größer und unterhalb der Seiten-Überschrift. Der Bereich <b>Rechts</b> beginnt rechts von der Überschrift und setzt sich am rechten Rand der Seite nach unten fort.<br /><br />Jeder Bereich hat seine eigene Liste von Blöcken, die dort in der Reihenfolge ihrer Nennung angezeigt werden. Sie können Blöcke hinzufügen, entfernen oder umsortieren, wie es Ihnen beliebt.<br /><br />Wenn eine der beiden Listen leer ist, dann werden die Blöcke des anderen Bereiches auf der vollen Seitenbreite dargestellt.<br /><br />";
$pgv_lang["login_block"]                = "Login";
$pgv_lang["login_descr"]                = "Der \"Login\"-Block ermöglicht Benutzern das Anmelden auf dieser Seite.";
$pgv_lang["theme_select_block"]         = "Theme Auswahl";
$pgv_lang["theme_select_descr"]         = "Der Block \"Theme Auswahl\" zeigt die Theme-Auswahl-Liste an - auch dann, wenn der Wechsel des Themes eigentlich deaktiviert ist.";
$pgv_lang["block_top10_title"]          = "Häufigste Nachnamen";
$pgv_lang["block_top10"]                = "Häufigste Nachnamen";
$pgv_lang["block_top10_descr"]          = "Dieser Block zeigt eine Liste mit den 10 häufigsten Nachnamen der Datenbank. Die tatsächliche Länge der Liste ist konfigurierbar. Sie können auch bestimmte Namen in der Liste unterdrücken.";

$pgv_lang["gedcom_news_block"]                = "GEDCOM-Neuigkeiten";
$pgv_lang["gedcom_news_descr"]                = "Der \"GEDCOM-Neuigkeiten\"-Block zeigt dem Besucher neue Veröffentlichungen oder Artikel an, die der Administrator meldet.<br /><br />Dieser Bereich ist ein guter Platz für die Mitteilung über eine neue GEDCOM-Datei, zur Ankündigung eines Familientreffens oder die Bekanntgabe der Geburt eines neuen Familienmitgliedes.";
$pgv_lang["user_news_block"]                = "Benutzer-Journal";
$pgv_lang["user_news_descr"]                = "Der Block \"Benutzer-Journal\" ermöglicht dem Benutzer Notizen oder ein Journal online zu verwalten.";
$pgv_lang["my_journal"]                        = "Mein Journal";
$pgv_lang["no_journal"]                        = "Sie haben noch keine Journal-Einträge angelegt.";
$pgv_lang["confirm_journal_delete"]        = "Möchten Sie diesen Journal-Eintrag wirklich löschen?";
$pgv_lang["add_journal"]                = "Einen neuen Journal-Eintrag hinzufügen";
$pgv_lang["gedcom_news"]                = "Neuigkeiten";
$pgv_lang["confirm_news_delete"]        = "Möchten Sie diesen Neuigkeiten-Eintrag wirklich löschen?";
$pgv_lang["add_news"]                        = "Einen Artikel unter Neuigkeiten eintragen";
$pgv_lang["no_news"]                        = "Es sind keine Neuigkeiten eingetragen.";
$pgv_lang["edit_news"]                        = "Journal/Neuigkeiten-Einträge hinzufügen/bearbeiten";
$pgv_lang["enter_title"]                = "Bitte geben Sie einen Titel ein.";
$pgv_lang["enter_text"]                        = "Bitte geben Sie Text für diesen Journal/Neuigkeiten-Eintrag ein.";
$pgv_lang["news_saved"]                        = "Der Neuigkeiten/Journal-Eintrag wurde erfolgreich gespeichert.";
$pgv_lang["article_text"]                = "Text:";
$pgv_lang["main_section"]                = "Blöcke im Hauptbereich";
$pgv_lang["right_section"]                = "Blöcke im rechten Bereich";
$pgv_lang["available_blocks"]		= "Verfügbare Blöcke";
$pgv_lang["move_up"]                        = "nach oben";
$pgv_lang["move_down"]                        = "nach unten";
$pgv_lang["move_right"]                        = "nach rechts";
$pgv_lang["move_left"]                        = "nach links";
$pgv_lang["add_main_block"]                = "Einen Block zum Hauptbereich hinzufügen...";
$pgv_lang["add_right_block"]                = "Einen Block zum rechten Bereich hinzufügen...";
$pgv_lang["broadcast_all"]                = "An alle Benutzer senden";
$pgv_lang["hit_count"]                        = "Besucher-Zähler:";
$pgv_lang["phpgedview_message"]                = "PhpGedView Nachricht";
$pgv_lang["common_surnames"]                = "Häufigste Nachnamen";
$pgv_lang["default_news_title"]                = "Willkommen zur Ahnenforschung";
$pgv_lang["default_news_text"]                = "Die genealogischen Informationen dieser Webseite werden mit Hilfe von <a href=\"http://www.phpgedview.net/\" target=\"_blank\">PhpGedView #VERSION#</a> dargestellt.  Diese Seite hier gibt Ihnen einen Überblick und eine Einführung zu diesem Ahnenforschungs-Projekt.<br /><br />Um mit den Daten zu arbeiten, wählen Sie z.B. ein Diagramm aus dem Diagramm-Menü aus, öffnen Sie die Personen-Liste oder suchen Sie nach einem Namen oder Ort.<br /><br />Wenn Sie bei der Nutzung dieser Seite auf Schwierigkeiten stoßen, klicken Sie auf das Hilfe-Menü, um nähere Informationen zur jeweiligen Seite zu bekommen.<br /><br />Viel Erfolg bei der Ahnenforschung!";
$pgv_lang["reset_default_blocks"]        = "Reset zur Block Standard-Auswahl";
$pgv_lang["recent_changes"]                = "Aktuelle Änderungen";
$pgv_lang["recent_changes_block"]        = "Aktuelle Änderungen";
$pgv_lang["recent_changes_descr"]        = "Der \"Aktuelle Änderungen\"-Block listet alle Änderungen, die an der GEDCOM-Datei im letzten Monat vorgenommen wurden. Dieser Block kann Ihnen helfen, diese jüngsten Veränderungen zu verfolgen. Die Änderungen werden automatisch anhand des \"CHAN\"-tags erkannt.";
$pgv_lang["recent_changes_none"]	= "<b>Innerhalb der letzten #pgv_lang[global_num1]# Tage, gab es keine Änderungen.</b><br />";
$pgv_lang["recent_changes_some"]	= "<b>In den letzten #pgv_lang[global_num1]# Tagen durchgeführte Änderungen</b><br />";
$pgv_lang["show_empty_block"]		= "Soll der Block verborgen werden, wenn er leer ist?";
$pgv_lang["hide_block_warn"]		= "Wenn Sie einen leeren Block verbergen, können Sie seine Konfiguration erst dann wieder ändern, wenn er wieder sichtbar wird, weil er nicht mehr leer ist!";
$pgv_lang["delete_selected_messages"]        = "Ausgewählte Nachrichten löschen";
$pgv_lang["use_blocks_for_default"]		= "Diese Blöcke als Voreinstellung für alle Benutzer verwenden?";

//-- upgrade.php messages
$pgv_lang["upgrade_util"]                = "Upgrade Modul";
$pgv_lang["no_upgrade"]                        = "Es gibt keine Dateien für ein Upgrade.";
$pgv_lang["use_version"]                = "Sie benutzen die Version:";
$pgv_lang["current_version"]                = "Aktuelle stabile Version:";
$pgv_lang["upgrade_download"]                = "Herunterladen:";
$pgv_lang["upgrade_tar"]                = "TAR";
$pgv_lang["upgrade_zip"]                = "ZIP";
$pgv_lang["latest"]                        = "Sie benutzen die jüngste Version von PhpGedView.";
$pgv_lang["location"]                        = "Speicherort der Upgrade-Dateien: ";
$pgv_lang["include"]                        = "Einschließen:";
$pgv_lang["options"]                        = "Optionen:";
$pgv_lang["inc_phpgedview"]                = " PhpGedView";
$pgv_lang["inc_languages"]                = " Sprachen";
$pgv_lang["inc_config"]                        = " Konfigurations-Datei";
$pgv_lang["inc_researchlog"]                = " Forschungs-Protokoll";
$pgv_lang["inc_index"]                        = " Index Dateien";
$pgv_lang["inc_themes"]                        = " Themes";
$pgv_lang["inc_docs"]                        = " Handbücher";
$pgv_lang["inc_privacy"]                = " Datenschutz-Dateien";
$pgv_lang["inc_backup"]                        = "Sicherung erstellen";
$pgv_lang["upgrade_help"]                = " Hilfe anzeigen";
$pgv_lang["cannot_read"]                = "Folgende Datei kann nicht gelesen werden:";
$pgv_lang["not_configured"]                = "PhpGedView wurde noch nicht konfiguriert.";
$pgv_lang["location_upgrade"]                = "Bitte geben Sie den Speicherort Ihrer Upgrade-Dateien an.";
$pgv_lang["new_variable"]                = "Neue Variable gefunden: ";
$pgv_lang["config_open_error"]                = "Es gab einen Fehler beim Öffnen der Konfigurations-Datei.";
$pgv_lang["gedcom_config_write_error"]                = "Fehler!!! Die GEDCOM-Konfigurations-Datei kann nicht geschrieben werden.";
$pgv_lang["config_update_ok"]                = "Konfigurations-Datei erfolgreich aktualisiert.";
$pgv_lang["config_uptodate"]                = "Ihre Konfigurations-Datei ist aktuell.";
$pgv_lang["processing"]                        = "Arbeite...";
$pgv_lang["privacy_open_error"]                = "Fehler beim Öffnen der Datei [#PRIVACY_MODULE#].";
$pgv_lang["privacy_write_error"]        = "FEHLER!!! Die Datei [#PRIVACY_MODULE#] kann nicht geschrieben werden.<br />Stellen Sie sicher, dass die Schreibrechte für diese Datei gesetzt sind.<br />Diese Rechte können zurückgesetzt werden, sobald die Datenschutzdatei geschrieben wurde.";
$pgv_lang["privacy_update_ok"]                = "Die Datenschutzdatei [#PRIVACY_MODULE#] wurde erfolgreich aktualisiert.";
$pgv_lang["privacy_uptodate"]                = "Ihre Datei [#PRIVACY_MODULE#] ist aktuell.";
$pgv_lang["heading_privacy"]                = "Datenschutz-Dateien:";
$pgv_lang["heading_phpgedview"]                = "PhpGedView-Dateien:";
$pgv_lang["heading_image"]                = "Image-Dateien:";
$pgv_lang["heading_index"]                 = "Index-Dateien:";
$pgv_lang["heading_language"]                = "Sprach-Dateien:";
$pgv_lang["heading_theme"]                = "Theme-Dateien:";
$pgv_lang["heading_docs"]                = "Handbücher:";
$pgv_lang["heading_researchlog"]	= "Forschungs-Protokoll-Dateien:";
$pgv_lang["heading_researchloglang"]= "Forschungs-Protokoll-Sprachdateien:";
$pgv_lang["copied_success"]                = "erfolgreich kopiert.";
$pgv_lang["backup_copied_success"]        = "Sicherungsdatei wurde erfolgreich erstellt.";
$pgv_lang["folder_created"]                = "Ordner angelegt";
$pgv_lang["process_error"]                = "Es gibt ein Problem beim Laden der Seite. Es kann nicht ermittelt werden, ob eine neue Version vorliegt.";
$pgv_lang["upgrade_completed"]                = "Upgrade erfolgreich beendet";
$pgv_lang["start_using_upgrad"]                = "Klicken Sie hier zum Benutzen der Version";

//-- validate GEDCOM
$pgv_lang["performing_validation"]        = "GEDCOM-Überprüfung wird durchgeführt, bitte wählen Sie die gewünschten Optionen und klicken Sie auf <b>Korrigieren</b>";
$pgv_lang["changed_mac"]                = "Macintosh Zeilenumbrüche entdeckt. Wagenrücklauf-Zeichen CR (Ctrl M) wurden in Wagenrücklauf-Zeichen CR (Ctrl M) mit zusätzlichem Zeilenvorschub LF (Ctrl J) geändert. Dadurch wurde das interne Dateiformat von Macintosh- auf das für PhpGedView erforderliche DOS-Format umgestellt.";
$pgv_lang["changed_places"]                = "Ungültige Orts-Angaben entdeckt. Die Angaben wurden an die gültige GEDCOM 5.5 Spezifikation angepasst. Ein Beispiel aus Ihrer GEDCOM-Datei ist:";
$pgv_lang["invalid_dates"]                = "Ungültige Datums-Formate entdeckt. Diese werden bei der Korrektur in das übliche englische Format DD MMM YYYY (z.B. 1 JAN 2004) geändert.";
$pgv_lang["valid_gedcom"]                = "Gültige GEDCOM-Datei erkannt. Keine Korrektur notwendig.";
$pgv_lang["optional_tools"]                = "Sie können folgende optionale Tools vor dem Import anwenden.";
$pgv_lang["optional"]                        = "Optionale Tools";
$pgv_lang["date_format"]                = "Datums Format:";
$pgv_lang["day_before_month"]                = "Tag vor Monat (DD MM YYYY)";
$pgv_lang["month_before_day"]                = "Monat vor Tag (MM DD YYYY)";
$pgv_lang["do_not_change"]                = "Nicht ändern";
$pgv_lang["change_id"]                        = "Persönliche ID ändern in:";
$pgv_lang["example_date"]                = "Beispiel eines ungültigen Datums aus Ihrer GEDCOM-Datei:";
$pgv_lang["add_media_tool"]                = "Multimedia-Hinzufügen Tool";
$pgv_lang["launch_media_tool"]                = "Klicken Sie hier, um das Multimedia-Hinzufügen Tool zu nutzen.";
$pgv_lang["add_media_descr"]                = "Dieses Tool fügt Multimedia OBJE-tags zu Ihrer Datenbank hinzu. Schließen Sie dieses Fenster, wenn Sie keine weiteren Multimedia-Objekte mehr hinzufügen möchten.";
$pgv_lang["highlighted"]                = "Hervorgehobenes Bild";
$pgv_lang["extension"]                        = "Erweiterung";
$pgv_lang["order"]                        = "Reihenfolge";
$pgv_lang["inject_media_tool"]		= "Multimedia-Objekt zur GEDCOM-Datei hinzufügen";
$pgv_lang["media_table_created"]        = "Die <i>Multimedia</i>-Tabelle wurde erfolgreich erstellt.";
$pgv_lang["click_to_add_media"]                = "Klicken Sie hier, um die o.g. Multimedia-Dateien zur GEDCOM-Datei #GEDCOM# hinzuzufügen";
$pgv_lang["adds_completed"]                = "Multimedia-Objekt wurde erfolgreich zur GEDCOM-Datei hinzugefügt.";
$pgv_lang["ansi_encoding_detected"]        = "ANSI Codierung der Datei entdeckt. PhpGedView arbeitet nur optimal mit Dateien im UTF-8 Format.";
$pgv_lang["invalid_header"]                = "In der Datei wurden noch Zeilen vor dem GEDCOM-Header <b>0 HEAD</n> entdeckt. Beim Korrigieren werden diese Zeilen gelöscht.";
$pgv_lang["macfile_detected"]        = "Macintosh-Datei erkannt. Beim Korrigieren wird Ihre Datei ins DOS-Format konvertiert.";
$pgv_lang["place_cleanup_detected"]        = "Ungültige Orts-Angaben entdeckt. Diese Fehler sollten behoben werden. Hier ein Beispiel von einer ungültigen Ortsangabe, die entdeckt wurde:";
$pgv_lang["cleanup_places"]                = "Ortsangaben korrigieren";
$pgv_lang["empty_lines_detected"]        = "In der GEDCOM-Datei wurden Leerzeilen entdeckt. Beim Korrigieren werden diese Zeilen entfernt.";

//-- hourglass chart
$pgv_lang["hourglass_chart"]                = "Sanduhr-Diagramm";

//-- report engine
$pgv_lang["choose_report"]                = "Bericht auswählen";
$pgv_lang["enter_report_values"]        = "Bericht-Daten eingeben";
$pgv_lang["selected_report"]                = "Ausgewählter Bericht";
$pgv_lang["run_report"]                        = "Bericht erstellen";
$pgv_lang["select_report"]                = "Bericht auswählen";
$pgv_lang["download_report"]                        = "Bericht download";
$pgv_lang["reports"]                        = "Berichte";
$pgv_lang["pdf_reports"]                = "PDF Berichte";
$pgv_lang["html_reports"]                = "HTML Berichte";
$pgv_lang["family_group_report"]        = "Familienbericht";
$pgv_lang["page"]                                = "Seite";
$pgv_lang["of"]                                        = "von";
$pgv_lang["enter_famid"]                = "Familien ID eingeben";
$pgv_lang["show_sources"]                = "Quellen anzeigen?";
$pgv_lang["show_notes"]                        = "Notizen anzeigen?";
$pgv_lang["show_basic"]                        = "Grunddaten auch ausdrucken falls leer?";
$pgv_lang["show_photos"]				= "Fotos zeigen?";
$pgv_lang["individual_report"]			= "Personenbericht";
$pgv_lang["enter_pid"]				= "Personen-ID eingeben";
$pgv_lang["individual_list_report"]		= "Personen-Liste Bericht";
$pgv_lang["generated_by"]				= "erstellt von";
$pgv_lang["list_children"]				= "Alle Kinder nach Geburtsdatum geordnet anzeigen.";
$pgv_lang["birth_report"]				= "Geburtstags- und -orte-Bericht";
$pgv_lang["birthplace"]					= "Geburtsort enthält";
$pgv_lang["birthdate1"]					= "Geburtsdatumsbereich Anfang";
$pgv_lang["birthdate2"]					= "Geburtsdatumsbereich Ende";
$pgv_lang["sort_by"]				= "Sortieren nach";

$pgv_lang["cleanup"]                        = "Korrigieren";
$pgv_lang["skip_cleanup"]			= "Korrigieren überspringen";

//-- CONFIGURE (extra) messgaes for programs patriarch, slklist and statistics
$pgv_lang["dynasty_list"]                = "Übersicht der Familien";
$pgv_lang["make_slklist"]                = "EXCEL (SLK) Liste erstellen";
$pgv_lang["excel_list"] 			= "Ausgabe im Excel (SLK) Format für die folgenden Dateien:";
$pgv_lang["excel_tab"]				= "Blatt:";


$pgv_lang["excel_create"]                = " wird erstellt in Datei:";
$pgv_lang["patriarch_list"]                = "Liste der Spitzenahnen";
$pgv_lang["slk_list"]                        = "EXCEL SLK Liste";
$pgv_lang["statistics"]                        = "Statistik";

//-- Merge Records
$pgv_lang["merge_records"]                = "Datensätze zusammenführen";
$pgv_lang["merge_same"]                        = "Die Datensätze sind nicht vom gleichen Typ und können daher nicht zusammengeführt werden.";
$pgv_lang["merge_step1"]                = "Zusammenführen Schritt 1 von 3";
$pgv_lang["merge_step2"]                = "Zusammenführen Schritt 2 von 3";
$pgv_lang["merge_step3"]                = "Zusammenführen Schritt 3 von 3";
$pgv_lang["select_gedcom_records"]        = "Wählen Sie zwei GEDCOM-Datensätze zum Zusammenführen aus. Die Datensätze müssen vom gleichen Typ sein.";
$pgv_lang["merge_to"]                        = "Zusammenführen zu ID:";
$pgv_lang["merge_from"]                        = "Zusammenführen von ID:";
$pgv_lang["merge_facts_same"]        = "Die folgenden Fakten waren identisch in beiden Datensätzen und werden automatisch zusammengeführt.";
$pgv_lang["no_matches_found"]        = "Keine übereinstimmenden Fakten gefunden";
$pgv_lang["unmatching_facts"]        = "Die folgenden Fakten stimmen nicht überein. Wählen Sie aus, welche Sie übernehmen möchten.";
$pgv_lang["record"]                                = "Datensatz";
$pgv_lang["adding"]                                = "Füge hinzu";
$pgv_lang["updating_linked"]        = "Aktualisiere verknüpfte Datensätze";
$pgv_lang["merge_more"]                        = "Weitere Datensätze zusammenführen.";
$pgv_lang["same_ids"]                        = "Sie haben zweimal die selbe ID eingegeben. Das Zusammenführen ist nicht möglich.";

//-- ANCESTRY FILE MESSAGES
$pgv_lang["ancestry_chart"] 		        = "Ahnentafel";
$pgv_lang["gen_ancestry_chart"]         = "#PEDIGREE_GENERATIONS# Generationen Ahnentafel";
$pgv_lang["chart_style"]    	     	= "Diagramm-Typ";
$pgv_lang["ancestry_list"]     	    = "Vorfahren-Liste";
$pgv_lang["ancestry_booklet"]     	= "Vorfahren-Büchlein";
$pgv_lang["show_cousins"]			= "Cousins und Cousinen anzeigen";
// 1st generation
$pgv_lang["sosa_2"]               = "Vater";
$pgv_lang["sosa_3"]               = "Mutter";
// 2nd generation
$pgv_lang["sosa_4"]               = "Großvater";
$pgv_lang["sosa_5"]               = "Großmutter";
$pgv_lang["sosa_6"]               = "Großvater";
$pgv_lang["sosa_7"]               = "Großmutter";
// 3rd generation
$pgv_lang["sosa_8"]               = "Ur-Großvater";
$pgv_lang["sosa_9"]               = "Ur-Großmutter";
$pgv_lang["sosa_10"]               = "Ur-Großvater";
$pgv_lang["sosa_11"]               = "Ur-Großmutter";
$pgv_lang["sosa_12"]               = "Ur-Großvater";
$pgv_lang["sosa_13"]               = "Ur-Großmutter";
$pgv_lang["sosa_14"]               = "Ur-Großvater";
$pgv_lang["sosa_15"]               = "Ur-Großmutter";
// 4th generation
$pgv_lang["sosa_16"]               = "Ur-Ur-Großvater";
$pgv_lang["sosa_17"]               = "Ur-Ur-Großmutter";
$pgv_lang["sosa_18"]               = "Ur-Ur-Großvater";
$pgv_lang["sosa_19"]               = "Ur-Ur-Großmutter";
$pgv_lang["sosa_20"]               = "Ur-Ur-Großvater";
$pgv_lang["sosa_21"]               = "Ur-Ur-Großmutter";
$pgv_lang["sosa_22"]               = "Ur-Ur-Großvater";
$pgv_lang["sosa_23"]               = "Ur-Ur-Großmutter";
$pgv_lang["sosa_24"]               = "Ur-Ur-Großvater";
$pgv_lang["sosa_25"]               = "Ur-Ur-Großmutter";
$pgv_lang["sosa_26"]               = "Ur-Ur-Großvater";
$pgv_lang["sosa_27"]               = "Ur-Ur-Großmutter";
$pgv_lang["sosa_28"]               = "Ur-Ur-Großvater";
$pgv_lang["sosa_29"]               = "Ur-Ur-Großmutter";
$pgv_lang["sosa_30"]               = "Ur-Ur-Großvater";
$pgv_lang["sosa_31"]               = "Ur-Ur-Großmutter";
// 5th generation
$pgv_lang["sosa_32"]               = "Ur-Ur-Ur-Großvater";
$pgv_lang["sosa_33"]               = "Ur-Ur-Ur-Großmutter";
$pgv_lang["sosa_34"]               = "Ur-Ur-Ur-Großvater";
$pgv_lang["sosa_35"]               = "Ur-Ur-Ur-Großmutter";
$pgv_lang["sosa_36"]               = "Ur-Ur-Ur-Großvater";
$pgv_lang["sosa_37"]               = "Ur-Ur-Ur-Großmutter";
$pgv_lang["sosa_38"]               = "Ur-Ur-Ur-Großvater";
$pgv_lang["sosa_39"]               = "Ur-Ur-Ur-Großmutter";
$pgv_lang["sosa_40"]               = "Ur-Ur-Ur-Großvater";
$pgv_lang["sosa_41"]               = "Ur-Ur-Ur-Großmutter";
$pgv_lang["sosa_42"]               = "Ur-Ur-Ur-Großvater";
$pgv_lang["sosa_43"]               = "Ur-Ur-Ur-Großmutter";
$pgv_lang["sosa_44"]               = "Ur-Ur-Ur-Großvater";
$pgv_lang["sosa_45"]               = "Ur-Ur-Ur-Großmutter";
$pgv_lang["sosa_46"]               = "Ur-Ur-Ur-Großvater";
$pgv_lang["sosa_47"]               = "Ur-Ur-Ur-Großmutter";
$pgv_lang["sosa_48"]               = "Ur-Ur-Ur-Großvater";
$pgv_lang["sosa_49"]               = "Ur-Ur-Ur-Großmutter";
$pgv_lang["sosa_50"]               = "Ur-Ur-Ur-Großvater";
$pgv_lang["sosa_51"]               = "Ur-Ur-Ur-Großmutter";
$pgv_lang["sosa_52"]               = "Ur-Ur-Ur-Großvater";
$pgv_lang["sosa_53"]               = "Ur-Ur-Ur-Großmutter";
$pgv_lang["sosa_54"]               = "Ur-Ur-Ur-Großvater";
$pgv_lang["sosa_55"]               = "Ur-Ur-Ur-Großmutter";
$pgv_lang["sosa_56"]               = "Ur-Ur-Ur-Großvater";
$pgv_lang["sosa_57"]               = "Ur-Ur-Ur-Großmutter";
$pgv_lang["sosa_58"]               = "Ur-Ur-Ur-Großvater";
$pgv_lang["sosa_59"]               = "Ur-Ur-Ur-Großmutter";
$pgv_lang["sosa_60"]               = "Ur-Ur-Ur-Großvater";
$pgv_lang["sosa_61"]               = "Ur-Ur-Ur-Großmutter";
$pgv_lang["sosa_62"]               = "Ur-Ur-Ur-Großvater";
$pgv_lang["sosa_63"]               = "Ur-Ur-Ur-Großmutter";

//-- FAN CHART
$pgv_lang["fan_chart"]				= "Kreis-Diagramm";
$pgv_lang["gen_fan_chart"]    = "#PEDIGREE_GENERATIONS# Generationen Kreis-Diagramm";
$pgv_lang["fan_width"]				= "Breite";
$pgv_lang["gd_library"]       = "Falsche Konfiguration des PHP-Servers: GD Bibliothek 2.x für Grafik-Funktionen nicht vorhanden.";
$pgv_lang["gd_freetype"]			= "Falsche Konfiguration des PHP-Servers: FreeType Bibliothek für True-Type Schriftarten nicht vorhanden.";
$pgv_lang["gd_helplink"]			= "http://de3.php.net/gd";
$pgv_lang["fontfile_error"]   = "Schriftart-Datei auf PHP-Server nicht vorhanden";
$pgv_lang["fanchart_IE"]			= "Dieses Kreis-Diagramm kann von Ihrem Browser nicht direkt gedruckt werden. Bitte speichern Sie das Bild mit dem Kontextmenü (rechter Mausklick) und drucken Sie die Datei anschließend.";

//-- RSS Feed
$pgv_lang["rss_descr"]		= "Neuigkeiten und Links von der Seite #GEDCOM_TITLE#";
$pgv_lang["rss_logo_descr"]	= "erstellt mit PhpGedView";
$pgv_lang["rss_feeds"]				= "RSS Feeds";

//-- ASSOciates RELAtionship
// After any change in the following list, please check $assokeys in edit_interface.php
$pgv_lang["attendant"] = "Begleiter";
$pgv_lang["attending"] = "begleitend";
$pgv_lang["circumciser"] = "Beschneider";
$pgv_lang["civil_registrar"] = "ziviler Standesbeamter";
$pgv_lang["friend"] = "Freund";
$pgv_lang["godfather"] = "Taufpate";
$pgv_lang["godmother"] = "Taufpatin";
$pgv_lang["godparent"] = "Taufpaten";
$pgv_lang["informant"] = "Informant";
$pgv_lang["lodger"] = "Mitbewohner";
$pgv_lang["nurse"] = "Kindermädchen";
$pgv_lang["priest"] = "Pfarrer";
$pgv_lang["rabbi"] = "Rabbiner";
$pgv_lang["registry_officer"] = "Standesbeamter";
$pgv_lang["servant"] = "Diener";
$pgv_lang["twin"] = "Zwilling";
$pgv_lang["twin_brother"] = "Zwillingsbruder";
$pgv_lang["twin_sister"] = "Zwillingsschwester";
$pgv_lang["witness"] = "Zeuge";

//-- statistics utility
$pgv_lang["statutci"]                    = "Index kann nicht erstellt werden";
$pgv_lang["statnnames"]                = "Anzahl der Namen =";
$pgv_lang["statnfam"]                  = "Anzahl der Familien =";
$pgv_lang["statnmale"]                 = "Anzahl männliche Personen =";
$pgv_lang["statnfemale"]               = "Anzahl weibliche Personen =";
$pgv_lang["statvars"]                     = "Geben Sie bitte die folgenden Variablen zum Zeichnen ein";
$pgv_lang["statlxa"]                      = "entlang der X-Achse:";
$pgv_lang["statlya"]                      = "entlang der Y-Achse:";
$pgv_lang["statlza"]                      = "entlang der Z-Achse:";
$pgv_lang["stat_10_none"]		 = "keiner";
$pgv_lang["stat_11_mb"]                       = "Geburtsmonat";
$pgv_lang["stat_12_md"]                       = "Sterbemonat";
$pgv_lang["stat_13_mm"]                       = "Hochzeitsmonat";
$pgv_lang["stat_14_mb1"]		= "Geburtsmonat des ersten Kindes in Bezug";
$pgv_lang["stat_15_mm1"]		= "Monat der ersten Hochzeit";
$pgv_lang["stat_16_mmb"]		= "Anzahl Monate zwischen Hochzeit und Geburt des ersten Kindes.";
$pgv_lang["stat_17_arb"]			 = "Alter bezogen auf das Geburtsjahr.";
$pgv_lang["stat_18_ard"]			 = "Alter bezogen auf das Sterbejahr.";
$pgv_lang["stat_19_arm"]			 = "Alter im Jahr der Hochzeit.";
$pgv_lang["stat_20_arm1"]			 = "Alter im Jahr der ersten Hochzeit.";
$pgv_lang["stat_21_nok"]			 = "Anzahl der Kinder.";
$pgv_lang["stat_gmx"]			= " Bitte Bereichsgrenzen für Monat angeben";
$pgv_lang["stat_gax"]			= " Bitte Bereichsgrenzen für das Alter angeben";
$pgv_lang["stat_gnx"]			= " Bitte Bereichsgrenzen für Anzahl angeben";
$pgv_lang["stat_200_none"]			 = "alle (bzw. keine)";
$pgv_lang["stat_201_num"]			 = "Anzahl";
$pgv_lang["stat_202_perc"]			 = "Prozentzahlen";
$pgv_lang["stat_300_none"]		= "keiner";
$pgv_lang["stat_301_mf"]			 = "männlich/weiblich";
$pgv_lang["stat_302_cgp"]			 = "Zeiträume (Bitte Bereichsgrenzen für Zeiträume angeben)";
$pgv_lang["statmess1"]			 = "<b>Hier nur die Werte angeben, die sich gegebenenfalls auf die X-Achse oder die Z-Achse beziehen</b>";
$pgv_lang["statar_xgp"]                     = "Bereichsgrenzen für Zeiträume (X-Achse):";
$pgv_lang["statar_xgl"]			 = "Bereichsgrenzen für Alter (X-Achse):";
$pgv_lang["statar_xgm"]			 = "Bereichsgrenzen für Monate (x-Achse).";
$pgv_lang["statar_xga"]			 = "Bereichsgrenzen für Anzahl (X-Achse):";
$pgv_lang["statar_zgp"]                     = "Bereichsgrenzen für Zeiträume (Z-Achse):";
$pgv_lang["statreset"]			 = "Zurücksetzen";
$pgv_lang["statsubmit"]			 = "Grafik anzeigen";

//-- statisticsplot utility

$pgv_lang["stpl"]			 	= "...";
$pgv_lang["stplGDno"]			 = "Die \"Graphics Display Library\" ist in PHP4 nicht verfügbar. Bitte wenden Sie sich an Ihren System-Administrator.";
$pgv_lang["stpljpgraphno"]		= "Die \"JPgraph\"-Module befinden sich nicht im Ordner <i>phpgedview/jpgraph/</i>. Bitte laden Sie sich diese von http://www.aditus.nu/jpgraph/jpdownload.php herunter<br> <h3>Installieren Sie zuerst JPgraph ins Verzeichnis <i>phpgedview/jpgraph/</i></h3>.<br>";
$pgv_lang["stplinfo"]			 = "Zeichnungs-Informationen:";
$pgv_lang["stpltype"]			 = "Typ:";
$pgv_lang["stplnoim"]			 = "nicht verfügbar:";
$pgv_lang["stplnumof"]			= "Anzahl der Messwerte ";
$pgv_lang["stplborns"]                   = "Geburten";
$pgv_lang["stpldeath"]                   = "Todesfälle";
$pgv_lang["stplmarr"]                    = "Hochzeiten";
$pgv_lang["stplpmonth"]			= " je Monat.n";
$pgv_lang["stplagerel"]			= "Alter bezogen auf ";
$pgv_lang["stplyob"]                      = "Geburtsjahr";
$pgv_lang["stplyod"]			 = "Sterbejahr";
$pgv_lang["stplaomd"]			 = "Alter zum Zeitpunkt der Hochzeit:n";
$pgv_lang["stplcpm"]			 = "Kinder pro Hochzeit";
$pgv_lang["stplmf"]			 = " / männlich-weiblich";
$pgv_lang["stplipot"]                     = " / pro Zeitraum";
$pgv_lang["stplgzas"]			 = "Bereiche Z-Achse:";
$pgv_lang["stplmonth"]                    = "Monat";
$pgv_lang["stplnumbers"]		= "Anzahl für eine Familie";
$pgv_lang["stplage"]			 = "Alter";
$pgv_lang["stplperc"]			 = "Prozentzahl";

//-- alive in year
$pgv_lang["alive_in_year"]			= "Lebend im Jahr";
$pgv_lang["is_alive_in"]			= "Lebte noch #YEAR#";
$pgv_lang["alive"]					= "Lebt";
$pgv_lang["dead"]					= "Verstorben";
$pgv_lang["maybe"]					= "Möglicherweise ";

//-- find media
$pgv_lang["add_directory"]			= "Verzeichnis hinzufügen";
$pgv_lang["show_thumbnail"]			= "Thumbnails anzeigen";
$pgv_lang["image_size"]				= "Bildgröße -- ";
$pgv_lang["no_thumb_dir"]			= " Thumbnail-Verzeichnis existiert nicht und konnte auch nicht erstellt werden.";
$pgv_lang["manage_media"]			= "Multimedia-Objekte verwalten";
$pgv_lang["gen_thumb"]				= "Thumbnail erzeugen";
$pgv_lang["move_to"]				= "Verschieben nach -->";
$pgv_lang["security_no_create"]		= "Sicherheits-Hinweis: Die Datei <b><i>index.php</i></b> existiert nicht im Ordner ";
$pgv_lang["security_not_exist"]		= "Sicherheits-Hinweis: Die Datei <b><i>index.php</i></b> konnte nicht erstellt werden im Ordner ";
$pgv_lang["illegal_chars"]			= "Unzulässige Zeichen im Namen";

//-- link media
$pgv_lang["link_media"]			= "Multimedia-Objekt verknüpfen";
$pgv_lang["to_person"]			= "mit Person";
$pgv_lang["to_family"]			= "mit Familie";
$pgv_lang["to_source"]			= "mit Quelle";
$pgv_lang["media_id"]			= "Multimedia-ID";
$pgv_lang["invalid_id"]			= "Diese ID existiert nicht in der GEDCOM-Datei.";

//-- Help system
$pgv_lang["definitions"]			= "Definitionen";

//-- Index_edit
$pgv_lang["description"]			= "Beschreibung";
$pgv_lang["block_desc"]				= "Block Beschreibung";
$pgv_lang["click_here"]				= "Fortfahren";
$pgv_lang["click_here_help"]		= "~#pgv_lang[click_here]#~<br /><br />Klicken Sie diesen Button, um die zuvor gespeicherten Änderungen zu verwenden.";
$pgv_lang["block_summaries"]		= "~#pgv_lang[block_desc]#~<br /><br />Hier finden Sie eine kurze Beschreibung aller Blöcke, die Sie auf die #pgv_lang[welcome]#-Seite oder auf das #pgv_lang[mygedview]# stellen können.<br /><table border='1' align='center'><tr><td class='list_value'><b>#pgv_lang[name]#</b></td><td class='list_value'><b>#pgv_lang[description]#</b></td></tr>#pgv_lang[block_summary_table]#</table><br /><br />";
$pgv_lang["block_summary_table"]	= "&nbsp;";

if (file_exists($PGV_BASE_DIRECTORY . "languages/lang.de.extra.php")) require $PGV_BASE_DIRECTORY . "languages/lang.de.extra.php";
?>
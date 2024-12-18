<?php
/*=================================================
	charset=utf-8
	Project: phpGedView
   File:	facts.es.php
	Author: John Finlay
   Comments:	Defines an array of GEDCOM codes and the spanish name facts that they represent.
   Change Log:	8/5/02 - File Created
   2005.02.19 "PhpGedView" and "GEDCOM" made consistent across all language files  G.Kroll (canajun2eh)
===================================================*/
# $Id: facts.es.php,v 1.1 2005/10/07 18:08:36 skenow Exp $
if (preg_match("/facts\...\.php$/", $_SERVER["PHP_SELF"])>0) {
	print "You cannot access a language file directly.";
	exit;
}
// -- Define a fact array to map GEDCOM tags with their argentinian spanish values
$factarray["ABBR"] = "Abreviatura";
$factarray["ADDR"] = "Dirección";
$factarray["ADR1"] = "Dirección 1";
$factarray["ADR2"] = "Dirección 2";
$factarray["ADOP"] = "Adopción";
$factarray["AFN"]  = "(AFN)";
$factarray["AGE"]  = "Edad";
$factarray["AGNC"] = "Agencia";
$factarray["ALIA"] = "Alias";
$factarray["ANCE"] = "Antepasados";
$factarray["ANCI"] = "Antepasados de Interés";
$factarray["ANUL"] = "Anulación";
$factarray["ASSO"] = "Asociados";
$factarray["AUTH"] = "Autor";
$factarray["BAPL"] = "Bautismo SUD";
$factarray["BAPM"] = "Bautismo";
$factarray["BARM"] = "Bar Mitzvah";
$factarray["BASM"] = "Bas Mitzvah";
$factarray["BIRT"] = "Nacimiento";
$factarray["BLES"] = "Bendición";
$factarray["BLOB"] = "Objeto de Datos Binarios";
$factarray["BURI"] = "Entierro";
$factarray["CALN"] = "Referencia";
$factarray["CAST"] = "Estatus Social";
$factarray["CAUS"] = "Causa de la muerte";
$factarray["CENS"] = "Censo";
$factarray["CHAN"] = "último cambio";
$factarray["CHAR"] = "Juego de Caracteres";
$factarray["CHIL"] = "Hijo";
$factarray["CHR"]  = "Bautismo";
$factarray["CHRA"] = "Bautismo en edad adulta";
$factarray["CITY"] = "Ciudad";
$factarray["CONF"] = "Confirmación";
$factarray["CONL"] = "Confirmación SUD";
$factarray["COPR"] = "Copyright";
$factarray["CORP"] = "Corporación / Compañía";
$factarray["CREM"] = "Cremación";
$factarray["CTRY"] = "País";
$factarray["DATA"] = "Datos";
$factarray["DATE"] = "Fecha";
$factarray["DEAT"] = "Defunción";
$factarray["DESC"] = "Descendientes";
$factarray["DESI"] = "Descendientes de Interés";
$factarray["DEST"] = "Destino";
$factarray["DIV"]  = "Divorcio";
$factarray["DIVF"] = "Divorcio Archivado";
$factarray["DSCR"] = "Descripción";
$factarray["EDUC"] = "Educación";
$factarray["EMIG"] = "Emigración";
$factarray["ENDL"] = "Investidura SUD";
$factarray["ENGA"] = "Compromiso matrimonio";
$factarray["EVEN"] = "Evento";
$factarray["FAM"]  = "Familia";
$factarray["FAMC"] = "Familia como hijo";
$factarray["FAMF"] = "Fichero Familia";
$factarray["FAMS"] = "Familia como cónyuge";
$factarray["FCOM"] = "Primera Communión";
$factarray["FILE"] = "Fichero Externo";
$factarray["FORM"] = "Formato:";
$factarray["GIVN"] = "Habitualmente nombrado";
$factarray["GRAD"] = "Graduación";
$factarray["IDNO"] = "Numero de Identificación";
$factarray["IMMI"] = "Immigración";
$factarray["LEGA"] = "Herencia";
$factarray["MARB"] = "Amonestaciones";
$factarray["MARC"] = "Contrato Matrimonial";
$factarray["MARL"] = "Licencia Matrimonial";
$factarray["MARR"] = "Matrimonio";
$factarray["MARS"] = "Dote";
$factarray["NAME"] = "Nombre";
$factarray["NATI"] = "Nacionalidad";
$factarray["NATU"] = "Natural";
$factarray["NCHI"] = "Número de Hijos";
$factarray["NICK"] = "Apodo";
$factarray["NMR"]  = "Número de matrimonios";
$factarray["NOTE"] = "Nota";
$factarray["NPFX"] = "Prefijo";
$factarray["NSFX"] = "Sufijo";
$factarray["OBJE"] = "Objeto Multimedia";
$factarray["OCCU"] = "Ocupación";
$factarray["ORDI"] = "Ordenanza";
$factarray["ORDN"] = "Ordenación";
$factarray["PAGE"] = "Detalles";
$factarray["PEDI"] = "Antepasados";
$factarray["PLAC"] = "Lugar";
$factarray["PHON"] = "Telef.";
$factarray["POST"] = "Código Postal";
$factarray["PROB"] = "Certificado Testamento";
$factarray["PROP"] = "Propiedad";
$factarray["PUBL"] = "Publicación";
$factarray["QUAY"] = "Calidad de los datos";
$factarray["REPO"] = "Archivo";
$factarray["REFN"] = "Número Ref";
$factarray["RELI"] = "Religión";
$factarray["RESI"] = "Residencia";
$factarray["RESN"] = "Restricción";
$factarray["RETI"] = "Jubilación";
$factarray["RFN"]  = "Número de archivo del registro";
$factarray["RIN"]  = "Número ID";
$factarray["ROLE"] = "Rol";
$factarray["SEX"]  = "Sexo";
$factarray["SLGC"] = "Sellam. SUD hijo";
$factarray["SLGS"] = "Sellam. SUD cónyuge";
$factarray["SOUR"] = "Fuente";
$factarray["SPFX"] = "Prefijo del Apellido";
$factarray["SSN"]  = "Número Seguridad Social";
$factarray["STAE"] = "Estado";
$factarray["STAT"] = "Estatus";
$factarray["SUBM"] = "Remitente";
$factarray["SUBN"] = "Envío";
$factarray["SURN"] = "Apellido";
$factarray["TEMP"] = "Templo";
$factarray["TEXT"] = "Texto";
$factarray["TIME"] = "Tiempo";
$factarray["TITL"] = "Título";
$factarray["WILL"] = "Testamento";
$factarray["TYPE"] = "Tipo";
$factarray["_EMAIL"] = "Email";
$factarray["EMAIL"] = "Correo electrónico";
$factarray["_TODO"]  = "Hacer Item";
$factarray["_UID"]   = "Identificador Universal";
// These facts are specific to GEDCOM exports from Family Tree Maker
$factarray["_MDCL"] = "Médico";
$factarray["_DEG"]  = "Grado";
$factarray["_MILT"] = "Servicio Militar";
$factarray["_SEPR"] = "Separado";
$factarray["_DETS"] = "Fallecimiento de un cónyuge";
$factarray["CITN"]  = "Ciudadanía";
$factarray["_FA1"]  = "Acontecimiento 1";
$factarray["_FA2"]  = "Acontecimiento 2";
$factarray["_FA3"]  = "Acontecimiento 3";
$factarray["_FA4"]  = "Acontecimiento 4";
$factarray["_FA5"]  = "Acontecimiento 5";
$factarray["_FA6"]  = "Acontecimiento 6";
$factarray["_FA7"]  = "Acontecimiento 7";
$factarray["_FA8"]  = "Acontecimiento 8";
$factarray["_FA9"]  = "Acontecimiento 9";
$factarray["_FA10"] = "Acontecimiento 10";
$factarray["_FA11"] = "Acontecimiento 11";
$factarray["_FA12"] = "Acontecimiento 12";
$factarray["_FA13"] = "Acontecimiento 13";
$factarray["_MREL"] = "Relacción con la Madre";
$factarray["_FREL"] = "Relacción con el Padre";
$factarray["_FA1"]  = "Matrimonio";
$factarray["_MSTAT"]= "Comienzo del matrimonio";
$factarray["_MEND"] = "Final del matrimonio";

// Other common customized facts
$factarray["_ADPF"] = "Adoptado por el padre";
$factarray["_ADPM"] = "Adoptado por la madre";
$factarray["_AKAN"] = "También conocido como";
$factarray["_AKA"] 	= "También conocido como";
$factarray["_BRTM"] = "Brit mila";
$factarray["_COML"] = "Derecho matrimonial";
$factarray["_EYEC"] = "Color de ojos";
$factarray["_FNRL"] = "Funeral";
$factarray["_HAIR"] = "Color de pelo";
$factarray["_HEIG"] = "Altura";
$factarray["_INTE"] = "Entierro";
$factarray["_MARI"] = "Proposición de matrimonio";
$factarray["_MBON"] = "Lazo matrimonial";
$factarray["_MEDC"] = "Estado médico";
$factarray["_MILI"] = "Militar";
$factarray["_NMR"]  = "Soltero";
$factarray["_NLIV"] = "Fallecido";
$factarray["_NMAR"] = "Nunca contrajo matrimonio";
$factarray["_PRMN"] = "Número fijo";
$factarray["_WEIG"] = "Peso";
$factarray["_YART"] = "Yartzeit";

if (file_exists($PGV_BASE_DIRECTORY . "languages/facts.es.extra.php")) require $PGV_BASE_DIRECTORY . "languages/facts.es.extra.php";

?>

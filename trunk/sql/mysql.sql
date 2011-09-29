CREATE TABLE pgv_blocks (
  b_id int(11) NOT NULL default '0',
  b_username varchar(100) default NULL,
  b_location varchar(30) default NULL,
  b_order int(11) default NULL,
  b_name varchar(255) default NULL,
  b_config text,
  PRIMARY KEY  (b_id)
) TYPE=MyISAM;

CREATE TABLE pgv_dates (
  d_day int(11) UNSIGNED default NULL,
  d_month varchar(5) default NULL,
  d_year int(11) UNSIGNED default NULL,
  d_fact varchar(10) default NULL,
  d_gid varchar(30) default NULL,
  d_file varchar(255) default NULL,
  KEY date_day (d_day),
  KEY date_month (d_month),
  KEY date_year (d_year),
  KEY date_fact (d_fact),
  KEY date_gid (d_gid),
  KEY date_file (d_file)
) TYPE=MyISAM;

CREATE TABLE pgv_families (
  f_id varchar(30) default NULL,
  f_file varchar(255) default NULL,
  f_husb varchar(30) default NULL,
  f_wife varchar(30) default NULL,
  f_chil varchar(255) default NULL,
  f_gedcom text,
  KEY fam_id (f_id),
  KEY fam_file (f_file)
) TYPE=MyISAM;


CREATE TABLE pgv_favorites (
  fv_id int(11) NOT NULL default '0',
  fv_username varchar(30) default NULL,
  fv_gid varchar(10) default NULL,
  fv_type varchar(10) default NULL,
  fv_file varchar(100) default NULL,
  PRIMARY KEY  (fv_id)
) TYPE=MyISAM;


CREATE TABLE pgv_individuals (
  i_id varchar(30) default NULL,
  i_file varchar(255) default NULL,
  i_rin varchar(30) default NULL,
  i_name varchar(255) default NULL,
  i_isdead int(1) default '1',
  i_gedcom text,
  i_letter varchar(5) default NULL,
  i_surname varchar(100) default NULL,
  KEY indi_id (i_id),
  KEY indi_name (i_name),
  KEY indi_letter (i_letter),
  KEY indi_file (i_file),
  KEY indi_surn (i_surname)
) TYPE=MyISAM;

CREATE TABLE pgv_messages (
  m_id int(11) NOT NULL default '0',
  m_from varchar(255) default NULL,
  m_to varchar(30) default NULL,
  m_subject varchar(255) default NULL,
  m_body text,
  m_created varchar(255) default NULL,
  PRIMARY KEY  (m_id)
) TYPE=MyISAM;

CREATE TABLE pgv_names (
  n_gid varchar(30) default NULL,
  n_file varchar(255) default NULL,
  n_name varchar(255) default NULL,
  n_letter varchar(5) default NULL,
  n_surname varchar(100) default NULL,
  n_type varchar(10) default NULL,
  KEY name_gid (n_gid),
  KEY name_name (n_name),
  KEY name_letter (n_letter),
  KEY name_type (n_type),
  KEY name_surn (n_surname)
) TYPE=MyISAM;

CREATE TABLE pgv_news (
  n_id int(11) NOT NULL default '0',
  n_username varchar(100) default NULL,
  n_date int(11) default NULL,
  n_title varchar(255) default NULL,
  n_text text,
  PRIMARY KEY  (n_id)
) TYPE=MyISAM;

CREATE TABLE pgv_other (
  o_id varchar(30) default NULL,
  o_file varchar(255) default NULL,
  o_type varchar(20) default NULL,
  o_gedcom text,
  KEY other_id (o_id),
  KEY other_file (o_file)
) TYPE=MyISAM;


CREATE TABLE pgv_placelinks (
  pl_p_id int(11) default NULL,
  pl_gid varchar(30) default NULL,
  pl_file varchar(255) default NULL,
  KEY plindex_place (pl_p_id),
  KEY plindex_gid (pl_gid),
  KEY plindex_file (pl_file)
) TYPE=MyISAM;


CREATE TABLE pgv_places (
  p_id int(11) NOT NULL default '0',
  p_place varchar(150) default NULL,
  p_level int(11) default NULL,
  p_parent_id int(11) default NULL,
  p_file varchar(255) default NULL,
  PRIMARY KEY  (p_id),
  KEY place_place (p_place),
  KEY place_level (p_level),
  KEY place_parent (p_parent_id),
  KEY place_file (p_file)
) TYPE=MyISAM;


CREATE TABLE pgv_sources (
  s_id varchar(30) default NULL,
  s_file varchar(255) default NULL,
  s_name varchar(255) default NULL,
  s_gedcom text,
  KEY sour_id (s_id),
  KEY sour_name (s_name),
  KEY sour_file (s_file)
) TYPE=MyISAM;

CREATE TABLE pgv_tblver (
  t_table varchar(255) NOT NULL default '',
  t_version int(10) unsigned NOT NULL default '0'
) TYPE=MyISAM;

INSERT INTO pgv_tblver (t_table, t_version) VALUES ('pgv_blocks', 1);
INSERT INTO pgv_tblver (t_table, t_version) VALUES ('pgv_dates', 1);
INSERT INTO pgv_tblver (t_table, t_version) VALUES ('pgv_families', 1);
INSERT INTO pgv_tblver (t_table, t_version) VALUES ('pgv_favorites', 1);
INSERT INTO pgv_tblver (t_table, t_version) VALUES ('pgv_individuals', 1);
INSERT INTO pgv_tblver (t_table, t_version) VALUES ('pgv_messages', 1);
INSERT INTO pgv_tblver (t_table, t_version) VALUES ('pgv_names', 1);
INSERT INTO pgv_tblver (t_table, t_version) VALUES ('pgv_news', 1);
INSERT INTO pgv_tblver (t_table, t_version) VALUES ('pgv_other', 1);
INSERT INTO pgv_tblver (t_table, t_version) VALUES ('pgv_placelinks', 1);
INSERT INTO pgv_tblver (t_table, t_version) VALUES ('pgv_places', 1);
INSERT INTO pgv_tblver (t_table, t_version) VALUES ('pgv_sources', 1);

CREATE TABLE pgv_users (
  u_xoopsid int(10) unsigned,
  u_username varchar(255),
  u_gedcomid text,
  u_rootid text,
  u_canedit text,
  u_contactmethod varchar(255),
  u_defaulttab int unsigned
) TYPE=MyISAM;

CREATE TABLE pgv_media (
  m_id int(11) NOT NULL auto_increment,
  m_media varchar(15) NOT NULL,
  m_ext char(6) NOT NULL,
  m_titl varchar(255) NULL,
  m_file varchar(255) NOT NULL,
  m_gedfile varchar(255) NOT NULL,
  m_gedrec text,
  PRIMARY KEY(m_id),
  KEY m_media (m_media)
) TYPE=MyISAM;

CREATE TABLE pgv_media_mapping (
  m_id int(11) NOT NULL auto_increment,
  m_media varchar(15) NOT NULL,
  m_indi varchar(15) NOT NULL,
  m_order int(11) NOT NULL,
  m_gedfile varchar(255) NOT NULL,
  m_gedrec text,
  PRIMARY KEY(m_id),
  KEY m_media (m_media)
) TYPE=MyISAM;
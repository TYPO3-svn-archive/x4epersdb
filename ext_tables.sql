#
# Table structure for table 'tx_listfeuseruni_function'
#
CREATE TABLE tx_x4epersdb_function (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
    sys_language_uid int(11) DEFAULT '0' NOT NULL,
    l18n_parent int(11) DEFAULT '0' NOT NULL,
    l18n_diffsource mediumblob NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_listfeuseruni_function'
#
CREATE TABLE tx_x4epersdb_department (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	persons int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_listfeuseruni_function'
#
CREATE TABLE tx_x4epersdb_affiliation (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

CREATE TABLE tx_x4epersdb_institutes (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

CREATE TABLE tx_x4epersdb_person_institute_mm (
	uid_local int(11) NOT NULL,
	uid_foreign int(11) NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign),
	KEY uid_local_foreign (uid_local,uid_foreign)
);

CREATE TABLE tx_x4epersdb_buildings (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	street text NOT NULL,
	zip text NOT NULL,
	city text NOT NULL,
	faculty text NOT NULL,
	page_id int(11) DEFAULT '0' NOT NULL,


	PRIMARY KEY (uid),
	KEY parent (pid)
);

CREATE TABLE tx_x4epersdb_person_building_mm (
	uid_local int(11) NOT NULL,
	uid_foreign int(11) NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign),
	KEY uid_local_foreign (uid_local,uid_foreign)
);

CREATE TABLE tx_x4epersdb_person_department_mm (
	uid_local int(11) NOT NULL,
	uid_foreign int(11) NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign),
	KEY uid_local_foreign (uid_local,uid_foreign)
);

#
# Table structure for table 'fe_users'
#
CREATE TABLE tx_x4epersdb_person (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l18n_parent int(11) DEFAULT '0' NOT NULL,
    l18n_diffsource mediumblob NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	alumni tinyint(3) DEFAULT '0' NOT NULL,
	function blob NOT NULL,
	departments int(11) NOT NULL,
	function_suffix tinytext NOT NULL,
	title tinytext NOT NULL,
	firstname tinytext NOT NULL,
	lastname tinytext NOT NULL,
	title_after tinytext NOT NULL,
	email tinytext NOT NULL,
	email2 tinytext NOT NULL,
	mobile_phone tinytext NOT NULL,
	alias tinytext NOT NULL,
	image tinytext NOT NULL,
	office_address text NOT NULL,
	office_roomnumber tinytext NOT NULL,
	office_zip tinytext NOT NULL,
	office_location tinytext NOT NULL,
	office_country tinytext NOT NULL,
	office_phone tinytext NOT NULL,
	office_phone2 tinytext NOT NULL,
	office_fax tinytext NOT NULL,
	address tinytext NOT NULL,
	zip tinytext NOT NULL,
	city tinytext NOT NULL,
	country tinytext NOT NULL,
	phone tinytext NOT NULL,
	phone2 tinytext NOT NULL,
	mobile tinytext NOT NULL,
	fax tinytext NOT NULL,
	url tinytext NOT NULL,
	beuser blob NOT NULL,
	personal_page varchar(30) NOT NULL default '',
	resume_page varchar(30) NOT NULL default '',
	course_page varchar(30) NOT NULL default '',
	research_page varchar(30) NOT NULL default '',
	office_mobile_phone tinytext NOT NULL,
	profile text NOT NULL,
	news text NOT NULL,
	research text NOT NULL,
	membership text NOT NULL,
	publadmin tinyint(3) DEFAULT '0' NOT NULL,
	qualiadmin tinyint(3) DEFAULT '0' NOT NULL,
	showpublics tinyint(3) DEFAULT '1' NOT NULL,
	showpublicsinmenu tinyint(3) DEFAULT '1' NOT NULL,
	showisislink tinyint(3) DEFAULT '1' NOT NULL,
	isisid tinytext NOT NULL,
	password tinytext NOT NULL,
	feuser_id int NOT NULL,
	username tinytext NOT NULL,
	lecture_link tinytext NOT NULL,
	floor tinytext NOT NULL,
	room tinytext NOT NULL,
	buildings int(11) DEFAULT '0' NOT NULL,
	institutes int(11) DEFAULT '0' NOT NULL,
	fe_groups text NOT NULL,
	
	dni int(11) DEFAULT '0' NOT NULL,
	mcss_id int(11) DEFAULT '0' NOT NULL,
	main_entry tinyint(4) DEFAULT '0' NOT NULL,
	company tinytext NOT NULL,
	static_info_country int(4) NOT NULL,
	tx_x4emutation_department tinytext NOT NULL,
	tx_x4emutation_affiliation tinytext NOT NULL,
	tx_x4emutation_speciality tinytext NOT NULL,
	external_id varchar(50) NOT NULL default '',
	rssUrl tinytext NOT NULL default '',
	PRIMARY KEY (uid),
	KEY parent (pid)
);
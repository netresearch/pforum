#
# Table structure for table 'tx_pforum_domain_model_forum'
#
CREATE TABLE tx_pforum_domain_model_forum (
	title varchar(255) DEFAULT '' NOT NULL,
	teaser varchar(255) DEFAULT '' NOT NULL,
	topics int(11) unsigned DEFAULT '0' NOT NULL
);

#
# Table structure for table 'tx_pforum_domain_model_topic'
#
CREATE TABLE tx_pforum_domain_model_topic (
	title varchar(255) DEFAULT '' NOT NULL,
	description text NOT NULL,
	posts int(11) unsigned DEFAULT '0' NOT NULL,
	anonymous_user int(11) unsigned DEFAULT '0',
	frontend_user int(11) unsigned DEFAULT '0',
	images int(11) unsigned DEFAULT '0' NOT NULL,
	forum int(11) unsigned DEFAULT '0' NOT NULL
);

#
# Table structure for table 'tx_pforum_domain_model_post'
#
CREATE TABLE tx_pforum_domain_model_post (
	title varchar(255) DEFAULT '' NOT NULL,
	description varchar(255) DEFAULT '' NOT NULL,
	anonymous_user int(11) unsigned DEFAULT '0',
	frontend_user int(11) unsigned DEFAULT '0',
	images int(11) unsigned DEFAULT '0' NOT NULL,
	topic int(11) unsigned DEFAULT '0' NOT NULL
);

#
# Table structure for table 'tx_pforum_domain_model_anonymoususer'
#
CREATE TABLE tx_pforum_domain_model_anonymoususer (
	name varchar(255) DEFAULT '' NOT NULL,
	username varchar(255) DEFAULT '' NOT NULL,
	email varchar(255) DEFAULT '' NOT NULL
);

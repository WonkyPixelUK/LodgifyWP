CREATE TABLE wp_lodgifywp_licenses (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    license_key varchar(255) NOT NULL,
    email varchar(255) NOT NULL,
    created datetime NOT NULL,
    expires datetime NOT NULL,
    domains text,
    domain_limit int(11) NOT NULL DEFAULT '1',
    status varchar(20) NOT NULL DEFAULT 'active',
    is_staging tinyint(1) NOT NULL DEFAULT '0',
    agency_name varchar(255) DEFAULT NULL,
    agency_id varchar(255) DEFAULT NULL,
    parent_license_key varchar(255) DEFAULT NULL,
    license_type varchar(20) NOT NULL DEFAULT 'standard',
    PRIMARY KEY (id),
    UNIQUE KEY license_key (license_key),
    KEY agency_id (agency_id),
    KEY parent_license_key (parent_license_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 
#!/bin/bash

TMPFILE="/tmp/tmp.create.db"

create_db() {

DBNAME="$1"
USER_USER="$2"
USER_PASS="$3"
ROOT_PASS="$4"


# DROP USER '${USER_USER}'@'localhost'

cat > ${TMPFILE} << EOF
DROP DATABASE IF EXISTS ${DBNAME};
DROP USER '${USER_USER}'@'localhost';
CREATE DATABASE $DBNAME;
CREATE USER '${USER_USER}'@'localhost' IDENTIFIED BY '${USER_PASS}';
GRANT SELECT,INSERT,UPDATE,DELETE ON ${DBNAME}.* TO '${USER_USER}'@'localhost';
FLUSH PRIVILEGES;

USE ${DBNAME};

CREATE TABLE products (
        id              INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        sku             VARCHAR(50),
        name            VARCHAR(100),
        description     VARCHAR(1024),
        price           DECIMAL(12, 2) NOT NULL,
        clothing_size   VARCHAR(50),
        dimensions      VARCHAR(50),
        changed_on      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                            ON UPDATE CURRENT_TIMESTAMP
    );

CREATE UNIQUE INDEX index_products_sku              ON products (sku);
CREATE UNIQUE INDEX index_products_clothing_size    ON products (clothing_size);
CREATE UNIQUE INDEX index_products_dimensions       ON products (dimensions);


CREATE TABLE product_images (
        id              INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        img             LONGBLOB NOT NULL,
        changed_on      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                            ON UPDATE CURRENT_TIMESTAMP
    );

CREATE TABLE sessions (
        id                  INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        token               VARCHAR(256) NOT NULL,
        valid_for_seconds   INT NOT NULL,
        created_on          TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
EOF

mysql -uroot -p"${ROOT_PASS}" < ${TMPFILE}
}


echo -n "Database name [bigshopper]: "
read DBNAME
if [ -z ${DBNAME} ]; then
    DBNAME="bigshopper"
fi

echo -n "user username [bigshopper]: "
read USER_USER
if [ -z ${USER_USER} ]; then
    USER_USER="bigshopper"
fi

echo -n "user user password: "
read -s USER_PASS
if [ -z ${USER_PASS} ]; then
    echo "error: no password provided"
    exit 1
fi
echo
echo "------------"
echo -n "Provide MySQL root pwd: "
read -s ROOT_PASS
if [ -z ${ROOT_PASS} ]; then
    echo "error: no password provided"
    exit 1
fi
echo

create_db "${DBNAME}" "${USER_USER}" "${USER_PASS}" "${ROOT_PASS}"


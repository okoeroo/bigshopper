#!/bin/bash

CONFIG="/usr/local/share/bigshopper/config.cron"
TMPFILE="/tmp/cron.$(date +%s).tmp"

# Source the config file
. ${CONFIG}

mysql \
    -u${USER} \
    -p${PASS} \
    --host=${HOST} ${DBNAME} \
    -e 'SELECT id, token, valid_for_seconds, created_on_unix, (valid_for_seconds + created_on_unix) as total from sessions group by id, token, total having  UNIX_TIMESTAMP(now()) > total' > ${TMPFILE}

cat ${TMPFILE} | \
    cut -s -f 1 | \
    grep -v id | \
while read ID; do
    mysql \
        -u${USER} \
        -p${PASS} \
        --host=${HOST} ${DBNAME} \
        -e "delete from sessions where id = $ID";

done

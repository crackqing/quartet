#!/bin/bash
REDIS_DIR=/usr/local/redis/var
REDIS_CMD=/usr/local/redis/bin/redis-cli
RSYNC_PASSWD=/etc/rsync.passwd
now="$(date -d'+0 day' +'%Y%m%d%H%M%S')"

$REDIS_CMD save 
[ $? -eq 0 ] && {
    cp $REDIS_DIR/dump.rdb $REDIS_DIR/dump_${now}.rdb
    rsync -ave  $REDIS_DIR/dump_${now}.rdb ruser@45.195.145.28::redisrdb --password-file=${RSYNC_PASSWD}
}

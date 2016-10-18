#!/bin/sh

THEDATE=`date +%d-%m-%y`
SEVENDAYDATE=`date -d '-7 day' +%d-%m-%y`
THEPATH="/tmp/"

if test $1 = 'luke' ; then
	THEDB="GICRM"
	THEDBUSER="root"
	THEDBPW="pr0sc@p3#"
elif test $1 = 'trace' ; then    
	THEDB="GICRM"
	THEDBUSER="root"
	THEDBPW="pr0sc@p3#"
elif test $1 = 'heffner' ; then    
	THEDB="GICRM"
	THEDBUSER="root"
	THEDBPW="pr0sc@p3#"
elif test $1 = 'wildwood' ; then    
	THEDB="GICRM"
	THEDBUSER="root"
	THEDBPW="pr0sc@p3#"
elif test $1 = 'nurney' ; then    
	THEDB="GICRM"
	THEDBUSER="root"
	THEDBPW="pr0sc@p3#"
elif test $1 = 'dirk' ; then    
	THEDB="GICRM"
	THEDBUSER="root"
	THEDBPW="pr0sc@p3#"
elif test $1 = 'barry' ; then    
	THEDB="GICRM"
	THEDBUSER="root"
	THEDBPW="pr0sc@p3#"
elif test $1 = 'hh' ; then    
	THEDB="GICRM"
	THEDBUSER="root"
	THEDBPW="pr0sc@p3#"
elif test $1 = 'dev' ; then    
	THEDB="zurmo"
	THEDBUSER="zurmo"
	THEDBPW="zurmo"
else
	THEDB="NewCRM"
	THEDBUSER="zurmo"
	THEDBPW="zurmo"
fi

THEBUCKET="gicrmdatabasebackup/"${1}

# export the database 
mysqldump -u $THEDBUSER -p${THEDBPW} $THEDB | gzip > ${THEPATH}db_${THEDB}_${THEDATE}.gz

# remove backups older than 7 days
find ${THEPATH}db_${THEDB}_* -mtime +7 -exec rm {} \;

# put that db file to amazon
s3cmd put ${THEPATH}db_${THEDB}_${THEDATE}.gz s3://$THEBUCKET/

# delete the seven days old db file 
s3cmd del s3://$THEBUCKET/db_${THEDB}_${SEVENDAYDATE}.gz

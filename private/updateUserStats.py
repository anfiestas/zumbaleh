import MySQLdb
import sys
import datetime
import time

#connecting to DB
conn = MySQLdb.connect('localhost','','', '')
conn2 =  MySQLdb.connect('localhost','','', '')
cursor = conn.cursor()
cursor2 = conn2.cursor()
#foreach symbol update DB
now=datetime.datetime.now()
lastMonth = now - datetime.timedelta(days=30)
lastMonth = time.mktime(lastMonth.timetuple())
print "delta time ", datetime.timedelta(days=30)
print "Now:" , now
print "last Month: %s" % lastMonth

currentUserId = 0
currentActiveDays=0
#Get User active days
result=cursor.execute("select us.uid,us.pin,((ud.last_connection - us.creation_time_gmt)/(24*60*60)) as activedays FROM user_stats us,user_device ud WHERE (us.uid=ud.uid and ud.uid!=-1) having activedays >0")
#foreach value 
row= cursor.fetchone()
while row is not None:
	if (row[0]!=currentUserId):
		currentUserId=row[0]
		currentActiveDays=row[2]
	else:
		if (row[2]>currentActiveDays):
			currentActiveDays=row[2]

	#updateDB if next is different user
	row=cursor.fetchone()
	if(row is not None and row[0]!=currentUserId):
		try:
			result2=cursor2.execute("UPDATE user_stats set active_days=%i where uid=%i " % (currentActiveDays,currentUserId))	
			print "Updated user:" , currentUserId
        		print "days:", currentActiveDays
		except:
			print "Error"

conn2.commit()

print "Users Activity updated",result	
cursor.close()
conn.close()
cursor2.close()
conn2.close()

## Actualizar contadores conversaciones

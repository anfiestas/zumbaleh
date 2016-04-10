import os
import MySQLdb
import sys
import datetime
import time

#connecting to DB
conn = MySQLdb.connect('localhost','spoorer1','.i$W!:3+5@@V"/v', 'spoora')
cursor = conn.cursor()
#foreach symbol update DB
now=datetime.datetime.now()
lastDay = now - datetime.timedelta(days=1)
lastDay = time.mktime(lastDay.timetuple())
print "delta time ", datetime.timedelta(days=1)
print "Now:" , now
print "last day: %s" % lastDay

result=cursor.execute("DELETE from user_device WHERE type = 2 AND last_connection < %f " % (lastDay))
conn.commit()
print "deleted rows:",result	
cursor.close()
conn.close()
print "End of Cleaning Sessions" 

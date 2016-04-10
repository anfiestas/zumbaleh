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

result=cursor.execute("UPDATE user set tokens_day=0 where tokens_day > 0")
conn.commit()
print "1000 tokens authorized to all users for this day",result	
cursor.close()
conn.close()
print "End of tokens reset" 

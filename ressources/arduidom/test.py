import time
import subprocess
import random

while 1 == 1:
    time.sleep(1)
    cmd = 'nice -n 19 /usr/bin/php /usr/share/nginx/www/jeedom/plugins/arduidom/core/php/jeeArduidom.php '
    cmd += str(int(random.randrange(1,4)))
    cmd += "="
    cmd += str(int(random.randrange(0,2)))
    print(cmd)
    subprocess.Popen(cmd, shell=True)
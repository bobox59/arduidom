touch /tmp/dependancy_arduidom_in_progress
echo 0 > /tmp/dependancy_arduidom_in_progress
echo "Launch install of arduidom dependancy"
echo "-------------------------------------"
echo ">>> Apt Clean"
sudo apt-get clean
echo 20 > /tmp/dependancy_arduidom_in_progress
echo ">>> Apt Update"
sudo apt-get update
echo 40 > /tmp/dependancy_arduidom_in_progress
echo ">>> Install Arduino"
sudo apt-get install -y arduino
echo 50 > /tmp/dependancy_arduidom_in_progress
echo ">>> Install Python PIP"
sudo apt-get install -y python-pip
echo 60 > /tmp/dependancy_arduidom_in_progress
echo ">>> Install INOTOOLS"
sudo pip install ino
echo 70 > /tmp/dependancy_arduidom_in_progress
echo ">>> Install INOTOOLS"
sudo easy_install ino
echo 80 > /tmp/dependancy_arduidom_in_progress
echo ">>> Install AVR-DUDE"
apt-get install -y avr-dude
echo 90 > /tmp/dependancy_arduidom_in_progress
sudo usermod -G dialout www-data
echo 100 > /tmp/dependancy_arduidom_in_progress
echo " ---------------------------- "
echo "|  Everything is installed!  |"
echo " ---------------------------- "
rm /tmp/dependancy_arduidom_in_progress


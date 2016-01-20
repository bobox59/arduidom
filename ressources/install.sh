touch /tmp/dependancy_arduidom_in_progress
echo 0 > /tmp/dependancy_arduidom_in_progress
echo "Launch install of arduidom dependancy"
sudo apt-get clean
echo 20 > /tmp/dependancy_arduidom_in_progress
sudo apt-get update
echo 30 > /tmp/dependancy_arduidom_in_progress
sudo apt-get install -y arduino
echo 50 > /tmp/dependancy_arduidom_in_progress
sudo easy_install ino
echo 70 > /tmp/dependancy_arduidom_in_progress
apt-get install -y avr-dude
echo 80 > /tmp/dependancy_arduidom_in_progress
sudo usermod -G dialout www-data
echo 100 > /tmp/dependancy_arduidom_in_progress
echo "Everything is successfully installed!"
rm /tmp/dependancy_arduidom_in_progress


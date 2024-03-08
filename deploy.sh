#!/bin/bash
echo "This script is used to deploy the project."
echo "It will install ALL the necessary tools and dependencies on a server."
echo "If you only want to update the application, please use update.sh instead."
echo "Please note that this script is only for Ubuntu/Debian based systems."
echo "Please make sure you have the necessary permissions to install software on the server."
v=true
while $v ; do
    read -p "Do you wish to perform this action?" yesno
    case $yesno in
        [Yy]* ) 
            echo "You answered yes"
			v=false
        ;;
        [Nn]* ) 
            echo "You answered no, exiting"
            exit
        ;;
        * ) echo "Answer either yes or no!";;
    esac
done

echo "Deploying the project..."


tools=(git php composer)

for tool in ${tools[@]}
do
    echo "Verifying the installation of necessary tools."
    echo "Verifiying $tool installation..."
    if command -v $tool > /dev/null; then
        echo "$tool is installed."
    else
        echo "$tool is not installed."
        sudo apt-get install $tool
        exit 1
    fi
done

if ! test -d ".git"
then
    echo "Required files are missing."
    git clone
else
    echo "Directory .git is there. Proceeding with the deployment."
fi
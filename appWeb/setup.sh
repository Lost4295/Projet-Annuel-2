#!/bin/bash

# echo "Now executing git pull"
# git pull
T="composer update"
myArray=("git pull" "composer update")
for i in ${!myArray[@]}; do
  echo "Now executing ${myArray[$i]}"
  echo "---------------------------------------------"
  ${myArray[$i]}
  echo "--------------------------------------------"
# echo "element $i is ${myArray[$i]}"
done
# $T  
sleep 2


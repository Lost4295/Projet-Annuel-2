#!/bin/bash

echo "Now executing git pull"
git pull
T="composer update"
myArray=("git pull" "composer update" "mouse" "frog")
for i in ${!myArray[@]}; do
  echo "Now executing $T"
  echo "element $i is ${myArray[$i]}"
done
# $T  
sleep 2


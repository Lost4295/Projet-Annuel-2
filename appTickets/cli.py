import keyboard  # using module keyboard
import os
from time import sleep

def printmenu(int:int):
    optlist= ["Add Ticket",
    "Search Ticket",
    "Exit",]
    for i in range(len(optlist)):
        if i == int:
            print(f"-> {i+1}. {optlist[i]}")
        else:
            print(f"   {i+1}. {optlist[i]}")
    print("Enter your choice: ")

print("-----------------------------")

i = 0
while True:
    printmenu(i)
    key = keyboard.read_key(suppress=True)
    sleep(0.18)
    os.system('clear')
    if key == "haut":
        if i == 0:
            i = 2
        else:
            i -= 1
    elif key == "bas":
        if i == 2:
            i = 0
        else:
            i += 1
    elif key == "enter":
        print(f"Selected option: {i+1}")
        break
print("-----------------------------")

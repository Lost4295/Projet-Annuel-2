import requests
import dotenv
import os
import time

dotenv.load_dotenv()
EMAIL = os.getenv("EMAIL")
PASSWORD = os.getenv("PASSWORD")

response = requests.post("http://pcs.freeboxos.fr:8080/auth", json={"email": EMAIL, "password": PASSWORD})
if response.status_code == 200:
    token = response.json()["token"]
else:
    print("Error while trying to authenticate")
    exit()
print("-----------------------------")

print("Bienvenue sur l'application de ticketing de PCS !")
print("1. Créer un ticket")
print("2. Lister les tickets")
print("3. Quitter")
print("-----------------------------")

while True:
    choice = input("Votre choix : ")
    if choice == "1":
        title = input("Titre du ticket : ")
        content = input("Contenu du ticket : ")
        response = requests.post("http://pcs.freeboxos.fr:8080/api/tickets", json={"title": title, "content": content}, headers={"Authorization": f"Bearer {token}"})
        if response.status_code == 200:
            print("Ticket créé avec succès !")
        else:
            print("Erreur lors de la création du ticket")
    elif choice == "2":
        response = requests.get("http://pcs.freeboxos.fr:8080/api/tickets", headers={"Authorization": f"Bearer {token}"})
        if response.status_code == 200:
            print("Liste des tickets :")
            tickets = response.json()
            for ticket in response.json():
                print(f"Titre : {ticket['title']}")
                print(f"Contenu : {ticket['content']}")
                print("-----------------------------")
        else:
            print("Erreur lors de la récupération des tickets")
            time.sleep(2)
            print(response.json())
    elif choice == "3":
        break
    else:
        print("Choix invalide")
        print("-----------------------------")


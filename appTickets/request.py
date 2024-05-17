import requests
import dotenv
import os
import time


dotenv.load_dotenv()
EMAIL = os.getenv("EMAIL")
PASSWORD = os.getenv("PASSWORD")
CHOICE = "Votre choix : "
INVALID = "Choix invalide"

class colors:
    reset = '\033[0m'
    bold = '\033[01m'
    disable = '\033[02m'
    underline = '\033[04m'
    reverse = '\033[07m'
    strikethrough = '\033[09m'
    invisible = '\033[08m'

    class text:
        black = '\033[30m'
        red = '\033[31m'
        green = '\033[32m'
        orange = '\033[33m'
        blue = '\033[34m'
        purple = '\033[35m'
        cyan = '\033[36m'
        lightgrey = '\033[37m'
        darkgrey = '\033[90m'
        lightred = '\033[91m'
        lightgreen = '\033[92m'
        yellow = '\033[93m'
        lightblue = '\033[94m'
        pink = '\033[95m'
        lightcyan = '\033[96m'

    class bg:
        black = '\033[40m'
        red = '\033[41m'
        green = '\033[42m'
        orange = '\033[43m'
        blue = '\033[44m'
        purple = '\033[45m'
        cyan = '\033[46m'
        lightgrey = '\033[47m'

# Authentication


def authenticate():
    url = "http://pcs.freeboxos.fr:8080"
    response = requests.post(url+"/auth",
                             json={"email": EMAIL, "password": PASSWORD})
    if response.status_code == 200:
        return [url, response.json()["token"]]
    else:
        print("Error while trying to authenticate")
        print("Trying with IP address...")
        url = "http://82.65.24.229:8080"
        response = requests.post(url+"/auth",
                                 json={"email": EMAIL, "password": PASSWORD})
        if response.status_code == 200:
            return [url, response.json()["token"]]
        else:
            print("Error while trying to authenticate")
            exit()

# Main menu


def main_menu(data):
    while True:
        print("-----------------------------")
        print("Bienvenue sur l'application de ticketing de PCS !")
        print("1. Lister les tickets")
        print("2. Voir un ticket")
        print("3. Créer un ticket")
        print("4. Modifier un ticket")
        print("5. Supprimer un ticket")
        print("6. Gestion des utilisateurs")
        print("7. Quitter")
        print("-----------------------------")

        choice = input(CHOICE)
        os.system('cls')
        match choice:
            case "1":
                list_tickets(data)
            case "2":
                see_ticket(data)
            case "3":
                create_ticket(data)
            case "4":
                modify_ticket(data)
            case "5":
                delete_ticket(data)
            case "6":
                user_management_menu(data)
            case "7":
                break
            case _:
                print(INVALID)
                print("-----------------------------")


# Créer ticket
# TODO : AJOUTER LES CHOICES

def create_ticket(data):
    url = data[0]
    token = data[1]
    print("Création d'un ticket (pour quitter, entrez le caractère 0):")
    titre = input("Titre du ticket : ")
    if titre == "0":
        return
    description = input("Contenu du ticket : ")
    if description == "0":
        return
    date_ouverture =  time.strftime("%Y-%m-%dT%H:%M:%S.000Z", time.localtime())
    ok = False
    while not ok:
        demandeur = input(
            "Entrez l'ID du demandeur ( pour le savoir, lancez la commande pour voir tous les utilisateurs ) : ")
        if demandeur == "0":
            return
        if not user_exists(data, demandeur):
            print("L'utilisateur n'existe pas")
        else:
            ok = True
    ok = False
    while not ok:
        print("category :")
        category = input(CHOICE)
        match category:
            case "0":
                return
            case "1":
                ok = True
            case _:
                print(INVALID)
    ok = False
    while not ok:
        print("Type du ticket : ")
        print("1. Epic")
        print("2. Task")
        print("3. Story")
        print("4. Bug")
        print("5. Subtask")
        typeid = input(CHOICE)
        match typeid:
            case "0":
                return
            case "1" | "2" | "3" | "4" | "5":
                ok = True
            case _:
                print(INVALID)
    print("-----------------------------")
    ok = False
    while not ok:
        print("Statut du ticket : ")
        print("1. Nouveau")
        print("2. En cours")
        print("3. Résolu")
        print("4. Fermé")
        print("5. En attente")
        print("6. Rejeté")
        status = input(CHOICE)
        match status:
            case "0":
                return
            case "1" | "2" | "3" | "4" | "5" | "6":
                ok = True
            case _:
                print(INVALID)
    print("-----------------------------")
    ok = False
    while not ok:
        print("Priorité du ticket : ")
        print("1. Bas")
        print("2. Normal")
        print("3. Haut")
        print("4. Urgent")
        priority = input(CHOICE)
        match priority:
            case "0":
                return
            case "1" | "2" | "3" | "4":
                ok = True
            case _:
                print(INVALID)
    print("-----------------------------")
    ok = False
    while not ok:
        print("Urgence du ticket : ")
        print("1. Faible")
        print("2. Moyenne")
        print("3. Haute")
        print("4. Critique")
        urgence = input(CHOICE)
        match urgence:
            case "0":
                return
            case "1" | "2" | "3" | "4":
                ok = True
            case _:
                print(INVALID)
    print("-----------------------------")
    testdata = {
        "dateOuverture": date_ouverture,
        "demandeur": "/api/users/"+demandeur,
        "category": category,
        "type": typeid,
        "status": status,
        "priority": priority,
        "titre": titre,
        "description": description,
        "urgence": urgence,
    }
    response = requests.post(url+"/api/tickets",
                             json=testdata, headers={"Authorization": f"Bearer {token}", "Content-Type": "application/ld+json"})
    if response.status_code == 201:
        print("Ticket créé avec succès !")
    else:
        print("Erreur lors de la création du ticket")
        print(response.json())


def see_ticket(data):
    url = data[0]
    token = data[1]
    ticket_id = input("ID du ticket à voir (entrez 0 pour annuler): ")
    if ticket_id == "0":
        return
    response = requests.get(f"{url}/api/tickets/{ticket_id}",
                            headers={"Authorization": f"Bearer {token}"})
    if response.status_code == 200:
        ticket = response.json()
        print("Titre :", ticket["titre"])
        print("Contenu :", ticket["description"])
        print("-----------------------------")
    else:
        print("Erreur lors de la récupération du ticket")
        raise response.json()

# Function to list tickets


def list_tickets(data):
    url = data[0]
    token = data[1]
    response = requests.get(url+"/api/tickets",
                            headers={"Authorization": f"Bearer {token}"})
    if response.status_code == 200:
        print("Liste des tickets :")
        tickets = response.json()
        print("-----------------------------")
        print(tickets["hydra:totalItems"], "tickets trouvés")
        print("-----------------------------")
        ticketsl = tickets["hydra:member"]
        for ticket in ticketsl:
            print("Titre :", ticket["titre"])
            print("Contenu :", ticket["description"])
            print("-----------------------------")
        print("1. Voir un ticket")
        print("2. Retour")
        ch = input(CHOICE)
        if ch == "1":
                see_ticket(data)
        else:
            return
    else:
        print("Erreur lors de la récupération des tickets")
        time.sleep(2)
        print(response.json())

# Function to modify a ticket
# TODO : VOIR POUR LES BONNES DATES

def modify_ticket(data):
    url = data[0]
    token = data[1]
    date_ouverture = ""
    date_fermeture = ""
    demandeur = ""
    last_update_date = ""
    category = ""
    typeid = ""
    status = ""
    priority = ""
    titre = ""
    description = ""
    pj = ""
    urgence = ""
    resolveur = ""
    ticket_id = input("ID du ticket à modifier (entrez 0 pour annuler): ")
    if ticket_id == "0":
        return
    while True:
        print("1. DateOuverture")
        print("2. DateFermeture")
        print("3. Demandeur")
        print("4. LastUpdateDate")
        print("5. Category")
        print("6. Type")
        print("7. Status")
        print("8. Priority")
        print("9. Titre")
        print("10. Description")
        print("11. Pj")
        print("12. Urgence")
        print("13. Resolveur")
        print("14. Confirmer")
        print("0. Annuler")
        print("-----------------------------")
        choice = input(CHOICE)
        match choice:
            case "1":
                date_ouverture = input("Nouvelle date d'ouverture : ")
            case "2":
                date_fermeture = input("Nouvelle date de fermeture : ")
            case "3":
                demandeur = input("Nouveau demandeur : ")
            case "4":
                last_update_date = input(
                    "Nouvelle date de dernière mise à jour : ")
            case "5":
                category = input("Nouvelle catégorie : ")
            case "6":
                typeid = input("Nouveau type : ")
            case "7":
                status = input("Nouveau statut : ")
            case "8":
                priority = input("Nouvelle priorité : ")
            case "9":
                titre = input("Nouveau titre : ")
            case "10":
                description = input("Nouveau contenu : ")
            case "11":
                pj = input("Nouvelle pièce jointe : ")
            case "12":
                urgence = input("Nouvelle urgence : ")
            case "13":
                resolveur = input("Nouveau resolveur : ")
            case "14":
                print("Modification du ticket :")
                if date_ouverture != "":
                    print("dateOuverture :", date_ouverture)
                if date_fermeture != "":
                    print("dateFermeture :", date_fermeture)
                if demandeur != "":
                    print("demandeur :", demandeur)
                if last_update_date != "":
                    print("lastUpdateDate :", last_update_date)
                if category != "":
                    print("category :", category)
                if typeid != "":
                    print("type :", typeid)
                if status != "":
                    print("status :", status)
                if priority != "":
                    print("priority :", priority)
                if titre != "":
                    print("titre :", titre)
                if description != "":
                    print("description :", description)
                if pj != "":
                    print("pj :", pj)
                if urgence != "":
                    print("urgence :", urgence)
                if resolveur != "":
                    print("resolveur :", resolveur)
                while True:
                    confirmer = input("Confirmer la modification (y/n) : ")
                    if confirmer == "y":
                        json = create_json(date_ouverture, date_fermeture, demandeur, last_update_date,
                                           category, typeid, status, priority, titre, description, pj, urgence, resolveur)
                        response = requests.put(
                            f"{url}/api/tickets/{ticket_id}", json=json, headers={"Authorization": f"Bearer {token}"})
                        if response.status_code == 200:
                            print("Ticket modifié avec succès !")
                        else:
                            print("Erreur lors de la modification du ticket")
                    else:
                        print("Modification annulée")
                        break
            case "0":
                break
            case _:
                print(INVALID)
                print("-----------------------------")


def create_json(date_ouverture, date_fermeture, demandeur, last_update_date, category, typeid, status, priority, titre, description, pj, urgence, resolveur):
    json = {}
    if date_ouverture != "":
        json["dateOuverture"] = date_ouverture
    if date_fermeture != "":
        json["dateFermeture"] = date_fermeture
    if demandeur != "":
        json["demandeur"] = demandeur
    if last_update_date != "":
        json["lastUpdateDate"] = last_update_date
    if category != "":
        json["category"] = category
    if typeid != "":
        json["type"] = typeid
    if status != "":
        json["status"] = status
    if priority != "":
        json["priority"] = priority
    if titre != "":
        json["titre"] = titre
    if description != "":
        json["description"] = description
    if pj != "":
        json["pj"] = pj
    if urgence != "":
        json["urgence"] = urgence
    if resolveur != "":
        json["resolveur"] = resolveur
    return json


# Function to delete a ticket
def delete_ticket(data):
    url = data[0]
    token = data[1]
    ticket_id = input("ID du ticket à supprimer (entrez 0 pour annuler ): ")
    if ticket_id == "0":
        return
    response = requests.delete(
        f"{url}/api/tickets/{ticket_id}", headers={"Authorization": f"Bearer {token}"})
    if response.status_code == 204:
        print("Ticket supprimé avec succès !")
    else:
        print("Erreur lors de la suppression du ticket")


# Submenu for user management
def user_management_menu(data):
    while True:
        print("-----------------------------")
        print("Gestion des utilisateurs")
        print("1. Voir un utilisateur")
        print("2. Lister les utilisateurs")
        print("3. Retour au menu principal")
        print("-----------------------------")

        choice = input(CHOICE)
        os.system('cls')
        if choice == "1":
            see_users(data)
        elif choice == "2":
            list_users(data)
        elif choice == "3":
            break
        else:
            print(INVALID)
            print("-----------------------------")


def user_exists(data, id):
    url = data[0]
    token = data[1]

    response = requests.get(f"{url}/api/users/{id}",
                            headers={"Authorization": f"Bearer {token}"})
    if response.status_code == 200:
        return True
    else:
        return False

def see_users(data):
    url = data[0]
    token = data[1]
    user_id = input("ID de l'utilisateur à voir  (entrez 0 pour annuler): ")
    if user_id == "0":
        return
    response = requests.get(f"{url}/api/users/{user_id}",
                            headers={"Authorization": f"Bearer {token}"})
    if response.status_code == 200:
        user = response.json()
        print("Id :", user["@id"])
        print("Nom :", user["nom"])
        print("Prenom :", user["prenom"])
        print("-----------------------------")
    else:
        print("Erreur lors de la récupération de l'utilisateur")
        time.sleep(2)
        print(response.json())

# Liste de tous les utilisateurs
def list_users(data):
    url = data[0]
    token = data[1]
    response = requests.get(url+"/api/users",
                            headers={"Authorization": f"Bearer {token}"})
    if response.status_code == 200:
        print("Liste des utilisateurs :")
        users = response.json()
        print("-----------------------------")
        print(users["hydra:totalItems"], "utilisateurs trouvés")
        print("-----------------------------")
        usersl = users["hydra:member"]
        for user in usersl:
            print("Id :", user["@id"].replace("/api/users/", ""))
            print("Nom :", user["nom"])
            print("Prenom :", user["prenom"])
            print("-----------------------------")
    else:
        print("Erreur lors de la récupération des utilisateurs")
        time.sleep(2)
        print(response.json())


# Main execution
if __name__ == '__main__':
    token = authenticate()
    main_menu(token)

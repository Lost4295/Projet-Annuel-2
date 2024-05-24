import requests
import dotenv
import os
import time


dotenv.load_dotenv()
EMAIL = os.getenv("EMAIL")
PASSWORD = os.getenv("PASSWORD")
CHOICE = "Votre choix : "
INVALID = "Choix invalide"
PREVIOUS = "hydra:previous"
MEMBER ="hydra:member" 
VIEW = "hydra:view"
NEXT= "hydra:next"
ITEMS ="hydra:totalItems"

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
        print("0. Quitter")
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
            case "0":
                break
            case _:
                print(INVALID)
                print("-----------------------------")


# Créer ticket
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
    date_ouverture = time.strftime("%Y-%m-%dT%H:%M:%S.000Z", time.localtime())
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
        print("Catégorie du ticket :")
        print("1. Technique")
        print("2. Fonctionnel")
        print("3. Demande")
        print("4. Incident")
        print("5. Autre")
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
        print("Erreur lors de la création du ticket. Merci de bien vouloir réessayer.")


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
        print("Date d'ouverture :", ticket["dateOuverture"])
        print("Date de fermeture :", ticket["dateFermeture"])
        print("Demandeur :", ticket["demandeur"])
        print("Dernière mise à jour :", ticket["lastUpdateDate"])
        print("Catégorie :", ticket["category"])
        print("Type :", ticket["type"])
        print("Statut :", ticket["status"])
        print("Priorité :", ticket["priority"])
        print("Urgence :", ticket["urgence"])
        print("Resolveur :", ticket["resolveur"])
        print("-----------------------------")
    elif response.status_code == 404:
        print("Ticket non trouvé. Merci de vérifier l'ID.")
# Function to list tickets


def list_tickets(data, page="/api/tickets"):
    url = data[0]
    token = data[1]
    response = requests.get(url+page,
                            headers={"Authorization": f"Bearer {token}"})
    if response.status_code == 200:
        print("Liste des tickets :")
        tickets = response.json()
        print("-----------------------------")
        print(tickets[ITEMS], "tickets trouvés")
        print("-----------------------------")
        if tickets[ITEMS] == 0:
            return
        ticketsl = tickets[MEMBER]
        for ticket in ticketsl:
            print("ID :", ticket["@id"].replace("/api/tickets/", ""))
            print("Titre :", ticket["titre"])
            print("Contenu :", ticket["description"])
            print("-----------------------------")
        if tickets[VIEW].get(NEXT) is not None:
            print("1. Voir la page suivante")
        if tickets[VIEW].get(PREVIOUS) is not None:
            print("2. Voir la page précédente")
        print("3. Voir un ticket")
        print("4. Retour")
        inp = input(CHOICE)
        if inp == "1":
            if tickets[VIEW].get(NEXT) is not None:
                list_tickets(data, tickets[VIEW][NEXT])
            else:
                print(INVALID)
        if inp == "2":
            if tickets[VIEW].get(PREVIOUS) is not None:
                list_tickets(data, tickets[VIEW][PREVIOUS])
            else:
                print(INVALID)
        if inp == "3":
                see_ticket(data)
        else:
            return

# Function to modify a ticket

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
        if date_ouverture != "" or date_fermeture != "" or demandeur != "" or last_update_date != "" or category != "" or typeid != "" or status != "" or priority != "" or titre != "" or description != "" or urgence != "" or resolveur != "":
            print("14. Confirmer")
        print("0. Annuler")
        print("-----------------------------")
        choice = input(CHOICE)
        match choice:
            case "1":
                print("Nouvelle date d'ouverture : ")
                date_ouverture = create_date()
                if date_ouverture == None:
                    break
            case "2":
                print("Nouvelle date de fermeture : ")
                date_fermeture = create_date()
                if date_fermeture == None:
                    break
            case "3":
                print("Nouveau demandeur : ")
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
            case "4":
                print("Nouvelle date de dernière mise à jour : ")
                last_update_date = create_date()
                if last_update_date == None:
                    break
            case "5":
                print("Nouvelle catégorie : ")
                ok = False
                while not ok:
                    print("Catégorie du ticket :")
                    print("1. Technique")
                    print("2. Fonctionnel")
                    print("3. Demande")
                    print("4. Incident")
                    print("5. Autre")
                    category = input(CHOICE)
                    match category:
                        case "0":
                            return
                        case "1":
                            ok = True
                        case _:
                            print(INVALID)
            case "6":
                print("Nouveau type : ")
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
            case "7":
                print("Nouveau statut : ")
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
            case "8":
                print("Nouvelle priorité : ")
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
            case "9":
                titre = input("Nouveau titre : ")
            case "10":
                description = input("Nouveau contenu : ")
            case "12":
                print("Nouvelle urgence : ")
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
            case "13":
                resolveur = input("Nouveau resolveur : ")
            case "14":
                if date_ouverture == "" and date_fermeture == "" and demandeur == "" and last_update_date == "" and category == "" and typeid == "" and status == "" and priority == "" and titre == "" and description == "" and urgence == "" and resolveur == "":
                    print("Option invalide")
                    continue
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
                if urgence != "":
                    print("urgence :", urgence)
                if resolveur != "":
                    print("resolveur :", resolveur)
                confirmer = input("Confirmer la modification (y/n) : ")
                if confirmer == "y":
                    json = create_json(date_ouverture, date_fermeture, demandeur, last_update_date,
                                       category, typeid, status, priority, titre, description, urgence, resolveur)
                    response = requests.put(
                        f"{url}/api/tickets/{ticket_id}", json=json, headers={"Authorization": f"Bearer {token}"})
                    if response.status_code == 200:
                        print("Ticket modifié avec succès !")
                    elif response.status_code == 404:
                        print("Ticket non trouvé. Merci de vérifier l'ID.")
                    elif response.status_code == 400:
                        print("Erreur lors de la modification du ticket. Merci de bien vouloir réessayer.")
                    elif response.status_code == 422:
                        print("Le ticket n'a pas pu être modifié. Veuillez vérifier les données saisies.")
                    else:
                        print(response.json())
                    break
                else:
                    print("Modification annulée")
                    break
            case "0":
                break
            case _:
                print(INVALID)
                print("-----------------------------")


def create_json(date_ouverture, date_fermeture, demandeur, last_update_date, category, typeid, status, priority, titre, description, urgence, resolveur):
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
    if urgence != "":
        json["urgence"] = urgence
    if resolveur != "":
        json["resolveur"] = resolveur
    return json

def create_date():
    year = ask_for_year()
    if year == "0":
        return None
    month = ask_for_month()
    if month == "0":
        return None
    day = ask_for_day(month, year)
    if day == "0":
        return None
    hour = ask_for_hour()
    if hour == "0":
        return None
    minute = ask_for_minute()
    if minute == "0":
        return None
    return year + "-" + month + "-" + day + "T" + hour + ":" + minute + ":00.000Z"
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
    elif response.status_code == 404:
        print("L'utilisateur avec cet ID n'existe pas.")

# Liste de tous les utilisateurs


def list_users(data, page="/api/users"):
    url = data[0]
    token = data[1]
    response = requests.get(url+page,
                            headers={"Authorization": f"Bearer {token}"})
    if response.status_code == 200:
        print("Liste des utilisateurs :")
        resp = response.json()
        print("-----------------------------")
        print(resp[ITEMS], "utilisateurs trouvés")
        print("-----------------------------")
        usersl = resp[MEMBER]
        for user in usersl:
            print("Id :", user["@id"].replace("/api/users/", ""))
            print("Nom :", user["nom"])
            print("Prenom :", user["prenom"])
            print("-----------------------------")
        if resp[VIEW].get(NEXT) is not None:
            print("1. Voir la page suivante")
        if resp[VIEW].get(PREVIOUS) is not None:
            print("2. Voir la page précédente")
        print("3. Voir un utilisateur")
        print("4. Retour")
        inp = input(CHOICE)
        if inp == "1":
            if resp[VIEW].get(NEXT) is not None:
                list_users(data, resp[VIEW][NEXT])
            else:
                print(INVALID)
        if inp == "2":
            if resp[VIEW].get(PREVIOUS) is not None:
                list_users(data, resp[VIEW][PREVIOUS])
            else:
                print(INVALID)
        elif inp == "3":
            see_users(data)
        elif inp == "4":
            return
        else:
            print(INVALID)
            print("-----------------------------")

def ask_for_year():
    year = ""
    good = False
    while not good:
        year = input("\nEntrez l'année de réservation (yyyy): (entrez 0 pour annuler)")
        if year == "0":
            return "0"
        if len(year) == 4:
            good = True
        else:
            print("\nL'année doit être au format yyyy")
    return year

def ask_for_month():
    month= ""
    good= False
    while not good:
        month = input("\nEntrez le mois de réservation (mm):")
        if month == "0":
            return "0"
        int_month= int(month)
        if 1 <= int_month and int_month <= 12:
            good = True
            if len(month) == 1:
                month = "0" + month
        else:
            print("\nLe mois doit être compris entre 1 et 12")
    return month


def ask_for_day(month:str, year:str):
    day= ""
    good= False
    while not good:
        day = input("\nEntrez le jour de réservation (dd): (entrez 0 pour annuler)")
        if day == "0":
            return "0"
        int_day = int(day)
        int_month = int(month)
        int_year = int(year)
        match int_month:
            case 1| 3| 5| 7| 8| 10| 12:
                if int_day >= 1 and int_day <= 31:
                    good = True
                else:
                    print("\nLe jour doit être compris entre 1 et 31")
            case 4| 6| 9| 11:
                if int_day >= 1 and int_day <= 30:
                    good = True
                else:
                    print("\nLe jour doit être compris entre 1 et 30")
            case 2:
                if int_year%4 == 0 and (int_year%100 != 0 or int_year%400 == 0):
                    if int_day >= 1 and int_day <= 29:
                        good = True
                    else:
                        print("\nLe jour doit être compris entre 1 et 29")
                else:
                    if int_day >= 1 and int_day <= 28:
                        good = True
                    else:
                        print("\nLe jour doit être compris entre 1 et 28")
        if len(day) == 1:
            day = "0" + day
    return day

def ask_for_hour():
    hour= ""
    good= False
    while not good:
        hour = input("\nEntrez l'heure de réservation (hh): (entrez 0 pour annuler)")
        if hour == "0":
            return "0"
        int_hour = int(hour)
        if int_hour >= 0 and int_hour <= 23:
            good = True
            if len(hour) == 1:
                hour = "0" + hour
        else:
            print("\nL'heure doit être comprise entre 0 et 23")
    return hour

def ask_for_minute():
    minute= ""
    good = False
    while not good:
        minute = input("\nEntrez les minutes de réservation (mm): (entrez 0 pour annuler)")
        if minute == "0":
            return "0"
        int_minute = int(minute)
        if int_minute >= 0 and int_minute <= 59:
            good = True
            if len(minute) == 1:
                minute = "0" + minute
        else:
            print("Les minutes doivent être comprises entre 0 et 59")
    return minute


# Main execution
if __name__ == '__main__':
    token = authenticate()
    main_menu(token)
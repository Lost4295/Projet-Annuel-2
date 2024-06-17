import PySimpleGUI as sg
import requests
import dotenv
import os
import time

dotenv.load_dotenv()

EMAIL = os.getenv("EMAIL")
PASSWORD = os.getenv("PASSWORD")
URL = os.getenv("URL")
IP = os.getenv("IP")

CHOICE = "Votre choix : "
INVALID = "Choix invalide"
MEMBER = "hydra:member"
VIEW = "hydra:view"
NEXT = "hydra:next"
URI = "/users/"
DESC = 'hydra:description'


def authenticate():
    def try_auth(url):
        response = requests.post(url + "/auth", json={"email": EMAIL, "password": PASSWORD})
        if response.status_code == 200:
            return [url, response.json()["token"]]
        return None

    auth_data = try_auth(URL)
    if not auth_data:
        print("Error while trying to authenticate with URL. Trying with IP address...")
        auth_data = try_auth(IP)
        if not auth_data:
            print("Error while trying to authenticate")
            exit()
    return auth_data


def request_ticket(method, data, id=None, json=None):
    url, token = data
    final_url = f"{url}/tickets"
    if id:
        final_url += f"/{id}"

    headers = {"Authorization": f"Bearer {token}"}
    if json:
        headers["Content-Type"] = "application/ld+json"

    response = requests.request(method, final_url, headers=headers, json=json)
    return response


def request_users(method, data, id=None):
    url, token = data
    final_url = f"{url}/users"
    if id:
        final_url += f"/{id}"

    headers = {"Authorization": f"Bearer {token}"}
    response = requests.request(method, final_url, headers=headers)
    return response


def list_tickets(response):
    def fetch_tickets(tickets):
        members = tickets[MEMBER]
        while tickets.get(VIEW) and tickets[VIEW].get(NEXT):
            next_url = URL + tickets[VIEW][NEXT]
            tickets = requests.get(next_url, headers={"Authorization": f"Bearer {token}"}).json()
            members += tickets[MEMBER]
        return members

    try:
        response = response['list_tickets']
    except KeyError:
        response = response.get('delete_ticket_choose') or response.get('update_ticket_choose')
        if not response:
            sg.popup(f"Erreur lors de la récupération des tickets : {response.json()[DESC]}", icon="ERROR")
            return None

    if response.status_code == 200:
        tickets = response.json()
        members = fetch_tickets(tickets)
        num = tickets["hydra:totalItems"]
        ticketlist = [members[i:i+3] for i in range(0, len(members), 3)]
        return [num, ticketlist]
    else:
        sg.popup(f"Erreur lors de la récupération des tickets : {response.json()[DESC]}", icon="ERROR")
        return None


def see_ticket(response):
    response = response.get('see_ticket') or response.get('modify_t')
    if response:
        if response.status_code == 200:
            return response.json()
        else:
            sg.popup(f"Erreur lors de la récupération du ticket : {response.json()[DESC]}", icon="ERROR")
    return None


def list_users(response):
    response = response['list_users']
    if response.status_code == 200:
        users = response.json()
        members = users[MEMBER]
        while users.get(VIEW) and users[VIEW].get(NEXT):
            next_url = URL + users[VIEW][NEXT]
            users = requests.get(next_url, headers={"Authorization": f"Bearer {token}"}).json()
            members += users[MEMBER]
        num = users["hydra:totalItems"]
        tabuser = [{"@id": user["@id"].replace(URI, ""), "nom": user["nom"], "prenom": user["prenom"]} for user in members]
        tabuser = [tabuser[i:i+3] for i in range(0, len(tabuser), 3)]
        return [num, tabuser]
    return None


def see_user(response):
    response = response['see_user']
    if response.status_code == 200:
        return response.json()
    return None


def get_user(token, id):
    response = requests.get(f"{token[0]}{URI}{id}", headers={"Authorization": f"Bearer {token[1]}"})
    if response.status_code == 200:
        return response.json()
    else:
        sg.popup(f"Erreur lors de la récupération de l'utilisateur : {response.json()[DESC]}", icon="ERROR")


def create_data(values):
    data = {
        "titre": values["clc 0 1"],
        "description": values["clc 1 1"],
        "demandeur": URI + values["clc 2 1"],
        "dateOuverture": time.strftime("%Y-%m-%d %H:%M:%S")
    }
    if values["clc 3 1"]:
        data["resolveur"] = URI + values["clc 3 1"]
    if values["clc 4 1"]:
        data["dateFermeture"] = values["clc 4 1"]

    categories = {"Technique": 1, "Fonctionnel": 2, "Demande": 3, "Incident": 4, "Autre": 5}
    types = {"Epic": 1, "Task": 2, "Story": 3, "Bug": 4, "Subtask": 5}
    statuses = {"Nouveau": 1, "En cours": 2, "Résolu": 3, "Fermé": 4, "En attente": 5, "Rejeté": 6}
    priorities = {"Bas": 1, "Normal": 2, "Haut": 3, "Urgent": 4}
    urgencies = {"Faible": 1, "Moyenne": 2, "Haute": 3, "Critique": 4}

    data["category"] = categories.get(values["clc2 0 1"])
    data["type"] = types.get(values["clc2 1 1"])
    data["status"] = statuses.get(values["clc2 2 1"])
    data["priority"] = priorities.get(values["clc2 3 1"])
    data["urgence"] = urgencies.get(values["clc2 4 1"])

    return data


def modify_data(values):
    data = create_data(values)
    return data


def delete_ticket(response):
    response = response['del_t']
    if response.status_code == 204:
        sg.popup("Ticket supprimé avec succès !", icon="INFO")
    else:
        sg.popup(f"Erreur lors de la suppression du ticket : {response.json()[DESC]}", icon="ERROR")


def create_ticket(token, data):
    response = requests.post(f"{token[0]}/tickets",
                             headers={"Authorization": f"Bearer {token[1]}", "Content-Type": "application/ld+json"},
                             json=data)
    if response.status_code == 201:
        sg.popup("Ticket créé avec succès !", icon="INFO")
    else:
        sg.popup(f"Erreur lors de la création du ticket : {response.json()[DESC]}", icon="ERROR")


def update_ticket(response):
    response = response.get('modify_t')
    if response:
        if response.status_code == 200:
            sg.popup("Ticket modifié avec succès !", icon="INFO")
        elif response.status_code == 400:
            sg.popup("Erreur lors de la modification du ticket. Merci de bien vouloir réessayer.", icon="ERROR")
        elif response.status_code == 422:
            sg.popup("Le ticket n'a pas pu être modifié. Veuillez vérifier les données saisies.", icon="ERROR")
        else:
            sg.popup(f"Erreur lors de la modification du ticket : {response.json()[DESC]}", icon="ERROR")
    else:
        sg.popup(f"Erreur lors de la récupération du ticket pour modification : {response.json()[DESC]}", icon="ERROR")


def return_to_main(cpt):
    window[f'-COL{cpt}-'].update(visible=False)
    cpt = 1
    window[f'-COL{cpt}-'].update(visible=True)
    return cpt


# Layout definitions
Menu = sg.Menu
main_menu = [
    [sg.T("Bienvenue sur l'application de ticketing de PCS !", font='_ 14', justification='c', expand_x=True)],
    [sg.Button("Lister les tickets", key="req::list_tickets")],
    [sg.Button("Créer un ticket", key="create_ticket")],
    [sg.Button("Modifier un ticket", key="req::update_ticket_choose")],
    [sg.Button("Supprimer un ticket", key="req::delete_ticket_choose")],
    [sg.Button("Lister les utilisateurs", key="req::list_users")],
    [sg.Button("Quitter", key="exit")],
]

layout = [
    [sg.Column(main_menu, key='-COL1-'),
     sg.Column([], visible=False, key='-COL2-'),
     sg.Column([], visible=False, key='-COL3-'),
     sg.Column([], visible=False, key='-COL4-'),
     sg.Column([], visible=False, key='-COL5-'),
     sg.Column([], visible=False, key='-COL6-'),
     sg.Column([], visible=False, key='-COL7-'),
     sg.Column([], visible=False, key='-COL8-'),
     sg.Column([], visible=False, key='-COL9-'),
     sg.Column([], visible=False, key='-COL10-'),
     sg.Column([], visible=False, key='-COL11-')]
]

cpt = 1
data = authenticate()
window = sg.Window("Application de ticketing", layout)

while True:
    event, values = window.read()

    if event == "exit" or event == sg.WIN_CLOSED:
        break

    response = None
    try:
        if event.startswith("req"):
            method, resource, action = event.split("::")
            if action == "list_tickets":
                response = request_ticket("GET", data)
                tickets = list_tickets(response)
                if tickets:
                    sg.popup(f"{tickets[0]} tickets trouvés.", icon="INFO")
            elif action == "delete_ticket_choose":
                response = request_ticket("GET", data)
                tickets = list_tickets(response)
                if tickets:
                    sg.popup("Sélectionnez un ticket à supprimer.", icon="INFO")
            elif action == "update_ticket_choose":
                response = request_ticket("GET", data)
                tickets = list_tickets(response)
                if tickets:
                    sg.popup("Sélectionnez un ticket à modifier.", icon="INFO")
            elif action == "list_users":
                response = request_users("GET", data)
                users = list_users(response)
                if users:
                    sg.popup(f"{users[0]} utilisateurs trouvés.", icon="INFO")
        elif event == "create_ticket":
            cpt = 3
            window[f'-COL{cpt}-'].update(visible=True)
        elif event == "create_t":
            cpt = return_to_main(cpt)
            create_ticket(data, create_data(values))
        elif event == "modify_ticket":
            cpt = 4
            window[f'-COL{cpt}-'].update(visible=True)
        elif event == "modify_t":
            cpt = return_to_main(cpt)
            update_ticket(request_ticket("PUT", data, values["mt0 0 1"], modify_data(values)))
        elif event == "delete_t":
            delete_ticket(request_ticket("DELETE", data, values["dt 0 1"]))
    except ValueError:
        sg.popup(f"Erreur : format de l'événement inattendu '{event}'", icon="ERROR")
    except Exception as e:
        sg.popup(f"An error occurred: {e}", icon="ERROR")

window.close()

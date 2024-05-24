import PySimpleGUI as sg
import requests
import dotenv
import os
import time


dotenv.load_dotenv()
EMAIL = os.getenv("EMAIL")
PASSWORD = os.getenv("PASSWORD")
CHOICE = "Votre choix : "
INVALID = "Choix invalide"
MEMBER = "hydra:member"
VIEW = "hydra:view"
NEXT = "hydra:next"
URI = "/api/users/"
DESC = 'hydra:description'


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


def list_tickets(data):
    url = data[0]
    token = data[1]
    response = requests.get(url+"/api/tickets",
                            headers={"Authorization": f"Bearer {token}"})
    if response.status_code == 200:
        tickets = response.json()
        members = tickets[MEMBER]
        while tickets.get(VIEW) is not None and tickets[VIEW].get(NEXT) is not None:
            response = requests.get(url+tickets[VIEW][NEXT],
                                    headers={"Authorization": f"Bearer {token}"})
            tickets = response.json()
            members += tickets[MEMBER]
        num = tickets["hydra:totalItems"]
        ticketlist = []
        for ticket in members:
            ticketlist.append(ticket)
        ticketlist = [ticketlist[i:i+3] for i in range(0, len(ticketlist), 3)]

    return [num, ticketlist]


def see_ticket(data, ticket_id):
    url = data[0]
    token = data[1]
    response = requests.get(f"{url}/api/tickets/{ticket_id}",
                            headers={"Authorization": f"Bearer {token}"})
    if response.status_code == 200:
        ticket = response.json()
        return ticket
    else:
        sg.popup("Erreur lors de la récupération du ticket :"+ response.json()[DESC], icon="ERROR")
        return None


def list_users(data):
    url = data[0]
    token = data[1]
    response = requests.get(url+"/api/users",
                            headers={"Authorization": f"Bearer {token}"})
    if response.status_code == 200:
        users = response.json()
        members = users[MEMBER]
        while users.get(VIEW) is not None and users[VIEW].get(NEXT) is not None:
            response = requests.get(url+users[VIEW][NEXT],
                                    headers={"Authorization": f"Bearer {token}"})
            users = response.json()
            members += users[MEMBER]
        num = users["hydra:totalItems"]
        tabuser = []
        for user in members:
            us = {}
            us["@id"] = user["@id"].replace(URI, "")
            us["nom"] = user["nom"]
            us["prenom"] = user["prenom"]
            tabuser.append(us)

        tabuser = [tabuser[i:i+3] for i in range(0, len(tabuser), 3)]

    return [num, tabuser]


def see_user(data, user_id):
    url = data[0]
    token = data[1]
    response = requests.get(f"{url}/api/users/{user_id}",
                            headers={"Authorization": f"Bearer {token}"})
    if response.status_code == 200:
        user = response.json()
    return user

def create_data(values):
    data = {}
    data["titre"] = values["clc 0 1"]
    data["description"] = values["clc 1 1"]
    data["demandeur"] = URI+ values["clc 2 1"]
    if values["clc 3 1"] != "":
        data["resolveur"] = URI+ values["clc 3 1"]
    if values["clc 4 1"] != "":
        data["dateFermeture"] = values["clc 4 1"]
    match values["clc2 0 1"]:
        case "Technique":
            data["category"] = 1
        case "Fonctionnel":
            data["category"] = 2
        case "Demande":
            data["category"] = 3
        case "Incident":
            data["category"] = 4
        case "Autre":
            data["category"] = 5
    match values["clc2 1 1"]: 
        case "Epic": 
            data["type"] = 1 
        case "Task":
            data["type"] = 2
        case "Story":
            data["type"] = 3
        case "Bug":
            data["type"] = 4
        case "Subtask":
            data["type"] = 5

    match values["clc2 2 1"]:
        case "Nouveau":
            data["status"] = 1
        case "En cours":
            data["status"] = 2
        case "Résolu":
            data["status"] = 3
        case "Fermé":
            data["status"] = 4
        case "En attente":
            data["status"] = 5
        case "Rejeté":
            data["status"] = 6
    match values["clc2 3 1"]:
        case "Bas":
            data["priority"] = 1
        case "Normal":
            data["priority"] = 2
        case "Haut":
            data["priority"] = 3
        case "Urgent":
            data["priority"] = 4
    match values["clc2 4 1"]:
        case "Faible":
            data["urgence"] = 1
        case "Moyenne":
            data["urgence"] = 2
        case "Haute":
            data["urgence"] = 3
        case "Critique":
            data["urgence"] = 4
    data["dateOuverture"] = time.strftime("%Y-%m-%d %H:%M:%S")
    return data
def delete_ticket(data, ticket_id):
    url = data[0]
    token = data[1]
    response = requests.delete(
        f"{url}/api/tickets/{ticket_id}", headers={"Authorization": f"Bearer {token}"})
    if response.status_code == 204:
        sg.popup("Ticket supprimé avec succès !", icon="INFO")
    else:
        sg.popup("Erreur lors de la suppression du ticket :"+ response.json()[DESC], icon="ERROR")
def create_ticket(token, data):
    response = requests.post(token[0]+"/api/tickets",
                             headers={"Authorization": f"Bearer {token[1]}", "Content-Type": "application/ld+json"},
                             json=data)
    if response.status_code == 201:
        sg.popup("Ticket créé avec succès !", icon="INFO")
    else:
        sg.popup("Erreur lors de la création du ticket :"+ response.json()[DESC], icon="ERROR")

def update_ticket(token, ticket_id, data):
    response = requests.put(token[0]+f"/api/tickets/{ticket_id}",
                             headers={"Authorization": f"Bearer {token[1]}", "Content-Type": "application/ld+json"},
                             json=data)
    if response.status_code == 200:
        sg.popup("Ticket modifié avec succès !", icon="INFO")
    elif response.status_code == 400:
        sg.popup("Erreur lors de la modification du ticket. Merci de bien vouloir réessayer.", icon="ERROR")
    elif response.status_code == 422:
        sg.popup("Le ticket n'a pas pu être modifié. Veuillez vérifier les données saisies.", icon="ERROR")
    else:
        sg.popup("Erreur lors de la modification du ticket :"+ response.json()[DESC], icon="ERROR")

def return_to_main(cpt):
    window[f'-COL{cpt}-'].update(visible=False)
    cpt = 1
    window[f'-COL{cpt}-'].update(visible=True)
    
Menu = sg.Menu
main_menu = [
    # [Menu([['File', ['Exit']], ['Edit', ['Edit Me', ]]],  k='-CUST MENUBAR1-', p=0)]
    [sg.T("Bienvenue sur l'application de ticketing de PCS !",
          font='_ 14', justification='c', expand_x=True)],
    [sg.Button("Lister les tickets", key="list_tickets")],
    [sg.Button("Créer un ticket", key="create_ticket")],
    [sg.Button("Modifier un ticket", key="update_ticket_choose")],
    [sg.Button("Supprimer un ticket", key="delete_ticket_choose")],
    [sg.Button("Lister les utilisateurs", key="list_users")],
    [sg.Button("Quitter", key="exit")],
]

tickets_list = []
ticket = []
users_list = []
user = []
create_ticket_l = []
update_ticket_choose = []
update_ticket_l = []
delete_ticket_l = []

layout = [
    [
        sg.Column(main_menu, key='-COL1-'),
        sg.Column(tickets_list, visible=False, key='-COL3-'),
        sg.Column(ticket, visible=False, key='-COL4-'),
        sg.Column(users_list, visible=False, key='-COL5-'),
        sg.Column(user, visible=False, key='-COL6-'),
        sg.Column(create_ticket_l, visible=False, key='-COL7-'),
        sg.Column(update_ticket_l, visible=False, key='-COL8-'),
        sg.Column(update_ticket_choose, visible=False, key='-COL2-'),
        sg.Column(delete_ticket_l, visible=False, key='-COL9-'),
    ],
]
# Create the Window

window = sg.Window('Hello Example', layout, size=(800, 600), resizable=True)

# Event Loop to process "events" and get the "values" of the inputs
if __name__ == '__main__':
    token = authenticate()
    cpt = 1  # The currently visible layout
    clt = cli = cld = clu = cle = clc = clm = 0
    while True:
        event, values = window.read()

        # if user closes window or clicks cancel
        if event == sg.WIN_CLOSED or event == 'Cancel' or event == 'exit':
            break

        if "return" in event:
            return_to_main(cpt)
            continue
        if "_" in event:
            window[f'-COL{cpt}-'].update(visible=False)
            match event:
                case "list_tickets":
                    cpt = 3
                    tickets = list_tickets(token)
                    count = tickets[0]//3
                    if tickets[0] % 3 <= 3:
                        count+=1
                    if clt > 0:
                        for j in range(count):
                            window[f"clt {j} {clt}"].hide_row()
                        window[f'clt {clt}'].hide_row()
                        window[f'clt {clt} return'].hide_row()
                    clt += 1
                    tickets_list = [
                        [sg.Text(f"Liste des tickets: {tickets[0]} tickets", font='_ 14', justification='c', expand_x=True, key=f"clt {clt}")],
                    ]
                    for j in range(count):
                        tickets_list.append([sg.Column([[sg.Button(f"Ticket {i['@id'].replace('/api/tickets/', '')} : {i['titre']}",
                                            key=f"see_ticket:{i['@id'].replace('/api/tickets/', '')}") for i in tickets[1][j]]], key=f"clt {j} {clt}")])
                    tickets_list.append(
                        [sg.Button("Retour", key=f"clt {clt} return")])
                    window.extend_layout(window[f'-COL{cpt}-'], tickets_list)
                case str(x) if "see_ticket" in x:
                    ticket = see_ticket(token, x.split(":")[1])
                    cpt = 4
                    if cli > 0:
                        for i in range(len(ticket)):
                            window[f'cli {cli}'].hide_row()
                        window[f'cli {cli} return'].hide_row()
                    cli += 1
                    fullname = see_user(token, ticket['demandeur'])['nom'] + " " + see_user(token, ticket['demandeur'])['prenom']
                    id = ticket['demandeur'].replace('/api/users/', '')
                    tickete = [
                        [sg.Text(f"Ticket {ticket['@id'].replace('/api/tickets/', '')} : {ticket['titre']}", font='_ 14', justification='c', expand_x=True, key=f"cli {cli}")],
                        [sg.Text(f"Titre : {ticket['titre']}", key=f"cli {cli}")],
                        [sg.Text(
                            f"Contenu : {ticket['description']}", key=f"cli {cli}")],
                        [sg.Text(
                            f"Date d'ouverture : {ticket['dateOuverture']}", key=f"cli {cli}")],
                        [sg.Text(
                            f"Date de fermeture : {ticket['dateFermeture']}", key=f"cli {cli}")],
                        [sg.Text(
                            f"Demandeur : {fullname} ( ID : {id})", key=f"cli {cli}")],
                        [sg.Text(
                            f"Dernière mise à jour : {ticket['lastUpdateDate']}", key=f"cli {cli}")],
                        [sg.Text(
                            f"Catégorie : {ticket['category']}", key=f"cli {cli}")],
                        [sg.Text(f"Type : {ticket['type']}", key=f"cli {cli}")],
                        [sg.Text(
                            f"Statut : {ticket['status']}", key=f"cli {cli}")],
                        [sg.Text(
                            f"Priorité : {ticket['priority']}", key=f"cli {cli}")],
                        [sg.Text(
                            f"Urgence : {ticket['urgence']}", key=f"cli {cli}")],
                        [sg.Text(
                            f"Resolveur : {ticket['resolveur']}", key=f"cli {cli}")],
                        [sg.Column([[
                                sg.Button("Modifier",  key=f"modify_t:{ticket['@id'].replace('/api/tickets/', '')}"),
                                sg.Button("Supprimer", key=f"del_t:{ticket['@id'].replace('/api/tickets/', '')}")]], key=f"cli {cli}")
                        ],
                        [sg.Button("Retour", key=f"cli {cli} return")],
                    ]
                    window.extend_layout(window[f'-COL{cpt}-'], tickete)
                case "list_users":
                    users = list_users(token)
                    cpt = 5
                    count = users[0]//3
                    if users[0] % 3 <= 3:
                        count+=1
                    if clu > 0:
                        for j in range(count):
                            window[f"clu {j} {clu}"].hide_row()
                        window[f'clu {clu}'].hide_row()
                        window[f'clu {clu} return'].hide_row()
                    clu += 1
                    userse = [
                        [sg.Text(f"Liste des utilisateurs : {users[0]} utilisateurs",
                                 font='_ 14', justification='c', expand_x=True, key=f"clu {clu}")],
                    ]
                    for j in range(count):
                        userse.append([sg.Column([[sg.Button(f"Utilisateur {i['@id'].replace('/api/users/', '')} : {i['nom']} {i['prenom']}",
                                      key=f"see_user:{i['@id'].replace('/api/users/', '')}") for i in users[1][j]]], key=f"clu {j} {clu}")])
                    userse.append(
                        [sg.Button("Retour", key=f"clu {clu} return")])
                    window.extend_layout(window[f'-COL{cpt}-'], userse)
                case str(x) if "see_user" in x:
                    suser = see_user(token, x.split(":")[1])
                    cpt = 6
                    if cli > 0:
                        for i in range(len(suser)):
                            window[f'cli {cli}'].hide_row()
                        window[f'cli {cli} return'].hide_row()
                    cli += 1
                    usere = [
                        [sg.Text(f"Utilisateur {suser['@id'].replace('/api/users/', '')} : {suser['nom']} {suser['prenom']}",
                                 font='_ 14', justification='c', expand_x=True, key=f"cli {cli}")],
                        [sg.Text(f"Nom : {suser['nom']}", key=f"cli {cli}")],
                        [sg.Text(
                            f"Prénom : {suser['prenom']}", key=f"cli {cli}")],
                        [sg.Text(
                            f"Id : {suser['@id'].replace('/api/users/', '')}", key=f"cli {cli}")],
                        [sg.Button("Retour", key=f"cli {cli} return")],
                    ]
                    window.extend_layout(window[f'-COL{cpt}-'], usere)
                case "create_ticket":
                    cpt = 7
                    txtrows = ["Titre du ticket", "Contenu du ticket", "ID du Demandeur"]
                    selectrows = ["Catégorie","Type", "Statut", "Priorité", "Urgence"]
                    if clc > 0:
                        count = len(txtrows)
                        count2 = len(selectrows)
                        for j in range(count):
                            window[f"clc {j} {clc}"].hide_row()
                        for j in range(count2):
                            window[f"clc2 {j} {clc}"].hide_row()
                        window[f'clc {clc} submit_ticket'].hide_row()
                        window[f'clc {clc}'].hide_row()
                        window[f'clc {clc} return'].hide_row()
                    clc += 1
                    data = [
                        ["Technique","Fonctionnel","Demande","Incident","Autre"],
                        ["Epic","Task","Story","Bug","Subtask",],
                        ["Nouveau","En cours","Résolu","Fermé","En attente","Rejeté",],
                        ["Bas","Normal","Haut","Urgent"],
                        ["Faible","Moyenne","Haute","Critique"]
                    ] 
                    create_ticket_l = [
                        [sg.Text("Créer un ticket", font='_ 14', justification='c', expand_x=True, key=f"clc {clc}")],
                    ]
                    for i in range(len(txtrows)):
                        create_ticket_l.append(
                            [sg.Text(txtrows[i]), sg.InputText(key=f"clc {i} {clc}", do_not_clear=False)])
                    for i in range(len(selectrows)):
                        create_ticket_l.append(
                            [sg.Text(selectrows[i]), sg.Combo(data[i], key=f"clc2 {i} {clc}", readonly=True)])
                    create_ticket_l.append(
                        [sg.Button("Créer", key=f"clc {clc} submit_ticket")])
                    create_ticket_l.append(
                        [sg.Button("Retour", key=f"clc {clc} return")])
                    window.extend_layout(window[f'-COL{cpt}-'], create_ticket_l)
                case str(x) if "submit_ticket" in x:
                    cpt = 7
                    create_ticket(token, create_data(values))
                    return_to_main(cpt)
                case str(x) if "modify_t" in x:
                    ticket = see_ticket(token, x.split(":")[1])
                    cpt = 2
                    if clm > 0:
                        for i in range(len(ticket)):
                            window[f'clm {clm}'].hide_row()
                        window[f'clm {clm} return'].hide_row()
                    clm += 1
                    modify_ticket = [
                        [sg.Text(f"Modifier le ticket {ticket['@id'].replace('/api/tickets/', '')} : {ticket['titre']}",
                                 font='_ 14', justification='c', expand_x=True, key=f"clm {clm}")],
                    ]
                    txtrows = ["Titre du ticket", "Contenu du ticket", "ID du Demandeur", "ID du Resolveur"]
                    dateRows = ["Date d'ouverture", "Date de fermeture", "Dernière mise à jour"]
                    selectrows = ["Catégorie","Type", "Statut", "Priorité", "Urgence"]
                    names = ["titre", "description", "demandeur", "resolveur", "dateOuverture", "dateFermeture", "lastUpdateDate"]
                    if clm > 0:
                        count = len(txtrows)
                        count2 = len(selectrows)
                        for j in range(count):
                            window[f"clm {j} {clm}"].hide_row()
                        for j in range(count2):
                            window[f"clc2 {j} {clm}"].hide_row()
                        window[f'clm {clm} submit_ticket'].hide_row()
                        window[f'clm {clm}'].hide_row()
                        window[f'clm {clm} return'].hide_row()
                    clm += 1
                    data = [
                        ["Technique","Fonctionnel","Demande","Incident","Autre"],
                        ["Epic","Task","Story","Bug","Subtask",],
                        ["Nouveau","En cours","Résolu","Fermé","En attente","Rejeté",],
                        ["Bas","Normal","Haut","Urgent"],
                        ["Faible","Moyenne","Haute","Critique"]
                    ] 
                    for i in range(len(txtrows)):
                        modify_ticket.append(
                            [sg.Text(txtrows[i]), sg.InputText(ticket[names[i]], key=f"clm {i} {clm}", do_not_clear=False)])
                    for i in range(len(dateRows)):
                        modify_ticket.append(
                            [sg.Text(dateRows[i]), sg.InputText(ticket[names[i+4]], key=f"clm {i} {clm}", do_not_clear=False)])
                    for i in range(len(selectrows)):
                        modify_ticket.append(
                            [sg.Text(selectrows[i]), sg.Combo(data[i], key=f"clc2 {i} {clm}", readonly=True)])
                    modify_ticket.append(
                        [sg.Button("Créer", key=f"clm {clm} mod_t")])
                    modify_ticket.append(
                        [sg.Button("Retour", key=f"clm {clm} return")])
                    modify_ticket.append(
                        [sg.Text("Tachez de vérifier que toutes les informations sont correctes avant de valider.", key=f"clm {clm}")])
                    window.extend_layout(window[f'-COL{cpt}-'], modify_ticket)
                case "update_ticket_choose":
                    cpt = 8
                    tickets = list_tickets(token)
                    count = tickets[0]//3
                    if count <= 3:
                        count = tickets[0] % 3
                    if cle > 0:
                        for j in range(count):
                            window[f"cle {j} {cle}"].hide_row()
                        window[f'cle {cle}'].hide_row()
                        window[f'cle {cle} return'].hide_row()
                    cle += 1
                    tickets_list = [
                        [sg.Text("Modifier un ticket", font='_ 14',
                                 justification='c', expand_x=True, key=f"cle {cle}")],
                    ]
                    for j in range(count):
                        tickets_list.append([sg.Column([[sg.Button(f"Ticket {i['@id'].replace('/api/tickets/', '')} : {i['titre']}",
                                            key=f"modify_t:{i['@id'].replace('/api/tickets/', '')}") for i in tickets[1][j]]], key=f"cle {j} {cle}")])
                    tickets_list.append(
                        [sg.Button("Retour", key=f"cle {cle} return")])
                    window.extend_layout(window[f'-COL{cpt}-'], tickets_list)
                case "delete_ticket":
                    cpt = 9
                    tickets = list_tickets(token)
                    count = tickets[0]//3
                    if count <= 3:
                        count = tickets[0] % 3
                    if cld > 0:
                        for j in range(count):
                            window[f"cld {j} {cld}"].hide_row()
                        window[f'cld {cld}'].hide_row()
                        window[f'cld {cld} return'].hide_row()
                    cld += 1
                    tickets_list = [
                        [sg.Text("Supprimer un ticket", font='_ 14',
                                 justification='c', expand_x=True, key=f"cld {cld}")],
                    ]
                    for j in range(count):
                        tickets_list.append([sg.Column([[sg.Button(f"Ticket {i['@id'].replace('/api/tickets/', '')} : {i['titre']}",
                                            key=f"del_t:{i['@id'].replace('/api/tickets/', '')}") for i in tickets[1][j]]], key=f"cld {j} {cld}")])
                    tickets_list.append(
                        [sg.Button("Retour", key=f"cld {cld} return")])
                    window.extend_layout(window[f'-COL{cpt}-'], tickets_list)
                case str(x) if "mod_t" in x:
                    update_ticket(token, x.split(":")[1], create_data(values))
                    return_to_main(cpt)
                case str(x) if "del_t" in x:
                    delete_ticket(token, x.split(":")[1])
                    return_to_main(cpt)

            window[f'-COL{cpt}-'].update(visible=True)

        print(event, f"cpt={cpt}", "modify_t" in event)

    window.close()

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
    response = requests.post(URL+"/auth",
                             json={"email": EMAIL, "password": PASSWORD})
    if response.status_code == 200:
        return [URL, response.json()["token"]]
    else:
        print("Error while trying to authenticate")
        print("Trying with IP address...")
        response = requests.post(IP+"/auth",
                                 json={"email": EMAIL, "password": PASSWORD})
        if response.status_code == 200:
            return [IP, response.json()["token"]]
        else:
            print("Error while trying to authenticate")
            exit()


def request_ticket(method, data, id, json=None):
    url = data[0]
    token = data[1]
    final_url = url+"/tickets"
    if id is not None:
        final_url += f"/{id}"
    match method:
        case "get":
            response = requests.get(
                final_url, headers={"Authorization": f"Bearer {token}"})
        case "delete":
            response = requests.delete(
                final_url, headers={"Authorization": f"Bearer {token}"})
    return response


def request_users(method, data, id):
    url = data[0]
    token = data[1]
    final_url = url+"/users"
    if id is not None:
        final_url += f"/{id}"
    match method:
        case "get":
            response = requests.get(
                final_url, headers={"Authorization": f"Bearer {token}"})
    return response


def list_tickets(response):
    try:
        response = response['list_tickets']
    except KeyError:
        try:
            response = response['delete_ticket_choose']
        except KeyError:
            try:
                response = response['update_ticket_choose']
            except KeyError:
                sg.popup("Erreur lors de la récupération des tickets :" +
                         response.json()[DESC], icon="ERROR")
                return None
    num = ticketlist = None
    if response.status_code == 200:
        tickets = response.json()
        members = tickets[MEMBER]
        while tickets.get(VIEW) is not None and tickets[VIEW].get(NEXT) is not None:
            response = requests.get(URL+tickets[VIEW][NEXT],
                                    headers={"Authorization": f"Bearer {token}"})
            tickets = response.json()
            members += tickets[MEMBER]
        num = tickets["hydra:totalItems"]
        ticketlist = []
        for ticket in members:
            ticketlist.append(ticket)
        ticketlist = [ticketlist[i:i+3] for i in range(0, len(ticketlist), 3)]
    else:
        sg.popup("Erreur lors de la récupération des tickets :" +
                 response.json()[DESC], icon="ERROR")
    return [num, ticketlist]

def see_ticket(response):
    try:
        response = response['see_ticket']
    except KeyError:
        try:
            response = response['modify_t']
        except KeyError:
            print(response)
            sg.popup("Erreur lors de la récupération du j :" +
                     response.json()[DESC], icon="ERROR")
            return None
    if response.status_code == 200:
        ticket = response.json()
        return ticket
    else:
        print(response.json())
        sg.popup("Erreur lors de la récupération du ticket :" +
                 response.json()[DESC], icon="ERROR")
        return None

def list_users(response):
    response = response['list_users']
    if response.status_code == 200:
        users = response.json()
        members = users[MEMBER]
        while users.get(VIEW) is not None and users[VIEW].get(NEXT) is not None:
            nae= token[1]
            response = requests.get(URL+users[VIEW][NEXT],
                                    headers={"Authorization": f"Bearer {nae}"})
            users = response.json()
            print(response.json())
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

def see_user(response):
    response = response['see_user']
    if response.status_code == 200:
        user = response.json()
    return user

def get_user(token, id):
    print(token[0], id)
    response = requests.get(token[0]+id,
                            headers={"Authorization": f"Bearer {token[1]}"})
    if response.status_code == 200:
        return response.json()
    else:
        print(response)
        sg.popup("Erreur lors de la récupération de l'utilisateur :" +
                 response.json()[DESC], icon="ERROR")

def modify_data(values):
    data = create_data(values)
    return data

def create_data(values):
    data = {}
    keys = ["titre", "description", "demandeur", "dateOuverture", "resolveur", "dateFermeture", "category", "type", "status", "priority", "urgence"]
    try:
        for key in keys:
            if key in values:
                if key == "demandeur" or key == "resolveur":
                    data[key] = URI + values[key].replace("/users/", "")
                elif key == "dateOuverture" or key == "lastUpdateDate":
                    data[key] = time.strftime("%Y-%m-%d %H:%M:%S")
                elif key == "category":
                    categories = {"Technique": 1, "Fonctionnel": 2, "Demande": 3, "Incident": 4, "Autre": 5}
                    data[key] = categories.get(values[key])
                elif key == "type":
                    types = {"Epic": 1, "Task": 2, "Story": 3, "Bug": 4, "Subtask": 5}
                    data[key] = types.get(values[key])
                elif key == "status":
                    statuses = {"Nouveau": 1, "En cours": 2, "Résolu": 3, "Fermé": 4, "En attente": 5, "Rejeté": 6}
                    data[key] = statuses.get(values[key])
                elif key == "priority":
                    priorities = {"Bas": 1, "Normal": 2, "Haut": 3, "Urgent": 4}
                    data[key] = priorities.get(values[key])
                elif key == "urgence":
                    urgencies = {"Faible": 1, "Moyenne": 2, "Haute": 3, "Critique": 4}
                    data[key] = urgencies.get(values[key])
                else:
                    data[key] = values[key]
    except KeyError:
        pass

    return data

def find_name(id, name):
    categories = {"Technique": 1, "Fonctionnel": 2, "Demande": 3, "Incident": 4, "Autre": 5}
    types = {"Epic": 1, "Task": 2, "Story": 3, "Bug": 4, "Subtask": 5}
    statuses = {"Nouveau": 1, "En cours": 2, "Résolu": 3, "Fermé": 4, "En attente": 5, "Rejeté": 6}
    priorities = {"Bas": 1, "Normal": 2, "Haut": 3, "Urgent": 4}
    urgencies = {"Faible": 1, "Moyenne": 2, "Haute": 3, "Critique": 4}
    match name:
        case "category":
            return list(categories.keys())[list(categories.values()).index(id)]
        case "type":
            return list(types.keys())[list(types.values()).index(id)]
        case "status":
            return list(statuses.keys())[list(statuses.values()).index(id)]
        case "priority":
            return list(priorities.keys())[list(priorities.values()).index(id)]
        case "urgence":
            return list(urgencies.keys())[list(urgencies.values()).index(id)]

def delete_ticket(response):
    response = response['del_t']
    if response.status_code == 204:
        sg.popup("Ticket supprimé avec succès !", icon="INFO")
    else:
        sg.popup("Erreur lors de la suppression du ticket :" +
                 response.json()[DESC], icon="ERROR")

def create_ticket(token, data):
    response = requests.post(token[0]+"/tickets",
                             headers={
                                 "Authorization": f"Bearer {token[1]}", "Content-Type": "application/ld+json"},
                             json=data)
    if response.status_code == 201:
        sg.popup("Ticket créé avec succès !", icon="INFO")
    else:
        sg.popup("Erreur lors de la création du ticket :" +
                 response.json()[DESC], icon="ERROR")


def update_ticket(token,data,tid):
    response = requests.put(
                token[0]+"/tickets/"+tid, headers={"Authorization": f"Bearer {token[1]}", "Content-Type":"application/ld+json"},json=data)
    if response.status_code == 200:
        sg.popup("Ticket modifié avec succès !", icon="INFO")
    elif response.status_code == 400:
        sg.popup(
            "Erreur lors de la modification du ticket. Merci de bien vouloir réessayer. Erreur : "+response.json()[DESC], icon="ERROR")
    elif response.status_code == 422:
        sg.popup(
            "Le ticket n'a pas pu être modifié. Veuillez vérifier les données saisies.", icon="ERROR")
    else:
        sg.popup("Erreur lors de la modification du ticket :" +
                 response.json()[DESC], icon="ERROR")


def return_to_main(cpt):
    window[f'-COL{cpt}-'].update(visible=False)
    cpt = 1
    window[f'-COL{cpt}-'].update(visible=True)
    return cpt


Menu = sg.Menu
main_menu = [
    # [Menu([['File', ['Exit']], ['Edit', ['Edit Me', ]]],  k='-CUST MENUBAR1-', p=0)]
    [sg.T("Bienvenue sur l'application de ticketing de PCS !",
          font='_ 14', justification='c', expand_x=True)],
    [sg.Button("Lister les tickets", key="req:get::list_tickets")],
    [sg.Button("Créer un ticket", key="create_ticket")],
    [sg.Button("Modifier un ticket", key="req:get::update_ticket_choose")],
    [sg.Button("Supprimer un ticket", key="req:get::delete_ticket_choose")],
    [sg.Button("Lister les utilisateurs", key="raq::list_users")],
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

def view_creator(data):
    view = []
    for i in data:
        view.append(i)
    return view




window = sg.Window('Hello Example', layout, size=(800, 600), resizable=True)
if __name__ == '__main__':
    token = authenticate()
    cpt = 1 
    cla = cli = cld = clu = cle = clc = clm = clz =  0
    while True:
        event, values = window.read()
        # if user closes window or clicks cancel
        if event in (sg.WINDOW_CLOSE_ATTEMPTED_EVENT, 'exit', 'Cancel', sg.WIN_CLOSED, 'Exit'):
            window.timer_stop_all()
            sg.popup_animated(None)
            break

        if "return" in event:
            cpt = return_to_main(cpt)
            continue
        elif event == sg.TIMER_KEY:
            sg.popup_animated(sg.DEFAULT_BASE64_LOADING_GIF,
                              message='Loading', time_between_frames=100)
            window.force_focus()
            continue
        if 'req' in event:  # req:get:[id]:list_tickets
            window.timer_start(100)
            method = event.split(":")[1]
            id = event.split(":")[2] if len(event.split(":")) > 2 else None
            callback_key = event.split(":")[3] if len(
                event.split(":")) > 3 else None
            if callback_key is not None:
                callback_key= ''.join(i for i in callback_key if not i.isdigit())
            window.start_thread(lambda: request_ticket(
                method, token, id, values), callback_key)
            print(event, callback_key)
            continue
        elif 'raq' in event:
            window.timer_start(100)
            id = event.split(":")[1] if len(event.split(":")) > 1 else None
            callback_key = event.split(":")[2] if len(
                event.split(":")) > 2 else None
            if callback_key is not None:
                callback_key= ''.join(i for i in callback_key if not i.isdigit())
            window.start_thread(lambda: request_users(
                "get", token, id), callback_key)
            continue
        if "_" in event:
            window[f'-COL{cpt}-'].update(visible=False)
            match event:
                case "list_tickets":
                    sg.popup_animated(None)
                    window.timer_stop_all()
                    cpt = 3
                    tickets = list_tickets(values)
                    count = tickets[0]//3
                    if 0 < tickets[0] % 3 and tickets[0] % 3 < 3:
                        count += 1
                    for i in range(len(tickets_list)):
                        for j in range(len(tickets_list[i])):
                                tickets_list[i][j].hide_row()
                    tickets_list = [
                        [sg.Text(f"Liste des tickets: {tickets[0]} tickets", font='_ 14',
                                 justification='c', expand_x=True, key=f"cla {cla}")],
                    ]
                    for j in range(count):
                        tickets_list.append([sg.Column([[sg.Button(f"Ticket {i['@id'].replace('/tickets/', '')} : {i['titre']} ",
                                            key=f"req:get:{i['@id'].replace('/tickets/', '')}:see_ticket") for i in tickets[1][j]]], key=f"cla {j} {cla}")])
                    tickets_list.append(
                        [sg.Button("Retour", key=f"cla {cla} return")])
                    window.extend_layout(window[f'-COL{cpt}-'], tickets_list)
                case str(x) if "see_ticket" in x:
                    ticket = see_ticket(values)
                    fullname = get_user(token, ticket['demandeur'])['nom'] + " " + get_user(token, ticket['demandeur'])['prenom']
                    fullnamer = get_user(token, ticket['resolveur'])['nom'] + " " + get_user(token, ticket['resolveur'])['prenom'] if ticket['resolveur'] is not None else "Non attribué"
                    window.timer_stop_all()
                    sg.popup_animated(None)
                    cpt = 4
                    try:
                        for i in range(len(tickete)):
                            for j in range(len(tickete[i])):
                                    tickete[i][j].hide_row()
                    except NameError:
                        pass
                    id = ticket['demandeur'].replace('/users/', '')
                    idr = ticket['resolveur'].replace('/users/', '') if ticket ["resolveur"] is not None else "Non attribué"
                    tickete = [
                        [sg.Text(f"Ticket {ticket['@id'].replace('/tickets/', '')} : {ticket['titre']}",
                                 font='_ 14', justification='c', expand_x=True, key=f"cli {cli}")],
                        [sg.Text(f"Titre : {ticket['titre']}",
                                 key=f"cli {cli}")],
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
                            f"Catégorie : {find_name(ticket['category'], 'category')}", key=f"cli {cli}")],
                        [sg.Text(f"Type : {find_name(ticket['type'], 'type')}",
                                 key=f"cli {cli}")],
                        [sg.Text(
                            f"Statut : {find_name(ticket['status'], 'status')}", key=f"cli {cli}")],
                        [sg.Text(
                            f"Priorité : {find_name(ticket['priority'], 'priority')}", key=f"cli {cli}")],
                        [sg.Text(
                            f"Urgence : {find_name(ticket['urgence'], 'urgence')}", key=f"cli {cli}")],
                        [sg.Text(
                            f"Résolveur : {fullnamer} ( ID : {idr})", key=f"cli {cli}")],
                        [sg.Column([[
                            sg.Button(
                                "Modifier",  key=f"req:get:{ticket['@id'].replace('/tickets/', '')}:modify_t"),
                            sg.Button("Supprimer", key=f"del_t:{ticket['@id'].replace('/tickets/', '')}")]], key=f"cli {cli}")
                         ],
                        [sg.Button("Retour", key=f"cli {cli} return")],
                    ]
                    window.extend_layout(window[f'-COL{cpt}-'], tickete)
                case "list_users":
                    users = list_users(values)
                    sg.popup_animated(None)
                    window.timer_stop_all()
                    cpt = 5
                    count = users[0]//3
                    if 0 < users[0] % 3 and users[0] % 3 < 3:
                        count += 1
                    try:
                        for i in range(len(userse)):
                            for j in range(len(userse[i])):
                                    userse[i][j].hide_row()
                    except NameError:
                        pass
                    userse = [
                        [sg.Text(f"Liste des utilisateurs : {users[0]} utilisateurs",
                                 font='_ 14', justification='c', expand_x=True, key=f"clu {clu}")],
                    ]
                    for j in range(count):
                        userse.append([sg.Column([[sg.Button(f"Utilisateur {i['@id'].replace('/users/', '')} : {i['nom']} {i['prenom']}",
                                      key=f"raq:{i['@id'].replace('/users/', '')}:see_user") for i in users[1][j]]], key=f"clu {j} {clu}")])
                    userse.append(
                        [sg.Button("Retour", key=f"clu {clu} return")])
                    window.extend_layout(window[f'-COL{cpt}-'], userse)
                case str(x) if "see_user" in x:
                    sg.popup_animated(None)
                    window.timer_stop_all()
                    suser = see_user(values)
                    cpt = 6
                    try:
                        for i in range(len(usere)):
                            for j in range(len(usere[i])):
                                    usere[i][j].hide_row()
                    except NameError:
                        pass
                    usere = [
                        [sg.Text(f"Utilisateur {suser['@id'].replace('/users/', '')} : {suser['nom']} {suser['prenom']}",
                                 font='_ 14', justification='c', expand_x=True, key=f"clz {clz}")],
                        [sg.Text(f"Nom : {suser['nom']}", key=f"clz {clz}")],
                        [sg.Text(
                            f"Prénom : {suser['prenom']}", key=f"clz {clz}")],
                        [sg.Text(
                            f"Id : {suser['@id'].replace('/users/', '')}", key=f"clz {clz}")],
                        [sg.Button("Retour", key=f"clz {clz} return")],
                    ]
                    window.extend_layout(window[f'-COL{cpt}-'], usere)
                case "create_ticket":
                    cpt = 7
                    txtrows = ["Titre du ticket",
                               "Contenu du ticket", "ID du Demandeur"]
                    selectrows = ["Catégorie", "Type",
                                  "Statut", "Priorité", "Urgence"]
                    keyrows = ["titre", "description", "demandeur"]
                    keyselect = ["category", "type", "status", "priority", "urgence"]
                    try:
                        for i in range(len(create_ticket_l)):
                            for j in range(len(create_ticket_l[i])):
                                    create_ticket_l[i][j].hide_row()
                    except NameError:
                        pass
                    data = [
                        ["Technique", "Fonctionnel", "Demande", "Incident", "Autre"],
                        ["Epic", "Task", "Story", "Bug", "Subtask",],
                        ["Nouveau", "En cours", "Résolu",
                            "Fermé", "En attente", "Rejeté",],
                        ["Bas", "Normal", "Haut", "Urgent"],
                        ["Faible", "Moyenne", "Haute", "Critique"]
                    ]
                    create_ticket_l = [
                        [sg.Text("Créer un ticket", font='_ 14',
                                 justification='c', expand_x=True, key=f"clc {clc}")],
                    ]
                    for i in range(len(txtrows)):
                        create_ticket_l.append(
                            [sg.Text(txtrows[i]), sg.InputText(key=keyrows[i], do_not_clear=False)])
                    for i in range(len(selectrows)):
                        create_ticket_l.append(
                            [sg.Text(selectrows[i]), sg.Combo(data[i], key=keyselect[i], readonly=True)])
                    create_ticket_l.append(
                        [sg.Button("Créer", key=f"clc {clc} submit_ticket")])
                    create_ticket_l.append(
                        [sg.Button("Retour", key=f"clc {clc} return")])
                    window.extend_layout(
                        window[f'-COL{cpt}-'], create_ticket_l)
                case str(x) if "submit_ticket" in x:
                    cpt = 7
                    create_ticket(token, create_data(values))
                    cpt = return_to_main(cpt)
                case str(x) if "modify_t" in x:
                    window.timer_stop_all()
                    sg.popup_animated(None)
                    ticket = see_ticket(values)
                    cpt = 2
                    try:
                        for i in range(len(modify_ticket)):
                            for j in range(len(modify_ticket[i])):
                                    modify_ticket[i][j].hide_row()
                    except NameError:
                        pass
                    modify_ticket = [
                        [sg.Text(f"Modifier le ticket {ticket['@id'].replace('/tickets/', '')} : {ticket['titre']}",
                                 font='_ 14', justification='c', expand_x=True, key=f"clm {clm}")],
                    ]
                    txtrows = ["Titre du ticket", "Contenu du ticket",
                               "ID du Demandeur", "ID du Resolveur"]
                    dateRows = ["Date d'ouverture",
                                "Date de fermeture", "Dernière mise à jour"]
                    selectrows = ["Catégorie", "Type",
                                  "Statut", "Priorité", "Urgence"]
                    names = ["titre", "description", "demandeur", "resolveur",
                             "dateOuverture", "dateFermeture", "lastUpdateDate"]
                    selectnames= ["category", "type", "status", "priority", "urgence"]
                    data = [
                        ["Technique", "Fonctionnel", "Demande", "Incident", "Autre"],
                        ["Epic", "Task", "Story", "Bug", "Subtask",],
                        ["Nouveau", "En cours", "Résolu",
                            "Fermé", "En attente", "Rejeté",],
                        ["Bas", "Normal", "Haut", "Urgent"],
                        ["Faible", "Moyenne", "Haute", "Critique"]
                    ]
                    for i in range(len(txtrows)):
                        modify_ticket.append(
                            [sg.Text(txtrows[i]), sg.InputText(ticket[names[i]], key=names[i], do_not_clear=False)])
                    for i in range(len(dateRows)):
                        modify_ticket.append(
                            [sg.Text(dateRows[i]), sg.InputText(ticket[names[i+4]], key=names[i+4], do_not_clear=False)])
                    for i in range(len(selectrows)):
                        modify_ticket.append(
                            [sg.Text(selectrows[i]), sg.Combo(data[i], key=selectnames[i], readonly=True, default_value=find_name(ticket[selectnames[i]], selectnames[i]))])
                    modify_ticket.append(
                        [sg.Button("Modifier", key=f"mod_t:{ticket['@id'].replace('/tickets/', '')}")])
                    modify_ticket.append(
                        [sg.Button("Retour", key=f"clm {clm} return")])
                    modify_ticket.append(
                        [sg.Text("Tachez de vérifier que toutes les informations sont correctes avant de valider.", key=f"clm {clm}")])
                    window.extend_layout(window[f'-COL{cpt}-'], modify_ticket)
                case "update_ticket_choose":
                    window.timer_stop_all()
                    sg.popup_animated(None)
                    cpt = 8
                    tickets = list_tickets(values)
                    count = tickets[0]//3
                    if 0 < tickets[0] % 3 and tickets[0] % 3 < 3:
                        count += 1
                    for i in range(len(tickets_list)):
                        for j in range(len(tickets_list[i])):
                                tickets_list[i][j].hide_row()
                    tickets_list = [
                        [sg.Text("Modifier un ticket", font='_ 14',
                                 justification='c', expand_x=True, key=f"cle {cle}")],
                    ]
                    for j in range(count):
                        tickets_list.append([sg.Column([[sg.Button(f"Ticket {i['@id'].replace('/tickets/', '')} : {i['titre']}",
                                            key=f"req:get:{i['@id'].replace('/tickets/', '')}:modify_t") for i in tickets[1][j]]], key=f"cle {j} {cle}")])
                    tickets_list.append(
                        [sg.Button("Retour", key=f"cle {cle} return")])
                    window.extend_layout(window[f'-COL{cpt}-'], tickets_list)
                case "delete_ticket_choose":
                    sg.popup_animated(None)
                    window.timer_stop_all()
                    cpt = 9
                    tickets = list_tickets(values)
                    count = tickets[0]//3
                    if 0 < tickets[0] % 3 and tickets[0] % 3 < 3:
                        count += 1
                    for i in range(len(tickets_list)):
                        for j in range(len(tickets_list[i])):
                                tickets_list[i][j].hide_row()
                    tickets_list = [
                        [sg.Text("Supprimer un ticket", font='_ 14',
                                 justification='c', expand_x=True, key=f"cld {cld}")],
                    ]
                    for j in range(count):
                        tickets_list.append([sg.Column([[sg.Button(f"Ticket {i['@id'].replace('/tickets/', '')} : {i['titre']}",
                                            key=f"req:delete:{i['@id'].replace('/tickets/', '')}:del_t") for i in tickets[1][j]]], key=f"cld {j} {cld}")])
                    tickets_list.append(
                        [sg.Button("Retour", key=f"cld {cld} return")])
                    window.extend_layout(window[f'-COL{cpt}-'], tickets_list)
                case str(x) if "mod_t" in x:
                    update_ticket(token, modify_data(values),  event.split(":")[1])
                    cpt = return_to_main(cpt)
                case str(x) if "del_t" in x:
                    delete_ticket(values)
                    cpt = return_to_main(cpt)

            window[f'-COL{cpt}-'].update(visible=True)

        print(event, f"cpt={cpt}")

    window.close()

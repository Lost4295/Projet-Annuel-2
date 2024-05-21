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
        return tickets
    return None


def see_ticket(data, ticket_id):
    url = data[0]
    token = data[1]
    response = requests.get(f"{url}/api/tickets/{ticket_id}",
                            headers={"Authorization": f"Bearer {token}"})
    if response.status_code == 200:
        ticket = response.json()
    return ticket


Menu = sg.Menu
main_menu = [
    # [Menu([['File', ['Exit']], ['Edit', ['Edit Me', ]]],  k='-CUST MENUBAR1-', p=0)]
    [sg.T("Bienvenue sur l'application de ticketing de PCS !",
          font='_ 14', justification='c', expand_x=True)],
    [sg.Button("Lister les tickets", key="list_tickets")],
    [sg.Button("Créer un ticket", key="create_ticket")],
    [sg.Button("Modifier un ticket", key="update_ticket")],
    [sg.Button("Supprimer un ticket", key="delete_ticket")],
    [sg.Button("Gestion des utilisateurs", key="user_menu")],
    [sg.Button("Quitter", key="exit")],
]
user_menu = [
    # [Menu([['File', ['Exit']], ['Edit', ['Edit Me', ]]],  k='-CUST MENUBAR-', p=0)]
    [sg.Text("Gestion des utilisateurs", font='_ 14',
             justification='c', expand_x=True)],
    [sg.Button("Lister les utilisateurs", key="list_users")],
    [sg.Button("Voir un utilisateur", key="see_user")],
    [sg.Button("Retour", key="return")],
]

tickets_list = []
ticket = []
users_list = [
    [sg.Text("Liste des utilisateurs", font='_ 14',
             justification='c', expand_x=True)],
    [sg.Button("Retour", key="return")],
]
user = [
    [sg.Text("Utilisateur X", font='_ 14', justification='c', expand_x=True)],
    [sg.Button("Retour", key="return")],

]
create_ticket = [
    [sg.Text("Créer un ticket", font='_ 14', justification='c', expand_x=True)],
    [sg.Button("Retour", key="return")],

]
update_ticket = [
    [sg.Text("Mise à jour du ticket", font='_ 14',
             justification='c', expand_x=True)],
    [sg.Button("Retour", key="return")],

]
delete_ticket = [
    [sg.Text("Supprimer un ticket", font='_ 14',
             justification='c', expand_x=True)],
    [sg.Button("Retour", key="return")],
]

layout = [
    [
        sg.Column(main_menu, key='-COL1-'),
        sg.Column(user_menu, visible=False, key='-COL2-'),
        sg.Column(tickets_list, visible=False, key='-COL3-'),
        sg.Column(ticket, visible=False, key='-COL4-'),
        sg.Column(users_list, visible=False, key='-COL5-'),
        sg.Column(user, visible=False, key='-COL6-'),
        sg.Column(create_ticket, visible=False, key='-COL7-'),
        sg.Column(update_ticket, visible=False, key='-COL8-'),
        sg.Column(delete_ticket, visible=False, key='-COL9-'),
    ],
]
# Create the Window

window = sg.Window('Hello Example', layout, size=(800, 600), resizable=True)

# Event Loop to process "events" and get the "values" of the inputs
if __name__ == '__main__':
    token = authenticate()
    cpt = 1  # The currently visible layout
    clt = cli = 0
    while True:
        event, values = window.read()

        # if user closes window or clicks cancel
        if event == sg.WIN_CLOSED or event == 'Cancel' or event == 'exit':
            break

        if "return" in event:
            window[f'-COL{cpt}-'].update(visible=False)
            cpt = 1
            window[f'-COL{cpt}-'].update(visible=True)
            continue
        if "_" in event:
            window[f'-COL{cpt}-'].update(visible=False)
            match event:
                case "list_tickets":
                    cpt = 3
                    tickets = list_tickets(token)
                    if clt > 0:
                        window[f'clt {clt}'].hide_row()
                        window[f'clt {clt} return'].hide_row()
                        for i in range(len(tickets["hydra:member"])):
                            window[f"see_ticket:{tickets['hydra:member'][i]['@id'].replace('/api/tickets/', '')}"].hide_row()
                    clt+=1
                    tickets_list = [
                        [sg.Text(f"Liste des tickets: {tickets['hydra:totalItems']} tickets", font='_ 14', justification='c', expand_x=True, key=f"clt {clt}")],
                        [sg.Button(f"Ticket {i['@id'].replace('/api/tickets/', '')} : {i['titre']}", key=f"see_ticket:{i['@id'].replace('/api/tickets/', '')}") for i in tickets["hydra:member"]],
                        [sg.Button("Retour", key=f"clt {clt} return")]]
                    window.extend_layout(window[f'-COL{cpt}-'], tickets_list)
                case str(x) if "see_ticket" in x:
                    ticket = see_ticket(token, x.split(":")[1])
                    print(ticket)
                    cpt = 4
                    if cli > 0:
                        for i in range(len(ticket)):
                            window[f'cli {cli}'].hide_row()
                        window[f'cli {cli} return'].hide_row()
                    cli+=1
                    ticket = [
                        [sg.Text(f"Ticket {ticket['@id'].replace('/api/tickets/', '')} : {ticket['titre']}", font='_ 14', justification='c', expand_x=True,key=f"cli {cli}")],
                        [sg.Text(f"Titre : {ticket['titre']}",key=f"cli {cli}")],
                        [sg.Text(f"Contenu : {ticket['description']}",key=f"cli {cli}")],
                        [sg.Text(f"Date d'ouverture : {ticket['dateOuverture']}",key=f"cli {cli}")],
                        [sg.Text(f"Date de fermeture : {ticket['dateFermeture']}",key=f"cli {cli}")],
                        [sg.Text(f"Demandeur : {ticket['demandeur']}",key=f"cli {cli}")],
                        [sg.Text(f"Dernière mise à jour : {ticket['lastUpdateDate']}",key=f"cli {cli}")],
                        [sg.Text(f"Catégorie : {ticket['category']}",key=f"cli {cli}")],
                        [sg.Text(f"Type : {ticket['type']}",key=f"cli {cli}")],
                        [sg.Text(f"Statut : {ticket['status']}",key=f"cli {cli}")],
                        [sg.Text(f"Priorité : {ticket['priority']}",key=f"cli {cli}")],
                        [sg.Text(f"Urgence : {ticket['urgence']}",key=f"cli {cli}")],
                        [sg.Text(f"Resolveur : {ticket['resolveur']}",key=f"cli {cli}")],
                        [sg.Text(f"Pièces jointes : {len(ticket['pj'])}",key=f"cli {cli}")],
                        [sg.Button("Retour", key=f"cli {cli} return")],
                    ]
                    window.extend_layout(window[f'-COL{cpt}-'], ticket)
                case "list_users":
                    func5()
                case 6:
                    func6()
                case 7:
                    func7()
                case 8:
                    func8()
                case 9:
                    func9()
            window[f'-COL{cpt}-'].update(visible=True)

        print(event, f"cpt={cpt}", "_" in event)

    window.close()

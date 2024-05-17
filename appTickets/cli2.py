from consolemenu import ConsoleMenu, SelectionMenu
from consolemenu.items import FunctionItem, SubmenuItem

# Fonction qui retourne une valeur
def my_function():
    return "Résultat de la fonction"

# Variable pour stocker le résultat
result = None

# Fonction wrapper pour exécuter my_function et mettre à jour le menu
def function_wrapper(menu):
    global result
    result = my_function()
    menu.subtitle = f"Le résultat de la fonction est: {result}"

# Créer un sous-menu
def create_submenu():
    submenu = ConsoleMenu("Sous-menu", "Choisissez une option dans le sous-menu")
    
    # FunctionItem pour appeler la fonction et mettre à jour le sous-menu
    submenu_function_item = FunctionItem("Appeler une fonction", function_wrapper, args=[submenu])
    
    # Ajouter l'élément au sous-menu
    submenu.append_item(submenu_function_item)
    
    return submenu

# Créer le menu principal
menu = ConsoleMenu("Menu Principal", "Choisissez une option")

# Créer un FunctionItem pour appeler function_wrapper dans le menu principal
function_item = FunctionItem("Appeler une fonction et afficher le résultat", function_wrapper, args=[menu])

# Créer un SubmenuItem pour accéder au sous-menu
submenu = create_submenu()
submenu_item = SubmenuItem("Aller au sous-menu", submenu, menu)

# Ajouter les éléments au menu principal
menu.append_item(function_item)
menu.append_item(submenu_item)

# Afficher le menu
menu.show()

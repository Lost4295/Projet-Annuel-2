from consolemenu import ConsoleMenu, SelectionMenu
from consolemenu.items import * 

# Fonction qui retourne une valeur
def my_function():
    return "Résultat de la fonction"

# Variable pour stocker le résultat
result = None

# Fonction wrapper pour exécuter my_function et mettre à jour le menu
def function_wrapper(menu):
    global result
    result = my_function()
    menu.subtitle = f"Le résultat de la fonction est: {result}\nLe résultat de la fonction est: {result}\nLe résultat de la fonction est: {result}\nLe résultat de la fonction est: {result}\nLe résultat de la fonction est: {result}\nLe résultat de la fonction est: {result}\nLe résultat de la fonction est: {result}\nLe résultat de la fonction est: {result}\nLe résultat de la fonction est: {result}\nLe résultat de la fonction est: {result}\nLe résultat de la fonction est: {result}\nLe résultat de la fonction est: {result}\nLe résultat de la fonction est: {result}\nLe résultat de la fonction est: {result}\nLe résultat de la fonction est: {result}\nLe résultat de la fonction est: {result}\nLe résultat de la fonction est: {result}\nLe résultat de la fonction est: {result}\nLe résultat de la fonction est: {result}\nLe résultat de la fonction est: {result}\nLe résultat de la fonction est: {result}\nLe résultat de la fonction est: {result}\nLe résultat de la fonction est: {result}\nLe résultat de la fonction est: {result}\nLe résultat de la fonction est: {result}\nLe résultat de la fonction est: {result}\nLe résultat de la fonction est: {result}\nLe résultat de la fonction est: {result}\nLe résultat de la fonction est: {result}\nLe résultat de la fonction est: {result}\n!!!!!!!!!!!!!!!!!!!"
    menu.show()

# Créer un menu principal
menu = ConsoleMenu("Menu Principal", "Choisissez une option")

submenu= SelectionMenu("Sous-menu", "Option 1", "Option 2", "Option 3")
submenui =SubmenuItem("submenu", submenu, menu)

# Créer un FunctionItem pour appeler function_wrapper
function_item = FunctionItem("Appeler une fonction et afficher le résultat", function_wrapper, args=[menu])

# Ajouter l'élément au menu principal
menu.append_item(function_item)

# Afficher le menu
menu.show()

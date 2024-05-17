from consolemenu import ConsoleMenu, SelectionMenu
from consolemenu.items import FunctionItem, SubmenuItem

# Variable for storing user input
user_text = ""

# Function to capture user input
def input_text():
    global user_text
    user_text = input("Please enter some text: ")

# Function to display the entered text
def display_text(menu):
    menu.subtitle = f"Entered text: {user_text}"
    menu.show()

# Function to combine input and display
def input_and_display(menu):
    input_text()
    display_text(menu)

# Create a submenu for input operations
def create_input_submenu():
    submenu = ConsoleMenu("Text Input Submenu", "Enter and display text")

    # FunctionItem for entering text
    input_item = FunctionItem("Enter text", input_text)

    # FunctionItem for displaying text
    display_item = FunctionItem("Display entered text", display_text, args=[submenu])

    # FunctionItem for combined input and display
    combined_item = FunctionItem("Enter and display text", input_and_display, args=[submenu])

    # Add items to the submenu
    submenu.append_item(input_item)
    submenu.append_item(display_item)
    submenu.append_item(combined_item)

    return submenu

# Create the main menu
menu = ConsoleMenu("Main Menu", "Choose an option")

# Create a submenu for text input
input_submenu = create_input_submenu()
submenu_item = SubmenuItem("Go to text input submenu", input_submenu, menu)

# Add items to the main menu
menu.append_item(submenu_item)

# Show the main menu
menu.show()

import tkinter as tk


fenetre = tk.Tk()
fenetre.title("PCS Ticketing System")

window_width = 800
window_height = 500
screen_width = fenetre.winfo_screenwidth()
screen_height = fenetre.winfo_screenheight()
center_x = int((screen_width - window_width)/2)
center_y = int((screen_height - window_height)/2)
fenetre.geometry(f"{window_width}x{window_height}+{center_x}+{center_y}")

for i in range(10):
    fenetre.rowconfigure(i, weight=1)
for i in range(7):
    fenetre.columnconfigure(i, weight=1)
    
test=["jkj","jkl","jkl",'4545']
select_timezone_label = tk.Label(fenetre, text="Please select a timezone.") # Instance of Listbox class for selection of timezone from list.
list_var = tk.Variable(value=[test])
select_timezone_listbox = tk.Listbox(fenetre, listvariable=list_var, height=1) # Instance of Button class to get the local time in the selected timezone.
select_timezone_button = tk.Button(fenetre, text="Get Time") # Second instance of the Label class to display the local time in the selected timezone. 
time_label = tk.Label(fenetre, text="")
select_timezone_label.grid(column=0, row=0, columnspan=4, sticky=tk.W, padx=10, pady=10)
select_timezone_listbox.grid(column=0, row=3, columnspan=3, sticky=tk.EW, padx=10, pady=10)
select_timezone_button.grid(column=6, row=1, sticky=tk.E, padx=10, pady=10)
time_label.grid(column=0, row=4, columnspan=4, sticky=tk.W, padx=10, pady=10)
fenetre.mainloop()

from tkinter import *
from tkinter import ttk
from tkinter.messagebox import *
from tkinter.filedialog import *

fenetre = Tk()
content = Frame(fenetre).pack()
button = Button(content, text="Click Me!").pack()
def alert():
    showinfo("alerte", "Bravo!")

menubar = Menu(fenetre)

menu1 = Menu(menubar, tearoff=0)
menu1.add_command(label="Créer", command=alert)
menu1.add_command(label="Editer", command=alert)
menu1.add_separator()
menu1.add_command(label="Quitter", command=fenetre.quit)
menubar.add_cascade(label="Fichier", menu=menu1)

menu2 = Menu(menubar, tearoff=0)
menu2.add_command(label="Couper", command=alert)
menu2.add_command(label="Copier", command=alert)
menu2.add_command(label="Coller", command=alert)
menubar.add_cascade(label="Editer", menu=menu2)

menu3 = Menu(menubar, tearoff=0)
menu3.add_command(label="A propos", command=alert)
menubar.add_cascade(label="Aide", menu=menu3)

fenetre.config(menu=menubar)
# # création du canvas
# canvas = Canvas(fenetre, width=250, height=250, bg="ivory")
# # coordonnées initiales
# coords = (0, 0)
# bouton=Button(fenetre, text="Fermer", command=fenetre.quit)
# bouton.pack()
# # création du rectangle
# rectangle = canvas.create_rectangle(0,0,25,25,fill="violet")
# # ajout du bond sur les touches du clavier
# canvas.focus_set()
# canvas.bind("<Key>", clavier)
# # création du canvas
# canvas.pack()
Button(fenetre, text ="arrow", relief=RAISED, cursor="arrow").pack()
Button(fenetre, text ="circle", relief=RAISED, cursor="circle").pack()
Button(fenetre, text ="clock", relief=RAISED, cursor="clock").pack()
Button(fenetre, text ="cross", relief=RAISED, cursor="cross").pack()
Button(fenetre, text ="dotbox", relief=RAISED, cursor="dotbox").pack()
Button(fenetre, text ="exchange", relief=RAISED, cursor="exchange").pack()
Button(fenetre, text ="fleur", relief=RAISED, cursor="fleur").pack()
Button(fenetre, text ="heart", relief=RAISED, cursor="heart").pack()
Button(fenetre, text ="man", relief=RAISED, cursor="man").pack()
Button(fenetre, text ="mouse", relief=RAISED, cursor="mouse").pack()
Button(fenetre, text ="pirate", relief=RAISED, cursor="pirate").pack()
Button(fenetre, text ="plus", relief=RAISED, cursor="plus").pack()
Button(fenetre, text ="shuttle", relief=RAISED, cursor="shuttle").pack()
Button(fenetre, text ="sizing", relief=RAISED, cursor="sizing").pack()
Button(fenetre, text ="spider", relief=RAISED, cursor="spider").pack()
Button(fenetre, text ="spraycan", relief=RAISED, cursor="spraycan").pack()
Button(fenetre, text ="star", relief=RAISED, cursor="star").pack()
Button(fenetre, text ="target", relief=RAISED, cursor="target").pack()
Button(fenetre, text ="tcross", relief=RAISED, cursor="tcross").pack()
Button(fenetre, text ="trek", relief=RAISED, cursor="trek").pack()
Button(fenetre, text ="watch", relief=RAISED, cursor="watch").pack()
b1 = Button(fenetre, text ="FLAT", relief=FLAT).pack()
b2 = Button(fenetre, text ="RAISED", relief=RAISED).pack()
b3 = Button(fenetre, text ="SUNKEN", relief=SUNKEN).pack()
b4 = Button(fenetre, text ="GROOVE", relief=GROOVE).pack()
b5 = Button(fenetre, text ="RIDGE", relief=RIDGE).pack()
def pointeur(event):
    chaine.configure(text = "Clic d�tect� en X =" + str(event.x) +\
                            ", Y =" + str(event.y))
cadre = Frame(fenetre, width =200, height =150, bg="light yellow")
cadre.bind("<Button-1>", pointeur)
cadre.pack()
chaine = Label(fenetre)
chaine.pack()
fenetre.mainloop()
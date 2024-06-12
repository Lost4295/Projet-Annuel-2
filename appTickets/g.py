import PySimpleGUI as sg
import time


def work():
    time.sleep(5)

layout = [
    [sg.Multiline(size=(40, 10), key='-MULTILINE-', no_scrollbar=True)],
    [sg.Button('Download Files'),sg.Button("Exit")],
]
window = sg.Window('Demo', layout, enable_close_attempted_event=True)

while True:
    event, values = window.read()

    if event in (sg.WINDOW_CLOSE_ATTEMPTED_EVENT, 'Exit'):
        window.timer_stop_all()
        sg.popup_animated(None)
        break
    elif event == sg.TIMER_KEY:
        sg.popup_animated(sg.DEFAULT_BASE64_LOADING_GIF, message='Loading', time_between_frames=100)
        window.force_focus()
    elif event == 'Download Files':
        window.timer_start(100)
        window.start_thread(work, 'Done')
    elif event == 'Done':
        window.timer_stop_all()
        sg.popup_animated(None)

window.close()
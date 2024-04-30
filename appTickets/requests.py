import requests

response = requests.get("https://jsonplaceholder.typicode.com/users")
if response.status_code == 200:
    response_json = response.json()

    for user in response_json:
        print(user["name"])

print("-----------------------------")
response = requests.get("http://pcs.freeboxos.fr:8080/api/users")

response_json = response.json()

print(response_json)
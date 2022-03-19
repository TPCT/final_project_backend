from requests import Session
from encryptionHelper import EncryptionHelper
from json import loads, dumps
from trainsSystemApi import trainsSystemApi

def printResponse(response):
    print('status code:', response.status_code)
    print("response:", response.text)


def sendData(session, path, data):
    invalid_response2 = session.post(path, data=data)
    printResponse(invalid_response2)


if __name__ == "__main__":
    API_URL = "http://localhost:8080"
    SECRET_KEY = EncryptionHelper.generateRandomBytes(32)

    USERNAME = "train1"
    PASSWORD = "train1234"
    MAC_ADDRESS = "FF:FF:FF:FF:FF:F4"

    # session = Session()
    # helper = EncryptionHelper(SECRET_KEY)
    # decrypted_text = "hello world i love python"
    # encrypted_text = helper.symmetricEncryption(decrypted_text)
    #
    # public_key_response = session.get(API_URL + "/api/session")
    # public_key = loads(public_key_response.text)['response']['public_key']
    # helper.loadPublicKey(public_key)
    # data = dumps({
    #     'secret_key': SECRET_KEY,
    #     'encrypted_test': encrypted_text,
    #     'decrypted_test': decrypted_text
    # })
    # encrypted_data = helper.asymmetricEncryption(data)
    # secret_key_response = session.post(API_URL + "/api/session", data={
    #     'data': encrypted_data
    # })
    #
    # secret_key_response = loads(secret_key_response.text)
    #
    # if not secret_key_response['error']['error_code']:
    #     print(secret_key_response['response']['status'])
    # else:
    #     print(secret_key_response['error']['error_message'])
    #
    # test_usernames = ["", "invalid", USERNAME]
    # test_passwords = ["", "invalid", PASSWORD]
    # test_mac_addresses = ["", "invalid", "FF:FF:FF:FF:FF:10", MAC_ADDRESS]
    #
    # for test_username in test_usernames:
    #     for test_password in test_passwords:
    #         for test_mac_address in test_mac_addresses:
    #             login_data = {
    #                 'username': test_username,
    #                 'password': test_password,
    #                 'mac_address': test_mac_address
    #             }
    #             login_data = dumps(login_data)
    #             login_data_encrypted = helper.asymmetricEncryption(login_data)
    #             login_response = session.post(API_URL + "/api/login", data={
    #                 'data': login_data_encrypted
    #             })
    #             login_response = loads(login_response.text)
    #             if login_response['error']['error_code']:
    #                 print(login_response['error']['error_message'])
    #             else:
    #                 print(login_response['response'])
    #
    # public_key_response = session.get(API_URL + "/api/session")
    # public_key = loads(public_key_response.text)['response']['public_key']
    # helper.loadPublicKey(public_key)
    # data = dumps({
    #     'secret_key': SECRET_KEY,
    #     'encrypted_test': encrypted_text,
    #     'decrypted_test': decrypted_text
    # })
    # encrypted_data = helper.symmetricEncryption(data)
    # secret_key_response = session.post(API_URL + "/api/session", data={
    #     'data': encrypted_data
    # })
    # print(secret_key_response.text)
    
    trainApi = trainsSystemApi(API_URL, USERNAME, PASSWORD, MAC_ADDRESS)
    print(trainApi.sendLog({
        "sensor1": {"name": "hello world i love python"},
        "sensor2": {"name": "hello world i love python2"}
    }, True))
#!/usr/bin/python3
from encryptionHelper import EncryptionHelper
from requests import Session
from json import dumps, loads


class trainsSystemApi:
    def __init__(self, api_url, username, password, mac_address) -> None:
        self.__SECRET_KEY = ''
        self.__generateSecretKey()
        self.__Encryptor = EncryptionHelper(self.__SECRET_KEY)
        self.__username = username
        self.__password = password
        self.__mac_address = mac_address
        self.__logged = False
        self.__API_URL = api_url
        self.__session = Session()
        self.__setSessionEncryption()
        self.__login()

    def __generateSecretKey(self):
        self.__SECRET_KEY = EncryptionHelper.generateRandomBytes(EncryptionHelper.SECRET_KEY_LENGTH)

    def __encryptSessionData(self, data):
        data = dumps(data)
        return {
            'data': self.__Encryptor.symmetricEncryption(data) if self.__logged else self.__Encryptor.asymmetricEncryption(data)
        }

    def __setSessionEncryption(self):
        print("[+] trying to generate session encryption keys.")
        public_key_response = self.__session.get(self.__API_URL + "/api/session")
        public_key = loads(public_key_response.text)['response']['public_key']
        self.__Encryptor.loadPublicKey(public_key)
        decrypted_text = "hello world i am train: " + self.__username
        encrypted_text = self.__Encryptor.symmetricEncryption(decrypted_text)
        data = {
            'secret_key': self.__SECRET_KEY,
            'encrypted_test': encrypted_text,
            'decrypted_test': decrypted_text
        }
        secret_key_response = self.send("/api/session", data)
        print("[-] session encryption keys has been generated successfully.")
        if secret_key_response['error']['error_code']:
            raise ValueError(secret_key_response['error']['error_message'])

    def __login(self):
        print("[+] trying to login to the system.")
        login_data = {
            'username': self.__username,
            'password': self.__password,
            'mac_address': self.__mac_address
        }
        login_data_encrypted = self.__encryptSessionData(login_data)
        login_response = self.__session.post(self.__API_URL + "/api/login", data=login_data_encrypted)
        login_response = loads(login_response.text)
        if login_response['error']['error_code']:
            raise ValueError(login_response['error']['error_message'])
        else:
            print("[+] logged in to the system successfully.")
            self.__logged = True

    def send(self, send_directory, data):
        print("[+] trying to send data to the server.\n\t data: {}".format(data))
        encrypted_data = self.__encryptSessionData(data)
        response = self.__session.post(
            self.__API_URL + send_directory,
            data=encrypted_data
        )
        print(response.text)
        response_data = loads(response.text)
        response_data = self.__Encryptor.symmetricDecryption(response_data['data']) if self.__logged else response_data
        print("[+] data has been sent to the server successfully.\n\t response: {}.".format(response))
        return response_data

    def sendLog(self, data, isEmergency=False):
        data = {
            "event_type": "info" if not isEmergency else "emergency",
            "event_data": data
        }
        return self.send("/api/log_event", data)


if __name__ == '__main__':
    USERNAME = "train1"
    PASSWORD = "train1234"
    MAC_ADDRESS = "FF:FF:FF:FF:FF:F1"
    system = trainsSystemApi('http://localhost:8080', USERNAME, PASSWORD, MAC_ADDRESS)

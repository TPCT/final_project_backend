from requests import Session
from encryptionHelper import EncryptionHelper
from json import loads, dumps

if __name__ == '__main__':
    SECRET_KEY = EncryptionHelper.generateRandomBytes(32)
    API_URL = "http://localhost:8080"

    session = Session()
    helper = EncryptionHelper(SECRET_KEY)
    decrypted_text = "hello world i love python"
    encrypted_text = helper.symmetricEncryption(decrypted_text)

    public_key_response = session.get(API_URL + "/api/session")
    public_key = loads(public_key_response.text)['response']['public_key']
    helper.loadPublicKey(public_key)
    data = dumps({
        'secret_key': SECRET_KEY,
        'encrypted_test': encrypted_text,
        'decrypted_test': decrypted_text
    })

    encrypted_data = helper.asymmetricEncryption(data)

    secret_key_response = session.post(API_URL + "/api/session", data={
        'data': encrypted_data
    })
    print(secret_key_response, secret_key_response.text)
from requests import Session
from encryptionHelper import EncryptionHelper
from json import loads

if __name__ == "__main__":
    secretKey = EncryptionHelper.generateRandomBytes(32)
    helper = EncryptionHelper(secretKey)
    session = Session()
    response = session.get("http://localhost:8080/api/session")
    response = loads(response.text)['response']
    public_key = response['public_key']
    helper.loadPublicKey(public_key)
    encrypted_text = helper.asymmetricEncryption("hello world i love python")
    decrypted_text = helper.asymmetricDecryption(encrypted_text)
    response = session.post("http://localhost:8080/api/asymmetric/decryption", data={
        'data': encrypted_text
    })
    encrypted_text = helper.symmetricEncryption("hello world i love python")
    response = session.post("http://localhost:8080/api/symmetric/decryption", data={
        'data': encrypted_text,
        'secret_key': secretKey
    })

    decrypted_text = "hello world i love python"
    response = session.post("http://localhost:8080/api/symmetric/encryption", data={
        'secret_key': secretKey
    })
    data = loads(response.text)
    print(helper.symmetricDecryption(data['encrypted_text']))
    print(response, response.text)

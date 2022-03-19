from cryptography.hazmat.backends import default_backend
from cryptography.hazmat.primitives import serialization, hashes
from cryptography.hazmat.primitives.asymmetric import padding
from Crypto.PublicKey import RSA

from Crypto.Cipher import AES
from Crypto.Random import get_random_bytes
from binascii import hexlify, unhexlify
from base64 import b64encode, b64decode


class EncryptionHelper:
    SECRET_KEY_LENGTH = 32
    IV_KEY_LENGTH = 16

    def __init__(self, secret_key):
        self.__publicKey = None
        self.__privateKey = None
        self.__SECRET_KEY = secret_key

    @staticmethod
    def generateRandomBytes(length):
        from string import ascii_lowercase, ascii_uppercase, digits
        from random import choices
        randomString = ''.join(choices(ascii_lowercase + ascii_uppercase + digits, k=length))
        return hexlify(randomString.encode('ascii')).decode('ascii')

    def loadPublicKey(self, publicKey):
        self.__publicKey = publicKey

    def loadPrivateKey(self, privateKey):
        self.__privateKey = privateKey

    @staticmethod
    def generate_key_pairs():
        new_key = RSA.generate(2048)
        public_key = new_key.publickey().exportKey()
        private_key = new_key.export_key()
        return private_key, public_key

    def asymmetricEncryption(self, message):
        if self.__publicKey:
            publicKey = serialization.load_pem_public_key(
                self.__publicKey.encode('ascii'),
                backend=default_backend()
            )
            encryptedMessage = publicKey.encrypt(
                message.encode('utf-8'),
                padding.OAEP(
                    mgf=padding.MGF1(algorithm=hashes.SHA1()),
                    algorithm=hashes.SHA1(),
                    label=None
                )
            )
            return hexlify(encryptedMessage).decode()
        return None

    def asymmetricDecryption(self, encrypted_text):
        if self.__privateKey:
            encrypted_text = unhexlify(encrypted_text)
            private_key = serialization.load_pem_private_key(
                self.__privateKey.encode('ascii'),
                backend=default_backend(),
                password=None
            )
            decryptedMessage = private_key.decrypt(
                encrypted_text,
                padding.OAEP(mgf=padding.MGF1(algorithm=hashes.SHA1()),
                             algorithm=hashes.SHA1(),
                             label=None)
            )
            return decryptedMessage.decode('utf-8')
        return None

    def symmetricEncryption(self, message):
        pad = lambda s: s + chr(EncryptionHelper.IV_KEY_LENGTH - len(s) % EncryptionHelper.IV_KEY_LENGTH) * (
                EncryptionHelper.IV_KEY_LENGTH - len(s) % EncryptionHelper.IV_KEY_LENGTH)
        secret_key = unhexlify(self.__SECRET_KEY.encode())
        iv_key = get_random_bytes(EncryptionHelper.IV_KEY_LENGTH)
        message = pad(message).encode('utf-8')
        cipher = AES.new(secret_key, AES.MODE_CBC, iv_key)
        encrypted_message_bytes = cipher.encrypt(message)
        cipher_b64 = b64encode("{}:{}".format(hexlify(iv_key).decode(), hexlify(encrypted_message_bytes).decode()).encode())
        return cipher_b64.decode()

    def symmetricDecryption(self, encryptedData):
        unpad = lambda s: s[:-s[-1]]
        secret_key = unhexlify(self.__SECRET_KEY.encode())
        encrypted_data = b64decode(encryptedData)
        iv_key, encrypted_message = encrypted_data.split(":".encode())
        iv_key, encrypted_message = unhexlify(iv_key), unhexlify(encrypted_message)
        cipher = AES.new(secret_key, AES.MODE_CBC, iv_key)
        message = cipher.decrypt(encrypted_message)
        return unpad(message).decode('utf-8').rstrip()

    @staticmethod
    def encryptSingleLetter(letter, public_key=(5, 14)):
        from string import ascii_lowercase
        encrypted_letter = (ascii_lowercase.find(letter) + 1) ** public_key[0] / public_key[1]
        encrypted_letter -= int(encrypted_letter)
        encrypted_letter *= public_key[1]
        return ascii_lowercase[round(encrypted_letter) - 1]

    @staticmethod
    def decryptSingleLetter(letter, private_key=(11, 14)):
        from string import ascii_lowercase
        decrypted_letter = (ascii_lowercase.find(letter) + 1) ** private_key[0] / private_key[1]
        decrypted_letter -= int(decrypted_letter)
        decrypted_letter *= private_key[1]
        return ascii_lowercase[round(decrypted_letter) - 1]


if __name__ == '__main__':
    from time import time
    secretKey = EncryptionHelper.generateRandomBytes(32)
    helper = EncryptionHelper(secretKey)

    def asymmetricEncryptionTest():
#         private_key = """-----BEGIN PRIVATE KEY-----
# MIIJQgIBADANBgkqhkiG9w0BAQEFAASCCSwwggkoAgEAAoICAQC2BLj5WUmBVOwM
# LrqBKblAW3eY4C5NjXpzG6MPj0o8KZCvObbDB6Ay7f1Xo2vyPIv+z/lk7YtVeST5
# V9Ejddbztpvs7c3oFbu/SL4MwmOf+GmOrWadjDDw3d/+N2ZtJSTg+mbHb9UFjM0z
# B8ZpxQ1dZmHbjNGH/uaBws4t4cgGgo4zLQgHBXQ/6G8m+X2+jHzstVbAPyQP3VtJ
# gZXUbzQPXt/LdaU0/nn/sPHm0vdfW+z6etAoNdgjoHseaA89187YL1SvHPeDtCCZ
# v4Y0Smp1j+ZHWS3Ayy3xWw2dWIl3jx0UE6NL7y8OnMGa+TeTlODDcpi8gVu5hBOT
# Pbpe7JUj7ibXQsbEaVEOSrPvrN5NLfCEe9AucQb8QhvuBoF1zSqFnmNBnFqOPWDS
# 1miLebt9pSOVDB0oItRDVjxu7FLp4m8gXQmGYo2JpLBZtOD9E9ppfx1Mfqi/Hk8d
# HezT2H5b2MYh10mpFEXLnLsF4W+5PL84x8D140NnUVyALrKqYWDTvybQBXhWxxlS
# DXotat5EwTn2sJMkCeVmnHRN9E3ahEEncSoxNr9qZfYiZUxTMpYEKGXuMomHRuaB
# G+cfs7PWqScR56FcUgJfRtspwlECWfYXQZCJ+EhRoBQGZhWjsH+Gt6JEpQQCZCHA
# fTMl01Sa9QMRECpEZi6UJz/I5JQ3CQIDAQABAoICACH0q5VIxN+tugNjzENAR2Ds
# L1mDqN+q3wcORtMmRhEHKAioWaH0kDwwDa0FvaOJDAzTO5FKh03AtdcWPNvCpgD9
# NgVzL5B2TXoX1eyHbxJuwzJswtHtT0v/+ENyjlcaMNEPpZDbGgBZDgbREoC01BNy
# gBr3IDego9vdPZ1tm2Urgd+mLPMyX0d00xeQP5cGml+GTNK8dLvI7bi+xfbFcA7W
# AiXwootKVfQPTHFlNeTCP2lS8/YsZU/wJke73ewJkHrgCYpJextExTgpdJ3YrBeW
# ar0ws8pdnQqDk/yggbSeLCyMpVALJNozACMrDuUnRu7vN/MiprvrGcPEJjqDJCcp
# Mie7Tdo5YNSiVQkKxEjmJvnJzfAtyXQ6U9d62L8Ipa9eHowlFFfxEH3dA4wunTnF
# 6ZAZ7SxsVq4H+zGLOutWWWKKp2BhM31OG8KOT73izdnmK81akeObb2h05xntwBwD
# LS5BCiMqhcIolBvKhZxnEgaTfVO3zoStya4DZlvdFeXL0Uln79vz9FU/zh3MGWl3
# BCAMkSnzlAFvi3QVvg5lVqAlFjU3ZSWYj1+6ss+6/TR9kCqS7wMaGR5aQUeok3Wf
# IYW3CjEfTL+Lsf+F6X2mqTw+5ZjQnGHMryIlgDQ8vVB3RmbncvyJPR6Sd2wshnk8
# XW2QNtkJSPZLt5w8y90JAoIBAQDgivM25lLNRftL0mj/NfdFp2sZo2GnhjYNkgmU
# EjMJdsFAXzHOt741d3YS5j4tkkEnq6+84ydHxihMJtNNW2Jk2Klq/YWSuQhxthY6
# YNeo7u+iupt9EztDHyjhFjq0WJg35U+VDXInvQO7QvDo9J8iNZYScn+g+jd5KNj+
# GdhNth9n7Fs7enPM+Thwqo4qfO9NsYB3NeuSYwSR7E6EmSyi0DpIk3WG5UpmYJWZ
# fY2isMiaoXx5nazLi2CRdKI+g/f64cDQ1Q49nEM4+8JrTFnnVpsSOnLii0QWQnpX
# lkMftB5LhfYt2OXQ5Fl+Agn6KnGsoEtaVwUg5VSsydhfbKbzAoIBAQDPhKvGEyr6
# PyDVOCSP+u0JKZD4exH5q512aUYCHPkdI3SNTED5zph4o1KudOr286qQPlrp2u7b
# vnIscMlCUrpDEbFdTE5blGEA3obGYJDoLqzo3xQ1EWLpknQK7dK0oC2tqvpqQyl3
# oi+7JSfi4mVCpdOseOaiudQVYMKDdzVBZw2WsVJ3IXF93hM+g9+Hltzq5NKCNNbg
# KcozeCJA944xElW73cB4JZF+aYe6NSeE0fAS137uyPfmGSjt9PvOfUGVie/g5y/w
# LVjkwhBHCp0jNCy9WQcVS23rSnkoCSlEVbSgu6X+Wk7zGxD8+O/gpbTNdclRum3g
# x29fXhymp6ETAoIBAQDBQvb43p6P7VbQOMaEOpecTO+ifnvzq1FktrglyNBCqgLE
# ++orqPw+mpZ2x9LmeEY59cxpP/20CfrvKE+f+jKi59RRsOlBkp6Q+I8DKg0Uaqq4
# nfJsYZwVNoGB0hcu2D4ThfvMJ2mEiGvoxkIBz2IUGJkVQZVWIWaTkrPJaMdCg6le
# UhFoDQoUdsC8QB/is+toLImMkU0zjqQFUKV3/Tps29n+U2TJeRpjKWg5VtYMHCpc
# 5Pb02eA6xLiWGRP3yLAsMBg75dS+9HrrVc1Nesa6lVEdDE+LayBsJEWWk08sv2N/
# z0pGxbNv6sqX6PtbZSK32LNiixlv6dLKcWQZJbQ/AoIBADPfWAjVNcMEELL7q7gJ
# 40KND13tdZrRWTUGL6fLkkHEYRgI3Z0UFWzidoKBW1KLqjEQRS17uoVXX7bYIpbf
# kwu82ncV4ehmeLD87vebn91J/ZLgYG67f9we7b7ln+vitkhZGuuBClaLRh8jtIq0
# SNeWGAle11gJ14fYfgbav4cvuWfXv2NkCriJ0Imp8TA1d9eHh09g4e16xL6E4xsW
# RobhrGifj8L4sRvGSVU0gEJlL/ulvmb1+XGdDGwe9uqwyLoWls3DPGpvC29zSxpW
# /tyT6DMUk/6wsjNElHB7icM5IUOkZGPmMyH1vBNgLqYbBczuCnrRfTHY6HAQWF0Y
# Pd0CggEAMAFhDy1JSRB01T/EZIWavteaFyPvW/4mMy5GwYBBZB8yc0IjDDKmzbxc
# CeUiewJYBBJ3oQs4rXK6SSbYljJvRpOBotC5fhSqw/z/u0Lqq06LYEbTjrDSv+3Q
# jGvB9kaUdlEpf7NhjmpwXZj9T5cYhu8bYIhpB4PPP0GuS6VK/TKvF/AB5JGJS7pF
# 0tPUaT7mzK8ImHIts+Zbc3EmU27ezIqL9LzSsZez79fyBCXEwbwuu65A8qlYOHlX
# gMpOZ9U59/7gsOiDPsN1VnxZWFXjgxyeA6SEVeCEk3wr+xKNUSJR9lAThr3T8chq
# No+/fQuIFEu9s1qO3gZY8Pa7vbwRKg==
# -----END PRIVATE KEY-----"""
#
#         public_key = """-----BEGIN PUBLIC KEY-----
# MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEAtgS4+VlJgVTsDC66gSm5
# QFt3mOAuTY16cxujD49KPCmQrzm2wwegMu39V6Nr8jyL/s/5ZO2LVXkk+VfRI3XW
# 87ab7O3N6BW7v0i+DMJjn/hpjq1mnYww8N3f/jdmbSUk4Ppmx2/VBYzNMwfGacUN
# XWZh24zRh/7mgcLOLeHIBoKOMy0IBwV0P+hvJvl9vox87LVWwD8kD91bSYGV1G80
# D17fy3WlNP55/7Dx5tL3X1vs+nrQKDXYI6B7HmgPPdfO2C9Urxz3g7Qgmb+GNEpq
# dY/mR1ktwMst8VsNnViJd48dFBOjS+8vDpzBmvk3k5Tgw3KYvIFbuYQTkz26XuyV
# I+4m10LGxGlRDkqz76zeTS3whHvQLnEG/EIb7gaBdc0qhZ5jQZxajj1g0tZoi3m7
# faUjlQwdKCLUQ1Y8buxS6eJvIF0JhmKNiaSwWbTg/RPaaX8dTH6ovx5PHR3s09h+
# W9jGIddJqRRFy5y7BeFvuTy/OMfA9eNDZ1FcgC6yqmFg078m0AV4VscZUg16LWre
# RME59rCTJAnlZpx0TfRN2oRBJ3EqMTa/amX2ImVMUzKWBChl7jKJh0bmgRvnH7Oz
# 1qknEeehXFICX0bbKcJRAln2F0GQifhIUaAUBmYVo7B/hreiRKUEAmQhwH0zJdNU
# mvUDERAqRGYulCc/yOSUNwkCAwEAAQ==
# -----END PUBLIC KEY-----"""
        private_key, public_key = EncryptionHelper.generate_key_pairs()
        private_key = private_key.decode()
        public_key = public_key.decode()
        message = "hello world i love python"
        helper.loadPrivateKey(private_key)
        helper.loadPublicKey(public_key)
        encrypted_text = helper.asymmetricEncryption(message)
        decrypted_text = helper.asymmetricDecryption(encrypted_text)
        print('encrypted message:', encrypted_text)
        print('decrypted message:', decrypted_text)

    def symmetricEncryptionTest():
        message = "hello world i love python, php, js, and linux os"
        encrypted_text = helper.symmetricEncryption(message)
        decrypted_text = helper.symmetricDecryption(encrypted_text)
        print(decrypted_text)

    symmetricEncryptionTest()
    asymmetricEncryptionTest()
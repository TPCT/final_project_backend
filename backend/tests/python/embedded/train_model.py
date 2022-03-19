from trainsSystemApi import trainsSystemApi
from random import randint, random


class TrainModel:
    def __init__(self, api_url, username, password, mac_address):
        self.api = trainsSystemApi(api_url, username, password, mac_address)

    def getLdrReading(self):
        return randint(0, 1)

    def getPirReading(self):
        return randint(0, 1)

    def getTempReading(self):
        return randint(-55, 150)

    def getIR1Reading(self):
        return randint(0, 1)

    def getIR2Reading(self):
        return randint(0, 1)

    def getFlameReading(self):
        return randint(0, 1)

    def getLongitude(self):
        return randint(-89, 89) + random()

    def getLatitude(self):
        return randint(-179, 179) + random()

    def generateRandomInputs(self):
        return {
            'LDR': self.getLdrReading(),
            'FLAME': self.getFlameReading(),
            'PIR': self.getPirReading(),
            'TEMPERATURE': self.getTempReading(),
            'IR1': self.getIR1Reading(),
            'IR2': self.getIR2Reading(),
            'LONGITUDE': self.getLongitude(),
            'LATITUDE': self.getLatitude()
        }


if __name__ == "__main__":
    from threading import Thread, currentThread, Lock
    LOCKER = Lock()

    def train1Thread():
        API_URL = "http://localhost:8080"
        username = "train1"
        password = "train1234"
        mac_address = "FF:FF:FF:FF:FF:F1"
        train1Model = TrainModel(API_URL, username, password, mac_address)
        for i in range(100):
            with LOCKER:
                fake_data = train1Model.generateRandomInputs()
                train1Model.api.sendLog(fake_data, randint(0, 1))

    def train2Thread():
        API_URL = "http://localhost:2020"
        username = "train2"
        password = "train2234"
        mac_address = "FF:FF:FF:FF:FF:F2"
        train2Model = TrainModel(API_URL, username, password, mac_address)
        for i in range(100):
            with LOCKER:
                fake_data = train2Model.generateRandomInputs()
                train2Model.api.sendLog(fake_data, randint(0, 1))

    def train3Thread():
        API_URL = "http://localhost:6060"
        username = "train3"
        password = "train3234"
        mac_address = "FF:FF:FF:FF:FF:F3"
        train3Model = TrainModel(API_URL, username, password, mac_address)
        for i in range(100):
            with LOCKER:
                fake_data = train3Model.generateRandomInputs()
                train3Model.api.sendLog(fake_data, randint(0, 1))

    print("simulation started.")

    thread1 = Thread(target=train1Thread)
    thread2 = Thread(target=train2Thread)
    thread3 = Thread(target=train3Thread)

    thread1.start()
    thread2.start()
    thread3.start()

    threads_pool = [thread1, thread2, thread3]
    for thread in threads_pool:
        if thread.is_alive():
            thread.join()

    print("simulation done.")
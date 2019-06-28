import os
from asn1crypto import pem, x509


path='certs/'
files = os.listdir(path)
for name in files:
    if '.crt' in name:
        with open(path+name, 'rb') as arch:
            data = arch.read()
            if pem.detect(data):
                type_name, headers, data = pem.unarmor(data)
            cert = x509.Certificate.load(data)
            info = {
            "file": name,
            "serial_number" : str(cert.serial_number),
            "name": cert.subject.native['given_name'] + " " +cert.subject.native['surname'],
            "identification" : cert.subject.native['serial_number'].replace("CPF-", "").replace("NUP-", '')
             }
            print("%(file)s\t%(serial_number)s\t%(name)s\t%(identification)s"%info )



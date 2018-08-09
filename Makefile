all: sp.key

sp.key:
	openssl req -x509 -nodes -sha256 -days 365 -newkey rsa:2048 -subj "/C=IT/ST=Italy/L=Rome/O=myservice/CN=localhost" -keyout sp.key -out sp.crt

clean:
	rm -rf vendor
	rm -f sp.key
	rm -f sp.crt
	rm -f idp_metadata/*.xml

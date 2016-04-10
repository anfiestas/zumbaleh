#    Do the change in Constants file and push to master repo before execute this command
#1.Update eqExchange first: 
#cd /usr/share/nginx/www && git pull
#2.Update user tokens
#mysql -D spoora -u root -pperlinoTio1! -e "UPDATE user SET tokens = ( tokens * 1.4 );"
#mysql -D spoora -u root -pperlinoTio1! -e "show tables;"
#3.Send Info message to All users
wget "https://www.myspoora.com/imservices/messages/send_to_all?user=admin&pass=perlinoTio1!&text=Spoora informa: Nuevas condiciones solicitud cobro. Donaciones ONG: Cambio del 50% a cuota fija de 1 euro independientemente del saldo canjeado. Nuevo mínimo necesario para canjear saldo: 5 euros. Nueva ecuación de canje noviembre 2015: 0.02 euros por 100 spooris."
wget "https://www.myspoora.com/imservices/messages/send_to_all?user=admin&pass=perlinoTio1!&text=Como compensación a la reducción en la ecuación de canje, a parte de bajar el mínimo de cobro a la mitad y cambiar el 50% de donación obligatoria por una cuota fija, se han incrementado los límites de acumulación de spooris."
wget "https://www.myspoora.com/imservices/messages/send_to_all?user=admin&pass=perlinoTio1!&text=Nuevo límite diario: 1.500 spooris (aplicable al envío de mensajes). Nuevo límite mensual: 60.000 spooris. En conclusión, hemos reducido la ecuación de canje pero hemos BAJADO EL MÍNIMO DE SALDO PARA COBRAR A LA MITAD, eliminado el 50% de donación obligatoria y aumentado los límites de acumulación de spooris. Cualquier duda o consulta envíanos un mail a contact@myspoora.com"

echo "EqExchange update successfully";

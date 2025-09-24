prepro_archivos() {
    entrada="$1";
    salida="$2";

    cat $entrada | sed '/^[[:space:]]*$/d' |         # se eliminan lineas en blanco
	sed -e 's/[ \t]\+/ /g' |                     # varios espacios y tabuladores por un solo espacio 
	sed 's/vinculos/vínculos/' |                 # una errata
	sed 's/patron/patrón/' |                 # una errata
	sed 's/margenes/márgenes/' |                 # una errata
	sed 's/[,.\)][ $]/ /g' |                     # se eliminan las comas, los ) y los puntos seguidos de  un espacio
	sed 's/[\(]//g' |
	awk '
	    BEGIN { FS="[ \t=]+" }
	          {
	          for (i = 1; i <= NF; i++) {
        	      if ($i ~ /^-?[0-9]/) {
            	      	 # Redondear el número flotante a 4 cifras decimales
            		 $i = sprintf("%.5g", $i)
        		 }
    		  }

     		  # Imprimimos la línea modificada
    		   print
		   }
	    '	 |
	cat > $salida
    }

comparar_archivos() {
    archivo1="$1"
    archivo2="$2"

    prepro_archivos $archivo1 '/tmp/newaux.txt'
    prepro_archivos $archivo2 '/tmp/aux.txt'
#     # py y php 'escriben' los reales en formato ligeramente distinto. Se traducen los ficheros
#     # de control generados originariamente en py. Se eliminan tambien las lineas en blanco
#     cat $archivo2 | sed 's/\.0\([^0-9]\)/\1/g' | sed 's/\.0$//g' |
# 	sed 's/e-\([0-9]\+\)/E-\1/g' |    # notacion cientifica
# 	sed 's/E-0/E-/g' |
# 	sed '/^[[:space:]]*$/d' |         # se eliminan lineas en blanco
# 	sed 's/vinculos/vínculos/' |
# 	sed 's/  (/ (/' |                 # y lo último, se redondenan a 4 cifras decimales los float
# 	sed -E 's/([0-9]+\.[0-9]+)\.([[:space:][:punct:]])/\1\2/g'  |awk 'BEGIN { FS="[ \t]+" }
# {
#     # Usamos un separador temporal para manejar los números flotantes
#     # Reemplazamos puntos en números decimales que no deben redondearse
#     gsub(/([0-9]+\.[0-9]+)\.([[:space:][:punct:]])/, "\\1\\2")

#     # Procesamos cada campo en la línea
#     for (i = 1; i <= NF; i++) {
#         if ($i ~ /^[-0-9]+\.[0-9]+E?-?[0-9]+([,\)])?$/) {
#             # Redondear el número flotante a 4 cifras decimales
#             $i = sprintf("%.5g", $i)
#         }
#     }

#     # Imprimimos la línea modificada
#     print
# }'    > /tmp/aux.txt	
	# sed 's/0.2999999999999998/0.3/' | # unos errores de redondeosed
	# sed 's/6.099999999999994/6.1/' |
	# sed 's/0.9000000000000057/0.90000000000001/' |
	# sed 's/0.09999999999999432/0.099999999999994/' |
	# sed 's/-79.9996043362943/-79.999604336294/' | sed 's/0.9698352033688877/0.96983520336889/'|
	# sed 's/-78.01321336062809/-78.013213360628/'| sed 's/0.9835949336573293/0.98359493365733/' |
	# sed 's/-9.00675505420368/-9.0067550542037/' |
	# sed 's/0\{5,\}[0-9]//g' |
	# sed 's/0.7000000000000002/0.7/'  > /tmp/aux.txt

#     cat $archivo1 |	sed -E 's/([0-9]+\.[0-9]+)\.([[:space:][:punct:]])/\1\2/g'  |awk 'BEGIN { FS="[ \t]+" }
# {
#     # Usamos un separador temporal para manejar los números flotantes
#     # Reemplazamos puntos en números decimales que no deben redondearse
#     gsub(/([0-9]+\.[0-9]+)\.([[:space:][:punct:]])/, "\\1\\2")

#     # Procesamos cada campo en la línea
#     for (i = 1; i <= NF; i++) {
#         if ($i ~ /^[-0-9]+\.[0-9]+E?-?[0-9]+([,\)])?$/) {
#             # Redondear el número flotante a 4 cifras decimales
#             $i = sprintf("%.5g", $i)
#         }
#     }

#     # Imprimimos la línea modificada
#     print
# }' 	> /tmp/newaux.txt
    
    # Compara los dos archivos
    # if diff "$archivo1" "$archivo2" > /dev/null; then
    # if diff "$archivo1" /tmp/aux.txt > /dev/null; then
    if diff /tmp/newaux.txt /tmp/aux.txt > /dev/null; then
    
        echo "OK"
    else
        echo "Los archivos $archivo1 y $archivo2 son diferentes"
	echo "Se genera el fichero ${archivo2}NEW"
	cp $archivo1 "${archivo2}NEW"
        exit 1  # Termina el script con un código de salida no cero para indicar un error
    fi
}

#----GEOMETRÍA Y TOPOLOGIA-----------------------------------------------------
echo "test1";
php prueba_0.php 'modelos/modelo1.$2k' 'modelos/modelo1.$2k' > /dev/null;  
comparar_archivos log.txt 'logs/log_test1.txt'

echo "test2"
php prueba_0.php 'modelos/modelo1.$2k' 'modelos/modelo1_KOx.$2k' > /dev/null;  
comparar_archivos log.txt 'logs/log_test2.txt'

echo "test3"
php prueba_0.php 'modelos/modelo1.$2k' 'modelos/modelo1_Desordenado.$2k' > /dev/null;  
comparar_archivos log.txt 'logs/log_test3.txt'

echo "test4"
php prueba_0.php 'modelos/mod_0.$2k' 'modelos/mod_1.$2k' > /dev/null;  
comparar_archivos log.txt 'logs/log_test4.txt'

echo "test5"
php prueba_0.php 'modelos/mod_1.$2k' 'modelos/mod_0.$2k' > /dev/null;  
comparar_archivos log.txt 'logs/log_test5.txt'

echo "test6"
php prueba_0.php 'modelos/mod_0.$2k' 'modelos/mod_2.$2k' > /dev/null;  
comparar_archivos log.txt 'logs/log_test6.txt'

echo "test7"
php prueba_0.php 'modelos/mod_2.$2k' 'modelos/mod_0.$2k' > /dev/null;  
comparar_archivos log.txt 'logs/log_test7.txt'

#----FRAME SECTION ASSIGNMENTS--------------------------------------------------
echo "test10";
php prueba_1.php 'modelos/modelo1.$2k' 'modelos/modelo1.$2k' > /dev/null;  
comparar_archivos log.txt 'logs/log_test10.txt'

echo "test11";
php prueba_1.php 'modelos/modelo1.$2k' 'modelos/modelo1_Desordenado.$2k' > /dev/null;  
comparar_archivos log.txt 'logs/log_test11.txt'

echo "test12";
php prueba_1.php 'modelos/modelo1.$2k' 'modelos/modelo1_Desordenado_KO2sec.$2k' > /dev/null;  
comparar_archivos log.txt 'logs/log_test12.txt'

echo "test13";
php prueba_1.php 'modelos/modelo1.$2k' 'modelos/modelo1_Desordenado_KOsec.$2k' > /dev/null;  
comparar_archivos log.txt 'logs/log_test13.txt'

echo "test14";
php prueba_1.php 'modelos/modelo1.$2k' 'modelos/modelo1_Desordenado_NombreSecDistintos.$2k' > /dev/null;  
comparar_archivos log.txt 'logs/log_test14.txt'

echo "test15";
php prueba_1.php 'modelos/modelo1.$2k' 'modelos/modelo1_Desordenado_NombreSecDistintos_KO.$2k' > /dev/null;  
comparar_archivos log.txt 'logs/log_test15.txt'

echo "test16";
php prueba_1.php 'modelos/mod_0.$2k' 'modelos/mod_1.$2k' > /dev/null;  
comparar_archivos log.txt 'logs/log_test16.txt'

echo "test17";
php prueba_1.php 'modelos/mod_0.$2k' 'modelos/mod_2.$2k' > /dev/null;  
comparar_archivos log.txt 'logs/log_test17.txt'

echo "test18";
php prueba_1.php 'modelos/mod_0.$2k' 'modelos/mod_1_KOmat.$2k' > /dev/null;  
comparar_archivos log.txt 'logs/log_test18.txt'

echo "test19";
php prueba_1.php 'modelos/mod_0.$2k' 'modelos/mod_0_KO_sec.$2k' >/dev/null;  
comparar_archivos log.txt 'logs/log_test19.txt'

echo "test20";
php prueba_2.php  'modelos/mod_2_KO2_rst.$2k' 'modelos/mod_0.$2k'  >/dev/null;  
comparar_archivos log.txt 'logs/log_test20.txt'

echo "test21";
php prueba_2.php 'modelos/mod_0.$2k' 'modelos/mod_2_KO2_rst.$2k' >/dev/null;  
comparar_archivos log.txt 'logs/log_test21.txt'


echo "test22";
php prueba_2.php  'modelos/mod_2_KO_rst.$2k' 'modelos/mod_0.$2k'  >/dev/null;  
comparar_archivos log.txt 'logs/log_test22.txt'

echo "test23";
php prueba_2.php 'modelos/mod_0.$2k' 'modelos/mod_2_KO_rst.$2k' >/dev/null;  
comparar_archivos log.txt 'logs/log_test23.txt'

echo "test30";
php prueba_3.php 'modelos/modelo1.$2k' 'modelos/modelo1_KN.$2k'  >/dev/null;  
comparar_archivos log.txt 'logs/log_test30.txt'

echo "test31";
php prueba_3.php 'modelos/modelo2.$2k' 'modelos/modelo2.$2k' >/dev/null;  
comparar_archivos log.txt 'logs/log_test31.txt'

echo "test32";
php prueba_3.php 'modelos/modelo2.$2k' 'modelos/modelo2_1.$2k' >/dev/null;  
comparar_archivos log.txt 'logs/log_test32.txt'

echo "test33";
php prueba_3.php 'modelos/modelo2.$2k' 'modelos/modelo2_2.$2k' >/dev/null;  
comparar_archivos log.txt 'logs/log_test33.txt'

echo "test40";
php prueba_4.php 'modelos/Modelo1.$2k' 'modelos/ANA DE ONA - PRACTICA1.$2k' 0.989 1.1 >/dev/null;  
comparar_archivos log.txt 'logs/log_test40.txt'

echo "test41";
php prueba_4.php 'modelos/modelo 2 0.$2k' 'modelos/ANA DE ONA - PRACTICA2.$2k' 1.04 0.98 >/dev/null;  
comparar_archivos log.txt 'logs/log_test41.txt'

#echo "test42"
#php prueba_4.py 'modelos/Modelo1.$2k' 'modelos/SAP modelo 1.$2k' 0.965 1.0476281250000001 >/dev/null;  
#comparar_archivos log.txt 'logs/log_test42.txt'

echo "test50"
php prueba_5.php 'modelos/modelo3.$2k' 'modelos/modelo3-1.$2k'  >/dev/null;  
comparar_archivos log.txt 'logs/log_test50.txt'

echo "test51"
php prueba_5.php 'modelos/modelo3.$2k' 'modelos/modelo3-2.$2k' >/dev/null;  
comparar_archivos log.txt 'logs/log_test51.txt'

echo "test52"
php prueba_5.php 'modelos/modelo3.$2k' 'modelos/modelo3-3.$2k' >/dev/null;  
comparar_archivos log.txt 'logs/log_test52.txt'

echo "test53"
php prueba_5.php 'modelos/modelo3.$2k' 'modelos/modelo3-4.$2k' >/dev/null;  
comparar_archivos log.txt 'logs/log_test53.txt'

echo "test54"
php prueba_5.php 'modelos/modelo3.$2k' 'modelos/modelo3-5.$2k' >/dev/null;  
comparar_archivos log.txt 'logs/log_test54.txt'

echo "test55"
php prueba_5.php 'modelos/modelo3.$2k' 'modelos/modelo3-6.$2k' >/dev/null;  
comparar_archivos log.txt 'logs/log_test55.txt'

echo "test56"
php prueba_5.php 'modelos/modelo3.$2k' 'modelos/modelo3-7.$2k' >/dev/null;  
comparar_archivos log.txt 'logs/log_test56.txt'

echo "test57"
php prueba_5.php 'modelos/modelo3.$2k' 'modelos/modelo3-8.$2k' >/dev/null;  
comparar_archivos log.txt 'logs/log_test57.txt'

echo "test58"
php prueba_5.php 'modelos/modelo3.$2k' 'modelos/modelo3-9.$2k' >/dev/null;  
comparar_archivos log.txt 'logs/log_test58.txt'

echo "test59"
php prueba_5.php 'modelos/modelo3-9.$2k' 'modelos/modelo3.$2k' >/dev/null;  
comparar_archivos log.txt 'logs/log_test59.txt'

echo "test60"
php prueba_5.php 'modelos/modelo3.$2k' 'modelos/modelo3-A.$2k' >/dev/null;  
comparar_archivos log.txt 'logs/log_test60.txt'

echo "test61"
php prueba_5.php 'modelos/modelo3-A.$2k' 'modelos/modelo3.$2k' >/dev/null;  
comparar_archivos log.txt 'logs/log_test61.txt'

echo "test62"
php prueba_5.php 'modelos/modelo3-A.$2k' 'modelos/modelo3-A.$2k' >/dev/null;  
comparar_archivos log.txt 'logs/log_test62.txt'


echo "test70"
php prueba_5.php 'modelos/modelo3.$2k' 'modelos/Modelo4_MartinGilJULIO.$2k'  1.09 1.03 >/dev/null;
comparar_archivos log.txt 'logs/log_test70.txt'  

echo "test71";
php prueba_4.php 'modelos/modelo3.$2k' 'modelos/Modelo4_MartinGilJULIO.$2k'  1.09 1.03 >/dev/null;  
comparar_archivos log.txt 'logs/log_test71.txt'

echo "test72";
php prueba_4.php 'modelos/solucion3.$2k' 'modelos/Modelo4_MartinGilJULIO.$2k'  1.09 1.03 >/dev/null;  
comparar_archivos log.txt 'logs/log_test72.txt'


echo "test80";
php prueba_6.php 'modelos/modelo51.$2k' 'modelos/modelo51.$2k'  1 1 >/dev/null;  
comparar_archivos log.txt 'logs/log_test80.txt'


echo "test81";
php prueba_6.php 'modelos/modelo51.$2k' 'modelos/modelo51-B.$2k'  1 1 >/dev/null;  
comparar_archivos log.txt 'logs/log_test81.txt'


echo "test82";
php prueba_4.php 'modelos/modelo51.$2k' 'modelos/modelo51.$2k'  1 1 >/dev/null;  
comparar_archivos log.txt 'logs/log_test82.txt'


echo "test83";
php prueba_4.php 'modelos/modelo51.$2k' 'modelos/modelo51-B.$2k'  1 1 >/dev/null;  
comparar_archivos log.txt 'logs/log_test83.txt'

echo "fin test"
exit;


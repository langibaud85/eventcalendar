# EventCalendar

1-Pré-requis

    Codeigniter 3.x

2-instalation
    Copier le projet cloné dans votre architecture
    Faire un composer install


3-Configuration SMTP
    Dans le config.php ajouter les lignes suivantes, en mettant les valeurs de votre configuration
     $config['glbUsername'] = '';
     $config['glbPassword'] = '';
     $config['srvSMTP']='smtp.orange.fr';
     $config['srvPort']="465";
     $config['SSL']="ssl" ; 

4-Configuration du mail
    La function index() de SendingMail donne un exemple 

    Changer le destinataire du mail
    
4-lancement
    Modifier localhost par votre serveur codeigniter

    http://localhost/eventcalendar/index.php/SendingMail
    
    

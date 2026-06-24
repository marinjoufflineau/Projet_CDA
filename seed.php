<?php
require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use App\Config\DatabaseConnection;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

// Chargement explicite des variables d'environnement pour que le seed utilise
// la même base que l'application, notamment MongoDB Atlas en production.
if (file_exists(__DIR__ . '/.env')) {
    Dotenv::createMutable(__DIR__)->safeLoad();
}

$db = DatabaseConnection::getDatabase();
foreach (['utilisateurs','categories','lieux','bons_plans','commentaires','votes'] as $collection) {
    $db->selectCollection($collection)->drop();
}

$users = $db->selectCollection('utilisateurs');
$adminId = new ObjectId();
$userId = new ObjectId();
$users->insertMany([
    ['_id'=>$adminId,'nom'=>'Administrateur','email'=>'admin@local.test','password'=>password_hash('admin', PASSWORD_DEFAULT),'role'=>'admin'],
    ['_id'=>$userId,'nom'=>'Marin','email'=>'marin@local.test','password'=>password_hash('marin', PASSWORD_DEFAULT),'role'=>'user'],
]);

$categories = [
    ['_id'=>new ObjectId(),'nom'=>'Restaurant'],
    ['_id'=>new ObjectId(),'nom'=>'Culture'],
    ['_id'=>new ObjectId(),'nom'=>'Sport'],
    ['_id'=>new ObjectId(),'nom'=>'Shopping'],
    ['_id'=>new ObjectId(),'nom'=>'Services'],
];
$db->selectCollection('categories')->insertMany($categories);
$cat = array_combine(array_column($categories,'nom'), array_map(fn($c)=>(string)$c['_id'], $categories));

$lieux = [
    ['_id'=>new ObjectId(),'nom'=>'Laval centre'],
    ['_id'=>new ObjectId(),'nom'=>'Angers'],
    ['_id'=>new ObjectId(),'nom'=>'Mayenne'],
    ['_id'=>new ObjectId(),'nom'=>'Rennes'],
];
$db->selectCollection('lieux')->insertMany($lieux);
$lieu = array_combine(array_column($lieux,'nom'), array_map(fn($l)=>(string)$l['_id'], $lieux));

$bons = [
    ['titre'=>'Menu étudiant à prix réduit','description'=>'Un restaurant local propose un menu complet accessible aux étudiants sur présentation d’une carte. Offre idéale pour manger rapidement sans exploser son budget.','categorie_id'=>$cat['Restaurant'],'lieu_id'=>$lieu['Laval centre'],'prix'=>8.90,'emoji'=>'🍔','tags'=>['étudiant','restaurant','budget'],'score'=>18],
    ['titre'=>'Place de cinéma le mardi soir','description'=>'Réduction hebdomadaire sur les séances du mardi. Bon plan pratique pour profiter des sorties culturelles en semaine.','categorie_id'=>$cat['Culture'],'lieu_id'=>$lieu['Angers'],'prix'=>6.50,'emoji'=>'🎬','tags'=>['cinéma','culture','soirée'],'score'=>24],
    ['titre'=>'Séance d’essai gratuite en salle','description'=>'Une salle de sport partenaire permet de tester gratuitement ses équipements pendant une séance découverte.','categorie_id'=>$cat['Sport'],'lieu_id'=>$lieu['Mayenne'],'prix'=>0,'emoji'=>'🏋️','tags'=>['sport','gratuit','découverte'],'score'=>12],
    ['titre'=>'Réduction sur réparation vélo','description'=>'Atelier local proposant une remise sur les petites réparations et réglages de vélo. Très utile pour les trajets quotidiens.','categorie_id'=>$cat['Services'],'lieu_id'=>$lieu['Laval centre'],'prix'=>15,'emoji'=>'🚲','tags'=>['mobilité','service','local'],'score'=>16],
    ['titre'=>'-20% sur une sélection seconde main','description'=>'Boutique indépendante avec remise sur vêtements et accessoires de seconde main. Bon compromis entre économie et consommation responsable.','categorie_id'=>$cat['Shopping'],'lieu_id'=>$lieu['Rennes'],'prix'=>null,'emoji'=>'🛍️','tags'=>['shopping','seconde-main','responsable'],'score'=>20],
    ['titre'=>'Café coworking après 16h','description'=>'Formule boisson chaude + place de travail en fin d’après-midi. Parfait pour réviser ou travailler dans un cadre calme.','categorie_id'=>$cat['Services'],'lieu_id'=>$lieu['Angers'],'prix'=>4.50,'emoji'=>'☕','tags'=>['coworking','café','travail'],'score'=>15],
];
$bonIds=[];
foreach ($bons as $b) {
    $b['_id'] = new ObjectId();
    $b['created_at'] = new UTCDateTime();
    $bonIds[] = (string)$b['_id'];
    $db->selectCollection('bons_plans')->insertOne($b);
}

$db->selectCollection('commentaires')->insertMany([
    ['bon_plan_id'=>$bonIds[0],'user_id'=>(string)$userId,'contenu'=>'Testé cette semaine, bon rapport qualité-prix.','created_at'=>new UTCDateTime()],
    ['bon_plan_id'=>$bonIds[1],'user_id'=>(string)$userId,'contenu'=>'Très bon plan pour les sorties en semaine.','created_at'=>new UTCDateTime()],
    ['bon_plan_id'=>$bonIds[3],'user_id'=>(string)$userId,'contenu'=>'Pratique et rapide pour un réglage vélo.','created_at'=>new UTCDateTime()],
]);
$db->selectCollection('votes')->insertMany([
    ['bon_plan_id'=>$bonIds[0],'user_id'=>(string)$userId,'value'=>1,'updated_at'=>new UTCDateTime()],
    ['bon_plan_id'=>$bonIds[1],'user_id'=>(string)$userId,'value'=>1,'updated_at'=>new UTCDateTime()],
]);

echo "Base local_bon_plan initialisée avec des données de démonstration.\n";
echo "Admin : admin@local.test / admin\n";
echo "Utilisateur : marin@local.test / marin\n";

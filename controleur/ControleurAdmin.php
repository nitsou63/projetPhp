<?php

class ControleurAdmin{
    
    //variable du nombre de news par page
    protected $nbNewsPage = 2;
    protected $tErreur = array ();

    function __construct() {		
        global $vues;
		//on initialise un tableau d'erreur
        $TErreur = array ();
        
		try{
            //recupération de l'action
			$action=$_REQUEST['action'];
			switch($action) {

                // si action null
                case NULL:
                    //affiche la page de news
                    header("Location: index.php?");
                    break;
                
                case "supprimerNews":
                    $this->supprimerNews();
                    header("Location: index.php?"); 
                    break;
                case "ajouterNews":
                    $this->ajouterNews();
                    break;
                case "validerAjoutNews":
                    $this->validerAjoutNews();
                    header("Location: index.php?");
                    break;
                case "deconnexion":
                    $this->deconnexion();
                    break;
				//sinon
                default:
                    // ajout d'une erreur
                    $TErreur[] = "action invalide";
                    // appel de la vue d'erreur
				    require ($vues['erreur']);
			        break;
		    }
        }
        catch (PDOException $e)
        {
		    // si erreur dans la BDD ajout d'une erreur
            $TErreur[] = $e->getMessage();
            // appel de la vue d'erreur
		    require ($vues['erreur']);
		
	    }
	    catch (Exception $e2)
	    {
            // ajout d'une erreur
            $TErreur[] = $e2->getMessage();
            // appel de la vue d'erreur
		    require ($vues['erreur']);
	    }
    }

    function supprimerNews(){
        $mNews = new ModeleNews();
        $val = new Validation();
        $idNews=$val->ValId($_GET['SupNews']);
        $mNews->supprimerNews($idNews);
    }

    function ajouterNews(){
        global $vues;
        require($vues['vAjoutNews']);
    }

    function validerAjoutNews(){
        $mNews = new ModeleNews();
        if(!isset($_POST['heure'])||!isset($_POST['site'])||!isset($_POST['titre'])||!isset($_POST['description'])||!isset($_POST['categorie'])||!isset($_POST['image']))
            throw new Exception("Au moin un des paramêtres de création d'un article n'a pas été défini");
        $news=VALIDATION::ValNews(new News(0,$_POST['heure'],$_POST['site'],$_POST['titre'],$_POST['description'],$_POST['categorie'],$_POST['image']));
        $mNews->ajouterNews($news);
        header("Location: index.php"); 
    }

    function deconnexion(){
        $admin = new ModeleAdmin();
        $nom = "";
        if(($a=$admin->isAdmin())!=null){
            $nom = $a->get_nom();
        }
        $admin->deconnexion();
        $this->afficherPageDeconnexion($nom);
    }

    function afficherPageDeconnexion($nom){
        global $vues;
        require($vues['vDeconnexion']);
    }
}
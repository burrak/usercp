<?php
session_start();
require("config.php");
// Nastavení interního kódování pro funkce pro práci s řetězci
//mb_internal_encoding("UTF-8");

// Callback pro automatické načítání tříd controllerů a modelů
function autoloadFunkce($class)
{
	// Končí název třídy řetězcem "Kontroler" ?
    if (preg_match('/Controller/', $class))
        require("controller/" . $class . ".php");        
    else
        require("model/" . $class . ".php");

}
        


// Registrace callbacku (Pod starým PHP 5.2 je nutné nahradit fcí __autoload())
spl_autoload_register("autoloadFunkce");

// Připojení k databázi
Db::connect();

// Vytvoření routeru a zpracování parametrů od uživatele z URL
$router = new RouterController();
if((isset($_SESSION['id'])) && ($_SESSION['id'] != 0))
{
    $user = new User($_SESSION['id']);
}
$router->proceed(array($_SERVER['REQUEST_URI']));


// Vyrenderování šablony
$router->renderContent();
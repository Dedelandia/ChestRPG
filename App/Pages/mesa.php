<?php
// Incluir suas classes e métodos necessários
require_once '../Model/conn.php';
require_once '../Model/tables.php';
require_once '../Model/tablesDao.php';
require_once '../Model/user.php';
require_once '../Model/userDao.php';
require_once '../Model/members.php';
require_once '../Model/membersDao.php';

//Session part
if (!isset($_SESSION)) {
  session_start();
}
// Verificar se o usuário está logado
if (isset($_SESSION['idUser'])) {
  if(!isset($_SESSION['idUser'])) {
    header("Location: ../Login/loginUser.php");
    exit();
}
  // Obter o ID do usuário logado
  $_SESSION['idUser']->getIdU();

  $tableDao = new \App\Model\TablesDao();
  $participatingTables = $tableDao->getTablesByUserId($_SESSION['idUser']->getIdU());

  if (empty($participatingTables)) {
    if (!isset($_SESSION['idUser'])) { // Verificar se o usuário está logado
      header("Location: ../Login/loginUser.php"); // Se não estiver logado, redirecione para a página de login
      exit();
    }

    // Verificar se o formulário de criação de mesa foi enviado
    if (isset($_POST['create'])) {
      $tableName = $_POST['name'];
      $tableDescription = $_POST['description'];
      $password = $_POST['password'];
      // Obter o ID do usuário logado
      $idUser = $_SESSION['idUser'];

      $table = new \App\Model\Tables();  // Criar uma instância da tabela e definir os valores
      $table->setIdFK($idUser);
      $table->setNameT($tableName);
      $table->setDescriptionT($tableDescription);
      $table->setPasswordT($password);


      $tableDao = new \App\Model\TablesDao(); // Criar uma instância do TableDao
      $tableDao->create($table); // Chamar a função create no TableDao
    }

    if (isset($_POST['join'])) {
      $joinCode = $_POST['code'];
      $idUser = $_SESSION['idUser'];

      $tableDao = new \App\Model\TablesDao();
      $tableData = $tableDao->getTableByCode($joinCode);

      if ($tableData) {
        // Mesa encontrada, agora você pode associar o usuário à mesa na tabela members
        $membersDao = new \App\Model\MembersDao();
        $member = new \App\Model\Members();
        $member->setIdUM($idUser);
        $member->setIdTM($tableData['idTable']);
        $member->setIsAdmin(false);  // Defina como desejado, neste exemplo é nulo.

        $membersDao->join($member);

        // Redirecione ou faça qualquer outra coisa após o usuário se juntar à mesa
        header("Location: insideTable.php");
        exit();
      } else {
        // Mesa não encontrada, adicione a lógica apropriada (ex: exiba uma mensagem de erro)
        echo "Mesa não encontrada.";
      }
    }
  } else {
    header("Location: usersTable.php");
    exit();
  }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@500&display=swap" rel="stylesheet">
  <title>Mesas</title>
  <link rel="stylesheet" href="../../css/mesa.css">
  <script src="../../js/mesa.js"></script>
</head>

<body>
  <header class="header">

    <div class="logo">
      <a href="index.html"><img src="Chest_RPG_NoBackGround.png" alt="" class="logo-img"></a>
    </div>
    <div class="container">
      <button class="btn">Home</button>
      <button class="btn">Sobre</button>
      <div class="dropdown">
        <button class="dropdown-button">Mesas </button>
        <div class="dropdown-content">
          <a href="#">Criar mesa</a>
          <a href="#">Entrar em mesa existente</a>
        </div>
      </div>
      <button class="btn">Atualizações</button>
    </div>
    <div class="login">Login <a href="../Login/logout.php">Logout</a></div>
  </header>

  <main class="main-content">

    <div class="content-button-left">
      <button id="openModalBtn" class="button"> Crie sua mesa</button>
      <div id="myModal" class="modal">
        <div class="modal-content">
          <span class="close" id="closeModalBtn">&times;</span>

          <div class="modal-logo-main"><img class="modal-logo" src="../img/logo.png" alt=""></div>
          <div class="main-text-modal">
            <h1>Crie sua mesa</h1>
          </div>
          <form action="mesa.php" method="POST">
            <section class="control-group-main">
              <div class="control-group">
                <input type="text" class="login-field" name="name" placeholder="Nome da mesa" id="login-name">
                <label class="user" for="login-name"></label>
              </div>


              <div class="control-group">
                <input type="text" class="login-field" name="description" placeholder="Descricão da mesa"
                  id="login-name">
                <label class="user" for="login-name"></label>
              </div>

              <div class="control-group">
                <input type="password" class="login-field" name="password" placeholder="Senha da mesa" id="login-pass">
                <label class="key" for="login-pass"></label>
              </div>

              <div>
                <div class="modal-button-main">
                  <button class="modal-button" type="submit" name="create"><i class="animation"></i>Criar mesa<i
                      class="animation"></i>
                  </button>
                </div>
              </div>
            </section>
          </form>


        </div>
      </div>
    </div>

    <div class="content-button-right">
      <button id="openJoinModalBtn" class="button"> Juntar-se a uma mesa</button>
      <div id="joinModal" class="modal">
        <div class="modal-content">
          <span class="close" id="closeJoinModalBtn">&times;</span>

          <div class="modal-logo-main"><img class="modal-logo" src="../img/logo.png" alt=""></div>
          <div class="main-text-modal">
            <h1>Junte-se a uma mesa</h1>
          </div>
          <form action="mesa.php" method="POST">
            <section class="control-group-main">
              <div class="control-group">
                <input type="text" class="login-field" name="code" placeholder="Código da mesa" id="login-name">
                <label class="user" for="login-name"></label>
              </div>
              <div>
                <div class="modal-button-main">
                  <button class="modal-button" type="submit" name="join"><i class="animation"></i>Criar mesa<i
                      class="animation"></i></button>
                </div>
              </div>
            </section>
          </form>
        </div>
      </div>
    </div>

  </main>


</body>

</html>
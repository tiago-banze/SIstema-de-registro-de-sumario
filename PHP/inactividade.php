<?php 
    // if (session_status() == PHP_SESSION_NONE) {
    //     session_start(); // Iniciar a sessão apenas se não estiver ativa
    // }
      
    // // Verifica o tempo limite de inatividade (20 segundos)
    // $inactivityTimeout = 20;
    
    // // Registre o último acesso do usuário
    // if (isset($_SESSION['last_activity'])) {
    //     $lastActivity = $_SESSION['last_activity'];
    //     $currentTime = time();
        
    //     // Verifica se houve inatividade pelo tempo especificado
    //     if (($currentTime - $lastActivity) > $inactivityTimeout) {
    //         // Destrua a sessão
    //         session_unset();
    //         session_destroy();
    //         header("Location: ../Paginas/sair.php"); // Redireciona para a página de saída
    //         exit();
    //     }
    // }
    
    
    // // Atualize o último acesso do usuário
    // $_SESSION['last_activity'] = time();
?>

<!-- <script>
  // Função para redirecionar para a página de saída após 20 segundos de inatividade
  function checkInactivity() {
      var inactivityTimeout = 5000000; // 20 segundos
      var lastActivity = <?php echo isset($_SESSION['last_activity']) ? $_SESSION['last_activity'] : 0; ?>;
      var currentTime = new Date().getTime();

      if ((currentTime - lastActivity) > inactivityTimeout) {
          window.location.href = '../Paginas/sair.php'; // Redireciona para a página de saída
      }
  }

  // Verifica a inatividade a cada 1 segundo
  setInterval(checkInactivity, 5000000);
  </script> -->
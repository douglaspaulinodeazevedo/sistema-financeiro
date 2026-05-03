
<?php
session_start();

if (!isset($_SESSION['transacoes'])) {
    $_SESSION['transacoes'] = [];
}

function calcularSaldo($transacoes) {
    $saldo = 0;
    foreach ($transacoes as $t) {
        if ($t['tipo'] === 'receita') {
            $saldo += $t['valor'];
        } else {
            $saldo -= $t['valor'];
        }
    }
    return $saldo;
}

function formatarDinheiro($valor) {
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

if (isset($_GET['limpar'])) {
    $_SESSION['transacoes'] = [];
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $usuario = $_POST['usuario'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if ($usuario === 'admin' && $senha === '1234') {
        $_SESSION['logado'] = true;
        header('Location: index.php');
        exit;
    } else {
        $erro = 'Login inválido';
    }
}

if (isset($_SESSION['logado']) && isset($_POST['transacao'])) {
    $nome = $_POST['nome'] ?? '';
    $valor = floatval($_POST['valor'] ?? 0);
    $tipo = $_POST['tipo'] ?? '';

    if ($nome !== '' && $valor > 0) {
        $_SESSION['transacoes'][] = [
            'nome' => $nome,
            'valor' => $valor,
            'tipo' => $tipo
        ];
    }
}

$saldo = calcularSaldo($_SESSION['transacoes']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sistema Financeiro</title>
</head>
<body>

<?php if (!isset($_SESSION['logado'])): ?>

<div class="container">
  <div class="card">
    <h2>Login</h2>

    <div class="info-login">
      Usuário: <b>admin</b><br>
      Senha: <b>1234</b>
    </div>

    <form method="POST">
      <input name="usuario" placeholder="Usuário">
      <input name="senha" type="password" placeholder="Senha">
      <button name="login">Entrar</button>
    </form>

    <?php if (isset($erro)) echo "<p>$erro</p>"; ?>
  </div>
</div>

<?php else: ?>

<div class="container">

  <div class="card">
    <h2>Dashboard</h2>
    <p><strong><?php echo formatarDinheiro($saldo); ?></strong></p>

    <form method="POST">
      <input name="nome" placeholder="Descrição">
      <input name="valor" type="number" step="0.01" placeholder="Valor">

      <select name="tipo">
        <option value="receita">Receita</option>
        <option value="despesa">Despesa</option>
      </select>

      <button name="transacao">Adicionar</button>
    </form>
  </div>

  <div class="card">
    <h3>Histórico</h3>

    <?php if (empty($_SESSION['transacoes'])): ?>
        <p>Sem registros.</p>
    <?php else: ?>

    <table>
      <tr>
        <th>Nome</th>
        <th>Tipo</th>
        <th>Valor</th>
      </tr>

      <?php foreach ($_SESSION['transacoes'] as $t): ?>
      <tr>
        <td><?php echo $t['nome']; ?></td>
        <td class="<?php echo $t['tipo']; ?>"><?php echo $t['tipo']; ?></td>
        <td class="<?php echo $t['tipo']; ?>"><?php echo formatarDinheiro($t['valor']); ?></td>
      </tr>
      <?php endforeach; ?>

    </table>

    <?php endif; ?>

    <div class="actions">
      <a href="?limpar=1">Limpar</a>
      <a href="?logout=1">Sair</a>
    </div>

  </div>

</div>

<?php endif; ?>

</body>

<style>
body {
    font-family: 'Segoe UI', Tahoma, sans-serif;
    background: linear-gradient(135deg, #667eea, #764ba2);
    margin: 0;
    padding: 0;
    color: #333;
}

.container {
    width: 100%;
    max-width: 420px;
    margin: 40px auto;
}

.card {
    background: #fff;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    margin-bottom: 20px;
}

h2, h3 {
    text-align: center;
    color: #5a67d8;
}

input, select {
    width: 100%;
    padding: 10px;
    margin: 6px 0;
    border-radius: 8px;
    border: 1px solid #ddd;
}

button {
    width: 100%;
    padding: 10px;
    background: #5a67d8;
    color: white;
    border: none;
    border-radius: 8px;
}

button:hover {
    background: #434190;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th {
    background: #5a67d8;
    color: white;
}

th, td {
    padding: 10px;
    text-align: center;
}

tr:nth-child(even) {
    background: #f7f7f7;
}

.receita {
    color: green;
    font-weight: bold;
}

.despesa {
    color: red;
    font-weight: bold;
}

.actions {
    text-align: center;
    margin-top: 15px;
}

.actions a {
    display: inline-block;
    margin: 5px;
    padding: 8px 12px;
    border-radius: 8px;
    text-decoration: none;
    color: white;
    background: #5a67d8;
}

.actions a:hover {
    background: #434190;
}

.info-login {
    background: #edf2ff;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 10px;
    text-align: center;
}
</style>

</html>
